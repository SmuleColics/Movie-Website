<?php
include '../includes/db-connection.php';
include '../includes/dashboard-header-sidebar.php';

// Handle Top 10 delete
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete-btn-modal"])) {
  $delete_id = intval($_POST["delete-id"]);
  $delete_query = "DELETE FROM tbl_top10 WHERE top10_id = $delete_id";
  $result = mysqli_query($con, $delete_query);

  if (!$result) {
    die("" . mysqli_error($con));
  } else {
    echo "<script>
      alert('Top 10 Deleted Successfully');
      window.location.href='web-home.php';
    </script>";
    exit;
  }
}

// Handle Trending delete
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete-trend-btn-modal"])) {
  $delete_id = intval($_POST["delete-id-trend"]);
  $delete_query = "DELETE FROM tbl_trend WHERE trend_id = $delete_id";
  $result = mysqli_query($con, $delete_query);

  if (!$result) {
    die("" . mysqli_error($con));
  } else {
    echo "<script>
      alert('Trending Deleted Successfully');
      window.location.href='web-home.php';
    </script>";
    exit;
  }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Website - Home</title>
  <link rel="stylesheet" href="../DASHBOARD-CSS/dashboard.css">
  <style>
    .sidebar-content-item:nth-child(4) {
      background-color: var(--dashboard-primary);
    }

    .section-btn {
      background: transparent;
      color: var(--dashboard-primary);
      transition: background 0.2s, color 0.2s;
    }

    .section-btn.active,
    .section-btn.db-bg-primary {
      background: var(--dashboard-primary) !important;
      color: #fff !important;
      border-color: var(--dashboard-primary) !important;
    }

    .btn-group .btn:not(:last-child) {
      border-right: 0;
    }

    .section-btn:hover {
      color: #f4fff8;
    }
  </style>
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.min.css" />
</head>

<body>
  <!-- BUTTON GROUP AND ADD BUTTON -->
  <div class="d-flex flex-md-row flex-column justify-content-between align-items-center p-3">
    <div class="btn-group border mb-md-0 mb-2" role="group" aria-label="Basic example">
      <a href="#" type="button" class="btn section-btn active border" data-section="top10">Top 10</a>
      <a href="#" type="button" class="btn section-btn border" data-section="trending">Trending this week</a>
      <a href="#" type="button" class="btn section-btn border" data-section="featured">Featured Movies</a>
      <a href="#" type="button" class="btn section-btn border" data-section="recommended">Recommended For You</a>
      <a href="#" type="button" class="btn section-btn border" data-section="movie">All Movies</a>
      <a href="#" type="button" class="btn section-btn border" data-section="series">All Series</a>
    </div>

    <div class="p-0 ms-md-2 ms-0 w-auto w-md-auto">
      <a href="home-top10.php" class="btn db-bg-primary db-text-sec" id="add-section-btn">Add Top 10</a>
    </div>
  </div>

  <!-- SECTION TOP 10 -->
  <section class="px-3 section-content" id="top10-section" style="display:block;">
    <div class="card bg-dark">
      <div class="card-body">
        <div class="table-task-top db-text-sec">
          <div class="py-2 ps-3">
            <p class="m-0 fs-20">Top 10</p>
            <p class="m-0 fs-14" style="transform: translateY(-2px)">List of Top 10 in Landing Page</p>
          </div>
        </div>
        <div class="table-responsive mt-2">
          <table class="table table-hover display" id="table-top-10">
            <thead>
              <tr>
                <th class="db-text-primary text-align-left" scope="col">#</th>
                <th class="db-text-primary" scope="col">Title</th>
                <th class="db-text-primary" scope="col">Duration</th>
                <th class="db-text-primary" scope="col">Poster</th>
                <th class="db-text-primary" scope="col">Video</th>
                <th class="db-text-primary" scope="col">Poster Title</th>
                <th class="db-text-primary text-align-left" scope="col">Date Released</th>
                <th class="db-text-primary" scope="col">Age Rating</th>
                <th class="db-text-primary" scope="col">Category</th>
                <th class="db-text-primary" scope="col">Genre 1</th>
                <th class="db-text-primary" scope="col">Genre 2</th>
                <th class="db-text-primary" scope="col">Genre 3</th>
                <th class="db-text-primary" scope="col">Cast</th>
                <th class="db-text-primary" scope="col">Description</th>
                <th class="db-text-primary" scope="col">Edit</th>
                <th class="db-text-primary" scope="col">Delete</th>
              </tr>
            </thead>
            <tbody>

              <?php
              $select = mysqli_query($con, "SELECT * FROM tbl_top10");

              while ($row = mysqli_fetch_assoc($select)):
                $top10_id = $row['top10_id'];
                $title = $row['title'];
                $duration = $row['duration'];
                $poster = $row['poster'];
                $video = $row['video'];
                $modal_poster_title = $row['modal_poster_title'];
                $date_released = $row['date_released'];
                $age_rating = $row['age_rating'];
                $category = $row['category'];
                $genre_1 = $row['genre_1'];
                $genre_2 = $row['genre_2'];
                $genre_3 = $row['genre_3'];
                $cast = $row['cast'];
                $description = $row['description'];
                ?>

                <tr>
                  <th class='db-text-primary text-align-left' scope='row'><?php echo $top10_id; ?></th>
                  <td><?php echo $title; ?></td>
                  <td><?php echo $duration; ?></td>
                  <td><?php echo $poster; ?></td>
                  <td><?php echo $video; ?></td>
                  <td><?php echo $modal_poster_title; ?></td>
                  <td><?php echo $date_released; ?></td>
                  <td><?php echo $age_rating; ?>+</td>
                  <td><?php echo $category; ?></td>
                  <td><?php echo $genre_1; ?></td>
                  <td><?php echo $genre_2; ?></td>
                  <td><?php echo $genre_3; ?></td>
                  <td class='text-truncate' style='max-width: 50px;' data-bs-toggle='tooltip' data-bs-placement='top'
                    title='<?php echo $cast; ?>'><?php echo $cast; ?></td>
                  <td class='text-truncate' style='max-width: 50px;' data-bs-toggle='tooltip' data-bs-placement='top'
                    title='<?php echo $description; ?>'><?php echo $description; ?></td>
                  <td>
                    <a href='home-top10.php?id=<?php echo $top10_id ?>' class='db-text-primary'>
                      <i class='fa-solid fa-pen-to-square'></i>
                    </a>
                  </td>
                  <td>
                    <button class='btn text-white p-0 border-0 delete-btn-top10' data-bs-toggle='modal'
                      data-bs-target='#modalDeleteTop10' data-identification='<?php echo $top10_id ?>'>
                      <i class='fa-solid fa-delete-left text-danger ps-2'></i>
                    </button>
                  </td>
                </tr>

              <?php endwhile; ?>

            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>

  <!-- SECTION TRENDING -->
  <section class="p-3 section-content" id="trending-section" style="display:none;">
    <div class="card bg-dark">
      <div class="card-body">
        <div class="table-task-top db-text-sec">
          <div class="py-2 ps-3">
            <p class="m-0 fs-20">Trending this week</p>
            <p class="m-0 fs-14" style="transform: translateY(-2px)">List of Trending in Landing Page</p>
          </div>
        </div>
        <div class="table-responsive mt-2">
          <table class="table table-hover display" id="table-trending">
            <thead>
              <tr>
                <th class="db-text-primary text-align-left" scope="col">#</th>
                <th class="db-text-primary" scope="col">Title</th>
                <th class="db-text-primary" scope="col">Duration</th>
                <th class="db-text-primary" scope="col">Poster</th>
                <th class="db-text-primary" scope="col">Video</th>
                <th class="db-text-primary" scope="col">Poster Title</th>
                <th class="db-text-primary text-align-left" scope="col">Date Released</th>
                <th class="db-text-primary" scope="col">Age Rating</th>
                <th class="db-text-primary" scope="col">Category</th>
                <th class="db-text-primary" scope="col">Genre 1</th>
                <th class="db-text-primary" scope="col">Genre 2</th>
                <th class="db-text-primary" scope="col">Genre 3</th>
                <th class="db-text-primary" scope="col">Cast</th>
                <th class="db-text-primary" scope="col">Description</th>
                <th class="db-text-primary" scope="col">Edit</th>
                <th class="db-text-primary" scope="col">Delete</th>
              </tr>
            </thead>
            <tbody>

              <?php
              $select = mysqli_query($con, "SELECT * FROM tbl_trend");

              while ($row = mysqli_fetch_assoc($select)):
                $trend_id = $row['trend_id'];
                $title = $row['title'];
                $duration = $row['duration'];
                $poster = $row['poster'];
                $video = $row['video'];
                $modal_poster_title = $row['modal_poster_title'];
                $date_released = $row['date_released'];
                $age_rating = $row['age_rating'];
                $category = $row['category'];
                $genre_1 = $row['genre_1'];
                $genre_2 = $row['genre_2'];
                $genre_3 = $row['genre_3'];
                $cast = $row['cast'];
                $description = $row['description'];
                ?>

                <tr>
                  <th class='db-text-primary text-align-left' scope='row'><?php echo $trend_id; ?></th>
                  <td><?php echo $title; ?></td>
                  <td><?php echo $duration; ?></td>
                  <td><?php echo $poster; ?></td>
                  <td><?php echo $video; ?></td>
                  <td><?php echo $modal_poster_title; ?></td>
                  <td><?php echo $date_released; ?></td>
                  <td><?php echo $age_rating; ?>+</td>
                  <td><?php echo $category; ?></td>
                  <td><?php echo $genre_1; ?></td>
                  <td><?php echo $genre_2; ?></td>
                  <td><?php echo $genre_3; ?></td>
                  <td class='text-truncate' style='max-width: 50px;' data-bs-toggle='tooltip' data-bs-placement='top'
                    title='<?php echo $cast; ?>'><?php echo $cast; ?></td>
                  <td class='text-truncate' style='max-width: 50px;' data-bs-toggle='tooltip' data-bs-placement='top'
                    title='<?php echo $description; ?>'><?php echo $description; ?></td>
                  <td>
                    <a href='home-trend.php?id=<?php echo $trend_id ?>' class='db-text-primary'>
                      <i class='fa-solid fa-pen-to-square'></i>
                    </a>
                  </td>
                  <td>
                    <button class='btn text-white p-0 border-0 delete-btn-trend' data-bs-toggle='modal'
                      data-bs-target='#modalDeleteTrend' data-identification='<?php echo $trend_id ?>'>
                      <i class='fa-solid fa-delete-left text-danger ps-2'></i>
                    </button>
                  </td>
                </tr>

              <?php endwhile; ?>

            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>

  <section class="p-3 section-content" id="featured-section" style="display:none;">
    <div class="card bg-dark">
      <div class="card-body">
        <div class="table-task-top db-text-sec">
          <div class="py-2 ps-3">
            <p class="m-0 fs-20">Featured Movies</p>
            <p class="m-0 fs-14" style="transform: translateY(-2px)">List of Featured in Landing Page</p>
          </div>
        </div>
        <div class="table-responsive mt-2">
          <table class="table table-hover display" id="table-featured">
            <thead>
              <tr>
                <th class="db-text-primary text-align-left" scope="col">#</th>
                <th class="db-text-primary" scope="col">Title</th>
                <th class="db-text-primary" scope="col">Poster</th>
                <th class="db-text-primary" scope="col">Modal Poster</th>
                <th class="db-text-primary" scope="col">Modal Poster Title</th>
                <th class="db-text-primary text-align-left" scope="col">Date Released</th>
                <th class="db-text-primary" scope="col">Age Rating</th>
                <th class="db-text-primary" scope="col">Category</th>
                <th class="db-text-primary" scope="col">Genre 1</th>
                <th class="db-text-primary" scope="col">Genre 2</th>
                <th class="db-text-primary" scope="col">Genre 3</th>
                <th class="db-text-primary" scope="col">Description</th>
                <th class="db-text-primary" scope="col">Edit</th>
                <th class="db-text-primary" scope="col">Delete</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th class='db-text-primary text-align-left' scope='row'>$trending_id</th>
                <td>Featured</td>
                <td>$poster</td>
                <td>$modal_poster</td>
                <td>$modal_poster_title</td>
                <td>$date_released</td>
                <td>$age_rating+</td>
                <td>$category</td>
                <td>$genre_1</td>
                <td>$genre_2</td>
                <td>$genre_3</td>
                <td class='text-truncate' style='max-width: 50px;' data-bs-toggle='tooltip' data-bs-placement='top'
                  title='$description'>$description</td>
                <td>
                  <a href='db-edit-landingpage.php?id=$trending_id' class='db-text-primary'>
                    <i class='fa-solid fa-pen-to-square'></i>
                  </a>
                </td>
                <td>
                  <button class='btn text-white p-0 border-0 delete-btn' data-bs-toggle='modal'
                    data-bs-target='#modalDelete' data-identification='$trending_id'>
                    <i class='fa-solid fa-delete-left text-danger ps-2'></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>

  <section class="p-3 section-content" id="recommended-section" style="display:none;">
    <div class="card bg-dark">
      <div class="card-body">
        <div class="table-task-top db-text-sec">
          <div class="py-2 ps-3">
            <p class="m-0 fs-20">Recommended For You</p>
            <p class="m-0 fs-14" style="transform: translateY(-2px)">List of Recommended in Landing Page</p>
          </div>
        </div>
        <div class="table-responsive mt-2">
          <table class="table table-hover display" id="table-recommended">
            <thead>
              <tr>
                <th class="db-text-primary text-align-left" scope="col">#</th>
                <th class="db-text-primary" scope="col">Title</th>
                <th class="db-text-primary" scope="col">Poster</th>
                <th class="db-text-primary" scope="col">Modal Poster</th>
                <th class="db-text-primary" scope="col">Modal Poster Title</th>
                <th class="db-text-primary text-align-left" scope="col">Date Released</th>
                <th class="db-text-primary" scope="col">Age Rating</th>
                <th class="db-text-primary" scope="col">Category</th>
                <th class="db-text-primary" scope="col">Genre 1</th>
                <th class="db-text-primary" scope="col">Genre 2</th>
                <th class="db-text-primary" scope="col">Genre 3</th>
                <th class="db-text-primary" scope="col">Description</th>
                <th class="db-text-primary" scope="col">Edit</th>
                <th class="db-text-primary" scope="col">Delete</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th class='db-text-primary text-align-left' scope='row'>$trending_id</th>
                <td>Recommended</td>
                <td>$poster</td>
                <td>$modal_poster</td>
                <td>$modal_poster_title</td>
                <td>$date_released</td>
                <td>$age_rating+</td>
                <td>$category</td>
                <td>$genre_1</td>
                <td>$genre_2</td>
                <td>$genre_3</td>
                <td class='text-truncate' style='max-width: 50px;' data-bs-toggle='tooltip' data-bs-placement='top'
                  title='$description'>$description</td>
                <td>
                  <a href='db-edit-landingpage.php?id=$trending_id' class='db-text-primary'>
                    <i class='fa-solid fa-pen-to-square'></i>
                  </a>
                </td>
                <td>
                  <button class='btn text-white p-0 border-0 delete-btn' data-bs-toggle='modal'
                    data-bs-target='#modalDelete' data-identification='$trending_id'>
                    <i class='fa-solid fa-delete-left text-danger ps-2'></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>


  <!-- SECTION MOVIE -->
  <section class="p-3 section-content" id="movie-section" style="display:none;">
    <div class="card bg-dark">
      <div class="card-body">
        <div class="table-task-top db-text-sec">
          <div class="py-2 ps-3">
            <p class="m-0 fs-20">Movies</p>
            <p class="m-0 fs-14" style="transform: translateY(-2px)">List of All Movie</p>
          </div>
        </div>
        <div class="table-responsive mt-2">
          <table class="table table-hover display" id="table-movie">
            <thead>
              <tr>
                <th class="db-text-primary text-align-left" scope="col">#</th>
                <th class="db-text-primary" scope="col">Title</th>
                <th class="db-text-primary" scope="col">Poster</th>
                <th class="db-text-primary" scope="col">Video</th>
                <th class="db-text-primary" scope="col">Modal Poster Title</th>
                <th class="db-text-primary text-align-left" scope="col">Date Released</th>
                <th class="db-text-primary" scope="col">Age Rating</th>
                <th class="db-text-primary" scope="col">Category</th>
                <th class="db-text-primary" scope="col">Genre 1</th>
                <th class="db-text-primary" scope="col">Genre 2</th>
                <th class="db-text-primary" scope="col">Genre 3</th>
                <th class="db-text-primary" scope="col">Description</th>
                <th class="db-text-primary" scope="col">Edit</th>
                <th class="db-text-primary" scope="col">Delete</th>
              </tr>
            </thead>
            <tbody>

              <?php
              $select = mysqli_query($con, "SELECT * FROM tbl_movie_series WHERE category = 'Movie'");

              while ($row = mysqli_fetch_assoc($select)):
                $movie_id = $row['movie_id'];
                $title = $row['title'];
                $poster = $row['poster'];
                $video = $row['video'];
                $modal_poster_title = $row['modal_poster_title'];
                $date_released = $row['date_released'];
                $age_rating = $row['age_rating'];
                $category = $row['category'];
                $genre_1 = $row['genre_1'];
                $genre_2 = $row['genre_2'];
                $genre_3 = $row['genre_3'];
                $description = $row['description'];
                ?>

                <tr>
                  <th class='db-text-primary text-align-left' scope='row'><?php echo $movie_id; ?></th>
                  <td><?php echo htmlspecialchars($title); ?></td>
                  <td><?php echo htmlspecialchars($poster); ?></td>
                  <td><?php echo htmlspecialchars($video); ?></td>
                  <td><?php echo htmlspecialchars($modal_poster_title); ?></td>
                  <td><?php echo htmlspecialchars($date_released); ?></td>
                  <td><?php echo htmlspecialchars($age_rating); ?>+</td>
                  <td><?php echo htmlspecialchars($category); ?></td>
                  <td><?php echo htmlspecialchars($genre_1); ?></td>
                  <td><?php echo htmlspecialchars($genre_2 ?? ''); ?></td>
                  <td><?php echo htmlspecialchars($genre_3 ?? ''); ?></td>
                  <td class='text-truncate' style='max-width: 50px;' data-bs-toggle='tooltip' data-bs-placement='top'
                    title='<?php echo htmlspecialchars($description); ?>'><?php echo htmlspecialchars($description); ?>
                  </td>
                  <td>
                    <a href='home-movie-series.php?id=<?php echo $movie_id ?>' class='db-text-primary'>
                      <i class='fa-solid fa-pen-to-square'></i>
                    </a>
                  </td>
                  <td>
                    <button class='btn text-white p-0 border-0 delete-btn-movie' data-bs-toggle='modal'
                      data-bs-target='#modalDeleteMovie' data-identification='<?php echo $movie_id ?>'>
                      <i class='fa-solid fa-delete-left text-danger ps-2'></i>
                    </button>
                  </td>
                </tr>

              <?php endwhile; ?>

            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>

  <!-- SECTION SERIES -->
  <section class="p-3 section-content" id="series-section" style="display:none;">
    <div class="card bg-dark">
      <div class="card-body">
        <div class="table-task-top db-text-sec">
          <div class="py-2 ps-3">
            <p class="m-0 fs-20">Series</p>
            <p class="m-0 fs-14" style="transform: translateY(-2px)">List of All Series</p>
          </div>
        </div>
        <div class="table-responsive mt-2">
          <table class="table table-hover display" id="table-series">
            <thead>
              <tr>
                <th class="db-text-primary text-align-left" scope="col">#</th>
                <th class="db-text-primary" scope="col">Title</th>
                <th class="db-text-primary" scope="col">Poster</th>
                <th class="db-text-primary" scope="col">Modal Poster</th>
                <th class="db-text-primary" scope="col">Modal Poster Title</th>
                <th class="db-text-primary text-align-left" scope="col">Date Released</th>
                <th class="db-text-primary" scope="col">Age Rating</th>
                <th class="db-text-primary" scope="col">Category</th>
                <th class="db-text-primary" scope="col">Genre 1</th>
                <th class="db-text-primary" scope="col">Genre 2</th>
                <th class="db-text-primary" scope="col">Genre 3</th>
                <th class="db-text-primary" scope="col">Description</th>
                <th class="db-text-primary" scope="col">Edit</th>
                <th class="db-text-primary" scope="col">Delete</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $select = mysqli_query($con, "SELECT * FROM tbl_movie_series WHERE category = 'Series'");

              while ($row = mysqli_fetch_assoc($select)):
                $movie_id = $row['movie_id'];
                $title = $row['title'];
                $poster = $row['poster'];
                $video = $row['video'];
                $modal_poster_title = $row['modal_poster_title'];
                $date_released = $row['date_released'];
                $age_rating = $row['age_rating'];
                $category = $row['category'];
                $genre_1 = $row['genre_1'];
                $genre_2 = $row['genre_2'];
                $genre_3 = $row['genre_3'];
                $description = $row['description'];
                ?>

                <tr>
                  <th class='db-text-primary text-align-left' scope='row'><?php echo $movie_id; ?></th>
                  <td><?php echo htmlspecialchars($title); ?></td>
                  <td><?php echo htmlspecialchars($poster); ?></td>
                  <td><?php echo htmlspecialchars($video); ?></td>
                  <td><?php echo htmlspecialchars($modal_poster_title); ?></td>
                  <td><?php echo htmlspecialchars($date_released); ?></td>
                  <td><?php echo htmlspecialchars($age_rating); ?>+</td>
                  <td><?php echo htmlspecialchars($category); ?></td>
                  <td><?php echo htmlspecialchars($genre_1); ?></td>
                  <td><?php echo htmlspecialchars($genre_2 ?? ''); ?></td>
                  <td><?php echo htmlspecialchars($genre_3 ?? ''); ?></td>
                  <td class='text-truncate' style='max-width: 50px;' data-bs-toggle='tooltip' data-bs-placement='top'
                    title='<?php echo htmlspecialchars($description); ?>'><?php echo htmlspecialchars($description); ?>
                  </td>
                  <td>
                    <a href='home-movie-series.php?id=<?php echo $movie_id ?>' class='db-text-primary'>
                      <i class='fa-solid fa-pen-to-square'></i>
                    </a>
                  </td>
                  <td>
                    <button class='btn text-white p-0 border-0 delete-btn-movie' data-bs-toggle='modal'
                      data-bs-target='#modalDeleteMovie' data-identification='<?php echo $movie_id ?>'>
                      <i class='fa-solid fa-delete-left text-danger ps-2'></i>
                    </button>
                  </td>
                </tr>

              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== START OF MODAL DELETE TOP10 =====  -->
  <div class="modal fade" id="modalDeleteTop10" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form action="" method="post">
          <div class="modal-header">
            <h1 class="modal-title fs-5 db-text-sec">Delete Top 10 Record</h1>
            <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal"
              style="filter: invert(1) grayscale(100%) brightness(200%); opacity: 1;"></button>
          </div>
          <div class="modal-body px-4">
            <input type="hidden" name="delete-id" id="delete-id-top10">
            <h4 class="my-4 db-text-sec">Are you sure you want to delete?</h4>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">NO</button>
            <button type="submit" class="btn db-bg-primary db-text-sec" name="delete-btn-modal"
              style="color: #f4fff8">YES</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- ===== END OF MODAL DELETE TOP10 =====  -->

  <!-- ===== START OF MODAL DELETE TREND =====  -->
  <div class="modal fade" id="modalDeleteTrend" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form action="" method="post">
          <div class="modal-header">
            <h1 class="modal-title fs-5 db-text-sec">Delete Trending Record</h1>
            <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal"
              style="filter: invert(1) grayscale(100%) brightness(200%); opacity: 1;"></button>
          </div>
          <div class="modal-body px-4">
            <input type="hidden" name="delete-id-trend" id="delete-id-trend">
            <h4 class="my-4 db-text-sec">Are you sure you want to delete?</h4>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">NO</button>
            <button type="submit" class="btn db-bg-primary db-text-sec" name="delete-trend-btn-modal"
              style="color: #f4fff8">YES</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- ===== END OF MODAL DELETE TREND =====  -->

  <!--  ========== DATA TABLES CDN  ========== -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const buttons = document.querySelectorAll('.section-btn');
      const addBtn = document.getElementById('add-section-btn');
      // Toggle logic for section buttons
      const sections = {
        top10: document.getElementById('top10-section'),
        trending: document.getElementById('trending-section'),
        featured: document.getElementById('featured-section'),
        recommended: document.getElementById('recommended-section'),
        movie: document.getElementById('movie-section'),
        series: document.getElementById('series-section')
      };

      const addBtnTexts = {
        top10: "Add Top 10",
        trending: "Add Trending",
        featured: "Add Featured",
        recommended: "Add Recommended",
        movie: "Add Movie/Series",
        series: "Add Movie/Series"
      };

      const addBtnHrefs = {
        top10: "home-top10.php",
        trending: "home-trend.php",
        featured: "home-featured.php",
        recommended: "home-recommended.php",
        movie: "home-movie-series.php",
        series: "home-movie-series.php"
      };

      buttons.forEach(btn => {
        btn.addEventListener('click', function (e) {
          e.preventDefault();
          // Remove active class from all buttons
          buttons.forEach(b => b.classList.remove('active'));
          // Add to current
          btn.classList.add('active');
          // Hide all sections
          Object.values(sections).forEach(section => section.style.display = 'none');
          // Show selected section
          const sectionKey = btn.getAttribute('data-section');
          if (sections[sectionKey]) {
            sections[sectionKey].style.display = '';
            // Change Add button text and href
            if (addBtn && addBtnTexts[sectionKey]) {
              addBtn.textContent = addBtnTexts[sectionKey];
              addBtn.setAttribute('href', addBtnHrefs[sectionKey]);
            }
          }
        });
      });

      // DataTable initialization (unchanged)
      if (document.getElementById('table-top-10')) {
        new DataTable('#table-top-10', {
          pagingType: 'simple_numbers',
          responsive: true,
          language: {
            search: '_INPUT_',
            searchPlaceholder: 'Search...'
          }
        });
      }
      if (document.getElementById('table-trending')) {
        new DataTable('#table-trending', {
          pagingType: 'simple_numbers',
          responsive: true,
          language: {
            search: '_INPUT_',
            searchPlaceholder: 'Search...'
          }
        });
      }
      if (document.getElementById('table-featured')) {
        new DataTable('#table-featured', {
          pagingType: 'simple_numbers',
          responsive: true,
          language: {
            search: '_INPUT_',
            searchPlaceholder: 'Search...'
          }
        });
      }
      if (document.getElementById('table-movie')) {
        new DataTable('#table-movie', {
          pagingType: 'simple_numbers',
          responsive: true,
          language: {
            search: '_INPUT_',
            searchPlaceholder: 'Search...'
          }
        });
      }
      if (document.getElementById('table-series')) {
        new DataTable('#table-series', {
          pagingType: 'simple_numbers',
          responsive: true,
          language: {
            search: '_INPUT_',
            searchPlaceholder: 'Search...'
          }
        });
      }
      if (document.getElementById('table-recommended')) {
        new DataTable('#table-recommended', {
          pagingType: 'simple_numbers',
          responsive: true,
          language: {
            search: '_INPUT_',
            searchPlaceholder: 'Search...'
          }
        });
      }

      // Delete button logic 
      $(document).ready(function () {
        $('.delete-btn-top10').on('click', function () {
          var id = $(this).data('identification');
          $('#delete-id-top10').val(id);
        });
        $('.delete-btn-trend').on('click', function () {
          var id = $(this).data('identification');
          $('#delete-id-trend').val(id);
        });
      });
    });
  </script>
</body>

</html>