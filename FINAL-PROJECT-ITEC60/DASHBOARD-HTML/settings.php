<?php
// Admin Settings: site information (shown on printed reports), the titles
// featured in the carousels and banners, and the admin's password.
include '../includes/db-connection.php';
require_once '../includes/tmdb.php';

$adminId = (int) ($_SESSION['admin_id'] ?? 0);

// Featured spots: what each one is and which titles can go there.
$slots = [
    'home-1' => ['Home carousel', 1, null], 'home-2' => ['Home carousel', 2, null], 'home-3' => ['Home carousel', 3, null],
    'banner1' => ['Home banner', 1, null], 'banner2' => ['Home banner', 2, null],
    'movies-1' => ['Movies carousel', 1, 'Movie'], 'movies-2' => ['Movies carousel', 2, 'Movie'], 'movies-3' => ['Movies carousel', 3, 'Movie'],
    'banner3' => ['Movies banner', 1, 'Movie'], 'banner4' => ['Movies banner', 2, 'Movie'],
    'series-1' => ['Series carousel', 1, 'Series'], 'series-2' => ['Series carousel', 2, 'Series'], 'series-3' => ['Series carousel', 3, 'Series'],
    'banner5' => ['Series banner', 1, 'Series'], 'banner6' => ['Series banner', 2, 'Series'],
];

$errors = [];
$section = $_POST['section'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($section === 'site') {
        $fields = [
            'site_name' => [trim($_POST['site_name'] ?? ''), 60],
            'site_tagline' => [trim($_POST['site_tagline'] ?? ''), 120],
            'contact_email' => [trim($_POST['contact_email'] ?? ''), 120],
            'contact_phone' => [trim($_POST['contact_phone'] ?? ''), 40],
            'contact_address' => [trim($_POST['contact_address'] ?? ''), 160],
        ];
        if ($fields['site_name'][0] === '') {
            $errors['site_name'] = 'Enter the site name.';
        }
        if ($fields['contact_email'][0] !== '' && !filter_var($fields['contact_email'][0], FILTER_VALIDATE_EMAIL)) {
            $errors['contact_email'] = 'Enter a valid email address, or leave it blank.';
        }
        foreach ($fields as $key => [$value, $max]) {
            if (mb_strlen($value) > $max) {
                $errors[$key] = "Keep this under $max characters.";
            }
        }
        if (!$errors) {
            foreach ($fields as $key => [$value]) {
                cv_setting_save($key, $value);
            }
            cv_alert_redirect('Site information saved.', 'settings.php');
        }
    }

    if ($section === 'featured') {
        $picked = [];
        foreach ($slots as $slot => $info) {
            $id = (int) ($_POST['slot'][$slot] ?? 0);
            if ($id) {
                if (in_array($id, $picked, true)) {
                    $errors['featured'] = 'Each title can only be featured in one spot. Pick a different title for the repeated one.';
                    break;
                }
                $picked[$slot] = $id;
            }
        }
        if (!$errors) {
            $con->begin_transaction();
            mysqli_query($con, 'UPDATE tbl_movie_series SET feature_slot = NULL WHERE feature_slot IS NOT NULL');
            $stmt = $con->prepare('UPDATE tbl_movie_series SET feature_slot = ? WHERE movie_series_id = ? AND backdrop IS NOT NULL');
            foreach ($picked as $slot => $id) {
                $stmt->bind_param('si', $slot, $id);
                $stmt->execute();
            }
            $stmt->close();
            $con->commit();
            cv_alert_redirect('Featured titles saved. The carousels and banners now show them.', 'settings.php#featured');
        }
    }

    if ($section === 'password') {
        $current = (string) ($_POST['current_password'] ?? '');
        $new = (string) ($_POST['new_password'] ?? '');
        $confirm = (string) ($_POST['confirm_password'] ?? '');
        $stmt = $con->prepare('SELECT * FROM tbl_admin_acc WHERE admin_id = ?');
        $stmt->bind_param('i', $adminId);
        $stmt->execute();
        $me = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $who = 'admin:' . $adminId;
        if (cv_too_many_attempts($con, 'password-change', $who)) {
            $errors['current_password'] = 'Too many wrong tries. Wait 15 minutes and try again.';
        } elseif (!$me || !cv_check_password($con, 'tbl_admin_acc', 'admin_id', 'password', $me, $current)) {
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
            $stmt = $con->prepare('UPDATE tbl_admin_acc SET password = ? WHERE admin_id = ?');
            $stmt->bind_param('si', $hash, $adminId);
            $stmt->execute();
            $stmt->close();
            cv_clear_failures($con, 'password-change', $who);
            session_regenerate_id(true);
            cv_alert_redirect('Your password was changed.', 'settings.php#password');
        }
    }
}

include '../includes/dashboard-header-sidebar.php';

// Titles that can be featured: they need a wide backdrop image.
$eligible = mysqli_fetch_all(mysqli_query($con, "SELECT movie_series_id, title, category, date_released, feature_slot
    FROM tbl_movie_series WHERE backdrop IS NOT NULL ORDER BY title"), MYSQLI_ASSOC);
$current = [];
foreach ($eligible as $t) {
    if ($t['feature_slot']) {
        $current[$t['feature_slot']] = (int) $t['movie_series_id'];
    }
}
$old = fn($key, $default = '') => e($section === 'site' && isset($_POST[$key]) ? $_POST[$key] : cv_setting($key, $default));
$err = fn($key) => isset($errors[$key]) ? '<div class="invalid-feedback d-block">' . e($errors[$key]) . '</div>' : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Settings | CineVault Admin</title>
  <link rel="stylesheet" href="../DASHBOARD-CSS/dashboard.css?v=<?php echo CV_ASSET_VERSION; ?>">
  <style>
    .cv-settings h2 { font-size: 20px; margin: 0 0 4px; color: #F1E8D4; }
    .cv-settings .cv-help { color: #B3A68C; font-size: 14px; margin-bottom: 14px; }
    .cv-settings .card { background: #1F1A14 !important; border: 1px solid #3D3326; border-radius: 12px; }
    .cv-settings label { color: #F1E8D4; font-size: 14px; margin-bottom: 4px; }
    .cv-slot-group { font-size: 13px; text-transform: uppercase; letter-spacing: .06em; color: #DDB963; margin: 14px 0 6px; }
    .cv-status { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 999px; font-size: 13px; }
    .cv-status.ok { background: rgba(80, 160, 90, .18); color: #9fd8a8; }
    .cv-status.off { background: rgba(201, 162, 75, .18); color: #DDB963; }
  </style>
</head>

<body>
  <main class="container-lg p-3 cv-settings">
    <h1 class="db-text-sec fs-3 mb-1">Settings</h1>
    <p class="cv-help">Site information, featured titles and your password.</p>

    <div class="row g-3">
      <!-- SITE INFORMATION -->
      <div class="col-lg-6">
        <form class="card p-3 h-100" method="post" id="site" novalidate>
          <input type="hidden" name="section" value="site">
          <h2>Site information</h2>
          <p class="cv-help">Shown on the letterhead of every printed report. Leave a contact field blank to leave it off.</p>
          <div class="mb-2"><label for="site_name">Site name</label><input class="form-control" id="site_name" name="site_name" maxlength="60" value="<?php echo $old('site_name', 'CineVault'); ?>" required><?php echo $err('site_name'); ?></div>
          <div class="mb-2"><label for="site_tagline">Tagline</label><input class="form-control" id="site_tagline" name="site_tagline" maxlength="120" value="<?php echo $old('site_tagline'); ?>"><?php echo $err('site_tagline'); ?></div>
          <div class="mb-2"><label for="contact_email">Contact email</label><input class="form-control" type="email" id="contact_email" name="contact_email" maxlength="120" value="<?php echo $old('contact_email'); ?>"><?php echo $err('contact_email'); ?></div>
          <div class="mb-2"><label for="contact_phone">Contact phone</label><input class="form-control" id="contact_phone" name="contact_phone" maxlength="40" value="<?php echo $old('contact_phone'); ?>"><?php echo $err('contact_phone'); ?></div>
          <div class="mb-3"><label for="contact_address">Address</label><input class="form-control" id="contact_address" name="contact_address" maxlength="160" value="<?php echo $old('contact_address'); ?>"><?php echo $err('contact_address'); ?></div>
          <div class="mt-auto text-end"><button class="btn db-bg-primary" type="submit">Save site information</button></div>
        </form>
      </div>

      <!-- PASSWORD + TMDB -->
      <div class="col-lg-6 d-flex flex-column gap-3">
        <form class="card p-3" method="post" id="password" novalidate>
          <input type="hidden" name="section" value="password">
          <h2>Change your password</h2>
          <p class="cv-help">At least 8 characters.</p>
          <div class="mb-2"><label for="current_password">Current password</label><input class="form-control" type="password" id="current_password" name="current_password" autocomplete="current-password" required><?php echo $err('current_password'); ?></div>
          <div class="mb-2"><label for="new_password">New password</label><input class="form-control" type="password" id="new_password" name="new_password" autocomplete="new-password" minlength="8" required><?php echo $err('new_password'); ?></div>
          <div class="mb-3"><label for="confirm_password">Confirm new password</label><input class="form-control" type="password" id="confirm_password" name="confirm_password" autocomplete="new-password" minlength="8" required><?php echo $err('confirm_password'); ?></div>
          <div class="text-end"><button class="btn db-bg-primary" type="submit">Change password</button></div>
        </form>

        <div class="card p-3">
          <h2>TMDB connection</h2>
          <p class="cv-help mb-2">Used by Search TMDB on the add form and by the library import.</p>
          <?php if (tmdb_configured()): ?>
            <span class="cv-status ok"><i class="fa-solid fa-circle-check"></i> Connected</span>
          <?php else: ?>
            <span class="cv-status off"><i class="fa-solid fa-circle-exclamation"></i> Not set up. Add the TMDB API key to includes/config.php.</span>
          <?php endif; ?>
          <p class="cv-help mt-2 mb-0">The key stays in includes/config.php on the server, so it never shows on a page.</p>
        </div>
      </div>

      <!-- FEATURED TITLES -->
      <div class="col-12">
        <form class="card p-3" method="post" id="featured">
          <input type="hidden" name="section" value="featured">
          <h2>Featured titles</h2>
          <p class="cv-help">What the big carousels and banners show on the Home, Movies and Series pages. Only titles with a wide backdrop image can be featured, and each title in one spot at a time.</p>
          <?php if (isset($errors['featured'])): ?><div class="alert alert-warning py-2"><?php echo e($errors['featured']); ?></div><?php endif; ?>
          <div class="row g-3">
            <?php
            $groups = [];
            foreach ($slots as $slot => [$group, $n, $cat]) {
                $groups[$group][] = [$slot, $n, $cat];
            }
            foreach ($groups as $group => $items): ?>
              <div class="col-md-6 col-xl-4">
                <p class="cv-slot-group"><?php echo e($group); ?></p>
                <?php foreach ($items as [$slot, $n, $cat]):
                  $sel = (int) ($_POST['slot'][$slot] ?? $current[$slot] ?? 0); ?>
                  <div class="mb-2">
                    <label for="slot-<?php echo $slot; ?>"><?php echo e($group); ?> <?php echo $n; ?></label>
                    <select class="form-select" id="slot-<?php echo $slot; ?>" name="slot[<?php echo $slot; ?>]">
                      <option value="0">(empty)</option>
                      <?php foreach ($eligible as $t): if ($cat && $t['category'] !== $cat) continue; ?>
                        <option value="<?php echo (int) $t['movie_series_id']; ?>" <?php echo $sel === (int) $t['movie_series_id'] ? 'selected' : ''; ?>>
                          <?php echo e($t['title'] . ($t['date_released'] ? ' (' . $t['date_released'] . ')' : '') . ($cat ? '' : ', ' . $t['category'])); ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endforeach; ?>
          </div>
          <div class="text-end mt-2"><button class="btn db-bg-primary" type="submit">Save featured titles</button></div>
        </form>
      </div>
    </div>
  </main>
</body>

</html>
