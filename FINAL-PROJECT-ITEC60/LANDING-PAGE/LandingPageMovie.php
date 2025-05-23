<?php
include('SignIn/db-con.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Landing Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="LandingPage.css">
  <link rel="stylesheet" href="../DASHBOARD-CSS/for-all.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="icon" href="../MOVIE-IMG/HEADER-IMG/CINEVAULT-LOGO.svg">
</head>

<body class="bg-dark">
  <!-- START OF HEADER -->
  <header class="d-flex justify-content-between align-items-center mx-5 bg-transparent landing-page-header fixed-top">
    <div class="left-header ">
      <a class="navbar-brand fs-20 text-white fw-semibold ms-md-5" href="#">Cine<span
          class="db-text-primary">Vault</span></a>
    </div>
    <div class="right-header">
      <button class="btn db-bg-primary db-text-sec me-md-5"><a class="text-white text-decoration-none" href="SignIn/LP-Signin.php">Sign
          In</a></button>
    </div>
  </header>
  <!-- END OF HEADER -->

  <!-- START OF MAIN CONTENT -->
  <main>

    <section class="section-movie-poster">
      <img class="landing-page-img position-relative" src="ImagesLP/LandingPageWallpaper.jpg" alt="">
      <div class="movie-poster-text db-text-sec text-center position-absolute">
        <h1 class="endless-movies fs-56 fw-bold text-nowrap text-center">Endless movies, TV <span
            class="d-block text-nowrap text-center">series, and more.</span></h1> 
        <h2 class="starting-at fs-20 fw-semibold mt-2 mb-4">Starting at just ₱99. Cancel anytime.</h2>
        <p class="excited-to">Excited to start streaming? <a href="SignIn/LP-Signup.php" class="db-text-primary">Sign up</a> first to subscribe or
          renew your membership.</p>
        <!-- <button class="btn btn-primary sign-up-btn">Sign up</button> -->
      </div>  
    </section>

    <!-- START OF SECTION TRENDING -->


    <section class="section-trending ms-md-5 ms-3">

      <div class="trending-this-week">
        <p class="trending-text db-text-sec fs-24 fw-bold">Trending this week</p>
      </div>

      <div class="featured-wrapper position-relative">

        <div class="prev-button-trending position-absolute">
          <button class="btn border-1 prev-chevron-btn">
            <i class="fas fa-chevron-left fa-2xl text-white-50"></i>
          </button>
        </div>

        <div
          class="featured-images-container trending-images-container d-flex gap-3 position-relative overflow-visible">
          <?php
          $select_query = mysqli_query($con, "SELECT * FROM tbl_trending");

          while ($row = mysqli_fetch_assoc($select_query)) {
            $trending_id = $row['trending_id'];
            $poster = $row['poster'];

            echo "
                <div class='trending-images'>
                  <a href='#' data-bs-toggle='modal' data-bs-target='#modal-$trending_id'>
                    <img class='trending-img rounded-3' src='../DASHBOARD-HTML/TRENDING_IMAGES/$poster' alt=''>
                    <i class='fa-solid fa-play play-button' style='z-index: 100;'></i>
                  </a>
                </div>  
                ";
          }
          ?>

        </div>

        <div class="next-button-trending position-absolute">
          <button class="btn border-1 next-chevron-btn">
            <i class="fas fa-chevron-right fa-2xl text-white-50"></i>
          </button>
        </div>

      </div>
    </section>
    <!-- END OF SECTION TRENDING -->

    <!-- START OF SECTION MORE REASONS TO JOIN -->
    <section class="section-more-reasons mx-5">
      <div class="more-reasons-top">
        <p class="trending-text db-text-sec fs-24 fw-bold">More Reasons to Join</p>
      </div>
      <div class="more-reasons-bottom db-text-sec d-flex">
        <div class="row">
          <div class="col-sm-12 col-md-6 col-lg-4">

            <div class="more-reasons-box p-4 d-flex flex-column justify-content-between mb-3">
              <div>
                <h3 class="py-2">Enjoy on your TV</h3>
                <p class="box-text">Watch on Smart TVs, gaming consoles like PlayStation and Xbox, streaming devices
                  such as Chromecast and Apple TV, Blu-ray players, and more.</p>
              </div>
              <div class="d-flex justify-content-end align-items-end box-icon-container">
                <i class=" box-icon fa-solid fa-tv"></i>
              </div>
            </div>
          </div>
          <div class="col-sm-12 col-md-6 col-lg-4 third-col">
            <div class="more-reasons-box p-4 d-flex flex-column justify-content-between mb-3">
              <div>
                <h3 class="py-2">Endless Variety of Entertainment</h3>
                <p class="box-text">
                  Explore a vast library of movies and series across all genres—from timeless classics to the latest releases. Whether you're in the mood for action, romance, comedy, or documentaries, there's always something new to discover.
                </p>
              </div>
              <div class="d-flex justify-content-end align-items-end box-icon-container">
                <i class="fa-solid fa-film box-icon"></i>
              </div>
            </div>
          </div>
          <div class="col-sm-12 col-md-6 col-lg-4">
            <div class="more-reasons-box p-4 d-flex flex-column justify-content-between mb-3">
              <div>
                <h3 class="py-2">Stream anywhere, anytime</h2>
                  <p class="box-text">Watch endless movies and TV shows on your phone, tablet, laptop, and TV anytime.
                  </p>
              </div>
              <div class="d-flex justify-content-end align-items-end box-icon-container-m">
                <i class="fas fa-mobile-screen box-icon"></i>
              </div>
            </div>
          </div>
          
        </div>
      </div>

    </section>
    <!-- END OF SECTION MORE REASONS TO JOIN -->

    <!-- START OF FOOTER -->
    <footer class="footer-landing-page">
      <div class="row db-text-sec">

        <div class="col-sm-6 col-md-3">
          <div class="pb-2">
            <a class="color-light-white" href="#">FAQ</a>
          </div>
          <div class="pb-2">
            <a class="color-light-white" href="#">Investor Relations</a>
          </div>
          <div class="pb-2">
            <a class="color-light-white" href="#">Ways to watch</a>
          </div>
          <div class="pb-2">
            <a class="color-light-white" href="#">Corporate Information</a>
          </div>
        </div>

        <div class="col-sm-6 col-md-3">
          <div class="pb-2">
            <a class="color-light-white" href="#">Help Center</a>
          </div>
          <div class="pb-2">
            <a class="color-light-white" href="#">Jobs</a>
          </div>
          <div class="pb-2">
            <a class="color-light-white" href="#">Terms of use</a>
          </div>
          <div class="pb-2">
            <a class="color-light-white" href="#">Contact Us</a>
          </div>
        </div>

        <div class="col-sm-6 col-md-3">
          <div class="pb-2">
            <a class="color-light-white" href="#">Account</a>
          </div>
          <div class="pb-2">
            <a class="color-light-white" href="#">Redeem Gift Cards</a>
          </div>
          <div class="pb-2">
            <a class="color-light-white" href="#">Piracy</a>
          </div>
          <div class="pb-2">
            <a class="color-light-white" href="#">Speed Test</a>
          </div>
        </div>

        <div class="col-sm-6 col-md-3">
          <div class="pb-2">
            <a class="color-light-white" href="#">Media Center</a>
          </div>
          <div class="pb-2">
            <a class="color-light-white" href="#">Buy Gift Cards</a>
          </div>
          <div class="pb-2">
            <a class="color-light-white" href="#">Cookie Preferences</a>
          </div>
          <div class="pb-2">
            <a class="color-light-white" href="#">Legal Notices</a>
          </div>
        </div>
        <p class="pt-5 color-light-white">CineVault Philippines</p>
      </div>
    </footer>
    <!-- END OF FOOTER -->
    
    <!-- ========== LANDING PAGE MODAL ========== -->

    <?php
    $select_query = mysqli_query($con, "SELECT * FROM tbl_trending");

    while ($row = mysqli_fetch_assoc($select_query)) {
      $trending_id = $row['trending_id'];
      $modal_poster = $row['modal_poster'];
      $modal_poster_title = $row['modal_poster_title'];
      $date_released = $row['date_released'];
      $age_rating = $row['age_rating'];
      $category = $row['category'];
      $genre_1 = $row['genre_1'];
      $genre_2 = $row['genre_2'];
      $genre_3 = $row['genre_3'];
      $description = $row['description'];

      echo "
      <div class='modal fade' id='modal-$trending_id' tabindex='-1' aria-labelledby='exampleModalLabel' aria-hidden='true'>
        <div class='modal-dialog modal-dialog-centered modal-lg modal-dark border-3'>
          <div class='modal-content bg-dark modals'>
            
            <div class='modal-body'>
              <div class='modal-body-content'>
                <div class='modal-pic-container m-0 position-relative'>
                  <img class='w-100 position-relative rounded-3 m-0 p-0 sosyal-wallpaper' src='../DASHBOARD-HTML/TRENDING_IMAGES/$modal_poster' alt=''>
                  <img class='poster-title-img' src='../DASHBOARD-HTML/TRENDING_IMAGES/$modal_poster_title' alt='' style='height: 52px;'  >
                
                  <button type='button' class='btn-close btn-close-white position-absolute modal-close-button' data-bs-dismiss='modal' aria-label='Close'></button>
                </div>   

                <div class='modal-body-text p-4 mt-1'>
                  <div class='modal-text-ratings d-flex gap-1 text-white'>
                    <p class='modal-text-rating'>$date_released</p>
                    <p class='modal-text-rating'>$age_rating+</p>
                    <p class='modal-text-rating'>$category</p>
                    <p class='modal-text-rating'>$genre_1</p>";

                  if (!empty($genre_2)) {
                    echo "<p class='modal-text-rating'>$genre_2</p>";
                  }
                  if (!empty($genre_3)) {
                    echo "<p class='modal-text-rating'>$genre_3</p>";
                  }

                  echo "      
                  </div>

                  <p class='text-white'>$description</p>
              
                  <div>
                    <button class='btn btn-primary fs-4 mb-3'>
                      <a class='text-white text-decoration-none' href='SignIn/LP-Signup.php'>
                        Get started <i class='fa-solid fa-chevron-right text-white'></i>
                      </a>
                    </button>
                  </div>
                </div>

              </div>
            </div>
            
          </div>
        </div>
      </div>
      ";
    }

    ?>

    <!-- END OF MODALS -->

  </main>
  <!-- END OF MAIN CONTENT -->
  <script>

    const slider = document.querySelector(".featured-images-container");
    const nextButton = document.querySelector(".next-chevron-btn");
    const prevButton = document.querySelector(".prev-chevron-btn");

    prevButton.classList.add('hide-btn');

    let scrollAmount = 0;
    let scrollStep = 500; // Default max clicks for large screens
    let clickCount = 0; // Counter for next button clicks

    // Function to check window width and update maxClicks
    function checkWidth() {
      if (window.innerWidth >= 1024) {
        maxClicks = 1;
      } else {
        maxClicks = 3;
      }
      clickCount = 0;
    }

    // Run checkWidth on page load
    checkWidth();

    nextButton.addEventListener("click", () => {
      if (clickCount < maxClicks) {
        scrollAmount += scrollStep;
        slider.style.transform = `translateX(-${scrollAmount}px)`;
        clickCount++;

        // Hide Next button if max clicks reached
        if (clickCount === maxClicks) {
          nextButton.classList.add('hide-btn');
        }
        prevButton.classList.remove('hide-btn'); // Show Prev button
      }
    });

    prevButton.addEventListener("click", () => {
      if (clickCount > 0) {
        scrollAmount -= scrollStep;
        slider.style.transform = `translateX(-${scrollAmount}px)`;
        clickCount--;

        // Show Next button when going back
        nextButton.classList.remove('hide-btn');

        // Hide Prev button if at the start
        if (clickCount === 0) {
          prevButton.classList.add('hide-btn');
        }
      }
    });

    window.addEventListener("resize", checkWidth);


  </script>
</body>

</html>