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
  <title>Home</title>
  <!-- ========== BOOTSTRAP LINK ========== -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
  <link rel="stylesheet" href="feature-js.css">
  <link rel="stylesheet" href="FirstProject.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <!-- ========== FONT AWESOME LINK ========== -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="icon" href="../MOVIE-IMG/HEADER-IMG/CINEVAULT-LOGO.svg">

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
              <a href="FirstProject.php" class="text-decoration-none text-primary fw-bold me-2">Home</a>
            </li>
            <li>
              <a href="Movies.php" class="text-decoration-none text-white fw-bold mx-2">Movies</a>
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
          <div class="d-flex align-items-center" style="display: flex;">
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
                  <a href="?logout=1" class="dropdown-item d-flex align-items-center">
                    <i class="fa-solid fa-right-from-bracket me-2 fs-22"></i>
                    <span class="fs-18 d-inline-block">Log out</span>
                  </a>
                </li>
              </ul>
            </div>

          </div>
          <!-- ========== END PROFILE MENU ========== -->

          <button class="navbar-toggler text-light ms-2 d-lg-none" type="button" data-bs-toggle="offcanvas"
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
            <a class="nav-link text-primary" aria-current="page" href="FirstProject.php">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="Movies.php">Movies</a>

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
    <section class="section-wallpaper">
      <!-- data-bs-ride="carousel" -->
      <div id="carouselExampleControls" class="carousel slide carousel-fade" data-bs-ride="carousel">

        <div class="carousel-inner">
          <div class="carousel-item active position-relative">

            <img src="../Images\SonicWallpaper.jpg" class="d-block w-100 wallpaper" alt="Sonic Wallpaper">

            <div class="wallpaper-description text-primary">

              <div class="wallpaper-left text-white d-flex flex-column">
                <div class="d-flex justify-content-center">
                  <img class="sonic-title-img" src="../Images/SonicTitle.png" alt="Sonic Title">
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
                  <p>Sonic, Knuckles, and Tails reunite against a powerful new adversary, Shadow, a mysterious...</p>
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

            <img src="../Images/CaptainAmericaWallpaper.jpg" class="d-block w-100 wallpaper"
              alt="Captain America Wallpaper">

            <div class="wallpaper-description text-primary">

              <div class="wallpaper-left text-white d-flex flex-column">
                <div class="d-flex justify-content-center">
                  <img class="sonic-title-img" src="../Images/CATitle.png" alt="Captain America Title">
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
                  After meeting with neewly elected U.S President Thaddeus Ross, Sam finds himself in the middle of...
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
            <img src="../Images/MoanaWallpaper.jpg" class="d-block w-100 wallpaper" alt="Moana Wallpaper">

            <div class="wallpaper-description text-primary">

              <div class="wallpaper-left text-white d-flex flex-column">
                <div class="d-flex justify-content-center">
                  <img class="sonic-title-img" src="../Images/MoanaTitle.png" alt="Moana Title">
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
                  After receiving an unexpected call from here waryfinding ancestors, Moana journeys alongside...
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
          ";
              // --- EPISODES/SEASONS block for Series (unchanged) ---
              if ($top10_category === 'Series') {
                $seasons_result = mysqli_query($con, "SELECT * FROM tbl_movie_series_seasons WHERE movie_series_id = {$movie_series_id} ORDER BY season_number");
                $seasons = [];
                while ($season = mysqli_fetch_assoc($seasons_result)) {
                  $episodes_result = mysqli_query($con, "SELECT * FROM tbl_movie_series_episodes WHERE season_id = {$season['season_id']} ORDER BY episode_id");
                  $episodes = [];
                  while ($ep = mysqli_fetch_assoc($episodes_result)) {
                    $episodes[] = $ep;
                  }
                  $season['episodes'] = $episodes;
                  $seasons[] = $season;
                }
                $modals .= '
      <div class="col-12 d-flex justify-content-between align-items-center mx-4" style="width: 100%">
          <div>
              <p class="fs-3 mb-0" style="color: #f4fff8;">Episodes</p>
          </div>
          <div class="position-relative" style="min-width:180px; ">
              <select style="transform: translateX(-48px);" id="modal-season-select-' . $movie_series_id . '" class="form-control me-5">';
                foreach ($seasons as $s) {
                  $modals .= '<option value="season-' . $s['season_id'] . '">' . htmlspecialchars($s['season_title']) . '</option>';
                }
                $modals .= '</select>
              <i class="fa-solid fa-caret-down caret-season" style="position: absolute; bottom: 12px; right: 63px; color: #f4fff8; pointer-events: none;"></i>
          </div>
      </div>';
                foreach ($seasons as $idx => $s) {
                  $modals .= '<div class="col-12 mb-4 modal-episodes-block"
            id="modal-episodes-' . $movie_series_id . '-season-' . $s['season_id'] . '"
            style="' . ($idx === 0 ? '' : 'display:none;') . '">';
                  foreach ($s['episodes'] as $ep_idx => $ep) {
                    $modals .= '
                  <div class="mx-3 py-2 d-flex gap-2">
                      <div class="d-flex align-items-center gap-3 me-2 ms-4">
                          <p class="mb-0 fs-2 db-text-sec">' . ($ep_idx + 1) . '</p>';
                    if (!empty($ep['episode_video'])) {
                      $modals .= '
                          <a href="play-vid.php?video=' . urlencode($ep['episode_video']) . '&type=episode&id=' . $ep['episode_id'] . '">
                              <video
                                  class="position-relative rounded-3 m-0 p-0 video-player video-episode"
                                  muted
                                  style="width: 120px; height: 70px; object-fit: cover;">
                                  <source src="../DASHBOARD-HTML/MOVIE_SERIES_EPISODE/' . htmlspecialchars($ep['episode_video']) . '" type="video/mp4">
                                  Your browser does not support the video tag.
                              </video>
                          </a>';
                    }
                    $modals .= '
                      </div>
                      <div class="db-text-sec me-4">
                          <div class="d-flex align-items-center justify-content-between mb-2">
                              <p class="mb-0">' . htmlspecialchars($ep['episode_title']) . '</p>
                              <p class="mb-0">' . htmlspecialchars($ep['episode_duration']) . '</p>
                          </div>
                          <div>
                              <p class="mb-0 " style="font-size: 14px;">' . htmlspecialchars($ep['episode_description']) . '</p>
                          </div>
                      </div>
                  </div>';
                  }
                  if (empty($s['episodes'])) {
                    $modals .= '<div class="mx-4 py-2 db-text-sec">No episodes available for this season.</div>';
                  }
                  $modals .= '</div>';
                }
                $modals .= '
      <script>
      document.addEventListener("DOMContentLoaded", function () {
          var select = document.getElementById("modal-season-select-' . $movie_series_id . '");
          if (select) {
              select.addEventListener("change", function () {
                  var val = this.value;';
                foreach ($seasons as $s) {
                  $modals .= '
                  document.getElementById("modal-episodes-' . $movie_series_id . '-season-' . $s['season_id'] . '").style.display = (val === "season-' . $s['season_id'] . '") ? "" : "none";';
                }
                $modals .= '
              });
          }
      });
      </script>
      ';
              }
              $modals .= "
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


    <!-- START OF SECTION ROMANCE -->
    <section class="section-trending ms-md-5 ms-3">
      <div class="trending-this-week">
        <p class="trending-text text-white fs-24 fw-bold">Romance</p>
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
          $trend_query = mysqli_query(
            $con,
            "SELECT ms.*, g1.genre_name AS genre_1, g2.genre_name AS genre_2, g3.genre_name AS genre_3
            FROM tbl_movie_series ms
            LEFT JOIN tbl_movie_series_genre g1 ON ms.genre_id1 = g1.genre_id
            LEFT JOIN tbl_movie_series_genre g2 ON ms.genre_id2 = g2.genre_id
            LEFT JOIN tbl_movie_series_genre g3 ON ms.genre_id3 = g3.genre_id
            WHERE g1.genre_name = 'Romance' OR g2.genre_name = 'Romance' OR g3.genre_name = 'Romance'
            ORDER BY ms.date_released DESC
            LIMIT 7"
          );
          $trend_modals = "";
          $trend_rank = 1;
          while ($row = mysqli_fetch_assoc($trend_query)):
            $movie_series_id = $row['movie_series_id'];
            $trend_title = $row['title'];
            $trend_poster = $row['poster'];
            $trend_video = $row['video'];
            $trend_modal_poster_title = $row['modal_poster_title'];
            $trend_duration = $row['duration'];
            $trend_date_released = $row['date_released'];
            $trend_age_rating = $row['age_rating'];
            $trend_category = $row['category'];
            $trend_genre_1 = $row['genre_1'];
            $trend_genre_2 = $row['genre_2'];
            $trend_genre_3 = $row['genre_3'];
            $trend_cast = $row['cast'];
            $trend_description = $row['description'];

            $genres = htmlspecialchars($trend_genre_1);
            if (!empty($trend_genre_2))
              $genres .= ', ' . htmlspecialchars($trend_genre_2);
            if (!empty($trend_genre_3))
              $genres .= ', ' . htmlspecialchars($trend_genre_3);
            ?>
            <div class="trending-images position-relative pe-2" style="overflow: visible;">
              <a href="#" data-bs-toggle="modal" data-bs-target="#modal-romance-<?php echo $movie_series_id ?>">
                <div class="trending-hover position-relative">
                  <img class="trending-img rounded-3"
                    src="../DASHBOARD-HTML/MOVIE_SERIES_TITLE/<?php echo htmlspecialchars($trend_poster); ?>"
                    alt="<?php echo htmlspecialchars($trend_title); ?>">
                  <i class="fa-solid fa-play play-button"></i>
                </div>
              </a>
            </div>
            <?php
            $trend_modals .= "
      <div class='modal fade' id='modal-romance-{$movie_series_id}' tabindex='-1' aria-labelledby='exampleModalLabel-romance-{$movie_series_id}' aria-hidden='true'>
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
                    <source src=\"../DASHBOARD-HTML/MOVIE_SERIES_VIDEO/" . htmlspecialchars($trend_video) . "\" type=\"video/mp4\">
                    Your browser does not support the video tag.
                  </video>"; 
            if (!empty($trend_modal_poster_title)) {
              $trend_modals .= "<img class='poster-title-img trend-title' src='../DASHBOARD-HTML/MOVIE_SERIES_TITLE/" . htmlspecialchars($trend_modal_poster_title) . "' alt='' style='object-fit: cover; transform: scale(2.0) translate(36px, -14px);'>";
            } else {
              $trend_modals .= "<p class='fw-bold fs-3 text-white position-absolute' style='top:10px; left:20px;'>" . htmlspecialchars($trend_title) . "</p>";
            }
            $trend_modals .= "
                  <a href=\"play-vid.php?video=" . urlencode($trend_video) . "&type=movie_series&id=" . $movie_series_id . "\" class=\"btn btn-light play-btn text-center fs-18 text-end position-absolute\" style=\"width: 120px;\">
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
                      <p class='modal-text-rating mb-0'>" . htmlspecialchars($trend_date_released) . "</p>
                      <p class='modal-text-rating mb-0'>" . htmlspecialchars($trend_duration) . "</p>
                    </div>
                    <div class=\"d-flex gap-2 align-items-center\">
                      <p class='modal-text-rating p-1' style=\"border: 1px solid #f4fff8; width: fit-content;\">" . htmlspecialchars($trend_age_rating) . "+</p>
                      <p class='modal-text-rating mb-0' style=\"transform: translateY(-8px);\">" . htmlspecialchars($trend_category) . "</p>
                    </div>
                    <p class='modal-text-rating'>" . htmlspecialchars($trend_description) . "</p>
                  </div>
                  <div class=\"col-4 ps-0 pe-4 text-wrap\">
                    <p class=\"modal-text-rating text-wrap\" style=\"margin-top: 12px;\">
                      <span class=\"text-wrap\" style=\"color: #888684;\">Cast: </span>
                      " . htmlspecialchars($trend_cast) . ", more...
                    </p>
                    <p class=\"modal-text-rating text-wrap\" style=\"margin-top: -2px;\">
                      <span class=\"text-wrap\" style=\"color: #888684;\">Genres: </span>
                      {$genres}
                    </p>
                  </div>
                </div>
              ";
            // --- Series/episodes code (unchanged) ---
            if ($trend_category === 'Series') {
              $seasons_result = mysqli_query($con, "SELECT * FROM tbl_movie_series_seasons WHERE movie_series_id = {$movie_series_id} ORDER BY season_number");
              $seasons = [];
              while ($season = mysqli_fetch_assoc($seasons_result)) {
                $episodes_result = mysqli_query($con, "SELECT * FROM tbl_movie_series_episodes WHERE season_id = {$season['season_id']} ORDER BY episode_id");
                $episodes = [];
                while ($ep = mysqli_fetch_assoc($episodes_result)) {
                  $episodes[] = $ep;
                }
                $season['episodes'] = $episodes;
                $seasons[] = $season;
              }
              $trend_modals .= '
            <div class="col-12 d-flex justify-content-between align-items-center mx-4" style="width: 100%">
                <div>
                    <p class="fs-3 mb-0" style="color: #f4fff8;">Episodes</p>
                </div>
                <div class="position-relative" style="min-width:180px; ">
                    <select style="transform: translateX(-48px);" id="modal-season-select-romance-' . $movie_series_id . '" class="form-control me-5">';
              foreach ($seasons as $s) {
                $trend_modals .= '<option value="season-' . $s['season_id'] . '">' . htmlspecialchars($s['season_title']) . '</option>';
              }
              $trend_modals .= '</select>
                    <i class="fa-solid fa-caret-down caret-season" style="position: absolute; bottom: 12px; right: 63px; color: #f4fff8; pointer-events: none;"></i>
                </div>
            </div>';
              foreach ($seasons as $idx => $s) {
                $trend_modals .= '<div class="col-12 mb-4 modal-episodes-block"
                id="modal-episodes-romance-' . $movie_series_id . '-season-' . $s['season_id'] . '"
                style="' . ($idx === 0 ? '' : 'display:none;') . '">';
                foreach ($s['episodes'] as $ep_idx => $ep) {
                  $trend_modals .= '
                        <div class="mx-3 py-2 d-flex gap-2">
                            <div class="d-flex align-items-center gap-3 me-2 ms-4">
                                <p class="mb-0 fs-2 db-text-sec">' . ($ep_idx + 1) . '</p>';
                  if (!empty($ep['episode_video'])) {
                    $trend_modals .= '
                                <a href="play-vid.php?video=' . urlencode($ep['episode_video']) . '&type=episode&id=' . $ep['episode_id'] . '">
                                    <video
                                        class="position-relative rounded-3 m-0 p-0 video-player video-episode"
                                        muted
                                        style="width: 120px; height: 70px; object-fit: cover;">
                                        <source src="../DASHBOARD-HTML/MOVIE_SERIES_EPISODE/' . htmlspecialchars($ep['episode_video']) . '" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                </a>';
                  }
                  $trend_modals .= '
                            </div>
                            <div class="db-text-sec me-4">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <p class="mb-0">' . htmlspecialchars($ep['episode_title']) . '</p>
                                    <p class="mb-0">' . htmlspecialchars($ep['episode_duration']) . '</p>
                                </div>
                                <div>
                                    <p class="mb-0 " style="font-size: 14px;">' . htmlspecialchars($ep['episode_description']) . '</p>
                                </div>
                            </div>
                        </div>';
                }
                if (empty($s['episodes'])) {
                  $trend_modals .= '<div class="mx-4 py-2 db-text-sec">No episodes available for this season.</div>';
                }
                $trend_modals .= '</div>';
              }
              $trend_modals .= '
            <script>
            document.addEventListener("DOMContentLoaded", function () {
                var select = document.getElementById("modal-season-select-romance-' . $movie_series_id . '");
                if (select) {
                    select.addEventListener("change", function () {
                        var val = this.value;';
              foreach ($seasons as $s) {
                $trend_modals .= '
                        document.getElementById("modal-episodes-romance-' . $movie_series_id . '-season-' . $s['season_id'] . '").style.display = (val === "season-' . $s['season_id'] . '") ? "" : "none";';
              }
              $trend_modals .= '
                    });
                }
            });
            </script>
            ';
            }
            $trend_modals .= "
              </div>
            </div>
          </div>
        </div>
      </div>
      ";
            $trend_rank++;
          endwhile;
          ?>
        </div>
        <div class="next-button-trending position-absolute"
          style="right: 0; top: 50%; transform: translateY(-50%);z-index: 400;">
          <button class="btn border-1 next-chevron-btn-trending">
            <i class="fas fa-chevron-right fa-2xl text-white-50"></i>
          </button>
        </div>
      </div>
    </section>
    <?php echo $trend_modals; ?>
    <!-- END OF SECTION ROMANCE -->

    <!-- START OF SECTION COMEDY -->
    <section class="section-action-movies ms-md-5 ms-3">
      <div class="action-movies-top d-flex justify-content-between">
        <p class="action-movies-text text-white fs-24 fw-bold">Comedy</p>

      </div>
      <div class="action-images-container d-flex gap-3 position-relative">
        <div class="comedy-images">
          <img class="action-movies-img rounded-3" src="../Movie Web/Comedy/2jIi55JtYKJTL1km8qHMuUilOWo.jpg" alt="">
          <i class="fa-solid fa-play play-button-comedy"></i>
        </div>
        <div class="comedy-images">
          <img class="action-movies-img rounded-3" src="../Movie Web/Comedy/2Wmmu1MkqxJ48J7aySET9EKEjXz.jpg" alt="">
          <i class="fa-solid fa-play play-button-comedy"></i>
        </div>
        <div class="comedy-images">
          <img class="action-movies-img rounded-3" src="../Movie Web/Comedy/3ZIPTvMnzI5ThmdGeEYQFxJV5Sg.jpg" alt="">
          <i class="fa-solid fa-play play-button-comedy"></i>
        </div>
        <div class="comedy-images">
          <img class="action-movies-img rounded-3" src="../Movie Web/Comedy/gFEHva8Csx18hMGJJZ6gi4sFSKR.jpg" alt="">
          <i class="fa-solid fa-play play-button-comedy"></i>
        </div>
        <div class="comedy-images">
          <img class="action-movies-img rounded-3" src="../Movie Web/Comedy/lgD4j9gUGmMckZpWWRJjorWqGVT.jpg" alt="">
          <i class="fa-solid fa-play play-button-comedy"></i>
        </div>
        <div class="comedy-images">
          <img class="action-movies-img rounded-3" src="../Movie Web/Comedy/nEwybsfZBSPjcZamPpKDTaTI5g.jpg" alt="">
          <i class="fa-solid fa-play play-button-comedy"></i>
        </div>
        <div class="comedy-images">
          <img class="action-movies-img rounded-3" src="../Movie Web/Comedy/oQxrvUhP3ycwnlxIrIMQ9Z3kleq.jpg" alt="">
          <i class="fa-solid fa-play play-button-comedy"></i>
        </div>
        <div class="comedy-images">
          <img class="action-movies-img rounded-3" src="../Movie Web/Comedy/tK855sva67LB7opmPW4JHn1c5VI.jpg" alt="">
          <i class="fa-solid fa-play play-button-comedy"></i>
        </div>

        <div class="next-button-action position-absolute">
          <button class="btn border-1 next-chevron-btn">
            <i class="fas fa-chevron-right fa-xl text-white-50"></i>
          </button>
        </div>
      </div>
    </section>
    <!-- END OF SECTION ACTION MOVIES -->

    <!-- START OF FEATURED -->
    <section class="section-featured text-white ms-md-5 ms-3">
      <div class="featured-container">
        <div class="featured-top">
          <p class="featured-text">ACTION</p>
          <p class="featured-movies-text fs-2">MOVIES/SERIES</p>
          <div class="featured-images-container d-flex gap-3 position-relative">
            <div class="featured-images position-relative">
              <img class="featured-img rounded-3" src="../Movie Web/Freatured/2cxhvwyEwRlysAmRH4iodkvo0z5.jpg" alt="">
              <i class="fa-solid fa-play play-button-featured"></i>
            </div>
            <div class="featured-images position-relative">
              <img class="featured-img rounded-3" src="../Movie Web/Freatured/3L3l6LsiLGHkTG4RFB2aBA6BttB.jpg" alt="">
              <i class="fa-solid fa-play play-button-featured"></i>
            </div>
            <div class="featured-images position-relative">
              <img class="featured-img rounded-3" src="../Movie Web/Trending This Week/CA.jpg" alt="">
              <i class="fa-solid fa-play play-button-featured"></i>
            </div>
            <div class="featured-images position-relative">
              <img class="featured-img rounded-3" src="../Movie Web/Freatured/4cR3hImKd78dSs652PAkSAyJ5Cx.jpg" alt="">
              <i class="fa-solid fa-play play-button-featured"></i>
            </div>
            <div class="featured-images position-relative">
              <img class="featured-img rounded-3" src="../Movie Web/Freatured/4Qksv87C57O0IWA8sT2KwxZ4fXh.jpg" alt="">
              <i class="fa-solid fa-play play-button-featured"></i>
            </div>
            <div class="featured-images position-relative">
              <img class="featured-img rounded-3" src="../Movie Web/Freatured/6m435uh40N7Gzfbd69ttp6W0sdR.jpg" alt="">
              <i class="fa-solid fa-play play-button-featured"></i>
            </div>
            <div class="featured-images position-relative">
              <img class="featured-img rounded-3" src="../Movie Web/Freatured/A9ENz6d4lC3UYOX8Z1gJwDEo1sM.jpg" alt="">
              <i class="fa-solid fa-play play-button-featured"></i>
            </div>
            <div class="featured-images position-relative">
              <img class="featured-img rounded-3" src="../Movie Web/Freatured/ajb1rMiorchfRemYHZCkbV9DBg6.jpg" alt="">
              <i class="fa-solid fa-play play-button-featured"></i>
            </div>

            <div class="next-button-featured position-absolute">
              <button class="btn border-1 next-chevron-btn">
                <i class="fas fa-chevron-right fa-xl text-white-50"></i>
              </button>
            </div>
          </div>
        </div>
    </section>
    <!-- END OF SECTION FEATURED -->

    <!-- START OF MOVIES 1 -->
    <section class="movies-1 mt-80">
      <div>

        <img src="../Images/ThereAreMurderersWallpaper.jpg" class="d-block w-100 wallpaper-lotr bg-danger"
          alt="Sonic Wallpaper">

        <div
          class="wallpaper-description-tem d-flex flex-column justify-content-end align-items-end position-relative me-5">

          <div class="d-flex justify-content-center">
            <img class="tem-title-img position-relative mb-1" src="../Images/ThereAreMurdersTitle.png"
              alt="Sonic Title">
          </div>

          <div class="rating-text-tem my-4 position-relative text-white">
            <p>A Stockholm detective under internal investigation heads to a ski resort to unwind, until a young girl's
              disappearance compels her back to work.</p>
          </div>

          <div class="watch-now-tem position-relative">
            <button class="btn btn-primary rounded-5 watch-now-small">
              <i class="fa-solid fa-play mx-1"></i>
              <span>Watch Now</span>
            </button>
          </div>

        </div>

      </div>

      </div>
    </section>
    <!-- END OF MOVIES 1 -->

    <!-- START OF RECOMMENDED -->
    <section class="section-popular text-white ms-md-5 ms-3">
      <div class="popular-container mt-4">
        <div class="popular-top mb-4">
          <p class="action-movies-text text-white fs-24 fw-bold">Recommended for You</p>
        </div>
        <div class="popular-images-container d-flex gap-3 position-relative">
          <div class="popular-images position-relative">
            <img class="popular-img rounded-3" src="../Movie Web/Freatured/moana.jpg" alt="">
            <i class="fa-solid fa-play play-button-popular"></i>
          </div>
          <div class="popular-images position-relative">
            <img class="popular-img rounded-3" src="../Movie Web/Freatured/nrlfJoxP1EkBVE9pU62L287Jl4D.jpg" alt="">
            <i class="fa-solid fa-play play-button-popular"></i>
          </div>
          <div class="popular-images position-relative">
            <img class="popular-img rounded-3" src="../Movie Web/Freatured/ttN5D6GKOwKWHmCzDGctAvaNMAi.jpg" alt="">
            <i class="fa-solid fa-play play-button-popular"></i>
          </div>
          <div class="popular-images position-relative">
            <img class="popular-img rounded-3" src="../Movie Web/Freatured/TVvIyCsFCmLk9MRLbAZi4X8f32.jpg" alt="">
            <i class="fa-solid fa-play play-button-popular"></i>
          </div>
          <div class="popular-images position-relative">
            <img class="popular-img rounded-3" src="../Movie Web/Freatured/uDW5eeFUYp1vaU2ymEdVBG6g7iq.jpg" alt="">
            <i class="fa-solid fa-play play-button-popular"></i>
          </div>
          <div class="popular-images position-relative">
            <img class="popular-img rounded-3" src="../Movie Web/Freatured/v313aUGmMNj6yNveaiQXysBmjVS.jpg" alt="">
            <i class="fa-solid fa-play play-button-popular"></i>
          </div>
          <div class="popular-images position-relative">
            <img class="popular-img rounded-3" src="../Movie Web/Freatured/vGXptEdgZIhPg3cGlc7e8sNPC2e.jpg" alt="">
            <i class="fa-solid fa-play play-button-popular"></i>
          </div>

          <div class="next-button-featured position-absolute">
            <button class="btn border-1 next-chevron-btn">
              <i class="fas fa-chevron-right fa-xl text-white-50"></i>
            </button>
          </div>
        </div>
      </div>

    </section>
    <!-- END OF RECOMMENDED -->

    <!-- START OF MOVIES 2 -->
    <section class="movies-2 mt-80">
      <div>

        <img src="../Images/LordOfTheRingsWallpaper.jpg" class="d-block w-100 wallpaper-lotr" alt="Sonic Wallpaper">

        <div
          class="wallpaper-description-lotr d-flex flex-column justify-content-end align-items-end position-relative me-5">

          <div class="d-flex justify-content-center">
            <img class="lotr-title-img position-relative mb-1" src="../Images/LordOfTheRingsTitle.png"
              alt="Sonic Title">
          </div>

          <div class="rating-text-lotr my-4 position-relative text-white">
            <p>Frodo Baggins and the other members of the Fellowship continue on their sacred quest to destroy the One
              Ring--but on separate paths.</p>
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