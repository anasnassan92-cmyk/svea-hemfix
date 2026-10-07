<?php
require __DIR__ . '/data/site.php';
$page = ['title' => 'Priser – Städning i Stockholm från ' . SVEA_HOURLY_RUT . ' kr/tim efter RUT | Svea Hemfix', 'desc' => 'Tydlig prislista för hemstädning, flyttstädning, fönsterputs m.m. i Stockholm. ' . SVEA_HOURLY . ' kr/tim före RUT – ' . SVEA_HOURLY_RUT . ' kr/tim efter RUT-avdrag. Städmaterial ingår.', 'canonical' => 'https://sveahemfix.se/priser', 'nav' => 'priser'];
require __DIR__ . '/partials/header.php';
?>
<section class="hero hero-sub">
<div class="wrap">
<div><span class="kicker">Priser · Stockholm</span><h1>Tydliga priser utan överraskningar</h1>
<p class="lead">Samma timpris för alla hushållsnära tjänster: <strong><?= SVEA_HOURLY ?> kr/tim – <?= SVEA_HOURLY_RUT ?> kr/tim efter RUT</strong>. Städmaterial ingår, RUT dras av direkt på fakturan och du får alltid ett fast pris i offerten innan vi börjar.</p>
<div class="acts"><a class="btn btn-p" href="/kontakt">Begär offert</a><a class="btn btn-o" href="#prislista">Se prislistan</a></div></div>
<div class="hero-img hero-vid"><video autoplay muted loop playsinline poster="/media/v/v01.webp" aria-label="Fönsterputsning"><source src="/media/v/v01.mp4" type="video/mp4"></video><div class="badge"><b>50%</b><span>RUT-<br>avdrag</span></div></div>
</div>
</section>
<section class="sec" id="prislista">
<div class="wrap">
<div class="center"><span class="kicker">Prislista</span><h2>Alla våra priser</h2><p class="lead">Timpris inkl. moms. Kolumnen "Du betalar" visar priset efter RUT-avdrag.</p></div>
<div class="ptw"><table class="pt"><thead><tr><th>Tjänst</th><th>Pris före RUT</th><th>Du betalar (efter RUT)</th><th>Uppskattad tid</th></tr></thead><tbody>
<?php foreach (SVEA_SERVICES as $s): ?>
<tr><td><a href="/tjanst/<?= $s['slug'] ?>"><?= $s['name'] ?></a></td><td><?= $s['rut'] ? SVEA_HOURLY . ' kr/tim' : 'från ' . SVEA_HOURLY . ' kr/tim exkl. moms' ?></td><td class="rut-p"><?= $s['rut'] ? SVEA_HOURLY_RUT . ' kr/tim' : '–' ?></td><td><?= $s['dur'] ?></td></tr>
<?php endforeach; ?>
<tr><td><a href="/trapphusstadning">Trapphusstädning (BRF/företag)</a></td><td>392 kr/tim inkl. material<br><small style="color:var(--mut)">eller 2 000 kr/mån (8 vån, 1 ggr/vecka)</small></td><td class="rut-p">–</td><td>Enligt avtal</td></tr>
</tbody></table>
<p style="font-size:.85rem;color:var(--mut);margin:1rem .5rem .25rem">Alla priser inkl. moms för privatpersoner. RUT-avdraget (50 % av arbetskostnaden) dras av direkt på fakturan. Städmaterial och utrustning ingår alltid. Exakt pris får du i din kostnadsfria offert.</p></div>
</div>
</section>
<section class="sec sec-alt center">
<div class="wrap"><span class="kicker">Prisexempel</span><h2>Vad kostar det för mig?</h2>
<div class="ex">
<div class="box"><span class="kicker">Hemstädning</span><h3>2:a på 55 kvm</h3><p>Ca 2 timmar varannan vecka</p><div class="price">449 kr <small>/tillfälle</small></div><p>898 kr före RUT</p></div>
<div class="box"><span class="kicker">Hemstädning</span><h3>Villa på 140 kvm</h3><p>Ca 4 timmar varannan vecka</p><div class="price">898 kr <small>/tillfälle</small></div><p>1 796 kr före RUT</p></div>
<div class="box"><span class="kicker">Flyttstädning</span><h3>3:a på 75 kvm</h3><p>Ca 5 timmar, inkl. fönsterputs</p><div class="price">2 245 kr <small>efter RUT</small></div><p>4 490 kr före RUT · fast pris i offert</p></div>
</div>
<p class="lead" style="margin-top:1.5rem;font-size:.9rem">Exemplen är riktvärden baserade på timpriset. Tidsåtgången beror på bostadens skick och storlek.</p></div>
</section>
<section class="sec">
<div class="wrap"><div class="center"><span class="kicker">RUT-avdrag</span><h2>Halva priset – helt lagligt</h2></div>
<div class="grid2">
<div class="box"><h3>Så fungerar RUT-avdraget</h3><ul class="checks"><li><?= ico('check') ?>Gäller privatpersoner som betalar skatt i Sverige</li><li><?= ico('check') ?>Du betalar bara 50 % av arbetskostnaden – vi sköter ansökan till Skatteverket</li><li><?= ico('check') ?>Max 75 000 kr i avdrag per person och år</li><li><?= ico('check') ?>Avdraget syns direkt på din faktura – inget att göra själv</li></ul><p style="margin-top:1rem;font-size:.9rem;color:var(--mut)">RUT gäller inte företag, bostadsrättsföreningar eller fastighetsägare. För dem lämnar vi fast pris exkl. moms.</p></div>
<div class="box"><h3>Vad påverkar priset?</h3><ul class="checks"><li><?= ico('check') ?>Bostadens storlek och antal rum</li><li><?= ico('check') ?>Skick – djuprengöring tar längre tid än underhållsstädning</li><li><?= ico('check') ?>Hur ofta – regelbunden städning går snabbare</li><li><?= ico('check') ?>Tillval som fönsterputs, ugn, kyl/frys eller balkong</li><li><?= ico('check') ?>Restid utanför Stockholms närområde</li></ul><p style="margin-top:1rem;font-size:.9rem;color:var(--mut)">Inga dolda avgifter. Avbokning är kostnadsfri fram till 24 timmar innan.</p></div>
</div></div>
</section>
<?php $ms = ['ba' => true, 'limit' => 8, 'title' => 'Det här får du för pengarna', 'kicker' => 'Före &amp; efter', 'lead' => 'Riktiga resultat från riktiga uppdrag – klicka för att förstora.']; require __DIR__ . '/partials/media-strip.php'; ?>
<section class="sec sec-alt">
<div class="wrap"><div class="cta"><div class="cta-c"><h2>Vill du ha ett exakt pris?</h2><p>Berätta om din bostad så återkommer vi med ett fast pris inom 24 timmar.</p><div class="acts"><a class="btn btn-y" href="/kontakt">Begär offert</a><a class="btn btn-g" href="tel:0103001032"><?= ico('phone') ?>Ring 010 300 10 32</a></div></div><video class="cta-v" autoplay muted loop playsinline preload="metadata" poster="/media/v/v15.webp" aria-hidden="true"><source src="/media/v/v15.mp4" type="video/mp4"></video></div></div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
