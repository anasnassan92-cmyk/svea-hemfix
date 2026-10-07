<?php
/**
 * First-run setup: creates the admin account. Only works while no admin password exists.
 */
declare(strict_types=1);
require_once __DIR__ . '/../lib/auth.php';
auth_session();
if (admin_configured()) { header('Location: /admin/login.php'); exit; }
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $email = trim((string) ($_POST['email'] ?? '')); $p1 = (string) ($_POST['password'] ?? ''); $p2 = (string) ($_POST['password2'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $err = 'Ange en giltig e-postadress.';
    elseif (strlen($p1) < 10) $err = 'Lösenordet måste vara minst 10 tecken.';
    elseif ($p1 !== $p2) $err = 'Lösenorden matchar inte.';
    else {
        setting_set('admin_email', $email);
        setting_set('admin_pass', password_hash($p1, PASSWORD_DEFAULT));
        setting_set('notify_email', 'info@sveahemfix.se');
        setting_set('notify_cc', 'info@mizagroup.se');
        setting_set('blg_autopublish', '1');
        setting_set('blg_webhook_secret', bin2hex(random_bytes(20)));
        session_regenerate_id(true); $_SESSION['admin'] = true; $_SESSION['t'] = time();
        header('Location: /admin/?welcome=1'); exit;
    }
}
?><!DOCTYPE html><html lang="sv"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow"><title>Skapa administratör – Svea Hemfix</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet"><link rel="stylesheet" href="/admin/admin.css"></head>
<body><div class="login"><form class="box" method="post">
<?= csrf_field() ?><img src="/images/logo.png" alt="Svea Hemfix"><h1>Skapa administratör</h1><p class="sub">Detta görs bara en gång. Välj e-post och ett starkt lösenord.</p>
<?php if ($err): ?><div class="flash err"><?= e($err) ?></div><?php endif; ?>
<div class="fld"><label for="em">E-post (inloggning)</label><input class="inp" id="em" name="email" type="email" required value="<?= e((string) ($_POST['email'] ?? 'info@sveahemfix.se')) ?>" autocomplete="username"></div>
<div class="fld"><label for="pw">Lösenord (minst 10 tecken)</label><input class="inp" id="pw" name="password" type="password" required minlength="10" autocomplete="new-password"></div>
<div class="fld"><label for="pw2">Upprepa lösenord</label><input class="inp" id="pw2" name="password2" type="password" required minlength="10" autocomplete="new-password"></div>
<button class="btn btn-p" type="submit">Skapa konto och logga in</button>
</form></div></body></html>
