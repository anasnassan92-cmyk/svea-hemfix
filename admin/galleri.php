<?php
/**
 * Gallery manager: upload / replace / delete media, edit captions, categories and visibility.
 * Uploads go through admin/media-upload.php (JSON); captions/categories are saved with the form below.
 */
declare(strict_types=1);
require_once __DIR__ . '/_layout.php';
require_once __DIR__ . '/../lib/media.php';
require_admin();
$cats = MEDIA_CATS;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $man = media_load();
    $in = $_POST['m'] ?? [];
    foreach (['photos', 'videos'] as $t) {
        foreach ($man[$t] as &$it) {
            $d = $in[$it['id']] ?? null; if (!$d) continue;
            $it['cap'] = mb_substr(strip_tags(trim((string) ($d['cap'] ?? $it['cap']))), 0, 120);
            if (isset($cats[$d['cat'] ?? ''])) $it['cat'] = $d['cat'];
            $it['hidden'] = !empty($d['hidden']);
        } unset($it);
    }
    try { media_save($man); flash('ok', 'Galleriet är uppdaterat.'); } catch (Throwable $e) { flash('err', $e->getMessage()); }
    header('Location: /admin/galleri.php?t=' . ($_POST['t'] ?? 'videos')); exit;
}
$man = media_load();
$t = ($_GET['t'] ?? 'videos') === 'photos' ? 'photos' : 'videos';
$hiddenN = count(array_filter(array_merge($man['photos'], $man['videos']), fn($x) => !empty($x['hidden'])));
admin_header('Galleri', 'galleri');
?>
<div class="card" id="up">
<h2><?= admin_icon('plus') ?>Ladda upp bilder eller video</h2>
<div class="f2" style="margin-bottom:.75rem">
<div class="fld"><label for="up-cap">Bildtext (kan ändras efteråt)</label><input class="inp" id="up-cap" placeholder="t.ex. Köksstädning – efter"></div>
<div class="fld"><label for="up-cat">Kategori</label><select class="inp" id="up-cat"><?php foreach ($cats as $k => $l): ?><option value="<?= $k ?>"><?= $l ?></option><?php endforeach; ?></select></div>
</div>
<label class="drop" id="drop"><input type="file" id="up-file" multiple accept="image/jpeg,image/png,image/webp,video/mp4,.mp4"><?= admin_icon('dl') ?><b>Släpp filer här eller klicka för att välja</b><span>JPG, PNG, WebP (max 25 MB) eller MP4-video (max 300 MB). Bilder optimeras automatiskt till WebP – videor bör vara komprimerade (H.264, 1080p) innan uppladdning.</span></label>
<div id="up-list"></div>
</div>
<div class="tabs"><a href="?t=videos" class="<?= $t === 'videos' ? 'on' : '' ?>">Videor (<?= count($man['videos']) ?>)</a><a href="?t=photos" class="<?= $t === 'photos' ? 'on' : '' ?>">Bilder (<?= count($man['photos']) ?>)</a><span class="mut" style="align-self:center;margin-left:.5rem"><?= $hiddenN ?> dolda</span></div>
<form method="post" id="gal-form"><?= csrf_field() ?><input type="hidden" name="t" value="<?= $t ?>">
<div class="toolbar"><button class="btn btn-p" type="submit"><?= admin_icon('check') ?>Spara bildtexter &amp; kategorier</button><a class="btn btn-o" href="/galleri" target="_blank" rel="noopener"><?= admin_icon('ext') ?>Visa galleriet</a><span class="sp"></span><span class="mut">Dolda objekt visas inte på webbplatsen men finns kvar.</span></div>
<?php if (!$man[$t]): ?><div class="card"><div class="empty">Inga <?= $t === 'videos' ? 'videor' : 'bilder' ?> ännu – ladda upp ovan.</div></div><?php else: ?>
<div class="tw"><table><thead><tr><th></th><th>ID</th><th>Bildtext</th><th>Kategori</th><th>Dölj</th><th></th></tr></thead><tbody>
<?php foreach ($man[$t] as $it): $id = $it['id']; $isV = $t === 'videos'; $cb = !empty($it['v']) ? '?v=' . $it['v'] : ''; ?>
<tr id="row-<?= e($id) ?>" style="<?= !empty($it['hidden']) ? 'opacity:.5' : '' ?>">
<td><a href="<?= $isV ? "/media/v/$id.mp4" : "/media/p/$id.webp" ?><?= $cb ?>" target="_blank" rel="noopener"><img class="thumb <?= ($it['h'] ?? 0) > ($it['w'] ?? 1) ? 'v' : '' ?>" src="<?= $isV ? "/media/v/$id.webp" : "/media/p/$id-t.webp" ?><?= $cb ?>" alt="" loading="lazy"></a></td>
<td class="mut"><?= e($id) ?><?= $isV ? '<br>' . (int) $it['dur'] . ' s' : '' ?><br><?= (int) $it['w'] ?>×<?= (int) $it['h'] ?></td>
<td><input class="inp" name="m[<?= e($id) ?>][cap]" value="<?= e($it['cap']) ?>"></td>
<td><select class="inp" name="m[<?= e($id) ?>][cat]"><?php foreach ($cats as $k => $l): ?><option value="<?= $k ?>" <?= ($it['cat'] ?? '') === $k ? 'selected' : '' ?>><?= $l ?></option><?php endforeach; ?></select></td>
<td><input class="chk" type="checkbox" name="m[<?= e($id) ?>][hidden]" value="1" <?= !empty($it['hidden']) ? 'checked' : '' ?>></td>
<td style="white-space:nowrap"><label class="btn btn-o btn-s" title="Byt fil"><?= admin_icon('refresh') ?>Byt<input type="file" class="repl" data-id="<?= e($id) ?>" accept="<?= $isV ? 'video/mp4,.mp4' : 'image/jpeg,image/png,image/webp' ?>" hidden></label> <button type="button" class="btn btn-d btn-s del" data-id="<?= e($id) ?>" title="Radera"><?= admin_icon('x') ?></button></td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<div class="toolbar" style="margin-top:1rem"><button class="btn btn-p" type="submit"><?= admin_icon('check') ?>Spara bildtexter &amp; kategorier</button></div>
<?php endif; ?>
</form>
<script>document.body.setAttribute('data-csrf','<?= e(csrf_token()) ?>');</script>
<?php admin_footer();
