<?php
require __DIR__ . '/data/site.php';
$ld = json_encode(['@context' => 'https://schema.org', '@type' => 'ContactPage', 'name' => 'Kontakta Svea Hemfix', 'url' => 'https://sveahemfix.se/kontakt', 'mainEntity' => ['@type' => 'LocalBusiness', 'name' => 'Svea Hemfix', 'telephone' => '+46-10-300-10-32', 'email' => 'info@sveahemfix.se', 'address' => ['@type' => 'PostalAddress', 'streetAddress' => 'Kronborgsgränd 19', 'postalCode' => '164 46', 'addressLocality' => 'Kista', 'addressCountry' => 'SE']]], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$page = ['title' => 'Kontakt & offert – Svea Hemfix | Städning i Stockholm', 'desc' => 'Begär kostnadsfri offert på städning i Stockholm. Ring 010 300 10 32, skicka formuläret eller WhatsApp – vi svarar inom 24 timmar.', 'canonical' => 'https://sveahemfix.se/kontakt', 'nav' => 'kontakt', 'ld' => [$ld]];
require __DIR__ . '/partials/header.php';
?>
<section class="hero hero-sub">
<div class="wrap">
<div><span class="kicker">Kontakt</span><h1>Kontakta oss – svar inom 24 timmar</h1>
<p class="lead">Ring, skriv eller skicka en offertförfrågan. Vi hjälper dig att välja rätt städning och ger ett fast pris – helt kostnadsfritt.</p>
<div class="acts"><a class="btn btn-p" href="tel:0103001032"><?= ico('phone') ?>Ring 010 300 10 32</a><a class="btn btn-o" href="https://wa.me/46103001032" target="_blank" rel="noopener"><?= ico('chat') ?>WhatsApp</a><a class="btn btn-o" href="#kontakt">Skicka förfrågan</a></div>
<div class="trust"><div><?= ico('clock') ?>Svar inom 24 h</div><div><?= ico('wallet') ?>Fast pris i offerten</div><div><?= ico('shield') ?>Inga förpliktelser</div></div></div>
<div class="hero-img hero-vid"><video autoplay muted loop playsinline poster="/media/v/v21.webp" aria-label="Svea Hemfix personal"><source src="/media/v/v21.mp4" type="video/mp4"></video></div>
</div>
</section>
<section class="sec" id="kontakt">
<div class="wrap">
<div class="center"><span class="kicker">Offertförfrågan</span><h2>Berätta om ditt hem – vi återkommer inom 24 timmar</h2><p class="lead">Fyll i formuläret så får du ett fast pris. Kostnadsfritt och utan förpliktelser.</p></div>
<div class="contact">
<form class="form" id="contact-form" action="/sendmail.php" method="POST">
<input type="text" name="_gotcha" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px" aria-hidden="true">
<div class="f2"><div class="fld"><label for="c-name">Namn *</label><input id="c-name" name="name" required></div><div class="fld"><label for="c-phone">Telefon *</label><input id="c-phone" name="phone" type="tel" required></div></div>
<div class="fld"><label for="c-email">E-post *</label><input id="c-email" name="email" type="email" required></div>
<div class="f2"><div class="fld"><label for="c-addr">Adress</label><input id="c-addr" name="address"></div><div class="fld"><label for="c-postal">Postnummer</label><input id="c-postal" name="postal"></div></div>
<div class="f2"><div class="fld"><label for="c-service">Önskad tjänst *</label><select id="c-service" name="service" required><option value="">Välj tjänst</option><?php foreach (SVEA_SERVICES as $s) echo '<option>' . $s['name'] . '</option>'; ?><option>Trapphusstädning</option><option>Annat / vet ej</option></select></div><div class="fld"><label for="c-date">Önskat datum</label><input id="c-date" name="date" type="date"></div></div>
<div class="f2"><div class="fld"><label for="c-size">Bostadens storlek (kvm)</label><input id="c-size" name="size" type="number" min="10" max="2000" placeholder="t.ex. 75"></div><div class="fld"><label for="c-freq">Hur ofta?</label><select id="c-freq" name="frequency"><option value="">Välj</option><option>Engångsstädning</option><option>Varje vecka</option><option>Varannan vecka</option><option>En gång i månaden</option><option>Vet ej ännu</option></select></div></div>
<div class="fld"><label for="c-msg">Meddelande</label><textarea id="c-msg" name="message"></textarea></div>
<label class="gdpr"><input type="checkbox" name="gdpr" required><span>Jag godkänner att Svea Hemfix behandlar mina uppgifter enligt <a href="/integritetspolicy">integritetspolicyn</a>. *</span></label>
<button class="btn btn-p" type="submit">Skicka förfrågan</button>
<p class="note">Kostnadsfritt och utan förpliktelser. Vi svarar inom 24 timmar – ofta samma dag.</p>
</form>
<div class="info">
<h3>Kontaktinformation</h3>
<ul>
<li><?= ico('phone') ?><div><small>Telefon</small><b><a href="tel:0103001032">010 300 10 32</a></b></div></li>
<li><?= ico('mail') ?><div><small>E-post</small><b><a href="mailto:info@sveahemfix.se">info@sveahemfix.se</a></b></div></li>
<li><?= ico('chat') ?><div><small>WhatsApp</small><b><a href="https://wa.me/46103001032" target="_blank" rel="noopener">010 300 10 32</a></b></div></li>
<li><?= ico('clock') ?><div><small>Öppettider</small><b>Mån–Fre 08–18 · Lör–Sön 09–15</b></div></li>
</ul>
<div class="areas"><h4>Serviceområden</h4><?php foreach (SVEA_AREAS as $a) echo '<span>' . $a . '</span>'; ?></div>
</div>
</div>
</div>
</section>
<section class="sec sec-alt center">
<div class="wrap"><span class="kicker">Så fungerar det</span><h2>Från förfrågan till skinande rent</h2>
<div class="steps"><div class="step"><i>1</i><h3>Skicka förfrågan</h3><p>Formulär, telefon eller WhatsApp – välj det som passar dig.</p></div><div class="step"><i>2</i><h3>Fast pris inom 24 h</h3><p>Vi återkommer med ett tydligt pris, ofta samma dag.</p></div><div class="step"><i>3</i><h3>Vi städar</h3><p>Välj tid – vi kommer med all utrustning. Nöjdhetsgaranti.</p></div></div></div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
