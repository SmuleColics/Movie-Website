<?php
ob_start();
include '../includes/dashboard-header-sidebar.php';
include '../includes/db-connection.php';

$genres = ["Action", "Adventure", "Animation", "Comedy", "Crime", "Documentary", "Drama", "Family", "Fantasy", "History", "Terror", "Music", "Mistery", "Science fiction", "Cinema TV", "Thriller", "War", "Western", "Kids", "News", "Reality", "Romance", "Sci-Fi & Fantasy", "Soap", "Talk", "War & Politics"];

$file_upload_error = "File can not be empty";
$valid_poster = true;
$valid_video = true;
$valid_modal_poster_title = true;
$valid_description = true;
$valid_cast = true;
$valid_title = true;
$valid_duration = true;

$date_released = 0;
$valid_date_released = true;
$valid_date_released_time = true;

$age_rating = 0;
$valid_age_rating = true;
$valid_age_rating_age = true;

$file_poster_path = '';
$file_video_path = '';
$file_modal_poster_title_path = '';

// Detect mode
$top10_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$is_edit = $top10_id > 0;

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

// For edit, fetch the record
if ($is_edit) {
    $result = mysqli_query($con, "SELECT * FROM tbl_top10 WHERE top10_id = $top10_id LIMIT 1");
    if ($result && mysqli_num_rows($result) > 0) {
        $record = mysqli_fetch_assoc($result);
    } else {
        echo "<script>alert('Record not found.'); window.location.href='web-home.php';</script>";
        exit();
    }
}

// Handle add or update on POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Set initial valid flags to false
    $valid_poster = true;
    $valid_video = true;
    $valid_modal_poster_title = true;
    $valid_age_rating = true;

    // Poster (optional for edit, required for add)
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        $file_poster = $_FILES['poster']['name'];
        $temp_name = $_FILES['poster']['tmp_name'];
        $file_poster_path = 'TOP10_IMAGES/' . $file_poster;
        if (!move_uploaded_file($temp_name, $file_poster_path)) {
            $valid_poster = false;
        }
    } else {
        $file_poster = $is_edit ? $record['poster'] : '';
        if (!$is_edit && empty($file_poster)) $valid_poster = false;
    }

    // Video (optional for edit, required for add)
    if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
        $file_video = $_FILES['video']['name'];
        $temp_name1 = $_FILES['video']['tmp_name'];
        $file_video_path = 'TOP10_VIDEOS/' . $file_video;
        if (!move_uploaded_file($temp_name1, $file_video_path)) {
            $valid_video = false;
            $file_upload_error = "Video upload failed (move error).";
        }
    } else {
        $file_video = $is_edit ? $record['video'] : '';
        if (!$is_edit && empty($file_video)) $valid_video = false;
    }

    // Modal Poster Title (optional for edit, required for add)
    if (isset($_FILES['modal-poster-title']) && $_FILES['modal-poster-title']['error'] === UPLOAD_ERR_OK) {
        $file_modal_poster_title = $_FILES['modal-poster-title']['name'];
        $temp_name2 = $_FILES['modal-poster-title']['tmp_name'];
        $file_modal_poster_title_path = 'TOP10_IMAGES/' . $file_modal_poster_title;
        if (!move_uploaded_file($temp_name2, $file_modal_poster_title_path)) {
            $valid_modal_poster_title = false;
        }
    } else {
        $file_modal_poster_title = $is_edit ? $record['modal_poster_title'] : '';
        if (!$is_edit && empty($file_modal_poster_title)) $valid_modal_poster_title = false;
    }

    // Validate title
    if (strlen(trim($_POST['title'])) < 2) {
        $valid_title = false;
    } else {
        $title = mysqli_real_escape_string($con, $_POST['title']);
    }

    // Validate Duration
    if (strlen(trim($_POST['duration'])) < 2) {
        $valid_duration = false;
    } else {
        $duration = mysqli_real_escape_string($con, $_POST['duration']);
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

    // Check if any validation failed
    if ($valid_poster && $valid_video && $valid_modal_poster_title && $valid_date_released && $valid_age_rating && $valid_description && $valid_age_rating_age && $valid_date_released_time && $valid_title && $valid_cast && $valid_duration) {
        if ($is_edit) {
            // UPDATE
            $update_sql = "
            UPDATE tbl_top10 SET
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
            WHERE top10_id = $top10_id
            ";
            $query = mysqli_query($con, $update_sql);
            $msg = "Data Updated Successfully";
        } else {
            // INSERT
            $insert_sql = "
            INSERT INTO tbl_top10
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
        }

        if (!$query) {
            die(mysqli_error($con));
        } else {
            echo "<script>
                alert('$msg');
                window.location.href='web-home.php';
            </script>";
            exit();
        }
    } else {
        // If validation fails, delete uploaded files
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
  <title>Dashboard Action - <?php echo $is_edit ? 'Edit' : 'Add'; ?> Top 10</title>
  <link rel="stylesheet" href="../DASHBOARD-CSS/dashboard.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
      .sidebar-content-item:nth-child(4) {
        background-color: var(--dashboard-primary);
      }
  </style>
</head>

<body>
  <main class="container-lg p-0 overflow-hidden">
    <div>
      <a href="web-home.php" class="btn db-bg-primary text-white ms-3 mt-3">
        <i class="fa-solid fa-chevron-left"></i>
        Go Back
      </a>
    </div>
    <section class="signup-accounts-section text-left p-3">
      <div class="card bg-dark">
        <div class="card-body">
          <div class="table-task-top db-text-sec">
            <div class="py-2 ps-3">
              <p class="m-0 fs-20"><?php echo $is_edit ? 'Edit' : 'Add'; ?> Top 10 Record</p>
              <p class="m-0 fs-14" style="transform: translateY(-2px)">
                <?php echo $is_edit ? 'Edit your entry for the Top 10 CineVault database' : 'Add your favorite movies to the CineVault database here'; ?>
              </p>
            </div>
          </div>
          <form method="post" enctype="multipart/form-data">
            <div class="row mt-3 mb-2">
              <div class="col-12">
                <label for="poster" class="db-text-sec fs-18 form-label">Upload Poster</label><br>
                <input type="file" name="poster" id="poster" class="form-control file-trending">
                <?php if ($is_edit && !empty($record['poster'])): ?>
                  <p class="mt-1 mb-2 db-text-sec">Current: <?php echo htmlspecialchars($record['poster']); ?></p>
                <?php endif; ?>
                <p class="fs-12 text-danger mt-1 mb-2">
                  <?php if (!$valid_poster) echo $file_upload_error; ?>
                </p>
              </div>
              <div class="col">
                <label for="video" class="db-text-sec fs-18 form-label">Upload Video</label><br>
                <input type="file" name="video" id="video" class="form-control file-trending">
                <?php if ($is_edit && !empty($record['video'])): ?>
                  <p class="mt-1 mb-2 db-text-sec">Current: <?php echo htmlspecialchars($record['video']); ?></p>
                <?php endif; ?>
                <p class="fs-12 text-danger mt-1 mb-2">
                  <?php if (!$valid_video) echo $file_upload_error; ?>
                </p>
              </div>
              <div class="col">
                <label for="modal-poster-title" class="db-text-sec fs-18 form-label text-nowrap">Upload Modal Poster Title</label><br>
                <input type="file" name="modal-poster-title" id="modal-poster-title" class="form-control file-trending">
                <?php if ($is_edit && !empty($record['modal_poster_title'])): ?>
                  <p class="mt-1 mb-2 db-text-sec">Current: <?php echo htmlspecialchars($record['modal_poster_title']); ?></p>
                <?php endif; ?>
                <p class="fs-12 text-danger mt-1 mb-2">
                  <?php if (!$valid_modal_poster_title) echo $file_upload_error; ?>
                </p>
              </div>
            </div>
            <div class="row mb-2">
              <div class="col">
                <label for="title" class="db-text-sec fs-18 form-label">Title</label><br>
                <input type="text" name="title" id="title" class="form-control file-trending" placeholder="Avengers" value="<?php echo htmlspecialchars($record['title']); ?>">
                <p class="fs-12 text-danger mt-1 mb-2">
                  <?php if (!$valid_title) echo "Title must be at least 2 characters long"; ?>
                </p>
              </div>
              <div class="col">
                <label for="duration" class="db-text-sec fs-18 form-label">Duration</label><br>
                <input type="text" name="duration" id="duration" class="form-control file-trending" placeholder="1hr 30mins" value="<?php echo htmlspecialchars($record['duration']); ?>">
                <p class="fs-12 text-danger mt-1 mb-2">
                  <?php if (!$valid_duration) echo "Duration must be at least 2 characters long"; ?>
                </p>
              </div>
              <div class="col">
                <label for="date-released" class="db-text-sec fs-18 form-label text-nowrap">Date Released</label><br>
                <input type="number" name="date-released" id="date-released" class="form-control file-trending" placeholder="0" value="<?php echo htmlspecialchars($record['date_released']); ?>">
                <p class="fs-12 text-danger mt-1 mb-2">
                  <?php 
                  if (!$valid_date_released) {
                    echo "Date Released can not be empty";
                  } elseif (!$valid_date_released_time) {
                    echo "Invalid Date Released Time";
                  }
                  ?>
                </p>
              </div>
              <div class="col">
                <label for="age-rating" class="db-text-sec fs-18 form-label text-nowrap ">Age Rating</label><br>
                <input type="number" name="age-rating" id="age-rating" class="form-control file-trending" placeholder="0" value="<?php echo htmlspecialchars($record['age_rating']); ?>">
                <p class="fs-12 text-danger mt-1 mb-2">
                  <?php 
                    if (!$valid_age_rating) {
                      echo "Age rating can not be empty";
                    } elseif(!$valid_age_rating_age) {
                      echo "Invalid age rating";
                    }
                  ?>
                </p>
              </div>
              <div class="col">
                <label for="category" class="db-text-sec fs-18 form-label text-nowrap">Choose Category</label><br>
                <div class="category-container">
                  <select name="category" id="category" class="form-control">
                    <option value="Movie" class="file-trending" <?php echo ($record['category']=='Movie') ? 'selected' : ''; ?>>Movie</option>
                    <option value="Series" class="file-trending" <?php echo ($record['category']=='Series') ? 'selected' : ''; ?>>Series</option>
                  </select>
                </div>
                <i class="fa-solid fa-caret-down caret-genre" style="transform: translate(-12px, -402px);"></i>
              </div>
            </div>  
            <div class="row mb-2">
              <div class="col">
                <label for="first-genre" class="db-text-sec fs-18 form-label">First Genre</label><br>
                <div class="genre-container">
                  <select name="first-genre" id="first-genre" class="form-control">
                    <?php foreach ($genres as $genre): ?>
                      <option value="<?= htmlspecialchars($genre) ?>" class="file-trending" <?php echo ($record['genre_1']==$genre) ? 'selected' : ''; ?>><?= htmlspecialchars($genre) ?></option>
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
                      <option value="<?= htmlspecialchars($genre) ?>" class="file-trending" <?php echo ($record['genre_2']==$genre) ? 'selected' : ''; ?>><?= htmlspecialchars($genre) ?></option>
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
                      <option value="<?= htmlspecialchars($genre) ?>" class="file-trending" <?php echo ($record['genre_3']==$genre) ? 'selected' : ''; ?>><?= htmlspecialchars($genre) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <i class="fa-solid fa-caret-down caret-genre"></i>
                </div>  
              </div>
              <div class="col-12 mt-3">
                <label for="cast" class="db-text-sec fs-18 form-label text-nowrap">Cast</label><br>
                <input type="text" name="cast" id="cast" class="form-control file-trending" placeholder="Robert Downey Jr., Chris Evans, Scarlett Johansson..." value="<?php echo htmlspecialchars($record['cast']); ?>">
                <p class="fs-12 text-danger mt-1 mb-2">
                  <?php if (!$valid_cast) echo "Cast actors can not be empty"; ?>
                </p>
              </div>
              <div class="col-12 mt-3">
                <label for="description" class="db-text-sec fs-18 form-label">Add Description</label><br>
                <textarea name="description" id="description" class="form-control file-trending p-2" placeholder="The movie is about..."><?php echo htmlspecialchars($record['description']); ?></textarea>
                <p class="fs-12 text-danger mt-1 mb-2">
                  <?php if (!$valid_description) echo "Description can not be empty"; ?>
                </p>
              </div>
            </div>
            <div class="d-flex justify-content-end mb-3">
              <button type="submit" name="submit-btn" class="btn db-bg-primary text-white ms-3 mt-3"><?php echo $is_edit ? 'Update' : 'Add'; ?> Top 10</button>
            </div>
          </form>
        </div>
      </div>
    </section>

    <section class="p-3 section-add-episodes">
      <div class="card bg-dark">
        <div class="card-body">
          <div class="table-task-top db-text-sec">
              <div class="py-2 ps-3">
                <p class="m-0 fs-20">Add episodes</p>
                <p class="m-0 fs-14" style="transform: translateY(-2px)">
                  Add List of Episodes Here
                </p>
            </div>
          </div>
          <form method="post" enctype="multipart/form-data">
            <div class="row mt-3 mb-2">
                
            </div>
          </form>
        </div>
      </div>
    </section>
  </main>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.min.js"></script>
  <script>
    new DataTable('#table-signup-acc', {
      pagingType: 'simple_numbers',
      responsive: true,
      language: {
        search: '_INPUT_',
        searchPlaceholder: 'Search...'
      }
    });
  </script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const categorySelect = document.getElementById('category');
      const addEpisodesSection = document.querySelector('.section-add-episodes');

      function toggleEpisodesSection() {
        if (categorySelect.value === 'Series') {
          addEpisodesSection.style.display = 'block';
        } else {
          addEpisodesSection.style.display = 'none';
        }
      }

      categorySelect.addEventListener('change', toggleEpisodesSection);

      // Optionally, ensure correct state on page load:
      toggleEpisodesSection();
    });
  </script>
</body>
</html>