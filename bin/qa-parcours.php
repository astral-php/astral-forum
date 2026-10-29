<?php

declare(strict_types=1);

/**
 * Parcours QA HTTP local (CLI) — register/login/topic/reply/admin/FAQ.
 * Usage: php storage/qa-parcours.php
 */

$base = getenv('QA_BASE') ?: 'http://astral-forum.local';
$cookie = sys_get_temp_dir() . '/astral-forum-qa-cookies.txt';
@unlink($cookie);

function req(string $method, string $url, array $opts = []): array
{
    global $cookie;
    $ch = curl_init($url);
    $headers = [];
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => $method,
        CURLOPT_COOKIEJAR      => $cookie,
        CURLOPT_COOKIEFILE     => $cookie,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_HEADER         => true,
    ]);
    if (($opts['body'] ?? null) !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $opts['body']);
    }
    $raw = curl_exec($ch);
    if ($raw === false) {
        throw new RuntimeException(curl_error($ch));
    }
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    curl_close($ch);
    $header = substr($raw, 0, $headerSize);
    $body = substr($raw, $headerSize);
    $location = null;
    if (preg_match('/^Location:\s*(.+)$/mi', $header, $m)) {
        $location = trim($m[1]);
    }

    return ['code' => $code, 'body' => $body, 'location' => $location, 'header' => $header];
}

function token(string $html): string
{
    if (!preg_match('/name="_token"\s+value="([a-f0-9]+)"/', $html, $m)) {
        throw new RuntimeException('CSRF token introuvable');
    }

    return $m[1];
}

function assert_true(bool $cond, string $msg): void
{
    if (!$cond) {
        fwrite(STDERR, "FAIL: {$msg}\n");
        exit(1);
    }
    echo "OK: {$msg}\n";
}

$email = 'qa-' . bin2hex(random_bytes(4)) . '@example.com';
$password = 'password123';

$r = req('GET', $base . '/register');
assert_true($r['code'] === 200, 'GET /register');
$t = token($r['body']);
$r = req('POST', $base . '/register', [
    'body' => http_build_query([
        'name' => 'QA Bot',
        'email' => $email,
        'password' => $password,
        'password_confirmation' => $password,
        '_token' => $t,
    ]),
]);
assert_true($r['code'] === 302, 'POST /register → 302');

$r = req('GET', $base . '/forums');
assert_true($r['code'] === 200 && str_contains($r['body'], 'Forum'), 'GET /forums');

$r = req('GET', $base . '/forums/general/topics/create');
assert_true($r['code'] === 200, 'GET create topic');
$t = token($r['body']);
$title = 'QA Topic ' . date('H:i:s');
$r = req('POST', $base . '/forums/general/topics', [
    'body' => http_build_query([
        'title' => $title,
        'content' => 'Message initial QA automatique.',
        '_token' => $t,
    ]),
]);
assert_true($r['code'] === 302 && is_string($r['location']), 'POST create topic');
$topicUrl = $r['location'];
if (str_starts_with($topicUrl, 'http')) {
    $topicUrl = parse_url($topicUrl, PHP_URL_PATH) ?: $topicUrl;
}
echo "  topic → {$topicUrl}\n";

$r = req('GET', $base . $topicUrl);
assert_true($r['code'] === 200, 'GET topic');
assert_true(preg_match('#action="/topics/(\d+)/posts"#', $r['body'], $tm) === 1, 'form reply present');
$topicId = (int) $tm[1];
$t = token($r['body']);
$r = req('POST', $base . '/topics/' . $topicId . '/posts', [
    'body' => http_build_query([
        'content' => 'Réponse QA automatique.',
        '_token' => $t,
    ]),
]);
assert_true($r['code'] === 302, 'POST reply');

$r = req('GET', $base . $topicUrl);
assert_true(str_contains($r['body'], 'Réponse QA automatique'), 'reply visible');

$r = req('GET', $base . '/faq/proposals/create');
$t = token($r['body']);
$r = req('POST', $base . '/faq/proposals', [
    'body' => http_build_query([
        'title' => 'FAQ QA ' . date('H:i:s'),
        'description' => 'Description suffisamment longue pour la FAQ QA.',
        '_token' => $t,
    ]),
]);
assert_true($r['code'] === 302 && str_contains((string) $r['location'], '/account/faq-proposals/'), 'FAQ submit');
preg_match('#/account/faq-proposals/(\d+)#', (string) $r['location'], $fm);
$faqId = (int) ($fm[1] ?? 0);
assert_true($faqId > 0, "FAQ id={$faqId}");

// Promote current user to admin in DB
$db = new PDO('sqlite:' . dirname(__DIR__) . '/database/forum.sqlite');
$db->prepare("UPDATE users SET role = 'admin' WHERE email = ?")->execute([$email]);

// Re-login to refresh role in session
$r = req('GET', $base . '/');
$t = token($r['body']);
req('POST', $base . '/logout', ['body' => http_build_query(['_token' => $t])]);
$r = req('GET', $base . '/login');
$t = token($r['body']);
$r = req('POST', $base . '/login', [
    'body' => http_build_query([
        'email' => $email,
        'password' => $password,
        '_token' => $t,
    ]),
]);
assert_true($r['code'] === 302, 're-login as admin');

$r = req('GET', $base . '/admin/forums');
assert_true($r['code'] === 200, 'GET /admin/forums');
$t = token($r['body']);
$r = req('POST', $base . '/admin/forums', [
    'body' => http_build_query([
        'name' => 'Support QA',
        'type' => 'specialized',
        'description' => 'Forum spécialisé QA',
        'position' => 5,
        '_token' => $t,
    ]),
]);
assert_true($r['code'] === 302, 'POST create specialized forum');
$r = req('GET', $base . '/forums');
assert_true(str_contains($r['body'], 'Support QA'), 'specialized forum listed');

$r = req('GET', $base . $topicUrl);
$t = token($r['body']);
$r = req('POST', $base . '/admin/topics/' . $topicId . '/lock', [
    'body' => http_build_query(['_token' => $t]),
]);
assert_true($r['code'] === 302, 'lock topic');
$r = req('GET', $base . $topicUrl);
assert_true(str_contains($r['body'], 'Verrouillé') || str_contains($r['body'], 'verrouill'), 'topic locked UI');
assert_true(!str_contains($r['body'], 'action="/topics/' . $topicId . '/posts"'), 'reply form hidden when locked');

$r = req('GET', $base . '/admin/faq/proposals/' . $faqId);
assert_true($r['code'] === 200, 'GET FAQ review');
$t = token($r['body']);
$r = req('POST', $base . '/admin/faq/proposals/' . $faqId . '/review', [
    'body' => http_build_query(['_token' => $t]),
]);
assert_true($r['code'] === 302, 'FAQ take review');

$r = req('GET', $base . '/admin/faq/proposals/' . $faqId);
$t = token($r['body']);
$r = req('POST', $base . '/admin/faq/proposals/' . $faqId . '/approve', [
    'body' => http_build_query(['_token' => $t]),
]);
assert_true($r['code'] === 302, 'FAQ approve');

$r = req('GET', $base . '/admin/faq/proposals/' . $faqId);
$t = token($r['body']);
$r = req('POST', $base . '/admin/faq/proposals/' . $faqId . '/publish', [
    'body' => http_build_query(['_token' => $t]),
]);
assert_true($r['code'] === 302, 'FAQ publish');

$row = $db->query('SELECT status, published_topic_id FROM faq_submissions WHERE id = ' . $faqId)->fetch(PDO::FETCH_ASSOC);
assert_true(($row['status'] ?? '') === 'published', 'FAQ status published');
assert_true((int) ($row['published_topic_id'] ?? 0) > 0, 'FAQ linked topic');

$faqForum = $db->query("SELECT slug FROM forums WHERE type = 'faq' LIMIT 1")->fetchColumn();
assert_true(is_string($faqForum) && $faqForum !== '', 'FAQ forum exists');

// Guest cannot access create
@unlink($cookie);
$r = req('GET', $base . '/forums/general/topics/create');
assert_true($r['code'] === 302, 'guest create topic → login');

echo "\nQA parcours OK\n";
