<?php
// My Profile: name, mobile number and email, plus a short activity summary.
// Same look as Settings (settings.php).
include '../includes/db-connection.php';

$user_id = (int) ($_SESSION['user_id'] ?? 0);
$errors = [];

$stmt = $con->prepare('SELECT * FROM tbl_signup_acc WHERE signup_id = ? LIMIT 1');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc() ?: [];
$stmt->close();

$form = [
    'first_name'    => $user['first_name'] ?? '',
    'last_name'     => $user['last_name'] ?? '',
    'mobile_number' => $user['mobile_number'] ?? '',
    'signup_email'  => $user['signup_email'] ?? '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update-profile']) && $user) {
    $form = [
        'first_name'    => trim((string) ($_POST['first_name'] ?? '')),
        'last_name'     => trim((string) ($_POST['last_name'] ?? '')),
        'mobile_number' => trim((string) ($_POST['mobile_number'] ?? '')),
        'signup_email'  => strtolower(trim((string) ($_POST['signup_email'] ?? ''))),
    ];

    // Same rules as sign up, and the email must not belong to someone else.
    $namePattern = "/^[A-Za-z][A-Za-z .'-]{1,99}$/";
    if (!preg_match($namePattern, $form['first_name'])) {
        $errors['first_name'] = 'Use letters, spaces, dots, dashes and apostrophes (at least 2 characters).';
    }
    if (!preg_match($namePattern, $form['last_name'])) {
        $errors['last_name'] = 'Use letters, spaces, dots, dashes and apostrophes (at least 2 characters).';
    }
    if (!preg_match('/^\d{11}$/', $form['mobile_number'])) {
        $errors['mobile_number'] = 'Enter exactly 11 digits, like 09171234567.';
    }
    if (!filter_var($form['signup_email'], FILTER_VALIDATE_EMAIL)) {
        $errors['signup_email'] = 'Enter a valid email address.';
    } else {
        $stmt = $con->prepare('SELECT 1 FROM tbl_signup_acc WHERE signup_email = ? AND signup_id <> ? LIMIT 1');
        $stmt->bind_param('si', $form['signup_email'], $user_id);
        $stmt->execute();
        if ($stmt->get_result()->fetch_assoc()) {
            $errors['signup_email'] = 'That email is already used by another account.';
        }
        $stmt->close();
    }

    if (!$errors) {
        $stmt = $con->prepare('UPDATE tbl_signup_acc SET first_name = ?, last_name = ?, mobile_number = ?, signup_email = ? WHERE signup_id = ?');
        $stmt->bind_param('ssssi', $form['first_name'], $form['last_name'], $form['mobile_number'], $form['signup_email'], $user_id);
        $stmt->execute();
        $stmt->close();
        cv_alert_redirect('Your profile was updated.', 'user-view-profile.php');
    }
}

// Shown on the profile card: the saved values, not what was just typed.
$fullname = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
$initials = strtoupper(substr($user['first_name'] ?? '', 0, 1) . substr($user['last_name'] ?? '', 0, 1)) ?: '?';
$joined = !empty($user['date_created']) ? date('F Y', strtotime($user['date_created'])) : '';

// Activity: titles in Continue Watching, and favorite genres.
$stmt = $con->prepare('SELECT COUNT(*) FROM tbl_watch_progress w JOIN tbl_movie_series m ON m.movie_series_id = w.movie_series_id WHERE w.signup_id = ?');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$inProgress = (int) $stmt->get_result()->fetch_column();
$stmt->close();

$favoriteNames = [];
$favoriteIds = array_values(array_filter(array_map('intval', explode(',', (string) ($user['favorite_genres'] ?? '')))));
if ($favoriteIds) {
    $list = implode(',', $favoriteIds);
    $byId = [];
    foreach (mysqli_fetch_all(mysqli_query($con, "SELECT genre_id, genre_name FROM tbl_movie_series_genre WHERE genre_id IN ($list)"), MYSQLI_ASSOC) as $g) {
        $byId[(int) $g['genre_id']] = $g['genre_name'];
    }
    foreach ($favoriteIds as $id) {
        if (isset($byId[$id])) {
            $favoriteNames[] = $byId[$id];
        }
    }
}

$err = fn($key) => isset($errors[$key]) ? '<div class="invalid-feedback d-block">' . e($errors[$key]) . '</div>' : '';
$bad = fn($key) => isset($errors[$key]) ? ' is-invalid' : '';

include 'CineVault-header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile | CineVault</title>
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

    .cv-profile { text-align: center; }
    .cv-avatar {
      width: 96px; height: 96px; margin: 4px auto 14px; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      background: radial-gradient(circle at 30% 30%, #DDB963, #A9853A);
      color: #14110D; font-family: 'Playfair Display', Georgia, serif; font-size: 36px; font-weight: 700;
      box-shadow: 0 0 0 4px #1F1A14, 0 0 0 5px #C9A24B;
    }
    .cv-profile .cv-name { font-size: 22px; margin: 0; overflow-wrap: anywhere; }
    .cv-role { display: inline-block; margin: 6px 0 16px; padding: 2px 12px; border: 1px solid #C9A24B; border-radius: 999px; color: #DDB963; font-size: 13px; }
    .cv-facts { list-style: none; padding: 0; margin: 0 0 16px; text-align: left; }
    .cv-facts li { display: flex; gap: 10px; align-items: flex-start; padding: 8px 0; border-top: 1px solid #3D3326; font-size: 14px; overflow-wrap: anywhere; }
    .cv-facts i { color: #C9A24B; width: 16px; margin-top: 3px; text-align: center; flex-shrink: 0; }
    .cv-facts span { color: #B3A68C; display: block; font-size: 12px; }
    .cv-profile .btn i { font-size: 14px; }

    .cv-stats { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
    .cv-stat { background: #2A231A; border: 1px solid #3D3326; border-radius: 10px; padding: 12px 14px; }
    .cv-stat b { display: block; font-size: 24px; color: #DDB963; line-height: 1.2; }
    .cv-stat span { color: #B3A68C; font-size: 13px; }
    .cv-chips { display: flex; flex-wrap: wrap; gap: 6px; }
    .cv-chip { padding: 3px 12px; border-radius: 999px; background: #2A231A; border: 1px solid #3D3326; font-size: 13px; }
  </style>
</head>

<body>
  <main class="cv-settings">
    <h1>My Profile</h1>
    <p class="cv-help">Your name and contact details. To change your password or how videos play, go to <a href="settings.php">Settings</a>.</p>

    <div class="row g-3">
      <div class="col-lg-4">
        <div class="cv-card cv-profile">
          <div class="cv-avatar" aria-hidden="true"><?php echo e($initials); ?></div>
          <p class="cv-name"><?php echo e($fullname ?: 'Member'); ?></p>
          <span class="cv-role">Member</span>
          <ul class="cv-facts">
            <li><i class="fa-solid fa-envelope"></i><div><span>Email</span><?php echo e($user['signup_email'] ?? ''); ?></div></li>
            <li><i class="fa-solid fa-phone"></i><div><span>Mobile number</span><?php echo e($user['mobile_number'] ?? ''); ?></div></li>
            <?php if ($joined): ?>
              <li><i class="fa-solid fa-calendar"></i><div><span>Member since</span><?php echo e($joined); ?></div></li>
            <?php endif; ?>
          </ul>
          <a class="btn btn-outline-light w-100" href="settings.php"><i class="fa-solid fa-gear me-1"></i> Settings</a>
        </div>
      </div>

      <div class="col-lg-8 d-flex flex-column gap-3">
        <form class="cv-card" method="post" id="details" novalidate>
          <h2>Personal information</h2>
          <p class="cv-help">Use an email you can open: password reset codes are sent there.</p>
          <div class="row g-2">
            <div class="col-sm-6"><label for="first_name">First name</label><input class="form-control<?php echo $bad('first_name'); ?>" id="first_name" name="first_name" value="<?php echo e($form['first_name']); ?>" autocomplete="given-name" maxlength="100" required><?php echo $err('first_name'); ?></div>
            <div class="col-sm-6"><label for="last_name">Last name</label><input class="form-control<?php echo $bad('last_name'); ?>" id="last_name" name="last_name" value="<?php echo e($form['last_name']); ?>" autocomplete="family-name" maxlength="100" required><?php echo $err('last_name'); ?></div>
            <div class="col-sm-6"><label for="mobile_number">Mobile number</label><input class="form-control<?php echo $bad('mobile_number'); ?>" id="mobile_number" name="mobile_number" value="<?php echo e($form['mobile_number']); ?>" inputmode="numeric" pattern="\d{11}" maxlength="11" autocomplete="tel" required><?php echo $err('mobile_number'); ?></div>
            <div class="col-sm-6"><label for="signup_email">Email</label><input class="form-control<?php echo $bad('signup_email'); ?>" type="email" id="signup_email" name="signup_email" value="<?php echo e($form['signup_email']); ?>" autocomplete="email" maxlength="100" required><?php echo $err('signup_email'); ?></div>
          </div>
          <div class="d-flex justify-content-end gap-2 mt-3">
            <a class="btn btn-outline-light" href="user-view-profile.php">Undo changes</a>
            <button class="btn db-bg-primary" type="submit" name="update-profile">Save changes</button>
          </div>
        </form>

        <div class="cv-card">
          <h2>Your activity</h2>
          <p class="cv-help">What you're watching and the genres your Recommended for You row is based on.</p>
          <div class="cv-stats mb-3">
            <div class="cv-stat"><b><?php echo $inProgress; ?></b><span><?php echo $inProgress === 1 ? 'title' : 'titles'; ?> in Continue Watching</span></div>
            <div class="cv-stat"><b><?php echo count($favoriteNames); ?></b><span>favorite <?php echo count($favoriteNames) === 1 ? 'genre' : 'genres'; ?></span></div>
          </div>
          <?php if ($favoriteNames): ?>
            <div class="cv-chips mb-3">
              <?php foreach ($favoriteNames as $name): ?><span class="cv-chip"><?php echo e($name); ?></span><?php endforeach; ?>
            </div>
          <?php endif; ?>
          <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-outline-light" href="FirstProject.php#favorite-genres"><?php echo $favoriteNames ? 'Change favorite genres' : 'Pick favorite genres'; ?></a>
            <?php if ($inProgress): ?><a class="btn btn-outline-light" href="FirstProject.php">Continue watching</a><?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </main>
  <footer class="mt-4">
    <div class="footer text-white d-flex justify-content-between mx-5 align-items-center">
      <p class="footer-long-text">Movie and series details and images come from <a class="text-white" href="https://www.themoviedb.org/" target="_blank" rel="noopener">TMDB</a>. This product uses the TMDB API but is not endorsed or certified by TMDB. Free classic films stream from the <a class="text-white" href="https://archive.org/" target="_blank" rel="noopener">Internet Archive</a>.</p>
      <p>© <?php echo date('Y'); ?> <?php echo e(cv_setting('site_name', 'CineVault')); ?>. All rights reserved.</p>
    </div>
  </footer>
  <script src="header-scroll.js"></script>
</body>

</html>
