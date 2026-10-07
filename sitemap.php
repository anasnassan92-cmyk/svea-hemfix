<?php
require __DIR__ . '/data/site.php';
require_once __DIR__ . '/lib/app.php';
header('Content-Type: application/xml; charset=utf-8');
$base = 'https://sveahemfix.se';
$urls = [['/', '1.0', 'weekly'], ['/tjanster', '0.9', 'monthly'], ['/priser', '0.9', 'monthly'], ['/trapphusstadning', '0.9', 'monthly'], ['/galleri', '0.7', 'monthly'], ['/kontakt', '0.8', 'monthly'], ['/blogg', '0.8', 'weekly'], ['/integritetspolicy', '0.2', 'yearly']];
foreach (SVEA_SERVICES as $s) $urls[] = ['/tjanst/' . $s['slug'], '0.8', 'monthly'];
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as [$u, $pr, $cf]) echo "<url><loc>$base$u</loc><changefreq>$cf</changefreq><priority>$pr</priority></url>\n";
try {
    foreach (db()->query("SELECT slug, updated_at FROM posts WHERE status='published' ORDER BY published_at DESC") as $p)
        echo '<url><loc>' . $base . '/blogg/' . e($p['slug']) . '</loc><lastmod>' . date('Y-m-d', strtotime($p['updated_at'])) . '</lastmod><changefreq>monthly</changefreq><priority>0.6</priority></url>' . "\n";
} catch (Throwable) {}
echo '</urlset>';
