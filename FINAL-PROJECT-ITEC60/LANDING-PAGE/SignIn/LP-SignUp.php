<?php 
session_start();
include 'db-con.php'; 
?>

<!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="SignIn.css">
    <link rel="stylesheet" href="../../DASHBOARD-CSS/for-all.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="icon" href="../../MOVIE-IMG/HEADER-IMG/CINEVAULT-LOGO.svg">

  </head>
  <body class="bg-dark">
    <header class="signin-header fixed-top d-flex align-items-center ms-5 fw-semibold">
      <div class="left-header fs-20">
        <a class="navbar-brand fw-semibold db-text-sec ms-5"  href="../LandingPageMovie.php">Cine<span class="db-text-primary">Vault</span></a>
      </div>
    </header>

    <main>
      <section class="position-relative">
        <img class="landing-page-img" src="../ImagesLP/LandingPageWallpaper.jpg" alt="">

        <div class="signin-container">
          <h1 class="signin-text db-text-sec text-start mb-4">Sign Up</h1>
          <p class="text-start create-acc db-text-primary p-0">Create An Account</p>

          <?php 
            $email_error = "";
            $pass_error = "";
            $valid_email = true;
            $valid_pass = true;

            // $form_submitted = $_SERVER["REQUEST_METHOD"] == "POST";

            if (isset($_POST["signup-btn"])) {

              $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
              $email = mysqli_real_escape_string($con, $email);

              $pass = filter_input(INPUT_POST, "password", FILTER_SANITIZE_EMAIL);

              $check_query = mysqli_query($con, "SELECT signup_email FROM tbl_signup_acc WHERE signup_email = '$email' LIMIT 1");

              if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $email_error = "Invalid email format";
                $valid_email = false;
              } elseif (mysqli_num_rows($check_query) > 0) {
                $email_error = "This email is already registered. Please use a different one.";
                $valid_email = false; 
              }

              if (strlen($pass) < 8) {
                $pass_error = "Password must be 8 characters long";
                $valid_pass = false;
              }

              if ($valid_email && $valid_pass) {
                $hash_pass = password_hash($pass, PASSWORD_DEFAULT);
                $_SESSION['signup_email'] = $email;
                $_SESSION['signup_password'] = $hash_pass;

                echo "<script>
                    alert('Account created successfully! Please complete your payment to continue.');
                    window.location.href = 'LP-Payment.php';
                  </script>";
                }
                // header("Location: LP-SignUp.php");
                // exit;
            }
          ?>

          <form class="signin-form db-text-sec d-flex flex-column gap-3" method="post" action="" id="signup-form">
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
              <input class="signin-inputs bg-transparent" type="password" name="password" placeholder="Create a password" required
              <?php if(!$valid_pass) echo "style='border-color: red;'" ?>
              >
              <label for="password">Create a password</label>
              <?php if(!$valid_pass): ?>
                <p class="mb-2 mt-1 text-danger text-start" style="font-size: 14px;"><?php echo $pass_error ?></p>
              <?php endif; ?>
            </div>
            <div class="d-grid">
              <button type="submit" class="btn db-text-sec db-bg-primary fs-20" id="signup-btn" name="signup-btn">Next</button>
            </div>
            
          </form>
          <p class="text-start ms-30 mt-3">
            <span class="box-text db-text-sec">Already Have An Account?</span>
            <a href="LP-SignIn.php" class="db-text-primary text-decoration-none signin-now">Sign in now.</a>
          </p>
        <div/>
      </section>

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
      </footer>

    </main>
    <script>
    /*document.getElementById("signup-btn").addEventListener("click", function(event) {
      event.preventDefault(); // Prevent default form submission
      document.getElementById("signup-form").submit(); // Submit the form

    });*/
    </script>
  </body>
</html>