<?php
session_start();
include '../includes/db-connection.php';

$email_error = $pass_error = $mobile_error = $fname_error = $mname_error = $lname_error = $fb_error = "";
$valid_email = $valid_pass = $valid_mobile = $valid_fname = $valid_mname = $valid_lname = $valid_fb = true;

if (isset($_POST['admin-signup-btn'])) {
  $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
  $email = mysqli_real_escape_string($con, $email);

  $mobile = trim($_POST['mobile']);
  $fname = trim($_POST['fname']);
  $mname = trim($_POST['mname']);
  $lname = trim($_POST['lname']);
  $fb_acc = trim($_POST['fb-acc']);
  $pword = $_POST['pword'];

  // Email Validation
  $check_query = mysqli_query($con, "SELECT admin_email FROM tbl_admin_acc WHERE admin_email = '$email' LIMIT 1");
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $email_error = "Invalid email format";
    $valid_email = false;
  } elseif (mysqli_num_rows($check_query) > 0) {
    $email_error = "This email is already registered. Please use a different one.";
    $valid_email = false;
  }

  // Password Validation
  if (strlen($pword) < 8) {
    $pass_error = "Password must be at least 8 characters long";
    $valid_pass = false;
  }

  // Mobile Validation
  if (!preg_match('/^[0-9]{11}$/', $mobile)) {
    $mobile_error = "Mobile number must be exactly 11 digits";
    $valid_mobile = false;
  }

  // Name Validations (at least 4 characters)
  if (strlen($fname) < 2) {
    $fname_error = "First name must be at least 2 characters";
    $valid_fname = false;
  }
  if (strlen($mname) < 4) {
    $mname_error = "Middle name must be at least 4 characters";
    $valid_mname = false;
  }
  if (strlen($lname) < 4) {
    $lname_error = "Last name must be at least 4 characters";
    $valid_lname = false;
  }

  if (strlen($fb_acc) < 2) {
    $fb_error = "Invalid Facebook account";
    $valid_fb = false;
  }

  if ($valid_email && $valid_pass && $valid_mobile && $valid_fname && $valid_mname && $valid_lname && $valid_fb) {
    $hashed_pass = password_hash($pword, PASSWORD_DEFAULT);
    $insert = mysqli_query($con, "INSERT INTO tbl_admin_acc 
      (admin_email, admin_mobile, admin_fname, admin_mname, admin_lname, admin_fb, admin_password)
      VALUES 
      ('$email', '$mobile', '$fname', '$mname', '$lname', '$fb_acc', '$hashed_pass')");

    if ($insert) {
      echo "<script>
        alert('Admin account successfully created!');
        window.location.href = 'admin-signin.php';
      </script>";
    } else {
      echo "<script>alert('Something went wrong. Please try again.');</script>";
    }
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
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../DASHBOARD-CSS/admin-signup.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <link rel="icon" href="../MOVIE-IMG/HEADER-IMG/CINEVAULT-LOGO.svg">

</head>

<body class="bg-dark overflow-hidden">
  <header class="signin-header fixed-top d-flex align-items-center ms-5 fw-semibold">
    <div class="left-header fs-20">
      <a class="navbar-brand fw-semibold text-white ms-5" href="#">Cine<span class="text-primary">Vault</span> Admin</a>
    </div>
  </header>
  <main>
    <section class="position-relative">
      <img class="landing-page-img" src="TRENDING_IMAGES/LandingPageWallpaper.jpg" alt="">

      <div class="signin-container" id="signup-con" style="width: clamp(580px, 50vw, 700px);">
        <h1 class="signin-text text-start mb-4">Sign Up</h1>
        <p class="text-start create-acc text-primary">Create An Account</p>
        <form class="signin-form d-flex flex-column gap-3" method="post" action="" id="signup-form">
          <div class="row mt-2">
            <div class="col pe-2">
              <div class="input-container">
                <input class="signin-inputs bg-transparent <?php if (!$valid_email)
                  echo 'border border-danger'; ?>"
                  type="text" name="email" placeholder="Email address" value="<?= isset($email) ? $email : '' ?>"
                  required>
                <label for="email">Email address</label>
                <?php if (!$valid_email): ?>
                  <p class="mb-2 mt-1 text-danger text-start" style="font-size: 14px;"><?= $email_error ?></p>
                <?php endif; ?>
              </div>
            </div>
            <div class="col ps-2">
              <div class="input-container">
                <input class="signin-inputs bg-transparent <?php if (!$valid_mobile)
                  echo 'border border-danger'; ?>"
                  type="number" name="mobile" placeholder="Mobile number" value="<?= isset($mobile) ? $mobile : '' ?>"
                  required>
                <label for="email">Mobile Number</label>
                <?php if (!$valid_mobile): ?>
                  <p class="mb-2 mt-1 text-danger text-start" style="font-size: 14px;"><?= $mobile_error ?></p>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <div class="row mt-2">
            <div class="col pe-2">
              <div class="input-container">
                <input class="signin-inputs bg-transparent <?php if (!$valid_fname)
                  echo 'border border-danger'; ?>"
                  type="text" name="fname" placeholder="First Name" value="<?= isset($fname) ? $fname : '' ?>" required>
                <label for="email">First Name</label>
                <?php if (!$valid_fname): ?>
                  <p class="mb-2 mt-1 text-danger text-start" style="font-size: 14px;"><?= $fname_error ?></p>
                <?php endif; ?>
              </div>
            </div>
            <div class="col">
              <div class="input-container">
                <input class="signin-inputs bg-transparent <?php if (!$valid_mname)
                  echo 'border border-danger'; ?>"
                  type="text" name="mname" placeholder="Middle Name" value="<?= isset($mname) ? $mname : '' ?>"
                  required>
                <label for="email">Middle Name</label>
                <?php if (!$valid_mname): ?>
                  <p class="mb-2 mt-1 text-danger text-start" style="font-size: 14px;"><?= $mname_error ?></p>
                <?php endif; ?>
              </div>
            </div>
            <div class="col ps-2">
              <div class="input-container">
                <input class="signin-inputs bg-transparent <?php if (!$valid_lname)
                  echo 'border border-danger'; ?>"
                  type="text" name="lname" placeholder="Last Name" value="<?= isset($lname) ? $lname : '' ?>" required>
                <label for="email">Last Name</label>
                <?php if (!$valid_lname): ?>
                  <p class="mb-2 mt-1 text-danger text-start" style="font-size: 14px;"><?= $lname_error ?></p>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <div class="row mt-2 pe-0">
            <div class="col pe-2">
              <div class="input-container">
                <input class="signin-inputs bg-transparent <?php if (!$valid_fb)
                  echo 'border border-danger'; ?>"
                  type="text" name="fb-acc" placeholder="Facebook account" value="<?= isset($fb_acc) ? $fb_acc : '' ?>"
                  required>
                <label for="email">Facebook account</label>
                <?php if (!$valid_fb): ?>
                  <p class="mb-2 mt-1 text-danger text-start" style="font-size: 14px;"><?= $fb_error ?></p>
                <?php endif; ?>
              </div>
            </div>
            <div class="col ps-2 ">
              <div class="input-container">
                <input class="signin-inputs bg-transparent <?php if (!$valid_pass)
                  echo 'border border-danger'; ?>"
                  type="password" name="pword" placeholder="Password" required>
                <label for="email">Password</label>
                <?php if (!$valid_pass): ?>
                  <p class="mb-2 mt-1 text-danger text-start" style="font-size: 14px;"><?= $pass_error ?></p>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <div class="d-grid mt-4">
            <button type="submit" class="btn btn-primary fs-20" id="signup-btn" name="admin-signup-btn">Sign Up</button>
          </div>
        </form>
        <p class="text-start ms-30 mt-3">
          <span class="box-text">Already Have An Account?</span>
          <a href="admin-signin.php" class="text-primary text-decoration-none signin-now">Sign in now.</a>
        </p>
        <div />
    </section>

  </main>
</body>

</html>