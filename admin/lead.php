<?php
declare(strict_types=1);
require_once __DIR__ . '/_layout.php';
require_admin();
$db = db();
$id = (int) ($_GET['id'] ?? 0);
$get = function () use ($db, $id) { $s = $db->prepare('SELECT * FROM leads WHERE id=?'); $s->execute([$id]); return $s->fetch(); };
$l = $get();
if (!$l) { flash('err', 'Förfrågan hittades inte.'); header('Location: /admin/leads.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $act = (string) ($_POST['act'] ?? '');
    if ($act === 'status' && isset(LEAD_STATUS[$_POST['status'] ?? ''])) {
        $db->prepare('UPDATE leads SET status=?, updated_at=? WHERE id=?')->execute([$_POST['status'], now(), $id]); flash('ok', 'Status uppdaterad.');
    } elseif ($act === 'note' && trim((string) ($_POST['note'] ?? '')) !== '') {
        $db->prepare('INSERT INTO lead_notes(lead_id,note,created_at) VALUES(?,?,?)')->execute([$id, mb_substr(trim((string) $_POST['note']), 0, 3000), now()]);
        $db->prepare('UPDATE leads SET updated_at=? WHERE id=?')->execute([now(), $id]); flash('ok', 'Anteckning sparad.');
    } elseif ($act === 'delnote') {
        $db->prepare('DELETE FROM lead_notes WHERE id=? AND lead_id=?')->execute([(int) $_POST['nid'], $id]);
    } elseif ($act === 'delete') {
        $db->prepare('DELETE FROM leads WHERE id=?')->execute([$id]); flash('ok', 'Förfrågan raderad.'); header('Location: /admin/leads.php'); exit;
    }
    header('Location: /admin/lead.php?id=' . $id); exit;
}
$notes = (function () use ($db, $id) { $s = $db->prepare('SELECT * FROM lead_notes WHERE lead_id=? ORDER BY id DESC'); $s->execute([$id]); return $s->fetchAll(); })();
$tel = preg_replace('/\s+/', '', (string) $l['phone']);
$wa = preg_replace('/\D/', '', (string) $l['phone']); if (str_starts_with($wa, '0')) $wa = '46' . substr($wa, 1);
admin_header('Förfrågan ' . $l['ref'], 'leads');
?>
<div class="toolbar"><a class="btn btn-o btn-s" href="/admin/leads.php">← Alla förfrågningar</a><span class="sp"></span>
<a class="btn btn-p btn-s" href="tel:<?= e($tel) ?>"><?= admin_icon('phone') ?>Ring</a>
<a class="btn btn-o btn-s" href="https://wa.me/<?= e($wa) ?>?text=<?= rawurlencode('Hej ' . $l['name'] . '! Tack för din förfrågan om ' . mb_strtolower((string) $l['service']) . ' (ref ' . $l['ref'] . '). ') ?>" target="_blank" rel="noopener">WhatsApp</a>
<a class="btn btn-o btn-s" href="mailto:<?= e($l['email']) ?>?subject=<?= rawurlencode('Offert – ' . $l['service'] . ' (ref ' . $l['ref'] . ')') ?>&body=<?= rawurlencode("Hej " . $l['name'] . ",\n\nTack för din förfrågan! Här kommer vår offert:\n\n") ?>"><?= admin_icon('mail') ?>Skicka offert</a>
</div>
<div class="grid2">
<div>
<div class="card"><h2><?= e($l['name']) ?> <?= status_pill($l['status']) ?></h2>
<dl class="dl">
<dt>Telefon</dt><dd><a href="tel:<?= e($tel) ?>"><?= e($l['phone']) ?></a></dd>
<dt>E-post</dt><dd><a href="mailto:<?= e($l['email']) ?>"><?= e($l['email']) ?></a></dd>
<dt>Adress</dt><dd><?= e(trim($l['address'] . ' ' . $l['postal'])) ?: '–' ?></dd>
<dt>Tjänst</dt><dd><b><?= e($l['service']) ?></b></dd>
<dt>Storlek</dt><dd><?= $l['size'] ? e($l['size']) . ' kvm' : '–' ?></dd>
<dt>Frekvens</dt><dd><?= e($l['frequency']) ?: '–' ?></dd>
<dt>Önskat datum</dt><dd><?= e($l['wish_date']) ?: '–' ?></dd>
<dt>Meddelande</dt><dd><?= $l['message'] ? nl2br(e($l['message'])) : '–' ?></dd>
<dt>Inkom</dt><dd><?= sv_date($l['created_at'], true) ?> · ref <span class="code"><?= e($l['ref']) ?></span> · <?= $l['mail_sent'] ? 'e-post skickad' : '<span style="color:#B91C1C">e-post misslyckades</span>' ?></dd>
<dt>Källa</dt><dd class="mut"><?= e($l['source']) ?> · IP <?= e($l['ip']) ?></dd>
</dl>
</div>
<div class="card"><h2>Anteckningar</h2>
<form method="post" style="margin-bottom:1rem"><?= csrf_field() ?><input type="hidden" name="act" value="note"><textarea class="inp" name="note" placeholder="T.ex. Ringde 14:30 – vill ha offert på varannan vecka…" required></textarea><div style="margin-top:.6rem"><button class="btn btn-p btn-s" type="submit">Spara anteckning</button></div></form>
<?php if (!$notes): ?><div class="mut">Inga anteckningar ännu.</div><?php endif; ?>
<?php foreach ($notes as $n): ?><div class="note"><small><?= sv_date($n['created_at'], true) ?> <form method="post" style="display:inline;float:right"><?= csrf_field() ?><input type="hidden" name="act" value="delnote"><input type="hidden" name="nid" value="<?= $n['id'] ?>"><button class="btn btn-s" style="padding:0 .4rem;border:0;color:#B91C1C" data-confirm="Radera anteckningen?">ta bort</button></form></small><?= nl2br(e($n['note'])) ?></div><?php endforeach; ?>
</div>
</div>
<div>
<div class="card"><h2>Status</h2>
<form method="post" class="status-form"><?= csrf_field() ?><input type="hidden" name="act" value="status"><select class="inp" name="status"><?php foreach (LEAD_STATUS as $k => $lab): ?><option value="<?= $k ?>" <?= $k === $l['status'] ? 'selected' : '' ?>><?= $lab ?></option><?php endforeach; ?></select><button class="btn btn-p btn-s" type="submit">Spara</button></form>
<p class="mut" style="margin-top:.75rem">Ny → Kontaktad → Offert skickad → Bokad. Markera som Förlorad om kunden tackar nej.</p>
</div>
<div class="card"><h2>Snabbsvar</h2>
<p class="mut" style="margin-bottom:.6rem">Kopiera och skicka via SMS/WhatsApp:</p>
<div class="note copy" data-copy="Hej <?= e($l['name']) ?>! Tack för din förfrågan om <?= e(mb_strtolower((string) $l['service'])) ?> till Svea Hemfix. Vi återkommer med ett fast pris inom 24 timmar. Har du frågor? Ring 010 300 10 32. /Svea Hemfix">Hej <?= e($l['name']) ?>! Tack för din förfrågan om <?= e(mb_strtolower((string) $l['service'])) ?> till Svea Hemfix. Vi återkommer med ett fast pris inom 24 timmar. Har du frågor? Ring 010 300 10 32. /Svea Hemfix</div>
<small class="mut">Klicka på texten för att kopiera.</small>
</div>
<div class="card"><h2>Radera</h2><form method="post"><?= csrf_field() ?><input type="hidden" name="act" value="delete"><button class="btn btn-d btn-s" data-confirm="Radera förfrågan permanent? (GDPR – kan inte ångras)">Radera förfrågan</button></form></div>
</div>
</div>
<?php admin_footer();
