<?php
require __DIR__ . '/data/site.php';
$slug = preg_replace('/[^a-z0-9-]/', '', $_GET['s'] ?? '');
$s = null;
foreach (SVEA_SERVICES as $x) if ($x['slug'] === $slug) { $s = $x; break; }
if (!$s) { http_response_code(404); require __DIR__ . '/404.php'; exit; }
$n = $s['name'];
$cta = '/kontakt?tjanst=' . rawurlencode($n);
$ld1 = json_encode(['@context' => 'https://schema.org', '@type' => 'Service', 'name' => $n, 'serviceType' => $n, 'areaServed' => 'Stockholm', 'provider' => ['@type' => 'LocalBusiness', 'name' => 'Svea Hemfix', 'telephone' => '+46-10-300-10-32', 'url' => 'https://sveahemfix.se'], 'offers' => ['@type' => 'Offer', 'price' => (string) SVEA_HOURLY, 'priceCurrency' => 'SEK', 'description' => SVEA_HOURLY . ' kr/tim före RUT' . ($s['rut'] ? ', ' . SVEA_HOURLY_RUT . ' kr/tim efter RUT-avdrag' : ' exkl. moms')]], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$ld2 = json_encode(['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => array_map(fn($f) => ['@type' => 'Question', 'name' => $f[0], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f[1]]], $s['faq'])], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$page = ['title' => $s['rut'] ? "$n Stockholm – " . SVEA_HOURLY_RUT . " kr/tim efter RUT | Svea Hemfix" : "$n Stockholm – fast pris | Svea Hemfix", 'desc' => mb_substr($s['intro'], 0, 155), 'canonical' => "https://sveahemfix.se/tjanst/$slug", 'nav' => 'tjanster', 'ld' => [$ld1, $ld2]];
require __DIR__ . '/partials/header.php';
$vmap = ['hemstadning' => 'v10', 'storstadning' => 'v25', 'flyttstadning' => 'v20', 'fonsterputsning' => 'v01', 'kontorsstadning' => 'v28', 'byggstadning' => 'v17'];
$cmap = ['hemstadning' => ['hem'], 'storstadning' => ['kok', 'badrum', 'mobel', 'hem'], 'flyttstadning' => ['flytt'], 'fonsterputsning' => ['fonster'], 'kontorsstadning' => ['kontor'], 'byggstadning' => ['flytt', 'kontor']];
$vid = $vmap[$slug] ?? 'v10';
$others = array_slice(array_values(array_filter(SVEA_SERVICES, fn($x) => $x['slug'] !== $slug)), 0, 3);
?>
<section class="hero hero-sub">
<div class="wrap">
<div><span class="kicker"><?= $s['rut'] ? 'RUT-avdrag 50 % · Stockholm' : 'För företag · Stockholm' ?></span><h1><?= $n ?> i Stockholm med omnejd</h1>
<p class="lead"><?= $s['intro'] ?></p>
<div class="acts"><a class="btn btn-p" href="<?= $cta ?>">Begär offert</a><a class="btn btn-o" href="#pris">Se pris</a></div>
<div class="trust"><div><?= ico('leaf') ?>Miljövänligt</div><div><?= ico('shield') ?>Försäkrat</div><div><?= ico('award') ?>Nöjdhetsgaranti</div></div></div>
<div class="hero-img hero-vid"><video autoplay muted loop playsinline poster="/media/v/<?= $vid ?>.webp" aria-label="<?= $n ?>"><source src="/media/v/<?= $vid ?>.mp4" type="video/mp4"></video><?php if ($s['rut']): ?><div class="badge"><b>50%</b><span>RUT-<br>avdrag</span></div><?php endif; ?></div>
</div>
</section>
<section class="sec" id="pris">
<div class="wrap">
<div class="center"><span class="kicker">Vad ingår &amp; pris</span><h2><?= $n ?> – detta ingår</h2></div>
<div class="two">
<div class="box"><h3>Detta ingår</h3><ul class="checks"><?php foreach ($s['included'] as $i) echo '<li>' . ico('check') . $i . '</li>'; ?></ul><p style="margin-top:1rem;font-size:.9rem;color:var(--mut)">Behöver du något utöver listan? Säg till i bokningen så anpassar vi städningen.</p></div>
<div class="pbox"><span class="tag">Pris – <?= $n ?></span>
<?php if ($s['rut']): ?><div class="price"><?= SVEA_HOURLY_RUT ?> kr <small>/tim efter RUT</small></div><p><?= SVEA_HOURLY ?> kr/tim före RUT-avdrag · uppskattad tid <strong><?= $s['dur'] ?></strong></p><p>Vi drar av RUT direkt på fakturan – du betalar bara halva arbetskostnaden. Städmaterial och utrustning ingår. Vid större jobb får du ett fast pris i offerten.</p>
<?php else: ?><div class="price">Fast pris <small>/mån</small></div><p>Timpris från <?= SVEA_HOURLY ?> kr exkl. moms – eller fast månadspris efter besiktning. Uppskattad tid per tillfälle <strong><?= $s['dur'] ?></strong>.</p><p>RUT-avdrag gäller inte företag. Vi lämnar en tydlig offert med omfattning, frekvens och fast pris.</p><?php endif; ?>
<a class="btn btn-y" href="<?= $cta ?>">Få exakt pris</a></div>
</div>
</div>
</section>
<?php if (!empty($s['parts'])): ?>
<section class="sec sec-alt">
<div class="wrap"><div class="center"><span class="kicker">Vad vi går igenom</span><h2>Rum för rum</h2><p class="lead">Boka hela hemmet eller bara de delar du behöver – samma timpris.</p></div>
<div class="parts"><?php foreach ($s['parts'] as [$pn, $pd, $pl]): ?><div class="box"><h3><?= $pn ?></h3><p class="mut-sm" style="margin-bottom:.6rem"><?= $pd ?></p><ul class="checks"><?php foreach ($pl as $i) echo '<li>' . ico('check') . $i . '</li>'; ?></ul></div><?php endforeach; ?></div></div>
</section>
<?php endif; ?>
<?php if (!empty($s['addons'])): ?>
<section class="sec">
<div class="wrap"><div class="center"><span class="kicker">Tillval</span><h2>Lägg till vid bokning</h2></div>
<div class="addons"><?php foreach ($s['addons'] as $a) echo '<span>' . ico('plus', 'ic') . $a . '</span>'; ?></div></div>
</section>
<?php endif; ?>
<?php $ms = ['cats' => $cmap[$slug] ?? [], 'limit' => 8, 'title' => 'Bilder & video från våra ' . mb_strtolower($n) . 'suppdrag', 'kicker' => 'Vårt arbete']; require __DIR__ . '/partials/media-strip.php'; ?>
<section class="sec sec-alt center">
<div class="wrap"><span class="kicker">Så fungerar det</span><h2>Bokat på två minuter</h2>
<div class="steps"><div class="step"><i>1</i><h3>Begär offert</h3><p>Fyll i formuläret eller ring. Pris inom 24 timmar – ofta samma dag.</p></div><div class="step"><i>2</i><h3>Välj tid</h3><p>Vardag som helg. Vi kommer med all utrustning och miljövänliga medel.</p></div><div class="step"><i>3</i><h3>Njut av resultatet</h3><p>Nöjdhetsgaranti – inte nöjd? Vi kommer tillbaka och åtgärdar utan kostnad.</p></div></div></div>
</section>
<section class="sec">
<div class="wrap"><div class="center"><span class="kicker">Vanliga frågor</span><h2>Frågor om <?= mb_strtolower($n) ?></h2></div>
<div class="faq"><?php foreach ($s['faq'] as $f) echo '<details><summary>' . $f[0] . '</summary><p>' . $f[1] . '</p></details>'; ?></div></div>
</section>
<section class="sec sec-alt">
<div class="wrap"><div class="center"><span class="kicker">Fler tjänster</span><h2>Andra tjänster som kan passa</h2></div>
<div class="rel"><?php foreach ($others as $o) echo '<a href="/tjanst/' . $o['slug'] . '"><img src="' . $o['img'] . '" alt="' . $o['name'] . '" loading="lazy"><div><h3>' . $o['name'] . '</h3><p>' . $o['short'] . '</p></div></a>'; ?></div></div>
</section>
<section class="sec">
<div class="wrap"><div class="cta"><div class="cta-c"><h2>Redo att boka <?= mb_strtolower($n) ?>?</h2><p>Kostnadsfri offert inom 24 timmar. Försäkrat, bakgrundskontrollerat och med nöjdhetsgaranti.</p><div class="acts"><a class="btn btn-y" href="<?= $cta ?>">Begär offert</a><a class="btn btn-g" href="tel:0103001032"><?= ico('phone') ?>Ring 010 300 10 32</a></div></div><video class="cta-v" autoplay muted loop playsinline preload="metadata" poster="/media/v/v19.webp" aria-hidden="true"><source src="/media/v/v19.mp4" type="video/mp4"></video></div></div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
