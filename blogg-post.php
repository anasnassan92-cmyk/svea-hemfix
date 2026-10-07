<?php
require __DIR__ . '/data/site.php';
require_once __DIR__ . '/lib/app.php';
$slug = preg_replace('/[^a-z0-9-]/', '', $_GET['s'] ?? '');
$st = db()->prepare("SELECT * FROM posts WHERE slug=? AND status='published'"); $st->execute([$slug]); $p = $st->fetch();
if (!$p) { http_response_code(404); require __DIR__ . '/404.php'; exit; }
try { db()->prepare('UPDATE posts SET views=views+1 WHERE id=?')->execute([$p['id']]); } catch (Throwable) {}
$img = $p['hero_image_url'] ?: '';
$url = 'https://sveahemfix.se/blogg/' . $p['slug'];
$ld = [];
$art = ['@context' => 'https://schema.org', '@type' => 'BlogPosting', 'headline' => $p['title'], 'description' => $p['meta_description'] ?: $p['excerpt'], 'datePublished' => date('c', strtotime($p['published_at'])), 'dateModified' => date('c', strtotime($p['updated_at'])), 'mainEntityOfPage' => $url, 'author' => ['@type' => 'Organization', 'name' => 'Svea Hemfix', 'url' => 'https://sveahemfix.se'], 'publisher' => ['@type' => 'Organization', 'name' => 'Svea Hemfix', 'logo' => ['@type' => 'ImageObject', 'url' => 'https://sveahemfix.se/images/logo.png']], 'inLanguage' => $p['lang'] ?: 'sv'];
if ($img) $art['image'] = str_starts_with($img, 'http') ? $img : 'https://sveahemfix.se' . $img;
$kw = json_decode((string) $p['keywords'], true) ?: [];
if ($kw) $art['keywords'] = implode(', ', $kw);
$ld[] = json_encode($art, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
if ($p['faq_json_ld'] && $p['faq_json_ld'] !== 'null') $ld[] = $p['faq_json_ld'];
$page = ['title' => $p['title'] . ' | Svea Hemfix', 'desc' => mb_substr($p['meta_description'] ?: $p['excerpt'] ?: excerpt_from_html((string) $p['content_html']), 0, 160), 'canonical' => $url, 'nav' => 'blogg', 'ld' => $ld];
if ($img) $page['og_image'] = $art['image'];
require __DIR__ . '/partials/header.php';
$rel = db()->prepare("SELECT title,slug,hero_image_url,published_at FROM posts WHERE status='published' AND id<>? ORDER BY published_at DESC LIMIT 3"); $rel->execute([$p['id']]); $related = $rel->fetchAll();
?>
<article class="post-page">
<header class="post-hero">
<div class="wrap">
<nav class="crumbs" aria-label="Brödsmulor"><a href="/">Hem</a> › <a href="/blogg">Blogg</a></nav>
<h1><?= e($p['title']) ?></h1>
<p class="pmeta"><?= sv_date($p['published_at']) ?> · <?= reading_time((string) $p['content_html']) ?> min läsning · Svea Hemfix</p>
<?php if ($img): ?><img class="pimg" src="<?= e($img) ?>" alt="<?= e($p['hero_image_alt'] ?: $p['title']) ?>" fetchpriority="high"><?php endif; ?>
</div>
</header>
<div class="wrap post-wrap">
<div class="post"><?= $p['content_html'] ?></div>
<aside class="post-side">
<div class="pbox"><span class="tag">Offert</span><h3>Vill du slippa städa?</h3><p>Hemstädning från <?= SVEA_HOURLY_RUT ?> kr/tim efter RUT. Fast pris inom 24 timmar.</p><a class="btn btn-y" href="/kontakt">Begär offert</a><a class="btn btn-o" href="tel:0103001032" style="margin-top:.5rem"><?= ico('phone') ?>010 300 10 32</a></div>
<?php if ($kw): ?><div class="ptags"><?php foreach (array_slice($kw, 0, 8) as $k) echo '<span>' . e((string) $k) . '</span>'; ?></div><?php endif; ?>
<div class="pshare"><b>Dela</b><a href="https://www.facebook.com/sharer/sharer.php?u=<?= rawurlencode($url) ?>" target="_blank" rel="noopener">Facebook</a><a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= rawurlencode($url) ?>" target="_blank" rel="noopener">LinkedIn</a><a href="mailto:?subject=<?= rawurlencode($p['title']) ?>&body=<?= rawurlencode($url) ?>">E-post</a></div>
</aside>
</div>
</article>
<?php if ($related): ?>
<section class="sec sec-alt">
<div class="wrap"><div class="center"><span class="kicker">Läs mer</span><h2>Fler artiklar</h2></div>
<div class="blog-grid small">
<?php foreach ($related as $r): ?><a class="bcard" href="/blogg/<?= e($r['slug']) ?>"><div class="bimg"><img src="<?= e($r['hero_image_url'] ?: '/media/p/p12.webp') ?>" alt="" loading="lazy"></div><div class="bbody"><small><?= sv_date($r['published_at']) ?></small><h2><?= e($r['title']) ?></h2><span class="more">Läs artikeln →</span></div></a><?php endforeach; ?>
</div></div>
</section>
<?php endif; ?>
<section class="sec">
<div class="wrap"><div class="cta"><div class="cta-c"><h2>Redo för ett renare hem?</h2><p>Försäkrat, miljövänligt och med nöjdhetsgaranti – upp till 50 % RUT-avdrag.</p><div class="acts"><a class="btn btn-y" href="/kontakt">Begär offert</a><a class="btn btn-g" href="/tjanster">Våra tjänster</a></div></div><video class="cta-v" autoplay muted loop playsinline preload="metadata" poster="/media/v/v04.webp" aria-hidden="true"><source src="/media/v/v04.mp4" type="video/mp4"></video></div></div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
