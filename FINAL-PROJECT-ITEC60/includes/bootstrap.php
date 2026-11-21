<?php
/*
 * Shared security setup for every CineVault page.
 * Loaded at the end of db-connection.php, so any page that talks to the
 * database also gets: safe sessions, CSRF protection on every POST form,
 * login guards for the admin and member areas, and helper functions.
 */

if (defined('CV_BOOTSTRAPPED')) {
    return;
}
define('CV_BOOTSTRAPPED', true);

/* ---------- Time zone ----------
 * PHP and MySQL must agree, or "5 minutes ago", reset-code expiry and
 * Remember me expiry drift by hours. XAMPP ships PHP on Europe/Berlin.
 */
date_default_timezone_set(cv_config('timezone', 'Asia/Manila'));
$con->query("SET time_zone = '" . $con->real_escape_string(date('P')) . "'");

/* ---------- Errors ---------- */
ini_set('display_errors', cv_config('debug') ? '1' : '0');
error_reporting(E_ALL);

/* ---------- Session ---------- */
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ]);
    session_start();
}

if (!headers_sent()) {
    header_remove('X-Powered-By'); // don't advertise the PHP version
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

/* ---------- Asset version ----------
 * Added to CSS/JS links as ?v=... so browsers fetch fresh copies.
 * Change this whenever a .css or .js file changes. */
const CV_ASSET_VERSION = '20261121.2';

/* ---------- Small helpers ---------- */
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/* ---------- Video sources ----------
 * A movie/episode video is either an uploaded file name (kept in
 * DASHBOARD-HTML/<folder>/) or a free stream link from the Internet Archive
 * (public domain films). Only archive.org links are trusted. */
function cv_is_stream_url($value)
{
    return is_string($value)
        && preg_match('#^https://(archive\.org|[a-z0-9-]+\.archive\.org)/download/[^\s"\'<>\\\\]+$#i', $value) === 1
        && strlen($value) <= 255;
}

function cv_video_src($value, $folder = 'MOVIE_SERIES_VIDEO')
{
    if (cv_is_stream_url($value)) {
        return $value;
    }
    return '../DASHBOARD-HTML/' . $folder . '/' . basename((string) $value);
}

/* ---------- Site settings (admin Settings page, tbl_settings) ---------- */
function cv_setting($key, $default = '')
{
    static $cache = null;
    global $con;
    if ($cache === null) {
        $cache = [];
        $res = @mysqli_query($con, 'SELECT setting_key, setting_value FROM tbl_settings');
        while ($res && ($r = mysqli_fetch_assoc($res))) {
            $cache[$r['setting_key']] = $r['setting_value'];
        }
    }
    $v = $cache[$key] ?? null;
    return ($v === null || $v === '') ? $default : $v;
}

function cv_setting_save($key, $value)
{
    global $con;
    $stmt = $con->prepare('INSERT INTO tbl_settings (setting_key, setting_value) VALUES (?, ?)
        ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
    $stmt->bind_param('ss', $key, $value);
    $stmt->execute();
    $stmt->close();
}

/* A member's own preferences (member Settings page), with defaults. */
function cv_member_prefs($memberId)
{
    global $con;
    $prefs = ['autoplay_previews' => 1, 'resume_playback' => 1];
    if ($memberId) {
        $stmt = $con->prepare('SELECT autoplay_previews, resume_playback FROM tbl_member_settings WHERE signup_id = ?');
        $stmt->bind_param('i', $memberId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($row) {
            $prefs = array_map('intval', $row);
        }
    }
    return $prefs;
}

/* ---------- Member "new arrivals" bell ----------
 * The newest titles, each marked read or unread for this member. */
const CV_ARRIVALS_SHOWN = 8;

function cv_member_arrivals($con, $memberId)
{
    $limit = CV_ARRIVALS_SHOWN;
    $stmt = $con->prepare(
        "SELECT m.movie_series_id, m.title, m.category, m.date_released, m.date_posted,
                (r.movie_series_id IS NOT NULL) AS is_read
         FROM tbl_movie_series m
         LEFT JOIN tbl_member_notif_reads r ON r.movie_series_id = m.movie_series_id AND r.signup_id = ?
         ORDER BY m.date_posted DESC, m.movie_series_id DESC
         LIMIT $limit"
    );
    $stmt->bind_param('i', $memberId);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

function cv_member_unread_arrivals($con, $memberId)
{
    $n = 0;
    foreach (cv_member_arrivals($con, $memberId) as $row) {
        $n += $row['is_read'] ? 0 : 1;
    }
    return $n;
}

// "5 minutes ago" style times (used by the notification bells).
function cv_time_ago($datetime)
{
    $diff = time() - strtotime($datetime);
    if ($diff < 60) {
        return 'Just now';
    }
    foreach ([['year', 31536000], ['month', 2592000], ['week', 604800], ['day', 86400], ['hour', 3600], ['minute', 60]] as [$unit, $secs]) {
        if ($diff >= $secs) {
            $n = (int) floor($diff / $secs);
            return $n . ' ' . $unit . ($n === 1 ? '' : 's') . ' ago';
        }
    }
    return 'Just now';
}

function cv_client_ip()
{
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/* ---------- CSRF ---------- */
function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field()
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

// Every POST must carry the token, either as a form field or an X-CSRF-Token header.
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $sent = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(403);
        $tooBig = empty($_POST) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0;
        echo $tooBig
            ? 'The upload is larger than the server allows (post_max_size). Use a smaller file.'
            : 'Your session expired or the form was sent from another site. Go back, refresh the page and try again.';
        exit;
    }
}

// Add the hidden token to every POST form on the page automatically,
// and expose it to JavaScript through a meta tag.
ob_start(function ($html) {
    $field = csrf_field();
    $html = preg_replace(
        '/<form\b[^>]*\bmethod\s*=\s*["\']?post["\']?[^>]*>/i',
        '$0' . $field,
        $html
    );
    $html = preg_replace(
        '/<head\b[^>]*>/i',
        '$0<meta name="csrf-token" content="' . e(csrf_token()) . '">',
        $html,
        1
    );
    // Vintage theme on every page, admin included (not the PDF reports or
    // the full-screen player). Added last in <head> so it wins over page CSS.
    $rel = cv_relative_script();
    if (stripos($html, '</head>') !== false
        && !preg_match('#^libs/#i', $rel)
        && $rel !== 'MovieWebsite/play-vid.php'
        && stripos($html, 'data-cv-plain') === false) { // printed sheets stay white
        $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $base = substr($script, 0, strlen($script) - strlen($rel));
        $theme = '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>'
            . '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&display=swap">'
            . '<link rel="stylesheet" href="' . e($base . 'shared/vintage-theme.css?v=' . CV_ASSET_VERSION) . '">';
        $html = preg_replace('#</head>#i', $theme . '$0', $html, 1);
    }
    // Show a waiting flash message (see cv_flash) on the first real page.
    if (!empty($_SESSION['cv_flash']) && preg_match('/<body\b[^>]*>/i', $html)) {
        $flash = $_SESSION['cv_flash'];
        unset($_SESSION['cv_flash']);
        $alert = cv_flash_html($flash);
        // callback form, so a "$" in the message is never read as a regex group
        $html = preg_replace_callback('/<body\b[^>]*>/i', fn($m) => $m[0] . $alert, $html, 1);
    }
    return $html;
});

/* ---------- Login guards ---------- */
function cv_relative_script()
{
    $root   = str_replace('\\', '/', realpath(dirname(__DIR__)));
    $script = str_replace('\\', '/', realpath($_SERVER['SCRIPT_FILENAME'] ?? '') ?: '');
    return ltrim(substr($script, strlen($root)), '/');
}

function cv_require_admin($con)
{
    $ok = false;
    if (!empty($_SESSION['admin_logged_in']) && !empty($_SESSION['admin_id'])) {
        $stmt = $con->prepare('SELECT admin_id FROM tbl_admin_acc WHERE admin_id = ? AND is_archived = 0 LIMIT 1');
        $id = (int) $_SESSION['admin_id'];
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $ok = (bool) $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }
    if (!$ok) {
        unset($_SESSION['admin_logged_in'], $_SESSION['admin_id'], $_SESSION['admin_email']);
        if (cv_wants_json()) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Not signed in']);
            exit;
        }
        header('Location: ../LANDING-PAGE/SignIn/LP-SignIn.php');
        exit;
    }
}

function cv_require_member($con)
{
    $ok = false;
    if (!empty($_SESSION['is_logged_in']) && !empty($_SESSION['user_id'])) {
        $stmt = $con->prepare(
            // Membership is free: any account that is not archived may watch.
            "SELECT signup_id FROM tbl_signup_acc WHERE signup_id = ? AND is_archived = 0 LIMIT 1"
        );
        $id = (int) $_SESSION['user_id'];
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $ok = (bool) $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }
    if (!$ok) {
        unset($_SESSION['is_logged_in'], $_SESSION['user_id'], $_SESSION['user_email']);
        header('Location: ../LANDING-PAGE/SignIn/LP-SignIn.php');
        exit;
    }
}

function cv_wants_json()
{
    return in_array(basename($_SERVER['SCRIPT_NAME'] ?? ''), ['admin-notifications.php', 'tmdb-api.php', 'archive-api.php', 'watch-progress.php'], true)
        || stripos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false;
}

// Sign the user in: new session id so an old one can't be reused.
function cv_login_session(array $values)
{
    session_regenerate_id(true);
    foreach ($values as $key => $value) {
        $_SESSION[$key] = $value;
    }
}

/*
 * Checks a password. Older versions of the site altered passwords before
 * hashing them (stripped characters), so if the real password fails we try
 * the old altered form once, and on success re-save the hash correctly.
 */
function cv_check_password($con, $table, $idColumn, $hashColumn, array $row, $password)
{
    $hash = $row[$hashColumn];
    $ok = password_verify($password, $hash);

    if (!$ok) {
        foreach ([FILTER_SANITIZE_EMAIL, FILTER_SANITIZE_FULL_SPECIAL_CHARS] as $oldFilter) {
            $legacy = filter_var($password, $oldFilter);
            if ($legacy !== $password && password_verify($legacy, $hash)) {
                $ok = true;
                break;
            }
        }
        if (!$ok) {
            return false;
        }
        $hash = ''; // force the re-save below
    }

    if ($hash === '' || password_needs_rehash($hash, PASSWORD_DEFAULT)) {
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $con->prepare("UPDATE `$table` SET `$hashColumn` = ? WHERE `$idColumn` = ?");
        $id = (int) $row[$idColumn];
        $stmt->bind_param('si', $newHash, $id);
        $stmt->execute();
        $stmt->close();
    }
    return true;
}

/* ---------- Brute force limits ---------- */
// True when this email (or this IP) has failed too often in the last 15 minutes.
function cv_too_many_attempts($con, $scope, $identifier, $max = 5, $minutes = 15)
{
    $identifier = strtolower(trim((string) $identifier));
    $ip = cv_client_ip();
    $stmt = $con->prepare(
        'SELECT
            SUM(identifier = ?) AS by_id,
            SUM(ip_address = ?) AS by_ip
         FROM tbl_login_attempts
         WHERE scope = ? AND attempted_at > (NOW() - INTERVAL ? MINUTE)'
    );
    $stmt->bind_param('sssi', $identifier, $ip, $scope, $minutes);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return ((int) $row['by_id']) >= $max || ((int) $row['by_ip']) >= $max * 4;
}

function cv_record_failure($con, $scope, $identifier)
{
    $identifier = strtolower(trim((string) $identifier));
    $ip = cv_client_ip();
    // Old records are only useful for the 15-minute lockout window; drop anything older than a day.
    $con->query('DELETE FROM tbl_login_attempts WHERE attempted_at < (NOW() - INTERVAL 1 DAY)');

    $stmt = $con->prepare('INSERT INTO tbl_login_attempts (scope, identifier, ip_address) VALUES (?, ?, ?)');
    $stmt->bind_param('sss', $scope, $identifier, $ip);
    $stmt->execute();
    $stmt->close();
}

function cv_clear_failures($con, $scope, $identifier)
{
    $identifier = strtolower(trim((string) $identifier));
    $stmt = $con->prepare('DELETE FROM tbl_login_attempts WHERE scope = ? AND identifier = ?');
    $stmt->bind_param('ss', $scope, $identifier);
    $stmt->execute();
    $stmt->close();
}

/* ---------- Archive for titles and genres ----------
 * Archived titles and genres live in their own *_archived tables, so every
 * page that reads tbl_movie_series / tbl_movie_series_genre sees only active
 * ones (free hosts don't allow database views). Members and admins keep an
 * is_archived column instead.
 */
const CV_ARCHIVE_TABLES = [
    'title' => ['tbl_movie_series', 'tbl_movie_series_archived', 'movie_series_id'],
    'genre' => ['tbl_movie_series_genre', 'tbl_movie_series_genre_archived', 'genre_id'],
];

// Moves one row from $from to $to. Returns false if it isn't there or the move fails.
function cv_move_row($con, $from, $to, $key, $id, $archived)
{
    $id = (int) $id;
    $con->begin_transaction();
    try {
        mysqli_query($con, "INSERT INTO `$to` SELECT * FROM `$from` WHERE `$key` = $id");
        if (mysqli_affected_rows($con) < 1) {
            $con->rollback();
            return false;
        }
        mysqli_query($con, $archived
            ? "UPDATE `$to` SET is_archived = 1, archived_at = NOW()" . ($from === 'tbl_movie_series' ? ', feature_slot = NULL' : '') . " WHERE `$key` = $id"
            : "UPDATE `$to` SET is_archived = 0, archived_at = NULL WHERE `$key` = $id");
        mysqli_query($con, "DELETE FROM `$from` WHERE `$key` = $id");
        $con->commit();
        return true;
    } catch (mysqli_sql_exception $ex) {
        $con->rollback();
        return false;
    }
}

function cv_archive_row($con, $kind, $id)
{
    [$active, $archive, $key] = CV_ARCHIVE_TABLES[$kind];
    return cv_move_row($con, $active, $archive, $key, $id, true);
}

function cv_restore_row($con, $kind, $id)
{
    [$active, $archive, $key] = CV_ARCHIVE_TABLES[$kind];
    if ($kind === 'title') {
        // Bring back any of its genres that were archived after it, so it keeps them.
        $stmt = $con->prepare('SELECT genre_id1, genre_id2, genre_id3 FROM tbl_movie_series_archived WHERE movie_series_id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $genres = array_filter((array) $stmt->get_result()->fetch_row());
        $stmt->close();
        foreach ($genres as $genreId) {
            cv_move_row($con, 'tbl_movie_series_genre_archived', 'tbl_movie_series_genre', 'genre_id', $genreId, false);
        }
    }
    return cv_move_row($con, $archive, $active, $key, $id, false);
}

/* ---------- Safe uploads ---------- */
const CV_IMAGE_TYPES = [
    'jpg' => ['image/jpeg'], 'jpeg' => ['image/jpeg'], 'png' => ['image/png'],
    'webp' => ['image/webp'], 'gif' => ['image/gif'],
];
const CV_VIDEO_TYPES = [
    'mp4' => ['video/mp4', 'application/mp4'], 'webm' => ['video/webm'], 'm4v' => ['video/mp4', 'video/x-m4v'],
];

/*
 * Checks the extension AND the real file contents, then saves under a random
 * name so nobody can upload a .php file or overwrite another file.
 * Returns the saved file name, or null with $error filled in.
 */
function cv_store_upload($tmpPath, $originalName, array $allowed, $targetDir, &$error = null)
{
    $ext = strtolower(pathinfo((string) $originalName, PATHINFO_EXTENSION));
    if (!isset($allowed[$ext])) {
        $error = 'File type not allowed. Use: ' . implode(', ', array_keys($allowed)) . '.';
        return null;
    }
    if (!is_uploaded_file($tmpPath)) {
        $error = 'Upload failed. Try again.';
        return null;
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmpPath);
    if (!in_array($mime, $allowed[$ext], true)) {
        $error = 'The file does not look like a real .' . $ext . ' file.';
        return null;
    }
    $name = bin2hex(random_bytes(12)) . '.' . $ext;
    if (!move_uploaded_file($tmpPath, rtrim($targetDir, '/') . '/' . $name)) {
        $error = 'Could not save the file.';
        return null;
    }
    return $name;
}

/* ---------- Mail ---------- */
function cv_mailer()
{
    require_once __DIR__ . '/../LANDING-PAGE/PHPMailer/src/Exception.php';
    require_once __DIR__ . '/../LANDING-PAGE/PHPMailer/src/PHPMailer.php';
    require_once __DIR__ . '/../LANDING-PAGE/PHPMailer/src/SMTP.php';

    $user = cv_config('mail_username');
    $pass = cv_config('mail_password');
    if (!$user || !$pass) {
        throw new RuntimeException('Email is not set up yet. Add mail_username and mail_password in includes/config.php.');
    }

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $user;
    $mail->Password   = $pass;
    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;
    $mail->CharSet    = 'UTF-8';
    $mail->setFrom($user, cv_config('mail_from_name', 'CineVault'));
    return $mail;
}

// Sends one HTML email. Returns null when sent, or a short error message.
function cv_send_mail($to, $subject, $htmlBody)
{
    try {
        $mail = cv_mailer();
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;
        $mail->send();
        return null;
    } catch (Throwable $ex) {
        error_log('CineVault mail error: ' . $ex->getMessage());
        return $ex instanceof RuntimeException ? $ex->getMessage() : 'The email could not be sent.';
    }
}

// Shows a browser alert, then goes to $url. Safe for any message text.
/* ---------- Flash messages (Bootstrap alerts) ----------
 * cv_flash() stores a one-time message; the next HTML page shows it as a
 * Bootstrap alert at the top of the screen (added by the output filter above).
 * Types: success, info, warning, danger. */
function cv_flash($message, $type = 'success')
{
    $_SESSION['cv_flash'] = ['message' => (string) $message, 'type' => $type];
}

// Shows $message on the page at $url as a Bootstrap alert (was a browser alert()).
function cv_alert_redirect($message, $url, $type = 'success')
{
    while (ob_get_level() > 0) {
        ob_end_clean(); // drop anything already printed; this response is only a redirect
    }
    // Set the message only after clearing: clearing runs the output filter, which
    // would otherwise "show" (use up) the message on the half-printed page.
    cv_flash($message, $type);
    header('Location: ' . $url);
    exit;
}

function cv_flash_html(array $flash)
{
    $type  = in_array($flash['type'], ['success', 'info', 'warning', 'danger'], true) ? $flash['type'] : 'info';
    $icons = ['success' => 'fa-circle-check', 'info' => 'fa-circle-info', 'warning' => 'fa-triangle-exclamation', 'danger' => 'fa-circle-exclamation'];
    return '
<div class="cv-flash-wrap" style="position:fixed;top:76px;left:50%;transform:translateX(-50%);z-index:2000;width:min(520px,calc(100vw - 24px));">
  <div class="alert alert-' . $type . ' alert-dismissible fade show shadow d-flex align-items-center gap-2 mb-0" role="alert" style="border-radius:12px;">
    <i class="fa-solid ' . $icons[$type] . '"></i>
    <span>' . e($flash['message']) . '</span>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
</div>
<script>
  setTimeout(function () {
    var a = document.querySelector(".cv-flash-wrap .alert");
    if (!a) return;
    if (window.bootstrap && bootstrap.Alert) { bootstrap.Alert.getOrCreateInstance(a).close(); }
    else { a.parentNode.remove(); }
  }, 4500);
</script>';
}

/* ---------- Remember me (members only) ----------
 * The cookie holds "selector:validator". The selector finds the row; the
 * validator is checked against a SHA-256 hash, so the database alone can't
 * be used to sign in. Each use swaps in a new validator, and the login
 * still ends 30 days after the member ticked "Remember me".
 */
const CV_REMEMBER_COOKIE = 'cv_remember';
const CV_REMEMBER_DAYS = 30;

function cv_set_remember_cookie($value, $expires)
{
    setcookie(CV_REMEMBER_COOKIE, $value, [
        'expires'  => $expires,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ]);
}

function cv_issue_remember_token($con, $signupId, $expiresAt = null)
{
    $selector  = bin2hex(random_bytes(12));
    $validator = bin2hex(random_bytes(32));
    $hash      = hash('sha256', $validator);
    $expiresAt = $expiresAt ?: date('Y-m-d H:i:s', time() + CV_REMEMBER_DAYS * 86400);

    $stmt = $con->prepare('INSERT INTO tbl_remember_tokens (signup_id, selector, validator_hash, expires_at) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('isss', $signupId, $selector, $hash, $expiresAt);
    $stmt->execute();
    $stmt->close();

    cv_set_remember_cookie($selector . ':' . $validator, strtotime($expiresAt));
    $_COOKIE[CV_REMEMBER_COOKIE] = $selector . ':' . $validator; // so a sign out on this same request finds it
}

// Removes this browser's saved login (used on sign out).
function cv_forget_me($con)
{
    $cookie = $_COOKIE[CV_REMEMBER_COOKIE] ?? '';
    if (is_string($cookie) && strpos($cookie, ':') !== false) {
        $selector = explode(':', $cookie, 2)[0];
        $stmt = $con->prepare('DELETE FROM tbl_remember_tokens WHERE selector = ?');
        $stmt->bind_param('s', $selector);
        $stmt->execute();
        $stmt->close();
    }
    cv_set_remember_cookie('', time() - 3600);
    unset($_COOKIE[CV_REMEMBER_COOKIE]);
}

// Removes every saved login for a member (used after a password reset).
function cv_forget_all_devices($con, $signupId)
{
    $stmt = $con->prepare('DELETE FROM tbl_remember_tokens WHERE signup_id = ?');
    $stmt->bind_param('i', $signupId);
    $stmt->execute();
    $stmt->close();
}

// Signs a member back in from a valid Remember me cookie.
function cv_try_remember_login($con)
{
    if (!empty($_SESSION['is_logged_in'])) {
        return;
    }
    $cookie = $_COOKIE[CV_REMEMBER_COOKIE] ?? '';
    if (!is_string($cookie) || !preg_match('/^([a-f0-9]{24}):([a-f0-9]{64})$/', $cookie, $m)) {
        if ($cookie !== '') {
            cv_set_remember_cookie('', time() - 3600);
        }
        return;
    }
    [, $selector, $validator] = $m;

    $stmt = $con->prepare(
        'SELECT t.token_id, t.signup_id, t.validator_hash, t.expires_at, s.signup_email, s.is_archived
         FROM tbl_remember_tokens t
         JOIN tbl_signup_acc s ON s.signup_id = t.signup_id
         WHERE t.selector = ? LIMIT 1'
    );
    $stmt->bind_param('s', $selector);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $valid = $row
        && hash_equals($row['validator_hash'], hash('sha256', $validator))
        && strtotime($row['expires_at']) > time()
        && (int) $row['is_archived'] === 0;

    // Old or used tokens are removed either way.
    $con->query('DELETE FROM tbl_remember_tokens WHERE expires_at < NOW()');
    if ($row) {
        $stmt = $con->prepare('DELETE FROM tbl_remember_tokens WHERE token_id = ?');
        $stmt->bind_param('i', $row['token_id']);
        $stmt->execute();
        $stmt->close();
    }

    if (!$valid) {
        cv_set_remember_cookie('', time() - 3600);
        return;
    }

    cv_login_session([
        'user_id' => (int) $row['signup_id'],
        'user_email' => $row['signup_email'],
        'is_logged_in' => true,
    ]);
    cv_issue_remember_token($con, (int) $row['signup_id'], $row['expires_at']);
}

/* ---------- Apply the guards for this page ---------- */
cv_try_remember_login($con);
$cvPage = cv_relative_script();
if (
    (strpos($cvPage, 'DASHBOARD-HTML/') === 0 || strpos($cvPage, 'libs/') === 0)
    && $cvPage !== 'DASHBOARD-HTML/admin-signin.php'
) {
    cv_require_admin($con);
}
if (strpos($cvPage, 'MovieWebsite/') === 0) {
    cv_require_member($con);
}
unset($cvPage);
