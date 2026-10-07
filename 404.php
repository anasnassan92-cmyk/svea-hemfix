<?php
http_response_code(404);
require __DIR__ . '/data/site.php';
$page = ['title' => 'Sidan hittades inte | Svea Hemfix', 'desc' => 'Sidan du letar efter finns inte.', 'canonical' => 'https://sveahemfix.se/'];
require __DIR__ . '/partials/header.php';
?>
<section class="sec center" style="min-height:60vh"><div class="wrap"><span class="kicker">404</span><h1>Sidan hittades inte</h1><p class="lead" style="margin:1rem auto 1.5rem">Sidan kan ha flyttats. Här är några genvägar:</p><div class="acts" style="display:flex;gap:.75rem;justify-content:center;flex-wrap:wrap"><a class="btn btn-p" href="/">Till startsidan</a><a class="btn btn-o" href="/tjanster">Våra tjänster</a><a class="btn btn-o" href="/priser">Priser</a></div></div></section>
<?php require __DIR__ . '/partials/footer.php'; ?>
