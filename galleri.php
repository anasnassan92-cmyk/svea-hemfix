<?php
require __DIR__ . '/data/site.php';
$media = json_decode(file_get_contents(__DIR__ . '/media/manifest.json'), true);
$page = ['title' => 'Galleri – Bilder & video från våra städuppdrag i Stockholm | Svea Hemfix', 'desc' => 'Se riktiga bilder och videoklipp från Svea Hemfix städuppdrag: hemstädning, kök, badrum, kontor, trapphus, fönsterputs och före/efter-resultat i Stockholm.', 'canonical' => 'https://sveahemfix.se/galleri', 'nav' => 'galleri'];
require __DIR__ . '/partials/header.php';
$cats = ['alla' => 'Alla', 'fore-efter' => 'Före & efter', 'video' => 'Video', 'hem' => 'Hemstädning', 'kok' => 'Kök', 'badrum' => 'Badrum', 'kontor' => 'Kontor', 'trapphus' => 'Trapphus', 'fonster' => 'Fönsterputs', 'flytt' => 'Flytt & bygg', 'mobel' => 'Möbler & mattor', 'team' => 'Vårt team'];
// interleave: before/after photos first, then mix videos and photos
$ba = array_values(array_filter($media['photos'], fn($p) => str_contains($p['cap'], 'före och efter')));
usort($ba, fn($x, $y) => ($x['id'] !== 'p24') <=> ($y['id'] !== 'p24'));
$ph = array_values(array_filter($media['photos'], fn($p) => !str_contains($p['cap'], 'före och efter')));
$vi = $media['videos'];
$nV = count($vi); $nP = count($media['photos']);
?>
<section class="hero hero-sub">
<div class="wrap">
<div><span class="kicker">Galleri</span><h1>Bilder och video från våra uppdrag</h1>
<p class="lead">Här ser du hur vi jobbar – riktiga uppdrag, riktiga resultat. <strong><?= $nV ?> videoklipp</strong> och <strong><?= $nP ?> bilder</strong> från hemstädning, kök, badrum, kontor, trapphus och fönsterputs i Stockholm.</p>
<div class="acts"><a class="btn btn-p" href="#video">Se videor</a><a class="btn btn-o" href="#fore-efter">Före &amp; efter</a></div>
<div class="trust"><div><?= ico('shield') ?>Försäkrat</div><div><?= ico('leaf') ?>Miljövänligt</div><div><?= ico('award') ?>Nöjdhetsgaranti</div></div></div>
<div class="hero-img hero-vid"><video autoplay muted loop playsinline poster="/media/hero.webp" width="1280" height="720"><source src="/media/hero.mp4" type="video/mp4"></video></div>
</div>
</section>
<nav class="gchips" aria-label="Galleri-sektioner"><a href="#video">Videor (<?= $nV ?>)</a><a href="#fore-efter">Före &amp; efter (<?= count($ba) ?>)</a><a href="#bilder">Bilder (<?= count($ph) ?>)</a></nav>
<section class="sec" id="video">
<div class="wrap">
<div class="center"><span class="kicker">Video</span><h2>Se oss i arbete</h2><p class="lead">Klippen spelas upp automatiskt – tryck på ett klipp för att se det i helskärm.</p></div>
<?php $vert = array_values(array_filter($vi, fn($v) => $v['h'] > $v['w'])); $land = array_values(array_filter($vi, fn($v) => $v['h'] <= $v['w'])); ?>
<div class="vgrid vgrid-l">
<?php foreach ($land as $m): ?>
<a class="v-item" href="/media/v/<?= $m['id'] ?>.mp4" data-lb="video" data-cat="<?= $m['cat'] ?>" data-cap="<?= $m['cap'] ?>"><video class="av" muted loop playsinline preload="none" poster="/media/v/<?= $m['id'] ?>.webp" data-src="/media/v/<?= $m['id'] ?>.mp4" width="<?= $m['w'] ?>" height="<?= $m['h'] ?>" aria-label="<?= $m['cap'] ?>"></video><span><?= $m['cap'] ?><small><?= $m['dur'] ?> s</small></span></a>
<?php endforeach; ?>
</div>
<div class="vgrid vgrid-p">
<?php foreach ($vert as $m): ?>
<a class="v-item tall" href="/media/v/<?= $m['id'] ?>.mp4" data-lb="video" data-cat="<?= $m['cat'] ?>" data-cap="<?= $m['cap'] ?>"><video class="av" muted loop playsinline preload="none" poster="/media/v/<?= $m['id'] ?>.webp" data-src="/media/v/<?= $m['id'] ?>.mp4" width="<?= $m['w'] ?>" height="<?= $m['h'] ?>" aria-label="<?= $m['cap'] ?>"></video><span><?= $m['cap'] ?><small><?= $m['dur'] ?> s</small></span></a>
<?php endforeach; ?>
</div>
</div>
</section>
<section class="sec sec-alt" id="fore-efter">
<div class="wrap">
<div class="center"><span class="kicker">Före &amp; efter</span><h2>Skillnaden syns direkt</h2><p class="lead">Riktiga resultat från riktiga uppdrag – klicka för att förstora.</p></div>
<div class="bagrid">
<?php foreach ($ba as $k => $p) echo '<a class="ba-item' . ($k === 0 ? ' big' : '') . '" href="/media/p/' . $p['id'] . '.webp" data-lb="img" data-cap="' . $p['cap'] . '"><img src="/media/p/' . $p['id'] . ($k === 0 ? '' : '-t') . '.webp" alt="' . $p['cap'] . '" loading="lazy" width="' . $p['w'] . '" height="' . $p['h'] . '"><span>' . $p['cap'] . '</span></a>'; ?>
</div>
</div>
</section>
<section class="sec" id="bilder">
<div class="wrap">
<div class="center"><span class="kicker">Bilder</span><h2>Vårt team, vår utrustning, våra uppdrag</h2></div>
<div class="masonry">
<?php foreach ($ph as $m): ?>
<a class="g-item" href="/media/p/<?= $m['id'] ?>.webp" data-lb="img" data-cat="<?= $m['cat'] ?>" data-cap="<?= $m['cap'] ?>"><img src="/media/p/<?= $m['id'] ?>-t.webp" alt="<?= $m['cap'] ?>" loading="lazy" width="<?= $m['w'] ?>" height="<?= $m['h'] ?>"><span><?= $m['cap'] ?></span></a>
<?php endforeach; ?>
</div>
</div>
</section>
<section class="sec">
<div class="wrap"><div class="cta"><div class="cta-c"><h2>Vill du ha samma resultat hemma?</h2><p>Kostnadsfri offert inom 24 timmar. Upp till 50 % RUT-avdrag för privatpersoner.</p><div class="acts"><a class="btn btn-y" href="/kontakt">Begär offert</a><a class="btn btn-g" href="tel:0103001032"><?= ico('phone') ?>Ring 010 300 10 32</a></div></div><video class="cta-v" autoplay muted loop playsinline preload="metadata" poster="/media/v/v11.webp" aria-hidden="true"><source src="/media/v/v11.mp4" type="video/mp4"></video></div></div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
