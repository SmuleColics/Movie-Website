<?php
include '../includes/db-connection.php';

// Handle Add
if (isset($_POST['add-genre'])) {
  $genre_name = trim($_POST['genre_name']);
  if ($genre_name != "") {
    $stmt = $con->prepare("INSERT INTO tbl_movie_series_genre (genre_name) VALUES (?)");
    $stmt->bind_param("s", $genre_name);
    $stmt->execute();
    $stmt->close();
    header("Location: web-genre-management.php");
    exit;
  }
}

// Handle Edit
if (isset($_POST['edit-genre-modal'])) {
  $genre_id = intval($_POST['edit-genre-id']);
  $genre_name = trim($_POST['edit-genre-name']);
  if ($genre_name != "" && $genre_id > 0) {
    $stmt = $con->prepare("UPDATE tbl_movie_series_genre SET genre_name = ? WHERE genre_id = ?");
    $stmt->bind_param("si", $genre_name, $genre_id);
    $stmt->execute();
    $stmt->close();
    header("Location: web-genre-management.php");
    exit;
  }
}

// Handle Delete
if (isset($_POST['delete-genre-btn-modal'])) {
  $genre_id = intval($_POST['delete-genre-id']);
  if ($genre_id > 0) {
    $stmt = $con->prepare("DELETE FROM tbl_movie_series_genre WHERE genre_id = ?");
    $stmt->bind_param("i", $genre_id);
    $stmt->execute();
    $stmt->close();
    header("Location: web-genre-management.php");
    exit;
  }
}

// After all logic that does header(), include UI with possible output.
include '../includes/dashboard-header-sidebar.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>Genre Management</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../DASHBOARD-CSS/dashboard.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.min.css" />
  <style>
    input::placeholder { color: #6c757d !important; }
    .sidebar-content-item:nth-child(3) { background-color: var(--dashboard-primary); }
  </style>
</head>

<body class="bg-dark">
  <div class="container mt-5">
    <div class="d-flex flex-md-row flex-column justify-content-between align-items-center mb-3">
      <div>
        <a href="web-genre.php" class="btn db-bg-primary text-white ms-3 mt-3">
          <i class="fa-solid fa-chevron-left"></i> Go Back
        </a>
      </div>
      <div class="p-0 ms-md-2 ms-0 w-auto w-md-auto">
        <form class="d-flex" method="POST" style="gap: 8px;">
          <input type="text" name="genre_name" class="form-control bg-transparent db-text-sec"
            placeholder="Add new genre" required>
          <button type="submit" name="add-genre" class="btn db-bg-primary db-text-sec">Add</button>
        </form>
      </div>
    </div>

    <!-- ===== START OF GENRES SECTION ===== -->
    <section class="px-3 section-content" id="genre-section" style="display:block;">
      <div class="card bg-dark">
        <div class="card-body">
          <div class="table-task-top db-text-sec">
            <div class="py-2 ps-3">
              <p class="m-0 fs-20">Genres</p>
              <p class="m-0 fs-14" style="transform: translateY(-2px)">List of All Genres</p>
            </div>
          </div>
          <div class="table-responsive mt-2">
            <table class="table table-hover display" id="table-genre">
              <thead>
                <tr>
                  <th class="db-text-primary text-center ps-4" scope="col">#</th>
                  <th class="db-text-primary text-center ps-2" scope="col">Genre Name</th>
                  <th class="db-text-primary text-center pe-2" scope="col">Edit</th>
                  <th class="db-text-primary text-center ps-4" scope="col">Delete</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $genres = mysqli_query($con, "SELECT * FROM tbl_movie_series_genre ORDER BY genre_id ASC");
                while ($row = mysqli_fetch_assoc($genres)):
                  ?>
                  <tr>
                    <th class="db-text-primary text-center" scope="row"><?php echo $row['genre_id']; ?></th>
                    <td class="text-center">
                      <span class="genre-label" id="genre-label-<?php echo $row['genre_id']; ?>">
                        <?php echo htmlspecialchars($row['genre_name']); ?>
                      </span>
                    </td>
                    <td class="text-center">
                      <a href="javascript:void(0);" class="db-text-primary edit-btn" data-bs-toggle="modal" data-bs-target="#modalEditGenre"
                        data-id="<?php echo $row['genre_id']; ?>"
                        data-name="<?php echo htmlspecialchars($row['genre_name']); ?>">
                        <i class="fa-solid fa-pen-to-square"></i>
                      </a>
                    </td>
                    <td class="text-center">
                      <a href="javascript:void(0);" class="text-danger delete-btn" data-bs-toggle="modal" data-bs-target="#modalDeleteGenre"
                        data-id="<?php echo $row['genre_id']; ?>">
                        <i class="fa-solid fa-delete-left"></i>
                      </a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
    <!-- ===== END OF GENRES SECTION ===== -->

    <!-- ===== START OF MODAL EDIT FOR GENRE ===== -->
    <div class="modal fade" id="modalEditGenre" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <form action="" method="post">
            <div class="modal-header">
              <h1 class="modal-title fs-5 db-text-sec">Edit Genre</h1>
              <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal"
                style="filter: invert(1) grayscale(100%) brightness(200%); opacity: 1;"></button>
            </div>
            <div class="modal-body px-4">
              <input type="hidden" name="edit-genre-id" id="edit-genre-id">
              <div class="mb-3">
                <label for="edit-genre-name" class="form-label db-text-sec">Genre Name</label>
                <input type="text" name="edit-genre-name" id="edit-genre-name" class="form-control" required>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn db-bg-primary db-text-sec" name="edit-genre-modal"
                style="color: #f4fff8">Save</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- ===== END OF MODAL EDIT FOR GENRE ===== -->

    <!-- ===== START OF MODAL DELETE FOR GENRE ===== -->
    <div class="modal fade" id="modalDeleteGenre" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <form action="" method="post">
            <div class="modal-header">
              <h1 class="modal-title fs-5 db-text-sec">Delete Genre</h1>
              <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal"
                style="filter: invert(1) grayscale(100%) brightness(200%); opacity: 1;"></button>
            </div>
            <div class="modal-body px-4">
              <input type="hidden" name="delete-genre-id" id="delete-genre-id">
              <h4 class="my-4 db-text-sec">Are you sure you want to delete this genre?</h4>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-bs-dismiss="modal">NO</button>
              <button type="submit" class="btn db-bg-primary db-text-sec" name="delete-genre-btn-modal"
                style="color: #f4fff8">YES</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- ===== END OF MODAL DELETE FOR GENRE ===== -->

    <!--  ========== DATA TABLES CDN  ========== -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://kit.fontawesome.com/3b8c7e1b07.js" crossorigin="anonymous"></script>

    <script>
      $(document).ready(function () {
        // DataTable initialization
        if (window.DataTable) {
          new DataTable('#table-genre', {
            pagingType: 'simple_numbers',
            responsive: true,
            language: { search: '_INPUT_', searchPlaceholder: 'Search...' }
          });
        }

        // Edit Modal population
        $(document).on('click', '.edit-btn', function () {
          var id = $(this).data('id');
          var name = $(this).data('name');
          $('#edit-genre-id').val(id);
          $('#edit-genre-name').val(name);
        });

        // Delete Modal population
        $(document).on('click', '.delete-btn', function () {
          var id = $(this).data('id');
          $('#delete-genre-id').val(id);
        });
      });
    </script>
</body>
</html>