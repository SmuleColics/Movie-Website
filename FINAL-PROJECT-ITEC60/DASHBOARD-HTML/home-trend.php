<?php
ob_start();
include '../includes/dashboard-header-sidebar.php';
include '../includes/db-connection.php';

$genres = ["Action", "Adventure", "Animation", "Comedy", "Crime", "Documentary", "Drama", "Family", "Fantasy", "History", "Terror", "Music", "Mystery", "Science fiction", "Cinema TV", "Thriller", "War", "Western", "Kids", "News", "Reality", "Romance", "Sci-Fi & Fantasy", "Soap", "Talk", "War & Politics"];

// --- AJAX DELETE HANDLER for episode delete in edit mode ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_episode_id'])) {
  $episode_id = intval($_POST['delete_episode_id']);
  mysqli_query($con, "DELETE FROM tbl_trend_episodes WHERE episode_id = $episode_id");
  echo 'success';
  exit;
}

// --- AJAX DELETE HANDLER for season delete (and all its episodes) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_season_id'])) {
  $season_id = intval($_POST['delete_season_id']);
  // Delete all episodes for the season
  mysqli_query($con, "DELETE FROM tbl_trend_episodes WHERE season_id = $season_id");
  // Delete the season
  mysqli_query($con, "DELETE FROM tbl_trend_seasons WHERE season_id = $season_id");
  echo 'success';
  exit;
}

$file_upload_error = "File can not be empty";
$valid_poster = $valid_video = $valid_modal_poster_title = $valid_description = $valid_cast = $valid_title = $valid_duration = $valid_date_released = $valid_date_released_time = $valid_age_rating = $valid_age_rating_age = true;
$valid_episodes = true;

$date_released = 0;
$age_rating = 0;

$file_poster_path = '';
$file_video_path = '';
$file_modal_poster_title_path = '';

// Detect mode
$trend_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$is_edit = $trend_id > 0;

// Default record values (for add mode)
$record = [
  'title' => '',
  'duration' => '',
  'poster' => '',
  'video' => '',
  'modal_poster_title' => '',
  'date_released' => '',
  'age_rating' => '',
  'category' => 'Movie',
  'genre_1' => '',
  'genre_2' => '',
  'genre_3' => '',
  'cast' => '',
  'description' => ''
];

// For edit, fetch the record and episodes
$episodes = [];
$seasons = [];

if ($is_edit) {
  $result = mysqli_query($con, "SELECT * FROM tbl_trend WHERE trend_id = $trend_id LIMIT 1");
  if ($result && mysqli_num_rows($result) > 0) {
    $record = mysqli_fetch_assoc($result);
    if ($record['category'] === 'Series') {
      // Fetch seasons
      $seasonq = mysqli_query($con, "SELECT * FROM tbl_trend_seasons WHERE trend_id = $trend_id ORDER BY season_number");
      while ($season = mysqli_fetch_assoc($seasonq)) {
        // Fetch episodes for this season
        $epq = mysqli_query($con, "SELECT * FROM tbl_trend_episodes WHERE season_id = {$season['season_id']} ORDER BY episode_id");
        $season['episodes'] = [];
        while ($ep = mysqli_fetch_assoc($epq))
          $season['episodes'][] = $ep;
        $seasons[] = $season;
      }
    }
  } else {
    echo "<script>alert('Record not found.'); window.location.href='web-home.php';</script>";
    exit();
  }
}

// Handle add or update on POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_episode_id']) && !isset($_POST['delete_season_id'])) {
  $valid_poster = $valid_video = $valid_modal_poster_title = $valid_age_rating = true;

  // Poster (optional for edit, required for add)
  if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
    $file_poster = $_FILES['poster']['name'];
    $temp_name = $_FILES['poster']['tmp_name'];
    $file_poster_path = 'TREND_IMAGES/' . $file_poster;
    if (!move_uploaded_file($temp_name, $file_poster_path)) {
      $valid_poster = false;
    }
  } else {
    $file_poster = $is_edit ? $record['poster'] : '';
    if (!$is_edit && empty($file_poster))
      $valid_poster = false;
  }

  // Video (optional for edit, required for add)
  if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK && !is_array($_FILES['video']['name'])) {
    $file_video = $_FILES['video']['name'];
    $temp_name1 = $_FILES['video']['tmp_name'];
    $file_video_path = 'TREND_VIDEOS/' . $file_video;
    if (!move_uploaded_file($temp_name1, $file_video_path)) {
      $valid_video = false;
      $file_upload_error = "Video upload failed (move error).";
    }
  } else {
    $file_video = $is_edit ? $record['video'] : '';
    if (!$is_edit && empty($file_video))
      $valid_video = false;
  }

  // Modal Poster Title (optional for edit, required for add)
  if (isset($_FILES['modal-poster-title']) && $_FILES['modal-poster-title']['error'] === UPLOAD_ERR_OK) {
    $file_modal_poster_title = $_FILES['modal-poster-title']['name'];
    $temp_name2 = $_FILES['modal-poster-title']['tmp_name'];
    $file_modal_poster_title_path = 'TREND_IMAGES/' . $file_modal_poster_title;
    if (!move_uploaded_file($temp_name2, $file_modal_poster_title_path)) {
      $valid_modal_poster_title = false;
    }
  } else {
    $file_modal_poster_title = $is_edit ? $record['modal_poster_title'] : '';
    if (!$is_edit && empty($file_modal_poster_title))
      $valid_modal_poster_title = false;
  }

  // Validate title
  if (strlen(trim($_POST['title'] ?? '')) < 2) {
    $valid_title = false;
  } else {
    $title = mysqli_real_escape_string($con, $_POST['title'] ?? '');
  }

  // Validate Duration
  if (strlen(trim($_POST['duration'] ?? '')) < 2) {
    $valid_duration = false;
  } else {
    $duration = mysqli_real_escape_string($con, $_POST['duration'] ?? '');
  }

  // Validate date released
  if (isset($_POST['date-released']) && $_POST['date-released'] !== '') {
    $temp = $_POST['date-released'];
    if ($temp <= 2025 && $temp >= 1950) {
      $date_released = $temp;
    } else {
      $valid_date_released_time = false;
    }
  } else {
    $valid_date_released = false;
  }

  // Validate age rating
  if (isset($_POST['age-rating']) && $_POST['age-rating'] !== '') {
    $tempo = $_POST['age-rating'];
    if ($tempo >= 1 && $tempo <= 18) {
      $age_rating = $tempo;
    } else {
      $valid_age_rating_age = false;
    }
  } else {
    $valid_age_rating = false;
  }

  // Category
  $category = $_POST['category'] ?? '';

  // Genres
  $firstGenre = $_POST['first-genre'] ?? '';
  $secondGenre = trim($_POST['second-genre'] ?? '');
  $thirdGenre = trim($_POST['third-genre'] ?? '');
  $secondGenre = ($secondGenre === '') ? 'NULL' : "'" . mysqli_real_escape_string($con, $secondGenre) . "'";
  $thirdGenre = ($thirdGenre === '') ? 'NULL' : "'" . mysqli_real_escape_string($con, $thirdGenre) . "'";

  // Cast
  if (!empty($_POST['cast'])) {
    $cast = mysqli_real_escape_string($con, $_POST['cast']);
    $valid_cast = true;
  } else {
    $valid_cast = false;
  }

  // Description
  if (!empty($_POST['description'])) {
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $valid_description = true;
  } else {
    $valid_description = false;
  }

  // Season/Episodes for Series
  $is_series = ($category === 'Series');
  $season_titles = $_POST['season-title'] ?? [];
  $season_numbers = $_POST['season-number'] ?? [];
  $valid_seasons = true;
  $valid_episodes = true;
  $episode_errors = [];

  // EPISODE VALIDATION PART -- THIS VALIDATES ONLY THE EPISODE BLOCKS
  $has_episode = false;
  $episode_incomplete = false;
  if ($is_series) {
    foreach ($season_titles as $sidx => $season_title) {
      $ep_titles = $_POST['episode-title'][$sidx] ?? [];
      $ep_descs = $_POST['episode-description'][$sidx] ?? [];
      $ep_durations = $_POST['episode-duration'][$sidx] ?? [];
      $ep_video_files = $_FILES['episode-video']['name'][$sidx] ?? [];
      $existing_videos = $_POST['existing-episode-video'][$sidx] ?? [];

      $max_eidx = max(
        count($ep_titles),
        count($ep_descs),
        count($ep_durations),
        count($ep_video_files),
        count($existing_videos)
      );

      for ($eidx = 0; $eidx < $max_eidx; $eidx++) {
        $title_ep = isset($ep_titles[$eidx]) ? trim($ep_titles[$eidx]) : '';
        $desc = isset($ep_descs[$eidx]) ? trim($ep_descs[$eidx]) : '';
        $duration_ep = isset($ep_durations[$eidx]) ? trim($ep_durations[$eidx]) : '';
        $video = isset($ep_video_files[$eidx]) ? trim($ep_video_files[$eidx]) : '';
        $existing = isset($existing_videos[$eidx]) ? trim($existing_videos[$eidx]) : '';

        // If ALL episode fields are empty, skip this block
        if ($title_ep === '' && $desc === '' && $duration_ep === '' && $video === '' && $existing === '') {
          continue;
        }

        // If any required field is missing, mark as error
        if ($title_ep === '' || $desc === '' || $duration_ep === '' || ($video === '' && $existing === '')) {
          $valid_episodes = false;
          $episode_incomplete = true;
          $episode_errors['alert'] = "All episode fields (title, video, duration, description) must be filled for each episode.";
        }
        $has_episode = true;
      }
    }
    if (!$has_episode) {
      $valid_episodes = false;
      $episode_errors['alert'] = "You must add at least 1 episode for a series.";
    } elseif ($episode_incomplete) {
      $episode_errors['alert'] = "All episode fields (title, video, duration, description) must be filled for each episode.";
    }
  }

  // Repopulate record for form display on error
  if (
    !(
      $valid_poster && $valid_video && $valid_modal_poster_title && $valid_date_released && $valid_age_rating &&
      $valid_description && $valid_age_rating_age && $valid_date_released_time && $valid_title && $valid_cast &&
      $valid_duration && (!$is_series || ($valid_seasons && $valid_episodes))
    )
  ) {
    $record['title'] = $_POST['title'] ?? '';
    $record['duration'] = $_POST['duration'] ?? '';
    $record['date_released'] = $_POST['date-released'] ?? '';
    $record['age_rating'] = $_POST['age-rating'] ?? '';
    $record['category'] = $_POST['category'] ?? '';
    $record['genre_1'] = $_POST['first-genre'] ?? '';
    $record['genre_2'] = $_POST['second-genre'] ?? '';
    $record['genre_3'] = $_POST['third-genre'] ?? '';
    $record['cast'] = $_POST['cast'] ?? '';
    $record['description'] = $_POST['description'] ?? '';
  }

  if (
    $valid_poster && $valid_video && $valid_modal_poster_title && $valid_date_released && $valid_age_rating &&
    $valid_description && $valid_age_rating_age && $valid_date_released_time && $valid_title && $valid_cast &&
    $valid_duration && (!$is_series || ($valid_seasons && $valid_episodes))
  ) {
    if ($is_edit) {
      $update_sql = "
            UPDATE tbl_trend SET
                title = '$title',
                duration = '$duration',
                poster = '$file_poster',
                video = '$file_video',
                modal_poster_title = '$file_modal_poster_title',
                date_released = '$date_released',
                age_rating = '$age_rating',
                category = '$category',
                genre_1 = '$firstGenre',
                genre_2 = $secondGenre,
                genre_3 = $thirdGenre,
                cast = '$cast',
                description = '$description'
            WHERE trend_id = $trend_id
            ";
      $query = mysqli_query($con, $update_sql);
      $msg = "Data Updated Successfully";
      $saved_trend_id = $trend_id;
    } else {
      $insert_sql = "
            INSERT INTO tbl_trend
                (title, duration, poster, video, modal_poster_title, date_released, age_rating, category, genre_1, genre_2, genre_3, cast, description)
            VALUES (
                '$title',
                '$duration',
                '$file_poster',
                '$file_video',
                '$file_modal_poster_title',
                '$date_released',
                '$age_rating',
                '$category',
                '$firstGenre',
                $secondGenre,
                $thirdGenre,
                '$cast',
                '$description'
            )";
      $query = mysqli_query($con, $insert_sql);
      $msg = "Data Added Successfully";
      $saved_trend_id = mysqli_insert_id($con);
    }

    if (!$query) {
      die(mysqli_error($con));
    } else {
      if ($is_series) {
        // Remove old seasons/episodes if edit
        if ($is_edit) {
          $seasonidsq = mysqli_query($con, "SELECT season_id FROM tbl_trend_seasons WHERE trend_id = $saved_trend_id");
          while ($row = mysqli_fetch_assoc($seasonidsq)) {
            $sid = $row['season_id'];
            mysqli_query($con, "DELETE FROM tbl_trend_episodes WHERE season_id = $sid");
          }
          mysqli_query($con, "DELETE FROM tbl_trend_seasons WHERE trend_id = $saved_trend_id");
        }
        foreach ($season_titles as $sidx => $season_title) {
          $season_title_esc = mysqli_real_escape_string($con, $season_title);
          $season_num = intval($season_numbers[$sidx]);
          mysqli_query($con, "INSERT INTO tbl_trend_seasons (trend_id, season_number, season_title) VALUES ($saved_trend_id, $season_num, '$season_title_esc')");
          $season_id = mysqli_insert_id($con);
          $ep_titles = $_POST['episode-title'][$sidx] ?? [];
          $ep_descs = $_POST['episode-description'][$sidx] ?? [];
          $ep_durations = $_POST['episode-duration'][$sidx] ?? [];
          $video_files = $_FILES['episode-video']['name'][$sidx] ?? [];
          $video_tmps = $_FILES['episode-video']['tmp_name'][$sidx] ?? [];
          $existing_videos = $_POST['existing-episode-video'][$sidx] ?? [];
          $max_eidx = max(
            count($ep_titles),
            count($ep_descs),
            count($ep_durations),
            count($video_files),
            count($existing_videos)
          );
          for ($eidx = 0; $eidx < $max_eidx; $eidx++) {
            $title_ep = isset($ep_titles[$eidx]) ? trim($ep_titles[$eidx]) : '';
            $desc = isset($ep_descs[$eidx]) ? trim($ep_descs[$eidx]) : '';
            $duration_ep = isset($ep_durations[$eidx]) ? trim($ep_durations[$eidx]) : '';
            $video = isset($video_files[$eidx]) ? trim($video_files[$eidx]) : '';
            $existing = isset($existing_videos[$eidx]) ? trim($existing_videos[$eidx]) : '';

            // If ALL fields are empty, skip
            if ($title_ep === '' && $desc === '' && $duration_ep === '' && $video === '' && $existing === '') {
              continue;
            }
            // If any episode field is missing, do NOT insert (shouldn't get here due to validation, but double check)
            if ($title_ep === '' || $desc === '' || $duration_ep === '' || ($video === '' && $existing === '')) {
              continue;
            }

            $ep_video_name = '';
            if ($video !== '' && isset($video_tmps[$eidx]) && $video_tmps[$eidx]) {
              $ep_video_name = $video;
              $ep_video_tmp = $video_tmps[$eidx];
              $ep_video_path = 'TREND_EPISODES/' . $ep_video_name;
              move_uploaded_file($ep_video_tmp, $ep_video_path);
            } else if ($existing !== '') {
              $ep_video_name = $existing;
            }
            $ep_title_esc = mysqli_real_escape_string($con, $title_ep);
            $ep_desc_esc = mysqli_real_escape_string($con, $desc);
            $ep_duration_esc = mysqli_real_escape_string($con, $duration_ep);
            $ep_sql = "INSERT INTO tbl_trend_episodes (season_id, episode_title, episode_description, episode_duration, episode_video) VALUES ($season_id, '$ep_title_esc', '$ep_desc_esc', '$ep_duration_esc', '$ep_video_name')";
            mysqli_query($con, $ep_sql);
          }
        }
      }
      echo "<script>
                alert('$msg');
                window.location.href='web-home.php';
            </script>";
      exit();
    }
  } else {
    if ($file_poster_path && file_exists($file_poster_path)) {
      unlink($file_poster_path);
    }
    if ($file_video_path && file_exists($file_video_path)) {
      unlink($file_video_path);
    }
    if ($file_modal_poster_title_path && file_exists($file_modal_poster_title_path)) {
      unlink($file_modal_poster_title_path);
    }
  }
  ob_end_flush();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Action - <?php echo $is_edit ? 'Edit' : 'Add'; ?> Trending</title>
  <link rel="stylesheet" href="../DASHBOARD-CSS/dashboard.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .sidebar-content-item:nth-child(4) {
      background-color: var(--dashboard-primary);
    }

    .season-block {
      border: 1px solid #30363d;
      margin-bottom: 16px;
      padding: 16px;
      background: #222;
      position: relative;
    }

    .season-delete-btn {
      position: absolute;
      top: 10px;
      right: 10px;
      z-index: 10;
    }

    .season-divider {
      width: 100%;
      margin: 24px 0 8px 0;
      height: 2px;
      background-color: #30363d;
    }

    .episode-divider {
      height: 1px;
      background-color: #30363d;
      width: 100%;
      margin: 16px 0;
    }
  </style>
</head>

<body>
  <main class="container-lg p-0 overflow-hidden">
    <div>
      <a href="web-home.php" class="btn db-bg-primary text-white ms-3 mt-3">
        <i class="fa-solid fa-chevron-left"></i> Go Back
      </a>
    </div>
    <section class="signup-accounts-section text-left p-3">
      <div class="card bg-dark">
        <div class="card-body">
          <div class="table-task-top db-text-sec">
            <div class="py-2 ps-3">
              <p class="m-0 fs-20"><?php echo $is_edit ? 'Edit' : 'Add'; ?> Trending Record</p>
              <p class="m-0 fs-14" style="transform: translateY(-2px)">
                <?php echo $is_edit ? 'Edit your entry for the trending CineVault database' : 'Add your favorite movies to the CineVault database here'; ?>
              </p>
            </div>
          </div>
          <!-- ====== FULL FORM FIELDS START ====== -->
          <form method="post" enctype="multipart/form-data" id="top10-form">
            <div class="row mt-3 mb-2">
              <div class="col-12">
                <label for="poster" class="db-text-sec fs-18 form-label">Upload Poster</label><br>
                <input type="file" name="poster" id="poster" class="form-control file-trending">
                <?php if ($is_edit && !empty($record['poster'])): ?>
                  <p class="mt-1 mb-2 db-text-sec">Current: <?php echo htmlspecialchars($record['poster']); ?></p>
                <?php endif; ?>
                <p class="fs-12 text-danger mt-1 mb-2"><?php if (!$valid_poster)
                  echo $file_upload_error; ?></p>
              </div>
              <div class="col">
                <label for="video" class="db-text-sec fs-18 form-label">Upload Video</label><br>
                <input type="file" name="video" id="video" class="form-control file-trending">
                <?php if ($is_edit && !empty($record['video'])): ?>
                  <p class="mt-1 mb-2 db-text-sec">Current: <?php echo htmlspecialchars($record['video']); ?></p>
                <?php endif; ?>
                <p class="fs-12 text-danger mt-1 mb-2"><?php if (!$valid_video)
                  echo $file_upload_error; ?></p>
              </div>
              <div class="col">
                <label for="modal-poster-title" class="db-text-sec fs-18 form-label text-nowrap">Upload Modal Poster
                  Title</label><br>
                <input type="file" name="modal-poster-title" id="modal-poster-title" class="form-control file-trending">
                <?php if ($is_edit && !empty($record['modal_poster_title'])): ?>
                  <p class="mt-1 mb-2 db-text-sec">Current: <?php echo htmlspecialchars($record['modal_poster_title']); ?>
                  </p>
                <?php endif; ?>
                <p class="fs-12 text-danger mt-1 mb-2"><?php if (!$valid_modal_poster_title)
                  echo $file_upload_error; ?>
                </p>
              </div>
            </div>
            <div class="row mb-2">
              <div class="col">
                <label for="title" class="db-text-sec fs-18 form-label">Title</label><br>
                <input type="text" name="title" id="title" class="form-control file-trending" placeholder="Avengers"
                  value="<?php echo htmlspecialchars($record['title'] ?? ''); ?>">
                <p class="fs-12 text-danger mt-1 mb-2">
                  <?php if (!$valid_title)
                    echo "Title must be at least 2 characters long"; ?>
                </p>
              </div>
              <div class="col">
                <label for="duration" class="db-text-sec fs-18 form-label">Duration</label><br>
                <input type="text" name="duration" id="duration" class="form-control file-trending"
                  placeholder="1hr 30mins" value="<?php echo htmlspecialchars($record['duration'] ?? ''); ?>">
                <p class="fs-12 text-danger mt-1 mb-2">
                  <?php if (!$valid_duration)
                    echo "Duration must be at least 2 characters long"; ?>
                </p>
              </div>
              <div class="col">
                <label for="date-released" class="db-text-sec fs-18 form-label text-nowrap">Date Released</label><br>
                <input type="number" name="date-released" id="date-released" class="form-control file-trending"
                  placeholder="0" value="<?php echo htmlspecialchars($record['date_released'] ?? ''); ?>">
                <p class="fs-12 text-danger mt-1 mb-2"><?php
                if (!$valid_date_released) {
                  echo "Date Released can not be empty";
                } elseif (!$valid_date_released_time) {
                  echo "Invalid Date Released Time";
                }
                ?></p>
              </div>
              <div class="col">
                <label for="age-rating" class="db-text-sec fs-18 form-label text-nowrap ">Age Rating</label><br>
                <input type="number" name="age-rating" id="age-rating" class="form-control file-trending"
                  placeholder="0" value="<?php echo htmlspecialchars($record['age_rating'] ?? ''); ?>">
                <p class="fs-12 text-danger mt-1 mb-2"><?php
                if (!$valid_age_rating) {
                  echo "Age rating can not be empty";
                } elseif (!$valid_age_rating_age) {
                  echo "Invalid age rating";
                }
                ?></p>
              </div>
              <div class="col">
                <label for="category" class="db-text-sec fs-18 form-label text-nowrap">Choose Category</label><br>
                <div class="category-container">
                  <select name="category" id="category" class="form-control">
                    <option value="Movie" class="file-trending" <?php echo ($record['category'] == 'Movie') ? 'selected' : ''; ?>>Movie</option>
                    <option value="Series" class="file-trending" <?php echo ($record['category'] == 'Series') ? 'selected' : ''; ?>>Series</option>
                  </select>
                  <i class="fa-solid fa-caret-down caret-genre"></i>
                </div>
              </div>
            </div>
            <div class="row mb-2">
              <div class="col">
                <label for="first-genre" class="db-text-sec fs-18 form-label">First Genre</label><br>
                <div class="genre-container">
                  <select name="first-genre" id="first-genre" class="form-control">
                    <?php foreach ($genres as $genre): ?>
                      <option value="<?= htmlspecialchars($genre) ?>" class="file-trending" <?php echo ($record['genre_1'] == $genre) ? 'selected' : ''; ?>><?= htmlspecialchars($genre) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <i class="fa-solid fa-caret-down caret-genre"></i>
                </div>
              </div>
              <div class="col">
                <label for="second-genre" class="db-text-sec fs-18 form-label">Second Genre</label><br>
                <div class="genre-container">
                  <select name="second-genre" id="second-genre" class="form-control">
                    <option value="">-- Select Genre --</option>
                    <?php foreach ($genres as $genre): ?>
                      <option value="<?= htmlspecialchars($genre) ?>" class="file-trending" <?php echo ($record['genre_2'] == $genre) ? 'selected' : ''; ?>><?= htmlspecialchars($genre) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <i class="fa-solid fa-caret-down caret-genre"></i>
                </div>
              </div>
              <div class="col">
                <label for="third-genre" class="db-text-sec fs-18 form-label">Third Genre</label><br>
                <div class="genre-container">
                  <select name="third-genre" id="third-genre" class="form-control">
                    <option value="">-- Select Genre --</option>
                    <?php foreach ($genres as $genre): ?>
                      <option value="<?= htmlspecialchars($genre) ?>" class="file-trending" <?php echo ($record['genre_3'] == $genre) ? 'selected' : ''; ?>><?= htmlspecialchars($genre) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <i class="fa-solid fa-caret-down caret-genre"></i>
                </div>
              </div>
              <div class="col-12 mt-3">
                <label for="cast" class="db-text-sec fs-18 form-label text-nowrap">Cast</label><br>
                <input type="text" name="cast" id="cast" class="form-control file-trending"
                  placeholder="Robert Downey Jr., Chris Evans, Scarlett Johansson..."
                  value="<?php echo htmlspecialchars($record['cast'] ?? ''); ?>">
                <p class="fs-12 text-danger mt-1 mb-2"><?php if (!$valid_cast)
                  echo "Cast actors can not be empty"; ?>
                </p>
              </div>
              <div class="col-12 mt-3">
                <label for="description" class="db-text-sec fs-18 form-label">Add Description</label><br>
                <textarea name="description" id="description" class="form-control file-trending p-2"
                  placeholder="The movie is about..."><?php echo htmlspecialchars($record['description'] ?? ''); ?></textarea>
                <p class="fs-12 text-danger mt-1 mb-2">
                  <?php if (!$valid_description)
                    echo "Description can not be empty"; ?>
                </p>
              </div>
            </div>
            <div class="d-flex justify-content-end mb-3">
              <button type="submit" name="submit-btn"
                class="btn db-bg-primary text-white ms-3 mt-3"><?php echo $is_edit ? 'Update' : 'Add'; ?> Trending</button>
            </div>
            <!-- ====== FULL FORM FIELDS END ====== -->

            <!-- SEASONS/EPISODES SECTION -->
            <div class="section-add-episodes p-3"
              style="display:<?php echo ($record['category'] == 'Series' ? 'block' : 'none'); ?>;">
              <h4 class="db-text-sec mb-3">Add Episodes by Season</h4>
              <div id="seasons-container">
                <?php if ($is_edit && count($seasons)): ?>
                  <?php foreach ($seasons as $sidx => $season): ?>
                    <div class="season-block" data-season="<?php echo $sidx; ?>"
                      data-seasonid="<?php echo $season['season_id']; ?>">
                      <button type="button" class="btn btn-outline-danger btn-sm season-delete-btn delete-season-btn"
                        data-seasonid="<?php echo $season['season_id']; ?>" title="Delete Season">
                        <i class="fa fa-trash"></i>
                      </button>
                      <div class="mb-2">
                        <label class="db-text-sec fs-18 form-label">Season Title</label>
                        <input type="text" placeholder="Season title" class="form-control file-trending"
                          name="season-title[]" value="<?php echo htmlspecialchars($season['season_title']); ?>">
                        <input type="hidden" name="season-number[]"
                          value="<?php echo htmlspecialchars($season['season_number']); ?>">
                      </div>
                      <div id="episodes-container-<?php echo $sidx; ?>">
                        <?php foreach ($season['episodes'] as $eidx => $ep): ?>
                          <div class="row mt-3 mb-2 episode-block" data-episode-id="<?php echo $ep['episode_id']; ?>">
                            <div class="col-12">
                              <div class="episode-divider"></div>
                            </div>
                            <div class="col">
                              <label class="db-text-sec fs-18 form-label">Upload Episode Video</label>
                              <input type="file" name="episode-video[<?php echo $sidx; ?>][]"
                                class="form-control file-trending">
                              <?php if (!empty($ep['episode_video'])): ?>
                                <p class="mt-1 mb-2 db-text-sec">Current: <?php echo htmlspecialchars($ep['episode_video']); ?>
                                </p>
                                <input type="hidden" name="existing-episode-video[<?php echo $sidx; ?>][<?php echo $eidx; ?>]"
                                  value="<?php echo htmlspecialchars($ep['episode_video']); ?>">
                              <?php endif; ?>
                            </div>
                            <div class="col">
                              <label class="db-text-sec fs-18 form-label">Episode Duration</label>
                              <input type="text" name="episode-duration[<?php echo $sidx; ?>][]"
                                class="form-control file-trending" placeholder="59min"
                                value="<?php echo htmlspecialchars($ep['episode_duration']); ?>">
                            </div>
                            <div class="col-12 mt-2">
                              <label class="db-text-sec fs-18 form-label">Episode Title</label>
                              <input type="text" name="episode-title[<?php echo $sidx; ?>][]"
                                class="form-control file-trending" placeholder="The title episode is about..."
                                value="<?php echo htmlspecialchars($ep['episode_title']); ?>">
                            </div>
                            <div class="col-12 mt-3">
                              <label class="db-text-sec fs-18 form-label">Add Episode Description</label>
                              <textarea name="episode-description[<?php echo $sidx; ?>][]"
                                class="form-control file-trending p-2"
                                placeholder="The episode is about..."><?php echo htmlspecialchars($ep['episode_description']); ?></textarea>
                            </div>
                            <?php if ($eidx > 0): ?>
                              <div class="col-12 mt-3">
                                <button type="button" class="btn btn-danger delete-episode-btn-db"
                                  data-episode-id="<?php echo $ep['episode_id']; ?>">Delete</button>
                              </div>
                            <?php endif; ?>
                          </div>
                        <?php endforeach; ?>
                      </div>
                      <button type="button" class="btn btn-secondary add-episode-btn mt-2"
                        data-season="<?php echo $sidx; ?>">Add Episode</button>
                    </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <div class="season-block" data-season="0">
                    <div class="mb-2">
                      <label class="db-text-sec fs-18 form-label">Season Title</label>
                      <input placeholder="Season title" type="text" class="form-control file-trending"
                        name="season-title[]" value="Season 1">
                      <input type="hidden" name="season-number[]" value="1">
                    </div>
                    <div id="episodes-container-0">
                      <div class="row mt-3 mb-2 episode-block">
                        <div class="col-12">
                          <div class="episode-divider"></div>
                        </div>
                        <div class="col">
                          <label class="db-text-sec fs-18 form-label">Upload Episode Video</label>
                          <input type="file" name="episode-video[0][]" class="form-control file-trending">
                        </div>
                        <div class="col">
                          <label class="db-text-sec fs-18 form-label">Episode Duration</label>
                          <input placeholder="59min" type="text" name="episode-duration[0][]"
                            class="form-control file-trending">
                        </div>
                        <div class="col-12 mt-2">
                          <label class="db-text-sec fs-18 form-label">Episode Title</label>
                          <input placeholder="The title episode is about..." type="text" name="episode-title[0][]"
                            class="form-control file-trending">
                        </div>
                        <div class="col-12 mt-3">
                          <label class="db-text-sec fs-18 form-label">Add Episode Description</label>
                          <textarea placeholder="The episode is about..." name="episode-description[0][]"
                            class="form-control file-trending p-2"></textarea>
                        </div>
                        <!-- No delete button for the first/required episode -->
                      </div>
                    </div>
                    <button type="button" class="btn btn-secondary add-episode-btn mt-2" data-season="0">Add
                      Episode</button>
                  </div>
                <?php endif; ?>
              </div>
              <div class="d-flex gap-2 mt-3">
                <button type="button" class="btn db-text-sec" id="add-season-btn" style="border:1px solid #607EBC;">Add
                  More Season</button>
              </div>
            </div>
          </form>
          <!-- ====== FULL FORM FIELDS END ====== -->
        </div>
      </div>
    </section>

    <!-- ===== START OF MODAL DELETE FOR EPISODE =====  -->
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete-episode-btn-modal"])) {
      $delete_id = intval($_POST["delete-episode-id"]);
      $delete_query = "DELETE FROM tbl_trend_episodes WHERE episode_id = $delete_id";
      $result = mysqli_query($con, $delete_query);

      if (!$result) {
        die("" . mysqli_error($con));
      } else {
        echo "<script>
      alert('Deleted Successfully');
      window.location.href=window.location.href;
    </script>";
      }
    }
    ?>

    <div class="modal fade" id="modalDeleteEpisode" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <form action="" method="post">
            <div class="modal-header">
              <h1 class="modal-title fs-5 db-text-sec">Delete Episode</h1>
              <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal"
                style="filter: invert(1) grayscale(100%) brightness(200%); opacity: 1;"></button>
            </div>
            <div class="modal-body px-4">
              <input type="hidden" name="delete-episode-id" id="delete-episode-id">
              <h4 class="my-4 db-text-sec">Are you sure you want to delete this episode?</h4>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal">NO</button>
              <button type="submit" class="btn db-bg-primary db-text-sec" name="delete-episode-btn-modal"
                style="color: #f4fff8">YES</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- =====  END OF MODAL DELETE FOR EPISODE =====  -->

    <!-- ===== START OF MODAL DELETE FOR SEASON =====  -->
    <div class="modal fade" id="modalDeleteSeason" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <form>
            <div class="modal-header">
              <h1 class="modal-title fs-5 db-text-sec">Delete Season</h1>
              <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal"
                style="filter: invert(1) grayscale(100%) brightness(200%); opacity: 1;"></button>
            </div>
            <div class="modal-body px-4">
              <input type="hidden" id="delete-season-id">
              <h4 class="my-4 db-text-sec">Are you sure you want to delete this season and all its episodes?</h4>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal">NO</button>
              <button type="button" class="btn db-bg-primary db-text-sec" style="color: #f4fff8"
                id="confirm-delete-season">YES</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- =====  END OF MODAL DELETE FOR SEASON =====  -->
  </main>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    $(document).ready(function () {
      <?php if (!$valid_episodes && isset($episode_errors['alert'])): ?>
        alert("<?php echo addslashes($episode_errors['alert']); ?>");
      <?php endif; ?>

      // Show/hide the episodes section on category change and allow adding
      $('#category').on('change', function () {
        if ($(this).val() === 'Series') {
          $('.section-add-episodes').show();
          // Add default season if none exists
          if ($('#seasons-container').children().length === 0) {
            let html = `
              <div class="season-block" data-season="0">
                <div class="mb-2">
                  <label class="db-text-sec fs-18 form-label">Season Title</label>
                  <input placeholder="Season title" type="text" class="form-control file-trending"
                    name="season-title[]" value="Season 1">
                  <input type="hidden" name="season-number[]" value="1">
                </div>
                <div id="episodes-container-0">
                  <div class="row mt-3 mb-2 episode-block">
                    <div class="col-12">
                      <div class="episode-divider"></div>
                    </div>
                    <div class="col">
                      <label class="db-text-sec fs-18 form-label">Upload Episode Video</label>
                      <input type="file" name="episode-video[0][]" class="form-control file-trending">
                    </div>
                    <div class="col">
                      <label class="db-text-sec fs-18 form-label">Episode Duration</label>
                      <input placeholder="59min" type="text" name="episode-duration[0][]" class="form-control file-trending">
                    </div>
                    <div class="col-12 mt-2">
                      <label class="db-text-sec fs-18 form-label">Episode Title</label>
                      <input placeholder="The title episode is about..." type="text" name="episode-title[0][]" class="form-control file-trending">
                    </div>
                    <div class="col-12 mt-3">
                      <label class="db-text-sec fs-18 form-label">Add Episode Description</label>
                      <textarea placeholder="The episode is about..." name="episode-description[0][]" class="form-control file-trending p-2"></textarea>
                    </div>
                  </div>
                </div>
                <button type="button" class="btn btn-secondary add-episode-btn mt-2" data-season="0">Add Episode</button>
              </div>
            `;
            $('#seasons-container').html(html);
          }
        } else {
          $('.section-add-episodes').hide();
        }
      });

      // Add Episode Button (add new blank episode row for given season)
      $(document).on('click', '.add-episode-btn', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var seasonIdx = $(this).data('season');
        var epHtml = `
      <div class="row mt-3 mb-2 episode-block">
        <div class="col-12">
          <div class="episode-divider"></div>
        </div>
        <div class="col">
          <label class="db-text-sec fs-18 form-label">Upload Episode Video</label>
          <input type="file" name="episode-video[`+ seasonIdx + `][]" class="form-control file-trending">
        </div>
        <div class="col">
          <label class="db-text-sec fs-18 form-label">Episode Duration</label>
          <input placeholder="59min" type="text" name="episode-duration[`+ seasonIdx + `][]" class="form-control file-trending">
        </div>
        <div class="col-12 mt-2">
          <label class="db-text-sec fs-18 form-label">Episode Title</label>
          <input placeholder="The title episode is about..." type="text" name="episode-title[`+ seasonIdx + `][]" class="form-control file-trending">
        </div>
        <div class="col-12 mt-3">
          <label class="db-text-sec fs-18 form-label">Add Episode Description</label>
          <textarea placeholder="The episode is about..." name="episode-description[`+ seasonIdx + `][]" class="form-control file-trending p-2"></textarea>
        </div>
        <div class="col-12 mt-3">
          <button type="button" class="btn btn-danger delete-episode-btn">Delete</button>
        </div>
      </div>
    `;
        $('#episodes-container-' + seasonIdx).append(epHtml);
        return false;
      });

      // Delete Episode Button for client-side (not in DB yet)
      $(document).on('click', '.delete-episode-btn', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).closest('.episode-block').remove();
        return false;
      });

      // Episode Delete Modal (DB)
      $(document).on('click', '.delete-episode-btn-db', function () {
        var episodeId = $(this).data('episode-id');
        $('#delete-episode-id').val(episodeId);
        $('#modalDeleteEpisode').modal('show');
      });

      // Add More Season (with jQuery)
      let seasonCounter = <?php echo ($is_edit && count($seasons)) ? count($seasons) : 1; ?>;
      $('#add-season-btn').click(function (e) {
        e.preventDefault();
        e.stopPropagation();
        let seasonIdx = seasonCounter++;
        let html = `
      <div class="season-block" data-season="` + seasonIdx + `">
        <div class="mb-2">
          <label class="db-text-sec fs-18 form-label">Season Title</label>
          <input type="text" class="form-control file-trending" name="season-title[]" value="Season ` + (seasonIdx + 1) + `" >
          <input type="hidden" name="season-number[]" value="` + (seasonIdx + 1) + `">
        </div>
        <div id="episodes-container-` + seasonIdx + `">
          <div class="row mt-3 mb-2 episode-block">
            <div class="col-12">
              <div class="episode-divider"></div>
            </div>
            <div class="col">
              <label class="db-text-sec fs-18 form-label">Upload Episode Video</label>
              <input type="file" name="episode-video[` + seasonIdx + `][]" class="form-control file-trending">
            </div>
            <div class="col">
              <label class="db-text-sec fs-18 form-label">Episode Duration</label>
              <input type="text" name="episode-duration[` + seasonIdx + `][]" class="form-control file-trending">
            </div>
            <div class="col-12 mt-2">
              <label class="db-text-sec fs-18 form-label">Episode Title</label>
              <input type="text" name="episode-title[` + seasonIdx + `][]" class="form-control file-trending">
            </div>
            <div class="col-12 mt-3">
              <label class="db-text-sec fs-18 form-label">Add Episode Description</label>
              <textarea name="episode-description[` + seasonIdx + `][]" class="form-control file-trending p-2"></textarea>
            </div>
          </div>
        </div>
        <button type="button" class="btn btn-secondary add-episode-btn mt-2" data-season="` + seasonIdx + `">Add Episode</button>
      </div>
    `;
        $('#seasons-container').append(html);
        return false;
      });

      // ===== SEASON DELETE MODAL (AJAX) =====
      var seasonToDelete = null;
      $(document).on('click', '.delete-season-btn', function (e) {
        e.preventDefault();
        e.stopPropagation();
        seasonToDelete = $(this).data('seasonid');
        $('#delete-season-id').val(seasonToDelete);
        $('#modalDeleteSeason').modal('show');
      });

      $('#confirm-delete-season').on('click', function () {
        var season_id = $('#delete-season-id').val();
        $.ajax({
          url: window.location.href,
          type: 'POST',
          data: { delete_season_id: season_id }
        }).always(function () {
          $('#modalDeleteSeason').modal('hide');
          alert('Season and all episodes deleted successfully');
          window.location.href = window.location.href;
        });
      });
    });
  </script>
</body>

</html>