<?php
declare(strict_types=1);
require_once __DIR__ . '/../lib/auth.php';

function admin_icon(string $n): string
{
    $p = [
        'home' => '<path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/>',
        'inbox' => '<path d="M3 13l2-8h14l2 8v6H3z"/><path d="M3 13h5l1.5 3h5L16 13h5"/>',
        'pen' => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/>',
        'img' => '<rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><path d="M21 16l-5-5-8 8"/>',
        'cog' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/>',
        'ext' => '<path d="M14 4h6v6"/><path d="M20 4l-9 9"/><path d="M20 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h5"/>',
        'out' => '<path d="M10 17l5-5-5-5"/><path d="M15 12H3"/><path d="M21 3v18"/>',
        'menu' => '<path d="M4 7h16M4 12h16M4 17h16"/>',
        'plus' => '<path d="M12 5v14M5 12h14"/>',
        'search' => '<circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/>',
        'phone' => '<path d="M3 5a2 2 0 0 1 2-2h3.3a1 1 0 0 1 .9.7l1.5 4.5a1 1 0 0 1-.5 1.2l-2.3 1.1a11 11 0 0 0 5.5 5.5l1.1-2.3a1 1 0 0 1 1.2-.5l4.5 1.5a1 1 0 0 1 .7.9V19a2 2 0 0 1-2 2h-1C9.7 21 3 14.3 3 6V5z"/>',
        'mail' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>',
        'dl' => '<path d="M12 3v12"/><path d="M7 10l5 5 5-5"/><path d="M4 21h16"/>',
        'check' => '<path d="M5 12l4 4L19 7"/>',
        'x' => '<path d="M6 6l12 12M18 6L6 18"/>',
        'refresh' => '<path d="M21 12a9 9 0 1 1-2.6-6.4"/><path d="M21 3v6h-6"/>',
        'eye' => '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>',
        'link' => '<path d="M10 14a5 5 0 0 0 7 0l3-3a5 5 0 0 0-7-7l-1 1"/><path d="M14 10a5 5 0 0 0-7 0l-3 3a5 5 0 0 0 7 7l1-1"/>',
        'bolt' => '<path d="M13 2L4 14h7l-1 8 9-12h-7z"/>',
    ];
    return '<svg class="i" viewBox="0 0 24 24" aria-hidden="true">' . ($p[$n] ?? '') . '</svg>';
}

function flash(string $type, string $msg): void { auth_session(); $_SESSION['flash'][] = [$type, $msg]; }
function flashes(): string
{
    auth_session();
    if (empty($_SESSION['flash'])) return '';
    $o = '';
    foreach ($_SESSION['flash'] as [$t, $m]) $o .= '<div class="flash ' . e($t) . '">' . e($m) . '</div>';
    unset($_SESSION['flash']);
    return $o;
}

function admin_header(string $title, string $nav = ''): void
{
    $newLeads = (int) db()->query("SELECT COUNT(*) FROM leads WHERE status='ny'")->fetchColumn();
    $items = [
        ['/admin/', 'Översikt', 'home', 'dash'],
        ['/admin/leads.php', 'Förfrågningar', 'inbox', 'leads', $newLeads],
        ['/admin/posts.php', 'Blogg', 'pen', 'posts'],
        ['/admin/galleri.php', 'Galleri', 'img', 'galleri'],
        ['/admin/settings.php', 'Inställningar', 'cog', 'settings'],
    ];
    ?><!DOCTYPE html>
<html lang="sv"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow">
<title><?= e($title) ?> – Svea Hemfix admin</title>
<link rel="icon" href="/images/logo.png"><link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/admin/admin.css?v=<?= filemtime(__DIR__ . '/admin.css') ?>">
</head><body>
<div class="app">
<aside class="side" id="side">
<a href="/admin/" class="brand"><img src="/images/logo.png" alt="Svea Hemfix"><span>Admin</span></a>
<nav>
<?php foreach ($items as $it): ?>
<a href="<?= $it[0] ?>" class="<?= $nav === $it[3] ? 'on' : '' ?>"><?= admin_icon($it[2]) ?><?= $it[1] ?><?php if (!empty($it[4])): ?><b class="cnt"><?= $it[4] ?></b><?php endif; ?></a>
<?php endforeach; ?>
</nav>
<div class="side-b"><a href="/" target="_blank" rel="noopener"><?= admin_icon('ext') ?>Visa webbplatsen</a><a href="/admin/logout.php"><?= admin_icon('out') ?>Logga ut</a></div>
</aside>
<div class="main">
<header class="top"><button class="burger" id="burger" aria-label="Meny"><?= admin_icon('menu') ?></button><h1><?= e($title) ?></h1><div class="top-r"><span class="who"><?= e((string) setting('admin_email', '')) ?></span></div></header>
<main class="content">
<?= flashes() ?>
<?php
}

function admin_footer(): void
{
    ?></main></div></div>
<script src="/admin/admin.js?v=<?= filemtime(__DIR__ . '/admin.js') ?>" defer></script>
</body></html><?php
}

function status_pill(string $s): string
{
    $cls = ['ny' => 'new', 'kontaktad' => 'info', 'offert' => 'warn', 'bokad' => 'ok', 'forlorad' => 'mut', 'spam' => 'mut'][$s] ?? 'mut';
    return '<span class="pill ' . $cls . '">' . e(LEAD_STATUS[$s] ?? $s) . '</span>';
}
