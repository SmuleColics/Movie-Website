<?php
include 'db-con.php';

// One sign-in page for everyone. Already signed in? Go straight to the right place.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: ../../DASHBOARD-HTML/dashboard.php');
    exit;
  }
  if (!empty($_SESSION['is_logged_in'])) {
    header('Location: ../../MovieWebsite/FirstProject.php');
    exit;
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="SignIn.css?v=<?php echo CV_ASSET_VERSION; ?>">
  <link rel="stylesheet" href="../../DASHBOARD-CSS/for-all.css?v=<?php echo CV_ASSET_VERSION; ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="icon" href="../../MOVIE-IMG/HEADER-IMG/CINEVAULT-LOGO.svg?v=<?php echo CV_ASSET_VERSION; ?>">
</head>

<body class="bg-dark overflow-hidden">
  <header class="signin-header fixed-top d-flex align-items-center ms-5 fw-semibold">
    <div class="left-header fs-20">
      <a class="navbar-brand fw-semibold db-text-sec ms-5" href="../LandingPageMovie.php">Cine<span
          class="db-text-primary">Vault</span></a>
    </div>
  </header>

  <main>
    <section class="position-relative">
      <img class="landing-page-img" src="../ImagesLP/LandingPageClassics.jpg" alt="">

      <div class="signin-container">
        <?php
        $email_error = "";
        $pass_error = "";
        $valid_email = true;
        $valid_pass = true;
        $email = '';
        $remember = false;

        if (isset($_POST['signin-button'])) {
          $email = strtolower(trim((string) ($_POST['email'] ?? '')));
          $pass = (string) ($_POST['password'] ?? '');
          $remember = isset($_POST['remember_me']);

          // The same form signs in admins and members: the email is checked against both.
          $stmt = $con->prepare("SELECT * FROM tbl_admin_acc WHERE email = ? AND is_archived = 0 LIMIT 1"); // archived admins can't sign in
          $stmt->bind_param("s", $email);
          $stmt->execute();
          $admin = $stmt->get_result()->fetch_assoc();
          $stmt->close();

          $stmt = $con->prepare("SELECT * FROM tbl_signup_acc WHERE signup_email = ? LIMIT 1");
          $stmt->bind_param("s", $email);
          $stmt->execute();
          $row = $stmt->get_result()->fetch_assoc();
          $stmt->close();

          if (cv_too_many_attempts($con, 'signin', $email)) {
            $pass_error = "Too many failed sign-in attempts. Please wait 15 minutes and try again.";
            $valid_pass = false;
          } elseif ($admin && cv_check_password($con, 'tbl_admin_acc', 'admin_id', 'password', $admin, $pass)) {
              // Admin: straight to the dashboard. "Remember me" is for members only.
              cv_clear_failures($con, 'signin', $email);
              cv_login_session([
                'admin_id' => (int) $admin['admin_id'],
                'admin_email' => $admin['email'],
                'admin_logged_in' => true,
              ]);
              $hi = trim(explode(' ', trim($admin['first_name']))[0]);
              cv_alert_redirect('Welcome back' . ($hi !== '' ? ', ' . $hi : '') . '! You are signed in as an admin.', '../../DASHBOARD-HTML/dashboard.php');
              exit;
          } elseif (!$row || !cv_check_password($con, 'tbl_signup_acc', 'signup_id', 'signup_password', $row, $pass)) {
            // Same message whether the email or the password is wrong.
            cv_record_failure($con, 'signin', $email);
            $pass_error = "The email or password you entered is incorrect.";
            $valid_pass = false;
          } elseif ($row['is_archived'] == 1) {
              $email_error = "Your account has been archived. Please contact support.";
              $valid_email = false;
          } else {
              // CineVault is free, so any active account can sign in.
              cv_clear_failures($con, 'signin', $email);
              cv_login_session([
                'user_id' => (int) $row['signup_id'],
                'user_email' => $row['signup_email'],
                'is_logged_in' => true,
              ]);
              if ($remember) {
                cv_issue_remember_token($con, (int) $row['signup_id']);
              }
              $hi = trim(explode(' ', trim((string) $row['first_name']))[0]);
              cv_alert_redirect('Welcome back' . ($hi !== '' ? ', ' . $hi : '') . '! You are signed in.', '../../MovieWebsite/FirstProject.php');
          }
        }
        ?>
        <a href="../LandingPageMovie.php" class="cv-back-link"><i class="fa-solid fa-chevron-left"></i> Back to home</a>
        <h1 class="signin-text db-text-sec text-start mb-4">Sign In</h1>
        <form class="signin-form db-text-sec d-flex flex-column gap-3" action="" method="post">
          <div class="d-grid input-container">
            <input class="signin-inputs bg-transparent" type="text" name="email" placeholder="Email address"
              value="<?php echo e($email); ?>" autocomplete="email"
              required <?php if (!$valid_email)
                echo "style='border-color: red;'" ?>>
              <label for="email">Email address</label>
            <?php if (!$valid_email): ?>
              <p class="mb-2 mt-1 text-danger text-start" style="font-size: 14px;"><?php echo $email_error ?></p>
            <?php endif; ?>
          </div>
          <div class="d-grid input-container">
            <input class="signin-inputs bg-transparent" type="password" name="password" placeholder="Password" required
              <?php if (!$valid_pass)
                echo "style='border-color: red;'" ?>>
              <label for="password">Password</label>
            <?php if (!$valid_pass): ?>
              <p class="mb-2 mt-1 text-danger text-start" style="font-size: 14px;"><?php echo $pass_error ?></p>
            <?php endif; ?>
          </div>
          <div class="form-check text-start cv-remember">
            <input class="form-check-input" type="checkbox" name="remember_me" id="remember_me"
              <?php if ($remember) echo 'checked'; ?>>
            <label class="form-check-label" for="remember_me">Remember me</label>
          </div>
          <div class="d-grid mb-1">
            <input class="btn db-bg-primary db-text-sec fs-20" type="submit" name="signin-button" value="Sign In">
          </div>
          <p class="text-center">
            <a href="forgot-pass.php" class="forgot-pass db-text-sec text-decoration-none">Forgot your password?</a>
          </p>
          <p class="text-start">
            <span class="box-text db-text-sec">New to CineVault?</span>
            <a href="LP-SignUp.php" class="db-text-primary text-decoration-none signup-now">Sign up now.</a>
          </p>
        </form>
      </div>
    </section>
  </main>
  <script src="password-toggle.js?v=<?php echo CV_ASSET_VERSION; ?>"></script>
</body>

</html>