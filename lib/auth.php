<?php
/**
 * Admin authentication: session, CSRF, login throttling, first-run setup.
 */
declare(strict_types=1);
require_once __DIR__ . '/app.php';

function auth_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) return;
    session_name('SVEAADM');
    session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'secure' => !empty($_SERVER['HTTPS']), 'httponly' => true, 'samesite' => 'Lax']);
    session_start();
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

function csrf_token(): string { auth_session(); return $_SESSION['csrf']; }
function csrf_field(): string { return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">'; }
function csrf_check(): void
{
    auth_session();
    $t = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF'] ?? '';
    if (!is_string($t) || !hash_equals($_SESSION['csrf'], $t)) { http_response_code(419); exit('Ogiltig session – ladda om sidan och försök igen.'); }
}

function admin_configured(): bool { return (bool) setting('admin_pass'); }
function is_admin(): bool { auth_session(); return !empty($_SESSION['admin']) && admin_configured(); }

function require_admin(): void
{
    if (!admin_configured()) { header('Location: /admin/setup.php'); exit; }
    if (!is_admin()) { header('Location: /admin/login.php?next=' . rawurlencode($_SERVER['REQUEST_URI'] ?? '/admin/')); exit; }
    // idle timeout 8h
    if (!empty($_SESSION['t']) && time() - (int) $_SESSION['t'] > 8 * 3600) { session_destroy(); header('Location: /admin/login.php?expired=1'); exit; }
    $_SESSION['t'] = time();
}

function throttle_check(string $ip): ?int
{
    $st = db()->prepare('SELECT attempts, last_at FROM login_attempts WHERE ip=?'); $st->execute([$ip]);
    $r = $st->fetch();
    if ($r && (int) $r['attempts'] >= 5 && time() - (int) $r['last_at'] < 900) return 900 - (time() - (int) $r['last_at']);
    return null;
}
function throttle_fail(string $ip): void
{
    db()->prepare('INSERT INTO login_attempts(ip,attempts,last_at) VALUES(?,1,?) ON CONFLICT(ip) DO UPDATE SET attempts=CASE WHEN ?-last_at>900 THEN 1 ELSE attempts+1 END, last_at=?')->execute([$ip, time(), time(), time()]);
}
function throttle_clear(string $ip): void { db()->prepare('DELETE FROM login_attempts WHERE ip=?')->execute([$ip]); }

function login(string $email, string $pass): bool
{
    $ip = client_ip();
    if (throttle_check($ip) !== null) return false;
    $ok = strcasecmp(trim($email), (string) setting('admin_email', '')) === 0 && password_verify($pass, (string) setting('admin_pass', ''));
    if (!$ok) { throttle_fail($ip); return false; }
    throttle_clear($ip);
    session_regenerate_id(true);
    $_SESSION['admin'] = true; $_SESSION['t'] = time();
    return true;
}

function logout(): void { auth_session(); $_SESSION = []; session_destroy(); }
