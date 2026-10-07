<?php
declare(strict_types=1);
require_once __DIR__ . '/_layout.php';
require_admin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $act = (string) ($_POST['act'] ?? '');
    if ($act === 'general') {
        $ne = trim((string) ($_POST['notify_email'] ?? ''));
        if (!filter_var($ne, FILTER_VALIDATE_EMAIL)) flash('err', 'Ogiltig e-postadress för notiser.');
        else { setting_set('notify_email', $ne); setting_set('notify_cc', trim((string) ($_POST['notify_cc'] ?? ''))); flash('ok', 'Inställningar sparade.'); }
    } elseif ($act === 'blg') {
        setting_set('blg_api_key', trim((string) ($_POST['blg_api_key'] ?? '')));
        setting_set('blg_autopublish', !empty($_POST['blg_autopublish']) ? '1' : '0');
        flash('ok', 'BabyLoveGrowth-inställningar sparade.');
    } elseif ($act === 'rotate') {
        setting_set('blg_webhook_secret', bin2hex(random_bytes(20))); flash('ok', 'Ny webhook-nyckel skapad – uppdatera den i BabyLoveGrowth.');
    } elseif ($act === 'password') {
        $cur = (string) ($_POST['current'] ?? ''); $p1 = (string) ($_POST['new1'] ?? ''); $p2 = (string) ($_POST['new2'] ?? '');
        if (!password_verify($cur, (string) setting('admin_pass'))) flash('err', 'Nuvarande lösenord är fel.');
        elseif (strlen($p1) < 10) flash('err', 'Nytt lösenord måste vara minst 10 tecken.');
        elseif ($p1 !== $p2) flash('err', 'Lösenorden matchar inte.');
        else { setting_set('admin_pass', password_hash($p1, PASSWORD_DEFAULT)); flash('ok', 'Lösenordet är bytt.'); }
    } elseif ($act === 'email') {
        $em = trim((string) ($_POST['admin_email'] ?? ''));
        if (!filter_var($em, FILTER_VALIDATE_EMAIL)) flash('err', 'Ogiltig e-post.'); else { setting_set('admin_email', $em); flash('ok', 'Inloggnings-e-post uppdaterad.'); }
    }
    header('Location: /admin/settings.php'); exit;
}
$secret = (string) setting('blg_webhook_secret', '');
$hookUrl = SVEA_SITE . '/api/blg-webhook.php';
admin_header('Inställningar', 'settings');
?>
<div class="grid2">
<div>
<div class="card"><h2><?= admin_icon('bolt') ?>BabyLoveGrowth</h2>
<p class="mut" style="margin-bottom:1rem">Så kopplar du: i BabyLoveGrowth → <b>Settings → Publishing → Webhook</b> (under "For developers"). Klistra in URL och token nedan, kör "Test connection" och spara.</p>
<div class="fld"><label>Webhook-URL</label><div class="code copy" data-copy="<?= e($hookUrl) ?>" title="Klicka för att kopiera"><?= e($hookUrl) ?></div></div>
<div class="fld"><label>Webhook-token (Bearer)</label><div class="code copy" data-copy="<?= e($secret) ?>" title="Klicka för att kopiera"><?= e($secret) ?></div><small>Klicka på nyckeln för att kopiera. Byt bara om den läckt – då måste den uppdateras i BabyLoveGrowth.</small></div>
<form method="post" style="margin-bottom:1.25rem"><?= csrf_field() ?><input type="hidden" name="act" value="rotate"><button class="btn btn-o btn-s" data-confirm="Skapa ny webhook-nyckel? Den gamla slutar fungera.">Skapa ny nyckel</button></form>
<form method="post"><?= csrf_field() ?><input type="hidden" name="act" value="blg">
<div class="fld"><label for="k">API-nyckel (valfritt – för "Hämta artiklar från API")</label><input class="inp" id="k" name="blg_api_key" value="<?= e((string) setting('blg_api_key', '')) ?>" autocomplete="off" placeholder="Finns under Settings → Publishing → API i BabyLoveGrowth"></div>
<div class="fld"><label class="sw"><input type="checkbox" name="blg_autopublish" value="1" <?= setting('blg_autopublish', '1') === '1' ? 'checked' : '' ?>> Publicera nya artiklar automatiskt</label><small>Avmarkerad = artiklar sparas som utkast som du godkänner under Blogg.</small></div>
<button class="btn btn-p" type="submit">Spara</button></form>
</div>
<div class="card"><h2><?= admin_icon('mail') ?>Notiser för förfrågningar</h2>
<form method="post"><?= csrf_field() ?><input type="hidden" name="act" value="general">
<div class="fld"><label for="ne">Skicka nya förfrågningar till</label><input class="inp" id="ne" type="email" name="notify_email" value="<?= e((string) setting('notify_email', 'info@sveahemfix.se')) ?>" required></div>
<div class="fld"><label for="cc">Kopia till (kommaseparerat, valfritt)</label><input class="inp" id="cc" name="notify_cc" value="<?= e((string) setting('notify_cc', '')) ?>"></div>
<button class="btn btn-p" type="submit">Spara</button></form>
</div>
</div>
<div>
<div class="card"><h2><?= admin_icon('cog') ?>Inloggning</h2>
<form method="post" style="margin-bottom:1.5rem"><?= csrf_field() ?><input type="hidden" name="act" value="email"><div class="fld"><label for="ae">E-post för inloggning</label><input class="inp" id="ae" type="email" name="admin_email" value="<?= e((string) setting('admin_email', '')) ?>" required></div><button class="btn btn-o btn-s" type="submit">Uppdatera e-post</button></form>
<form method="post"><?= csrf_field() ?><input type="hidden" name="act" value="password">
<div class="fld"><label for="c">Nuvarande lösenord</label><input class="inp" id="c" type="password" name="current" required autocomplete="current-password"></div>
<div class="f2"><div class="fld"><label for="n1">Nytt lösenord</label><input class="inp" id="n1" type="password" name="new1" required minlength="10" autocomplete="new-password"></div><div class="fld"><label for="n2">Upprepa</label><input class="inp" id="n2" type="password" name="new2" required minlength="10" autocomplete="new-password"></div></div>
<button class="btn btn-p btn-s" type="submit">Byt lösenord</button></form>
</div>
<div class="card"><h2>Systeminfo</h2>
<dl class="dl"><dt>Databas</dt><dd>SQLite · <?= file_exists(SVEA_DB) ? round(filesize(SVEA_DB) / 1024) . ' kB' : '–' ?></dd><dt>PHP</dt><dd><?= PHP_VERSION ?></dd><dt>Webhook</dt><dd><a href="<?= e($hookUrl) ?>" target="_blank" rel="noopener">Testa (GET)</a></dd><dt>Sitemap</dt><dd><a href="/sitemap.xml" target="_blank" rel="noopener">/sitemap.xml</a> (uppdateras automatiskt)</dd></dl>
</div>
</div>
</div>
<?php admin_footer();
