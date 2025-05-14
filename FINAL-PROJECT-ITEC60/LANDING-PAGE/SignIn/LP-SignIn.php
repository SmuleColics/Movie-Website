<?php  
include 'db-con.php'; 
?>

<!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="SignIn.css"> 
    <link rel="stylesheet" href="../../DASHBOARD-CSS/for-all.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="icon" href="../../MOVIE-IMG/HEADER-IMG/CINEVAULT-LOGO.svg">

  </head>

  <body class="bg-dark overflow-hidden  ">
    <header class="signin-header fixed-top d-flex align-items-center ms-5 fw-semibold">
      <div class="left-header fs-20">
        <a class="navbar-brand fw-semibold db-text-sec ms-5"  href="../LandingPageMovie.php">Cine<span class="db-text-primary">Vault</span></a>
      </div>
    </header>

    <main>
      <section class="position-relative">
        <img class="landing-page-img" src="../ImagesLP/LandingPageWallpaper.jpg" alt="">

        <div class="signin-container">
          
          <?php 
            $email_error = "";
            $pass_error = "";
            $valid_email = true;
            $valid_pass = true;

            if (isset($_POST['signin-button']))  {
              $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
              $pass = filter_input(INPUT_POST, "password", FILTER_SANITIZE_EMAIL);

              $email = mysqli_real_escape_string($con, $email);

              $result = mysqli_query($con, "SELECT * FROM tbl_signup_acc WHERE signup_email = '$email'");

              if ($row = mysqli_fetch_assoc($result)) {
                if (password_verify($pass, $row['signup_password'])) {
                    $_SESSION['user_id'] = $row['signup_id'];
                    $_SESSION['user_email'] = $row['signup_email'];
                    $_SESSION['is_logged_in'] = true;

                    // ✅ Redirect
                    echo "<script>
                            alert('Login successful!');
                            window.location.href = '../../MovieWebsite/FirstProject.html';
                          </script>";
                    exit;

                } else {
                  $pass_error = "The password you’ve entered is incorrect.";
                  $valid_pass = false;
                }
              } else {
                $email_error = "The email or mobile number you entered isn’t connected to an account. Find your account and log in.";
                $valid_email = false;
              }
            } 
              
          ?>
          <h1 class="signin-text db-text-sec text-start mb-4">Sign In</h1>
          <form class="signin-form db-text-sec d-flex flex-column gap-3" action="" method="post">
            <div class="d-grid input-container">
              <input class="signin-inputs bg-transparent" type="text" name="email" placeholder="Email or mobile number" required 
              <?php if(!$valid_email) echo "style='border-color: red;'" ?>
              >
              <label for="email">Email or mobile number</label>
              <?php if(!$valid_email): ?>
                <p class="mb-2 mt-1 text-danger text-start" style="font-size: 14px;"><?php echo $email_error ?></p>
              <?php endif; ?>
            </div>
            <div class="d-grid input-container">
              <input class="signin-inputs bg-transparent" type="password" name="password" placeholder="Password" required
              <?php if(!$valid_pass) echo "style='border-color: red;'" ?>
              >
              <label for="email">Password</label>
              <?php if(!$valid_pass): ?>
                <p class="mb-2 mt-1 text-danger text-start" style="font-size: 14px;"><?php echo $pass_error ?></p>
              <?php endif; ?>
            </div>
            <div class="d-grid mb-4">
              <input class="btn db-bg-primary db-text-sec fs-20" type="submit" name="signin-button" value="Sign In">
            </div>
  
            <p class="text-start">
              <span class="box-text db-text-sec">New to CineVault?</span>
              <a href="LP-SignUp.php" class="db-text-primary text-decoration-none signup-now">Sign up now.</a>
            </p>
          </form>
        </div>

      </section>
<!-- 
      <footer class="footer-signin pb-5">
        <div class="row text-white">
          <p class="pt-5 color-light-white">Questions? <a href="#" class="color-light-white text-decoration-none">Contact us.</a></p>
          <div class="col-sm-6 col-md-3">
            <div class="pb-2">
              <a class="color-light-white" href="#">FAQ</a>
            </div>
            <div class="pb-2">
              <a class="color-light-white" href="#">Cookie Preferences</a>
            </div>
          </div>

          <div class="col-sm-6 col-md-3">
            <div class="pb-2">
              <a class="color-light-white" href="#">Help Center</a>
            </div>
            <div class="pb-2">
              <a class="color-light-white" href="#">Corporate Information</a>
            </div>
          </div>

          <div class="col-sm-6 col-md-3">
            <div class="pb-2">
              <a class="color-light-white" href="#">Terms of Use</a>
            </div>
          </div>

          <div class="col-sm-6 col-md-3">
            <div class="pb-2">
              <a class="color-light-white" href="#">Privacy</a>
            </div>
          </div>
          
        </div>
      </footer> -->

    </main>
  </body>
</html> 