<?php
// Member Settings: password, playback preferences, watch history and devices.
include '../includes/db-connection.php';

$memberId = (int) ($_SESSION['user_id'] ?? 0);
$errors = [];
$section = $_POST['section'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $memberId) {
    if ($section === 'password') {
        $current = (string) ($_POST['current_password'] ?? '');
        $new = (string) ($_POST['new_password'] ?? '');
        $confirm = (string) ($_POST['confirm_password'] ?? '');
        $stmt = $con->prepare('SELECT * FROM tbl_signup_acc WHERE signup_id = ?');
        $stmt->bind_param('i', $memberId);
        $stmt->execute();
        $me = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $who = 'member:' . $memberId;
        if (cv_too_many_attempts($con, 'password-change', $who)) {
            $errors['current_password'] = 'Too many wrong tries. Wait 15 minutes and try again.';
        } elseif (!$me || !cv_check_password($con, 'tbl_signup_acc', 'signup_id', 'signup_password', $me, $current)) {
            cv_record_failure($con, 'password-change', $who);
            $errors['current_password'] = 'That is not your current password.';
        }
        if (strlen($new) < 8) {
            $errors['new_password'] = 'Use at least 8 characters.';
        } elseif ($new === $current) {
            $errors['new_password'] = 'Pick a password different from your current one.';
        }
        if ($new !== $confirm) {
            $errors['confirm_password'] = 'The two new passwords don\'t match.';
        }
        if (!$errors) {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $stmt = $con->prepare('UPDATE tbl_signup_acc SET signup_password = ? WHERE signup_id = ?');
            $stmt->bind_param('si', $hash, $memberId);
            $stmt->execute();
            $stmt->close();
            cv_clear_failures($con, 'password-change', $who);
            // A new password signs out every "Remember me" device.
            cv_forget_all_devices($con, $memberId);
            session_regenerate_id(true);
            cv_alert_redirect('Your password was changed. Devices where you chose "Remember me" will need to sign in again.', 'settings.php');
        }
    }

    if ($section === 'playback') {
        $autoplay = !empty($_POST['autoplay_previews']) ? 1 : 0;
        $resume = !empty($_POST['resume_playback']) ? 1 : 0;
        $stmt = $con->prepare('INSERT INTO tbl_member_settings (signup_id, autoplay_previews, resume_playback) VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE autoplay_previews = VALUES(autoplay_previews), resume_playback = VALUES(resume_playback)');
        $stmt->bind_param('iii', $memberId, $autoplay, $resume);
        $stmt->execute();
        $stmt->close();
        cv_alert_redirect('Playback settings saved.', 'settings.php');
    }

    if ($section === 'history') {
        foreach (['tbl_watch_progress', 'tbl_episode_progress'] as $table) {
            $stmt = $con->prepare("DELETE FROM $table WHERE signup_id = ?");
            $stmt->bind_param('i', $memberId);
            $stmt->execute();
            $stmt->close();
        }
        cv_alert_redirect('Your watch history was cleared. Continue Watching is empty now.', 'settings.php');
    }

    if ($section === 'devices') {
        cv_forget_all_devices($con, $memberId);
        cv_alert_redirect('Signed out of every device where you chose "Remember me". You are still signed in here.', 'settings.php');
    }
}

$prefs = cv_member_prefs($memberId);
$stmt = $con->prepare('SELECT COUNT(*) FROM tbl_watch_progress WHERE signup_id = ?');
$stmt->bind_param('i', $memberId);
$stmt->execute();
$inProgress = (int) $stmt->get_result()->fetch_column();
$stmt->close();
$stmt = $con->prepare('SELECT COUNT(*) FROM tbl_remember_tokens WHERE signup_id = ? AND expires_at > NOW()');
$stmt->bind_param('i', $memberId);
$stmt->execute();
$devices = (int) $stmt->get_result()->fetch_column();
$stmt->close();
$err = fn($key) => isset($errors[$key]) ? '<div class="invalid-feedback d-block">' . e($errors[$key]) . '</div>' : '';

include 'CineVault-header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Settings | CineVault</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="FirstProject.css?v=<?php echo CV_ASSET_VERSION; ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="icon" href="../MOVIE-IMG/HEADER-IMG/CINEVAULT-LOGO.svg?v=<?php echo CV_ASSET_VERSION; ?>">
  <style>
    .cv-settings { max-width: 980px; margin: 0 auto; padding: 96px 16px 48px; }
    .cv-settings h1 { font-size: 30px; margin-bottom: 4px; }
    .cv-card { background: #1F1A14; border: 1px solid #3D3326; border-radius: 12px; padding: 20px; height: 100%; }
    .cv-card h2 { font-size: 20px; margin: 0 0 4px; }
    .cv-help { color: #B3A68C; font-size: 14px; margin-bottom: 14px; }
    .cv-card label { font-size: 14px; margin-bottom: 4px; }
    .form-switch .form-check-input { width: 2.6em; height: 1.4em; cursor: pointer; }
    .form-switch .form-check-label { padding-left: 6px; cursor: pointer; }
  </style>
</head>

<body>
  <main class="cv-settings">
    <h1>Settings</h1>
    <p class="cv-help">Your password, how videos play, and your watch history. To change your name, mobile number or email, go to <a href="user-view-profile.php">My Profile</a>.</p>

    <div class="row g-3">
      <div class="col-lg-6">
        <form class="cv-card" method="post" id="password" novalidate>
          <input type="hidden" name="section" value="password">
          <h2>Change your password</h2>
          <p class="cv-help">At least 8 characters.</p>
          <div class="mb-2"><label for="current_password">Current password</label><input class="form-control" type="password" id="current_password" name="current_password" autocomplete="current-password" required><?php echo $err('current_password'); ?></div>
          <div class="mb-2"><label for="new_password">New password</label><input class="form-control" type="password" id="new_password" name="new_password" autocomplete="new-password" minlength="8" required><?php echo $err('new_password'); ?></div>
          <div class="mb-3"><label for="confirm_password">Confirm new password</label><input class="form-control" type="password" id="confirm_password" name="confirm_password" autocomplete="new-password" minlength="8" required><?php echo $err('confirm_password'); ?></div>
          <div class="text-end"><button class="btn db-bg-primary" type="submit">Change password</button></div>
        </form>
      </div>

      <div class="col-lg-6 d-flex flex-column gap-3">
        <form class="cv-card" method="post" id="playback">
          <input type="hidden" name="section" value="playback">
          <h2>Playback</h2>
          <p class="cv-help">How videos behave when you browse and watch.</p>
          <div class="form-check form-switch mb-2">
            <input class="form-check-input" type="checkbox" role="switch" id="autoplay_previews" name="autoplay_previews" value="1" <?php echo $prefs['autoplay_previews'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="autoplay_previews">Play a preview when I open a title</label>
          </div>
          <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" role="switch" id="resume_playback" name="resume_playback" value="1" <?php echo $prefs['resume_playback'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="resume_playback">Resume where I stopped</label>
          </div>
          <div class="text-end"><button class="btn db-bg-primary" type="submit">Save playback settings</button></div>
        </form>

        <div class="cv-card">
          <h2>Favorite genres</h2>
          <p class="cv-help">Your Recommended for You row is based on these.</p>
          <a class="btn btn-outline-light" href="FirstProject.php#favorite-genres">Change favorite genres</a>
        </div>
      </div>

      <div class="col-lg-6">
        <form class="cv-card" method="post" id="history" onsubmit="return confirm('Clear your whole watch history? Continue Watching will be empty.');">
          <input type="hidden" name="section" value="history">
          <h2>Watch history</h2>
          <p class="cv-help">
            <?php echo $inProgress ? 'Continue Watching has ' . $inProgress . ' ' . ($inProgress === 1 ? 'title' : 'titles') . '. Clearing it also forgets where you stopped in each one.' : 'Continue Watching is empty.'; ?>
          </p>
          <button class="btn btn-outline-light" type="submit" <?php echo $inProgress ? '' : 'disabled'; ?>>Clear watch history</button>
        </form>
      </div>

      <div class="col-lg-6">
        <form class="cv-card" method="post" id="devices">
          <input type="hidden" name="section" value="devices">
          <h2>Signed-in devices</h2>
          <p class="cv-help">
            <?php echo $devices ? 'You chose "Remember me" on ' . $devices . ' ' . ($devices === 1 ? 'device' : 'devices') . '. Sign them out if you lost one or used a shared computer.' : 'No devices are remembered right now.'; ?>
          </p>
          <button class="btn btn-outline-light" type="submit" <?php echo $devices ? '' : 'disabled'; ?>>Sign out remembered devices</button>
        </form>
      </div>
    </div>
  </main>
  <footer class="mt-4">
    <div class="footer text-white d-flex justify-content-between mx-5 align-items-center">
      <p class="footer-long-text">Movie and series details and images come from <a class="text-white" href="https://www.themoviedb.org/" target="_blank" rel="noopener">TMDB</a>. This product uses the TMDB API but is not endorsed or certified by TMDB. Free classic films stream from the <a class="text-white" href="https://archive.org/" target="_blank" rel="noopener">Internet Archive</a>.</p>
      <p>© <?php echo date('Y'); ?> <?php echo e(cv_setting('site_name', 'CineVault')); ?>. All rights reserved.</p>
    </div>
  </footer>
</body>

</html>
