<?php
include '../includes/db-connection.php';
include '../includes/dashboard-header-sidebar.php';

// Handle Top 10 delete
// if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete-btn-modal"])) {
//   $delete_id = intval($_POST["delete-id"]);
//   $delete_query = "DELETE FROM tbl_top10 WHERE top10_id = $delete_id";
//   $result = mysqli_query($con, $delete_query);

//   if (!$result) {
//     die("" . mysqli_error($con));
//   } else {
//     echo "<script>
//       alert('Top 10 Deleted Successfully');
//       window.location.href='web-home.php';
//     </script>";
//     exit;
//   }
// }

// Handle Trending delete
// if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete-trend-btn-modal"])) {
//   $delete_id = intval($_POST["delete-id-trend"]);
//   $delete_query = "DELETE FROM tbl_trend WHERE trend_id = $delete_id";
//   $result = mysqli_query($con, $delete_query);

//   if (!$result) {
//     die("" . mysqli_error($con));
//   } else {
//     echo "<script>
//       alert('Trending Deleted Successfully');
//       window.location.href='web-home.php';
//     </script>";
//     exit;
//   }
// }

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Website - Home</title>
  <link rel="stylesheet" href="../DASHBOARD-CSS/dashboard.css">
  <style>
    .sidebar-content-item:nth-child(2) {
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
      <a href="#" type="button" class="btn section-btn border" data-section="recommended">Recommended For You</a>
    </div>
    <div class="p-0 ms-md-2 ms-0 w-auto w-md-auto">
      <a href="web-movie-series.php" class="btn db-bg-primary db-text-sec" id="add-movie-btn">Add Movie/Series</a>
    </div>
  </div>

   <!-- SECTION TOP 10 -->
  <section class="px-3 section-content" id="top10-section" style="display:block;">
    <div class="card bg-dark">
      <div class="card-body">
        <div class="table-task-top db-text-sec">
          <div class="py-2 ps-3">
            <p class="m-0 fs-20">Top 10</p>
            <p class="m-0 fs-14" style="transform: translateY(-2px)">List of Top 10 Movies/Series</p>
          </div>
        </div>
        <div class="table-responsive mt-2">
          <table class="table table-hover display" id="table-top-10">
            <thead>
              <tr>
                <th class="db-text-primary text-align-left" scope="col">#</th>
                <th class="db-text-primary" scope="col">Title</th>
                <th class="db-text-primary" scope="col">Poster</th>
                <th class="db-text-primary" scope="col">Description</th>
                <th class="db-text-primary" scope="col">View</th>
                <th class="db-text-primary" scope="col">Edit</th>
                <th class="db-text-primary" scope="col">Delete</th>
              </tr>
            </thead>
            <tbody>
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

              while ($row = mysqli_fetch_assoc($select)):
                // Prepare all fields for data attributes (escaping as needed)
                $data_attrs = '';
                foreach([
                  'movie_series_id', 'title', 'duration', 'poster', 'video', 'modal_poster_title', 'date_released', 'age_rating', 'category',
                  'genre_1', 'genre_2', 'genre_3', 'cast', 'description', 'views', 'date_posted'
                ] as $field) {
                  $value = $row[$field] ?? '';
                  // For textarea, allow newlines, for html attribute use htmlspecialchars (no quotes)
                  $data_attrs .= " data-$field=\"".htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."\"";
                }
              ?>
                <tr>
                  <th class='db-text-primary text-align-left' scope='row'><?php echo $row['movie_series_id']; ?></th>
                  <td><?php echo $row['title']; ?></td>
                  <td><?php echo $row['poster']; ?></td>
                  <td class='text-truncate' style='max-width: 50px;' data-bs-toggle='tooltip' data-bs-placement='top'
                    title='<?php echo $row['description']; ?>'><?php echo $row['description']; ?></td>
                  <td>
                    <a href="#" class='db-text-primary view-btn'
                      data-bs-toggle='modal' data-bs-target='#modalView'
                      <?php echo $data_attrs; ?>>
                      <i class="fas fa-eye"></i>
                    </a>
                  </td>
                  <td>
                    <a href="web-movie-series.php?id=<?php echo $row['movie_series_id']; ?>&from=web-home.php" class="db-text-primary">
                      <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                  </td>
                  <td>
                    <button class='btn text-white p-0 border-0 delete-btn-top10' data-bs-toggle='modal'
                      data-bs-target='#modalDeleteTop10' data-identification='<?php echo $row['movie_series_id'] ?>'>
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

  <!-- SECTION RECOMMENDED -->
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
                <th class='db-text-primary text-align-left' scope='row'>1</th>
                <td>Spider-Man: Into the Spider-Verse</td>
                <td>spiderman-poster.jpg</td>
                <td>spiderman-modal.jpg</td>
                <td>Spider-Verse Modal</td>
                <td>2018-12-14</td>
                <td>10+</td>
                <td>Animation</td>
                <td>Action</td>
                <td>Adventure</td>
                <td>Fantasy</td>
                <td>Teen Miles Morales becomes Spider-Man of his reality.</td>
                <td>
                  <a href='db-edit-landingpage.php?id=1' class='db-text-primary'>
                    <i class='fa-solid fa-pen-to-square'></i>
                  </a>
                </td>
                <td>
                  <button class='btn text-white p-0 border-0 delete-btn' data-bs-toggle='modal'
                    data-bs-target='#modalDelete' data-identification='1'>
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

  <!-- Modal for viewing details -->
  <div class="modal fade" id="modalView" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <form>
          <div class="modal-header">
            <h1 class="modal-title fs-5 db-text-sec">Movie/Series Information</h1>
            <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal"
              style="filter: invert(1) grayscale(100%) brightness(200%); opacity: 1;"></button>
          </div>
          <div class="modal-body px-4">
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label db-text-sec">ID</label>
                <input type="text" class="form-control db-text-sec" id="view-movie_series_id" readonly>
              </div>
              <div class="col-md-8">
                <label class="form-label db-text-sec">Title</label>
                <input type="text" class="form-control" id="view-title" readonly>
              </div>
              <div class="col-md-4">
                <label class="form-label db-text-sec">Duration</label>
                <input type="text" class="form-control" id="view-duration" readonly>
              </div>
              <div class="col-md-4">
                <label class="form-label db-text-sec">Poster</label>
                <input type="text" class="form-control" id="view-poster" readonly>
              </div>
              <div class="col-md-4">
                <label class="form-label db-text-sec">Video</label>
                <input type="text" class="form-control" id="view-video" readonly>
              </div>
              <div class="col-md-6">
                <label class="form-label db-text-sec">Modal Poster Title</label>
                <input type="text" class="form-control" id="view-modal_poster_title" readonly>
              </div>
              <div class="col-md-3">
                <label class="form-label db-text-sec">Date Released</label>
                <input type="text" class="form-control" id="view-date_released" readonly>
              </div>
              <div class="col-md-3">
                <label class="form-label db-text-sec">Age Rating</label>
                <input type="text" class="form-control" id="view-age_rating" readonly>
              </div>
              <div class="col-md-4">
                <label class="form-label db-text-sec">Category</label>
                <input type="text" class="form-control" id="view-category" readonly>
              </div>
              <div class="col-md-4">
                <label class="form-label db-text-sec">Genre 1</label>
                <input type="text" class="form-control" id="view-genre_1" readonly>
              </div>
              <div class="col-md-4">
                <label class="form-label db-text-sec">Genre 2</label>
                <input type="text" class="form-control" id="view-genre_2" readonly>
              </div>
              <div class="col-md-4">
                <label class="form-label db-text-sec">Genre 3</label>
                <input type="text" class="form-control" id="view-genre_3" readonly>
              </div>
              <div class="col-md-8">
                <label class="form-label db-text-sec">Cast</label>
                <input type="text" class="form-control" id="view-cast" readonly>
              </div>
              <div class="col-12">
                <label class="form-label db-text-sec">Description</label>
                <textarea class="form-control bg-transparent db-text-sec p-2" id="view-description" rows="2" readonly style="border-color: #6c757d;;"></textarea>
              </div>
              <div class="col-md-4">
                <label class="form-label db-text-sec">Views</label>
                <input type="text" class="form-control" id="view-views" readonly>
              </div>
              <div class="col-md-8">
                <label class="form-label db-text-sec">Date Posted</label>
                <input type="text" class="form-control" id="view-date_posted" readonly>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- USE THIS ON GENRE -->
  <!-- SECTION MOVIE -->
  <!-- <section class="p-3 section-content" id="movie-section" style="display:none;">
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
              <tr>
                <th class='db-text-primary text-align-left' scope='row'>1</th>
                <td>Inception</td>
                <td>inception-poster.jpg</td>
                <td>inception-video.mp4</td>
                <td>Inception Modal</td>
                <td>2010-07-16</td>
                <td>13+</td>
                <td>Movie</td>
                <td>Sci-Fi</td>
                <td>Action</td>
                <td>Thriller</td>
                <td class='text-truncate' style='max-width: 50px;' data-bs-toggle='tooltip' data-bs-placement='top'
                  title='A thief who steals corporate secrets through dream-sharing technology.'>
                  A thief who steals corporate secrets through dream-sharing technology.
                </td>
                <td>
                  <a href='home-movie-series.php?id=1' class='db-text-primary'>
                    <i class='fa-solid fa-pen-to-square'></i>
                  </a>
                </td>
                <td>
                  <button class='btn text-white p-0 border-0 delete-btn-movie' data-bs-toggle='modal'
                    data-bs-target='#modalDeleteMovie' data-identification='1'>
                    <i class='fa-solid fa-delete-left text-danger ps-2'></i>
                  </button>
                </td>
              </tr>
              <tr>
                <th class='db-text-primary text-align-left' scope='row'>2</th>
                <td>Toy Story</td>
                <td>toystory-poster.jpg</td>
                <td>toystory-video.mp4</td>
                <td>Toy Story Modal</td>
                <td>1995-11-22</td>
                <td>7+</td>
                <td>Movie</td>
                <td>Animation</td>
                <td>Family</td>
                <td>Comedy</td>
                <td class='text-truncate' style='max-width: 50px;' data-bs-toggle='tooltip' data-bs-placement='top'
                  title='A cowboy doll is profoundly threatened and jealous when a new spaceman figure supplants him.'>
                  A cowboy doll is profoundly threatened and jealous when a new spaceman figure supplants him.
                </td>
                <td>
                  <a href='home-movie-series.php?id=2' class='db-text-primary'>
                    <i class='fa-solid fa-pen-to-square'></i>
                  </a>
                </td>
                <td>
                  <button class='btn text-white p-0 border-0 delete-btn-movie' data-bs-toggle='modal'
                    data-bs-target='#modalDeleteMovie' data-identification='2'>
                    <i class='fa-solid fa-delete-left text-danger ps-2'></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section> -->

  <!-- SECTION SERIES -->
  <!-- <section class="p-3 section-content" id="series-section" style="display:none;">
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
              <tr>
                <th class='db-text-primary text-align-left' scope='row'>1</th>
                <td>Stranger Things</td>
                <td>strangerthings-poster.jpg</td>
                <td>strangerthings-modal.jpg</td>
                <td>Stranger Things Modal</td>
                <td>2016-07-15</td>
                <td>16+</td>
                <td>Series</td>
                <td>Sci-Fi</td>
                <td>Horror</td>
                <td>Drama</td>
                <td class='text-truncate' style='max-width: 50px;' data-bs-toggle='tooltip' data-bs-placement='top'
                  title='A group of young friends witness supernatural forces and secret government exploits.'>
                  A group of young friends witness supernatural forces and secret government exploits.
                </td>
                <td>
                  <a href='home-movie-series.php?id=11' class='db-text-primary'>
                    <i class='fa-solid fa-pen-to-square'></i>
                  </a>
                </td>
                <td>
                  <button class='btn text-white p-0 border-0 delete-btn-movie' data-bs-toggle='modal'
                    data-bs-target='#modalDeleteMovie' data-identification='11'>
                    <i class='fa-solid fa-delete-left text-danger ps-2'></i>
                  </button>
                </td>
              </tr>
              <tr>
                <th class='db-text-primary text-align-left' scope='row'>2</th>
                <td>Friends</td>
                <td>friends-poster.jpg</td>
                <td>friends-modal.jpg</td>
                <td>Friends Modal</td>
                <td>1994-09-22</td>
                <td>13+</td>
                <td>Series</td>
                <td>Comedy</td>
                <td>Romance</td>
                <td>Drama</td>
                <td class='text-truncate' style='max-width: 50px;' data-bs-toggle='tooltip' data-bs-placement='top'
                  title='Follows the personal and professional lives of six twenty to thirty-something friends.'>
                  Follows the personal and professional lives of six twenty to thirty-something friends.
                </td>
                <td>
                  <a href='home-movie-series.php?id=12' class='db-text-primary'>
                    <i class='fa-solid fa-pen-to-square'></i>
                  </a>
                </td>
                <td>
                  <button class='btn text-white p-0 border-0 delete-btn-movie' data-bs-toggle='modal'
                    data-bs-target='#modalDeleteMovie' data-identification='12'>
                    <i class='fa-solid fa-delete-left text-danger ps-2'></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section> -->

  <!-- ===== START OF MODAL DELETE TOP10 =====  -->
  <!-- <div class="modal fade" id="modalDeleteTop10" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
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
  </div> -->
  <!-- ===== END OF MODAL DELETE TOP10 =====  -->

  <!-- ===== START OF MODAL DELETE TREND =====  -->
  <!-- <div class="modal fade" id="modalDeleteTrend" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
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
  </div> -->
  <!-- ===== END OF MODAL DELETE TREND =====  -->

  <!--  ========== DATA TABLES CDN  ========== -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const buttons = document.querySelectorAll('.section-btn');
      const sections = {
        top10: document.getElementById('top10-section'),
        recommended: document.getElementById('recommended-section'),
      };
      Object.entries(sections).forEach(([key, section]) => {
        if (key === 'top10') {
          section.style.display = 'block';
        } else {
          section.style.display = 'none';
        }
      });
      buttons.forEach(btn => {
        btn.addEventListener('click', function (e) {
          e.preventDefault();
          buttons.forEach(b => b.classList.remove('active'));
          btn.classList.add('active');
          Object.values(sections).forEach(section => section.style.display = 'none');
          const sectionKey = btn.getAttribute('data-section');
          if (sections[sectionKey]) {
            sections[sectionKey].style.display = 'block';
          }
        });
      });

      // DataTable initialization (unchanged)
      if (document.getElementById('table-top-10')) {
        new DataTable('#table-top-10', {
          pagingType: 'simple_numbers',
          responsive: true,
          language: { search: '_INPUT_', searchPlaceholder: 'Search...' }
        });
      }
      if (document.getElementById('table-recommended')) {
        new DataTable('#table-recommended', {
          pagingType: 'simple_numbers',
          responsive: true,
          language: { search: '_INPUT_', searchPlaceholder: 'Search...' }
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

      // View Details Modal population
      $(document).on('click', '.view-btn', function () {
        var fields = [
          'movie_series_id', 'title', 'duration', 'poster', 'video', 'modal_poster_title', 'date_released', 'age_rating', 'category',
          'genre_1', 'genre_2', 'genre_3', 'cast', 'description', 'views', 'date_posted'
        ];
        for (const field of fields) {
          var val = $(this).data(field) ?? '';
          if (field === 'description') {
            $('#view-' + field).val(val);
          } else {
            $('#view-' + field).val(val);
          }
        }
      });
  </script>
</body>

</html>