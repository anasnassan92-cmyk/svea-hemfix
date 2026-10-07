<?php
$page = $page ?? [];
$t = $page['title'] ?? 'Svea Hemfix';
$d = $page['desc'] ?? 'Professionell städning i Stockholm med omnejd. Upp till 50% RUT-avdrag.';
$c = $page['canonical'] ?? 'https://sveahemfix.se/';
$cur = $page['nav'] ?? '';
function ico(string $n, string $cls = 'ic'): string {
    static $p = [
        'phone' => '<path d="M3 5a2 2 0 0 1 2-2h3.3a1 1 0 0 1 .9.7l1.5 4.5a1 1 0 0 1-.5 1.2l-2.3 1.1a11 11 0 0 0 5.5 5.5l1.1-2.3a1 1 0 0 1 1.2-.5l4.5 1.5a1 1 0 0 1 .7.9V19a2 2 0 0 1-2 2h-1C9.7 21 3 14.3 3 6V5z"/>',
        'leaf' => '<path d="M5 20c0-8 4-14 14-14-1 9-5 13-14 14z"/><path d="M5 20c2-5 5-8 9-11"/>',
        'shield' => '<path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6l8-3z"/><path d="M9 12l2 2 4-4"/>',
        'award' => '<circle cx="12" cy="9" r="5"/><path d="M9 13l-1.5 8 4.5-2.5L16.5 21 15 13"/>',
        'home' => '<path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/><path d="M10 20v-6h4v6"/>',
        'spark' => '<path d="M12 3v4M12 17v4M3 12h4M17 12h4M5.6 5.6l2.8 2.8M15.6 15.6l2.8 2.8M18.4 5.6l-2.8 2.8M8.4 15.6l-2.8 2.8"/>',
        'office' => '<rect x="4" y="3" width="16" height="18" rx="2"/><path d="M9 7h2M13 7h2M9 11h2M13 11h2M9 15h2M13 15h2M10 21v-3h4v3"/>',
        'truck' => '<path d="M3 7h11v9H3zM14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/>',
        'window' => '<rect x="4" y="3" width="16" height="18" rx="1.5"/><path d="M12 3v18M4 12h16"/>',
        'stairs' => '<path d="M4 20h4v-4h4v-4h4V8h4"/><path d="M4 20V4"/>',
        'check' => '<path d="M5 12l4 4L19 7"/>',
        'mail' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>',
        'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'chat' => '<path d="M21 12a8 8 0 0 1-11.6 7.1L4 21l1.9-4.6A8 8 0 1 1 21 12z"/>',
        'pin' => '<path d="M12 21s-6-5.5-6-11a6 6 0 0 1 12 0c0 5.5-6 11-6 11z"/><circle cx="12" cy="10" r="2"/>',
        'wallet' => '<rect x="3" y="6" width="18" height="13" rx="2"/><path d="M3 10h18M16 14h2"/>',
        'users' => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><circle cx="17" cy="9" r="2.5"/><path d="M15.5 14.5A5 5 0 0 1 21.5 20"/>',
        'menu' => '<path d="M4 7h16M4 12h16M4 17h16"/>',
        'play' => '<circle cx="12" cy="12" r="9"/><path d="M10 8.5v7l5-3.5z"/>',
        'sofa' => '<path d="M4 11V8a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3"/><path d="M2 13a2 2 0 0 1 2-2 2 2 0 0 1 2 2v3h12v-3a2 2 0 0 1 4 0v5H2z"/>',
        'drop' => '<path d="M12 3s6 7 6 11a6 6 0 0 1-12 0c0-4 6-11 6-11z"/>',
        'plus' => '<path d="M12 5v14M5 12h14"/>',
    ];
    return '<svg class="' . $cls . '" viewBox="0 0 24 24" aria-hidden="true">' . ($p[$n] ?? '') . '</svg>';
}
$navItems = [['/', 'Hem', 'hem'], ['/tjanster', 'Tjänster', 'tjanster'], ['/priser', 'Priser', 'priser'], ['/galleri', 'Galleri', 'galleri'], ['/blogg', 'Blogg', 'blogg'], ['/kontakt', 'Kontakt', 'kontakt']];
$svcNav = defined('SVEA_SERVICES') ? SVEA_SERVICES : [];
?><!DOCTYPE html>
<html lang="sv-SE">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($t) ?></title>
<meta name="description" content="<?= htmlspecialchars($d) ?>">
<link rel="canonical" href="<?= htmlspecialchars($c) ?>">
<meta property="og:title" content="<?= htmlspecialchars($t) ?>">
<meta property="og:description" content="<?= htmlspecialchars($d) ?>">
<meta property="og:type" content="website">
<meta property="og:url" content="<?= htmlspecialchars($c) ?>">
<meta property="og:image" content="<?= htmlspecialchars($page['og_image'] ?? 'https://sveahemfix.se/media/p/p12.webp') ?>">
<meta name="theme-color" content="#2E7D32">
<link rel="icon" type="image/png" href="/images/logo.png">
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/site.css?v=<?= filemtime(__DIR__ . '/../assets/site.css') ?>">
<?php foreach (($page['ld'] ?? []) as $ld) echo '<script type="application/ld+json">' . $ld . "</script>\n"; ?>
</head>
<body>
<header class="hdr">
<div class="wrap">
<a href="/" class="logo" aria-label="Svea Hemfix – startsida"><img src="/images/logo.png" alt="Svea Hemfix" width="180" height="79"></a>
<nav class="nav" aria-label="Huvudmeny"><?php foreach ($navItems as [$h, $l, $k]): if ($k === 'tjanster'): ?><div class="dd"><a href="/tjanster" class="<?= $cur === 'tjanster' ? 'on' : '' ?>" aria-haspopup="true" aria-expanded="false">Tjänster <svg class="ic ic-dd" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg></a>
<div class="dd-m"><div class="dd-cols"><?php foreach (SVEA_GROUPS as $gk => $gl): ?><div class="dd-col"><b class="dd-h"><?= $gl ?></b><div class="dd-g"><?php foreach ($svcNav as $sv) if (($sv['group'] ?? 'privat') === $gk) echo '<a href="/tjanst/' . $sv['slug'] . '">' . ico($sv['icon']) . '<span><b>' . $sv['name'] . '</b><small>' . $sv['short'] . '</small></span></a>'; ?><?php if ($gk === 'foretag'): ?><a href="/trapphusstadning"><?= ico('stairs') ?><span><b>Trapphusstädning</b><small>Regelbunden städning av trapphus, entré och hiss för BRF och fastighetsägare.</small></span></a><?php endif; ?></div></div><?php endforeach; ?></div><div class="dd-f"><a href="/tjanster" class="btn btn-p">Alla tjänster</a><a href="/priser" class="btn btn-o">Se priser</a></div></div></div>
<?php else: echo '<a href="' . $h . '"' . ($k && $k === $cur ? ' class="on"' : '') . '>' . $l . '</a>'; endif; endforeach; ?></nav>
<div class="hdr-r">
<a class="call" href="tel:0103001032"><?= ico('phone') ?><span>Ring oss<b>010 300 10 32</b></span></a>
<a class="btn btn-y" href="/kontakt">Få offert</a>
<button class="burger" id="burger" aria-label="Öppna meny" aria-expanded="false" aria-controls="mnav"><?= ico('menu') ?></button>
</div>
</div>
<nav class="mnav" id="mnav" aria-label="Mobilmeny"><?php foreach ($navItems as [$h, $l, $k]): if ($k === 'tjanster'): ?><button type="button" class="macc" aria-expanded="false">Tjänster <svg class="ic ic-dd" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg></button><div class="macc-p"><?php foreach (SVEA_GROUPS as $gk => $gl): ?><small class="macc-h"><?= $gl ?></small><?php foreach ($svcNav as $sv) if (($sv['group'] ?? 'privat') === $gk) echo '<a href="/tjanst/' . $sv['slug'] . '">' . $sv['name'] . '</a>'; if ($gk === 'foretag') echo '<a href="/trapphusstadning">Trapphusstädning</a>'; endforeach; ?><a href="/tjanster"><b>Alla tjänster →</b></a></div>
<?php else: echo '<a href="' . $h . '">' . $l . '</a>'; endif; endforeach; ?><a href="tel:0103001032">Ring 010 300 10 32</a></nav>
</header>
<main>
