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

  <link rel="stylesheet" href="LandingPage.css?v=<?php echo CV_ASSET_VERSION; ?>">
  <link rel="stylesheet" href="../DASHBOARD-CSS/for-all.css?v=<?php echo CV_ASSET_VERSION; ?>">
  <link rel="stylesheet" href="../MovieWebsite/FirstProject.css?v=<?php echo CV_ASSET_VERSION; ?>">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="icon" href="../MOVIE-IMG/HEADER-IMG/CINEVAULT-LOGO.svg">
</head>
<body class="bg-dark" style="height: fit-content">
  <!-- START OF HEADER -->
  <header class="d-flex justify-content-between align-items-center mx-5 bg-transparent landing-page-header fixed-top">
    <div class="left-header ">
      <a class="navbar-brand fs-20 text-white fw-semibold ms-md-5" href="#">Cine<span
          class="db-text-primary">Vault</span></a>
    </div>
    <div class="right-header">
      <a class="btn db-bg-primary text-white me-md-5" href="SignIn/LP-SignIn.php">Sign In</a>
    </div>
  </header>
  <!-- END OF HEADER -->

  <!-- START OF MAIN CONTENT -->
  <main>

    <section class="section-movie-poster" style="transform: translateY(-25px);">
      <img class="landing-page-img position-relative" src="ImagesLP/LandingPageClassics.jpg" alt="">
      <div class="movie-poster-text db-text-sec text-center position-absolute">
        <h1 class="endless-movies fs-56 fw-bold text-nowrap text-center">Classic movies and TV, <span
            class="d-block text-nowrap text-center">free and in full.</span></h1>
        <h2 class="starting-at fs-20 fw-semibold mt-2 mb-4">Free to join. No payment, no card needed.</h2>
        <p class="excited-to">Treasures from the film vault: silent comedies, film noir, 1950s sci-fi and old sitcoms. Every title is the whole movie or episode.</p>
        <div class="d-flex flex-wrap justify-content-center gap-3 mt-4">
          <a href="SignIn/LP-SignUp.php" class="btn db-bg-primary text-white lp-hero-btn">
            Get Started <i class="fa-solid fa-chevron-right ms-2"></i>
          </a>
          <a href="#how-it-works" class="btn btn-outline-light lp-hero-btn">How it works</a>
        </div>
      </div>
    </section>

    <!-- START OF SECTION TOP 10 -->
    <?php
    $select = mysqli_query($con, "
  SELECT 
    ms.*,
    g1.genre_name AS genre_1,
    g2.genre_name AS genre_2,
    g3.genre_name AS genre_3
  FROM tbl_movie_series ms
  LEFT JOIN tbl_movie_series_genre g1 ON ms.genre_id1 = g1.genre_id
  LEFT JOIN tbl_movie_series_genre g2 ON ms.genre_id2 = g2.genre_id
  LEFT JOIN tbl_movie_series_genre g3 ON ms.genre_id3 = g3.genre_id
  ORDER BY ms.views DESC LIMIT 10
");
    $top10_rank = 1;
    $modals = "";
    ?>
    <section class="section-top-10 tewhite ms-5">
      <div class="top-10-container">

        <div class="top-10-top d-flex align-items-center">
          <p class="top-10-top-text">TOP 10</p>
          <div class="ms-3 fs-2 movies-today-text" style="color: #f4fff8">
            <p class="movies-text">MOVIES/SERIES</p>
            <p class="today-text">ALL TIME</p>
          </div>
        </div>

        <div class="top10-featured-wrapper position-relative">
          <div class="prev-button-top10"
            style="position: absolute; left: 0; top: 50%; transform: translateY(-50%);z-index: 300;">
            <button class="btn border-1 prev-chevron-btn-top10">
              <i class="fas fa-chevron-left fa-2xl text-white-50"></i>
            </button>
          </div>
          <div class="top-10-images-container d-flex gap-8 position-relative overflow-visible">
            <?php while ($row = mysqli_fetch_assoc($select)):
              $movie_series_id = $row['movie_series_id'];
              $top10_title = $row['title'];
              $top10_duration = $row['duration'];
              $top10_poster = $row['poster'];
              $top10_video = $row['video'];
              $top10_modal_poster_title = $row['modal_poster_title'];
              $top10_date_released = $row['date_released'];
              $top10_age_rating = $row['age_rating'];
              $top10_category = $row['category'];
              $top10_genre_1 = $row['genre_1'];
              $top10_genre_2 = $row['genre_2'];
              $top10_genre_3 = $row['genre_3'];
              $top10_cast = $row['cast'];
              $top10_description = $row['description'];
              ?>
              <div class="top-10-images position-relative">
                <p class="top-10-text<?php echo $top10_rank >= 10 ? ' is-double' : ''; ?>"><?php echo $top10_rank; ?></p>
                <a href='#' data-bs-toggle='modal' data-bs-target='#modal-top10-<?php echo $movie_series_id ?>'>
                  <div class="top-10-hover position-relative">
                    <img class="top-10-img rounded-3"
                      src="../DASHBOARD-HTML/MOVIE_SERIES_TITLE/<?php echo htmlspecialchars($top10_poster); ?>"
                      alt="<?php echo htmlspecialchars($top10_title); ?>">
                    <i class="fa-solid fa-play play-button-top-10"></i>
                  </div>
                </a>
              </div>
              <?php
              $genres = htmlspecialchars($top10_genre_1);
              if (!empty($top10_genre_2))
                $genres .= ', ' . htmlspecialchars($top10_genre_2);
              if (!empty($top10_genre_3))
                $genres .= ', ' . htmlspecialchars($top10_genre_3);

              // === MODAL DESIGN IS COPIED FROM YOUR SECTION TRENDING MODAL ===
              $modals .= "
<div class='modal fade' id='modal-top10-{$movie_series_id}' tabindex='-1' aria-labelledby='exampleModalLabel-top10-{$movie_series_id}' aria-hidden='true'>
  <div class='modal-dialog modal-dialog-centered modal-lg modal-dark border-3'>
    <div class='modal-content bg-dark modals'>
      <div class='modal-body'>
        <div class='modal-body-content'>
          <div class='modal-pic-container m-0 position-relative'>
            <img class='w-100 position-relative rounded-3 m-0 p-0 sosyal-wallpaper' src='../DASHBOARD-HTML/MOVIE_SERIES_TITLE/" . htmlspecialchars($top10_poster) . "' alt='' >

            <img class='position-relative'  src='../DASHBOARD-HTML/MOVIE_SERIES_TITLE/" . htmlspecialchars($top10_modal_poster_title) . "' alt='' style='object-fit: cover; transform: scale(.5) translate(-260px, -440px);'>

            <button type='button' class='btn-close btn-close-white position-absolute modal-close-button' data-bs-dismiss='modal' aria-label='Close'></button>
          </div>

          <div class='modal-body-text p-4 mt-1' style='position: absolute; bottom: -30px;'>
            <div class='modal-text-ratings d-flex gap-1 text-white'>
              <p class='modal-text-rating'>" . htmlspecialchars($top10_date_released) . "</p>
              <p class='modal-text-rating'>" . htmlspecialchars($top10_age_rating) . "+</p>
              <p class='modal-text-rating'>" . htmlspecialchars($top10_category) . "</p>
              <p class='modal-text-rating'>" . htmlspecialchars($top10_genre_1) . "</p>";
              if (!empty($top10_genre_2)) {
                $modals .= "<p class='modal-text-rating'>" . htmlspecialchars($top10_genre_2) . "</p>";
              }
              if (!empty($top10_genre_3)) {
                $modals .= "<p class='modal-text-rating'>" . htmlspecialchars($top10_genre_3) . "</p>";
              }
              $modals .= "
            </div>
            <p class='text-white'>" . htmlspecialchars($top10_description) . "</p>
            <div>
              <button class='btn btn-primary fs-4 mb-3'>
                <a class='text-white text-decoration-none' href='SignIn/LP-SignUp.php'>
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
              $top10_rank++;
            endwhile; ?>
          </div>
          <div class="next-button-top10 position-absolute"
            style="right: 0; top: 50%; transform: translateY(-50%);z-index: 300;">
            <button class="btn border-1 next-chevron-btn-top10">
              <i class="fas fa-chevron-right fa-2xl text-white-50"></i>
            </button>
          </div>
        </div>
      </div>
    </section>
    <?php echo $modals; ?>
    <!-- END OF SECTION TOP 10 -->

    <?php
    // Live numbers for the stats strip and the genre list.
    $lp_stats = mysqli_fetch_assoc(mysqli_query($con, "
      SELECT
        SUM(category = 'Movie')  AS movies,
        SUM(category = 'Series') AS series,
        (SELECT COUNT(*) FROM tbl_movie_series_episodes) AS episodes
      FROM tbl_movie_series
    "));
    $lp_genres = mysqli_query($con, "
      SELECT g.genre_name, COUNT(m.movie_series_id) AS total
      FROM tbl_movie_series_genre g
      JOIN tbl_movie_series m ON g.genre_id IN (m.genre_id1, m.genre_id2, m.genre_id3)
      GROUP BY g.genre_id, g.genre_name
      ORDER BY total DESC, g.genre_name
    ");
    $lp_genre_count = mysqli_num_rows($lp_genres);
    ?>

    <!-- START OF SECTION STATS -->
    <section class="lp-section mx-5">
      <div class="lp-stats row g-3 text-center db-text-sec">
        <div class="col-6 col-md-3">
          <div class="lp-stat">
            <p class="lp-stat-number"><?php echo (int) $lp_stats['movies']; ?></p>
            <p class="lp-stat-label">Movies</p>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="lp-stat">
            <p class="lp-stat-number"><?php echo (int) $lp_stats['series']; ?></p>
            <p class="lp-stat-label">Series</p>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="lp-stat">
            <p class="lp-stat-number"><?php echo (int) $lp_stats['episodes']; ?></p>
            <p class="lp-stat-label">Episodes</p>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="lp-stat">
            <p class="lp-stat-number"><?php echo $lp_genre_count; ?></p>
            <p class="lp-stat-label">Genres</p>
          </div>
        </div>
      </div>
    </section>
    <!-- END OF SECTION STATS -->

    <!-- START OF SECTION HOW IT WORKS -->
    <section class="lp-section mx-5" id="how-it-works">
      <p class="trending-text db-text-sec fs-24 fw-bold mb-1">How it Works</p>
      <p class="lp-section-sub mb-4">Three steps from sign up to your first movie, all free.</p>
      <div class="row g-3 db-text-sec">
        <div class="col-12 col-md-4">
          <div class="lp-step h-100">
            <span class="lp-step-number">1</span>
            <i class="fa-solid fa-user-plus lp-step-icon"></i>
            <h3 class="fs-5 fw-semibold mt-3">Create your account</h3>
            <p class="box-text mb-0">Sign up with your name, mobile number, email and a password.</p>
          </div>
        </div>
        <div class="col-12 col-md-4">
          <div class="lp-step h-100">
            <span class="lp-step-number">2</span>
            <i class="fa-solid fa-magnifying-glass lp-step-icon"></i>
            <h3 class="fs-5 fw-semibold mt-3">Find something to watch</h3>
            <p class="box-text mb-0">Browse the Top 10, pick a genre, or search by title or cast.</p>
          </div>
        </div>
        <div class="col-12 col-md-4">
          <div class="lp-step h-100">
            <span class="lp-step-number">3</span>
            <i class="fa-solid fa-circle-play lp-step-icon"></i>
            <h3 class="fs-5 fw-semibold mt-3">Press play</h3>
            <p class="box-text mb-0">Your account works right away. No payment and no waiting for approval.</p>
          </div>
        </div>
      </div>
    </section>
    <!-- END OF SECTION HOW IT WORKS -->

    <?php if ($lp_genre_count > 0): ?>
    <!-- START OF SECTION GENRES -->
    <section class="lp-section mx-5">
      <p class="trending-text db-text-sec fs-24 fw-bold mb-1">Browse by Genre</p>
      <p class="lp-section-sub mb-4">Something for every mood. Sign up to see every title.</p>
      <div class="d-flex flex-wrap gap-2">
        <?php while ($g = mysqli_fetch_assoc($lp_genres)): ?>
          <a href="SignIn/LP-SignUp.php" class="lp-genre-chip">
            <?php echo e($g['genre_name']); ?>
            <span class="lp-genre-count"><?php echo (int) $g['total']; ?></span>
          </a>
        <?php endwhile; ?>
      </div>
    </section>
    <!-- END OF SECTION GENRES -->
    <?php endif; ?>

    <!-- START OF SECTION MORE REASONS TO JOIN -->
    <section class="section-more-reasons lp-section mx-5">
      <div class="more-reasons-top">
        <p class="trending-text db-text-sec fs-24 fw-bold">More Reasons to Join</p>
      </div>
      <div class="more-reasons-bottom db-text-sec d-flex">
        <div class="row">
          <div class="col-sm-12 col-md-6 col-lg-4">

            <div class="more-reasons-box p-4 d-flex flex-column justify-content-between mb-3">
              <div>
                <h3 class="py-2">Every title plays in full</h3>
                <p class="box-text">No trailers and no clips. Each movie and episode on CineVault is the complete
                  version, streamed from the Internet Archive.</p>
              </div>
              <div class="d-flex justify-content-end align-items-end box-icon-container">
                <i class=" box-icon fa-solid fa-tv"></i>
              </div>
            </div>
          </div>
          <div class="col-sm-12 col-md-6 col-lg-4 third-col">
            <div class="more-reasons-box p-4 d-flex flex-column justify-content-between mb-3">
              <div>
                <h3 class="py-2">Classics from every era</h3>
                <p class="box-text">
                  Silent comedies, film noir, horror, westerns, sci-fi B-movies, old sitcoms and cartoons, mostly from
                  the 1920s to the 1960s, plus a few modern open movies.
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
                <h3 class="py-2">Free, legal, anywhere</h3>
                  <p class="box-text">Every title is in the public domain or openly licensed, so it is free to watch
                    on your phone, tablet, laptop or smart TV.
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

    <!-- START OF SECTION FAQ -->
    <section class="lp-section mx-5" id="faq">
      <p class="trending-text db-text-sec fs-24 fw-bold mb-4">Frequently Asked Questions</p>
      <div class="accordion lp-faq" id="lpFaq">
        <?php
        $lp_faqs = [
          ['What is CineVault?', 'CineVault is a free streaming site for classic movies and TV series. Every title is a full-length film or episode that is in the public domain or openly licensed, so members can watch all of it for free.'],
          ['Why are the movies old?', 'New movies belong to studios and are only licensed to paid services. CineVault only shows films whose copyright has ended or whose makers let anyone share them. That is mostly titles from before the mid-1960s, plus modern open movies like Big Buck Bunny.'],
          ['Where do the videos come from?', 'They stream from the Internet Archive (archive.org), a non-profit digital library. Movie details and posters come from TMDB.'],
          ['How much does it cost?', 'Nothing. CineVault is free. You only need an account.'],
          ['When can I start watching?', 'Right after you sign up. Your account works immediately, with no payment and no approval step.'],
          ['What do I need to sign up?', 'Your name, mobile number, email address and a password of at least 8 characters.'],
          ['Where can I watch?', 'Anywhere with a browser: your phone, tablet, laptop or smart TV.'],
          ['I forgot my password. What now?', 'On the sign in page, choose Forgot Your Password. We email you a 6-digit code to set a new one.'],
        ];
        foreach ($lp_faqs as $i => $faq): ?>
          <div class="accordion-item">
            <h3 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#lpFaq<?php echo $i; ?>" aria-expanded="false" aria-controls="lpFaq<?php echo $i; ?>">
                <?php echo e($faq[0]); ?>
              </button>
            </h3>
            <div id="lpFaq<?php echo $i; ?>" class="accordion-collapse collapse" data-bs-parent="#lpFaq">
              <div class="accordion-body"><?php echo e($faq[1]); ?></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
    <!-- END OF SECTION FAQ -->

    <!-- START OF SECTION FINAL CALL TO ACTION -->
    <section class="lp-section mx-5">
      <div class="lp-cta text-center db-text-sec">
        <h2 class="fw-bold mb-2">Ready to watch?</h2>
        <p class="box-text mb-4">Create your free account and watch the whole library of classics.</p>
        <a href="SignIn/LP-SignUp.php" class="btn db-bg-primary text-white lp-hero-btn">
          Get Started <i class="fa-solid fa-chevron-right ms-2"></i>
        </a>
      </div>
    </section>
    <!-- END OF SECTION FINAL CALL TO ACTION -->
            
    <!-- START OF FOOTER -->
    <footer class="footer-lp">
      <?php require_once __DIR__ . '/../includes/site-footer.php'; echo cv_site_footer_links(''); ?>
    </footer>
    <!-- END OF FOOTER -->
  
  </main>
  <!-- END OF MAIN CONTENT -->
  <script>

    // --- Top 10 Slider Logic ---
    const top10Slider = document.querySelector(".top-10-images-container");
    const top10NextButton = document.querySelector(".next-chevron-btn-top10");
    const top10PrevButton = document.querySelector(".prev-chevron-btn-top10");
    let top10ScrollAmount = 0;
    let top10ScrollStep = 500;
    top10Slider.style.transition = "transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)";

    function getTop10MaxScroll() {
      return top10Slider.scrollWidth - top10Slider.clientWidth + 56; // room after the last poster
    }
    function updateTop10Buttons() {
      if (top10ScrollAmount <= 0) {
        top10PrevButton.closest(".prev-button-top10").style.display = "none";
      } else {
        top10PrevButton.closest(".prev-button-top10").style.display = "";
      }
      if (
        getTop10MaxScroll() <= 0 ||
        top10ScrollAmount >= getTop10MaxScroll() - 5
      ) {
        top10NextButton.closest(".next-button-top10").style.display = "none";
      } else {
        top10NextButton.closest(".next-button-top10").style.display = "";
      }
    }
    top10NextButton.addEventListener("click", function () {
      top10ScrollAmount += top10ScrollStep;
      if (top10ScrollAmount > getTop10MaxScroll())
        top10ScrollAmount = getTop10MaxScroll();
      top10Slider.style.transform = `translateX(-${top10ScrollAmount}px)`;
      updateTop10Buttons();
    });
    top10PrevButton.addEventListener("click", function () {
      top10ScrollAmount -= top10ScrollStep;
      if (top10ScrollAmount < 0) top10ScrollAmount = 0;
      top10Slider.style.transform = `translateX(-${top10ScrollAmount}px)`;
      updateTop10Buttons();
    });
    window.addEventListener("resize", function () {
      if (top10ScrollAmount > getTop10MaxScroll()) {
        top10ScrollAmount = getTop10MaxScroll();
        top10Slider.style.transform = `translateX(-${top10ScrollAmount}px)`;
      }
      updateTop10Buttons();
    });
    updateTop10Buttons();


    // --- Scroll animations: each part slides and fades in as it comes into view ---
    (function () {
      if (!('IntersectionObserver' in window)) return;
      if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return; // respect the OS setting

      // [selector, animation, delay between items in ms]
      const groups = [
        ['.section-top-10 .top-10-top', 'up', 0],
        ['.section-top-10 .top-10-images', 'right', 90],
        ['.lp-stat', 'up', 110],
        ['#how-it-works > p', 'up', 80],
        ['.lp-step', 'up', 140],
        ['.lp-genre-chip', 'pop', 35],
        ['.section-more-reasons .trending-text', 'up', 0],
        ['.more-reasons-box', 'up', 140],
        ['#faq > p', 'up', 0],
        ['.lp-faq .accordion-item', 'up', 70],
        ['.lp-cta', 'zoom', 0],
        ['.cv-footer-links > div', 'up', 90],
      ];

      const observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          const el = entry.target;
          el.classList.add('is-visible');
          observer.unobserve(el);
          // After it has appeared, drop the stagger delay so hover effects react at once.
          setTimeout(function () { el.style.transitionDelay = ''; }, (parseInt(el.style.transitionDelay, 10) || 0) + 800);
          const num = entry.target.querySelector('.lp-stat-number');
          if (num) countUp(num);
        });
      }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

      groups.forEach(function ([selector, kind, step]) {
        document.querySelectorAll(selector).forEach(function (el, i) {
          el.classList.add('cv-reveal', 'cv-reveal-' + kind);
          el.style.transitionDelay = Math.min(i * step, 700) + 'ms';
          observer.observe(el);
        });
      });

      // Stats count up from 0 to their number.
      function countUp(el) {
        const target = parseInt(el.textContent, 10) || 0;
        const start = performance.now();
        const duration = 900;
        el.textContent = '0';
        (function tick(now) {
          const p = Math.min((now - start) / duration, 1);
          el.textContent = Math.round(target * (1 - Math.pow(1 - p, 3)));
          if (p < 1) requestAnimationFrame(tick);
        })(start);
      }
    })();
  </script>
  <script src="../shared/ellipsis-tooltips.js?v=<?php echo CV_ASSET_VERSION; ?>" defer></script>
</body>

</html>