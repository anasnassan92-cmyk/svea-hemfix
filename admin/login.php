<?php
declare(strict_types=1);
require_once __DIR__ . '/../lib/auth.php';
auth_session();
if (!admin_configured()) { header('Location: /admin/setup.php'); exit; }
if (is_admin()) { header('Location: /admin/'); exit; }
$err = ''; $next = (string) ($_GET['next'] ?? '/admin/');
if (!preg_match('#^/admin/#', $next)) $next = '/admin/';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $wait = throttle_check(client_ip());
    if ($wait !== null) $err = 'För många misslyckade försök. Försök igen om ' . ceil($wait / 60) . ' minuter.';
    elseif (login((string) ($_POST['email'] ?? ''), (string) ($_POST['password'] ?? ''))) { header('Location: ' . $next); exit; }
    else $err = 'Fel e-post eller lösenord.';
}
?><!DOCTYPE html><html lang="sv"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow"><title>Logga in – Svea Hemfix admin</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet"><link rel="stylesheet" href="/admin/admin.css"></head>
<body><div class="login"><form class="box" method="post" autocomplete="on">
<?= csrf_field() ?><img src="/images/logo.png" alt="Svea Hemfix"><h1>Logga in</h1><p class="sub">Administration för sveahemfix.se</p>
<?php if ($err): ?><div class="flash err"><?= e($err) ?></div><?php endif; ?>
<?php if (!empty($_GET['expired'])): ?><div class="flash info">Du loggades ut automatiskt – logga in igen.</div><?php endif; ?>
<div class="fld"><label for="em">E-post</label><input class="inp" id="em" name="email" type="email" required autofocus autocomplete="username" value="<?= e((string) ($_POST['email'] ?? '')) ?>"></div>
<div class="fld"><label for="pw">Lösenord</label><input class="inp" id="pw" name="password" type="password" required autocomplete="current-password"></div>
<button class="btn btn-p" type="submit">Logga in</button>
<p class="hint"><a href="/">← Till webbplatsen</a></p>
</form></div></body></html>
