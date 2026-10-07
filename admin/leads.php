<?php
declare(strict_types=1);
require_once __DIR__ . '/_layout.php';
require_admin();
$db = db();

/* ajax status update */
if (($_GET['ajax'] ?? '') === 'status' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    header('Content-Type: application/json');
    $id = (int) ($_POST['id'] ?? 0); $st = (string) ($_POST['status'] ?? '');
    if (!$id || !isset(LEAD_STATUS[$st])) { echo json_encode(['ok' => false]); exit; }
    $db->prepare('UPDATE leads SET status=?, updated_at=? WHERE id=?')->execute([$st, now(), $id]);
    $cls = ['ny' => 'new', 'kontaktad' => 'info', 'offert' => 'warn', 'bokad' => 'ok', 'forlorad' => 'mut', 'spam' => 'mut'][$st];
    echo json_encode(['ok' => true, 'cls' => $cls, 'label' => LEAD_STATUS[$st]]); exit;
}

/* CSV export */
if (($_GET['export'] ?? '') === 'csv') {
    header('Content-Type: text/csv; charset=utf-8'); header('Content-Disposition: attachment; filename="forfragningar-' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w'); fwrite($out, "\xEF\xBB\xBF");
    fputcsv($out, ['ID', 'Ref', 'Inkom', 'Status', 'Namn', 'Telefon', 'E-post', 'Adress', 'Postnr', 'Tjänst', 'Storlek', 'Frekvens', 'Önskat datum', 'Meddelande'], ';');
    foreach ($db->query('SELECT * FROM leads ORDER BY created_at DESC') as $l) fputcsv($out, [$l['id'], $l['ref'], $l['created_at'], LEAD_STATUS[$l['status']] ?? $l['status'], $l['name'], $l['phone'], $l['email'], $l['address'], $l['postal'], $l['service'], $l['size'], $l['frequency'], $l['wish_date'], $l['message']], ';');
    exit;
}

$status = (string) ($_GET['status'] ?? ''); $qs = trim((string) ($_GET['q'] ?? '')); $page = max(1, (int) ($_GET['p'] ?? 1)); $per = 25;
$w = []; $p = [];
if ($status !== '' && isset(LEAD_STATUS[$status])) { $w[] = 'status=?'; $p[] = $status; } elseif ($status === '') { $w[] = "status<>'spam'"; }
if ($qs !== '') { $w[] = '(name LIKE ? OR phone LIKE ? OR email LIKE ? OR service LIKE ? OR address LIKE ? OR ref LIKE ?)'; array_push($p, "%$qs%", "%$qs%", "%$qs%", "%$qs%", "%$qs%", "%$qs%"); }
$where = $w ? 'WHERE ' . implode(' AND ', $w) : '';
$total = (int) (function () use ($db, $where, $p) { $s = $db->prepare("SELECT COUNT(*) FROM leads $where"); $s->execute($p); return $s->fetchColumn(); })();
$s = $db->prepare("SELECT * FROM leads $where ORDER BY created_at DESC LIMIT $per OFFSET " . ($page - 1) * $per); $s->execute($p); $rows = $s->fetchAll();
$counts = []; foreach ($db->query('SELECT status, COUNT(*) c FROM leads GROUP BY status') as $r) $counts[$r['status']] = (int) $r['c'];
$pages = (int) ceil($total / $per);
admin_header('Förfrågningar', 'leads');
?>
<div class="tabs">
<a href="/admin/leads.php" class="<?= $status === '' ? 'on' : '' ?>">Alla (<?= array_sum($counts) - ($counts['spam'] ?? 0) ?>)</a>
<?php foreach (LEAD_STATUS as $k => $l): ?><a href="/admin/leads.php?status=<?= $k ?>" class="<?= $status === $k ? 'on' : '' ?>"><?= $l ?> (<?= $counts[$k] ?? 0 ?>)</a><?php endforeach; ?>
</div>
<form class="toolbar" method="get">
<?php if ($status): ?><input type="hidden" name="status" value="<?= e($status) ?>"><?php endif; ?>
<div class="srch"><?= admin_icon('search') ?><input class="inp" type="search" name="q" value="<?= e($qs) ?>" placeholder="Sök namn, telefon, e-post, tjänst…"></div>
<button class="btn btn-o" type="submit">Sök</button>
<span class="sp"></span>
<a class="btn btn-o" href="/admin/leads.php?export=csv"><?= admin_icon('dl') ?>Exportera CSV</a>
</form>
<?php if (!$rows): ?><div class="card"><div class="empty">Inga förfrågningar matchar.</div></div><?php else: ?>
<div class="tw"><table><thead><tr><th>Inkom</th><th>Kund</th><th>Tjänst</th><th>Bostad</th><th>Status</th><th></th></tr></thead><tbody>
<?php foreach ($rows as $l): ?>
<tr>
<td class="mut" style="white-space:nowrap"><?= sv_date($l['created_at'], true) ?><br><span class="code"><?= e($l['ref']) ?></span></td>
<td><a class="rl" href="/admin/lead.php?id=<?= $l['id'] ?>"><?= e($l['name']) ?></a><div class="mut"><a href="tel:<?= e(preg_replace('/\s+/', '', $l['phone'])) ?>"><?= e($l['phone']) ?></a> · <a href="mailto:<?= e($l['email']) ?>"><?= e($l['email']) ?></a></div></td>
<td><?= e($l['service']) ?><?= $l['frequency'] ? '<div class="mut">' . e($l['frequency']) . '</div>' : '' ?></td>
<td class="mut"><?= $l['size'] ? e($l['size']) . ' kvm' : '' ?><?= $l['postal'] ? '<br>' . e($l['postal']) : '' ?><?= $l['wish_date'] ? '<br>Önskat: ' . e($l['wish_date']) : '' ?></td>
<td><?= status_pill($l['status']) ?><br><select class="inp w-auto" style="margin-top:.35rem;padding:.3rem .5rem;font-size:.8rem" data-status="<?= $l['id'] ?>"><?php foreach (LEAD_STATUS as $k => $lab): ?><option value="<?= $k ?>" <?= $k === $l['status'] ? 'selected' : '' ?>><?= $lab ?></option><?php endforeach; ?></select></td>
<td><a class="btn btn-o btn-s" href="/admin/lead.php?id=<?= $l['id'] ?>">Öppna</a></td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<?php if ($pages > 1): ?><div class="pag"><?php for ($i = 1; $i <= $pages; $i++): $u = '/admin/leads.php?' . http_build_query(['status' => $status ?: null, 'q' => $qs ?: null, 'p' => $i]); ?><?= $i === $page ? "<span class=\"on\">$i</span>" : "<a href=\"" . e($u) . "\">$i</a>" ?><?php endfor; ?></div><?php endif; ?>
<?php endif; ?>
<script>document.body.setAttribute('data-csrf','<?= e(csrf_token()) ?>');</script>
<?php admin_footer();
