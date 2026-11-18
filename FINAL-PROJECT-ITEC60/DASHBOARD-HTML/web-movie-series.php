<?php
include '../includes/dashboard-header-sidebar.php';
include '../includes/db-connection.php';
require_once '../includes/tmdb.php';

// Fetch genres from the DB for select boxes
$genres = [];
$genreResult = mysqli_query($con, "SELECT genre_id, genre_name FROM tbl_movie_series_genre ORDER BY genre_name");
while ($row = mysqli_fetch_assoc($genreResult)) {
    $genres[] = $row; // ['genre_id' => ..., 'genre_name' => ...]
}

// AJAX DELETE HANDLER for episode delete in edit mode
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_episode_id'])) {
    $episode_id = intval($_POST['delete_episode_id']);
    mysqli_query($con, "DELETE FROM tbl_movie_series_episodes WHERE episode_id = $episode_id");
    echo 'success';
    exit;
}

// AJAX DELETE HANDLER for season delete (and all its episodes)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_season_id'])) {
    $season_id = intval($_POST['delete_season_id']);
    mysqli_query($con, "DELETE FROM tbl_movie_series_episodes WHERE season_id = $season_id");
    mysqli_query($con, "DELETE FROM tbl_movie_series_seasons WHERE season_id = $season_id");
    echo 'success';
    exit;
}

// Validation variables
$file_upload_error = "File can not be empty";
$valid_poster = $valid_video = $valid_modal_poster_title = $valid_description = $valid_cast = $valid_title = $valid_duration = $valid_date_released = $valid_date_released_time = $valid_age_rating = $valid_age_rating_age = true;
$valid_episodes = true;

$date_released = 0;
$age_rating = 0;

$file_poster_path = '';
$file_video_path = '';
$file_modal_poster_title_path = '';
$tmdb_error = '';

// Detect mode
$movie_series_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$is_edit = $movie_series_id > 0;

// Default record values (for add mode)
$record = [
    'title' => '',
    'duration' => '',
    'poster' => '',
    'video' => '',
    'modal_poster_title' => '',
    'date_released' => '',
    'age_rating' => '',
    'category' => ($_GET['category'] ?? '') === 'Series' ? 'Series' : 'Movie', // "Add" from the Series list starts as a series
    'genre_id1' => '',
    'genre_id2' => '',
    'genre_id3' => '',
    'cast' => '',
    'description' => ''
];

// For edit, fetch the record and episodes
$episodes = [];
$seasons = [];

if ($is_edit) {
    $result = mysqli_query($con, "SELECT * FROM tbl_movie_series WHERE movie_series_id = $movie_series_id LIMIT 1");
    if ($result && mysqli_num_rows($result) > 0) {
        $record = mysqli_fetch_assoc($result);
        if ($record['category'] === 'Series') {
            $seasonq = mysqli_query($con, "SELECT * FROM tbl_movie_series_seasons WHERE movie_series_id = $movie_series_id ORDER BY season_number");
            while ($season = mysqli_fetch_assoc($seasonq)) {
                $epq = mysqli_query($con, "SELECT * FROM tbl_movie_series_episodes WHERE season_id = {$season['season_id']} ORDER BY episode_id");
                $season['episodes'] = [];
                while ($ep = mysqli_fetch_assoc($epq))
                    $season['episodes'][] = $ep;
                $seasons[] = $season;
            }
        }
    } else {
        echo "<script>alert('Record not found.'); window.location.href='web-movies.php';</script>";
        exit();
    }
}

// Handle add or update on POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_episode_id']) && !isset($_POST['delete_season_id'])) {
    $valid_poster = $valid_video = $valid_modal_poster_title = $valid_age_rating = true;

    // Poster (optional for edit, required for add)
    // Uploads: only real images/videos are accepted, saved under random names.
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        $file_poster = cv_store_upload($_FILES['poster']['tmp_name'], $_FILES['poster']['name'], CV_IMAGE_TYPES, 'MOVIE_SERIES_TITLE', $upload_err);
        if ($file_poster === null) {
            $valid_poster = false;
            $file_upload_error = "Poster: $upload_err";
        } else {
            $file_poster_path = 'MOVIE_SERIES_TITLE/' . $file_poster;
        }
    } elseif (!empty($_POST['tmdb-poster-path'])) {
        // No upload, but a title was picked from TMDB: download its poster.
        $file_poster = tmdb_save_image($_POST['tmdb-poster-path'], 'w500', 'MOVIE_SERIES_TITLE', $upload_err);
        if ($file_poster === null) {
            $valid_poster = false;
            $file_upload_error = "Poster from TMDB: $upload_err Upload one instead.";
        } else {
            $file_poster_path = 'MOVIE_SERIES_TITLE/' . $file_poster;
        }
    } else {
        $file_poster = $is_edit ? $record['poster'] : '';
        if (!$is_edit && empty($file_poster))
            $valid_poster = false;
    }

    // Video (optional for edit, required for add)
    if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK && !is_array($_FILES['video']['name'])) {
        $file_video = cv_store_upload($_FILES['video']['tmp_name'], $_FILES['video']['name'], CV_VIDEO_TYPES, 'MOVIE_SERIES_VIDEO', $upload_err);
        if ($file_video === null) {
            $valid_video = false;
            $file_upload_error = "Video: $upload_err";
        } else {
            $file_video_path = 'MOVIE_SERIES_VIDEO/' . $file_video;
        }
    } elseif (!empty($_POST['stream-url'])) {
        // No upload, but a free full movie was picked from the Internet Archive.
        if (cv_is_stream_url($_POST['stream-url'])) {
            $file_video = mysqli_real_escape_string($con, $_POST['stream-url']);
        } else {
            $valid_video = false;
            $file_upload_error = 'Video: that is not an Internet Archive video link.';
        }
    } else {
        $file_video = $is_edit ? $record['video'] : '';
        if (!$is_edit && empty($file_video))
            $valid_video = false;
    }

    // Modal Poster Title
    if (isset($_FILES['modal-poster-title']) && $_FILES['modal-poster-title']['error'] === UPLOAD_ERR_OK) {
        $file_modal_poster_title = cv_store_upload($_FILES['modal-poster-title']['tmp_name'], $_FILES['modal-poster-title']['name'], CV_IMAGE_TYPES, 'MOVIE_SERIES_TITLE', $upload_err);
        if ($file_modal_poster_title === null) {
            $valid_modal_poster_title = false;
            $file_upload_error = "Title image: $upload_err";
        } else {
            $file_modal_poster_title_path = 'MOVIE_SERIES_TITLE/' . $file_modal_poster_title;
        }
    } elseif (!empty($_POST['tmdb-logo-path'])) {
        // No upload, but TMDB has a title logo for the picked title: download it.
        $file_modal_poster_title = tmdb_save_image($_POST['tmdb-logo-path'], 'w500', 'MOVIE_SERIES_TITLE', $upload_err);
        if ($file_modal_poster_title === null) {
            $valid_modal_poster_title = false;
            $file_upload_error = "Title image from TMDB: $upload_err Upload one instead.";
        } else {
            $file_modal_poster_title_path = 'MOVIE_SERIES_TITLE/' . $file_modal_poster_title;
        }
    } else {
        $file_modal_poster_title = $is_edit ? $record['modal_poster_title'] : '';
        if (!$is_edit && empty($file_modal_poster_title))
            $valid_modal_poster_title = false;
    }

    // Which TMDB title this came from (kept on edit unless a new one is picked).
    $tmdb_type = in_array($_POST['tmdb-type'] ?? '', ['movie', 'tv'], true) ? $_POST['tmdb-type'] : null;
    $tmdb_id = (int) ($_POST['tmdb-id'] ?? 0) ?: null;
    if (!$tmdb_id || !$tmdb_type) {
        $tmdb_id = $is_edit ? ($record['tmdb_id'] ?? null) : null;
        $tmdb_type = $is_edit ? ($record['tmdb_type'] ?? null) : null;
    }
    // The same TMDB title can only be in the library once.
    $tmdb_error = '';
    if ($tmdb_id && $tmdb_type) {
        $dup = $con->prepare("SELECT movie_series_id, title FROM tbl_movie_series WHERE tmdb_type = ? AND tmdb_id = ? AND movie_series_id <> ? LIMIT 1");
        $tid = (int) $tmdb_id;
        $self = (int) $movie_series_id;
        $dup->bind_param('sii', $tmdb_type, $tid, $self);
        $dup->execute();
        $dupRow = $dup->get_result()->fetch_assoc();
        $dup->close();
        if ($dupRow) {
            $tmdb_error = '"' . $dupRow['title'] . '" from TMDB is already in your library (record #' . $dupRow['movie_series_id'] . ').';
        }
    }
    $tmdb_id_sql = $tmdb_id ? (int) $tmdb_id : 'NULL';
    $tmdb_type_sql = $tmdb_type ? "'" . $tmdb_type . "'" : 'NULL';

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
        $temp = (int) $_POST['date-released'];
        if ($temp <= (int) date('Y') + 1 && $temp >= 1900) {
            $date_released = $temp;
        } else {
            $valid_date_released_time = false;
        }
    } else {
        $valid_date_released = false;
    }

    // Validate age rating
    if (isset($_POST['age-rating']) && $_POST['age-rating'] !== '') {
        $tempo = (int) $_POST['age-rating'];
        if ($tempo >= 1 && $tempo <= 18) {
            $age_rating = $tempo;
        } else {
            $valid_age_rating_age = false;
        }
    } else {
        $valid_age_rating = false;
    }

    // Category
    $category = ($_POST['category'] ?? '') === 'Series' ? 'Series' : 'Movie';

    // Genres (IDs)
    $genre_id1 = intval($_POST['first-genre'] ?? 0);
    $genre_id2 = ($_POST['second-genre'] ?? '') !== '' ? intval($_POST['second-genre']) : 'NULL';
    $genre_id3 = ($_POST['third-genre'] ?? '') !== '' ? intval($_POST['third-genre']) : 'NULL';

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

    // Series/episode logic unchanged...
    $is_series = ($category === 'Series');
    $season_titles = $_POST['season-title'] ?? [];
    $season_numbers = $_POST['season-number'] ?? [];
    $valid_seasons = true;
    $valid_episodes = true;
    $episode_errors = [];

    // EPISODE VALIDATION
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
                count($ep_titles), count($ep_descs), count($ep_durations),
                count($ep_video_files), count($existing_videos)
            );
            for ($eidx = 0; $eidx < $max_eidx; $eidx++) {
                $title_ep = isset($ep_titles[$eidx]) ? trim($ep_titles[$eidx]) : '';
                $desc = isset($ep_descs[$eidx]) ? trim($ep_descs[$eidx]) : '';
                $duration_ep = isset($ep_durations[$eidx]) ? trim($ep_durations[$eidx]) : '';
                $video = isset($ep_video_files[$eidx]) ? trim($ep_video_files[$eidx]) : '';
                $existing = isset($existing_videos[$eidx]) ? trim($existing_videos[$eidx]) : '';
                if ($title_ep === '' && $desc === '' && $duration_ep === '' && $video === '' && $existing === '') {
                    continue;
                }
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
            $valid_duration && $tmdb_error === '' && (!$is_series || ($valid_seasons && $valid_episodes))
        )
    ) {
        $record['title'] = $_POST['title'] ?? '';
        $record['duration'] = $_POST['duration'] ?? '';
        $record['date_released'] = $_POST['date-released'] ?? '';
        $record['age_rating'] = $_POST['age-rating'] ?? '';
        $record['category'] = $_POST['category'] ?? '';
        $record['genre_id1'] = $_POST['first-genre'] ?? '';
        $record['genre_id2'] = $_POST['second-genre'] ?? '';
        $record['genre_id3'] = $_POST['third-genre'] ?? '';
        $record['cast'] = $_POST['cast'] ?? '';
        $record['description'] = $_POST['description'] ?? '';
    }

    if (
        $valid_poster && $valid_video && $valid_modal_poster_title && $valid_date_released && $valid_age_rating &&
        $valid_description && $valid_age_rating_age && $valid_date_released_time && $valid_title && $valid_cast &&
        $valid_duration && $tmdb_error === '' && (!$is_series || ($valid_seasons && $valid_episodes))
    ) {
        if ($is_edit) {
            $update_sql = "
                UPDATE tbl_movie_series SET
                    title = '$title',
                    duration = '$duration',
                    poster = '$file_poster',
                    video = '$file_video',
                    modal_poster_title = '$file_modal_poster_title',
                    date_released = '$date_released',
                    age_rating = '$age_rating',
                    category = '$category',
                    genre_id1 = $genre_id1,
                    genre_id2 = $genre_id2,
                    genre_id3 = $genre_id3,
                    cast = '$cast',
                    description = '$description',
                    tmdb_id = $tmdb_id_sql,
                    tmdb_type = $tmdb_type_sql
                WHERE movie_series_id = $movie_series_id
            ";
            $query = mysqli_query($con, $update_sql);
            $msg = "Data Updated Successfully";
            $saved_movie_series_id = $movie_series_id;
        } else {
            $insert_sql = "
                INSERT INTO tbl_movie_series
                    (title, duration, poster, video, modal_poster_title, date_released, age_rating, category, genre_id1, genre_id2, genre_id3, cast, description, tmdb_id, tmdb_type)
                VALUES (
                    '$title',
                    '$duration',
                    '$file_poster',
                    '$file_video',
                    '$file_modal_poster_title',
                    '$date_released',
                    '$age_rating',
                    '$category',
                    $genre_id1,
                    $genre_id2,
                    $genre_id3,
                    '$cast',
                    '$description',
                    $tmdb_id_sql,
                    $tmdb_type_sql
                )";
            $query = mysqli_query($con, $insert_sql);
            $msg = "Data Added Successfully";
            $saved_movie_series_id = mysqli_insert_id($con);
        }

        if (!$query) {
            die(mysqli_error($con));
        } else {
            if ($is_series) {
                if ($is_edit) {
                    $seasonidsq = mysqli_query($con, "SELECT season_id FROM tbl_movie_series_seasons WHERE movie_series_id = $saved_movie_series_id");
                    while ($row = mysqli_fetch_assoc($seasonidsq)) {
                        $sid = $row['season_id'];
                        mysqli_query($con, "DELETE FROM tbl_movie_series_episodes WHERE season_id = $sid");
                    }
                    mysqli_query($con, "DELETE FROM tbl_movie_series_seasons WHERE movie_series_id = $saved_movie_series_id");
                }
                foreach ($season_titles as $sidx => $season_title) {
                    $season_title_esc = mysqli_real_escape_string($con, $season_title);
                    $season_num = intval($season_numbers[$sidx]);
                    mysqli_query($con, "INSERT INTO tbl_movie_series_seasons (movie_series_id, season_number, season_title) VALUES ($saved_movie_series_id, $season_num, '$season_title_esc')");
                    $season_id = mysqli_insert_id($con);
                    $ep_titles = $_POST['episode-title'][$sidx] ?? [];
                    $ep_descs = $_POST['episode-description'][$sidx] ?? [];
                    $ep_durations = $_POST['episode-duration'][$sidx] ?? [];
                    $video_files = $_FILES['episode-video']['name'][$sidx] ?? [];
                    $video_tmps = $_FILES['episode-video']['tmp_name'][$sidx] ?? [];
                    $existing_videos = $_POST['existing-episode-video'][$sidx] ?? [];
                    $max_eidx = max(
                        count($ep_titles), count($ep_descs), count($ep_durations),
                        count($video_files), count($existing_videos)
                    );
                    for ($eidx = 0; $eidx < $max_eidx; $eidx++) {
                        $title_ep = isset($ep_titles[$eidx]) ? trim($ep_titles[$eidx]) : '';
                        $desc = isset($ep_descs[$eidx]) ? trim($ep_descs[$eidx]) : '';
                        $duration_ep = isset($ep_durations[$eidx]) ? trim($ep_durations[$eidx]) : '';
                        $video = isset($video_files[$eidx]) ? trim($video_files[$eidx]) : '';
                        $existing = isset($existing_videos[$eidx]) ? trim($existing_videos[$eidx]) : '';
                        if ($title_ep === '' && $desc === '' && $duration_ep === '' && $video === '' && $existing === '') {
                            continue;
                        }
                        if ($title_ep === '' || $desc === '' || $duration_ep === '' || ($video === '' && $existing === '')) {
                            continue;
                        }
                        $ep_video_name = '';
                        if ($video !== '' && isset($video_tmps[$eidx]) && $video_tmps[$eidx]) {
                            $ep_video_name = (string) cv_store_upload($video_tmps[$eidx], $video, CV_VIDEO_TYPES, 'MOVIE_SERIES_EPISODE', $upload_err);
                        }
                        if ($ep_video_name === '' && $existing !== '') {
                            // Keep only a plain file name, never a path.
                            $ep_video_name = basename($existing);
                        }
                        $ep_video_name = mysqli_real_escape_string($con, $ep_video_name);
                        $ep_title_esc = mysqli_real_escape_string($con, $title_ep);
                        $ep_desc_esc = mysqli_real_escape_string($con, $desc);
                        $ep_duration_esc = mysqli_real_escape_string($con, $duration_ep);
                        $ep_sql = "INSERT INTO tbl_movie_series_episodes (season_id, episode_title, episode_description, episode_duration, episode_video) VALUES ($season_id, '$ep_title_esc', '$ep_desc_esc', '$ep_duration_esc', '$ep_video_name')";
                        mysqli_query($con, $ep_sql);
                    }
                }
            }
            echo "<script>
                    alert('$msg');
                    window.location.href='web-movies.php';
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

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Action - <?php echo $is_edit ? 'Edit' : 'Add'; ?> Movie/Series</title>
    <link rel="stylesheet" href="../DASHBOARD-CSS/dashboard.css?v=<?php echo CV_ASSET_VERSION; ?>">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .season-block {
            border: 1px solid #3D3326; margin-bottom: 16px; padding: 16px; background: #222; position: relative;
        }
        .season-delete-btn { position: absolute; top: 10px; right: 10px; z-index: 10;}
        .season-divider { width: 100%; margin: 24px 0 8px 0; height: 2px; background-color: #3D3326;}
        .episode-divider { height: 1px; background-color: #3D3326; width: 100%; margin: 16px 0;}
    </style>
</head>
<body>
  
<main class="container-lg p-0 overflow-hidden">
    <?php $from = isset($_GET['from']) ? $_GET['from'] : 'web-movies.php';  ?>
    <a href="<?php echo htmlspecialchars($from); ?>" class="btn db-bg-primary text-white ms-3 mt-3">
      <i class="fa-solid fa-chevron-left"></i> Go Back
    </a>
    <section class="signup-accounts-section text-left p-3">
        <div class="card bg-dark">
            <div class="card-body">
                <div class="table-task-top db-text-sec">
                    <div class="py-2 ps-3">
                        <p class="m-0 fs-20"><?php echo $is_edit ? 'Edit' : 'Add'; ?> Movie/Series Record</p>
                        <p class="m-0 fs-14" style="transform: translateY(-2px)">
                            <?php echo $is_edit ? 'Edit your entry for the Movie/Series database' : 'Add your favorite movies/series to the database here'; ?>
                        </p>
                    </div>
                </div>
                <!-- ====== FULL FORM FIELDS START ====== -->
                <form method="post" enctype="multipart/form-data" id="movie-series-form"
                    data-has-poster="<?php echo $is_edit && !empty($record['poster']) ? '1' : '0'; ?>"
                    data-has-video="<?php echo $is_edit && !empty($record['video']) ? '1' : '0'; ?>"
                    data-has-logo="<?php echo $is_edit && !empty($record['modal_poster_title']) ? '1' : '0'; ?>">

                    <!-- ====== SEARCH TMDB: fills the form from The Movie Database ====== -->
                    <div class="cv-tmdb mt-3">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                            <div>
                                <p class="m-0 fs-18 fw-semibold db-text-sec"><i class="fa-solid fa-wand-magic-sparkles me-2"></i>Step 1: Details from TMDB</p>
                                <p class="m-0 fs-14 cv-tmdb-sub">Search for the title. Its details, poster and title image fill in for you, and Step 2 searches for a free copy of it automatically.</p>
                            </div>
                            <span class="cv-tmdb-credit">Data from TMDB</span>
                        </div>
                        <?php if (!tmdb_configured()): ?>
                            <div class="alert alert-warning py-2 mb-2 fs-14">
                                TMDB is not set up yet. Add your free TMDB API key to <code>includes/config.php</code> (<code>tmdb_api_key</code>).
                            </div>
                        <?php endif; ?>
                        <?php if ($tmdb_error): ?>
                            <div class="alert alert-danger py-2 mb-2 fs-14"><?php echo e($tmdb_error); ?></div>
                        <?php endif; ?>
                        <div class="input-group">
                            <input type="search" id="tmdb-query" class="form-control" placeholder="Type a title, e.g. Inside Out 2"
                                autocomplete="off" <?php echo tmdb_configured() ? '' : 'disabled'; ?>>
                            <button type="button" id="tmdb-search-btn" class="btn db-bg-primary text-white" <?php echo tmdb_configured() ? '' : 'disabled'; ?>>
                                <i class="fa-solid fa-magnifying-glass"></i> Search
                            </button>
                        </div>
                        <div id="tmdb-status" class="fs-14 mt-2 cv-tmdb-sub" aria-live="polite"></div>
                        <div id="tmdb-results" class="cv-tmdb-results"></div>
                        <div id="tmdb-picked" class="cv-tmdb-picked d-none"></div>

                        <input type="hidden" name="tmdb-id" id="tmdb-id" value="<?php echo e($_POST['tmdb-id'] ?? ''); ?>">
                        <input type="hidden" name="tmdb-type" id="tmdb-type" value="<?php echo e($_POST['tmdb-type'] ?? ''); ?>">
                        <input type="hidden" name="tmdb-poster-path" id="tmdb-poster-path" value="<?php echo e($_POST['tmdb-poster-path'] ?? ''); ?>">
                        <input type="hidden" name="tmdb-logo-path" id="tmdb-logo-path" value="<?php echo e($_POST['tmdb-logo-path'] ?? ''); ?>">
                    </div>

                    <!-- ====== FREE FULL MOVIE: streams from the Internet Archive ====== -->
                    <div class="cv-tmdb mt-3">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                            <div>
                                <p class="m-0 fs-18 fw-semibold db-text-sec"><i class="fa-solid fa-film me-2"></i>Step 2: The video</p>
                                <p class="m-0 fs-14 cv-tmdb-sub">Pick a free, public domain copy from the Internet Archive (it streams in CineVault's own player), or upload your own file below. Only older classics are free.</p>
                            </div>
                            <span class="cv-tmdb-credit">Videos from archive.org</span>
                        </div>
                        <div class="input-group">
                            <input type="search" id="ia-query" class="form-control" placeholder="Type a title, e.g. Night of the Living Dead" autocomplete="off">
                            <button type="button" id="ia-search-btn" class="btn db-bg-primary text-white">
                                <i class="fa-solid fa-magnifying-glass"></i> Search
                            </button>
                        </div>
                        <div id="ia-status" class="fs-14 mt-2 cv-tmdb-sub" aria-live="polite"></div>
                        <div id="ia-results" class="cv-tmdb-results"></div>
                        <?php $picked_stream = (string) ($_POST['stream-url'] ?? ''); ?>
                        <div id="ia-picked" class="cv-tmdb-picked <?php echo cv_is_stream_url($picked_stream) ? '' : 'd-none'; ?>">
                            <?php if (cv_is_stream_url($picked_stream)): ?>
                                <div class="flex-grow-1 fs-14">
                                    <p class="mb-1 fw-semibold"><i class="fa-solid fa-circle-check text-info me-1"></i>Free full movie still selected</p>
                                    <p class="mb-0 cv-tmdb-sub text-break"><?php echo e(rawurldecode(basename($picked_stream))); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <input type="hidden" name="stream-url" id="stream-url" value="<?php echo e($_POST['stream-url'] ?? ''); ?>">
                    </div>
              
                    <div class="row mt-3 mb-2">
                        <div class="col-12">
                            <label for="poster" class="db-text-sec fs-18 form-label">Upload Poster</label><br>
                            <input type="file" name="poster" id="poster" class="form-control file-trending">
                            <?php if ($is_edit && !empty($record['poster'])): ?>
                                <p class="mt-1 mb-2 db-text-sec">Current: <?php echo htmlspecialchars($record['poster']); ?></p>
                            <?php endif; ?>
                            <p class="fs-12 text-danger mt-1 mb-2"><?php if (!$valid_poster) echo $file_upload_error; ?></p>
                        </div>
                        <div class="col">
                            <label for="video" class="db-text-sec fs-18 form-label">Upload Video <span class="fs-14 cv-tmdb-sub">(or pick a free full movie above)</span></label><br>
                            <input type="file" name="video" id="video" class="form-control file-trending">
                            <?php if ($is_edit && !empty($record['video'])): ?>
                                <p class="mt-1 mb-2 db-text-sec">Current: <?php echo htmlspecialchars($record['video']); ?></p>
                            <?php endif; ?>
                            <p class="fs-12 text-danger mt-1 mb-2"><?php if (!$valid_video) echo $file_upload_error; ?></p>
                        </div>
                        <div class="col">
                            <label for="modal-poster-title" class="db-text-sec fs-18 form-label text-nowrap">Upload Modal Poster Title</label><br>
                            <input type="file" name="modal-poster-title" id="modal-poster-title" class="form-control file-trending">
                            <?php if ($is_edit && !empty($record['modal_poster_title'])): ?>
                                <p class="mt-1 mb-2 db-text-sec">Current: <?php echo htmlspecialchars($record['modal_poster_title']); ?></p>
                            <?php endif; ?>
                            <p class="fs-12 text-danger mt-1 mb-2"><?php if (!$valid_modal_poster_title) echo $file_upload_error; ?></p>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col">
                            <label for="title" class="db-text-sec fs-18 form-label">Title</label><br>
                            <input type="text" name="title" id="title" class="form-control file-trending" placeholder="Avengers"
                                   value="<?php echo htmlspecialchars($record['title'] ?? ''); ?>">
                            <p class="fs-12 text-danger mt-1 mb-2"><?php if (!$valid_title) echo "Title must be at least 2 characters long"; ?></p>
                        </div>
                        <div class="col">
                            <label for="duration" class="db-text-sec fs-18 form-label">Duration</label><br>
                            <input type="text" name="duration" id="duration" class="form-control file-trending"
                                   placeholder="1hr 30min" value="<?php echo htmlspecialchars($record['duration'] ?? ''); ?>">
                            <p class="fs-12 text-danger mt-1 mb-2"><?php if (!$valid_duration) echo "Duration must be at least 2 characters long"; ?></p>
                        </div>
                        <div class="col">
                            <label for="date-released" class="db-text-sec fs-18 form-label text-nowrap">Date Released</label><br>
                            <input type="number" name="date-released" id="date-released" class="form-control file-trending"
                                   placeholder="2022" value="<?php echo htmlspecialchars($record['date_released'] ?? ''); ?>">
                            <p class="fs-12 text-danger mt-1 mb-2"><?php
                            if (!$valid_date_released) { echo "Date Released can not be empty";
                            } elseif (!$valid_date_released_time) { echo "Invalid Date Released Time"; } ?></p>
                        </div>
                        <div class="col">
                            <label for="age-rating" class="db-text-sec fs-18 form-label text-nowrap ">Age Rating</label><br>
                            <input type="number" name="age-rating" id="age-rating" class="form-control file-trending"
                                   placeholder="13" value="<?php echo htmlspecialchars($record['age_rating'] ?? ''); ?>">
                            <p class="fs-12 text-danger mt-1 mb-2"><?php
                            if (!$valid_age_rating) { echo "Age rating can not be empty";
                            } elseif (!$valid_age_rating_age) { echo "Invalid age rating"; } ?></p>
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
                                <select name="first-genre" id="first-genre" class="form-control" required>
                                    <option value="">-- Select Genre --</option>
                                    <?php foreach ($genres as $genre): ?>
                                        <option value="<?= $genre['genre_id'] ?>" <?= ($record['genre_id1'] == $genre['genre_id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($genre['genre_name']) ?>
                                        </option>
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
                                        <option value="<?= $genre['genre_id'] ?>" <?= ($record['genre_id2'] == $genre['genre_id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($genre['genre_name']) ?>
                                        </option>
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
                                        <option value="<?= $genre['genre_id'] ?>" <?= ($record['genre_id3'] == $genre['genre_id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($genre['genre_name']) ?>
                                        </option>
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
                            <p class="fs-12 text-danger mt-1 mb-2"><?php if (!$valid_cast) echo "Cast actors can not be empty"; ?></p>
                        </div>
                        <div class="col-12 mt-3">
                            <label for="description" class="db-text-sec fs-18 form-label">Add Description</label><br>
                            <textarea name="description" id="description" class="form-control file-trending p-2"
                                      placeholder="The movie/series is about..."><?php echo htmlspecialchars($record['description'] ?? ''); ?></textarea>
                            <p class="fs-12 text-danger mt-1 mb-2"><?php if (!$valid_description) echo "Description can not be empty"; ?></p>
                        </div>
                    </div>
                    <!-- WHEN THE CATEGORY IS MOVIE THIS BUTTON WILL APPEAR AS ADD MOVIE -->
            
                    <div id="submit-btn-placeholder-top"></div>
                    <div id="submit-btn-container" class="d-flex justify-content-end mb-3">
                      <button type="submit" name="submit-btn" class="btn db-bg-primary text-white ms-3 mt-3">
                          <?php echo $is_edit ? 'Update' : 'Add'; ?> Movie/Series
                      </button>
                  </div>
                    <!-- ====== FULL FORM FIELDS END ====== -->

                    <!-- SEASONS/EPISODES SECTION -->
                    <div class="section-add-episodes p-3" style="display:<?php echo ($record['category'] == 'Series' ? 'block' : 'none'); ?>;">
                        <h4 class="db-text-sec mb-3">Add Episodes by Season</h4>
                        <div id="seasons-container">
                        <?php if ($is_edit && count($seasons)): ?>
                            <?php foreach ($seasons as $sidx => $season): ?>
                            <div class="season-block" data-season="<?php echo $sidx; ?>" data-seasonid="<?php echo $season['season_id']; ?>">
                                <button type="button" class="btn btn-outline-danger btn-sm season-delete-btn delete-season-btn"
                                        data-seasonid="<?php echo $season['season_id']; ?>" title="Delete Season">
                                    <i class="fa fa-trash"></i>
                                </button>
                                <div class="mb-2">
                                    <label class="db-text-sec fs-18 form-label">Season Title</label>
                                    <input type="text" placeholder="Season title" class="form-control file-trending"
                                           name="season-title[]" value="<?php echo htmlspecialchars($season['season_title']); ?>">
                                    <input type="hidden" name="season-number[]" value="<?php echo htmlspecialchars($season['season_number']); ?>">
                                </div>
                                <div id="episodes-container-<?php echo $sidx; ?>">
                                    <?php foreach ($season['episodes'] as $eidx => $ep): ?>
                                    <div class="row mt-3 mb-2 episode-block" data-episode-id="<?php echo $ep['episode_id']; ?>">
                                        <div class="col-12">
                                            <div class="episode-divider"></div>
                                        </div>
                                        <div class="col">
                                            <label class="db-text-sec fs-18 form-label">Upload Episode Video</label>
                                            <input type="file" name="episode-video[<?php echo $sidx; ?>][]" class="form-control file-trending">
                                            <?php if (!empty($ep['episode_video'])): ?>
                                                <p class="mt-1 mb-2 db-text-sec">Current: <?php echo htmlspecialchars($ep['episode_video']); ?></p>
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
                                    </div>
                                </div>
                                <button type="button" class="btn btn-secondary add-episode-btn mt-2" data-season="0">Add Episode</button>
                            </div>
                        <?php endif; ?>
                        </div>
                        <div class="d-flex gap-2 mt-3">
                            <button type="button" class="btn db-text-sec" id="add-season-btn" style="border:1px solid #C9A24B;">Add More Season</button>
                        </div>
                        <!-- WHEN THE CATEGORY IS SERIES THIS BUTTON WILL APPEAR AS ADD SERIES -->
                        <!-- Replace both submit button blocks with this: -->
                        
                    </div>
                    </div>
                </form>

                <!-- ===== START OF MODAL DELETE FOR EPISODE =====  -->
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
                                            style="color: #F3EAD6">YES</button>
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
                                    <button type="button" class="btn db-bg-primary db-text-sec" style="color: #F3EAD6"
                                            id="confirm-delete-season">YES</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- =====  END OF MODAL DELETE FOR SEASON =====  -->
            </div>
        </div>
    </section>
</main>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS is already loaded by the admin header; loading it twice doubled every popup backdrop. -->
<script>
$(document).ready(function () {
    <?php if (!$valid_episodes && isset($episode_errors['alert'])): ?>
    alert("<?php echo addslashes($episode_errors['alert']); ?>");
    <?php endif; ?>

    // Show/hide the episodes section on category change and allow adding
    $('#category').on('change', function () {
        if ($(this).val() === 'Series') {
            $('.section-add-episodes').show();
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
                            <div class="col-12"><div class="episode-divider"></div></div>
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

    function moveSubmitButton() {
      var cat = $('#category').val();
      if (cat === 'Movie') {
          $('#submit-btn-container').appendTo('#submit-btn-placeholder-top');
      } else {
          $('.section-add-episodes').append($('#submit-btn-container'));
      }
    }

    // On page load
    moveSubmitButton();

    // On category change
    $('#category').on('change', function () {
        moveSubmitButton();
    });

    // Add Episode Button (add new blank episode row for given season)
    $(document).on('click', '.add-episode-btn', function (e) {
        e.preventDefault();
        var seasonIdx = $(this).data('season');
        var epHtml = `
        <div class="row mt-3 mb-2 episode-block">
            <div class="col-12"><div class="episode-divider"></div></div>
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
                    <div class="col-12"><div class="episode-divider"></div></div>
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

    // SEASON DELETE MODAL (AJAX)
    var seasonToDelete = null;
    $(document).on('click', '.delete-season-btn', function (e) {
        e.preventDefault();
        seasonToDelete = $(this).data('seasonid');
        $('#delete-season-id').val(seasonToDelete);
        $('#modalDeleteSeason').modal('show');
    });

    $('#confirm-delete-season').on('click', function () {
        var season_id = $('#delete-season-id').val();
        $.ajax({
            url: window.location.href,
            type: 'POST',
            headers: { 'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') },
            data: { delete_season_id: season_id }
        }).always(function () {
            $('#modalDeleteSeason').modal('hide');
            alert('Season and all episodes deleted successfully');
            window.location.href = window.location.href;
        });
    });
});
</script>
<!-- ====== SEARCH TMDB (script + styles) ====== -->
<style>
  .cv-tmdb { padding: 16px; border: 1px solid #4A3E2C; border-radius: 12px; background: rgba(201, 162, 75, .08); }
  .cv-tmdb-sub { color: #B3A68C; }
  .cv-tmdb-credit { font-size: 12px; color: #B3A68C; border: 1px solid #4A3E2C; border-radius: 999px; padding: 2px 10px; }
  .cv-tmdb #tmdb-query, .cv-tmdb #ia-query { background: #1E1913; color: #F1E8D4; border-color: #4A3E2C; caret-color: #DDB963; }
  .cv-tmdb #tmdb-query:focus, .cv-tmdb #ia-query:focus { background: #1E1913; color: #F1E8D4; }
  .cv-tmdb #tmdb-query::placeholder, .cv-tmdb #ia-query::placeholder { color: #B3A68C; }
  .cv-tmdb-results { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 12px; margin-top: 12px; }
  .cv-tmdb-result { background: #2A2219; border: 1px solid #4A3E2C; border-radius: 10px; overflow: hidden; cursor: pointer; text-align: left; padding: 0; color: #F1E8D4; transition: transform .15s, border-color .15s; }
  .cv-tmdb-result:hover, .cv-tmdb-result:focus-visible { transform: translateY(-3px); border-color: #C9A24B; outline: none; }
  .cv-tmdb-result img, .cv-tmdb-noimg { width: 100%; aspect-ratio: 2 / 3; object-fit: cover; display: block; background: #1E1913; }
  .cv-tmdb-noimg { display: flex; align-items: center; justify-content: center; color: #B3A68C; font-size: 28px; }
  .cv-tmdb-result .cv-tmdb-meta { padding: 8px 10px; }
  .cv-tmdb-result .cv-tmdb-name { font-size: 13px; font-weight: 600; line-height: 1.25; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
  .cv-tmdb-result .cv-tmdb-kind { font-size: 12px; color: #B3A68C; }
  .cv-tmdb-picked { display: flex; gap: 14px; align-items: flex-start; margin-top: 12px; padding: 12px; border-radius: 10px; background: #2A2219; border: 1px solid #C9A24B; color: #F1E8D4; }
  .cv-tmdb-picked img.cv-p { width: 90px; border-radius: 8px; }
  .cv-tmdb-picked img.cv-l { max-width: 180px; max-height: 60px; object-fit: contain; background: rgba(255,255,255,.06); border-radius: 6px; padding: 4px; }
  .cv-filled { box-shadow: 0 0 0 2px rgba(201, 162, 75, .7) !important; transition: box-shadow .4s; }
</style>
<script>
(function () {
  const api = 'tmdb-api.php';
  const $q = document.getElementById('tmdb-query');
  const $btn = document.getElementById('tmdb-search-btn');
  const $status = document.getElementById('tmdb-status');
  const $results = document.getElementById('tmdb-results');
  const $picked = document.getElementById('tmdb-picked');
  if (!$q) return;

  const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

  function setStatus(text, isError) {
    $status.textContent = text || '';
    $status.style.color = isError ? '#ff8a8a' : '';
  }

  async function getJson(url) {
    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
    const data = await res.json().catch(() => ({}));
    if (!res.ok) throw new Error(data.error || 'Something went wrong. Try again.');
    return data;
  }

  async function search() {
    const q = $q.value.trim();
    if (q.length < 2) { setStatus('Type at least 2 letters.'); return; }
    setStatus('Searching TMDB...');
    $results.innerHTML = '';
    try {
      const data = await getJson(api + '?action=search&q=' + encodeURIComponent(q));
      if (!data.results.length) { setStatus('No movies or series found for "' + q + '".'); return; }
      setStatus('Pick the right one:');
      $results.innerHTML = data.results.map(r => `
        <button type="button" class="cv-tmdb-result" data-id="${r.id}" data-type="${r.type}">
          ${r.poster ? `<img src="${esc(r.poster)}" alt="" loading="lazy">` : '<span class="cv-tmdb-noimg"><i class="fa-solid fa-film"></i></span>'}
          <span class="cv-tmdb-meta d-block">
            <span class="cv-tmdb-name">${esc(r.title)}</span>
            <span class="cv-tmdb-kind">${r.type === 'tv' ? 'Series' : 'Movie'}${r.year ? ' · ' + r.year : ''}</span>
          </span>
        </button>`).join('');
    } catch (e) {
      setStatus(e.message, true);
    }
  }

  function flash(el) {
    el.classList.add('cv-filled');
    setTimeout(() => el.classList.remove('cv-filled'), 1500);
  }

  function fill(id, value) {
    const el = document.getElementById(id);
    if (!el || value === null || value === undefined || value === '') return;
    el.value = value;
    flash(el);
  }

  async function pick(btn) {
    setStatus('Loading details...');
    try {
      const { details: d } = await getJson(api + '?action=details&type=' + btn.dataset.type + '&id=' + btn.dataset.id);

      fill('title', d.title);
      fill('duration', d.duration);
      fill('date-released', d.year);
      fill('age-rating', d.age_rating);
      fill('cast', d.cast);
      fill('description', d.description);

      // Movie / Series (lets the page show or hide the seasons section)
      const cat = document.getElementById('category');
      if (cat) {
        cat.value = d.category;
        if (window.jQuery) jQuery(cat).trigger('change');
        else cat.dispatchEvent(new Event('change', { bubbles: true }));
        flash(cat);
      }

      // Genres: matched to this site's genre list
      ['first-genre', 'second-genre', 'third-genre'].forEach((sel, i) => {
        const el = document.getElementById(sel);
        if (!el) return;
        if (d.genres[i]) { el.value = String(d.genres[i].id); flash(el); }
        else if (i > 0) { el.value = ''; }
      });

      document.getElementById('tmdb-id').value = d.tmdb_id;
      document.getElementById('tmdb-type').value = d.tmdb_type;
      document.getElementById('tmdb-poster-path').value = d.poster_path || '';
      document.getElementById('tmdb-logo-path').value = d.logo_path || '';

      $results.innerHTML = '';
      $picked.classList.remove('d-none');
      $picked.innerHTML = `
        ${d.poster_url ? `<img class="cv-p" src="${esc(d.poster_url)}" alt="">` : ''}
        <div class="flex-grow-1">
          <p class="mb-1 fw-semibold">${esc(d.title)} ${d.year ? '(' + d.year + ')' : ''}
            <span class="badge bg-secondary ms-1">${esc(d.category)}</span></p>
          ${d.existing ? `<p class="mb-1 text-warning fs-14"><i class="fa-solid fa-triangle-exclamation"></i> Already in your library as record #${d.existing.movie_series_id}. Saving will be blocked.</p>` : ''}
          <p class="mb-1 fs-14 cv-tmdb-sub">Poster: ${d.poster_path ? 'from TMDB (upload your own to replace it)' : '<span class="text-warning">not on TMDB, please upload one</span>'}</p>
          <p class="mb-1 fs-14 cv-tmdb-sub">Title image: ${d.logo_path ? 'from TMDB' : '<span class="text-warning">not on TMDB, please upload one</span>'}</p>
          ${d.logo_url ? `<img class="cv-l mb-1" src="${esc(d.logo_url)}" alt="">` : ''}
          <p class="mb-0 fs-14 cv-tmdb-sub">Genres matched: ${d.genres.length ? d.genres.map(g => esc(g.name)).join(', ') : 'none, please choose'}${d.age_rating ? '' : ' · Age rating: not on TMDB, please enter one'}</p>
          ${d.trailer ? `<a class="fs-14" href="${esc(d.trailer)}" target="_blank" rel="noopener">Watch the trailer on YouTube</a>` : ''}
          <p class="mb-0 mt-1 fs-14 text-info"><i class="fa-solid fa-arrow-down"></i> Step 2 below is looking for a free copy. Check it (or upload your own video), fill anything still missing, and save.</p>
        </div>
        <button type="button" class="btn btn-sm btn-outline-light" id="tmdb-clear">Clear</button>`;
      document.getElementById('tmdb-clear').onclick = clearPick;
      setStatus('');
      // Step 2 listens for this and looks for a free copy of the same title.
      document.dispatchEvent(new CustomEvent('cv:tmdb-picked', { detail: d }));
    } catch (e) {
      setStatus(e.message, true);
    }
  }

  function clearPick() {
    ['tmdb-id', 'tmdb-type', 'tmdb-poster-path', 'tmdb-logo-path'].forEach(id => document.getElementById(id).value = '');
    $picked.classList.add('d-none');
    $picked.innerHTML = '';
    document.dispatchEvent(new CustomEvent('cv:tmdb-cleared'));
  }

  $btn.addEventListener('click', search);
  // Enter searches instead of submitting the whole form
  $q.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); search(); } });
  $results.addEventListener('click', e => { const b = e.target.closest('.cv-tmdb-result'); if (b) pick(b); });
})();
</script>

<!-- ====== FREE FULL MOVIE (Internet Archive) ====== -->
<style>
  .cv-ia-preview { width: 260px; max-width: 100%; border-radius: 8px; background: #000; }
  .cv-ia-files { background: #1E1913; color: #fff; border-color: #4A3E2C; }
</style>
<script>
(function () {
  const api = 'archive-api.php';
  const $q = document.getElementById('ia-query');
  const $btn = document.getElementById('ia-search-btn');
  const $status = document.getElementById('ia-status');
  const $results = document.getElementById('ia-results');
  const $picked = document.getElementById('ia-picked');
  const $url = document.getElementById('stream-url');
  if (!$q) return;

  const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  const setStatus = (text, isError) => { $status.textContent = text || ''; $status.style.color = isError ? '#ff8a8a' : ''; };

  async function getJson(url) {
    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
    const data = await res.json().catch(() => ({}));
    if (!res.ok) throw new Error(data.error || 'Something went wrong. Try again.');
    return data;
  }

  // What Step 1 (TMDB) picked: used to find and check the right copy.
  let expected = null; // { title, year, runtime, category }
  const $note = document.createElement('div');
  $note.className = 'fs-14 mt-2';
  $status.before($note);

  // Start the search with the title TMDB filled in, if there is one.
  $q.addEventListener('focus', () => {
    const t = document.getElementById('title');
    if (!$q.value && t && t.value) $q.value = t.value;
  });

  async function search(tryNext) {
    const q = $q.value.trim();
    if (q.length < 2) { setStatus('Type at least 2 letters.'); return; }
    setStatus('Searching the Internet Archive...');
    $results.innerHTML = '';
    try {
      const data = await getJson(api + '?action=search&q=' + encodeURIComponent(q));
      let results = data.results;
      if (!results.length) { if (!tryNext) setStatus('No free copy found for "' + q + '". Newer titles are not free, so upload a video you have the rights to instead.'); return false; }
      // Copies from the same year as the TMDB pick first.
      if (expected && expected.year) {
        const gap = r => (r.year ? Math.abs(r.year - expected.year) : 50);
        results = results.slice().sort((a, b) => gap(a) - gap(b));
      }
      setStatus(expected ? 'Free copies of "' + expected.title + '". Pick the right one:' : 'Pick the right one:');
      $results.innerHTML = results.map(r => {
        const same = expected && expected.year && r.year && Math.abs(r.year - expected.year) <= 1;
        return `
        <button type="button" class="cv-tmdb-result" data-id="${esc(r.id)}">
          <img src="${esc(r.thumb)}" alt="" loading="lazy" style="aspect-ratio: 4 / 3;">
          <span class="cv-tmdb-meta d-block">
            <span class="cv-tmdb-name">${esc(r.title)}</span>
            <span class="cv-tmdb-kind">${r.year ? r.year : 'Year unknown'}${same ? ' · <span class="text-success">same year</span>' : ''}</span>
          </span>
        </button>`;
      }).join('');
      return true;
    } catch (e) { setStatus(e.message, true); return true; }
  }

  // Is this file the whole film? Compare it with TMDB's runtime.
  function lengthCheck(minutes) {
    const box = document.getElementById('ia-length');
    if (!box) return;
    if (!expected || !expected.runtime || !minutes) { box.innerHTML = ''; return; }
    if (minutes >= expected.runtime * 0.8) {
      box.innerHTML = `<span class="text-success"><i class="fa-solid fa-circle-check"></i> Full length: ${minutes} of ${expected.runtime} min.</span>`;
    } else {
      box.innerHTML = `<span class="text-warning"><i class="fa-solid fa-triangle-exclamation"></i> Only ${minutes} min, but the film runs ${expected.runtime} min. This may be a clip or trailer. Pick another copy or file.</span>`;
    }
  }

  function choose(url, minutes) {
    $url.value = url;
    const v = $picked.querySelector('video');
    if (v) { v.src = url; }
    lengthCheck(minutes);
    document.dispatchEvent(new CustomEvent('cv:video-changed'));
  }

  function clearVideo() {
    $url.value = '';
    $picked.classList.add('d-none');
    $picked.innerHTML = '';
    document.dispatchEvent(new CustomEvent('cv:video-changed'));
  }

  // Step 1 picked a title: look for a free copy of it right away.
  document.addEventListener('cv:tmdb-picked', e => {
    const d = e.detail;
    expected = { title: d.title, year: d.year, runtime: d.runtime || 0, category: d.category };
    clearVideo();
    const notes = [];
    if (d.year && d.year > 1970) {
      notes.push('<span class="text-warning"><i class="fa-solid fa-triangle-exclamation"></i> ' + esc(d.title) + ' (' + d.year + ') is a newer title, so it is almost certainly not free. Upload a video you have the rights to instead.</span>');
    }
    if (d.category === 'Series') {
      notes.push('<span class="cv-tmdb-sub"><i class="fa-solid fa-circle-info"></i> For a series, each episode needs its own video in the Seasons and Episodes section below.</span>');
    }
    $note.innerHTML = notes.join('<br>');
    // Try the title, then its other names, until one finds copies.
    const names = (d.search_titles && d.search_titles.length) ? d.search_titles : [d.title];
    (async () => {
      for (let i = 0; i < names.length; i++) {
        $q.value = names[i];
        if (await search(i < names.length - 1)) return;
      }
    })();
  });
  document.addEventListener('cv:tmdb-cleared', () => { expected = null; $note.innerHTML = ''; });

  async function pick(btn) {
    setStatus('Loading the video files...');
    try {
      const { item } = await getJson(api + '?action=videos&id=' + encodeURIComponent(btn.dataset.id));
      if (!item.videos.length) { setStatus('This one has no video that plays in a browser. Try another result.', true); return; }
      $results.innerHTML = '';
      $picked.classList.remove('d-none');
      $picked.innerHTML = `
        <video class="cv-ia-preview" controls muted preload="metadata" src="${esc(item.videos[0].url)}"></video>
        <div class="flex-grow-1">
          <p class="mb-1 fw-semibold">${esc(item.title)} ${item.year ? '(' + item.year + ')' : ''}</p>
          <label class="fs-14 cv-tmdb-sub mb-1" for="ia-file">Video file</label>
          <select id="ia-file" class="form-select form-select-sm cv-ia-files mb-2">
            ${item.videos.map(f => `<option value="${esc(f.url)}">${esc(f.name)} · ${esc(f.format)}${f.minutes ? ' · ' + f.minutes + ' min' : ''}${f.size_mb ? ' · ' + f.size_mb + ' MB' : ''}</option>`).join('')}
          </select>
          <p class="mb-1 fs-14" id="ia-length"></p>
          <p class="mb-1 fs-14 cv-tmdb-sub">Check the preview plays and that it is the right film. <a href="${esc(item.page)}" target="_blank" rel="noopener">See it on archive.org</a></p>
          <p class="mb-0 fs-14 text-info"><i class="fa-solid fa-circle-check"></i> No upload needed. Members will stream it inside CineVault.</p>
        </div>
        <button type="button" class="btn btn-sm btn-outline-light" id="ia-clear">Clear</button>`;
      const minutesOf = url => (item.videos.find(f => f.url === url) || {}).minutes || 0;
      choose(item.videos[0].url, item.videos[0].minutes);
      document.getElementById('ia-file').onchange = e => choose(e.target.value, minutesOf(e.target.value));
      document.getElementById('ia-clear').onclick = clearVideo;
      setStatus('');
    } catch (e) { setStatus(e.message, true); }
  }

  $btn.addEventListener('click', search);
  $q.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); search(); } });
  $results.addEventListener('click', e => { const b = e.target.closest('.cv-tmdb-result'); if (b) pick(b); });
})();
</script>

<!-- ====== ADD/UPDATE BUTTON: only active when everything required is filled ====== -->
<script>
(function () {
  const form = document.getElementById('movie-series-form');
  const btn = form && form.querySelector('button[name="submit-btn"]');
  if (!btn) return;

  const hint = document.createElement('p');
  hint.className = 'fs-14 text-end mt-2 mb-0 me-1 cv-tmdb-sub';
  hint.setAttribute('aria-live', 'polite');
  btn.after(hint);
  btn.parentElement.classList.add('flex-wrap');
  hint.style.flexBasis = '100%';

  const val = id => { const el = document.getElementById(id); return el ? String(el.value || '').trim() : ''; };
  const hasFile = id => { const el = document.getElementById(id); return !!(el && el.files && el.files.length); };

  function missing() {
    const out = [];
    if (!hasFile('poster') && !val('tmdb-poster-path') && form.dataset.hasPoster !== '1') out.push('poster');
    if (!hasFile('video') && !val('stream-url') && form.dataset.hasVideo !== '1') out.push('video (upload or pick a free copy)');
    if (!hasFile('modal-poster-title') && !val('tmdb-logo-path') && form.dataset.hasLogo !== '1') out.push('title image');
    [['title', 'title'], ['duration', 'duration'], ['date-released', 'year released'], ['age-rating', 'age rating'],
     ['first-genre', 'first genre'], ['cast', 'cast'], ['description', 'description']].forEach(([id, label]) => {
      if (!val(id)) out.push(label);
    });

    if (val('category') === 'Series') {
      let complete = 0, partial = false;
      form.querySelectorAll('.section-add-episodes .episode-block').forEach(block => {
        const get = n => { const el = block.querySelector('[name^="' + n + '["]'); return el ? String(el.value || '').trim() : ''; };
        const vid = block.querySelector('input[type="file"][name^="episode-video["]');
        const hasVid = (vid && vid.files && vid.files.length) || !!block.querySelector('[name^="existing-episode-video["]');
        const parts = [get('episode-title'), get('episode-description'), get('episode-duration'), hasVid ? 'x' : ''];
        const filled = parts.filter(Boolean).length;
        if (filled === 4) complete++;
        else if (filled > 0) partial = true;
      });
      if (!complete) out.push('at least one full episode');
      else if (partial) out.push('the unfinished episode (title, video, duration and description)');
    }
    return out;
  }

  function check() {
    const m = missing();
    btn.disabled = m.length > 0;
    btn.title = m.length ? 'Fill in the required fields first' : '';
    hint.textContent = m.length ? 'Still needed: ' + m.join(', ') + '.' : '';
  }

  ['input', 'change'].forEach(ev => form.addEventListener(ev, check));
  ['cv:tmdb-picked', 'cv:tmdb-cleared', 'cv:video-changed'].forEach(ev => document.addEventListener(ev, () => setTimeout(check, 0)));
  // Fields filled by scripts (TMDB, jQuery, added episodes) don't always fire events.
  setInterval(check, 800);
  form.addEventListener('submit', e => { if (missing().length) { e.preventDefault(); check(); } });
  check();
})();
</script>
</body>
</html>