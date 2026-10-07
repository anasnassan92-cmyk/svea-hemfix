<?php
/**
 * Gallery media management (images → WebP via GD, videos as MP4 + poster).
 * Files live in media/p (pNN.webp + pNN-t.webp) and media/v (vNN.mp4 + vNN.webp).
 * manifest.full.json = everything (incl. hidden), manifest.json = what the site renders.
 */
declare(strict_types=1);
require_once __DIR__ . '/app.php';

const MEDIA_DIR = SVEA_ROOT . '/media';
const MEDIA_CATS = ['hem' => 'Hemstädning', 'kok' => 'Kök', 'badrum' => 'Badrum', 'kontor' => 'Kontor', 'trapphus' => 'Trapphus', 'fonster' => 'Fönsterputs', 'flytt' => 'Flytt & bygg', 'mobel' => 'Möbler & mattor', 'team' => 'Vårt team'];
const MEDIA_MAX_IMAGE = 25 * 1024 * 1024;
const MEDIA_MAX_VIDEO = 300 * 1024 * 1024;

function media_load(): array
{
    $f = file_exists(MEDIA_DIR . '/manifest.full.json') ? MEDIA_DIR . '/manifest.full.json' : MEDIA_DIR . '/manifest.json';
    $m = json_decode((string) @file_get_contents($f), true);
    if (!is_array($m)) $m = [];
    $m['photos'] = array_values($m['photos'] ?? []); $m['videos'] = array_values($m['videos'] ?? []);
    return $m;
}

function media_save(array $m): void
{
    $live = $m;
    $live['photos'] = array_values(array_filter($m['photos'], fn($x) => empty($x['hidden'])));
    $live['videos'] = array_values(array_filter($m['videos'], fn($x) => empty($x['hidden'])));
    $flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT;
    if (file_put_contents(MEDIA_DIR . '/manifest.full.json', json_encode($m, $flags), LOCK_EX) === false) throw new RuntimeException('Kunde inte skriva manifest.full.json');
    if (file_put_contents(MEDIA_DIR . '/manifest.json', json_encode($live, $flags), LOCK_EX) === false) throw new RuntimeException('Kunde inte skriva manifest.json');
}

function media_valid_id(string $id): bool { return (bool) preg_match('/^[pv]\d{1,4}$/', $id); }

function media_next_id(array $m, string $type): string
{
    $p = $type === 'v' ? 'v' : 'p'; $max = 0;
    foreach ($m[$type === 'v' ? 'videos' : 'photos'] as $it) if (preg_match('/^[pv](\d+)$/', (string) $it['id'], $mm)) $max = max($max, (int) $mm[1]);
    // also avoid clashing with stray files on disk
    foreach (glob(MEDIA_DIR . ($p === 'v' ? '/v/v*.mp4' : '/p/p*.webp')) ?: [] as $f) if (preg_match('/[pv](\d+)(-t)?\.\w+$/', $f, $mm)) $max = max($max, (int) $mm[1]);
    return $p . ($max + 1);
}

function media_find(array $m, string $id): ?array
{
    $t = $id[0] === 'v' ? 'videos' : 'photos';
    foreach ($m[$t] as $i => $it) if ($it['id'] === $id) return [$t, $i];
    return null;
}

/* ---------- images ---------- */
function media_image_from_upload(string $tmp): GdImage
{
    $info = @getimagesize($tmp);
    if (!$info) throw new InvalidArgumentException('Filen är inte en giltig bild (jpg, png eller webp).');
    $mime = $info['mime'];
    $img = match ($mime) {
        'image/jpeg' => @imagecreatefromjpeg($tmp),
        'image/png' => @imagecreatefrompng($tmp),
        'image/webp' => @imagecreatefromwebp($tmp),
        default => null,
    };
    if (!$img) throw new InvalidArgumentException('Bildformatet stöds inte (' . $mime . '). Använd jpg, png eller webp.');
    if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
        $ex = @exif_read_data($tmp);
        $o = (int) ($ex['Orientation'] ?? 1);
        if ($o === 3) $img = imagerotate($img, 180, 0);
        elseif ($o === 6) $img = imagerotate($img, -90, 0);
        elseif ($o === 8) $img = imagerotate($img, 90, 0);
    }
    return $img;
}

function media_write_webp(GdImage $src, string $dest, int $maxSide, int $q): array
{
    $w = imagesx($src); $h = imagesy($src);
    $s = min(1, $maxSide / max($w, $h));
    $nw = max(1, (int) round($w * $s)); $nh = max(1, (int) round($h * $s));
    $dst = imagecreatetruecolor($nw, $nh);
    imagealphablending($dst, false); imagesavealpha($dst, true);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
    if (!imagewebp($dst, $dest, $q)) { imagedestroy($dst); throw new RuntimeException('Kunde inte spara ' . basename($dest)); }
    imagedestroy($dst);
    return [$nw, $nh];
}

function media_store_image(string $tmp, string $id): array
{
    $img = media_image_from_upload($tmp);
    [$w, $h] = media_write_webp($img, MEDIA_DIR . "/p/$id.webp", 1600, 80);
    media_write_webp($img, MEDIA_DIR . "/p/$id-t.webp", 640, 75);
    imagedestroy($img);
    return [$w, $h];
}

/* ---------- videos ---------- */
function media_store_video(string $tmp, string $id, ?string $posterTmp, array $meta): array
{
    $fi = new finfo(FILEINFO_MIME_TYPE); $mime = (string) $fi->file($tmp);
    if (!in_array($mime, ['video/mp4', 'video/quicktime', 'application/octet-stream'], true) || strtolower(pathinfo((string) ($meta['name'] ?? ''), PATHINFO_EXTENSION)) !== 'mp4') throw new InvalidArgumentException('Videon måste vara en MP4-fil (H.264). Fick: ' . $mime);
    if (!@move_uploaded_file($tmp, MEDIA_DIR . "/v/$id.mp4") && !@rename($tmp, MEDIA_DIR . "/v/$id.mp4")) throw new RuntimeException('Kunde inte spara videofilen.');
    @chmod(MEDIA_DIR . "/v/$id.mp4", 0644);
    $w = (int) ($meta['w'] ?? 0) > 0 ? (int) $meta['w'] : 1080; $h = (int) ($meta['h'] ?? 0) > 0 ? (int) $meta['h'] : 1920; $dur = max(0, (int) round((float) ($meta['dur'] ?? 0)));
    $posterOk = false;
    if ($posterTmp && is_file($posterTmp)) {
        try { $pi = media_image_from_upload($posterTmp); media_write_webp($pi, MEDIA_DIR . "/v/$id.webp", 960, 75); imagedestroy($pi); $posterOk = true; } catch (Throwable) {}
    }
    if (!$posterOk) { // neutral placeholder poster
        $pw = $h > $w ? 540 : 960; $ph = $h > $w ? 960 : 540;
        $im = imagecreatetruecolor($pw, $ph); imagefill($im, 0, 0, imagecolorallocate($im, 27, 94, 32));
        $c = imagecolorallocate($im, 245, 197, 24); imagefilledellipse($im, (int) ($pw / 2), (int) ($ph / 2), 90, 90, $c);
        imagewebp($im, MEDIA_DIR . "/v/$id.webp", 70); imagedestroy($im);
    }
    return [$w, $h, $dur];
}

/* ---------- high-level ops ---------- */
function media_add(array $file, string $cap, string $cat, ?array $poster = null, array $meta = []): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) throw new InvalidArgumentException(media_upload_error((int) $file['error']));
    $isVideo = str_starts_with((string) mime_content_type($file['tmp_name']), 'video/') || strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION)) === 'mp4';
    if ($isVideo && $file['size'] > MEDIA_MAX_VIDEO) throw new InvalidArgumentException('Videon är för stor (max ' . (MEDIA_MAX_VIDEO / 1048576) . ' MB). Komprimera den först.');
    if (!$isVideo && $file['size'] > MEDIA_MAX_IMAGE) throw new InvalidArgumentException('Bilden är för stor (max ' . (MEDIA_MAX_IMAGE / 1048576) . ' MB).');
    $m = media_load();
    $cap = mb_substr(strip_tags(trim($cap)), 0, 120) ?: pathinfo((string) $file['name'], PATHINFO_FILENAME);
    $cat = isset(MEDIA_CATS[$cat]) ? $cat : 'hem';
    if ($isVideo) {
        $id = media_next_id($m, 'v');
        [$w, $h, $dur] = media_store_video($file['tmp_name'], $id, $poster['tmp_name'] ?? null, $meta + ['name' => $file['name']]);
        $entry = ['id' => $id, 'cap' => $cap, 'cat' => $cat, 'w' => $w, 'h' => $h, 'dur' => $dur, 'size' => (int) filesize(MEDIA_DIR . "/v/$id.mp4")];
        array_unshift($m['videos'], $entry);
    } else {
        $id = media_next_id($m, 'p');
        [$w, $h] = media_store_image($file['tmp_name'], $id);
        $entry = ['id' => $id, 'cap' => $cap, 'cat' => $cat, 'w' => $w, 'h' => $h];
        array_unshift($m['photos'], $entry);
    }
    media_save($m);
    return $entry;
}

function media_replace(string $id, array $file, ?array $poster = null, array $meta = []): array
{
    if (!media_valid_id($id)) throw new InvalidArgumentException('Ogiltigt id.');
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) throw new InvalidArgumentException(media_upload_error((int) $file['error']));
    $m = media_load(); $pos = media_find($m, $id);
    if (!$pos) throw new InvalidArgumentException('Objektet finns inte.');
    [$t, $i] = $pos; $it = $m[$t][$i];
    if ($t === 'videos') {
        [$w, $h, $dur] = media_store_video($file['tmp_name'], $id, $poster['tmp_name'] ?? null, $meta + ['name' => $file['name']]);
        $it += []; $it['w'] = $w; $it['h'] = $h; $it['dur'] = $dur; $it['size'] = (int) filesize(MEDIA_DIR . "/v/$id.mp4");
    } else {
        [$w, $h] = media_store_image($file['tmp_name'], $id); $it['w'] = $w; $it['h'] = $h;
    }
    $it['v'] = time(); // cache-buster hint
    $m[$t][$i] = $it; media_save($m);
    return $it;
}

function media_delete(string $id): void
{
    if (!media_valid_id($id)) throw new InvalidArgumentException('Ogiltigt id.');
    $m = media_load(); $pos = media_find($m, $id);
    if (!$pos) throw new InvalidArgumentException('Objektet finns inte.');
    [$t, $i] = $pos;
    $files = $t === 'videos' ? [MEDIA_DIR . "/v/$id.mp4", MEDIA_DIR . "/v/$id.webp"] : [MEDIA_DIR . "/p/$id.webp", MEDIA_DIR . "/p/$id-t.webp"];
    foreach ($files as $f) if (is_file($f)) @unlink($f);
    array_splice($m[$t], $i, 1);
    media_save($m);
}

function media_upload_error(int $code): string
{
    return match ($code) {
        UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Filen är för stor för servern.',
        UPLOAD_ERR_PARTIAL => 'Uppladdningen avbröts – försök igen.',
        UPLOAD_ERR_NO_FILE => 'Ingen fil vald.',
        default => 'Uppladdningen misslyckades (kod ' . $code . ').',
    };
}
