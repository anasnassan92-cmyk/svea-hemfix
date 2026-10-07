<?php
/**
 * Svea Hemfix – contact/offert form handler.
 * Sends the request to the company inbox (+ copy), confirms to the customer, logs locally.
 */
declare(strict_types=1);

require_once __DIR__ . '/lib/app.php';
$TO       = setting('notify_email', 'info@sveahemfix.se') ?: 'info@sveahemfix.se';
$CC       = array_values(array_filter(array_map('trim', explode(',', (string) setting('notify_cc', 'info@mizagroup.se')))));
$FROM     = 'noreply@sveahemfix.se';
$BRAND    = 'Svea Hemfix';
$PHONE    = '010 300 10 32';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}
if (!empty($_POST['_gotcha'])) { echo json_encode(['ok' => true]); exit; } // honeypot

$clean = static function (string $key, int $max = 500): string {
    $v = isset($_POST[$key]) ? trim((string) $_POST[$key]) : '';
    $v = str_replace(["\r", "\n"], ' ', $v);
    return mb_substr(strip_tags($v), 0, $max);
};

$name      = $clean('name', 120);
$phone     = $clean('phone', 40);
$email     = $clean('email', 120);
$address   = $clean('address', 200);
$postal    = $clean('postal', 20);
$service   = $clean('service', 80);
$size      = $clean('size', 10);
$frequency = $clean('frequency', 40);
$date      = $clean('date', 20);
$message   = isset($_POST['message']) ? mb_substr(strip_tags(trim((string) $_POST['message'])), 0, 3000) : '';
$gdpr      = !empty($_POST['gdpr']);

$errors = [];
if ($name === '')                               $errors[] = 'name';
if (mb_strlen($phone) < 6)                      $errors[] = 'phone';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'email';
if (!$gdpr)                                     $errors[] = 'gdpr';
if ($errors) { http_response_code(422); echo json_encode(['ok' => false, 'errors' => $errors]); exit; }

$ref = strtoupper(substr(md5($email . microtime(true)), 0, 6));

$lines = [
    "Ny förfrågan från sveahemfix.se  (ref $ref)",
    str_repeat('-', 46),
    "Namn:           $name",
    "Telefon:        $phone",
    "E-post:         $email",
    "Adress:         $address" . ($postal ? ", $postal" : ''),
    "Tjänst:         $service",
    "Storlek:        " . ($size ? "$size kvm" : '-'),
    "Frekvens:       " . ($frequency ?: '-'),
    "Önskat datum:   " . ($date ?: '-'),
    "Meddelande:",
    $message !== '' ? $message : '-',
    str_repeat('-', 46),
    'Skickat: ' . date('Y-m-d H:i') . ' · IP: ' . ($_SERVER['REMOTE_ADDR'] ?? ''),
    'Svara direkt på detta mejl för att nå kunden.',
];
$body = implode("\n", $lines) . "\n";

$enc = static fn(string $s): string => '=?UTF-8?B?' . base64_encode($s) . '?=';
$headersBase = "MIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit\r\nX-Mailer: sveahemfix.se\r\n";

// 1) to company
$h1 = "From: $BRAND <$FROM>\r\nReply-To: $name <$email>\r\n" . ($CC ? 'Cc: ' . implode(', ', $CC) . "\r\n" : '') . $headersBase;
$sent = @mail($TO, $enc("Ny förfrågan: $service – $name (ref $ref)"), $body, $h1, '-f' . $FROM);

// 2) confirmation to customer
$conf = "Hej $name,\n\nTack för din förfrågan till $BRAND! Vi har tagit emot den (ref $ref) och återkommer inom 24 timmar – ofta samma dag.\n\n"
      . "Din förfrågan:\n  Tjänst: $service\n" . ($size ? "  Storlek: $size kvm\n" : '') . ($frequency ? "  Frekvens: $frequency\n" : '') . ($date ? "  Önskat datum: $date\n" : '')
      . "\nHar du frågor under tiden? Ring oss på $PHONE eller svara på detta mejl.\n\nVänliga hälsningar\n$BRAND\nsveahemfix.se · $PHONE\n";
$h2 = "From: $BRAND <$FROM>\r\nReply-To: $BRAND <$TO>\r\n" . $headersBase;
@mail($email, $enc("Vi har tagit emot din förfrågan – $BRAND (ref $ref)"), $conf, $h2, '-f' . $FROM);

// 3) store in admin panel (SQLite) – the lead is saved even if mail fails
$stored = false;
try {
    lead_create(['ref' => $ref, 'name' => $name, 'phone' => $phone, 'email' => $email, 'address' => $address, 'postal' => $postal, 'service' => $service, 'size' => $size, 'frequency' => $frequency, 'wish_date' => $date, 'message' => $message, 'source' => 'webb', 'ip' => client_ip(), 'ua' => (string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 'mail_sent' => $sent ? 1 : 0]);
    $stored = true;
} catch (Throwable $t) {
    @file_put_contents(__DIR__ . '/_forfragningar.log', date('c') . "\t$ref\t$name\t$phone\t$email\t$service\t$size\t$frequency\t$date\tDBERR\n", FILE_APPEND | LOCK_EX);
}

if ($sent || $stored) { echo json_encode(['ok' => true, 'ref' => $ref]); }
else { http_response_code(500); echo json_encode(['ok' => false, 'error' => 'mail_failed']); }
