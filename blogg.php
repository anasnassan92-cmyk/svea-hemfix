<?php
require __DIR__ . '/data/site.php';
require_once __DIR__ . '/lib/app.php';
$per = 9; $pg = max(1, (int) ($_GET['sida'] ?? 1));
$total = (int) db()->query("SELECT COUNT(*) FROM posts WHERE status='published'")->fetchColumn();
$pages = max(1, (int) ceil($total / $per));
$st = db()->prepare("SELECT id,title,slug,excerpt,hero_image_url,hero_image_alt,published_at,keywords,content_html FROM posts WHERE status='published' ORDER BY published_at DESC LIMIT $per OFFSET " . ($pg - 1) * $per);
$st->execute(); $posts = $st->fetchAll();
$page = ['title' => 'Blogg – Tips om städning, RUT-avdrag & ett renare hem | Svea Hemfix', 'desc' => 'Guider och tips från Svea Hemfix: hemstädning, flyttstädning, fönsterputs, RUT-avdrag och hur du håller hemmet rent i Stockholm.', 'canonical' => 'https://sveahemfix.se/blogg' . ($pg > 1 ? '?sida=' . $pg : ''), 'nav' => 'blogg', 'ld' => [json_encode(['@context' => 'https://schema.org', '@type' => 'Blog', 'name' => 'Svea Hemfix blogg', 'url' => 'https://sveahemfix.se/blogg', 'publisher' => ['@type' => 'Organization', 'name' => 'Svea Hemfix', 'logo' => 'https://sveahemfix.se/images/logo.png']], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)]];
require __DIR__ . '/partials/header.php';
?>
<section class="hero hero-sub">
<div class="wrap">
<div><span class="kicker">Blogg</span><h1>Tips, guider och nyheter om städning</h1>
<p class="lead">Praktiska råd från våra städare – hur du håller hemmet rent, vad RUT-avdraget innebär och hur du får ut mest av en professionell städning.</p>
<div class="acts"><a class="btn btn-p" href="/kontakt">Begär offert</a><a class="btn btn-o" href="/tjanster">Våra tjänster</a></div></div>
<div class="hero-img hero-vid"><video autoplay muted loop playsinline poster="/media/v/v06.webp" aria-label="Svea Hemfix"><source src="/media/v/v06.mp4" type="video/mp4"></video></div>
</div>
</section>
<section class="sec">
<div class="wrap">
<?php if (!$posts): ?>
<div class="center" style="padding:2rem 0"><h2>Snart fyller vi på här</h2><p class="lead">Vi publicerar guider om städning, RUT-avdrag och hemtrivsel inom kort. Under tiden – <a href="/tjanster" style="color:var(--p);font-weight:700">se våra tjänster</a>.</p></div>
<?php else: ?>
<div class="blog-grid">
<?php foreach ($posts as $i => $p): $img = $p['hero_image_url'] ?: '/media/p/p12.webp'; ?>
<a class="bcard<?= $i === 0 && $pg === 1 ? ' feat' : '' ?>" href="/blogg/<?= e($p['slug']) ?>">
<div class="bimg"><img src="<?= e($img) ?>" alt="<?= e($p['hero_image_alt'] ?: $p['title']) ?>" loading="<?= $i < 2 ? 'eager' : 'lazy' ?>"></div>
<div class="bbody"><small><?= sv_date($p['published_at']) ?> · <?= reading_time((string) $p['content_html']) ?> min läsning</small><h2><?= e($p['title']) ?></h2><p><?= e($p['excerpt'] ?: excerpt_from_html((string) $p['content_html'], 140)) ?></p><span class="more">Läs artikeln →</span></div>
</a>
<?php endforeach; ?>
</div>
<?php if ($pages > 1): ?><nav class="bpag" aria-label="Sidor"><?php for ($i = 1; $i <= $pages; $i++): ?><?= $i === $pg ? "<span class=\"on\">$i</span>" : '<a href="/blogg' . ($i > 1 ? "?sida=$i" : '') . "\">$i</a>" ?><?php endfor; ?></nav><?php endif; ?>
<?php endif; ?>
</div>
</section>
<section class="sec sec-alt">
<div class="wrap"><div class="cta"><div class="cta-c"><h2>Hellre låta oss städa?</h2><p>Kostnadsfri offert inom 24 timmar – upp till 50 % RUT-avdrag för privatpersoner.</p><div class="acts"><a class="btn btn-y" href="/kontakt">Begär offert</a><a class="btn btn-g" href="tel:0103001032"><?= ico('phone') ?>Ring 010 300 10 32</a></div></div><video class="cta-v" autoplay muted loop playsinline preload="metadata" poster="/media/v/v11.webp" aria-hidden="true"><source src="/media/v/v11.mp4" type="video/mp4"></video></div></div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
