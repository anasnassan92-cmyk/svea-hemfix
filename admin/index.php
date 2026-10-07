<?php
declare(strict_types=1);
require_once __DIR__ . '/_layout.php';
require_admin();
$db = db();
$q = fn(string $sql, array $p = []) => (function () use ($db, $sql, $p) { $s = $db->prepare($sql); $s->execute($p); return $s; })();
$new   = (int) $q("SELECT COUNT(*) FROM leads WHERE status='ny'")->fetchColumn();
$week  = (int) $q("SELECT COUNT(*) FROM leads WHERE created_at >= ?", [date('Y-m-d 00:00:00', strtotime('monday this week'))])->fetchColumn();
$month = (int) $q("SELECT COUNT(*) FROM leads WHERE created_at >= ?", [date('Y-m-01 00:00:00')])->fetchColumn();
$booked = (int) $q("SELECT COUNT(*) FROM leads WHERE status='bokad' AND created_at >= ?", [date('Y-m-01 00:00:00')])->fetchColumn();
$posts = (int) $q("SELECT COUNT(*) FROM posts WHERE status='published'")->fetchColumn();
$drafts = (int) $q("SELECT COUNT(*) FROM posts WHERE status='draft'")->fetchColumn();
$recent = $q("SELECT * FROM leads WHERE status<>'spam' ORDER BY created_at DESC LIMIT 8")->fetchAll();
$byService = $q("SELECT service, COUNT(*) c FROM leads WHERE created_at >= ? AND status<>'spam' GROUP BY service ORDER BY c DESC LIMIT 7", [date('Y-m-d', strtotime('-90 days'))])->fetchAll();
$maxS = max(1, ...array_map(fn($r) => (int) $r['c'], $byService ?: [['c' => 1]]));
$lastHook = $q("SELECT * FROM webhook_log ORDER BY id DESC LIMIT 1")->fetch();
$latestPosts = $q("SELECT id,title,slug,status,published_at,source FROM posts ORDER BY updated_at DESC LIMIT 5")->fetchAll();
admin_header('Översikt', 'dash');
if (!empty($_GET['welcome'])) echo '<div class="flash ok">Välkommen! Ditt administratörskonto är skapat. Börja med att lägga in BabyLoveGrowth-nycklarna under Inställningar.</div>';
?>
<div class="kpis">
<a class="kpi y" href="/admin/leads.php?status=ny"><?= admin_icon('inbox') ?><small>Nya förfrågningar</small><b><?= $new ?></b><span>väntar på svar</span></a>
<div class="kpi"><?= admin_icon('bolt') ?><small>Denna vecka</small><b><?= $week ?></b><span>förfrågningar sedan måndag</span></div>
<div class="kpi"><?= admin_icon('check') ?><small>Bokade i <?= ['jan','feb','mar','apr','maj','jun','jul','aug','sep','okt','nov','dec'][(int) date('n') - 1] ?></small><b><?= $booked ?></b><span>av <?= $month ?> förfrågningar</span></div>
<a class="kpi" href="/admin/posts.php"><?= admin_icon('pen') ?><small>Blogginlägg</small><b><?= $posts ?></b><span><?= $drafts ?> utkast · <?= $lastHook ? 'senaste webhook ' . sv_date($lastHook['received_at'], true) : 'ingen webhook ännu' ?></span></a>
</div>
<div class="grid2">
<div class="card">
<h2><?= admin_icon('inbox') ?>Senaste förfrågningar <a class="btn btn-o btn-s" style="margin-left:auto" href="/admin/leads.php">Visa alla</a></h2>
<?php if (!$recent): ?><div class="empty">Inga förfrågningar ännu. När någon skickar formuläret på <a href="/kontakt">/kontakt</a> hamnar den här.</div><?php else: ?>
<div class="tw"><table><thead><tr><th>Kund</th><th>Tjänst</th><th>Status</th><th>Inkom</th></tr></thead><tbody>
<?php foreach ($recent as $l): ?>
<tr><td><a class="rl" href="/admin/lead.php?id=<?= $l['id'] ?>"><?= e($l['name']) ?></a><div class="mut"><?= e($l['phone']) ?></div></td><td><?= e($l['service']) ?><?= $l['size'] ? '<div class="mut">' . e($l['size']) . ' kvm</div>' : '' ?></td><td><?= status_pill($l['status']) ?></td><td class="mut"><?= sv_date($l['created_at'], true) ?></td></tr>
<?php endforeach; ?>
</tbody></table></div><?php endif; ?>
</div>
<div>
<div class="card"><h2><?= admin_icon('bolt') ?>Populära tjänster (90 dagar)</h2>
<?php if (!$byService): ?><div class="empty">Ingen data ännu.</div><?php else: ?><div class="bars"><?php foreach ($byService as $r): ?><div class="bar"><span><?= e($r['service'] ?: 'Okänd') ?></span><i><b style="width:<?= round($r['c'] / $maxS * 100) ?>%"></b></i><em><?= $r['c'] ?></em></div><?php endforeach; ?></div><?php endif; ?>
</div>
<div class="card"><h2><?= admin_icon('pen') ?>Blogg</h2>
<?php if (!$latestPosts): ?><div class="empty">Inga inlägg ännu. Koppla BabyLoveGrowth under <a href="/admin/settings.php">Inställningar</a> eller <a href="/admin/post.php">skriv ett inlägg</a>.</div><?php else: ?>
<div class="tw"><table><tbody><?php foreach ($latestPosts as $p): ?><tr><td><a class="rl" href="/admin/post.php?id=<?= $p['id'] ?>"><?= e($p['title']) ?></a><div class="mut"><?= $p['source'] === 'blg' ? 'BabyLoveGrowth' : 'Manuellt' ?> · <?= sv_date($p['published_at'] ?: null) ?></div></td><td><span class="pill <?= $p['status'] === 'published' ? 'pub' : 'draft' ?>"><?= $p['status'] === 'published' ? 'Publicerad' : 'Utkast' ?></span></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?>
</div>
</div>
</div>
<?php admin_footer();
