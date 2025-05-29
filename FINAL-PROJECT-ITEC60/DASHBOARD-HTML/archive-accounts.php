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
    .sidebar-content-item:nth-child(4) {
      background-color: var(--dashboard-primary);
    }

    .sidebar-collapse-acc:nth-child(1) {
      background-color: var(--dashboard-primary);
    }

    .truncate-text {
      max-width: 200px;
      /* Adjust as needed */
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
    <div>
        <a href="signup-accounts.php" class="btn db-bg-primary text-white ms-3 mt-3">
            <i class="fa-solid fa-chevron-left"></i> Go Back
        </a>
    </div>
    <!-- ========== SIGN UP ACCOUNTS SECTION ========== -->
    <section class="signup-accounts-section text-left p-3">
      <div class="card bg-dark">
        <div class="card-body">

          <div class="table-task-top db-text-sec">
            <div class="py-2 ps-3">
              <p class="m-0 fs-20">Archive Accounts</p>
              <p class="m-0 fs-14" style="transform: translateY(-2px)">Archive Sign-up Accounts</p>
            </div>
          </div>

          <div class="table-responsive mt-2">
            <table class="table table-hover display" id="table-signup-acc">
              <thead>
                <tr>
                  <th class="db-text-primary text-align-left" scope="col">#</th>
                  <th class="db-text-primary" scope="col">Email</th>
                  <th class="db-text-primary" scope="col">Cash Tendered</th>
                  <th class="db-text-primary" scope="col">Reference No.</th>
                  <th class="db-text-primary" scope="col">Date Created</th>
                  <th class="db-text-primary" scope="col">Restore</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $result = mysqli_query($con, "SELECT s.signup_id, s.signup_email, p.payment_amount, p.reference_no, p.date_created FROM tbl_signup_acc AS s JOIN tbl_payment AS p ON s.signup_id = p.signup_id WHERE s.is_archived=1");
                while ($row = mysqli_fetch_assoc($result)) {
                  $signup_id = $row['signup_id'];
                  $signup_email = $row['signup_email'];
                  ?>
                  <tr>
                    <th class="db-text-primary text-align-left" scope="row"><?php echo $signup_id ?></th>
                    <td> <?php echo htmlspecialchars($signup_email) ?> </td>
                    <td class="text-center"> <?php echo $row['payment_amount'] ?> </td>
                    <td class="text-center"> <?php echo htmlspecialchars($row['reference_no']) ?> </td>
                    <td> <?php echo $row['date_created'] ?> </td>
                    <td>
                      <button class="btn text-white p-0 border-0 restore-btn" data-restore-id="<?php echo $signup_id ?>"
                        data-bs-toggle="modal" data-bs-target="#signupRestoreModal">
                        <i class="ps-4 fa-solid fa-rotate-left text-success ps-2"></i>
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

  <!-- ========== SIGNUP ACCOUNTS MODAL RESTORE ========== -->
  <div class="modal fade" id="signupRestoreModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header d-flex justify-content-between">
          <h1 class="modal-title fs-5 db-text-sec" id="staticBackdropLabel">Restore Signup Account</h1>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <?php
        if (isset($_POST['modal-restore-button'])) {
          $restore_id = (int) $_POST['restore-id'];
          $restore_query = mysqli_query($con, "UPDATE tbl_signup_acc SET is_archived=0 WHERE signup_id=$restore_id");
          if ($restore_query) {
            echo "<script>
                  alert('Account restored successfully.');
                  window.location.href = 'archive-accounts.php';
                </script>";
          }
        }
        ?>
        <form action="" method="post">
          <input type="hidden" name="restore-id" id="restore-id">
          <div class="modal-body">
            <h3 class="db-text-sec text-center m-0 py-4">Are you sure you want to Restore this account?</h3>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" name="modal-restore-button" class="btn btn-success">Restore</button>
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
      $('.restore-btn').on('click', function () {
        var id = $(this).data('restore-id');
        $('#restore-id').val(id);
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