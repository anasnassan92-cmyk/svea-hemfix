<?php
require __DIR__ . '/data/site.php';
$page = ['title' => 'Integritetspolicy | Svea Hemfix', 'desc' => 'Så behandlar Svea Hemfix dina personuppgifter enligt GDPR.', 'canonical' => 'https://sveahemfix.se/integritetspolicy'];
require __DIR__ . '/partials/header.php';
?>
<section class="sec"><div class="wrap legal-page">
<span class="kicker">GDPR</span><h1 style="font-size:2.2rem;margin-bottom:.5rem">Integritetspolicy</h1><p style="color:var(--mut)">Senast uppdaterad: <?= date('Y-m-d') ?></p>
<h2>1. Personuppgiftsansvarig</h2><p>Svea Hemfix (<?= SVEA_COMPANY ?>), org.nr <?= SVEA_ORGNR ?>, <?= SVEA_ADDRESS ?>, e-post <a href="mailto:info@sveahemfix.se">info@sveahemfix.se</a>, telefon 010 300 10 32, är personuppgiftsansvarig för behandlingen av dina personuppgifter på denna webbplats.</p>
<h2>2. Vilka uppgifter samlar vi in?</h2><p>När du skickar en offertförfrågan eller bokning via vårt formulär, per telefon, e-post eller WhatsApp samlar vi in de uppgifter du lämnar: namn, telefonnummer, e-postadress, adress, postnummer, bostadens storlek, önskad tjänst, önskat datum och ditt meddelande.</p>
<h2>3. Varför behandlar vi uppgifterna?</h2><p>För att besvara din förfrågan, lämna offert, planera och utföra städtjänsten, fakturera samt – om du är privatperson – ansöka om RUT-avdrag hos Skatteverket för din räkning. Rättslig grund är fullgörande av avtal (art. 6.1 b GDPR) och rättslig förpliktelse (bokföring och RUT, art. 6.1 c).</p>
<h2>4. Hur länge sparas uppgifterna?</h2><p>Förfrågningar som inte leder till uppdrag raderas senast 12 månader efter sista kontakt. Kund- och fakturauppgifter sparas så länge kundrelationen består och därefter enligt bokföringslagen (7 år).</p>
<h2>5. Vem får ta del av uppgifterna?</h2><p>Uppgifterna hanteras av Svea Hemfix och våra personuppgiftsbiträden för webbhotell, e-post och fakturering. Vid RUT-avdrag lämnas nödvändiga uppgifter till Skatteverket. Vi säljer aldrig dina uppgifter vidare.</p>
<h2>6. Cookies</h2><p>Webbplatsen använder inga spårnings- eller marknadsföringscookies.</p>
<h2>7. Dina rättigheter</h2><p>Du har rätt att begära tillgång till, rättelse eller radering av dina personuppgifter, att invända mot behandlingen samt att få dina uppgifter flyttade. Kontakta oss på <a href="mailto:info@sveahemfix.se">info@sveahemfix.se</a>. Du kan även lämna klagomål till Integritetsskyddsmyndigheten (IMY).</p>
</div></section>
<?php require __DIR__ . '/partials/footer.php'; ?>
