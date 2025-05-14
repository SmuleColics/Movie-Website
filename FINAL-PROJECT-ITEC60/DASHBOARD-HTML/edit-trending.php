<?php
ob_start();
include '../includes/dashboard-header-sidebar.php';
include '../includes/db-connection.php';

$genres = ["Action", "Adventure", "Animation", "Comedy", "Crime", "Documentary", "Drama", "Family", "Fantasy", "History", "Terror", "Music", "Mistery", "Science fiction", "Cinema TV", "Thriller", "War", "Western", "Kids", "News", "Reality", "Romance", "Sci-Fi & Fantasy", "Soap", "Talk", "War & Politics"];

$trending_id = $_GET['id'] ?? null;

$query = mysqli_query($con, "SELECT * FROM tbl_trending WHERE trending_id = $trending_id");
$row = mysqli_fetch_assoc($query);

$file_upload_error = "File can not be empty";
$valid_poster = true;
$valid_modal_poster = true;
$valid_modal_poster_title = true;
$valid_description = true;

$date_released = 0;
$valid_date_released = true;
$valid_date_released_time = true;

$age_rating = 0;
$valid_age_rating = true;
$valid_age_rating_age = true;

$file_poster_path = '';
$file_modal_poster_path = '';
$file_modal_poster_title_path = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $valid_poster = true;
  $valid_modal_poster = true;
  $valid_modal_poster_title = true;
  $valid_age_rating = true;

  // Poster
  if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
      $file_poster = $_FILES['poster']['name'];
      $temp_name = $_FILES['poster']['tmp_name'];
      $file_poster_path = 'TRENDING_IMAGES/' . $file_poster;
      // if (!move_uploaded_file($temp_name, $file_poster_path)) {
      //     $valid_poster = false;
      // }
  } else {
      $file_poster = $row['poster'];
      // $valid_poster = false;
  }

  // Modal Poster
  if (isset($_FILES['modal-poster']) && $_FILES['modal-poster']['error'] === UPLOAD_ERR_OK) {
      $file_modal_poster = $_FILES['modal-poster']['name'];
      $temp_name1 = $_FILES['modal-poster']['tmp_name'];
      $file_modal_poster_path = 'TRENDING_IMAGES/' . $file_modal_poster;
      // if (!move_uploaded_file($temp_name1, $file_modal_poster_path)) {
      //     $valid_modal_poster = false;
      // }
  } else {
    $file_modal_poster = $row['modal_poster'];
      // $valid_modal_poster = false;
  }

  // Modal Poster Title
  if (isset($_FILES['modal-poster-title']) && $_FILES['modal-poster-title']['error'] === UPLOAD_ERR_OK) {
      $file_modal_poster_title = $_FILES['modal-poster-title']['name'];
      $temp_name2 = $_FILES['modal-poster-title']['tmp_name'];
      $file_modal_poster_title_path = 'TRENDING_IMAGES/' . $file_modal_poster_title;
      // if (!move_uploaded_file($temp_name2, $file_modal_poster_title_path)) {
      //     $valid_modal_poster_title = false;
      // }
  } else {
    $file_modal_poster_title = $row['modal_poster_title'];
      // $valid_modal_poster_title = false;
  }

  // Date Released
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

  // Age Rating
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

  // Description
  if (!empty($_POST['description'])) {
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $valid_description = true;
  } else {
    $valid_description = false;
  }

  if ($valid_date_released && $valid_age_rating && $valid_description && $valid_age_rating_age && $valid_date_released_time) {
      $category = $_POST['category'] ?? '';
      $firstGenre = $_POST['first-genre'] ?? '';
      $secondGenre = trim($_POST['second-genre'] ?? '');
      $thirdGenre = trim($_POST['third-genre'] ?? '');

      $secondGenre = ($secondGenre === '') ? 'NULL' : "'" . mysqli_real_escape_string($con, $secondGenre) . "'";
      $thirdGenre = ($thirdGenre === '') ? 'NULL' : "'" . mysqli_real_escape_string($con, $thirdGenre) . "'";

      $update_query = mysqli_query($con, "UPDATE tbl_trending SET 
        poster = '$file_poster', 
        modal_poster = '$file_modal_poster', 
        modal_poster_title = '$file_modal_poster_title', 
        date_released = '$date_released', 
        age_rating = '$age_rating', 
        category = '$category', 
        genre_1 = '$firstGenre', 
        genre_2 = $secondGenre, 
        genre_3 = $thirdGenre, 
        description = '$description' 
        WHERE trending_id = $trending_id
      ");

      if (!$update_query) {
          die(mysqli_error($con));
      } else {
        echo "<script>
        alert('Data Edited Successfully');
        window.location.href='trending.php';
        </script>";
        exit();
      }
  } else {
      if (file_exists($file_poster_path)) unlink($file_poster_path);
      if (file_exists($file_modal_poster_path)) unlink($file_modal_poster_path);
      if (file_exists($file_modal_poster_title_path)) unlink($file_modal_poster_title_path);
  }
  ob_end_flush();
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EDIT TRENDING SECTION</title>
  <!-- ========== CSS LINK ========== -->
  <link rel="stylesheet" href="../DASHBOARD-CSS/dashboard.css">
  <!-- ===== DATA TABLES CDN ===== -->
  <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.min.css">
  <!-- ========== Bootstrap 5.3 CSS  ========== -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
      .sidebar-content-item:nth-child(6) {
        background-color: var(--dashboard-primary);
      }
  </style>
</head>

<body>

  <main class="container-lg p-0 overflow-hidden">
    <a href="trending.php" class="btn db-bg-primary text-white ms-3 mt-3"><i class="fa-solid fa-chevron-left"></i>
    Go Back</a>

    <!-- ========== TRENDING LANDING PAGE ========== -->
    <section class="signup-accounts-section text-left p-3">
      <div class="card bg-dark">
        <div class="card-body">

          <div class="table-task-top db-text-sec">
            <div class="py-2 ps-3">
              <p class="m-0 fs-20">Edit Trending this week</p>
              <p class="m-0 fs-14" style="transform: translateY(-2px)">List of Trending this week</p>
            </div>
          </div>

          <form method="post" enctype="multipart/form-data">
        
            <div class="row mt-3 mb-2">
              <div class="col-12">
                <label for="poster" class="db-text-sec fs-18 form-label">Upload Poster</label><br>
                <input type="file" name="poster" id="poster" class="form-control file-trending">
                <p class="db-text-sec mt-1">Current: <?= $row['poster'] ?></p>
                <p class="fs-12 text-danger mt-1 mb-2">
                  <?php if (!$valid_poster) {
                    echo $file_upload_error;
                  }  ?>
                </p>
              </div>
              <div class="col">
                <label for="modal-poster" class="db-text-sec fs-18 form-label">Upload Modal Poster</label><br>
                <input type="file" name="modal-poster" id="modal-poster" class="form-control file-trending">
                <p class="db-text-sec mt-1">Current: <?= $row['modal_poster'] ?></p>
                <p class="fs-12 text-danger mt-1 mb-2">
                  <?php if (!$valid_modal_poster) {
                    echo $file_upload_error;
                  }  ?>
                </p>
              </div>
              <div class="col">
                <label for="modal-poster-title" class="db-text-sec fs-18 form-label text-nowrap">Upload Modal Poster Title</label><br>
                <input type="file" name="modal-poster-title" id="modal-poster-title" class="form-control file-trending">
                <p class="db-text-sec mt-1">Current: <?= $row['modal_poster_title'] ?></p>
                <p class="fs-12 text-danger mt-1 mb-2">
                  <?php if (!$valid_modal_poster_title) { 
                    echo $file_upload_error;
                  }  ?>
                </p>
              </div>
            </div>
            <div class="row mb-2">
              <div class="col">
                <label for="date-released" class="db-text-sec fs-18 form-label">Date Released</label><br>
                <input type="number" name="date-released" id="date-released" class="form-control file-trending" placeholder="0" value="<?= $row['date_released'] ?>">
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
                <label for="age-rating" class="db-text-sec fs-18 form-label">Age Rating</label><br>
                <input type="number" name="age-rating" id="age-rating" class="form-control file-trending" placeholder="0"  value="<?= $row['age_rating'] ?>">
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
                <label for="age-rating" class="db-text-sec fs-18 form-label text-nowrap">Choose Category</label><br>
                <div class="category-container">
                  <select name="category" id="category" class="form-control">
                    <option value="Movie" class="file-trending" <?= htmlspecialchars($row['category']) == 'Movie' ? 'selected' : '' ?>>Movie</option>
                    <option value="Series" class="file-trending" <?= htmlspecialchars($row['category']) == 'Series' ? 'selected' : '' ?>>Series</option>
                  </select>
                </div>
                <i class="fa-solid fa-caret-down caret-category"></i>
              </div>
            </div>  

            <div class="row mb-2">
              <div class="col">
                <label for="first-genre" class="db-text-sec fs-18 form-label">First Genre</label><br>
                <div class="genre-container">
                  <select name="first-genre" id="first-genre" class="form-control">
                    <?php foreach ($genres as $genre): ?>
                      <option value="<?= htmlspecialchars($genre) ?>" <?= htmlspecialchars($row['genre_1']) == $genre ? 'selected' : '' ?> class="file-trending"><?= htmlspecialchars($genre) ?></option>

                    <?php endforeach; ?>
                  </select>
                  <i class="fa-solid fa-caret-down caret-genre"></i>
                </div>
              </div>

              <div class="col">
                <label for="second-genre" class="db-text-sec fs-18 form-label">Second Genre</label><br>
                <div class="genre-container">
                  <select name="second-genre" id="second-genre" class="form-control">
                    <option value="">-- Select Genre --</option> <!-- This is your empty option -->
                    <?php foreach ($genres as $genre): ?>
                      <option value="<?= htmlspecialchars($genre) ?>" <?= htmlspecialchars($row['genre_2']) == $genre ? 'selected' : '' ?> class="file-trending"><?= htmlspecialchars($genre) ?></option>

                    <?php endforeach; ?>
                  </select>
                  <i class="fa-solid fa-caret-down caret-genre"></i>
                </div>
              </div>

              <div class="col">
                <label for="third-genre" class="db-text-sec fs-18 form-label">Third Genre</label><br>
                <div class="genre-container">
                  <select name="third-genre" id="third-genre" class="form-control">
                    <option value="">-- Select Genre --</option> <!-- This is your empty option -->
                    <?php foreach ($genres as $genre): ?>
                      <option value="<?= htmlspecialchars($genre) ?>" <?= htmlspecialchars($row['genre_3']) == $genre ? 'selected' : '' ?> class="file-trending"><?= htmlspecialchars($genre) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <i class="fa-solid fa-caret-down caret-genre"></i>
                </div>  
              </div>

              <div class="col-12 mt-3">
                <label for="description" class="db-text-sec fs-18 form-label">Add Description</label><br>
                <textarea name="description" id="description" class="form-control file-trending p-2" placeholder="The movie is about..."><?= $row['description'] ?></textarea>
                <p class="fs-12 text-danger mt-1 mb-2">
                  <?php 
                    if (!$valid_description) {
                      echo "Description can not be empty";
                    }
                  ?>
                </p>
              </div>
            </div>

            <div class="d-flex justify-content-end mb-3">
              <button type="submit" name="edit-trending-btn" class="btn db-bg-primary text-white ms-3 mt-3">Edit Trending</button>
            </div>

          </form>
          
        </div>
      </div>
    </section>
  </main>



  <!--  ========== DATA TABLES CDN  ========== -->
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
</body>

</html>