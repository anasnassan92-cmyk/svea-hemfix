<?php
/**
 * Svea Hemfix – application bootstrap (DB, settings, helpers).
 * Storage: SQLite in data/app.sqlite (data/ is blocked from the web by .htaccess).
 */
declare(strict_types=1);

const SVEA_ROOT = __DIR__ . '/..';
const SVEA_DB   = SVEA_ROOT . '/data/app.sqlite';
const SVEA_SITE = 'https://sveahemfix.se';

date_default_timezone_set('Europe/Stockholm');

function db(): PDO
{
    static $pdo = null;
    if ($pdo) return $pdo;
    $new = !file_exists(SVEA_DB);
    $pdo = new PDO('sqlite:' . SVEA_DB, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $pdo->exec('PRAGMA journal_mode=WAL; PRAGMA foreign_keys=ON; PRAGMA busy_timeout=5000;');
    if ($new) @chmod(SVEA_DB, 0600);
    migrate($pdo);
    return $pdo;
}

function migrate(PDO $pdo): void
{
    $pdo->exec('CREATE TABLE IF NOT EXISTS settings (key TEXT PRIMARY KEY, value TEXT)');
    $v = (int) ($pdo->query("SELECT value FROM settings WHERE key='schema'")->fetchColumn() ?: 0);
    if ($v < 1) {
        $pdo->exec('CREATE TABLE IF NOT EXISTS leads (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ref TEXT, name TEXT NOT NULL, phone TEXT, email TEXT, address TEXT, postal TEXT,
            service TEXT, size TEXT, frequency TEXT, wish_date TEXT, message TEXT,
            status TEXT NOT NULL DEFAULT "ny", source TEXT DEFAULT "webb", ip TEXT, ua TEXT,
            mail_sent INTEGER DEFAULT 0,
            created_at TEXT NOT NULL, updated_at TEXT NOT NULL)');
        $pdo->exec('CREATE INDEX IF NOT EXISTS leads_status ON leads(status, created_at)');
        $pdo->exec('CREATE TABLE IF NOT EXISTS lead_notes (
            id INTEGER PRIMARY KEY AUTOINCREMENT, lead_id INTEGER NOT NULL REFERENCES leads(id) ON DELETE CASCADE,
            note TEXT NOT NULL, created_at TEXT NOT NULL)');
        $pdo->exec('CREATE TABLE IF NOT EXISTS posts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            blg_id TEXT UNIQUE, title TEXT NOT NULL, slug TEXT NOT NULL UNIQUE,
            excerpt TEXT, meta_description TEXT, content_html TEXT, content_md TEXT,
            hero_image_url TEXT, hero_image_alt TEXT, keywords TEXT, lang TEXT DEFAULT "sv",
            json_ld TEXT, faq_json_ld TEXT,
            status TEXT NOT NULL DEFAULT "published", source TEXT NOT NULL DEFAULT "manual",
            views INTEGER DEFAULT 0,
            published_at TEXT, created_at TEXT NOT NULL, updated_at TEXT NOT NULL)');
        $pdo->exec('CREATE INDEX IF NOT EXISTS posts_pub ON posts(status, published_at)');
        $pdo->exec('CREATE TABLE IF NOT EXISTS webhook_log (
            id INTEGER PRIMARY KEY AUTOINCREMENT, received_at TEXT NOT NULL, ok INTEGER NOT NULL,
            message TEXT, article_id TEXT, ip TEXT)');
        $pdo->exec('CREATE TABLE IF NOT EXISTS login_attempts (
            ip TEXT PRIMARY KEY, attempts INTEGER NOT NULL DEFAULT 0, last_at INTEGER NOT NULL)');
        $pdo->exec("INSERT OR REPLACE INTO settings(key,value) VALUES('schema','1')");
    }
}

/* ---------- settings ---------- */
function setting(string $key, ?string $default = null): ?string
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        foreach (db()->query('SELECT key, value FROM settings') as $r) $cache[$r['key']] = $r['value'];
    }
    return array_key_exists($key, $cache) ? $cache[$key] : $default;
}
function setting_set(string $key, ?string $value): void
{
    $st = db()->prepare('INSERT INTO settings(key,value) VALUES(?,?) ON CONFLICT(key) DO UPDATE SET value=excluded.value');
    $st->execute([$key, $value]);
}

/* ---------- helpers ---------- */
function e(?string $s): string { return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function now(): string { return date('Y-m-d H:i:s'); }
function client_ip(): string { return (string) ($_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? ''); }

function slugify(string $s): string
{
    $s = mb_strtolower(trim($s));
    $s = strtr($s, ['å' => 'a', 'ä' => 'a', 'ö' => 'o', 'é' => 'e', 'ü' => 'u', 'ß' => 'ss', 'æ' => 'ae', 'ø' => 'o']);
    $s = preg_replace('/[^a-z0-9]+/u', '-', $s) ?? '';
    return trim(substr($s, 0, 90), '-') ?: 'inlagg';
}

function unique_slug(string $slug, ?int $ignoreId = null): string
{
    $base = $slug; $i = 2;
    while (true) {
        $st = db()->prepare('SELECT id FROM posts WHERE slug=? ' . ($ignoreId ? 'AND id<>?' : ''));
        $st->execute($ignoreId ? [$slug, $ignoreId] : [$slug]);
        if (!$st->fetch()) return $slug;
        $slug = $base . '-' . $i++;
    }
}

function excerpt_from_html(string $html, int $len = 180): string
{
    $html = preg_replace('#</(p|h[1-6]|li|div|blockquote|tr)>#i', ' ', $html) ?? $html;
    $t = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($html), ENT_QUOTES, 'UTF-8')) ?? '');
    return mb_strlen($t) > $len ? rtrim(mb_substr($t, 0, $len)) . '…' : $t;
}

function reading_time(string $html): int
{
    return max(1, (int) round(str_word_count(strip_tags($html)) / 200));
}

function sv_date(?string $ts, bool $time = false): string
{
    if (!$ts) return '–';
    $t = strtotime($ts);
    $m = ['jan', 'feb', 'mar', 'apr', 'maj', 'jun', 'jul', 'aug', 'sep', 'okt', 'nov', 'dec'];
    return date('j', $t) . ' ' . $m[(int) date('n', $t) - 1] . ' ' . date('Y', $t) . ($time ? ' ' . date('H:i', $t) : '');
}

/* ---------- leads ---------- */
const LEAD_STATUS = ['ny' => 'Ny', 'kontaktad' => 'Kontaktad', 'offert' => 'Offert skickad', 'bokad' => 'Bokad', 'forlorad' => 'Förlorad', 'spam' => 'Spam'];

function lead_create(array $d): int
{
    $st = db()->prepare('INSERT INTO leads(ref,name,phone,email,address,postal,service,size,frequency,wish_date,message,status,source,ip,ua,mail_sent,created_at,updated_at)
        VALUES(:ref,:name,:phone,:email,:address,:postal,:service,:size,:frequency,:wish_date,:message,"ny",:source,:ip,:ua,:mail_sent,:c,:c)');
    $st->execute([
        ':ref' => $d['ref'] ?? '', ':name' => $d['name'] ?? '', ':phone' => $d['phone'] ?? '', ':email' => $d['email'] ?? '',
        ':address' => $d['address'] ?? '', ':postal' => $d['postal'] ?? '', ':service' => $d['service'] ?? '', ':size' => $d['size'] ?? '',
        ':frequency' => $d['frequency'] ?? '', ':wish_date' => $d['wish_date'] ?? '', ':message' => $d['message'] ?? '',
        ':source' => $d['source'] ?? 'webb', ':ip' => $d['ip'] ?? '', ':ua' => mb_substr((string) ($d['ua'] ?? ''), 0, 300),
        ':mail_sent' => (int) ($d['mail_sent'] ?? 0), ':c' => $d['created_at'] ?? now(),
    ]);
    return (int) db()->lastInsertId();
}

/* ---------- blog: upsert from BabyLoveGrowth payload (webhook or API) ---------- */
function post_upsert_blg(array $a): array
{
    $blgId = (string) ($a['id'] ?? '');
    $title = trim((string) ($a['title'] ?? ''));
    $html  = (string) ($a['content_html'] ?? $a['contentHtml'] ?? '');
    if ($blgId === '' || $title === '' || $html === '') throw new InvalidArgumentException('Saknar id, title eller content_html');

    $slug = slugify((string) ($a['slug'] ?? $title));
    $kw = $a['keywords'] ?? [];
    $row = [
        'title' => $title, 'excerpt' => (string) ($a['excerpt'] ?? excerpt_from_html($html)),
        'meta_description' => (string) ($a['metaDescription'] ?? $a['meta_description'] ?? ''),
        'content_html' => $html, 'content_md' => (string) ($a['content_markdown'] ?? $a['contentMarkdown'] ?? ''),
        'hero_image_url' => (string) ($a['heroImageUrl'] ?? $a['hero_image_url'] ?? ''),
        'hero_image_alt' => (string) ($a['heroImageAlt'] ?? $a['hero_image_alt'] ?? $title),
        'keywords' => json_encode(is_array($kw) ? $kw : [$kw], JSON_UNESCAPED_UNICODE),
        'lang' => (string) ($a['languageCode'] ?? $a['lang'] ?? 'sv'),
        'json_ld' => is_string($a['jsonLd'] ?? null) ? $a['jsonLd'] : json_encode($a['jsonLd'] ?? null, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        'faq_json_ld' => is_string($a['faqJsonLd'] ?? null) ? $a['faqJsonLd'] : json_encode($a['faqJsonLd'] ?? null, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    ];
    $ex = db()->prepare('SELECT id, slug, status FROM posts WHERE blg_id=?'); $ex->execute([$blgId]); $exists = $ex->fetch();
    $status = setting('blg_autopublish', '1') === '1' ? 'published' : 'draft';
    if ($exists) {
        $row['slug'] = unique_slug($slug, (int) $exists['id']);
        $sets = implode(',', array_map(fn($k) => "$k=:$k", array_keys($row)));
        $st = db()->prepare("UPDATE posts SET $sets, updated_at=:u WHERE id=:id");
        $st->execute($row + [':u' => now(), ':id' => $exists['id']]);
        return ['id' => (int) $exists['id'], 'slug' => $row['slug'], 'created' => false];
    }
    $row['slug'] = unique_slug($slug);
    $created = !empty($a['createdAt'] ?? $a['created_at']) ? date('Y-m-d H:i:s', strtotime((string) ($a['createdAt'] ?? $a['created_at']))) : now();
    $cols = array_keys($row);
    $st = db()->prepare('INSERT INTO posts(blg_id,' . implode(',', $cols) . ',status,source,published_at,created_at,updated_at) VALUES(:blg_id,' . implode(',', array_map(fn($k) => ":$k", $cols)) . ',:status,"blg",:p,:c,:c)');
    $st->execute($row + [':blg_id' => $blgId, ':status' => $status, ':p' => $status === 'published' ? $created : null, ':c' => $created]);
    return ['id' => (int) db()->lastInsertId(), 'slug' => $row['slug'], 'created' => true];
}

/* ---------- BabyLoveGrowth API client ---------- */
function blg_api(string $path, ?string $key = null): array
{
    $key = $key ?? setting('blg_api_key', '');
    if (!$key) throw new RuntimeException('Ingen API-nyckel sparad');
    $ch = curl_init('https://api.babylovegrowth.ai/api/integrations' . $path);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 30, CURLOPT_HTTPHEADER => ['X-API-Key: ' . $key, 'Content-Type: application/json', 'Accept: application/json']]);
    $body = curl_exec($ch); $code = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE); $err = curl_error($ch); curl_close($ch);
    if ($body === false) throw new RuntimeException('Nätverksfel: ' . $err);
    if ($code >= 400) throw new RuntimeException("API svarade $code: " . mb_substr((string) $body, 0, 200));
    $j = json_decode((string) $body, true);
    if (!is_array($j)) throw new RuntimeException('Ogiltigt JSON-svar');
    return $j;
}

function blg_sync(int $max = 50): array
{
    $list = blg_api('/v1/articles?limit=' . $max . '&offset=0');
    $items = $list['articles'] ?? $list['data'] ?? (array_is_list($list) ? $list : []);
    $new = 0; $upd = 0; $errs = [];
    foreach ($items as $it) {
        $id = (string) ($it['id'] ?? ''); if ($id === '') continue;
        $st = db()->prepare('SELECT id FROM posts WHERE blg_id=?'); $st->execute([$id]);
        if ($st->fetch()) continue; // already synced; webhook keeps it fresh
        try {
            $full = blg_api('/v1/articles/' . rawurlencode($id));
            $full = $full['article'] ?? $full;
            $r = post_upsert_blg($full + ['id' => $id]);
            $r['created'] ? $new++ : $upd++;
        } catch (Throwable $t) { $errs[] = $id . ': ' . $t->getMessage(); }
    }
    return ['total' => count($items), 'new' => $new, 'updated' => $upd, 'errors' => $errs];
}
