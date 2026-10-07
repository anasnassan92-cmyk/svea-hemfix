<?php
declare(strict_types=1);
require_once __DIR__ . '/_layout.php';
require_admin();
$db = db();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $act = (string) ($_POST['act'] ?? ''); $id = (int) ($_POST['id'] ?? 0);
    if ($act === 'sync') {
        try { $r = blg_sync(); flash('ok', "Synk klar: {$r['new']} nya, {$r['updated']} uppdaterade av {$r['total']} artiklar." . ($r['errors'] ? ' Fel: ' . implode('; ', $r['errors']) : '')); }
        catch (Throwable $t) { flash('err', 'Synk misslyckades: ' . $t->getMessage()); }
    } elseif ($act === 'toggle' && $id) {
        $db->prepare("UPDATE posts SET status=CASE WHEN status='published' THEN 'draft' ELSE 'published' END, published_at=COALESCE(published_at, ?), updated_at=? WHERE id=?")->execute([now(), now(), $id]); flash('ok', 'Status ändrad.');
    } elseif ($act === 'delete' && $id) {
        $db->prepare('DELETE FROM posts WHERE id=?')->execute([$id]); flash('ok', 'Inlägget raderat.');
    }
    header('Location: /admin/posts.php' . (!empty($_POST['ret']) ? '?' . $_POST['ret'] : '')); exit;
}
$f = (string) ($_GET['f'] ?? ''); $qs = trim((string) ($_GET['q'] ?? ''));
$w = []; $p = [];
if ($f === 'published' || $f === 'draft') { $w[] = 'status=?'; $p[] = $f; }
if ($f === 'blg') { $w[] = "source='blg'"; }
if ($qs !== '') { $w[] = '(title LIKE ? OR slug LIKE ? OR keywords LIKE ?)'; array_push($p, "%$qs%", "%$qs%", "%$qs%"); }
$where = $w ? 'WHERE ' . implode(' AND ', $w) : '';
$s = $db->prepare("SELECT id,blg_id,title,slug,status,source,hero_image_url,published_at,updated_at,views,keywords FROM posts $where ORDER BY COALESCE(published_at, created_at) DESC LIMIT 200"); $s->execute($p); $rows = $s->fetchAll();
$c = []; foreach ($db->query("SELECT status, COUNT(*) n FROM posts GROUP BY status") as $r) $c[$r['status']] = (int) $r['n'];
$blgN = (int) $db->query("SELECT COUNT(*) FROM posts WHERE source='blg'")->fetchColumn();
$hooks = $db->query('SELECT * FROM webhook_log ORDER BY id DESC LIMIT 8')->fetchAll();
$hasKey = (bool) setting('blg_api_key');
admin_header('Blogg', 'posts');
?>
<div class="tabs">
<a href="/admin/posts.php" class="<?= $f === '' ? 'on' : '' ?>">Alla (<?= array_sum($c) ?>)</a>
<a href="/admin/posts.php?f=published" class="<?= $f === 'published' ? 'on' : '' ?>">Publicerade (<?= $c['published'] ?? 0 ?>)</a>
<a href="/admin/posts.php?f=draft" class="<?= $f === 'draft' ? 'on' : '' ?>">Utkast (<?= $c['draft'] ?? 0 ?>)</a>
<a href="/admin/posts.php?f=blg" class="<?= $f === 'blg' ? 'on' : '' ?>">Från BabyLoveGrowth (<?= $blgN ?>)</a>
</div>
<form class="toolbar" method="get">
<div class="srch"><?= admin_icon('search') ?><input class="inp" type="search" name="q" value="<?= e($qs) ?>" placeholder="Sök titel eller nyckelord…"></div><button class="btn btn-o" type="submit">Sök</button>
<span class="sp"></span>
<a class="btn btn-o" href="/blogg" target="_blank" rel="noopener"><?= admin_icon('ext') ?>Visa bloggen</a>
<a class="btn btn-p" href="/admin/post.php"><?= admin_icon('plus') ?>Nytt inlägg</a>
</form>
<div class="grid2">
<div>
<?php if (!$rows): ?><div class="card"><div class="empty">Inga inlägg ännu.<br><br>Koppla <b>BabyLoveGrowth</b> under <a href="/admin/settings.php">Inställningar</a> så publiceras artiklar automatiskt här – eller <a href="/admin/post.php">skriv ett inlägg själv</a>.</div></div><?php else: ?>
<div class="tw"><table><thead><tr><th></th><th>Titel</th><th>Status</th><th>Publicerad</th><th>Visningar</th><th></th></tr></thead><tbody>
<?php foreach ($rows as $r): ?>
<tr>
<td><?php if ($r['hero_image_url']): ?><img class="thumb" src="<?= e($r['hero_image_url']) ?>" alt="" loading="lazy"><?php else: ?><div class="thumb"></div><?php endif; ?></td>
<td><a class="rl" href="/admin/post.php?id=<?= $r['id'] ?>"><?= e($r['title']) ?></a><div class="mut">/blogg/<?= e($r['slug']) ?> · <?= $r['source'] === 'blg' ? 'BabyLoveGrowth' : 'Manuellt' ?></div></td>
<td><span class="pill <?= $r['status'] === 'published' ? 'pub' : 'draft' ?>"><?= $r['status'] === 'published' ? 'Publicerad' : 'Utkast' ?></span></td>
<td class="mut"><?= sv_date($r['published_at'] ?: null) ?></td>
<td class="mut"><?= (int) $r['views'] ?></td>
<td style="white-space:nowrap"><a class="btn btn-o btn-s" href="/blogg/<?= e($r['slug']) ?>" target="_blank" rel="noopener" title="Visa"><?= admin_icon('eye') ?></a> <form method="post" style="display:inline"><?= csrf_field() ?><input type="hidden" name="act" value="toggle"><input type="hidden" name="id" value="<?= $r['id'] ?>"><input type="hidden" name="ret" value="<?= e(http_build_query(['f' => $f ?: null, 'q' => $qs ?: null])) ?>"><button class="btn btn-o btn-s" type="submit"><?= $r['status'] === 'published' ? 'Avpublicera' : 'Publicera' ?></button></form></td>
</tr>
<?php endforeach; ?>
</tbody></table></div><?php endif; ?>
</div>
<div>
<div class="card"><h2><?= admin_icon('bolt') ?>BabyLoveGrowth</h2>
<p class="mut" style="margin-bottom:.8rem">Nya artiklar tas emot automatiskt via webhook. Du kan även hämta alla artiklar från API:et.</p>
<form method="post"><?= csrf_field() ?><input type="hidden" name="act" value="sync"><button class="btn btn-p btn-s" type="submit" <?= $hasKey ? '' : 'disabled' ?>><?= admin_icon('refresh') ?>Hämta artiklar från API</button></form>
<?php if (!$hasKey): ?><p class="mut" style="margin-top:.6rem">Lägg in API-nyckeln under <a href="/admin/settings.php">Inställningar</a> för att aktivera.</p><?php endif; ?>
</div>
<div class="card"><h2>Webhook-logg</h2>
<?php if (!$hooks): ?><div class="mut">Inga anrop ännu.</div><?php else: ?>
<?php foreach ($hooks as $h): ?><div class="note" style="background:<?= $h['ok'] ? '#F0FDF4' : '#FEF2F2' ?>;border-color:<?= $h['ok'] ? '#BBF7D0' : '#FECACA' ?>"><small style="color:<?= $h['ok'] ? '#166534' : '#991B1B' ?>"><?= sv_date($h['received_at'], true) ?> · <?= $h['ok'] ? 'OK' : 'FEL' ?></small><?= e($h['message']) ?></div><?php endforeach; ?>
<?php endif; ?>
</div>
</div>
</div>
<?php admin_footer();
