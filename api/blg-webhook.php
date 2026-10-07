<?php
/**
 * BabyLoveGrowth → Svea Hemfix webhook receiver.
 * Configure in BabyLoveGrowth: Settings → Publishing → Webhook
 *   URL:    https://sveahemfix.se/api/blg-webhook.php
 *   Token:  the secret shown in /admin/settings.php
 * Must answer 2xx + JSON body.
 */
declare(strict_types=1);
require_once __DIR__ . '/../lib/app.php';
header('Content-Type: application/json; charset=utf-8');

function hook_log(bool $ok, string $msg, ?string $aid = null): void
{
    try { db()->prepare('INSERT INTO webhook_log(received_at,ok,message,article_id,ip) VALUES(?,?,?,?,?)')->execute([now(), $ok ? 1 : 0, mb_substr($msg, 0, 500), $aid, client_ip()]); } catch (Throwable) {}
    try { db()->exec('DELETE FROM webhook_log WHERE id NOT IN (SELECT id FROM webhook_log ORDER BY id DESC LIMIT 200)'); } catch (Throwable) {}
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') { echo json_encode(['success' => true, 'service' => 'sveahemfix-blog-webhook', 'method' => 'POST required']); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success' => false, 'error' => 'method']); exit; }

$secret = (string) setting('blg_webhook_secret', '');
$auth = (string) ($_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');
$token = preg_match('/^Bearer\s+(.+)$/i', $auth, $m) ? trim($m[1]) : (string) ($_SERVER['HTTP_X_API_KEY'] ?? '');
if ($secret === '' || $token === '' || !hash_equals($secret, $token)) {
    hook_log(false, 'Unauthorized (fel token)');
    http_response_code(401); echo json_encode(['success' => false, 'error' => 'unauthorized']); exit;
}

$raw = file_get_contents('php://input') ?: '';
$data = json_decode($raw, true);
if (!is_array($data)) { hook_log(false, 'Ogiltig JSON'); http_response_code(400); echo json_encode(['success' => false, 'error' => 'invalid_json']); exit; }
$article = $data['article'] ?? $data['data'] ?? $data;
if (!isset($article['id']) && isset($data['id'])) $article = $data;

// connection test from the BLG wizard (no article yet)
if (empty($article['title']) && empty($article['content_html'])) {
    hook_log(true, 'Test-anrop OK');
    echo json_encode(['success' => true, 'message' => 'Webhook connected']); exit;
}

try {
    $r = post_upsert_blg($article);
    $link = SVEA_SITE . '/blogg/' . $r['slug'];
    hook_log(true, ($r['created'] ? 'Ny artikel: ' : 'Uppdaterad: ') . ($article['title'] ?? ''), (string) $article['id']);
    echo json_encode(['success' => true, 'link' => $link, 'id' => $r['id'], 'created' => $r['created']], JSON_UNESCAPED_SLASHES);
} catch (InvalidArgumentException $ex) {
    hook_log(false, $ex->getMessage(), (string) ($article['id'] ?? ''));
    http_response_code(400); echo json_encode(['success' => false, 'error' => $ex->getMessage()]);
} catch (Throwable $ex) {
    hook_log(false, 'Serverfel: ' . $ex->getMessage(), (string) ($article['id'] ?? ''));
    http_response_code(500); echo json_encode(['success' => false, 'error' => 'server_error']);
}
