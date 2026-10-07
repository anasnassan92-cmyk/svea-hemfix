<?php
declare(strict_types=1);
require_once __DIR__ . '/_layout.php';
require_admin();
$db = db();
$id = (int) ($_GET['id'] ?? 0);
$post = ['id' => 0, 'title' => '', 'slug' => '', 'excerpt' => '', 'meta_description' => '', 'content_html' => '', 'hero_image_url' => '', 'hero_image_alt' => '', 'keywords' => '[]', 'status' => 'draft', 'source' => 'manual', 'published_at' => null, 'blg_id' => null, 'views' => 0, 'faq_json_ld' => null, 'json_ld' => null];
if ($id) { $s = $db->prepare('SELECT * FROM posts WHERE id=?'); $s->execute([$id]); $post = $s->fetch() ?: null; if (!$post) { flash('err', 'Inlägget hittades inte.'); header('Location: /admin/posts.php'); exit; } }
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (($_POST['act'] ?? '') === 'delete' && $id) { $db->prepare('DELETE FROM posts WHERE id=?')->execute([$id]); flash('ok', 'Inlägget raderat.'); header('Location: /admin/posts.php'); exit; }
    $title = trim((string) ($_POST['title'] ?? '')); $html = (string) ($_POST['content_html'] ?? '');
    $slug = slugify((string) ($_POST['slug'] ?: $title));
    $status = ($_POST['status'] ?? 'draft') === 'published' ? 'published' : 'draft';
    $kw = array_values(array_filter(array_map('trim', explode(',', (string) ($_POST['keywords'] ?? '')))));
    $pubAt = trim((string) ($_POST['published_at'] ?? ''));
    if ($title === '') $err = 'Titel saknas.';
    elseif (trim(strip_tags($html)) === '') $err = 'Innehåll saknas.';
    else {
        $slug = unique_slug($slug, $id ?: null);
        $row = ['title' => $title, 'slug' => $slug, 'excerpt' => trim((string) ($_POST['excerpt'] ?? '')) ?: excerpt_from_html($html), 'meta_description' => mb_substr(trim((string) ($_POST['meta_description'] ?? '')), 0, 170), 'content_html' => $html, 'hero_image_url' => trim((string) ($_POST['hero_image_url'] ?? '')), 'hero_image_alt' => trim((string) ($_POST['hero_image_alt'] ?? '')), 'keywords' => json_encode($kw, JSON_UNESCAPED_UNICODE), 'status' => $status, 'published_at' => $status === 'published' ? ($pubAt ? date('Y-m-d H:i:s', strtotime($pubAt)) : ($post['published_at'] ?: now())) : ($pubAt ? date('Y-m-d H:i:s', strtotime($pubAt)) : $post['published_at'])];
        if ($id) {
            $sets = implode(',', array_map(fn($k) => "$k=:$k", array_keys($row)));
            $db->prepare("UPDATE posts SET $sets, updated_at=:u WHERE id=:id")->execute($row + [':u' => now(), ':id' => $id]);
        } else {
            $cols = array_keys($row);
            $db->prepare('INSERT INTO posts(' . implode(',', $cols) . ',source,created_at,updated_at) VALUES(' . implode(',', array_map(fn($k) => ":$k", $cols)) . ',"manual",:c,:c)')->execute($row + [':c' => now()]);
            $id = (int) $db->lastInsertId();
        }
        flash('ok', $status === 'published' ? 'Inlägget är publicerat.' : 'Utkastet är sparat.');
        header('Location: /admin/post.php?id=' . $id); exit;
    }
    $post = array_merge($post, ['title' => $title, 'slug' => $slug, 'excerpt' => $_POST['excerpt'] ?? '', 'meta_description' => $_POST['meta_description'] ?? '', 'content_html' => $html, 'hero_image_url' => $_POST['hero_image_url'] ?? '', 'hero_image_alt' => $_POST['hero_image_alt'] ?? '', 'keywords' => json_encode($kw), 'status' => $status]);
}
$kwList = implode(', ', json_decode((string) $post['keywords'], true) ?: []);
admin_header($id ? 'Redigera inlägg' : 'Nytt inlägg', 'posts');
if ($err) echo '<div class="flash err">' . e($err) . '</div>';
?>
<form method="post" class="editor">
<?= csrf_field() ?>
<div>
<div class="card">
<div class="fld"><label for="p-title">Titel</label><input class="inp" id="p-title" name="title" value="<?= e($post['title']) ?>" required style="font-size:1.1rem;font-weight:700"></div>
<div class="fld"><label for="p-slug">URL (slug)</label><div style="display:flex;align-items:center;gap:.4rem"><span class="mut">/blogg/</span><input class="inp" id="p-slug" name="slug" value="<?= e($post['slug']) ?>" data-auto="<?= $post['slug'] ? '0' : '1' ?>"></div></div>
<div class="tabs" style="margin:0 0 .6rem"><a href="#" data-tab="html" class="on">HTML</a><a href="#" data-tab="prev">Förhandsvisning</a></div>
<div data-pane="html"><textarea class="inp html" id="p-html" name="content_html" placeholder="<h2>Rubrik</h2>&#10;<p>Text…</p>"><?= e($post['content_html']) ?></textarea><small class="mut">Skriv HTML: &lt;h2&gt;, &lt;p&gt;, &lt;ul&gt;&lt;li&gt;, &lt;img src=""&gt;, &lt;a href=""&gt;. Artiklar från BabyLoveGrowth kommer färdigformaterade.</small></div>
<div data-pane="prev" style="display:none"><div class="prev post" id="p-prev"><?= $post['content_html'] ?></div></div>
</div>
</div>
<div class="sticky">
<div class="card">
<div class="fld"><label>Status</label><select class="inp" name="status"><option value="draft" <?= $post['status'] === 'draft' ? 'selected' : '' ?>>Utkast</option><option value="published" <?= $post['status'] === 'published' ? 'selected' : '' ?>>Publicerad</option></select></div>
<div class="fld"><label for="p-date">Publiceringsdatum</label><input class="inp" id="p-date" type="datetime-local" name="published_at" value="<?= $post['published_at'] ? date('Y-m-d\TH:i', strtotime($post['published_at'])) : '' ?>"><small>Tomt = nu vid publicering.</small></div>
<div style="display:flex;gap:.5rem;flex-wrap:wrap"><button class="btn btn-p" type="submit"><?= admin_icon('check') ?>Spara</button><?php if ($id): ?><a class="btn btn-o" href="/blogg/<?= e($post['slug']) ?>" target="_blank" rel="noopener"><?= admin_icon('eye') ?>Visa</a><?php endif; ?></div>
<?php if ($post['source'] === 'blg'): ?><p class="mut" style="margin-top:.8rem">Från BabyLoveGrowth (id <span class="code"><?= e((string) $post['blg_id']) ?></span>). Om artikeln uppdateras där skrivs innehållet över.</p><?php endif; ?>
</div>
<div class="card"><h2>SEO</h2>
<div class="fld"><label for="p-meta">Meta description</label><textarea class="inp" id="p-meta" name="meta_description" data-count="c-meta" style="min-height:70px"><?= e($post['meta_description']) ?></textarea><small id="c-meta"></small></div>
<div class="fld"><label for="p-ex">Ingress / utdrag</label><textarea class="inp" id="p-ex" name="excerpt" style="min-height:70px"><?= e($post['excerpt']) ?></textarea><small>Visas i bloggens lista. Tomt = skapas automatiskt.</small></div>
<div class="fld"><label for="p-kw">Nyckelord (kommaseparerade)</label><input class="inp" id="p-kw" name="keywords" value="<?= e($kwList) ?>"></div>
</div>
<div class="card"><h2>Bild</h2>
<div class="fld"><label for="p-img">Bild-URL</label><input class="inp" id="p-img" name="hero_image_url" value="<?= e($post['hero_image_url']) ?>" placeholder="https://… eller /media/p/p12.webp"><small>Tips: använd en bild från <a href="/admin/galleri.php">galleriet</a> (t.ex. /media/p/p12.webp).</small></div>
<div class="fld"><label for="p-alt">Alt-text</label><input class="inp" id="p-alt" name="hero_image_alt" value="<?= e($post['hero_image_alt']) ?>"></div>
<?php if ($post['hero_image_url']): ?><img src="<?= e($post['hero_image_url']) ?>" alt="" style="width:100%;border-radius:10px"><?php endif; ?>
</div>
<?php if ($id): ?><div class="card"><button class="btn btn-d btn-s" type="submit" name="act" value="delete" data-confirm="Radera inlägget permanent?">Radera inlägg</button></div><?php endif; ?>
</div>
</form>
<?php admin_footer();
