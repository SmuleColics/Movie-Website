<?php
include '../includes/dashboard-header-sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Action</title>
  <!-- ========== CSS LINK ========== -->
  <link rel="stylesheet" href="../DASHBOARD-CSS/dashboard.css">
  <!-- ===== DATA TABLES CDN ===== -->
  <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.min.css">
  <!-- ========== Bootstrap 5.3 CSS  ========== -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .movies-db {
      background-color: var(--dashboard-primary);
    }

    .sidebar-collapse-movies:nth-child(1) {
      background-color: var(--dashboard-primary);
    }
  </style>
</head>

<body>

  <main class="container-lg p-0 overflow-hidden">
    <button class="btn db-bg-primary text-white ms-3 mt-3">Add a Movie</button>

    <!-- ========== SIGN UP ACCOUNTS SECTION ========== -->
    <section class="signup-accounts-section text-left p-3">
      <div class="card bg-dark">
        <div class="card-body">

          <div class="table-task-top db-text-sec">
            <div class="py-2 ps-3">
              <p class="m-0 fs-20">Action Movies</p>
              <p class="m-0 fs-14" style="transform: translateY(-2px)">List of Action Moviess</p>
            </div>
          </div>

          <div class="table-responsive mt-2">
            <table class="table table-hover display" id="table-signup-acc">
              <thead>
                <tr>
                  <th class="db-text-primary text-align-left" scope="col">#</th>
                  <th class="db-text-primary" scope="col">Email</th>
                  <th class="db-text-primary" scope="col">Password</th>
                  <th class="db-text-primary" scope="col">Date Created</th>
                  <th class="db-text-primary" scope="col">Edit</th>
                  <th class="db-text-primary" scope="col">Delete</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <th class="db-text-primary text-align-left" scope="row">1</th>
                  <td>james.macalintal@cvsu.edu.ph</td>
                  <td>jamespogi123</td>
                  <td>1/12/1234 - 12:00</td>
                  <td>
                    <button class="btn text-white p-0 border-0" data-bs-toggle="modal"
                      data-bs-target="#signupEditModal">
                      <i class="fa-solid fa-pen-to-square ps-2"></i>
                    </button>
                  </td>
                  <td>
                    <button class="btn text-white p-0 border-0" data-bs-toggle="modal"
                      data-bs-target="#signupDeleteModal">
                      <i class="fa-solid fa-delete-left text-danger ps-2"></i>
                    </button>
                  </td>
                </tr>
                <tr>
                  <th class="db-text-primary text-align-left" scope="row">2</th>
                  <td>ewankosayo@cvsu.edu.ph</td>
                  <td>ewankosayo123</td>
                  <td>1/12/1234 - 12:00</td>
                  <td>
                    <button class="btn text-white p-0 border-0" data-bs-toggle="modal"
                      data-bs-target="#signupEditModal">
                      <i class="fa-solid fa-pen-to-square ps-2"></i>
                    </button>
                  </td>
                  <td>
                    <button class="btn text-white p-0 border-0" data-bs-toggle="modal"
                      data-bs-target="#signupDeleteModal">
                      <i class="fa-solid fa-delete-left text-danger ps-2"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </section>
  </main>

  <!-- ========== EMPLOYEE MODAL  EDIT ========== -->
  <div class="modal fade" id="signupEditModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5 db-text-sec" id="staticBackdropLabel">Edit Signup Accounts</h1>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="#" method="post">
          <div class="modal-body">
            <div class="mb-3">
              <label for="signup-email" class="form-label db-text-sec">Email</label>
              <input type="text" class="form-control" id="signup-email" name="emp-name">
            </div>
            <div class="mb-3">
              <label for="signup-password" class="form-label db-text-sec">Password</label>
              <input type="text" class="form-control" id="signup-password" name="emp-section">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn db-bg-primary text-white">Save</button>
          </div>
        </form>

      </div>
    </div>
  </div>


  <!-- ========== SIGNIN ACCOUNTS MODAL DELETE ========== -->
  <div class="modal fade" id="signupDeleteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header d-flex justify-content-between">
          <h1 class="modal-title fs-5 db-text-sec" id="staticBackdropLabel">Delete Signup Accounts</h1>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="" method="post">
          <div class="modal-body">
            <h3 class="db-text-sec text-center m-0 py-4">Are you sure you want to Delete?</h3>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-danger">Delete</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!--  ========== DATA TABLES CDN  ========== -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.min.js"></script>
  >

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