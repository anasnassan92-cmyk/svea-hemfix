<?php
require __DIR__ . '/data/site.php';
$faqLd = json_encode(['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => array_map(fn($f) => ['@type' => 'Question', 'name' => $f[0], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f[1]]], SVEA_FAQ)], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$bizLd = json_encode(['@context' => 'https://schema.org', '@type' => 'LocalBusiness', 'name' => 'Svea Hemfix', 'description' => 'Professionell städning för hem, företag och fastigheter i Stockholm med omnejd, med RUT-avdrag', 'url' => 'https://sveahemfix.se', 'telephone' => '+46-10-300-10-32', 'email' => 'info@sveahemfix.se', 'areaServed' => 'Stockholm, Sverige', 'legalName' => SVEA_COMPANY, 'vatID' => 'SE' . str_replace('-', '', SVEA_ORGNR) . '01', 'address' => ['@type' => 'PostalAddress', 'streetAddress' => 'Kronborgsgränd 19', 'postalCode' => '164 46', 'addressLocality' => 'Kista', 'addressRegion' => 'Stockholm', 'addressCountry' => 'SE'], 'openingHours' => ['Mo-Fr 08:00-18:00', 'Sa-Su 09:00-15:00'], 'priceRange' => 'SEK 224-5000', 'image' => 'https://sveahemfix.se/images/logo.png'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$page = ['title' => 'Svea Hemfix | Professionell Städning i Stockholm | RUT-avdrag 50%', 'desc' => 'Professionell städning för hem, företag och fastigheter i Stockholm med omnejd. Försäkrat, miljövänligt och med upp till 50% RUT-avdrag. Hemstädning 449 kr/tim – 224,50 kr efter RUT.', 'canonical' => 'https://sveahemfix.se/', 'nav' => 'hem', 'ld' => [$bizLd, $faqLd]];
require __DIR__ . '/partials/header.php';
$media = json_decode(file_get_contents(__DIR__ . '/media/manifest.json'), true);
$ba = array_values(array_filter($media['photos'], fn($p) => str_contains($p['cap'], 'före och efter')));
usort($ba, fn($x, $y) => ($x['id'] !== 'p24') <=> ($y['id'] !== 'p24'));
$reelIds = ['v36', 'v33', 'v28', 'v42', 'v31', 'v27', 'v39', 'v45'];
$reel = array_values(array_filter($media['videos'], fn($v) => in_array($v['id'], $reelIds)));
usort($reel, fn($x, $y) => array_search($x['id'], $reelIds) <=> array_search($y['id'], $reelIds));
$home = array_values(array_filter(SVEA_SERVICES, fn($s) => in_array($s['slug'], ['hemstadning', 'storstadning', 'flyttstadning', 'fonsterputsning', 'kontorsstadning'])));
?>
<section class="hero-full" id="hem">
<video class="hf-bg" autoplay muted loop playsinline preload="auto" poster="/media/hero.webp" aria-hidden="true"><source src="/media/hero.mp4" type="video/mp4"></video>
<div class="hf-ov"></div>
<div class="wrap">
<div class="hf-c">
<span class="kicker kicker-w">Stockholms betrodda städfirma</span>
<h1>Professionell städning för ett friskare hem</h1>
<p class="lead">Pålitlig, försäkrad och miljövänlig städning i Stockholm med omnejd – anpassad efter dina behov och med upp till <strong>50&nbsp;% RUT-avdrag</strong>.</p>
<div class="acts"><a class="btn btn-y" href="/kontakt">Få offert gratis</a><a class="btn btn-w" href="/tjanster">Våra tjänster</a></div>
<div class="trust trust-w">
<div><?= ico('leaf') ?>Miljövänliga produkter</div>
<div><?= ico('shield') ?>Bakgrundskontrollerade &amp; försäkrade</div>
<div><?= ico('award') ?>Nöjdhetsgaranti</div>
</div>
</div>
</div>
<div class="hf-card"><div class="b"><b>100%</b><span>Nöjdhets-<br>garanti</span></div><p><span class="stars">★★★★★</span><b>4,9 av 5 i kundbetyg</b>Över 5 000 städade hem i Stockholm</p></div>
<a class="hf-scroll" href="#tjanster" aria-label="Scrolla ner"><i></i>Scrolla</a>
</section>

<section class="sec" id="tjanster">
<div class="wrap">
<div class="svc">
<div>
<h2>Våra städtjänster</h2>
<p class="lead">Från regelbunden hemstädning till djuprengöring och trapphusstädning – vi har dig täckt.</p>
<a class="btn btn-y" href="/tjanster">Alla tjänster</a>
</div>
<div class="svc-grid">
<?php foreach ($home as $s): ?>
<a class="card" href="/tjanst/<?= $s['slug'] ?>"><?= ico($s['icon']) ?><h3><?= $s['name'] ?></h3><p><?= $s['short'] ?></p><span class="more">Läs mer →</span></a>
<?php endforeach; ?>
<a class="card" href="/trapphusstadning"><?= ico('stairs') ?><h3>Trapphusstädning</h3><p>Regelbunden städning för BRF, fastighetsägare och företag. Från 2 000 kr/mån.</p><span class="more">Läs mer →</span></a>
</div>
</div>
</div>
</section>

<section class="sec sec-alt center" id="om-oss">
<div class="wrap">
<span class="kicker">Varför Svea Hemfix?</span>
<h2>Kvalitet, omtanke och professionalism</h2>
<p class="lead">Sedan 2016 har vi hjälpt tusentals hem och företag i Stockholm att hålla rent – enkelt, pålitligt och prisvärt.</p>
<div class="why">
<div><?= ico('leaf') ?><h3>Miljövänliga produkter</h3><p>Säkert för din familj, dina husdjur och miljön.</p></div>
<div><?= ico('users') ?><h3>Kontrollerade städare</h3><p>Erfarna, bakgrundskontrollerade och fullt försäkrade.</p></div>
<div><?= ico('wallet') ?><h3>Tydliga priser</h3><p>Fast timpris, RUT dras av direkt – inga överraskningar.</p></div>
<div><?= ico('award') ?><h3>Nöjdhetsgaranti</h3><p>Inte nöjd? Vi kommer tillbaka och åtgärdar gratis.</p></div>
</div>
</div>
</section>

<section class="sec">
<div class="wrap">
<div class="split">
<video class="split-v" autoplay muted loop playsinline preload="metadata" poster="/media/v/v16.webp" aria-label="Ytdesinfektion"><source src="/media/v/v16.mp4" type="video/mp4"></video>
<div class="split-c">
<h2>Vi får ditt hem att skina</h2>
<ul class="checks">
<li><?= ico('check') ?>Miljömärkta, giftfria städmedel – vi tar med all utrustning</li>
<li><?= ico('check') ?>Noggranna, professionella städare – samma person varje gång</li>
<li><?= ico('check') ?>Flexibel bokning – vardag som helg, avboka fritt 24 h innan</li>
<li><?= ico('check') ?>RUT-avdraget sköter vi åt dig direkt på fakturan</li>
</ul>
<a class="btn btn-y" href="/kontakt">Boka nu</a>
</div>
</div>
</div>
</section>

<section class="sec sec-alt" id="fore-efter">
<div class="wrap">
<div class="center"><span class="kicker">Före &amp; efter</span><h2>Riktiga resultat från riktiga uppdrag</h2><p class="lead">Inga retuscherade bilder – så här ser skillnaden ut efter en städning av Svea Hemfix. Klicka på en bild för att förstora.</p></div>
<div class="bagrid">
<?php foreach ($ba as $i => $p) echo '<a class="ba-item' . ($i === 0 ? ' big' : '') . '" href="/media/p/' . $p['id'] . '.webp" data-lb="img" data-cap="' . $p['cap'] . '"><img src="/media/p/' . $p['id'] . ($i === 0 ? '' : '-t') . '.webp" alt="' . $p['cap'] . '" loading="lazy" width="' . $p['w'] . '" height="' . $p['h'] . '"><span>' . $p['cap'] . '</span></a>'; ?>
</div>
<div class="center" style="margin-top:2rem"><a class="btn btn-p" href="/galleri">Se hela galleriet</a></div>
</div>
</section>

<section class="sec" id="reel">
<div class="wrap">
<div class="center"><span class="kicker">Vårt arbete i rörelse</span><h2>Se hur vi jobbar</h2><p class="lead">Korta klipp från våra uppdrag – kök, badrum, kontor och trapphus. Tryck för att spela upp.</p></div>
<div class="reel">
<?php foreach ($reel as $v) echo '<a class="reel-item" href="/media/v/' . $v['id'] . '.mp4" data-lb="video" data-cap="' . $v['cap'] . '"><video class="av" muted loop playsinline preload="none" poster="/media/v/' . $v['id'] . '.webp" data-src="/media/v/' . $v['id'] . '.mp4" width="' . $v['w'] . '" height="' . $v['h'] . '" aria-label="' . $v['cap'] . '"></video><i>' . ico('play', 'ic play') . '</i><span>' . $v['cap'] . '<small>' . $v['dur'] . ' s</small></span></a>'; ?>
</div>
<div class="center" style="margin-top:1.5rem"><a class="btn btn-o" href="/galleri#video">Alla videor</a></div>
</div>
</section>

<section class="stats">
<div class="wrap">
<div><b>5 000+</b><span>Städade hem</span></div>
<div><b>10+</b><span>Års erfarenhet</span></div>
<div><b>847+</b><span>Nöjda kunder</span></div>
<div><b>100%</b><span>Försäkrat</span></div>
</div>
</section>

<section class="sec center">
<div class="wrap">
<span class="kicker">Så fungerar det</span>
<h2>Bokat på två minuter</h2>
<div class="steps">
<div class="step"><i>1</i><h3>Begär offert</h3><p>Fyll i formuläret eller ring oss. Du får ett tydligt pris inom 24 timmar – ofta samma dag.</p></div>
<div class="step"><i>2</i><h3>Vi städar</h3><p>Välj tid som passar dig. Vårt team kommer med all utrustning och miljövänliga medel.</p></div>
<div class="step"><i>3</i><h3>Du njuter</h3><p>Kom hem till ett skinande rent hem. Nöjdhetsgaranti – annars städar vi om gratis.</p></div>
</div>
</div>
</section>

<section class="sec sec-alt center" id="priser">
<div class="wrap">
<span class="kicker">Enkla &amp; tydliga priser</span>
<h2>Vad kostar det?</h2>
<p class="lead">Samma timpris för alla hushållsnära tjänster. Städmaterial ingår och RUT dras av direkt på fakturan.</p>
<div class="plans">
<div class="plan"><span class="tag">Privat</span><h3>Hemstädning</h3><div class="price"><?= SVEA_HOURLY_RUT ?> kr <small>/tim efter RUT</small></div><p class="pre"><?= SVEA_HOURLY ?> kr/tim före RUT-avdrag</p>
<ul><li><?= ico('check') ?>Regelbundet eller engång</li><li><?= ico('check') ?>Städmaterial ingår</li><li><?= ico('check') ?>Samma städare varje gång</li><li><?= ico('check') ?>Avboka fritt 24 h innan</li></ul><a class="btn btn-p" href="/kontakt?tjanst=Hemstädning">Boka nu</a></div>
<div class="plan hi"><span class="tag">BRF &amp; fastighet</span><h3>Trapphusstädning</h3><div class="price">2 000 kr <small>/mån</small></div><p class="pre">8 våningar, 1 gång/vecka – eller 392 kr/tim inkl. material</p>
<ul><li><?= ico('check') ?>Entré, hiss och trapphus</li><li><?= ico('check') ?>Fast månadspris</li><li><?= ico('check') ?>En kontaktperson</li><li><?= ico('check') ?>Avtal 12 mån eller enligt överenskommelse</li></ul><a class="btn btn-p" href="/trapphusstadning">Läs mer</a></div>
<div class="plan"><span class="tag">Företag</span><h3>Kontorsstädning</h3><div class="price">Fast pris <small>/mån</small></div><p class="pre">Från <?= SVEA_HOURLY ?> kr/tim exkl. moms – offert efter besiktning</p>
<ul><li><?= ico('check') ?>Dag- eller kvällstid</li><li><?= ico('check') ?>Toaletter, kök och golv</li><li><?= ico('check') ?>Förbrukningsmaterial fylls på</li><li><?= ico('check') ?>Löpande städavtal</li></ul><a class="btn btn-p" href="/kontakt?tjanst=Kontorsstädning">Begär offert</a></div>
</div>
<p class="lead" style="margin-top:1.5rem;font-size:.9rem">Flyttstädning, fönsterputs och övriga tjänster: <?= SVEA_HOURLY ?> kr/tim (<?= SVEA_HOURLY_RUT ?> kr efter RUT) – fast pris i offerten. <a href="/priser" style="color:var(--p);font-weight:700">Se hela prislistan →</a></p>
<div class="rut" style="text-align:left">
<div><span class="kicker" style="background:rgba(255,255,255,.15);color:#fff">RUT-avdrag</span><h2>Spara 50 % med RUT-avdrag</h2><p class="lead">Som privatperson betalar du bara halva arbetskostnaden – resten hanterar vi direkt med Skatteverket.</p>
<ul class="checks"><li><?= ico('check') ?>Avdraget syns direkt på fakturan</li><li><?= ico('check') ?>Vi sköter allt pappersarbete</li><li><?= ico('check') ?>Max 75 000 kr i avdrag per person och år</li></ul></div>
<div class="calc"><h3>Räkna ut ditt pris</h3><div class="hrs"><button id="rut-m" aria-label="Färre timmar">−</button><b id="rut-h">2</b><button id="rut-p" aria-label="Fler timmar">+</button></div>
<div class="row"><span>Pris före RUT</span><b id="rut-b">898 kr</b></div><div class="row"><span>RUT-avdrag (50 %)</span><b id="rut-d">−449 kr</b></div><div class="row tot"><span>Du betalar</span><b id="rut-a">449 kr</b></div></div>
</div>
</div>
</section>

<section class="sec center" id="recensioner">
<div class="wrap">
<span class="kicker">Kundrecensioner</span>
<h2>Vad våra kunder säger</h2>
<div class="tsg">
<?php foreach (array_slice(SVEA_REVIEWS, 0, 3) as $r): ?>
<div class="ts"><div class="stars"><?= str_repeat('★', $r[2]) . str_repeat('☆', 5 - $r[2]) ?></div><p>"<?= $r[3] ?>"</p><b><?= $r[0] ?></b><small><?= $r[1] ?></small></div>
<?php endforeach; ?>
</div>
</div>
</section>

<section class="sec sec-alt" id="faq">
<div class="wrap center">
<span class="kicker">Vanliga frågor</span>
<h2>Frågor &amp; svar</h2>
<div class="faq" style="text-align:left">
<?php foreach (SVEA_FAQ as $f): ?><details><summary><?= $f[0] ?></summary><p><?= $f[1] ?></p></details><?php endforeach; ?>
</div>
</div>
</section>

<section class="sec">
<div class="wrap">
<div class="cta">
<div class="cta-c"><h2>Redo för ett renare hem?</h2><p>Boka din städning idag och njut av professionell städning med upp till 50 % besparing via RUT-avdrag.</p>
<div class="acts"><a class="btn btn-y" href="/kontakt">Boka nu</a><a class="btn btn-g" href="tel:0103001032"><?= ico('phone') ?>Ring 010 300 10 32</a></div></div>
<video class="cta-v" autoplay muted loop playsinline preload="metadata" poster="/media/v/v04.webp" aria-hidden="true"><source src="/media/v/v04.mp4" type="video/mp4"></video>
</div>
</div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>
