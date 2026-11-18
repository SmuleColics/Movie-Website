<?php
include '../includes/db-connection.php';
include '../includes/dashboard-header-sidebar.php';
// ?category=Movie or ?category=Series shows just that kind (sidebar Movies / Series).
$cv_category = in_array($_GET['category'] ?? '', ['Movie', 'Series'], true) ? $_GET['category'] : '';

// Handle Movie/Series delete
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete-btn-modal"])) {
  $delete_id = intval($_POST["delete-id"]);
  $delete_query = "DELETE FROM tbl_movie_series WHERE movie_series_id = $delete_id";
  $result = mysqli_query($con, $delete_query);

  if (!$result) {
    die("Delete failed: " . mysqli_error($con));
  } else {
    echo "<script>
      alert('Movie/Series Deleted Successfully');
      window.location.href='web-movies.php';
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
  <title>Website - Genre</title>
  <link rel="stylesheet" href="../DASHBOARD-CSS/dashboard.css?v=<?php echo CV_ASSET_VERSION; ?>">
  <style>
  </style>
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.min.css" />
</head>

<body>

  <div class="d-flex justify-content-end align-items-center p-3">
    <div class="p-0 ms-md-2 ms-0 w-auto w-md-auto">
      <a href="web-movie-series.php<?php echo $cv_category === 'Series' ? '?category=Series' : ''; ?>" class="btn db-bg-primary db-text-sec" id="add-movie-btn"><?php echo $cv_category === 'Series' ? 'Add Series' : ($cv_category === 'Movie' ? 'Add Movie' : 'Add Movie/Series'); ?></a>
    </div>
  </div>

  <!-- SECTION ALL MOVIES/SERIES -->
  <section class="px-3 section-content" id="top10-section" style="display:block;">
    <div class="card bg-dark">
      <div class="card-body">
        <div class="table-task-top db-text-sec">
          <?php
          // ?category=Movie or ?category=Series (from the dashboard cards) shows just that kind.
          $cv_category = in_array($_GET['category'] ?? '', ['Movie', 'Series'], true) ? $_GET['category'] : '';
          ?>
          <div class="py-2 ps-3 d-flex flex-wrap align-items-center justify-content-between gap-2 pe-3">
            <div>
              <p class="m-0 fs-20"><?php echo $cv_category === 'Movie' ? 'Movies' : ($cv_category === 'Series' ? 'Series' : 'Movies/Series'); ?></p>
              <p class="m-0 fs-14" style="transform: translateY(-2px)">
                <?php echo $cv_category ? 'Showing ' . ($cv_category === 'Movie' ? 'movies' : 'series') . ' only' : 'List of All Movies/Series'; ?>
              </p>
            </div>
            <div class="btn-group btn-group-sm" role="group" aria-label="Show">
              <a href="web-movies.php" class="btn <?php echo $cv_category === '' ? 'btn-light' : 'btn-outline-light'; ?>">All</a>
              <a href="web-movies.php?category=Movie" class="btn <?php echo $cv_category === 'Movie' ? 'btn-light' : 'btn-outline-light'; ?>">Movies</a>
              <a href="web-movies.php?category=Series" class="btn <?php echo $cv_category === 'Series' ? 'btn-light' : 'btn-outline-light'; ?>">Series</a>
            </div>
          </div>
        </div>
        <div class="table-responsive mt-2">
          <table class="table table-hover display" id="table-all-movie">
            <thead>
              <tr>
                <th class="db-text-primary text-align-left" scope="col">#</th>
                <th class="db-text-primary" scope="col">Title</th>
                <th class="db-text-primary text-nowrap" scope="col">Genre 1</th>
                <th class="db-text-primary text-nowrap" scope="col">Genre 2</th>
                <th class="db-text-primary text-nowrap" scope="col">Genre 3</th>
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
                  ms.movie_series_id,
                  ms.title,
                  ms.duration,
                  ms.poster,
                  ms.video,
                  ms.modal_poster_title,
                  ms.date_released,
                  ms.age_rating,
                  ms.category,
                  g1.genre_name AS genre_1,
                  g2.genre_name AS genre_2,
                  g3.genre_name AS genre_3,
                  ms.cast,
                  ms.description,
                  ms.views,
                  ms.date_posted
                FROM tbl_movie_series ms
                LEFT JOIN tbl_movie_series_genre g1 ON ms.genre_id1 = g1.genre_id
                LEFT JOIN tbl_movie_series_genre g2 ON ms.genre_id2 = g2.genre_id
                LEFT JOIN tbl_movie_series_genre g3 ON ms.genre_id3 = g3.genre_id
                " . ($cv_category ? "WHERE ms.category = '" . $cv_category . "'" : "") . "
                ORDER BY ms.movie_series_id ASC
              ");

              while ($row = mysqli_fetch_assoc($select)):
                $data_attrs = '';
                foreach([
                  'movie_series_id', 'title', 'duration', 'poster', 'video', 'modal_poster_title', 'date_released', 'age_rating', 'category',
                  'genre_1', 'genre_2', 'genre_3', 'cast', 'description', 'views', 'date_posted'
                ] as $field) {
                  $value = $row[$field] ?? '';
                  $data_attrs .= " data-$field=\"".htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."\"";
                }
              ?>
                <tr>
                  <th class='db-text-primary text-align-left' scope='row'><?php echo $row['movie_series_id']; ?></th>
                  <td><?php echo e($row['title']); ?></td>
                  <td><?php echo e($row['genre_1']); ?></td>
                  <td><?php echo e($row['genre_2']); ?></td>
                  <td><?php echo e($row['genre_3']); ?></td>
                  <td class='text-truncate' style='max-width: 100px;'><?php echo htmlspecialchars($row['description']); ?></td>
                  <td>
                    <a href="#" class='db-text-primary view-btn'
                      data-bs-toggle='modal' data-bs-target='#modalView'
                      <?php echo $data_attrs; ?>>
                      <i class="fas fa-eye"></i>
                    </a>
                  </td>
                  <td>
                    <a href="web-movie-series.php?id=<?php echo $row['movie_series_id']; ?>&from=web-genre.php" class="db-text-primary">
                        <i class="fa-solid fa-pen-to-square"></i>
                      </a>
                  </td>
                  <td>
                    <button class='btn text-white p-0 border-0 delete-btn' data-bs-toggle='modal'
                      data-bs-target='#modalDelete' data-identification='<?php echo $row['movie_series_id'] ?>'>
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

  <!-- Modal for viewing details (show all values) -->
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
                <textarea class="form-control bg-transparent db-text-sec p-2" id="view-description" rows="2" readonly style="border-color: #6c757d;"></textarea>
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

  <!-- ===== START OF MODAL DELETE =====  -->
  <div class="modal fade" id="modalDelete" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form action="" method="post">
          <div class="modal-header">
            <h1 class="modal-title fs-5 db-text-sec">Delete Movie/Series</h1>
            <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal"
              style="filter: invert(1) grayscale(100%) brightness(200%); opacity: 1;"></button>
          </div>
          <div class="modal-body px-4">
            <input type="hidden" name="delete-id" id="delete-id" required>
            <h4 class="my-4 db-text-sec text-center">Are you sure you want to delete this movie/series?</h4>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">NO</button>
            <button type="submit" class="btn db-bg-primary db-text-sec" name="delete-btn-modal"
              style="color: #F3EAD6">YES</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- ===== END OF MODAL DELETE =====  -->

  <!--  ========== DATA TABLES CDN  ========== -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.min.js"></script>
  <script>
    $(document).ready(function () {
      // DataTable initialization
      if (document.getElementById('table-all-movie')) {
        new DataTable('#table-all-movie', {
          pagingType: 'simple_numbers',
          responsive: true,
          language: { search: '_INPUT_', searchPlaceholder: 'Search...' }
        });
      }

      // Use delegated event binding for delete button
      $(document).on('click', '.delete-btn', function () {
        var id = $(this).data('identification');
        $('#delete-id').val(id);
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
    });
  </script>
</body>

</html>