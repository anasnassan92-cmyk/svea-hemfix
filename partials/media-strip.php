<?php
// Usage: $ms = ['cats' => ['kok'], 'limit' => 8, 'title' => '...', 'kicker' => '...', 'ba' => false]; require partials/media-strip.php
$ms = $ms ?? [];
$mediaAll = $mediaAll ?? json_decode(file_get_contents(__DIR__ . '/../media/manifest.json'), true);
$cats = $ms['cats'] ?? [];
$lim = $ms['limit'] ?? 8;
$items = [];
if (!empty($ms['ba'])) {
    foreach ($mediaAll['photos'] as $p) if (str_contains($p['cap'], 'före och efter')) $items[] = ['p', $p];
} else {
    $vs = array_values(array_filter($mediaAll['videos'], fn($v) => !$cats || in_array($v['cat'], $cats)));
    $ps = array_values(array_filter($mediaAll['photos'], fn($p) => (!$cats || in_array($p['cat'], $cats)) && !str_contains($p['cap'], 'före och efter')));
    $bs = array_values(array_filter($mediaAll['photos'], fn($p) => (!$cats || in_array($p['cat'], $cats)) && str_contains($p['cap'], 'före och efter')));
    $i = $j = $k = 0;
    while (count($items) < $lim && ($i < count($vs) || $j < count($ps) || $k < count($bs))) {
        if ($k < count($bs)) $items[] = ['p', $bs[$k++]];
        if ($i < count($vs)) $items[] = ['v', $vs[$i++]];
        if ($i < count($vs)) $items[] = ['v', $vs[$i++]];
        if ($j < count($ps)) $items[] = ['p', $ps[$j++]];
    }
}
$items = array_slice($items, 0, $lim);
if ($items): ?>
<section class="sec sec-alt" id="media">
<div class="wrap">
<div class="center"><span class="kicker"><?= $ms['kicker'] ?? 'Bilder &amp; video' ?></span><h2><?= $ms['title'] ?? 'Se hur vi jobbar' ?></h2><p class="lead"><?= $ms['lead'] ?? 'Riktiga uppdrag från Stockholm – klicka för att förstora eller spela upp.' ?></p></div>
<div class="mstrip">
<?php foreach ($items as [$t, $m]): if ($t === 'v'): ?>
<a class="mg-item<?= $m['h'] > $m['w'] ? ' tall' : '' ?>" href="/media/v/<?= $m['id'] ?>.mp4" data-lb="video" data-cap="<?= $m['cap'] ?>"><video class="av" muted loop playsinline preload="none" poster="/media/v/<?= $m['id'] ?>.webp" data-src="/media/v/<?= $m['id'] ?>.mp4" width="<?= $m['w'] ?>" height="<?= $m['h'] ?>" aria-label="<?= $m['cap'] ?>"></video><i><?= ico('play', 'ic') ?></i><span><?= $m['cap'] ?> · <?= $m['dur'] ?> s</span></a>
<?php else: ?>
<a class="mg-item" href="/media/p/<?= $m['id'] ?>.webp" data-lb="img" data-cap="<?= $m['cap'] ?>"><img src="/media/p/<?= $m['id'] ?>-t.webp" alt="<?= $m['cap'] ?>" loading="lazy" width="<?= $m['w'] ?>" height="<?= $m['h'] ?>"><span><?= $m['cap'] ?></span></a>
<?php endif; endforeach; ?>
</div>
<div class="center" style="margin-top:2rem"><a class="btn btn-p" href="/galleri">Se hela galleriet – <?= count($mediaAll['videos']) ?> videor &amp; <?= count($mediaAll['photos']) ?> bilder</a></div>
</div>
</section>
<?php endif; ?>
