<?php
include 'db-con.php';

$first_name_error = "";
$last_name_error = "";
$mobile_error = "";
$email_error = "";
$pass_error = "";

$valid_first_name = true;
$valid_last_name = true;
$valid_mobile = true;
$valid_email = true;
$valid_pass = true;

$first_name = $last_name = $mobile_number = $email = "";
$agreed = false;
$terms_error = "";

if (isset($_POST["signup-btn"])) {
  // Sanitize and assign
  $first_name = trim((string) ($_POST["first_name"] ?? ''));
  $last_name = trim((string) ($_POST["last_name"] ?? ''));
  $mobile_number = trim((string) ($_POST["mobile_number"] ?? ''));
  $email = strtolower(trim((string) ($_POST["email"] ?? '')));
  $pass = (string) ($_POST["password"] ?? '');

  // Names: letters, plus spaces, dots, dashes and apostrophes (e.g. "John Lenard", "Dela Cruz")
  if (!preg_match("/^[A-Za-z][A-Za-z .'-]{1,99}$/", $first_name)) {
    $first_name_error = "First name must be at least 2 letters.";
    $valid_first_name = false;
  }

  if (!preg_match("/^[A-Za-z][A-Za-z .'-]{1,99}$/", $last_name)) {
    $last_name_error = "Last name must be at least 2 letters.";
    $valid_last_name = false;
  }

  $agreed = isset($_POST['agree_terms']);
  if (!$agreed) {
    $terms_error = "Please agree to the Terms of Service and Privacy Policy.";
  }

  // Mobile number validation
  if (!preg_match('/^\d{11}$/', $mobile_number)) {
    $mobile_error = "Mobile number must be exactly 11 digits.";
    $valid_mobile = false;
  }

  // Email validation
  $stmt = $con->prepare("SELECT 1 FROM tbl_signup_acc WHERE signup_email = ? LIMIT 1");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $email_taken = (bool) $stmt->get_result()->fetch_assoc();
  $stmt->close();
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $email_error = "Invalid email format";
    $valid_email = false;
  } elseif ($email_taken) {
    $email_error = "This email is already registered. Sign in instead, or use a different email.";
    $valid_email = false;
  }

  // Password validation
  if (strlen($pass) < 8) {
    $pass_error = "Password must be at least 8 characters long";
    $valid_pass = false;
  }

  if ($valid_first_name && $valid_last_name && $valid_mobile && $valid_email && $valid_pass && $agreed) {
    $hash_pass = password_hash($pass, PASSWORD_DEFAULT);

    // CineVault is free: the account works right away, no payment or approval.
    $stmt = $con->prepare("INSERT INTO tbl_signup_acc (first_name, last_name, mobile_number, signup_email, signup_password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $first_name, $last_name, $mobile_number, $email, $hash_pass);
    $stmt->execute();
    $signup_id = $stmt->insert_id;
    $stmt->close();

    cv_login_session([
      'user_id' => (int) $signup_id,
      'user_email' => $email,
      'is_logged_in' => true,
    ]);
    cv_alert_redirect('Welcome to CineVault! Your account is ready.', '../../MovieWebsite/FirstProject.php');
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="SignIn.css?v=<?php echo CV_ASSET_VERSION; ?>">
  <link rel="stylesheet" href="../../DASHBOARD-CSS/for-all.css?v=<?php echo CV_ASSET_VERSION; ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="icon" href="../../MOVIE-IMG/HEADER-IMG/CINEVAULT-LOGO.svg">
</head>

<body class="bg-dark">
  <header class="signin-header fixed-top d-flex align-items-center ms-5 fw-semibold">
    <div class="left-header fs-20">
      <a class="navbar-brand fw-semibold db-text-sec ms-5" href="../LandingPageMovie.php">Cine<span
          class="db-text-primary">Vault</span></a>
    </div>
  </header>

  <main>
    <section class="position-relative">
      <img class="landing-page-img" src="../ImagesLP/LandingPageClassics.jpg" alt="">
      <div class="signin-container" style="width: 500px;">
        <a href="../LandingPageMovie.php" class="cv-back-link"><i class="fa-solid fa-chevron-left"></i> Back to home</a>
        <h1 class="signin-text db-text-sec text-start mb-4">Sign Up</h1>
        <p class="text-start create-acc db-text-primary p-0">Create An Account</p>
        <form class="signin-form db-text-sec d-flex flex-column gap-3" method="post" action="" id="signup-form">
          <div class="d-flex justify-content-between align-items-center gap-2">
            <div class="d-grid input-container">
              <input class="signin-inputs bg-transparent" type="text" name="first_name" placeholder="First Name"
                value="<?php echo htmlspecialchars($first_name); ?>" required <?php if (!$valid_first_name) echo "style='border-color: red;'"; ?>>
              <label for="first_name">First Name</label>
              <?php if (!$valid_first_name): ?>
                <p class="mb-2 mt-1 text-danger text-start" style="font-size: 14px;"><?php echo $first_name_error ?></p>
              <?php endif; ?>
            </div>
            <div class="d-grid input-container">
              <input class="signin-inputs bg-transparent" type="text" name="last_name" placeholder="Last Name"
                value="<?php echo htmlspecialchars($last_name); ?>" required <?php if (!$valid_last_name) echo "style='border-color: red;'"; ?>>
              <label for="last_name">Last Name</label>
              <?php if (!$valid_last_name): ?>
                <p class="mb-2 mt-1 text-danger text-start" style="font-size: 14px;"><?php echo $last_name_error ?></p>
              <?php endif; ?>
            </div>
          </div>
          <div class="d-grid input-container">
            <input class="signin-inputs bg-transparent" type="text" name="mobile_number" placeholder="Mobile Number"
              maxlength="11" value="<?php echo htmlspecialchars($mobile_number); ?>" required <?php if (!$valid_mobile) echo "style='border-color: red;'"; ?>>
            <label for="mobile_number">Mobile Number</label>
            <?php if (!$valid_mobile): ?>
              <p class="mb-2 mt-1 text-danger text-start" style="font-size: 14px;"><?php echo $mobile_error ?></p>
            <?php endif; ?>
          </div>
          <div class="d-grid input-container">
            <input class="signin-inputs bg-transparent" type="text" name="email" placeholder="Email address"
              value="<?php echo htmlspecialchars($email); ?>" required <?php if (!$valid_email) echo "style='border-color: red;'"; ?>>
            <label for="email">Email address</label>
            <?php if (!$valid_email): ?>
              <p class="mb-2 mt-1 text-danger text-start" style="font-size: 14px;"><?php echo $email_error ?></p>
            <?php endif; ?>
          </div>
          <div class="d-grid input-container">
            <input class="signin-inputs bg-transparent" type="password" name="password" placeholder="Create a password"
              required <?php if (!$valid_pass) echo "style='border-color: red;'"; ?>>
            <label for="password">Create a password</label>
            <?php if (!$valid_pass): ?>
              <p class="mb-2 mt-1 text-danger text-start" style="font-size: 14px;"><?php echo $pass_error ?></p>
            <?php endif; ?>
          </div>
          <div class="text-start cv-terms-check">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="agree_terms" id="agree_terms" required
                <?php if ($agreed) echo 'checked'; ?>>
              <label class="form-check-label" for="agree_terms">
                I agree to the <a href="../terms.php" target="_blank" class="db-text-primary">Terms of Service</a>
                and <a href="../privacy.php" target="_blank" class="db-text-primary">Privacy Policy</a>.
              </label>
            </div>
            <?php if ($terms_error): ?>
              <p class="mb-0 mt-1 text-danger" style="font-size: 14px;"><?php echo e($terms_error); ?></p>
            <?php endif; ?>
          </div>
          <div class="d-grid">
            <button type="submit" class="btn db-text-sec db-bg-primary fs-20" id="signup-btn"
              name="signup-btn">Create Account</button>
          </div>
        </form>
        <p class="text-start ms-30 mt-3">
          <span class="box-text db-text-sec">Already Have An Account?</span>
          <a href="LP-SignIn.php" class="db-text-primary text-decoration-none signin-now">Sign in now.</a>
        </p>
        <div />
    </section>
    <footer class="footer-signin pb-5">
      <?php require_once __DIR__ . '/../../includes/site-footer.php'; echo cv_site_footer_links('../'); ?>
    </footer>
  </main>
  <script src="password-toggle.js?v=<?php echo CV_ASSET_VERSION; ?>"></script>
</body>

</html>
