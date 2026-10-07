<?php
require __DIR__ . '/data/site.php';
$ld = json_encode(['@context' => 'https://schema.org', '@type' => 'Service', 'name' => 'Trapphusstädning', 'serviceType' => 'Trapphusstädning', 'areaServed' => 'Stockholm', 'provider' => ['@type' => 'LocalBusiness', 'name' => 'Svea Hemfix', 'telephone' => '+46-10-300-10-32', 'url' => 'https://sveahemfix.se'], 'offers' => [['@type' => 'Offer', 'price' => '2000', 'priceCurrency' => 'SEK', 'description' => 'Fastighet med 8 våningar, 1 gång per vecka, per månad inklusive städmaterial'], ['@type' => 'Offer', 'price' => '392', 'priceCurrency' => 'SEK', 'description' => 'Per timme inklusive städmaterial']]], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$page = ['title' => 'Trapphusstädning Stockholm – BRF, fastighetsägare & företag | Svea Hemfix', 'desc' => 'Professionell och regelbunden trapphusstädning i Stockholm för bostadsrättsföreningar, fastighetsägare och företag. Fast pris från 2 000 kr/mån eller 392 kr/tim inkl. städmaterial. Kostnadsfri offert.', 'canonical' => 'https://sveahemfix.se/trapphusstadning', 'nav' => 'trapphus', 'ld' => [$ld]];
require __DIR__ . '/partials/header.php';
$cta = '/kontakt?tjanst=Trapphusst%C3%A4dning';
$ingar = ['Rengöring av entré', 'Städning av hiss', 'Rengöring av ytterdörr och entréparti', 'Städning av trapphusets golv', 'Rengöring av synliga ytor vid behov', 'Städning av gemensamma gångytor'];
$tillagg = ['Fönsterputs', 'Städning av källare', 'Städning av förråd', 'Städning av tvättstuga', 'Extra rengöring av entré', 'Rengöring av hiss', 'Dammtorkning av räcken och ledstänger', 'Rengöring av dörrar och glaspartier', 'Golvvård', 'Maskinskurning', 'Extra städning vid behov', 'Grovstädning', 'Städning efter renovering', 'Snö- och enklare utomhusrelaterade tjänster efter överenskommelse'];
$avtal = ['Bostadsrättsföreningar', 'Hyresfastigheter', 'Fastighetsägare', 'Kontor', 'Företag', 'Kommersiella fastigheter'];
?>
<section class="hero hero-sub">
<div class="wrap">
<div><span class="kicker">För BRF, fastighetsägare &amp; företag</span><h1>Trapphusstädning i Stockholm med omnejd</h1>
<p class="lead">Svea Hemfix erbjuder professionell och regelbunden trapphusstädning för bostadsrättsföreningar, fastighetsägare, företag och andra fastigheter i Stockholm och närområdet. Vi anpassar städningen efter fastighetens storlek, antal våningar och behov – med en pålitlig service och <strong>tydliga priser</strong>.</p>
<div class="acts"><a class="btn btn-p" href="<?= $cta ?>">Begär kostnadsfri offert</a><a class="btn btn-o" href="#priser">Se priser</a></div>
<div class="trust"><div><?= ico('users') ?>En kontaktperson</div><div><?= ico('shield') ?>Försäkrat</div><div><?= ico('wallet') ?>Fast månadspris</div></div></div>
<div class="hero-img hero-vid"><video autoplay muted loop playsinline poster="/media/v/v27.webp" aria-label="Trapphusstädning"><source src="/media/v/v27.mp4" type="video/mp4"></video></div>
</div>
</section>
<section class="sec">
<div class="wrap"><div class="center"><span class="kicker">Standardstädning</span><h2>Detta ingår i vår standardstädning</h2></div>
<div class="grid2">
<div class="box"><h3>Detta ingår</h3><ul class="checks"><?php foreach ($ingar as $i) echo '<li>' . ico('check') . $i . '</li>'; ?></ul></div>
<div class="box"><h3>Vårt mål</h3><p>Vi anpassar städningen efter fastighetens storlek, antal våningar och behov. Vårt mål är att skapa rena, välkomnande och välskötta gemensamma utrymmen – med en pålitlig service och tydliga priser.</p><p style="margin-top:.75rem">Med ett löpande städavtal får ni en kontaktperson, tydlig omfattning och en städning som anpassas efter fastighetens behov.</p></div>
</div></div>
</section>
<section class="sec sec-alt" id="priser">
<div class="wrap"><div class="center"><span class="kicker">Priser</span><h2>Tydliga priser – inklusive städmaterial</h2></div>
<div class="plans">
<div class="plan hi"><span class="tag">Exempel – fastighet med 8 våningar</span><h3>Frekvens: 1 gång per vecka</h3><div class="price">2 000 kr <small>/månad</small></div><p class="pre">inklusive städmaterial</p><p class="pre">Alternativt: <strong>392 kr/timme</strong> inklusive städmaterial</p><ul><li><?= ico('check') ?>Priset kan anpassas efter fastighetens storlek, antal trapphus och städningens omfattning</li></ul><a class="btn btn-p" href="<?= $cta ?>">Begär offert</a></div>
<div class="plan"><span class="tag">Större fastigheter</span><h3>Extra våningar och fler trapphus</h3><div class="price">Offert</div><p class="pre">Individuellt pris</p><ul><li><?= ico('check') ?>Har fastigheten fler än 8 våningar eller flera trapphus lämnar vi gärna en individuell offert</li><li><?= ico('check') ?>Paketlösningar för större fastigheter och fastighetsbestånd</li></ul><a class="btn btn-o" href="<?= $cta ?>">Begär offert</a></div>
<div class="plan"><span class="tag">Extra arbete</span><h3>Pris för extra arbete</h3><div class="price">392 kr <small>/timme</small></div><p class="pre">inklusive material</p><ul><li><?= ico('check') ?>För större eller återkommande arbeten lämnar vi gärna ett fast pris efter besiktning eller offertförfrågan</li></ul><a class="btn btn-o" href="<?= $cta ?>">Fråga om fast pris</a></div>
</div></div>
</section>
<section class="sec">
<div class="wrap"><div class="center"><span class="kicker">Tilläggstjänster</span><h2>Tilläggstjänster</h2><p class="lead">Utöver den regelbundna trapphusstädningen kan vi hjälpa till med:</p></div>
<div class="box" style="margin-top:2.5rem"><div class="grid2" style="margin-top:0"><ul class="checks"><?php foreach (array_slice($tillagg, 0, 7) as $i) echo '<li>' . ico('check') . $i . '</li>'; ?></ul><ul class="checks"><?php foreach (array_slice($tillagg, 7) as $i) echo '<li>' . ico('check') . $i . '</li>'; ?></ul></div></div></div>
</section>
<section class="sec sec-alt">
<div class="wrap"><div class="center"><span class="kicker">Avtal &amp; regelbunden service</span><h2>Avtal och regelbunden service</h2></div>
<div class="grid2">
<div class="box"><h3>Vi erbjuder löpande städavtal för exempelvis</h3><ul class="checks"><?php foreach ($avtal as $i) echo '<li>' . ico('check') . $i . '</li>'; ?></ul><p style="margin-top:1rem">Avtal kan tecknas för 12 månader, eller enligt överenskommelse.</p></div>
<div class="box"><h3>Varför välja Svea Hemfix?</h3><p>Vi arbetar långsiktigt och professionellt med fokus på kvalitet, punktlighet och bra kundservice.</p><p style="margin:.75rem 0 1rem">Med ett löpande städavtal får ni en kontaktperson, tydlig omfattning och en städning som anpassas efter fastighetens behov.</p><ul class="checks"><li><?= ico('check') ?>Kvalitet och punktlighet</li><li><?= ico('check') ?>En kontaktperson</li><li><?= ico('check') ?>Tydlig omfattning och tydliga priser</li><li><?= ico('check') ?>Försäkrad och miljövänlig städning</li></ul></div>
</div></div>
</section>
<?php $ms = ['cats' => ['trapphus', 'kontor'], 'limit' => 8, 'title' => 'Trapphus, entréer och lokaler vi städar', 'kicker' => 'Vårt arbete']; require __DIR__ . '/partials/media-strip.php'; ?>
<section class="sec">
<div class="wrap"><div class="cta"><div class="cta-c"><h2>Kontakta Svea Hemfix för en kostnadsfri offert</h2><p>Vi kan komma ut och titta på fastigheten och därefter lämna ett tydligt förslag på pris och omfattning.<br><em>Svea Hemfix – Professionell städning för rena och välskötta fastigheter.</em></p><div class="acts"><a class="btn btn-y" href="<?= $cta ?>">Begär offert</a><a class="btn btn-g" href="tel:0103001032"><?= ico('phone') ?>Ring 010 300 10 32</a><a class="btn btn-g" href="mailto:info@sveahemfix.se"><?= ico('mail') ?>info@sveahemfix.se</a></div></div><video class="cta-v" autoplay muted loop playsinline preload="metadata" poster="/media/v/v27.webp" aria-hidden="true"><source src="/media/v/v27.mp4" type="video/mp4"></video></div></div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
