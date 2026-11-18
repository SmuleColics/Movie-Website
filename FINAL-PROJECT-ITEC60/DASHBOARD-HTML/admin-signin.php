<?php
include '../includes/db-connection.php';

$email_error = "";
$pass_error = "";
$valid_email = true;
$valid_pass = true;

if (isset($_POST['admin-signin-btn'])) {
  $email = strtolower(trim((string) ($_POST['email'] ?? '')));
  $pass = (string) ($_POST['password'] ?? '');

  $stmt = $con->prepare("SELECT * FROM tbl_admin_acc WHERE email = ? LIMIT 1");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $row = $stmt->get_result()->fetch_assoc();
  $stmt->close();

  if (cv_too_many_attempts($con, 'admin', $email)) {
    $pass_error = "Too many failed sign-in attempts. Please wait 15 minutes and try again.";
    $valid_pass = false;
  } elseif ($row && cv_check_password($con, 'tbl_admin_acc', 'admin_id', 'password', $row, $pass)) {
    cv_clear_failures($con, 'admin', $email);
    cv_login_session([
      'admin_id' => (int) $row['admin_id'],
      'admin_email' => $row['email'],
      'admin_logged_in' => true,
    ]);
    header('Location: dashboard.php');
    exit;
  } else {
    // Same message whether the email or the password is wrong.
    cv_record_failure($con, 'admin', $email);
    $pass_error = "The email or password you entered is incorrect.";
    $valid_pass = false;
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
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <link rel="stylesheet" href="../DASHBOARD-CSS/admin-signup.css">

  <link rel="icon" href="../MOVIE-IMG/HEADER-IMG/CINEVAULT-LOGO.svg">

</head>

<body class="bg-dark overflow-hidden">
  <header class="signin-header fixed-top d-flex align-items-center ms-5 fw-semibold">
    <div class="left-header fs-20">
      <a class="navbar-brand fw-semibold text-white ms-5" href="../LandingPageMovie.php">Cine<span
          class="text-primary">Vault</span> Admin</a>
    </div>
  </header>

  <main>
    <section class="position-relative">
      <img class="landing-page-img" src="TRENDING_IMAGES/LandingPageWallpaper.jpg" alt="">

      <div id="signin-con" class="signin-container">

        <h1 class="signin-text text-start mb-4">Sign In</h1>
        <form class="signin-form d-flex flex-column gap-3" action="" method="post">
          <div class="d-grid input-container">
            <input class="signin-inputs bg-transparent" type="text" name="email" placeholder="Email address" required
              <?php if (!$valid_email) {
                echo "style='border-color: red;'";
              } ?>>
            <label for="email">Email address</label>
            <?php if (!$valid_email): ?>
              <p class="mb-2 mt-1 text-danger text-start" style="font-size: 14px;"><?php echo $email_error ?></p>
            <?php endif; ?>
          </div>

          <div class="d-grid input-container">
            <input class="signin-inputs bg-transparent" type="password" name="password" placeholder="Password" required
              <?php if (!$valid_pass) {
                echo "style='border-color: red;'";
              } ?>>
            <label for="password">Password</label>
            <?php if (!$valid_pass): ?>
              <p class="mb-2 mt-1 text-danger text-start" style="font-size: 14px;"><?php echo $pass_error ?></p>
            <?php endif; ?>
          </div>
          
          <div class="d-grid">
            <input class="btn btn-primary fs-20" type="submit" name="admin-signin-btn" value="Sign In">
          </div>
          <div class="mb-4">
            <a href="../LANDING-PAGE/SignIn/forgot-pass.php" class="text-white text-decoration-none" href="#" >Forgot Your Password?</a></div>
        </form>
      </div>

    </section>
  </main>
</body>


</html>