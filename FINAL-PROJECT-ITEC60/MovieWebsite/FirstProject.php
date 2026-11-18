<?php
include 'CineVault-header.php';

/* ---------- Favorite genres (asked once per account) ---------- */
$member_id = (int) ($_SESSION['user_id'] ?? 0);

// Genres that actually have titles, with how many, for the picker.
$picker_genres = [];
$pg = mysqli_query($con, "
  SELECT g.genre_id, g.genre_name, COUNT(m.movie_series_id) AS total
  FROM tbl_movie_series_genre g
  JOIN tbl_movie_series m ON g.genre_id IN (m.genre_id1, m.genre_id2, m.genre_id3)
  GROUP BY g.genre_id, g.genre_name
  ORDER BY total DESC, g.genre_name
");
while ($r = mysqli_fetch_assoc($pg)) {
  $picker_genres[(int) $r['genre_id']] = $r;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['skip_genres'])) {
  $_SESSION['genre_picker_skipped'] = true; // ask again next time they sign in
  header('Location: FirstProject.php');
  exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_genres'])) {
  $chosen = array_values(array_unique(array_map('intval', (array) ($_POST['genre_ids'] ?? []))));
  $chosen = array_values(array_filter($chosen, fn($id) => isset($picker_genres[$id])));
  if (count($chosen) === 3) {
    $csv = implode(',', $chosen);
    $stmt = $con->prepare("UPDATE tbl_signup_acc SET favorite_genres = ? WHERE signup_id = ?");
    $stmt->bind_param("si", $csv, $member_id);
    $stmt->execute();
    $stmt->close();
    unset($_SESSION['genre_picker_skipped']);
  }
  header('Location: FirstProject.php#recommended');
  exit;
}

$stmt = $con->prepare("SELECT first_name, favorite_genres FROM tbl_signup_acc WHERE signup_id = ?");
$stmt->bind_param("i", $member_id);
$stmt->execute();
$member = $stmt->get_result()->fetch_assoc() ?: [];
$stmt->close();

$favorite_ids = array_values(array_filter(array_map('intval', explode(',', (string) ($member['favorite_genres'] ?? '')))));
$favorite_names = [];
foreach ($favorite_ids as $fid) {
  if (isset($picker_genres[$fid])) {
    $favorite_names[] = $picker_genres[$fid]['genre_name'];
  }
}
$show_genre_picker = !$favorite_ids && empty($_SESSION['genre_picker_skipped']) && $picker_genres;

// Icon for each genre in the picker.
function cv_genre_icon($name)
{
  $map = [
    'action' => 'fa-explosion', 'adventure' => 'fa-compass', 'animation' => 'fa-wand-magic-sparkles',
    'comedy' => 'fa-face-laugh-squint', 'crime' => 'fa-user-secret', 'documentary' => 'fa-video',
    'drama' => 'fa-masks-theater', 'family' => 'fa-people-roof', 'fantasy' => 'fa-dragon',
    'history' => 'fa-landmark', 'terror' => 'fa-ghost', 'horror' => 'fa-ghost', 'music' => 'fa-music',
    'mystery' => 'fa-magnifying-glass', 'science fiction' => 'fa-rocket', 'cinema tv' => 'fa-tv',
    'thriller' => 'fa-skull', 'war' => 'fa-person-rifle', 'western' => 'fa-hat-cowboy',
    'news' => 'fa-newspaper', 'reality' => 'fa-camera', 'romance' => 'fa-heart',
    'sci-fi & fantasy' => 'fa-user-astronaut', 'soap' => 'fa-tv', 'talk' => 'fa-microphone',
    'war & politics' => 'fa-flag', 'kids' => 'fa-child-reaching',
  ];
  return $map[strtolower(trim($name))] ?? 'fa-film';
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
  <link rel="stylesheet" href="FirstProject.css?v=<?php echo CV_ASSET_VERSION; ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    .genre-btn-modal.selected {
      background: #607BEC !important;
      color: #fff !important;
      border-color: #607BEC !important;
    }

    .genre-btn-modal.shake {
      animation: shake 0.3s;
    }

    @keyframes shake {
      0% {
        transform: translateX(0);
      }

      25% {
        transform: translateX(-5px);
      }

      50% {
        transform: translateX(5px);
      }

      75% {
        transform: translateX(-5px);
      }

      100% {
        transform: translateX(0);
      }
    }
  </style>

<body class="bg-dark">

  <!-- START OF THE MAIN CONTENT -->
  <main>

    <!-- START OF SECTION WALLPAPER -->
    <section class="section-wallpaper">
      <div id="carouselExampleControls" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <div class="carousel-inner">
          <?php
          $carousel_modals = "";
          $carousel_query = mysqli_query($con, "
        SELECT ms.*, g1.genre_name AS genre_1, g2.genre_name AS genre_2, g3.genre_name AS genre_3
        FROM tbl_movie_series ms
        LEFT JOIN tbl_movie_series_genre g1 ON ms.genre_id1 = g1.genre_id
        LEFT JOIN tbl_movie_series_genre g2 ON ms.genre_id2 = g2.genre_id
        LEFT JOIN tbl_movie_series_genre g3 ON ms.genre_id3 = g3.genre_id
        WHERE ms.feature_slot LIKE 'home-%'
        ORDER BY ms.feature_slot
        LIMIT 3
      ");
          $active = true;
          while ($row = mysqli_fetch_assoc($carousel_query)):
            $movie_series_id = $row['movie_series_id'];
            $desc = htmlspecialchars($row['description']);
            $maxlen = 110;
            if (function_exists('mb_substr')) {
              if (mb_strlen($desc) > $maxlen) {
                $desc = mb_substr($desc, 0, $maxlen) . '...';
              }
            } else {
              if (strlen($desc) > $maxlen) {
                $desc = substr($desc, 0, $maxlen) . '...';
              }
            }
            $genres = htmlspecialchars($row['genre_1']);
            if (!empty($row['genre_2']))
              $genres .= ', ' . htmlspecialchars($row['genre_2']);
            if (!empty($row['genre_3']))
              $genres .= ', ' . htmlspecialchars($row['genre_3']);
            ?>
            <div class="carousel-item<?php if ($active) {
              echo ' active';
              $active = false;
            } ?> position-relative">
              <img src="../DASHBOARD-HTML/MOVIE_SERIES_TITLE/<?php echo htmlspecialchars($row['backdrop'] ?: $row['poster']); ?>"
                class="d-block w-100 wallpaper" alt="<?php echo htmlspecialchars($row['title']); ?> Wallpaper">
              <div class="wallpaper-description text-primary">
                <div class="wallpaper-left text-white d-flex flex-column">
                  <div class="d-flex justify-content-center">
                    <img class="sonic-title-img"
                      src="../DASHBOARD-HTML/MOVIE_SERIES_TITLE/<?php echo htmlspecialchars($row['modal_poster_title']); ?>"
                      alt="<?php echo htmlspecialchars($row['title']); ?> Title">
                  </div>
                  <div class="wallpaper-ratings d-flex align-items-center justify-content-center my-2 my-md-4">
                    <div class="rating-year">
                      <?php echo htmlspecialchars($row['date_released']); ?>
                    </div>
                    <span class="mx-2 text-white-50">|</span>
                    <div class="rating-points d-flex">
                      <?php echo htmlspecialchars($row['age_rating']); ?>+
                    </div>
                    <span class="mx-2 text-white-50">|</span>
                    <div class="rating-gender text-nowrap">
                      <?php echo htmlspecialchars($row['category']); ?>
                    </div>
                    <div class="wallpaper-line bg-secondary mx-3"></div>
                  </div>
                  <div class="rating-text cv-full-desc" tabindex="0" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="cv-desc-tooltip" data-bs-title="<?php echo htmlspecialchars($row['description'], ENT_QUOTES); ?>">
                    <?php echo $desc; ?>
                  </div>
                </div>
                <div class="wallpaper-right mt-1">
                  <div class="wallpaper-watch d-none d-md-block">
                    <div class="d-flex align-items-center gap-3">
                      <!-- More Info Button with transform style -->
                      <button class="btn d-flex db-text-sec text-nowrap align-items-center rounded-2"
                        style="background: rgba(255,255,255,0.15); border: none; width: fit-content; font-size: 16px; height: fit-content; transform: translate(280px, 30px); z-index: 300;"
                        data-bs-toggle="modal" data-bs-target="#modal-carousel-<?php echo $movie_series_id; ?>">
                        <i class="fa-solid fa-info-circle me-1"></i> More Info
                      </button>
                      <button class="btn btn-outline-primary rounded-circle"
                        onclick="window.location.href='play-vid.php?video=<?php echo urlencode($row['video']); ?>&type=movie_series&id=<?php echo $row['movie_series_id']; ?>'">
                        <i class="fa-solid fa-play"></i>
                      </button>
                      <p class="watch-now fs-2 fw-bold text-white m-0 text-nowrap" style="transform: translateY(-10px)">
                        WATCH NOW!</p>
                    </div>
                  </div>
                  <div class="wallpaper-watch-s d-md-none d-flex flex-column gap-2">
                    <button class="btn btn-primary rounded-5 mt-3 watch-now-small"
                      onclick="window.location.href='play-vid.php?video=<?php echo urlencode($row['video']); ?>&type=movie_series&id=<?php echo $row['movie_series_id']; ?>'">
                      <i class="fa-solid fa-play mx-1"></i>
                      <span>Watch Now</span>
                    </button>
                    <!-- More Info Button for mobile -->
                    <button class="btn d-flex db-text-sec text-nowrap align-items-center rounded-2"
                      style="background: rgba(255,255,255,0.15); border: none; width: fit-content; font-size: 16px; height: fit-content; position: relative; transform: translate(12px, 10px); z-index: 300;"
                      data-bs-toggle="modal" data-bs-target="#modal-carousel-<?php echo $movie_series_id; ?>">
                      <i class="fa-solid fa-info-circle me-1"></i> More Info
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <?php
            // Carousel modal for this item
            $carousel_modals .= "
      <div class='modal fade' id='modal-carousel-{$movie_series_id}' tabindex='-1' aria-labelledby='exampleModalLabel-carousel-{$movie_series_id}' aria-hidden='true'>
        <div class='modal-dialog modal-dialog-centered modal-lg modal-dark border-3'>
          <div class='modal-content bg-dark modals'>
            <div class='modal-body'>
              <div class='modal-body-content'>
                <div class='modal-pic-container m-0 position-relative'>
                  <video
                    class=\"w-100 position-relative rounded-3 m-0 p-0 video-player\"
                    data-cv-autoplay preload=none
                    muted
                    loop
                  >
                    <source src=\"" . htmlspecialchars(cv_video_src($row['video'])) . "\" type=\"video/mp4\">
                    Your browser does not support the video tag.
                  </video>";
            if (!empty($row['modal_poster_title'])) {
              $carousel_modals .= "<img class='poster-title-img trend-title' src='../DASHBOARD-HTML/MOVIE_SERIES_TITLE/" . htmlspecialchars($row['modal_poster_title']) . "' alt='' style='object-fit: cover; transform: scale(2.0) translate(36px, -14px);'>";
            } else {
              $carousel_modals .= "<p class='fw-bold fs-3 text-white position-absolute' style='top:10px; left:20px;'>" . htmlspecialchars($row['title']) . "</p>";
            }
            $carousel_modals .= "
                  <a href=\"play-vid.php?video=" . urlencode($row['video']) . "&type=movie_series&id=" . $movie_series_id . "\" class=\"btn btn-light play-btn text-center fs-18 text-end position-absolute\" style=\"width: 120px;\">
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
                      <p class='modal-text-rating mb-0'>" . htmlspecialchars($row['date_released']) . "</p>
                      <p class='modal-text-rating mb-0'>" . htmlspecialchars($row['duration']) . "</p>
                    </div>
                    <div class=\"d-flex gap-2 align-items-center\">
                      <p class='modal-text-rating p-1' style=\"border: 1px solid #f4fff8; width: fit-content;\">" . htmlspecialchars($row['age_rating']) . "+</p>
                      <p class='modal-text-rating mb-0' style=\"transform: translateY(-8px);\">" . htmlspecialchars($row['category']) . "</p>
                    </div>
                    <p class='modal-text-rating'>" . htmlspecialchars($row['description']) . "</p>
                  </div>
                  <div class=\"col-4 ps-0 pe-4 text-wrap\">
                    <p class=\"modal-text-rating text-wrap\" style=\"margin-top: 12px;\">
                      <span class=\"text-wrap\" style=\"color: #888684;\">Cast: </span>
                      " . htmlspecialchars($row['cast']) . ", more...
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
      <?php echo $carousel_modals; ?>
    </section>
    <!-- END OF SECTION WALLPAPER -->

    <!-- START OF SECTION CONTINUE WATCHING -->
    <?php
    // Titles this member started but didn't finish, newest first. For a
    // series whose last episode was finished, offer the next episode.
    $cw_items = [];
    $stmt = $con->prepare("
      SELECT wp.movie_series_id, wp.episode_id, wp.position_sec, wp.duration_sec,
             m.title, m.poster, m.category, e.episode_title, s.season_number, s.season_id
      FROM tbl_watch_progress wp
      JOIN tbl_movie_series m ON m.movie_series_id = wp.movie_series_id
      LEFT JOIN tbl_movie_series_episodes e ON e.episode_id = wp.episode_id
      LEFT JOIN tbl_movie_series_seasons s ON s.season_id = e.season_id
      WHERE wp.signup_id = ?
      ORDER BY wp.updated_at DESC
      LIMIT 20");
    $stmt->bind_param('i', $member_id);
    $stmt->execute();
    $cw_rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    foreach ($cw_rows as $r) {
      $finished = $r['position_sec'] >= $r['duration_sec'] - 90;
      if ($r['position_sec'] < 30 && !$finished) {
        continue; // barely started
      }
      if ($finished && $r['episode_id']) {
        // Next episode of the series, if there is one.
        $nx = $con->prepare("SELECT e.episode_id, e.episode_title, s.season_number
          FROM tbl_movie_series_episodes e JOIN tbl_movie_series_seasons s ON s.season_id = e.season_id
          WHERE s.movie_series_id = ? AND (s.season_number > ? OR (s.season_number = ? AND e.episode_id > ?))
          ORDER BY s.season_number, e.episode_id LIMIT 1");
        $nx->bind_param('iiii', $r['movie_series_id'], $r['season_number'], $r['season_number'], $r['episode_id']);
        $nx->execute();
        $next = $nx->get_result()->fetch_assoc();
        $nx->close();
        if (!$next) {
          continue; // finished the whole series
        }
        $cw_items[] = [
          'title' => $r['title'], 'poster' => $r['poster'],
          'sub' => 'Up next: S' . $next['season_number'] . ' · ' . $next['episode_title'],
          'pct' => 0, 'url' => 'play-vid.php?type=episode&id=' . (int) $next['episode_id'],
        ];
        continue;
      }
      if ($finished) {
        continue; // finished the movie
      }
      $left = max(1, (int) round(($r['duration_sec'] - $r['position_sec']) / 60));
      $cw_items[] = [
        'title' => $r['title'], 'poster' => $r['poster'],
        'sub' => ($r['episode_id'] ? 'S' . $r['season_number'] . ' · ' . $r['episode_title'] . ' · ' : '') . $left . ' min left',
        'pct' => min(100, round($r['position_sec'] / max(1, $r['duration_sec']) * 100)),
        'url' => $r['episode_id'] ? 'play-vid.php?type=episode&id=' . (int) $r['episode_id'] : 'play-vid.php?type=movie_series&id=' . (int) $r['movie_series_id'],
      ];
      if (count($cw_items) >= 12) {
        break;
      }
    }
    ?>
    <?php if ($cw_items): ?>
    <section class="cv-continue ms-md-5 ms-3" id="section-continue-watching">
      <p class="trending-text text-white fs-24 fw-bold mb-3">Continue Watching</p>
      <div class="cv-continue-row">
        <?php foreach ($cw_items as $it): ?>
          <a class="cv-continue-card" href="<?php echo e($it['url']); ?>">
            <span class="cv-continue-poster">
              <img src="../DASHBOARD-HTML/MOVIE_SERIES_TITLE/<?php echo e($it['poster']); ?>" alt="<?php echo e($it['title']); ?>" loading="lazy">
              <i class="fa-solid fa-play cv-continue-play"></i>
              <span class="cv-continue-bar"><span style="width: <?php echo (int) $it['pct']; ?>%"></span></span>
            </span>
            <span class="cv-continue-title"><?php echo e($it['title']); ?></span>
            <span class="cv-continue-sub"><?php echo e($it['sub']); ?></span>
          </a>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>
    <!-- END OF SECTION CONTINUE WATCHING -->

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
    WHERE ms.poster NOT LIKE '%HomeCarousel%'
      AND ms.poster NOT LIKE '%MovieCarousel%'
      AND ms.poster NOT LIKE '%SeriesCarousel%'
      AND ms.poster NOT LIKE '%HomeWallpaper%'
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

              $modals .= "
  <div class='modal fade' id='modal-top10-{$movie_series_id}' tabindex='-1' aria-labelledby='exampleModalLabel-{$movie_series_id}' aria-hidden='true'>
    <div class='modal-dialog modal-dialog-centered modal-lg modal-dark border-3'>
      <div class='modal-content bg-dark modals'>
        <div class='modal-body'>
          <div class='modal-body-content'>
            <div class='modal-pic-container m-0 position-relative'>
              <video
                class=\"w-100 position-relative rounded-3 m-0 p-0 video-player\"
                data-cv-autoplay preload=none
                muted
                loop
              >
                <source src=\"" . htmlspecialchars(cv_video_src($top10_video)) . "\" type=\"video/mp4\">
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
                                  class="position-relative rounded-3 m-0 p-0 video-player video-episode" preload=none
                                  muted
                                  style="width: 120px; height: 70px; object-fit: cover;">
                                  <source src="' . htmlspecialchars(cv_video_src($ep['episode_video'], 'MOVIE_SERIES_EPISODE')) . '" type="video/mp4">
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
      <?php
      $genre_result = mysqli_query($con, "SELECT genre_id, genre_name FROM tbl_movie_series_genre WHERE genre_name LIKE '%Romance%' LIMIT 1");
      $genre = mysqli_fetch_assoc($genre_result);
      $genre_id = $genre ? $genre['genre_id'] : 22; // fallback if not found
      $genre_name = $genre ? $genre['genre_name'] : "Romance";
      ?>
      <div class="trending-this-week">
        <p class="trending-text text-white fs-24 fw-bold">
          <?php echo htmlspecialchars($genre_name); ?>
        </p>
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

          $genre_result = mysqli_query($con, "SELECT genre_id, genre_name FROM tbl_movie_series_genre WHERE genre_name LIKE '%Romance%' LIMIT 1");
          $genre = mysqli_fetch_assoc($genre_result);
          $genre_id = $genre ? $genre['genre_id'] : 22; // fallback if not found
          $genre_name = $genre ? $genre['genre_name'] : "Romance";
          $trend_query = mysqli_query(
            $con,
            "SELECT ms.*, g1.genre_name AS genre_1, g2.genre_name AS genre_2, g3.genre_name AS genre_3
    FROM tbl_movie_series ms
    LEFT JOIN tbl_movie_series_genre g1 ON ms.genre_id1 = g1.genre_id
    LEFT JOIN tbl_movie_series_genre g2 ON ms.genre_id2 = g2.genre_id
    LEFT JOIN tbl_movie_series_genre g3 ON ms.genre_id3 = g3.genre_id
    WHERE (ms.genre_id1 = $genre_id OR ms.genre_id2 = $genre_id OR ms.genre_id3 = $genre_id)
      AND ms.poster NOT LIKE '%HomeCarousel%'
      AND ms.poster NOT LIKE '%MovieCarousel%'
      AND ms.poster NOT LIKE '%SeriesCarousel%'
      AND ms.poster NOT LIKE '%HomeWallpaper%'
    ORDER BY ms.date_released DESC
    LIMIT 20"
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
                    data-cv-autoplay preload=none
                    muted
                    loop
                  >
                    <source src=\"" . htmlspecialchars(cv_video_src($trend_video)) . "\" type=\"video/mp4\">
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
                                        class="position-relative rounded-3 m-0 p-0 video-player video-episode" preload=none
                                        muted
                                        style="width: 120px; height: 70px; object-fit: cover;">
                                        <source src="' . htmlspecialchars(cv_video_src($ep['episode_video'], 'MOVIE_SERIES_EPISODE')) . '" type="video/mp4">
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
      <?php
      $comedy_result = mysqli_query($con, "SELECT genre_id, genre_name FROM tbl_movie_series_genre WHERE genre_name LIKE '%Comedy%' LIMIT 1");
      $comedy = mysqli_fetch_assoc($comedy_result);
      $comedy_id = $comedy ? $comedy['genre_id'] : 4; // fallback to Comedy's genre_id
      $comedy_name = $comedy ? $comedy['genre_name'] : "Comedy";
      ?>
      <div class="action-movies-top d-flex justify-content-between">
        <p class="action-movies-text text-white fs-24 fw-bold">
          <?php echo htmlspecialchars($comedy_name); ?>
        </p>
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

          $genre_result = mysqli_query($con, "SELECT genre_id, genre_name FROM tbl_movie_series_genre WHERE genre_name LIKE '%Comedy%' LIMIT 1");
          $genre = mysqli_fetch_assoc($genre_result);
          $genre_id = $genre ? $genre['genre_id'] : 4; // fallback if not found
          $genre_name = $genre ? $genre['genre_name'] : "Comedy";

          $comedy_query = mysqli_query(
            $con,
            "SELECT ms.*, g1.genre_name AS genre_1, g2.genre_name AS genre_2, g3.genre_name AS genre_3
    FROM tbl_movie_series ms
    LEFT JOIN tbl_movie_series_genre g1 ON ms.genre_id1 = g1.genre_id
    LEFT JOIN tbl_movie_series_genre g2 ON ms.genre_id2 = g2.genre_id
    LEFT JOIN tbl_movie_series_genre g3 ON ms.genre_id3 = g3.genre_id
    WHERE (ms.genre_id1 = $genre_id OR ms.genre_id2 = $genre_id OR ms.genre_id3 = $genre_id)
      AND ms.poster NOT LIKE '%HomeCarousel%'
      AND ms.poster NOT LIKE '%MovieCarousel%'
      AND ms.poster NOT LIKE '%SeriesCarousel%'
      AND ms.poster NOT LIKE '%HomeWallpaper%'
    ORDER BY ms.date_released DESC
    LIMIT 20"
          );
          $comedy_modals = "";
          while ($row = mysqli_fetch_assoc($comedy_query)):
            $movie_series_id = $row['movie_series_id'];
            $comedy_title = $row['title'];
            $comedy_poster = $row['poster'];
            $comedy_video = $row['video'];
            $comedy_modal_poster_title = $row['modal_poster_title'];
            $comedy_duration = $row['duration'];
            $comedy_date_released = $row['date_released'];
            $comedy_age_rating = $row['age_rating'];
            $comedy_category = $row['category'];
            $comedy_genre_1 = $row['genre_1'];
            $comedy_genre_2 = $row['genre_2'];
            $comedy_genre_3 = $row['genre_3'];
            $comedy_cast = $row['cast'];
            $comedy_description = $row['description'];

            $genres = htmlspecialchars($comedy_genre_1);
            if (!empty($comedy_genre_2))
              $genres .= ', ' . htmlspecialchars($comedy_genre_2);
            if (!empty($comedy_genre_3))
              $genres .= ', ' . htmlspecialchars($comedy_genre_3);
            ?>
            <div class="comedy-images position-relative pe-2" style="overflow: visible;">
              <a href="#" data-bs-toggle="modal" data-bs-target="#modal-comedy-<?php echo $movie_series_id ?>">
                <div class="trending-hover position-relative">
                  <img class="action-movies-img rounded-3"
                    src="../DASHBOARD-HTML/MOVIE_SERIES_TITLE/<?php echo htmlspecialchars($comedy_poster); ?>"
                    alt="<?php echo htmlspecialchars($comedy_title); ?>">
                  <i class="fa-solid fa-play play-button-comedy"></i>
                </div>
              </a>
            </div>
            <?php
            $comedy_modals .= "
  <div class='modal fade' id='modal-comedy-{$movie_series_id}' tabindex='-1' aria-labelledby='exampleModalLabel-comedy-{$movie_series_id}' aria-hidden='true'>
    <div class='modal-dialog modal-dialog-centered modal-lg modal-dark border-3'>
      <div class='modal-content bg-dark modals'>
        <div class='modal-body'>
          <div class='modal-body-content'>
            <div class='modal-pic-container m-0 position-relative'>
              <video
                class=\"w-100 position-relative rounded-3 m-0 p-0 video-player\"
                data-cv-autoplay preload=none
                muted
                loop
              >
                <source src=\"" . htmlspecialchars(cv_video_src($comedy_video)) . "\" type=\"video/mp4\">
                Your browser does not support the video tag.
              </video>";
            if (!empty($comedy_modal_poster_title)) {
              $comedy_modals .= "<img class='poster-title-img trend-title' src='../DASHBOARD-HTML/MOVIE_SERIES_TITLE/" . htmlspecialchars($comedy_modal_poster_title) . "' alt='' style='object-fit: cover; transform: scale(2.0) translate(36px, -14px);'>";
            } else {
              $comedy_modals .= "<p class='fw-bold fs-3 text-white position-absolute' style='top:10px; left:20px;'>" . htmlspecialchars($comedy_title) . "</p>";
            }
            $comedy_modals .= "
              <a href=\"play-vid.php?video=" . urlencode($comedy_video) . "&type=movie_series&id=" . $movie_series_id . "\" class=\"btn btn-light play-btn text-center fs-18 text-end position-absolute\" style=\"width: 120px;\">
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
                  <p class='modal-text-rating mb-0'>" . htmlspecialchars($comedy_date_released) . "</p>
                  <p class='modal-text-rating mb-0'>" . htmlspecialchars($comedy_duration) . "</p>
                </div>
                <div class=\"d-flex gap-2 align-items-center\">
                  <p class='modal-text-rating p-1' style=\"border: 1px solid #f4fff8; width: fit-content;\">" . htmlspecialchars($comedy_age_rating) . "+</p>
                  <p class='modal-text-rating mb-0' style=\"transform: translateY(-8px);\">" . htmlspecialchars($comedy_category) . "</p>
                </div>
                <p class='modal-text-rating'>" . htmlspecialchars($comedy_description) . "</p>
              </div>
              <div class=\"col-4 ps-0 pe-4 text-wrap\">
                <p class=\"modal-text-rating text-wrap\" style=\"margin-top: 12px;\">
                  <span class=\"text-wrap\" style=\"color: #888684;\">Cast: </span>
                  " . htmlspecialchars($comedy_cast) . ", more...
                </p>
                <p class=\"modal-text-rating text-wrap\" style=\"margin-top: -2px;\">
                  <span class=\"text-wrap\" style=\"color: #888684;\">Genres: </span>
                  {$genres}
                </p>
              </div>
            </div>
          ";
            // --- Series/episodes code (unchanged) ---
            if ($comedy_category === 'Series') {
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
              $comedy_modals .= '
        <div class="col-12 d-flex justify-content-between align-items-center mx-4" style="width: 100%">
            <div>
                <p class="fs-3 mb-0" style="color: #f4fff8;">Episodes</p>
            </div>
            <div class="position-relative" style="min-width:180px; ">
                <select style="transform: translateX(-48px);" id="modal-season-select-comedy-' . $movie_series_id . '" class="form-control me-5">';
              foreach ($seasons as $s) {
                $comedy_modals .= '<option value="season-' . $s['season_id'] . '">' . htmlspecialchars($s['season_title']) . '</option>';
              }
              $comedy_modals .= '</select>
                <i class="fa-solid fa-caret-down caret-season" style="position: absolute; bottom: 12px; right: 63px; color: #f4fff8; pointer-events: none;"></i>
            </div>
        </div>';
              foreach ($seasons as $idx => $s) {
                $comedy_modals .= '<div class="col-12 mb-4 modal-episodes-block"
            id="modal-episodes-comedy-' . $movie_series_id . '-season-' . $s['season_id'] . '"
            style="' . ($idx === 0 ? '' : 'display:none;') . '">';
                foreach ($s['episodes'] as $ep_idx => $ep) {
                  $comedy_modals .= '
                    <div class="mx-3 py-2 d-flex gap-2">
                        <div class="d-flex align-items-center gap-3 me-2 ms-4">
                            <p class="mb-0 fs-2 db-text-sec">' . ($ep_idx + 1) . '</p>';
                  if (!empty($ep['episode_video'])) {
                    $comedy_modals .= '
                            <a href="play-vid.php?video=' . urlencode($ep['episode_video']) . '&type=episode&id=' . $ep['episode_id'] . '">
                                <video
                                    class="position-relative rounded-3 m-0 p-0 video-player video-episode" preload=none
                                    muted
                                    style="width: 120px; height: 70px; object-fit: cover;">
                                    <source src="' . htmlspecialchars(cv_video_src($ep['episode_video'], 'MOVIE_SERIES_EPISODE')) . '" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </a>';
                  }
                  $comedy_modals .= '
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
                  $comedy_modals .= '<div class="mx-4 py-2 db-text-sec">No episodes available for this season.</div>';
                }
                $comedy_modals .= '</div>';
              }
              $comedy_modals .= '
        <script>
        document.addEventListener("DOMContentLoaded", function () {
            var select = document.getElementById("modal-season-select-comedy-' . $movie_series_id . '");
            if (select) {
                select.addEventListener("change", function () {
                    var val = this.value;';
              foreach ($seasons as $s) {
                $comedy_modals .= '
                    document.getElementById("modal-episodes-comedy-' . $movie_series_id . '-season-' . $s['season_id'] . '").style.display = (val === "season-' . $s['season_id'] . '") ? "" : "none";';
              }
              $comedy_modals .= '
                });
            }
        });
        </script>
        ';
            }
            $comedy_modals .= "
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
    <?php echo $comedy_modals; ?>
    <!-- END OF SECTION COMEDY -->

    <!-- START OF SECTION ACTION -->
    <section class="section-featured text-white ms-md-5 ms-3">
      <div class="featured-container">
        <div class="featured-top">
          <?php
          $action_result = mysqli_query($con, "SELECT genre_id, genre_name FROM tbl_movie_series_genre WHERE genre_name LIKE '%Action%' LIMIT 1");
          $action = mysqli_fetch_assoc($action_result);
          $action_id = $action ? $action['genre_id'] : 1; // fallback to genre_id 1 if not found
          $action_name = $action ? $action['genre_name'] : "ACTION";
          ?>
          <p class="featured-text text-uppercase"><?php echo htmlspecialchars($action_name); ?></p>
          <p class="featured-movies-text fs-2">MOVIES/SERIES</p>
          <div class="top10-featured-wrapper position-relative">
            <div class="prev-button-featured position-absolute"
              style="left: 0; top: 50%; transform: translateY(-50%); z-index: 400;">
              <button class="btn border-1 prev-chevron-btn-featured">
                <i class="fas fa-chevron-left fa-2xl text-white-50"></i>
              </button>
            </div>

            <div class="featured-images-container d-flex gap-3 position-relative">
              <?php
              $genre_result = mysqli_query($con, "SELECT genre_id, genre_name FROM tbl_movie_series_genre WHERE genre_name LIKE '%Action%' LIMIT 1");
              $genre = mysqli_fetch_assoc($genre_result);
              $genre_id = $genre ? $genre['genre_id'] : 1; // fallback if not found
              $genre_name = $genre ? $genre['genre_name'] : "Action";


              $action_query = mysqli_query(
                $con,
                "SELECT ms.*, g1.genre_name AS genre_1, g2.genre_name AS genre_2, g3.genre_name AS genre_3
      FROM tbl_movie_series ms
      LEFT JOIN tbl_movie_series_genre g1 ON ms.genre_id1 = g1.genre_id
      LEFT JOIN tbl_movie_series_genre g2 ON ms.genre_id2 = g2.genre_id
      LEFT JOIN tbl_movie_series_genre g3 ON ms.genre_id3 = g3.genre_id
      WHERE (ms.genre_id1 = $genre_id OR ms.genre_id2 = $genre_id OR ms.genre_id3 = $genre_id)
        AND ms.poster NOT LIKE '%HomeCarousel%'
        AND ms.poster NOT LIKE '%MovieCarousel%'
        AND ms.poster NOT LIKE '%SeriesCarousel%'
        AND ms.poster NOT LIKE '%HomeWallpaper%'
      ORDER BY ms.date_released DESC
      LIMIT 20"
              );
              $action_modals = "";
              while ($row = mysqli_fetch_assoc($action_query)):
                $movie_series_id = $row['movie_series_id'];
                $action_title = $row['title'];
                $action_poster = $row['poster'];
                $action_video = $row['video'];
                $action_modal_poster_title = $row['modal_poster_title'];
                $action_duration = $row['duration'];
                $action_date_released = $row['date_released'];
                $action_age_rating = $row['age_rating'];
                $action_category = $row['category'];
                $action_genre_1 = $row['genre_1'];
                $action_genre_2 = $row['genre_2'];
                $action_genre_3 = $row['genre_3'];
                $action_cast = $row['cast'];
                $action_description = $row['description'];

                $genres = htmlspecialchars($action_genre_1);
                if (!empty($action_genre_2))
                  $genres .= ', ' . htmlspecialchars($action_genre_2);
                if (!empty($action_genre_3))
                  $genres .= ', ' . htmlspecialchars($action_genre_3);
                ?>
                <div class="featured-images position-relative pe-2" style="overflow: visible;">
                  <a href="#" data-bs-toggle="modal" data-bs-target="#modal-action-<?php echo $movie_series_id ?>">
                    <div class="trending-hover position-relative">
                      <img class="featured-img rounded-3"
                        src="../DASHBOARD-HTML/MOVIE_SERIES_TITLE/<?php echo htmlspecialchars($action_poster); ?>"
                        alt="<?php echo htmlspecialchars($action_title); ?>">
                      <i class="fa-solid fa-play play-button-featured"></i>
                    </div>
                  </a>
                </div>
                <?php
                $action_modals .= "
    <div class='modal fade' id='modal-action-{$movie_series_id}' tabindex='-1' aria-labelledby='exampleModalLabel-action-{$movie_series_id}' aria-hidden='true'>
      <div class='modal-dialog modal-dialog-centered modal-lg modal-dark border-3'>
        <div class='modal-content bg-dark modals'>
          <div class='modal-body'>
            <div class='modal-body-content'>
              <div class='modal-pic-container m-0 position-relative'>
                <video
                  class=\"w-100 position-relative rounded-3 m-0 p-0 video-player\"
                  data-cv-autoplay preload=none
                  muted
                  loop
                >
                  <source src=\"" . htmlspecialchars(cv_video_src($action_video)) . "\" type=\"video/mp4\">
                  Your browser does not support the video tag.
                </video>";
                if (!empty($action_modal_poster_title)) {
                  $action_modals .= "<img class='poster-title-img trend-title' src='../DASHBOARD-HTML/MOVIE_SERIES_TITLE/" . htmlspecialchars($action_modal_poster_title) . "' alt='' style='object-fit: cover; transform: scale(2.0) translate(36px, -14px);'>";
                } else {
                  $action_modals .= "<p class='fw-bold fs-3 text-white position-absolute' style='top:10px; left:20px;'>" . htmlspecialchars($action_title) . "</p>";
                }
                $action_modals .= "
                <a href=\"play-vid.php?video=" . urlencode($action_video) . "&type=movie_series&id=" . $movie_series_id . "\" class=\"btn btn-light play-btn text-center fs-18 text-end position-absolute\" style=\"width: 120px;\">
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
                    <p class='modal-text-rating mb-0'>" . htmlspecialchars($action_date_released) . "</p>
                    <p class='modal-text-rating mb-0'>" . htmlspecialchars($action_duration) . "</p>
                  </div>
                  <div class=\"d-flex gap-2 align-items-center\">
                    <p class='modal-text-rating p-1' style=\"border: 1px solid #f4fff8; width: fit-content;\">" . htmlspecialchars($action_age_rating) . "+</p>
                    <p class='modal-text-rating mb-0' style=\"transform: translateY(-8px);\">" . htmlspecialchars($action_category) . "</p>
                  </div>
                  <p class='modal-text-rating'>" . htmlspecialchars($action_description) . "</p>
                </div>
                <div class=\"col-4 ps-0 pe-4 text-wrap\">
                  <p class=\"modal-text-rating text-wrap\" style=\"margin-top: 12px;\">
                    <span class=\"text-wrap\" style=\"color: #888684;\">Cast: </span>
                    " . htmlspecialchars($action_cast) . ", more...
                  </p>
                  <p class=\"modal-text-rating text-wrap\" style=\"margin-top: -2px;\">
                    <span class=\"text-wrap\" style=\"color: #888684;\">Genres: </span>
                    {$genres}
                  </p>
                </div>
              </div>
            ";
                // --- Series/episodes code (unchanged) ---
                if ($action_category === 'Series') {
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
                  $action_modals .= '
          <div class="col-12 d-flex justify-content-between align-items-center mx-4" style="width: 100%">
              <div>
                  <p class="fs-3 mb-0" style="color: #f4fff8;">Episodes</p>
              </div>
              <div class="position-relative" style="min-width:180px; ">
                  <select style="transform: translateX(-48px);" id="modal-season-select-action-' . $movie_series_id . '" class="form-control me-5">';
                  foreach ($seasons as $s) {
                    $action_modals .= '<option value="season-' . $s['season_id'] . '">' . htmlspecialchars($s['season_title']) . '</option>';
                  }
                  $action_modals .= '</select>
                  <i class="fa-solid fa-caret-down caret-season" style="position: absolute; bottom: 12px; right: 63px; color: #f4fff8; pointer-events: none;"></i>
              </div>
          </div>';
                  foreach ($seasons as $idx => $s) {
                    $action_modals .= '<div class="col-12 mb-4 modal-episodes-block"
              id="modal-episodes-action-' . $movie_series_id . '-season-' . $s['season_id'] . '"
              style="' . ($idx === 0 ? '' : 'display:none;') . '">';
                    foreach ($s['episodes'] as $ep_idx => $ep) {
                      $action_modals .= '
                      <div class="mx-3 py-2 d-flex gap-2">
                          <div class="d-flex align-items-center gap-3 me-2 ms-4">
                              <p class="mb-0 fs-2 db-text-sec">' . ($ep_idx + 1) . '</p>';
                      if (!empty($ep['episode_video'])) {
                        $action_modals .= '
                              <a href="play-vid.php?video=' . urlencode($ep['episode_video']) . '&type=episode&id=' . $ep['episode_id'] . '">
                                  <video
                                      class="position-relative rounded-3 m-0 p-0 video-player video-episode" preload=none
                                      muted
                                      style="width: 120px; height: 70px; object-fit: cover;">
                                      <source src="' . htmlspecialchars(cv_video_src($ep['episode_video'], 'MOVIE_SERIES_EPISODE')) . '" type="video/mp4">
                                      Your browser does not support the video tag.
                                  </video>
                              </a>';
                      }
                      $action_modals .= '
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
                      $action_modals .= '<div class="mx-4 py-2 db-text-sec">No episodes available for this season.</div>';
                    }
                    $action_modals .= '</div>';
                  }
                  $action_modals .= '
          <script>
          document.addEventListener("DOMContentLoaded", function () {
              var select = document.getElementById("modal-season-select-action-' . $movie_series_id . '");
              if (select) {
                  select.addEventListener("change", function () {
                      var val = this.value;';
                  foreach ($seasons as $s) {
                    $action_modals .= '
                      document.getElementById("modal-episodes-action-' . $movie_series_id . '-season-' . $s['season_id'] . '").style.display = (val === "season-' . $s['season_id'] . '") ? "" : "none";';
                  }
                  $action_modals .= '
                  });
              }
          });
          </script>
          ';
                }
                $action_modals .= "
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
    <?php echo $action_modals; ?>
    <!-- END OF SECTION ACTION -->


    <!-- START OF SECTION WALLPAPER 1 -->
    <section class="movies-1 mt-80">
      <div>
        <?php
        // Fetch the movie/series for HomeWallpaper1
        $wallpaper1_query = mysqli_query($con, "
      SELECT * FROM tbl_movie_series
      WHERE feature_slot = 'banner1'
      LIMIT 1
    ");
        $wallpaper1 = mysqli_fetch_assoc($wallpaper1_query);
        if ($wallpaper1):
          ?>
          <img src="../DASHBOARD-HTML/MOVIE_SERIES_TITLE/<?php echo htmlspecialchars($wallpaper1['backdrop'] ?: $wallpaper1['poster']); ?>"
            class="d-block w-100 wallpaper-lotr bg-danger"
            alt="<?php echo htmlspecialchars($wallpaper1['title']); ?> Wallpaper">

          <div
            class="wallpaper-description-tem d-flex flex-column justify-content-end align-items-end position-relative me-5">

            <div class="d-flex justify-content-center">
              <img class="tem-title-img position-relative mb-1"
                src="../DASHBOARD-HTML/MOVIE_SERIES_TITLE/<?php echo htmlspecialchars($wallpaper1['modal_poster_title']); ?>"
                alt="<?php echo htmlspecialchars($wallpaper1['title']); ?> Title">
            </div>

            <div class="rating-text-tem my-4 position-relative text-white">
              <p><?php echo htmlspecialchars($wallpaper1['description']); ?></p>
            </div>

            <div class="watch-now-tem position-relative">
              <button class="btn btn-primary rounded-5 watch-now-small"
                onclick="window.location.href='play-vid.php?video=<?php echo urlencode($wallpaper1['video']); ?>&type=movie_series&id=<?php echo $wallpaper1['movie_series_id']; ?>'">
                <i class="fa-solid fa-play mx-1"></i>
                <span>Watch Now</span>
              </button>
            </div>

          </div>
        <?php endif; ?>
      </div>
    </section>
    <!-- END OF SECTION WALLPAPER 1 -->

    <!-- START OF SECTION RECOMMENDED -->
    <?php
    $recommended_items = [];
    $show_based_on_genre = false;
    $genres_for_recommend = [];

    // Favorite genres saved on the account, plus genres of titles recently played on this device.
    $genres_for_recommend = $favorite_names;
    if (isset($_COOKIE['preferred_genres']) && $_COOKIE['preferred_genres'] !== '') {
      $genres_for_recommend = array_merge($genres_for_recommend, array_map('trim', explode(',', $_COOKIE['preferred_genres'])));
    }
    $genres_for_recommend = array_values(array_unique(array_filter($genres_for_recommend)));

    if ($genres_for_recommend) {
      $show_based_on_genre = true;

      // Build WHERE clause for multiple genres
      $genre_conditions = [];
      foreach ($genres_for_recommend as $genre) {
        $genre = mysqli_real_escape_string($con, $genre);
        $genre_conditions[] = "(g1.genre_name = '$genre' OR g2.genre_name = '$genre' OR g3.genre_name = '$genre')";
      }
      $where_genres = implode(' OR ', $genre_conditions);

      $recommend_query = mysqli_query(
        $con,
        "SELECT ms.*, g1.genre_name AS genre_1, g2.genre_name AS genre_2, g3.genre_name AS genre_3
        FROM tbl_movie_series ms
        LEFT JOIN tbl_movie_series_genre g1 ON ms.genre_id1 = g1.genre_id
        LEFT JOIN tbl_movie_series_genre g2 ON ms.genre_id2 = g2.genre_id
        LEFT JOIN tbl_movie_series_genre g3 ON ms.genre_id3 = g3.genre_id
        WHERE ($where_genres)
          AND ms.poster NOT LIKE '%HomeCarousel%'
          AND ms.poster NOT LIKE '%MovieCarousel%'
          AND ms.poster NOT LIKE '%SeriesCarousel%'
          AND ms.poster NOT LIKE '%HomeWallpaper%'
        ORDER BY RAND() LIMIT 20"
      );
    } else {
      // Fallback: random picks
      $recommend_query = mysqli_query(
        $con,
        "SELECT ms.*, g1.genre_name AS genre_1, g2.genre_name AS genre_2, g3.genre_name AS genre_3
        FROM tbl_movie_series ms
        LEFT JOIN tbl_movie_series_genre g1 ON ms.genre_id1 = g1.genre_id
        LEFT JOIN tbl_movie_series_genre g2 ON ms.genre_id2 = g2.genre_id
        LEFT JOIN tbl_movie_series_genre g3 ON ms.genre_id3 = g3.genre_id
        WHERE ms.poster NOT LIKE '%HomeCarousel%'
          AND ms.poster NOT LIKE '%MovieCarousel%'
          AND ms.poster NOT LIKE '%SeriesCarousel%'
          AND ms.poster NOT LIKE '%HomeWallpaper%'
        ORDER BY RAND() LIMIT 20"
      );
    }
    while ($row = mysqli_fetch_assoc($recommend_query)) {
      $recommended_items[] = $row;
    }
    $recommended_modals = "";
    ?>
    <section class="section-popular text-white ms-md-5 ms-3" id="recommended">
      <div class="popular-container mt-4">
        <div class="popular-top mb-4 d-flex flex-wrap align-items-center gap-2">
          <p class="action-movies-text text-white fs-24 fw-bold mb-0">
            Recommended for You
            <?php if ($show_based_on_genre)
              echo "<span class='fs-6 ms-1 text-primary'>(Based on " . htmlspecialchars(implode(', ', $genres_for_recommend)) . ")</span>"; ?>
          </p>
          <?php if ($picker_genres): ?>
            <button type="button" class="btn btn-sm cv-edit-genres" data-bs-toggle="modal" data-bs-target="#genreModal">
              <i class="fa-solid fa-sliders me-1"></i><?php echo $favorite_ids ? 'Change my genres' : 'Pick my genres'; ?>
            </button>
          <?php endif; ?>
        </div>
        <div class="position-relative" style="width: 100%;">
          <div class="popular-images-container d-flex gap-3 position-relative">
            <?php foreach ($recommended_items as $item): ?>
              <div class="popular-images position-relative pe-2" style="overflow: visible;">
                <a href="#" class="recommended-link" data-bs-toggle="modal"
                  data-bs-target="#modal-recommended-<?php echo $item['movie_series_id']; ?>"
                  data-genre1="<?php echo htmlspecialchars($item['genre_1']); ?>"
                  data-genre2="<?php echo htmlspecialchars($item['genre_2']); ?>"
                  data-genre3="<?php echo htmlspecialchars($item['genre_3']); ?>">
                  <img class="popular-img rounded-3"
                    src="../DASHBOARD-HTML/MOVIE_SERIES_TITLE/<?php echo htmlspecialchars($item['poster']); ?>"
                    alt="<?php echo htmlspecialchars($item['title']); ?>">
                  <i class="fa-solid fa-play play-button-popular"></i>
                </a>
              </div>
              <?php
              $genres = htmlspecialchars($item['genre_1']);
              if (!empty($item['genre_2']))
                $genres .= ', ' . htmlspecialchars($item['genre_2']);
              if (!empty($item['genre_3']))
                $genres .= ', ' . htmlspecialchars($item['genre_3']);

              $recommended_modals .= "
    <div class='modal fade' id='modal-recommended-{$item['movie_series_id']}' tabindex='-1' aria-labelledby='exampleModalLabel-recommended-{$item['movie_series_id']}' aria-hidden='true'>
      <div class='modal-dialog modal-dialog-centered modal-lg modal-dark border-3'>
        <div class='modal-content bg-dark modals'>
          <div class='modal-body'>
            <div class='modal-body-content'>
              <div class='modal-pic-container m-0 position-relative'>
                <video
                  class=\"w-100 position-relative rounded-3 m-0 p-0 video-player\"
                  data-cv-autoplay preload=none
                  muted
                  loop
                >
                  <source src=\"" . htmlspecialchars(cv_video_src($item['video'])) . "\" type=\"video/mp4\">
                  Your browser does not support the video tag.
                </video>";
              if (!empty($item['modal_poster_title'])) {
                $recommended_modals .= "<img class='poster-title-img trend-title' src='../DASHBOARD-HTML/MOVIE_SERIES_TITLE/" . htmlspecialchars($item['modal_poster_title']) . "' alt='' style='object-fit: cover; transform: scale(2.0) translate(36px, -14px);'>";
              } else {
                $recommended_modals .= "<p class='fw-bold fs-3 text-white position-absolute' style='top:10px; left:20px;'>" . htmlspecialchars($item['title']) . "</p>";
              }
              $recommended_modals .= "
                <a href=\"play-vid.php?video=" . urlencode($item['video']) . "&type=movie_series&id=" . $item['movie_series_id'] . "\" class=\"btn btn-light play-btn text-center fs-18 text-end position-absolute\" style=\"width: 120px;\">
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
                    <p class='modal-text-rating mb-0'>" . htmlspecialchars($item['date_released']) . "</p>
                    <p class='modal-text-rating mb-0'>" . htmlspecialchars($item['duration']) . "</p>
                  </div>
                  <div class=\"d-flex gap-2 align-items-center\">
                    <p class='modal-text-rating p-1' style=\"border: 1px solid #f4fff8; width: fit-content;\">" . htmlspecialchars($item['age_rating']) . "+</p>
                    <p class='modal-text-rating mb-0' style=\"transform: translateY(-8px);\">" . htmlspecialchars($item['category']) . "</p>
                  </div>
                  <p class='modal-text-rating'>" . htmlspecialchars($item['description']) . "</p>
                </div>
                <div class=\"col-4 ps-0 pe-4 text-wrap\">
                  <p class=\"modal-text-rating text-wrap\" style=\"margin-top: 12px;\">
                    <span class=\"text-wrap\" style=\"color: #888684;\">Cast: </span>
                    " . htmlspecialchars($item['cast']) . ", more...
                  </p>
                  <p class=\"modal-text-rating text-wrap\" style=\"margin-top: -2px;\">
                    <span class=\"text-wrap\" style=\"color: #888684;\">Genres: </span>
                    {$genres}
                  </p>
                </div>
              </div>
        ";
              // --- Series/episodes code (unchanged) ---
              if ($item['category'] === 'Series') {
                $seasons_result = mysqli_query($con, "SELECT * FROM tbl_movie_series_seasons WHERE movie_series_id = {$item['movie_series_id']} ORDER BY season_number");
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
                $recommended_modals .= '
            <div class="col-12 d-flex justify-content-between align-items-center mx-4" style="width: 100%">
                <div>
                    <p class="fs-3 mb-0" style="color: #f4fff8;">Episodes</p>
                </div>
                <div class="position-relative" style="min-width:180px; ">
                    <select style="transform: translateX(-48px);" id="modal-season-select-recommended-' . $item['movie_series_id'] . '" class="form-control me-5">';
                foreach ($seasons as $s) {
                  $recommended_modals .= '<option value="season-' . $s['season_id'] . '">' . htmlspecialchars($s['season_title']) . '</option>';
                }
                $recommended_modals .= '</select>
                    <i class="fa-solid fa-caret-down caret-season" style="position: absolute; bottom: 12px; right: 63px; color: #f4fff8; pointer-events: none;"></i>
                </div>
            </div>';
                foreach ($seasons as $idx => $s) {
                  $recommended_modals .= '<div class="col-12 mb-4 modal-episodes-block"
                id="modal-episodes-recommended-' . $item['movie_series_id'] . '-season-' . $s['season_id'] . '"
                style="' . ($idx === 0 ? '' : 'display:none;') . '">';
                  foreach ($s['episodes'] as $ep_idx => $ep) {
                    $recommended_modals .= '
                        <div class="mx-3 py-2 d-flex gap-2">
                            <div class="d-flex align-items-center gap-3 me-2 ms-4">
                                <p class="mb-0 fs-2 db-text-sec">' . ($ep_idx + 1) . '</p>';
                    if (!empty($ep['episode_video'])) {
                      $recommended_modals .= '
                                <a href="play-vid.php?video=' . urlencode($ep['episode_video']) . '&type=episode&id=' . $ep['episode_id'] . '">
                                    <video
                                        class="position-relative rounded-3 m-0 p-0 video-player video-episode" preload=none
                                        muted
                                        style="width: 120px; height: 70px; object-fit: cover;">
                                        <source src="' . htmlspecialchars(cv_video_src($ep['episode_video'], 'MOVIE_SERIES_EPISODE')) . '" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                </a>';
                    }
                    $recommended_modals .= '
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
                    $recommended_modals .= '<div class="mx-4 py-2 db-text-sec">No episodes available for this season.</div>';
                  }
                  $recommended_modals .= '</div>';
                }
                $recommended_modals .= '
            <script>
            document.addEventListener("DOMContentLoaded", function () {
                var select = document.getElementById("modal-season-select-recommended-' . $item['movie_series_id'] . '");
                if (select) {
                    select.addEventListener("change", function () {
                        var val = this.value;';
                foreach ($seasons as $s) {
                  $recommended_modals .= '
                        document.getElementById("modal-episodes-recommended-' . $item['movie_series_id'] . '-season-' . $s['season_id'] . '").style.display = (val === "season-' . $s['season_id'] . '") ? "" : "none";';
                }
                $recommended_modals .= '
                    });
                }
            });
            </script>
            ';
              }
              $recommended_modals .= "
              </div>
            </div>
          </div>
        </div>
      </div>
    ";
              ?>
            <?php endforeach; ?>
          </div>
          <div class="next-button-recommended position-absolute"
            style="right: 0; top: 50%; transform: translateY(-50%);z-index: 400;">
            <button class="btn border-1 next-chevron-btn-recommended">
              <i class="fas fa-chevron-right fa-2xl text-white-50"></i>
            </button>
          </div>
          <div class="prev-button-recommended position-absolute"
            style="left: 0; top: 50%; transform: translateY(-50%); z-index: 400;">
            <button class="btn border-1 prev-chevron-btn-recommended">
              <i class="fas fa-chevron-left fa-2xl text-white-50"></i>
            </button>
          </div>
        </div>
      </div>
    </section>
    <?php echo $recommended_modals; ?>
    <!-- END OF SECTION RECOMMENDED -->

    <!-- START OF MOVIES 2 -->
    <section class="movies-2 mt-80">
      <div>
        <?php
        // Fetch the movie/series for HomeWallpaper2
        $wallpaper2_query = mysqli_query($con, "
      SELECT * FROM tbl_movie_series
      WHERE feature_slot = 'banner2'
      LIMIT 1
    ");
        $wallpaper2 = mysqli_fetch_assoc($wallpaper2_query);
        if ($wallpaper2):
          ?>
          <img src="../DASHBOARD-HTML/MOVIE_SERIES_TITLE/<?php echo htmlspecialchars($wallpaper2['backdrop'] ?: $wallpaper2['poster']); ?>"
            class="d-block w-100 wallpaper-lotr" alt="<?php echo htmlspecialchars($wallpaper2['title']); ?> Wallpaper">

          <div
            class="wallpaper-description-lotr d-flex flex-column justify-content-end align-items-end position-relative me-5">

            <div class="d-flex justify-content-center">
              <img class="lotr-title-img position-relative mb-1"
                src="../DASHBOARD-HTML/MOVIE_SERIES_TITLE/<?php echo htmlspecialchars($wallpaper2['modal_poster_title']); ?>"
                alt="<?php echo htmlspecialchars($wallpaper2['title']); ?> Title">
            </div>

            <div class="rating-text-lotr my-4 position-relative text-white">
              <p><?php echo htmlspecialchars($wallpaper2['description']); ?></p>
            </div>

            <div class="watch-now-lotr position-relative">
              <button class="btn btn-primary rounded-5 watch-now-small"
                onclick="window.location.href='play-vid.php?video=<?php echo urlencode($wallpaper2['video']); ?>&type=movie_series&id=<?php echo $wallpaper2['movie_series_id']; ?>'">
                <i class="fa-solid fa-play mx-1"></i>
                <span>Watch Now</span>
              </button>
            </div>

          </div>
        <?php endif; ?>
      </div>
    </section>
    <!-- END OF MOVIES 2 -->
  </main>
  <!-- END OF THE MAIN CONTENT -->

  <!-- ========== FAVORITE GENRES PICKER ========== -->
  <div class="modal fade" id="genreModal" tabindex="-1" aria-labelledby="genreModalLabel" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg cv-genre-dialog">
      <div class="modal-content cv-genre-modal text-white">
        <form id="genre-form" method="post" class="d-flex flex-column h-100">
          <input type="hidden" name="save_genres" value="1">

          <div class="cv-genre-head text-center">
            <?php if ($favorite_ids): ?>
              <button type="button" class="btn-close btn-close-white cv-genre-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <?php endif; ?>
            <div class="cv-genre-badge"><i class="fa-solid fa-clapperboard"></i></div>
            <h2 class="fs-3 fw-bold mb-1" id="genreModalLabel">
              <?php if ($favorite_ids): ?>
                Change your favorite genres
              <?php else: ?>
                Welcome<?php echo !empty($member['first_name']) ? ', ' . e($member['first_name']) : ''; ?>!
              <?php endif; ?>
            </h2>
            <p class="cv-genre-sub mb-3">Pick <strong>3 genres</strong> you love and we'll recommend movies and series for you.</p>
            <div class="cv-genre-progress" aria-live="polite">
              <div class="cv-genre-dots">
                <span></span><span></span><span></span>
              </div>
              <span id="genre-count-text">0 of 3 selected</span>
            </div>
          </div>

          <div class="modal-body cv-genre-body">
            <div class="cv-genre-grid">
              <?php foreach ($picker_genres as $gid => $g):
                $checked = in_array($gid, $favorite_ids, true); ?>
                <label class="cv-genre-card<?php echo $checked ? ' is-selected' : ''; ?>">
                  <input type="checkbox" name="genre_ids[]" value="<?php echo (int) $gid; ?>" <?php echo $checked ? 'checked' : ''; ?>>
                  <span class="cv-genre-check"><i class="fa-solid fa-check"></i></span>
                  <i class="fa-solid <?php echo cv_genre_icon($g['genre_name']); ?> cv-genre-icon"></i>
                  <span class="cv-genre-name"><?php echo e($g['genre_name']); ?></span>
                  <span class="cv-genre-total"><?php echo (int) $g['total']; ?> title<?php echo (int) $g['total'] === 1 ? '' : 's'; ?></span>
                </label>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="cv-genre-foot d-flex flex-wrap align-items-center justify-content-between gap-2">
            <?php if ($favorite_ids): ?>
              <button type="button" class="btn btn-link cv-genre-skip" data-bs-dismiss="modal">Cancel</button>
            <?php else: ?>
              <button type="submit" name="skip_genres" value="1" class="btn btn-link cv-genre-skip" formnovalidate>Skip for now</button>
            <?php endif; ?>
            <button type="submit" class="btn cv-genre-continue" id="submit-btn" disabled>
              Pick 3 to continue
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <footer>
    <div class="footer text-white d-flex justify-content-between mx-5 align-items-center">
      <p class="footer-long-text">Movie and series details and images come from <a class="text-white" href="https://www.themoviedb.org/" target="_blank" rel="noopener">TMDB</a>. This product uses the TMDB API but is not endorsed or certified by TMDB. Free classic films stream from the <a class="text-white" href="https://archive.org/" target="_blank" rel="noopener">Internet Archive</a>.</p>
      <p>© 2025 CineVault. All rights reserved.</p>
    </div>
  </footer>

</body>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('genreModal');
    const cards = modalEl.querySelectorAll('.cv-genre-card');
    const boxes = modalEl.querySelectorAll('.cv-genre-card input');
    const dots = modalEl.querySelectorAll('.cv-genre-dots span');
    const countText = document.getElementById('genre-count-text');
    const submitBtn = document.getElementById('submit-btn');

    function refresh() {
      const picked = [...boxes].filter(b => b.checked).length;
      cards.forEach(function (card) {
        const box = card.querySelector('input');
        card.classList.toggle('is-selected', box.checked);
        // After 3 picks, the rest are dimmed until one is removed.
        card.classList.toggle('is-locked', !box.checked && picked >= 3);
      });
      dots.forEach((d, i) => d.classList.toggle('on', i < picked));
      countText.textContent = picked + ' of 3 selected';
      submitBtn.disabled = picked !== 3;
      submitBtn.innerHTML = picked === 3
        ? 'Show my recommendations <i class="fa-solid fa-arrow-right ms-1"></i>'
        : 'Pick ' + (3 - picked) + ' more';
    }

    boxes.forEach(function (box) {
      box.addEventListener('change', function () {
        const picked = [...boxes].filter(b => b.checked).length;
        if (picked > 3) {
          box.checked = false;
          const card = box.closest('.cv-genre-card');
          card.classList.add('shake');
          setTimeout(() => card.classList.remove('shake'), 350);
        }
        refresh();
      });
    });
    refresh();

    <?php if ($show_genre_picker): ?>
    new bootstrap.Modal(modalEl).show();
    <?php endif; ?>
  });
</script>
<script src="video-player.js?v=<?php echo CV_ASSET_VERSION; ?>"></script>
<script src="poster-slide.js?v=<?php echo CV_ASSET_VERSION; ?>"></script>

</html>