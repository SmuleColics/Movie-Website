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
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

/* ---------- Small helpers ---------- */
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
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
    return preg_replace(
        '/<head\b[^>]*>/i',
        '$0<meta name="csrf-token" content="' . e(csrf_token()) . '">',
        $html,
        1
    );
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
        $stmt = $con->prepare('SELECT admin_id FROM tbl_admin_acc WHERE admin_id = ? LIMIT 1');
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
        header('Location: ../DASHBOARD-HTML/admin-signin.php');
        exit;
    }
}

function cv_require_member($con)
{
    $ok = false;
    if (!empty($_SESSION['is_logged_in']) && !empty($_SESSION['user_id'])) {
        $stmt = $con->prepare(
            "SELECT s.signup_id FROM tbl_signup_acc s
             JOIN tbl_payment p ON p.signup_id = s.signup_id AND p.status = 'approved'
             WHERE s.signup_id = ? AND s.is_archived = 0 LIMIT 1"
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
    return basename($_SERVER['SCRIPT_NAME'] ?? '') === 'search.php'
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
function cv_alert_redirect($message, $url)
{
    echo '<script>alert(' . json_encode($message) . '); window.location.href = ' . json_encode($url) . ';</script>';
    exit;
}

/* ---------- Apply the guards for this page ---------- */
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
