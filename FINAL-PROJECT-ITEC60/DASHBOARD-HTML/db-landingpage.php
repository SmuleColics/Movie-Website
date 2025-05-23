<?php
include '../includes/dashboard-header-sidebar.php';
include '../includes/db-connection.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Action</title>
  <!-- ========== CSS LINK ========== -->
  <link rel="stylesheet" href="../DASHBOARD-CSS/dashboard.css">
  <link rel="stylesheet" href="../DASHBOARD-CSS/for-all.css">
  <!-- ===== DATA TABLES CDN ===== -->
  <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.min.css">
  <!-- ========== Bootstrap 5.3 CSS  ========== -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
      .sidebar-content-item:nth-child(7) {
        background-color: var(--dashboard-primary);
      }
  </style>
</head>

<body>

  <main class="container-lg p-0 overflow-hidden">
    <a href="db-add-landingpage.php" class="btn db-bg-primary text-white ms-3 mt-3">Add a Trending</a>

    <!-- ========== SIGN UP ACCOUNTS SECTION ========== -->
    <section class="signup-accounts-section text-left p-3">
      <div class="card bg-dark">
        <div class="card-body">

          <div class="table-task-top db-text-sec">
            <div class="py-2 ps-3">
              <p class="m-0 fs-20">Trending this week</p>
              <p class="m-0 fs-14" style="transform: translateY(-2px)">List of Trending in Landing Page</p>
            </div>
          </div>

          <div class="table-responsive mt-2">
            <table class="table table-hover display" id="table-signup-acc">
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
                  $select_query = mysqli_query($con, "SELECT * FROM tbl_trending");

                  while ($row = mysqli_fetch_assoc($select_query))  {
                    $trending_id = $row['trending_id'];
                    $title = $row['title'];
                    $poster = $row['poster'];
                    $modal_poster = $row['modal_poster'];
                    $modal_poster_title = $row['modal_poster_title'];
                    $date_released = $row['date_released'];
                    $age_rating = $row['age_rating'];
                    $category = $row['category'];
                    $genre_1 = $row['genre_1'];
                    $genre_2 = $row['genre_2'];
                    $genre_3 = $row['genre_3'];
                    $description = $row['description'];

                    echo "
                    <tr>
                      <th class='db-text-primary text-align-left' scope='row'>$trending_id</th>
                      <td>$title</td>
                      <td>$poster</td>
                      <td>$modal_poster</td>
                      <td>$modal_poster_title</td>
                      <td>$date_released</td>
                      <td>$age_rating+</td>
                      <td>$category</td>
                      <td>$genre_1</td>
                      <td>$genre_2</td>
                      <td>$genre_3</td>
                      <td class='text-truncate' style='max-width: 50px;' data-bs-toggle='tooltip' data-bs-placement='top' title='$description'>$description</td>
                      <td>
                        <a href='db-edit-landingpage.php?id=$trending_id' class='db-text-primary'>
                          <i class='fa-solid fa-pen-to-square'></i>
                        </a>  
                      </td>
                      <td>
                        <button 
                          class='btn text-white p-0 border-0 delete-btn' data-bs-toggle='modal'
                          data-bs-target='#modalDelete'
                          data-identification='$trending_id'>
                          <i class='fa-solid fa-delete-left text-danger ps-2'></i>
                        </button>
                      </td>
                    </tr>
                  ";

                  }
                ?>

              </tbody>
            </table>
          </div>

        </div>
      </div>
    </section>
  </main>

  <!-- ===== START OF MODAL DELETE =====  -->
  <?php 
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete-btn-modal"])) {

      $delete_id = $_POST["delete-id"];
      $delete_query = "DELETE FROM tbl_trending WHERE trending_id = $delete_id";
      $result = mysqli_query($con, $delete_query);

      if (!$result) { 
        die("". mysqli_error($con));
      } else {
        echo "<script>
        alert('Deleted Successfully');
        window.location.href='db-landingpage.php';
        </script>";
      }
    }
  ?>

  <div class="modal fade" id="modalDelete" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form action="" method="post">
          <div class="modal-header">
            <h1 class="modal-title fs-5 db-text-sec">Delete Trending Record</h1>
            <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal" style="filter: invert(1) grayscale(100%) brightness(200%); opacity: 1;"></button>
          </div>
          <div class="modal-body px-4">
            <input type="hidden" name="delete-id" id="delete-id">
            <h4 class="my-4 db-text-sec">Are you sure you want to delete?</h4>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">NO</button>
            <button type="submit" class="btn db-bg-primary db-text-sec" name="delete-btn-modal" style="color: #f4fff8">YES</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- =====  END OF MODAL DELETE =====  -->


  <!--  ========== DATA TABLES CDN  ========== -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.min.js"></script>
  >

  <!-- Bootstrap JS (make sure this is included before closing </body>) -->
  <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->

  <script>
    new DataTable('#table-signup-acc', {
      pagingType: 'simple_numbers',
      responsive: true,
      language: {
        search: '_INPUT_',
        searchPlaceholder: 'Search...'
      }
    });

    $(document).ready(function () {
        $('.delete-btn').on('click', function () {

        var id = $(this).data('identification');

        $('#delete-id').val(id);
        });

      });

    // const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    // const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => 
    //   new bootstrap.Tooltip(tooltipTriggerEl)
    // );
  </script> 
</body>

</html>