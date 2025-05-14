<?php
include '../includes/dashboard-header-sidebar.php';
include '../includes/db-connection.php';
?>

  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Company Profile</title>
    <!-- ========== CSS LINK ========== -->
    <link rel="stylesheet" href="../DASHBOARD-CSS/dashboard.css">
    <!-- ===== DATA TABLES CDN ===== -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.min.css">
    <!-- ========== Bootstrap 5.3 CSS  ========== -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
      .sidebar-content-item:nth-child(2) {
        background-color: var(--dashboard-primary);
      }
      .sidebar-collapse-acc:nth-child(1) {
        background-color: var(--dashboard-primary);
      }
      .truncate-text {
        max-width: 200px; /* Adjust as needed */
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
      }
      .bg-primary {
        background: #607BEC;
      }
    </style>
  </head>

  <body>
    <main class="container-lg p-0 overflow-hidden">
      <!-- ========== SIGN UP ACCOUNTS SECTION ========== -->
      <section class="signup-accounts-section text-left p-3">
        <div class="card bg-dark">
          <div class="card-body">

            <div class="table-task-top db-text-sec">
              <div class="py-2 ps-3">
                <p class="m-0 fs-20">Signup Accounts</p>
                <p class="m-0 fs-14" style="transform: translateY(-2px)">Authorized Sign-up Accounts</p>
              </div>
            </div>

            <div class="table-responsive mt-2">
              <table class="table table-hover display" id="table-signup-acc">
                <thead>
                  <tr>
                    <th class="db-text-primary text-align-left" scope="col">#</th>
                    <th class="db-text-primary" scope="col">Email</th>
                    <th class="db-text-primary" scope="col">Password</th>
                    <th class="db-text-primary" scope="col">Edit</th>
                    <th class="db-text-primary" scope="col">Delete</th>
                  </tr>
                </thead>
                <tbody>

                  <?php 

                    $result = mysqli_query($con, "SELECT * FROM tbl_signup_acc");

                    while ($row = mysqli_fetch_assoc($result)) {
                      $signup_id = $row['signup_id'];
                      $signup_email = $row['signup_email'];
                      $signup_password = $row['signup_password'];
                  ?>

                  <tr>   

                    <th class="db-text-primary text-align-left" scope="row"><?php echo $signup_id ?></th>
                    <td> <?php echo htmlspecialchars($signup_email) ?> </td>
                    <td class="truncate-text"><?php echo htmlspecialchars($signup_password) ?></td>
                    <td>
                        <button class="btn text-white p-0 border-0 edit-btn" data-bs-toggle="modal" data-bs-target="#signupEditModal" data-id="<?php echo $signup_id ?>" 
                        data-email="<?php echo $signup_email ?>"
                        data-password="<?php echo $signup_password ?>">
                          <i class="fa-solid fa-pen-to-square ps-2"></i> 
                        </button>
                      </td> 
                      <td>
                        <button class="btn text-white p-0 border-0 delete-btn" 
                        data-delete-id="<?php echo $signup_id ?>"
                        data-bs-toggle="modal" data-bs-target="#signupDeleteModal">
                          <i class="fa-solid fa-delete-left text-danger ps-2"></i>
                        </button>
                      </td>
                  </tr>

                  <?php } ?>
                </tbody>
              </table>
            </div>

          </div>
        </div>
      </section>
    </main>

    <!-- ========== SIGNUP MODAL  EDIT ========== -->
  <div class="modal fade" id="signupEditModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5 db-text-sec" id="staticBackdropLabel">Edit Signup Accounts</h1>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="#" method="post">
          
          <?php 
            $modal_email_error = "";
            $modal_pword_error = "";
            $valid_modal_email = true;
            $valid_modal_password = true;

            if (isset($_POST['modal-edit-btn'])) {
              $id = $_POST['update-id'];
              $email = filter_input(INPUT_POST, "update-email", FILTER_SANITIZE_EMAIL);
              $modal_email = mysqli_real_escape_string($con, $email);
              $modal_pass = filter_input(INPUT_POST, "update-password", FILTER_SANITIZE_EMAIL);

              $check_query = mysqli_query($con, "SELECT signup_email FROM tbl_signup_acc WHERE signup_email = '$modal_email' AND signup_id != $id LIMIT 1");;

              if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $modal_email_error = "Invalid email format";
                $valid_modal_email = false;
              } elseif (mysqli_num_rows($check_query) > 0) {
                $modal_email_error = "This email is already registered. Please use a different one.";
                $valid_modal_email = false; 
              }

              if(empty($modal_pass)) {

                if ($valid_modal_email) {
                  $update = mysqli_query($con, "UPDATE tbl_signup_acc SET signup_email = '$modal_email' WHERE signup_id = $id");

                  echo "<script>
                    alert('Successfully updated the account');
                    window.location.href = 'signup-accounts.php';
                  </script>";
                }
              
              } else {

                if (strlen($modal_pass) < 8) {
                  $modal_pword_error = "Password must be 8 characters long";
                  $valid_modal_password = false;
                } 

                if ($valid_modal_email && $valid_modal_password) {
                  $hash_pass = password_hash($modal_pass, PASSWORD_DEFAULT);

                  $update = mysqli_query($con, "UPDATE tbl_signup_acc SET signup_email = '$modal_email', signup_password = '$hash_pass' WHERE signup_id = $id");

                  echo "<script>
                    alert('Successfully updated the account');
                    window.location.href = 'signup-accounts.php';
                  </script>";
                }
              }
                
            }
            

          ?>

          <div class="modal-body">
            <input type="hidden" name="update-id" id="update-id">
            <div class="mb-3">
              <label for="signup-email" class="form-label db-text-sec">Email</label>
              <input type="text" class="form-control" id="update-email" name="update-email"
              value="<?php echo isset($_POST['update-email']) ? htmlspecialchars($_POST['update-email']) : ''; ?>"
              <?php if(!$valid_modal_email) echo "style='border-color: red;'"; ?> required>
              <?php if(!$valid_modal_email): ?>
                <p class="mb-2 mt-1 text-danger text-start" style="font-size: 14px;"><?php echo $modal_email_error ?></p>
              <?php endif; ?>
            </div>
            <div class="mb-3">
              <label for="signup-password" class="form-label db-text-sec">Password</label>
              <input type="text" class="form-control" id="update-password" name="update-password" <?php if(!$valid_modal_password) echo "style='border-color: red;'" ?>>
              <?php if(!$valid_modal_password): ?>
                <p class="mb-2 mt-1 text-danger text-start" style="font-size: 14px;"><?php echo $modal_pword_error ?></p>
              <?php endif; ?>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            <button type="submit" id="modal-edit-btn" name="modal-edit-btn" class="btn btn-primary">Save Changes</button>
          </div>
        </form>
        
      </div>
    </div>
  </div>


    <!-- ========== SIGNUP ACCOUNTS MODAL DELETE ========== -->
    <div class="modal fade" id="signupDeleteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
      aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header d-flex justify-content-between">
            <h1 class="modal-title fs-5 db-text-sec" id="staticBackdropLabel">Delete Signup Accounts</h1>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <?php 
            if (isset($_POST['modal-delete-button'])) {
              $delete_id = (int)$_POST['delete-id'];

              mysqli_query($con, "DELETE FROM tbl_payment WHERE signup_id = $delete_id");

              $delete_query = mysqli_query($con,  "DELETE FROM tbl_signup_acc WHERE signup_id = $delete_id");

              if ($delete_query) {
                echo "<script>
                  alert('Successfully deleted the account');
                  window.location.href = 'signup-accounts.php';
                </script>";
              }
            }
          ?>
          <form action="" method="post">
            <input type="hidden" name="delete-id" id="delete-id">
            <div class="modal-body">
              <h3 class="db-text-sec text-center m-0 py-4">Are you sure you want to Delete?</h3>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" name="modal-delete-button" class="btn btn-danger">Delete</button>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
      $(document).ready(function () {
        $('.delete-btn').on('click', function () {
          var id = $(this).data('delete-id');

          $('#delete-id').val(id);
        });
      });
    </script>
    <script>
      $(document).ready(function () {
        $('.edit-btn').on('click', function () {
          var id = $(this).data('id');
          var email = $(this).data('email');

          $('#update-id').val(id);
          $('#update-email').val(email);
          $('#update-password').val('');
        });
      });
    </script>
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