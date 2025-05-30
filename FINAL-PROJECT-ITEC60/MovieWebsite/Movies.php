<?php
include '../includes/db-connection.php';

session_start();

if (isset($_GET['logout'])) {
  session_unset();
  session_destroy();
  header('Location: ../LANDING-PAGE/LandingPageMovie.php');
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Movies</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
  <link rel="stylesheet" href="FirstProject.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="Override.css">
  <link rel="icon" href="../MOVIE-IMG/HEADER-IMG/CINEVAULT-LOGO.svg">

</head>

<body class="bg-dark">
  <!-- START OF HEADER -->
  <nav id="navbar" class="navbar navbar-dark fixed-top">
    <div class="container-fluid">
      <header class="d-flex justify-content-between align-items-center w-100 px-md-5 bg-transparent">
        <div class="left-header">
          <a class="navbar-brand fw-semibold" href="#">Cine<span class="text-primary">Vault</span></a>
        </div>

        <div class="middle-header position-relative d-none d-lg-block">
          <ul class="d-flex list-unstyled fs-18 m-0">
            <li>
              <a href="FirstProject.php" class="text-decoration-none text-white fw-bold me-2">Home</a>
            </li>
            <li>
              <a href="Movies.php" class="text-decoration-none text-primary fw-bold mx-2">Movies</a>
            </li>
            <li>
              <a href="Series.php" class="text-decoration-none text-white fw-bold mx-2">Series</a>
            </li>
            <li>
              <a href="Categories.php" class="text-decoration-none text-white fw-bold ms-2">Categories</a>
            </li>
          </ul>
        </div>

        <div class="right-header d-flex">
          <div class="d-flex align-items-center">
            <div class="search-container">
              <input class="search rounded-5 bg-transparent text-white" type="text" placeholder="Search..." />
              <i class="fa-solid fa-magnifying-glass"></i>
            </div>
            <!-- ========== PROFILE MENU ========== -->
            <div class="dropdown-center">

              <button class="btn p-0 border-0" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="header-user rounded-circle flexbox-align ms-1" style="padding: 11px; color: #f4fff8;">
                  <i class="fa-solid fa-user db-text-sec fs-18"></i>
                </div>
              </button>

              <ul class="dropdown-menu dropdown-menu-dark mt-1 profile-dropdown" style="transform: translateX(-130px);">
                <li class="dropdown-profile-top d-flex mb-1" style="height: 50px;">
                  <a class="dropdown-item d-flex align-items-center" href="">

                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center ms-1"
                      style="height: 32px; width: 32px; transform: translateX(-9px);">
                      <i class="fa-solid fa-user db-text-sec fs-18"></i>
                    </div>

                    <div class="dropdown-profile-text" style="margin-left: -4px;">
                      <p class="fs-18 view-profile-text" style="transform: translateY(10px)">Manage Profile</p>
                      <p class="m-0 fs-14 db-text-secondary" style="transform: translateY(-10px)">User</p>
                    </div>
                  </a>
                </li>
                <li class="mb-1">
                  <a class="dropdown-item d-flex align-items-center" href="#">
                    <i class="fa-solid fa-question ms-1 me-2 fs-22"></i>
                    <span class="fs-18 d-inline-block ms-1">Help & Support</span>
                  </a>
                </li>
                <li class="mb-1" style="transform: translateX(-12px);">
                  <a class="dropdown-item d-flex align-items-center" href="#">
                    <i class="fa-solid fa-gear me-2 fs-22 "></i>
                    <span class="fs-18 d-inline-block">Settings</span>
                  </a>
                </li>
                <li class="mb-1">
                  <a href="../LANDING-PAGE/LandingPageMovie.php" class="dropdown-item d-flex align-items-center">
                    <i class="fa-solid fa-right-from-bracket me-2 fs-22"></i>
                    <span class="fs-18 d-inline-block">Log out</span>
                  </a>
                </li>
              </ul>
            </div>

          </div>
          <!-- ========== END PROFILE MENU ========== -->
          <button class="navbar-toggler text-light d-lg-none ms-3" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#offcanvasDarkNavbar" aria-controls="offcanvasDarkNavbar">
            <span class="navbar-toggler-icon"></span>
          </button>
        </div>
    </div>
    </header>

    <div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="offcanvasDarkNavbar"
      aria-labelledby="offcanvasDarkNavbarLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasDarkNavbarLabel">
          Cine<span class="text-primary">Vault</span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body text-center">
        <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
          <li class="nav-item">
            <a class="nav-link text-white" aria-current="page" href="FirstProject.php">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-primary" href="Movies.php">Movies</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="Series.php">Series</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="Categories.php">Categories</a>
          </li>
        </ul>
      </div>
    </div>
    </div>
  </nav>
  <!-- END OF HEADER -->

  <!-- START OF THE MAIN CONTENT -->
  <main>
    <!-- START OF SECTION WALLPAPER -->
    <section id="section-wallpaper-movies" class="section-wallpaper">
      <!-- data-bs-ride="carousel" -->
      <div id="carouselExampleControls" class="carousel slide carousel-fade" data-bs-ride="carousel">

        <div class="carousel-inner">
          <div class="carousel-item active position-relative">

            <img src="../Images/VenomWallpaper.jpg" class="d-block w-100 wallpaper" alt="Sonic Wallpaper">

            <div class="wallpaper-description text-primary">

              <div class="wallpaper-left text-white d-flex flex-column">
                <div class="d-flex justify-content-center">
                  <img class="sonic-title-img" src="../Images/venomTitle.png" alt="Sonic Title">
                </div>
                <div class="wallpaper-ratings d-flex align-items-center justify-content-center my-2 my-md-4">
                  <div class="rating-year">
                    2024
                  </div>
                  <span class="mx-2 text-white-50">|</span>
                  <div class="rating-points d-flex">
                    16+
                  </div>
                  <span class="mx-2 text-white-50">|</span>
                  <div class="rating-gender text-nowrap">
                    Sci-Fi
                  </div>
                  <div class="wallpaper-line bg-secondary mx-3"></div>
                </div>
                <div class="rating-text">
                  <p>Eddie and Venom are on the run. Hunted by both of their worlds and with the net closing in, the duo
                    are forced into a devastating decision that will bring...</p>
                </div>
              </div>

              <div class="wallpaper-right mt-1">

                <div class="wallpaper-watch d-none d-md-block">
                  <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-outline-primary rounded-circle">
                      <i class="fa-solid fa-play"></i>
                    </button>
                    <p class="watch-now fs-2 fw-bold text-white m-0">WATCH NOW!</p>
                  </div>
                </div>

                <div class="wallpaper-watch-s d-md-none">
                  <button class="btn btn-primary rounded-5 watch-now-small">
                    <i class="fa-solid fa-play mx-1"></i>
                    <span>Watch Now</span>
                  </button>
                </div>

              </div>

            </div>

          </div>

          <div class="carousel-item">

            <img src="../Images/KravenWallpaper.jpg" class="d-block w-100 wallpaper" alt="Captain America Wallpaper">

            <div class="wallpaper-description text-primary">

              <div class="wallpaper-left text-white d-flex flex-column">
                <div class="d-flex justify-content-center">
                  <img class="sonic-title-img" src="../Images/kravenTitle.png" alt="Captain America Title">
                </div>
                <div class="wallpaper-ratings d-flex align-items-center my-2 my-md-4">
                  <div class="rating-year">
                    2024
                  </div>
                  <span class="mx-2 text-white-50">|</span>
                  <div class="rating-points d-flex">
                    16+
                  </div>
                  <span class="mx-2 text-white-50">|</span>
                  <div class="rating-gender text-nowrap">
                    Sci-Fi
                  </div>
                  <div class="wallpaper-line bg-secondary mx-3"></div>
                </div>
                <div class="rating-text d-block">
                  Kraven Kravinoff's complex relationship with his ruthless gangster father, Nikolai, starts him down a
                  path of vengeance with brutal consequences, motivating him to become not only the greatest...
                </div>
              </div>

              <div class="wallpaper-right mt-1">

                <div class="wallpaper-watch d-none d-md-block">
                  <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-outline-primary rounded-circle">
                      <i class="fa-solid fa-play"></i>
                    </button>
                    <p class="watch-now fs-2 fw-bold text-white m-0">WATCH NOW!</p>
                  </div>
                </div>

                <div class="wallpaper-watch-s d-md-none">
                  <button class="btn btn-primary rounded-5 watch-now-small">
                    <i class="fa-solid fa-play mx-1"></i>
                    <span>Watch Now</span>
                  </button>
                </div>

              </div>

            </div>

          </div>

          <div class="carousel-item">
            <img src="../Images/PandaPlanWallpaper.jpg" class="d-block w-100 wallpaper" alt="Moana Wallpaper">

            <div class="wallpaper-description text-primary">

              <div class="wallpaper-left text-white d-flex flex-column">
                <div class="d-flex justify-content-center">
                  <img class="sonic-title-img" src="../Images/pandaPlanTitle.png" alt="Moana Title">
                </div>
                <div class="wallpaper-ratings d-flex align-items-center my-2 my-md-4">
                  <div class="rating-year">
                    2024
                  </div>
                  <span class="mx-2 text-white-50">|</span>
                  <div class="rating-points d-flex">
                    16+
                  </div>
                  <span class="mx-2 text-white-50">|</span>
                  <div class="rating-gender text-nowrap">
                    Sci-Fi
                  </div>
                  <div class="wallpaper-line bg-secondary mx-3"></div>
                </div>
                <div class="rating-text d-block">
                  International action star Jackie Chan is invited to the adoption ceremony of a rare baby panda, but
                  after an international crime syndicate attempts to...
                </div>
              </div>

              <div class="wallpaper-right mt-3">

                <div class="wallpaper-watch d-none d-md-block">
                  <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-outline-primary rounded-circle">
                      <i class="fa-solid fa-play"></i>
                    </button>
                    <p class="watch-now fs-2 fw-bold text-white m-0">WATCH NOW!</p>
                  </div>
                </div>

                <div class="wallpaper-watch-s d-md-none">
                  <button class="btn btn-primary rounded-5 watch-now-small">
                    <i class="fa-solid fa-play mx-1"></i>
                    <span>Watch Now</span>
                  </button>
                </div>

              </div>

            </div>
          </div>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls"
          data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls"
          data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Next</span>
        </button>

      </div>

    </section>
    <!-- START OF SECTION WALLPAPER -->

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
  WHERE ms.category = 'Movie'
  ORDER BY ms.views DESC LIMIT 10
");
    $top10_rank = 1;
    $modals = "";
    ?>
    <section id="section-top-10" class="section-top-10 text-white ms-5">
      <div class="top-10-container">
        <div class="top-10-top d-flex align-items-center">
          <p class="top-10-top-text">TOP 10</p>
          <div class="ms-3 fs-2 movies-today-text">
            <p class="movies-text">MOVIES</p>
            <p class="today-text">TODAY</p>
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
                <p class="top-10-text"><?php echo $top10_rank; ?></p>
                <a href="#" data-bs-toggle="modal" data-bs-target="#modal-top10-<?php echo $movie_series_id ?>">
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

              // Modal for each movie
              $modals .= "
<div class='modal fade' id='modal-top10-{$movie_series_id}' tabindex='-1' aria-labelledby='exampleModalLabel-{$movie_series_id}' aria-hidden='true'>
  <div class='modal-dialog modal-dialog-centered modal-lg modal-dark border-3'>
    <div class='modal-content bg-dark modals'>
      <div class='modal-body'>
        <div class='modal-body-content'>
          <div class='modal-pic-container m-0 position-relative'>
            <video
              class=\"w-100 position-relative rounded-3 m-0 p-0 video-player\"
              autoplay
              muted
              loop
            >
              <source src=\"../DASHBOARD-HTML/MOVIE_SERIES_VIDEO/" . htmlspecialchars($top10_video) . "\" type=\"video/mp4\">
              Your browser does not support the video tag.
            </video>";
              if (!empty($top10_modal_poster_title)) {
                $modals .= "<img class='poster-title-img top10-title' src='../DASHBOARD-HTML/MOVIE_SERIES_TITLE/" . htmlspecialchars($top10_modal_poster_title) . "' alt=''>";
              } else {
                $modals .= "<p class='fw-bold fs-3 text-white position-absolute' style='top:10px; left:20px;'>" . htmlspecialchars($top10_title) . "</p>";
              }
              $modals .= "
            <a href=\"play-vid.php?video=" . urlencode($top10_video) . "&type=movie_series&id=" . $movie_series_id . "\" class=\"btn btn-light play-btn text-center fs-18 text-end position-absolute\" style=\"width: 120px;\">
              <i class=\"fa-solid fa-play me-1\"></i> Play
            </a>
            <button class=\"volume-control bg-transparent position-absolute\">
              <i class=\"fa-solid fa-volume-xmark volume-icon\"></i>
            </button>
            <button type='button' class='btn-close btn-close-white position-absolute modal-close-button' data-bs-dismiss='modal' aria-label='Close'></button>
          </div>
          <div class=\"row modal-body-text\" style=\"margin-left: 18px;\">
            <div class=\"col-8 text-wrap\">
              <div class=\"d-flex gap-2\">
                <p class='modal-text-rating mb-0'>" . htmlspecialchars($top10_date_released) . "</p>
                <p class='modal-text-rating mb-0'>" . htmlspecialchars($top10_duration) . "</p>
              </div>
              <div class=\"d-flex gap-2 align-items-center\">
                <p class='modal-text-rating p-1' style=\"border: 1px solid #f4fff8; width: fit-content;\">" . htmlspecialchars($top10_age_rating) . "+</p>
                <p class='modal-text-rating mb-0' style=\"transform: translateY(-8px);\">" . htmlspecialchars($top10_category) . "</p>
              </div>
              <p class='modal-text-rating'>" . htmlspecialchars($top10_description) . "</p>
            </div>
            <div class=\"col-4 ps-0 pe-4 text-wrap\">
              <p class=\"modal-text-rating text-wrap\" style=\"margin-top: 12px;\">
                <span class=\"text-wrap\" style=\"color: #888684;\">Cast: </span>
                " . htmlspecialchars($top10_cast) . ", more...
              </p>
              <p class=\"modal-text-rating text-wrap\" style=\"margin-top: -2px;\">
                <span class=\"text-wrap\" style=\"color: #888684;\">Genres: </span>
                {$genres}
              </p>
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

    <!-- START OF SECTION ADVENTURE MOVIES -->
    <section id="section-popular-adventure-movies" class="section-trending ms-md-5 ms-3">
      <div id="dropdown-popular-adventure" class="trending-this-week d-flex justify-content-between">
        <p class="trending-text text-white fs-24 fw-bold">Adventure Movies</p>
      </div>
      <div class="top10-featured-wrapper position-relative">
        <div class="prev-button-trending position-absolute"
          style="left: 0; top: 50%; transform: translateY(-50%); z-index: 400;">
          <button class="btn border-1 prev-chevron-btn-trending">
            <i class="fas fa-chevron-left fa-2xl text-white-50"></i>
          </button>
        </div>
        <div class="trending-images-container d-flex gap-3 position-relative">
          <?php
          $adventure_query = mysqli_query(
            $con,
            "SELECT ms.*, g1.genre_name AS genre_1, g2.genre_name AS genre_2, g3.genre_name AS genre_3
        FROM tbl_movie_series ms
        LEFT JOIN tbl_movie_series_genre g1 ON ms.genre_id1 = g1.genre_id
        LEFT JOIN tbl_movie_series_genre g2 ON ms.genre_id2 = g2.genre_id
        LEFT JOIN tbl_movie_series_genre g3 ON ms.genre_id3 = g3.genre_id
        WHERE (g1.genre_name = 'Adventure' OR g2.genre_name = 'Adventure' OR g3.genre_name = 'Adventure')
          AND ms.category = 'Movie'
        ORDER BY ms.date_released DESC
        LIMIT 8"
          );
          $adventure_modals = "";
          while ($row = mysqli_fetch_assoc($adventure_query)):
            $movie_series_id = $row['movie_series_id'];
            $title = $row['title'];
            $poster = $row['poster'];
            $video = $row['video'];
            $modal_poster_title = $row['modal_poster_title'];
            $duration = $row['duration'];
            $date_released = $row['date_released'];
            $age_rating = $row['age_rating'];
            $category = $row['category'];
            $genre_1 = $row['genre_1'];
            $genre_2 = $row['genre_2'];
            $genre_3 = $row['genre_3'];
            $cast = $row['cast'];
            $description = $row['description'];

            $genres = htmlspecialchars($genre_1);
            if (!empty($genre_2))
              $genres .= ', ' . htmlspecialchars($genre_2);
            if (!empty($genre_3))
              $genres .= ', ' . htmlspecialchars($genre_3);
            ?>
            <div class="trending-images position-relative pe-2" style="overflow: visible;">
              <a href="#" data-bs-toggle="modal" data-bs-target="#modal-adventure-<?php echo $movie_series_id ?>">
                <div class="trending-hover position-relative">
                  <img class="trending-img rounded-3"
                    src="../DASHBOARD-HTML/MOVIE_SERIES_TITLE/<?php echo htmlspecialchars($poster); ?>"
                    alt="<?php echo htmlspecialchars($title); ?>">
                  <i class="fa-solid fa-play play-button"></i>
                </div>
              </a>
            </div>
            <?php
            $adventure_modals .= "
      <div class='modal fade' id='modal-adventure-{$movie_series_id}' tabindex='-1' aria-labelledby='exampleModalLabel-adventure-{$movie_series_id}' aria-hidden='true'>
        <div class='modal-dialog modal-dialog-centered modal-lg modal-dark border-3'>
          <div class='modal-content bg-dark modals'>
            <div class='modal-body'>
              <div class='modal-body-content'>
                <div class='modal-pic-container m-0 position-relative'>
                  <video
                    class=\"w-100 position-relative rounded-3 m-0 p-0 video-player\"
                    autoplay
                    muted
                    loop
                  >
                    <source src=\"../DASHBOARD-HTML/MOVIE_SERIES_VIDEO/" . htmlspecialchars($video) . "\" type=\"video/mp4\">
                    Your browser does not support the video tag.
                  </video>";
            if (!empty($modal_poster_title)) {
              $adventure_modals .= "<img class='poster-title-img trend-title' src='../DASHBOARD-HTML/MOVIE_SERIES_TITLE/" . htmlspecialchars($modal_poster_title) . "' alt='' style='object-fit: cover; transform: scale(2.0) translate(36px, -14px);'>";
            } else {
              $adventure_modals .= "<p class='fw-bold fs-3 text-white position-absolute' style='top:10px; left:20px;'>" . htmlspecialchars($title) . "</p>";
            }
            $adventure_modals .= "
                  <a href=\"play-vid.php?video=" . urlencode($video) . "&type=movie_series&id=" . $movie_series_id . "\" class=\"btn btn-light play-btn text-center fs-18 text-end position-absolute\" style=\"width: 120px;\">
                    <i class=\"fa-solid fa-play me-1\"></i> Play
                  </a>
                  <button class=\"volume-control bg-transparent position-absolute\">
                    <i class=\"fa-solid fa-volume-xmark volume-icon\"></i>
                  </button>
                  <button type='button' class='btn-close btn-close-white position-absolute modal-close-button' data-bs-dismiss='modal' aria-label='Close'></button>
                </div>
                <div class=\"row modal-body-text\" style=\"margin-left: 18px;\">
                  <div class=\"col-8 text-wrap\">
                    <div class=\"d-flex gap-2\">
                      <p class='modal-text-rating mb-0'>" . htmlspecialchars($date_released) . "</p>
                      <p class='modal-text-rating mb-0'>" . htmlspecialchars($duration) . "</p>
                    </div>
                    <div class=\"d-flex gap-2 align-items-center\">
                      <p class='modal-text-rating p-1' style=\"border: 1px solid #f4fff8; width: fit-content;\">" . htmlspecialchars($age_rating) . "+</p>
                      <p class='modal-text-rating mb-0' style=\"transform: translateY(-8px);\">" . htmlspecialchars($category) . "</p>
                    </div>
                    <p class='modal-text-rating'>" . htmlspecialchars($description) . "</p>
                  </div>
                  <div class=\"col-4 ps-0 pe-4 text-wrap\">
                    <p class=\"modal-text-rating text-wrap\" style=\"margin-top: 12px;\">
                      <span class=\"text-wrap\" style=\"color: #888684;\">Cast: </span>
                      " . htmlspecialchars($cast) . ", more...
                    </p>
                    <p class=\"modal-text-rating text-wrap\" style=\"margin-top: -2px;\">
                      <span class=\"text-wrap\" style=\"color: #888684;\">Genres: </span>
                      {$genres}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      ";
          endwhile; ?>
        </div>
        <div class="next-button-trending position-absolute"
          style="right: 0; top: 50%; transform: translateY(-50%);z-index: 400;">
          <button class="btn border-1 next-chevron-btn-trending">
            <i class="fas fa-chevron-right fa-2xl text-white-50"></i>
          </button>
        </div>
      </div>
    </section>
    <?php echo $adventure_modals; ?>
    <!-- END OF SECTION ADVENTURE MOVIES -->

    <!-- START OF SECTION THRILLER -->
    <section id="section-thriller" class="ms-md-5 ms-3" style="margin-top: 60px;">
      <div class="action-movies-top d-flex justify-content-between">
        <p class="action-movies-text text-white fs-24 fw-bold">Thriller Movies</p>
      </div>
      <div class="top10-featured-wrapper position-relative">
        <div class="prev-button-comedy position-absolute"
          style="left: 0; top: 50%; transform: translateY(-50%); z-index: 400;">
          <button class="btn border-1 prev-chevron-btn-comedy">
            <i class="fas fa-chevron-left fa-2xl text-white-50"></i>
          </button>
        </div>
        <div class="action-images-container d-flex gap-3 position-relative">
          <?php
          $thriller_query = mysqli_query(
            $con,
            "SELECT ms.*, g1.genre_name AS genre_1, g2.genre_name AS genre_2, g3.genre_name AS genre_3
         FROM tbl_movie_series ms
         LEFT JOIN tbl_movie_series_genre g1 ON ms.genre_id1 = g1.genre_id
         LEFT JOIN tbl_movie_series_genre g2 ON ms.genre_id2 = g2.genre_id
         LEFT JOIN tbl_movie_series_genre g3 ON ms.genre_id3 = g3.genre_id
         WHERE (g1.genre_name = 'Thriller' OR g2.genre_name = 'Thriller' OR g3.genre_name = 'Thriller')
           AND ms.category = 'Movie'
         ORDER BY ms.date_released DESC
         LIMIT 7"
          );
          $thriller_modals = "";
          while ($row = mysqli_fetch_assoc($thriller_query)):
            $movie_series_id = $row['movie_series_id'];
            $thriller_title = $row['title'];
            $thriller_poster = $row['poster'];
            $thriller_video = $row['video'];
            $thriller_modal_poster_title = $row['modal_poster_title'];
            $thriller_duration = $row['duration'];
            $thriller_date_released = $row['date_released'];
            $thriller_age_rating = $row['age_rating'];
            $thriller_category = $row['category'];
            $thriller_genre_1 = $row['genre_1'];
            $thriller_genre_2 = $row['genre_2'];
            $thriller_genre_3 = $row['genre_3'];
            $thriller_cast = $row['cast'];
            $thriller_description = $row['description'];

            $genres = htmlspecialchars($thriller_genre_1);
            if (!empty($thriller_genre_2))
              $genres .= ', ' . htmlspecialchars($thriller_genre_2);
            if (!empty($thriller_genre_3))
              $genres .= ', ' . htmlspecialchars($thriller_genre_3);
            ?>
            <div class="comedy-images position-relative pe-2" style="overflow: visible;">
              <a href="#" data-bs-toggle="modal" data-bs-target="#modal-thriller-<?php echo $movie_series_id ?>">
                <div class="trending-hover position-relative">
                  <img class="action-movies-img rounded-3"
                    src="../DASHBOARD-HTML/MOVIE_SERIES_TITLE/<?php echo htmlspecialchars($thriller_poster); ?>"
                    alt="<?php echo htmlspecialchars($thriller_title); ?>">
                  <i class="fa-solid fa-play play-button-comedy"></i>
                </div>
              </a>
            </div>
            <?php
            $thriller_modals .= "
<div class='modal fade' id='modal-thriller-{$movie_series_id}' tabindex='-1' aria-labelledby='exampleModalLabel-thriller-{$movie_series_id}' aria-hidden='true'>
  <div class='modal-dialog modal-dialog-centered modal-lg modal-dark border-3'>
    <div class='modal-content bg-dark modals'>
      <div class='modal-body'>
        <div class='modal-body-content'>
          <div class='modal-pic-container m-0 position-relative'>
            <video
              class=\"w-100 position-relative rounded-3 m-0 p-0 video-player\"
              autoplay
              muted
              loop
            >
              <source src=\"../DASHBOARD-HTML/MOVIE_SERIES_VIDEO/" . htmlspecialchars($thriller_video) . "\" type=\"video/mp4\">
              Your browser does not support the video tag.
            </video>";
            if (!empty($thriller_modal_poster_title)) {
              $thriller_modals .= "<img class='poster-title-img trend-title' src='../DASHBOARD-HTML/MOVIE_SERIES_TITLE/" . htmlspecialchars($thriller_modal_poster_title) . "' alt='' style='object-fit: cover; transform: scale(2.0) translate(36px, -14px);'>";
            } else {
              $thriller_modals .= "<p class='fw-bold fs-3 text-white position-absolute' style='top:10px; left:20px;'>" . htmlspecialchars($thriller_title) . "</p>";
            }
            $thriller_modals .= "
            <a href=\"play-vid.php?video=" . urlencode($thriller_video) . "&type=movie_series&id=" . $movie_series_id . "\" class=\"btn btn-light play-btn text-center fs-18 text-end position-absolute\" style=\"width: 120px;\">
              <i class=\"fa-solid fa-play me-1\"></i> Play
            </a>
            <button class=\"volume-control bg-transparent position-absolute\">
              <i class=\"fa-solid fa-volume-xmark volume-icon\"></i>
            </button>
            <button type='button' class='btn-close btn-close-white position-absolute modal-close-button' data-bs-dismiss='modal' aria-label='Close'></button>
          </div>
          <div class=\"row modal-body-text\" style=\"margin-left: 18px;\">
            <div class=\"col-8 text-wrap\">
              <div class=\"d-flex gap-2\">
                <p class='modal-text-rating mb-0'>" . htmlspecialchars($thriller_date_released) . "</p>
                <p class='modal-text-rating mb-0'>" . htmlspecialchars($thriller_duration) . "</p>
              </div>
              <div class=\"d-flex gap-2 align-items-center\">
                <p class='modal-text-rating p-1' style=\"border: 1px solid #f4fff8; width: fit-content;\">" . htmlspecialchars($thriller_age_rating) . "+</p>
                <p class='modal-text-rating mb-0' style=\"transform: translateY(-8px);\">" . htmlspecialchars($thriller_category) . "</p>
              </div>
              <p class='modal-text-rating'>" . htmlspecialchars($thriller_description) . "</p>
            </div>
            <div class=\"col-4 ps-0 pe-4 text-wrap\">
              <p class=\"modal-text-rating text-wrap\" style=\"margin-top: 12px;\">
                <span class=\"text-wrap\" style=\"color: #888684;\">Cast: </span>
                " . htmlspecialchars($thriller_cast) . ", more...
              </p>
              <p class=\"modal-text-rating text-wrap\" style=\"margin-top: -2px;\">
                <span class=\"text-wrap\" style=\"color: #888684;\">Genres: </span>
                {$genres}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
";
          endwhile;
          ?>
        </div>
        <div class="next-button-comedy position-absolute"
          style="right: 0; top: 50%; transform: translateY(-50%);z-index: 400;">
          <button class="btn border-1 next-chevron-btn-comedy">
            <i class="fas fa-chevron-right fa-2xl text-white-50"></i>
          </button>
        </div>
      </div>
    </section>
    <?php echo $thriller_modals; ?>
    <!-- END OF SECTION THRILLER -->

    <!-- START OF SECTION SCI-FI  -->
    <section class="section-featured text-white ms-md-5 ms-3">
      <div class="featured-container">
        <div class="featured-top">
          <p class="featured-text">SCI-FI</p>
          <p class="featured-movies-text fs-2">MOVIES</p>
          <div class="top10-featured-wrapper position-relative">
            <div class="prev-button-featured position-absolute"
              style="left: 0; top: 50%; transform: translateY(-50%); z-index: 400;">
              <button class="btn border-1 prev-chevron-btn-featured">
                <i class="fas fa-chevron-left fa-2xl text-white-50"></i>
              </button>
            </div>
            <div class="featured-images-container d-flex gap-3 position-relative">
              <?php
              $scifi_query = mysqli_query(
                $con,
                "SELECT ms.*, g1.genre_name AS genre_1, g2.genre_name AS genre_2, g3.genre_name AS genre_3
             FROM tbl_movie_series ms
             LEFT JOIN tbl_movie_series_genre g1 ON ms.genre_id1 = g1.genre_id
             LEFT JOIN tbl_movie_series_genre g2 ON ms.genre_id2 = g2.genre_id
             LEFT JOIN tbl_movie_series_genre g3 ON ms.genre_id3 = g3.genre_id
             WHERE (g1.genre_name = 'Science fiction' OR g2.genre_name = 'Science fiction' OR g3.genre_name = 'Science fiction')
               AND ms.category = 'Movie'
             ORDER BY ms.date_released DESC
             LIMIT 8"
              );
              $scifi_modals = "";
              while ($row = mysqli_fetch_assoc($scifi_query)):
                $movie_series_id = $row['movie_series_id'];
                $scifi_title = $row['title'];
                $scifi_poster = $row['poster'];
                $scifi_video = $row['video'];
                $scifi_modal_poster_title = $row['modal_poster_title'];
                $scifi_duration = $row['duration'];
                $scifi_date_released = $row['date_released'];
                $scifi_age_rating = $row['age_rating'];
                $scifi_category = $row['category'];
                $scifi_genre_1 = $row['genre_1'];
                $scifi_genre_2 = $row['genre_2'];
                $scifi_genre_3 = $row['genre_3'];
                $scifi_cast = $row['cast'];
                $scifi_description = $row['description'];

                $genres = htmlspecialchars($scifi_genre_1);
                if (!empty($scifi_genre_2))
                  $genres .= ', ' . htmlspecialchars($scifi_genre_2);
                if (!empty($scifi_genre_3))
                  $genres .= ', ' . htmlspecialchars($scifi_genre_3);
                ?>
                <div class="featured-images position-relative pe-2" style="overflow: visible;">
                  <a href="#" data-bs-toggle="modal" data-bs-target="#modal-scifi-<?php echo $movie_series_id ?>">
                    <div class="trending-hover position-relative">
                      <img class="featured-img rounded-3"
                        src="../DASHBOARD-HTML/MOVIE_SERIES_TITLE/<?php echo htmlspecialchars($scifi_poster); ?>"
                        alt="<?php echo htmlspecialchars($scifi_title); ?>">
                      <i class="fa-solid fa-play play-button-featured"></i>
                    </div>
                  </a>
                </div>
                <?php
                $scifi_modals .= "
<div class='modal fade' id='modal-scifi-{$movie_series_id}' tabindex='-1' aria-labelledby='exampleModalLabel-scifi-{$movie_series_id}' aria-hidden='true'>
  <div class='modal-dialog modal-dialog-centered modal-lg modal-dark border-3'>
    <div class='modal-content bg-dark modals'>
      <div class='modal-body'>
        <div class='modal-body-content'>
          <div class='modal-pic-container m-0 position-relative'>
            <video
              class=\"w-100 position-relative rounded-3 m-0 p-0 video-player\"
              autoplay
              muted
              loop
            >
              <source src=\"../DASHBOARD-HTML/MOVIE_SERIES_VIDEO/" . htmlspecialchars($scifi_video) . "\" type=\"video/mp4\">
              Your browser does not support the video tag.
            </video>";
                if (!empty($scifi_modal_poster_title)) {
                  $scifi_modals .= "<img class='poster-title-img trend-title' src='../DASHBOARD-HTML/MOVIE_SERIES_TITLE/" . htmlspecialchars($scifi_modal_poster_title) . "' alt='' style='object-fit: cover; transform: scale(2.0) translate(36px, -14px);'>";
                } else {
                  $scifi_modals .= "<p class='fw-bold fs-3 text-white position-absolute' style='top:10px; left:20px;'>" . htmlspecialchars($scifi_title) . "</p>";
                }
                $scifi_modals .= "
            <a href=\"play-vid.php?video=" . urlencode($scifi_video) . "&type=movie_series&id=" . $movie_series_id . "\" class=\"btn btn-light play-btn text-center fs-18 text-end position-absolute\" style=\"width: 120px;\">
              <i class=\"fa-solid fa-play me-1\"></i> Play
            </a>
            <button class=\"volume-control bg-transparent position-absolute\">
              <i class=\"fa-solid fa-volume-xmark volume-icon\"></i>
            </button>
            <button type='button' class='btn-close btn-close-white position-absolute modal-close-button' data-bs-dismiss='modal' aria-label='Close'></button>
          </div>
          <div class=\"row modal-body-text\" style=\"margin-left: 18px;\">
            <div class=\"col-8 text-wrap\">
              <div class=\"d-flex gap-2\">
                <p class='modal-text-rating mb-0'>" . htmlspecialchars($scifi_date_released) . "</p>
                <p class='modal-text-rating mb-0'>" . htmlspecialchars($scifi_duration) . "</p>
              </div>
              <div class=\"d-flex gap-2 align-items-center\">
                <p class='modal-text-rating p-1' style=\"border: 1px solid #f4fff8; width: fit-content;\">" . htmlspecialchars($scifi_age_rating) . "+</p>
                <p class='modal-text-rating mb-0' style=\"transform: translateY(-8px);\">" . htmlspecialchars($scifi_category) . "</p>
              </div>
              <p class='modal-text-rating'>" . htmlspecialchars($scifi_description) . "</p>
            </div>
            <div class=\"col-4 ps-0 pe-4 text-wrap\">
              <p class=\"modal-text-rating text-wrap\" style=\"margin-top: 12px;\">
                <span class=\"text-wrap\" style=\"color: #888684;\">Cast: </span>
                " . htmlspecialchars($scifi_cast) . ", more...
              </p>
              <p class=\"modal-text-rating text-wrap\" style=\"margin-top: -2px;\">
                <span class=\"text-wrap\" style=\"color: #888684;\">Genres: </span>
                {$genres}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
";
              endwhile;
              ?>
            </div>
            <div class="next-button-featured position-absolute"
              style="right: 0; top: 50%; transform: translateY(-50%);z-index: 400;">
              <button class="btn border-1 next-chevron-btn-featured">
                <i class="fas fa-chevron-right fa-2xl text-white-50"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>
    <?php echo $scifi_modals; ?>
    <!-- END OF SECTION SCI-FI  -->

    <!-- START OF MOVIES 1 -->
    <section class="movies-1 mt-80">
        <img src="../Images/PrincessMononoke.jpg" class="d-block w-100 wallpaper-lotr bg-danger" alt="Sonic Wallpaper">

        <div
          class="wallpaper-description-tem d-flex flex-column justify-content-end align-items-end position-relative me-5">

          <div class="d-flex justify-content-center">
            <img class="tem-title-img position-relative mb-1" src="../Images/PrincessMononokeTitle.png"
              alt="Sonic Title">
          </div>

          <div class="rating-text-tem my-4 position-relative text-white">
            <p>Ashitaka, a prince of the disappearing Emishi people, is cursed by a demonized boar god and must journey
              to the west to find a cure. Along the way, he encounters San, a young human woman fighting</p>
          </div>

          <div class="watch-now-tem position-relative">
            <button class="btn btn-primary rounded-5 watch-now-small">
              <i class="fa-solid fa-play mx-1"></i>
              <span>Watch Now</span>
            </button>
          </div>

        </div>

    </section>
    <!-- END OF MOVIES 1 -->

    <!-- START OF SECTION DRAMA -->
    <section id="section-drama" class="ms-md-5 ms-3" style="margin-top: -120px;">
      <div class="action-movies-top d-flex justify-content-between">
        <p class="action-movies-text text-white fs-24 fw-bold">Drama Movies</p>
      </div>
      <div class="top10-featured-wrapper position-relative">
        <div class="prev-button-drama position-absolute"
          style="left: 0; top: 50%; transform: translateY(-50%); z-index: 400;">
          <button class="btn border-1 prev-chevron-btn-drama">
            <i class="fas fa-chevron-left fa-2xl text-white-50"></i>
          </button>
        </div>
        <div class="action-images-container d-flex gap-3 position-relative">
          <?php
          $drama_query = mysqli_query(
            $con,
            "SELECT ms.*, g1.genre_name AS genre_1, g2.genre_name AS genre_2, g3.genre_name AS genre_3
         FROM tbl_movie_series ms
         LEFT JOIN tbl_movie_series_genre g1 ON ms.genre_id1 = g1.genre_id
         LEFT JOIN tbl_movie_series_genre g2 ON ms.genre_id2 = g2.genre_id
         LEFT JOIN tbl_movie_series_genre g3 ON ms.genre_id3 = g3.genre_id
         WHERE (g1.genre_name = 'Drama' OR g2.genre_name = 'Drama' OR g3.genre_name = 'Drama')
           AND ms.category = 'Movie'
         ORDER BY ms.date_released DESC
         LIMIT 7"
          );
          $drama_modals = "";
          while ($row = mysqli_fetch_assoc($drama_query)):
            $movie_series_id = $row['movie_series_id'];
            $drama_title = $row['title'];
            $drama_poster = $row['poster'];
            $drama_video = $row['video'];
            $drama_modal_poster_title = $row['modal_poster_title'];
            $drama_duration = $row['duration'];
            $drama_date_released = $row['date_released'];
            $drama_age_rating = $row['age_rating'];
            $drama_category = $row['category'];
            $drama_genre_1 = $row['genre_1'];
            $drama_genre_2 = $row['genre_2'];
            $drama_genre_3 = $row['genre_3'];
            $drama_cast = $row['cast'];
            $drama_description = $row['description'];

            $genres = htmlspecialchars($drama_genre_1);
            if (!empty($drama_genre_2))
              $genres .= ', ' . htmlspecialchars($drama_genre_2);
            if (!empty($drama_genre_3))
              $genres .= ', ' . htmlspecialchars($drama_genre_3);
            ?>
            <div class="comedy-images position-relative pe-2" style="overflow: visible;">
              <a href="#" data-bs-toggle="modal" data-bs-target="#modal-drama-<?php echo $movie_series_id ?>">
                <div class="trending-hover position-relative">
                  <img class="action-movies-img rounded-3"
                    src="../DASHBOARD-HTML/MOVIE_SERIES_TITLE/<?php echo htmlspecialchars($drama_poster); ?>"
                    alt="<?php echo htmlspecialchars($drama_title); ?>">
                  <i class="fa-solid fa-play play-button-comedy"></i>
                </div>
              </a>
            </div>
            <?php
            $drama_modals .= "
<div class='modal fade' id='modal-drama-{$movie_series_id}' tabindex='-1' aria-labelledby='exampleModalLabel-drama-{$movie_series_id}' aria-hidden='true'>
  <div class='modal-dialog modal-dialog-centered modal-lg modal-dark border-3'>
    <div class='modal-content bg-dark modals'>
      <div class='modal-body'>
        <div class='modal-body-content'>
          <div class='modal-pic-container m-0 position-relative'>
            <video
              class=\"w-100 position-relative rounded-3 m-0 p-0 video-player\"
              autoplay
              muted
              loop
            >
              <source src=\"../DASHBOARD-HTML/MOVIE_SERIES_VIDEO/" . htmlspecialchars($drama_video) . "\" type=\"video/mp4\">
              Your browser does not support the video tag.
            </video>";
            if (!empty($drama_modal_poster_title)) {
              $drama_modals .= "<img class='poster-title-img trend-title' src='../DASHBOARD-HTML/MOVIE_SERIES_TITLE/" . htmlspecialchars($drama_modal_poster_title) . "' alt='' style='object-fit: cover; transform: scale(2.0) translate(36px, -14px);'>";
            } else {
              $drama_modals .= "<p class='fw-bold fs-3 text-white position-absolute' style='top:10px; left:20px;'>" . htmlspecialchars($drama_title) . "</p>";
            }
            $drama_modals .= "
            <a href=\"play-vid.php?video=" . urlencode($drama_video) . "&type=movie_series&id=" . $movie_series_id . "\" class=\"btn btn-light play-btn text-center fs-18 text-end position-absolute\" style=\"width: 120px;\">
              <i class=\"fa-solid fa-play me-1\"></i> Play
            </a>
            <button class=\"volume-control bg-transparent position-absolute\">
              <i class=\"fa-solid fa-volume-xmark volume-icon\"></i>
            </button>
            <button type='button' class='btn-close btn-close-white position-absolute modal-close-button' data-bs-dismiss='modal' aria-label='Close'></button>
          </div>
          <div class=\"row modal-body-text\" style=\"margin-left: 18px;\">
            <div class=\"col-8 text-wrap\">
              <div class=\"d-flex gap-2\">
                <p class='modal-text-rating mb-0'>" . htmlspecialchars($drama_date_released) . "</p>
                <p class='modal-text-rating mb-0'>" . htmlspecialchars($drama_duration) . "</p>
              </div>
              <div class=\"d-flex gap-2 align-items-center\">
                <p class='modal-text-rating p-1' style=\"border: 1px solid #f4fff8; width: fit-content;\">" . htmlspecialchars($drama_age_rating) . "+</p>
                <p class='modal-text-rating mb-0' style=\"transform: translateY(-8px);\">" . htmlspecialchars($drama_category) . "</p>
              </div>
              <p class='modal-text-rating'>" . htmlspecialchars($drama_description) . "</p>
            </div>
            <div class=\"col-4 ps-0 pe-4 text-wrap\">
              <p class=\"modal-text-rating text-wrap\" style=\"margin-top: 12px;\">
                <span class=\"text-wrap\" style=\"color: #888684;\">Cast: </span>
                " . htmlspecialchars($drama_cast) . ", more...
              </p>
              <p class=\"modal-text-rating text-wrap\" style=\"margin-top: -2px;\">
                <span class=\"text-wrap\" style=\"color: #888684;\">Genres: </span>
                {$genres}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
";
          endwhile;
          ?>
        </div>
        <div class="next-button-drama position-absolute"
          style="right: 0; top: 50%; transform: translateY(-50%);z-index: 400;">
          <button class="btn border-1 next-chevron-btn-drama">
            <i class="fas fa-chevron-right fa-2xl text-white-50"></i>
          </button>
        </div>
      </div>
    </section>
    <?php echo $drama_modals; ?>
    <!-- END OF SECTION DRAMA -->

    <!-- START OF MOVIES 2 -->
    <section class="movies-2">
      <div>

        <img src="../Images/Stellar.jpg" class="d-block w-100 wallpaper-lotr" alt="Sonic Wallpaper">

        <div
          class="wallpaper-description-lotr d-flex flex-column justify-content-end align-items-end position-relative me-5">

          <div class="d-flex justify-content-center">
            <img class="lotr-title-img position-relative mb-1" src="../Images/InterstellarTitle.png" alt="Sonic Title">
          </div>

          <div class="rating-text-lotr my-4 position-relative text-white">
            <p>The adventures of a group of explorers who make use of a newly discovered wormhole to surpass the
              limitations on human space travel and conquer the vast distances involved in an interstellar voyage.
            </p>
          </div>

          <div class="watch-now-lotr position-relative">
            <button class="btn btn-primary rounded-5 watch-now-small">
              <i class="fa-solid fa-play mx-1"></i>
              <span>Watch Now</span>
            </button>
          </div>

        </div>

      </div>

      </div>
    </section>
    <!-- END OF MOVIES 2 -->
  </main>
  <!-- END OF THE MAIN CONTENT -->

  <footer>
    <div class="footer text-white d-flex justify-content-between mx-5 align-items-center">
      <p class="footer-long-text">This site does not store any files on it's server, It only links to the media which is
        hosted on 3rd party services like YouTube, Dailymotion, Ok.ru, Vidsrc and more.</p>
      <p>© 2025 CineVault. All rights reserved.</p>
    </div>
  </footer>

</body>

<script src="header-scroll.js"></script>
<script src="video-player.js"></script>
<script src="poster-slide.js"></script>

</html>