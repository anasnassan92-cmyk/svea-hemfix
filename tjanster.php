<?php
require __DIR__ . '/data/site.php';
$page = ['title' => 'Våra tjänster | Städning i Stockholm | Svea Hemfix', 'desc' => 'Hemstädning, storstädning, flyttstädning, fönsterputs, byggstädning, kontorsstädning och trapphusstädning i Stockholm med omnejd. Miljövänligt, försäkrat och med upp till 50% RUT-avdrag.', 'canonical' => 'https://sveahemfix.se/tjanster', 'nav' => 'tjanster'];
require __DIR__ . '/partials/header.php';
?>
<section class="hero hero-sub">
<div class="wrap">
<div><span class="kicker">Våra tjänster</span><h1>Professionell städning för varje behov</h1>
<p class="lead">Hem, kontor och fastigheter i Stockholm. Alla tjänster utförs med miljövänliga produkter, försäkrad personal och – för privatpersoner – <strong>50&nbsp;% RUT-avdrag</strong>. Timpris <?= SVEA_HOURLY ?> kr (<?= SVEA_HOURLY_RUT ?> kr efter RUT).</p>
<div class="acts"><a class="btn btn-p" href="#lista">Se tjänsterna</a><a class="btn btn-o" href="/kontakt">Begär offert</a></div></div>
<div class="hero-img hero-vid"><video autoplay muted loop playsinline poster="/media/v/v11.webp" aria-label="Flyttstädning i Stockholm"><source src="/media/v/v11.mp4" type="video/mp4"></video><div class="badge"><b>50%</b><span>RUT-<br>avdrag</span></div></div>
</div>
</section>
<section class="sec" id="lista">
<div class="wrap">
<?php foreach (SVEA_GROUPS as $gk => $gl): ?>
<div class="center" style="<?= $gk === 'privat' ? '' : 'margin-top:3.5rem' ?>"><span class="kicker"><?= $gk === 'privat' ? 'För hemmet' : 'För verksamheten' ?></span><h2><?= $gl ?></h2><p class="lead"><?= $gk === 'privat' ? 'Alla hushållsnära tjänster: ' . SVEA_HOURLY . ' kr/tim – du betalar ' . SVEA_HOURLY_RUT . ' kr/tim efter RUT. Städmaterial ingår.' : 'Fast pris exklusive moms efter besiktning. Löpande avtal eller enstaka uppdrag.' ?></p></div>
<div class="rel" style="margin-top:2rem">
<?php foreach (SVEA_SERVICES as $s): if (($s['group'] ?? 'privat') !== $gk) continue; ?>
<a href="/tjanst/<?= $s['slug'] ?>"><img src="<?= $s['img'] ?>" alt="<?= $s['name'] ?>" loading="lazy"><div><h3><?= $s['name'] ?><?= $s['popular'] ? ' <span class="kicker" style="margin:0 0 0 .4rem;padding:.15rem .5rem;font-size:.6rem">Populär</span>' : '' ?></h3><p><?= $s['short'] ?></p><?php if (!empty($s['parts'])): ?><p class="mut-sm">Ingår: <?= implode(' · ', array_map(fn($p) => $p[0], $s['parts'])) ?></p><?php endif; ?><p style="margin-top:.5rem;color:var(--p);font-weight:700"><?= $s['rut'] ? SVEA_HOURLY_RUT . ' kr/tim efter RUT' : 'Fast pris på offert' ?> · <?= $s['dur'] ?></p></div></a>
<?php endforeach; ?>
<?php if ($gk === 'foretag'): ?><a href="/trapphusstadning"><img src="/media/p/p06.webp" alt="Trapphusstädning" loading="lazy"><div><h3>Trapphusstädning</h3><p>Regelbunden städning av trapphus, entré och hiss för BRF och fastighetsägare.</p><p style="margin-top:.5rem;color:var(--p);font-weight:700">Från 2 000 kr/mån · avtal</p></div></a><?php endif; ?>
</div>
<?php endforeach; ?>
</div>
</section>
<?php $ms = ['cats' => [], 'limit' => 8, 'title' => 'Så här ser våra uppdrag ut', 'kicker' => 'Vårt arbete']; require __DIR__ . '/partials/media-strip.php'; ?>
<section class="sec sec-alt">
<div class="wrap"><div class="cta"><div class="cta-c"><h2>Osäker på vilken tjänst du behöver?</h2><p>Ring oss eller skicka en förfrågan så hjälper vi dig att välja rätt – kostnadsfritt.</p><div class="acts"><a class="btn btn-y" href="/kontakt">Begär offert</a><a class="btn btn-g" href="tel:0103001032"><?= ico('phone') ?>Ring 010 300 10 32</a></div></div><video class="cta-v" autoplay muted loop playsinline preload="metadata" poster="/media/v/v18.webp" aria-hidden="true"><source src="/media/v/v18.mp4" type="video/mp4"></video></div></div>
</section>
<?php require __DIR__ . '/partials/footer.php'; ?>
