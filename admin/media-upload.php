<?php
/**
 * Admin-only JSON endpoint for gallery uploads / replace / delete (called by admin.js).
 */
declare(strict_types=1);
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/media.php';
header('Content-Type: application/json; charset=utf-8');
if (!is_admin()) { http_response_code(401); echo json_encode(['ok' => false, 'error' => 'Inte inloggad – ladda om sidan.']); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['ok' => false, 'error' => 'POST krävs']); exit; }
csrf_check();
$act = (string) ($_POST['act'] ?? 'add');
$meta = ['w' => $_POST['w'] ?? 0, 'h' => $_POST['h'] ?? 0, 'dur' => $_POST['dur'] ?? 0];
try {
    if ($act === 'add') {
        $entry = media_add($_FILES['file'] ?? [], (string) ($_POST['cap'] ?? ''), (string) ($_POST['cat'] ?? 'hem'), $_FILES['poster'] ?? null, $meta);
        echo json_encode(['ok' => true, 'item' => $entry], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } elseif ($act === 'replace') {
        $entry = media_replace((string) ($_POST['id'] ?? ''), $_FILES['file'] ?? [], $_FILES['poster'] ?? null, $meta);
        echo json_encode(['ok' => true, 'item' => $entry], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } elseif ($act === 'delete') {
        media_delete((string) ($_POST['id'] ?? ''));
        echo json_encode(['ok' => true]);
    } else { http_response_code(400); echo json_encode(['ok' => false, 'error' => 'Okänd åtgärd']); }
} catch (InvalidArgumentException $e) {
    http_response_code(422); echo json_encode(['ok' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500); echo json_encode(['ok' => false, 'error' => 'Serverfel: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
