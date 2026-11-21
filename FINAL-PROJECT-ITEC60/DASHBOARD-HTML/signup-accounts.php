<?php
include '../includes/dashboard-header-sidebar.php';
include '../includes/db-connection.php';

// Handle Archive (Delete) POST (for already approved accounts)
if (isset($_POST['modal-delete-button'])) {
  $delete_id = (int) $_POST['delete-id'];
  $stmt = $con->prepare("UPDATE tbl_signup_acc SET is_archived = 1, archived_at = NOW() WHERE signup_id = ?");
  $stmt->bind_param("i", $delete_id);
  if ($stmt->execute()) {
    cv_alert_redirect('Account moved to the Archive. You can restore it from the Archive page.', 'signup-accounts.php');
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Signup Accounts</title>
  <link rel="stylesheet" href="../DASHBOARD-CSS/dashboard.css?v=<?php echo CV_ASSET_VERSION; ?>">
  <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .sidebar-collapse-acc:nth-child(1) {
      background-color: var(--dashboard-primary);
    }
    .truncate-text {
      max-width: 200px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
    .bg-primary {
      background: #C9A24B;
    }
  </style>
</head>

<body>
  <main class="container-lg p-0 overflow-hidden">
    <div class="me-3 mt-3 p-0 ms-md-2 ms-0 w-auto w-md-auto d-flex justify-content-end">
      <a href="archive.php?tab=members" class="btn db-bg-primary db-text-sec" id="add-movie-btn">Archived Members</a>
    </div>
    <!-- ========== MEMBER ACCOUNTS SECTION ========== -->
    <section class="signup-accounts-section text-left p-3">
      <div class="card bg-dark">
        <div class="card-body">

          <div class="table-task-top db-text-sec">
            <div class="py-2 ps-3 pe-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
              <div>
                <p class="m-0 fs-20">Member Accounts</p>
                <p class="m-0 fs-14" style="transform: translateY(-2px)">Everyone who signed up. Membership is free, so accounts work right away.</p>
              </div>
              <button type="button" class="btn btn-sm btn-outline-light" data-print-table="#table-signup-acc" data-print-title="Member Accounts"><i class="fa-solid fa-print me-1"></i> Print</button>
            </div>
          </div>

          <div class="table-responsive mt-2">
            <table class="table table-hover display" id="table-signup-acc">
              <thead>
                <tr>
                  <th class="db-text-primary text-align-left" scope="col">#</th>
                  <th class="db-text-primary" scope="col">First Name</th>
                  <th class="db-text-primary" scope="col">Last Name</th>
                  <th class="db-text-primary" scope="col">Mobile</th>
                  <th class="db-text-primary" scope="col">Email</th>
                  <th class="db-text-primary text-start" scope="col">Date Joined</th>
                  <th class="db-text-primary" scope="col">Archive</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $result = mysqli_query($con, "
                  SELECT signup_id AS id, first_name, last_name, mobile_number, signup_email, date_created
                  FROM tbl_signup_acc
                  WHERE is_archived = 0
                  ORDER BY date_created DESC
                ");
                while ($row = mysqli_fetch_assoc($result)) {
                  $id = (int) $row['id'];
                  ?>
                  <tr>
                    <th class="db-text-primary text-align-left" scope="row"><?php echo $id ?></th>
                    <td><?php echo e($row['first_name']) ?></td>
                    <td><?php echo e($row['last_name']) ?></td>
                    <td><?php echo e($row['mobile_number']) ?></td>
                    <td><?php echo e($row['signup_email']) ?></td>
                    <td><?php echo e(date('M j, Y g:i A', strtotime($row['date_created']))) ?></td>
                    <td>
                      <button class="btn text-white p-0 border-0 delete-btn" data-delete-id="<?php echo $id ?>"
                        data-bs-toggle="modal" data-bs-target="#signupDeleteModal" title="Archive account">
                        <i class="ps-3 fa-solid fa-delete-left text-danger ps-2"></i>
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

  <!-- ========== SIGNUP ACCOUNTS MODAL DELETE (ARCHIVE) ========== -->
  <div class="modal fade" id="signupDeleteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header d-flex justify-content-between">
          <h1 class="modal-title fs-5 db-text-sec" id="staticBackdropLabel">Archive Signup Account</h1>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="" method="post">
          <input type="hidden" name="delete-id" id="delete-id">
          <div class="modal-body">
            <h3 class="db-text-sec text-center m-0 py-4">Archive this account? The member will not be able to sign in until you restore it.</h3>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" name="modal-delete-button" class="btn btn-danger">Archive</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!--  ========== DATA TABLES CDN  ========== -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.min.js"></script>

  <script>
    $(document).ready(function () {
      $('.delete-btn').on('click', function () {
        var id = $(this).data('delete-id');
        $('#delete-id').val(id);
      });
    });
  </script>
  <script>
    new DataTable('#table-signup-acc', {
      pagingType: 'simple_numbers',
      responsive: true,
      // Opened from a notification? Show just that member.
      search: { search: <?php echo json_encode((string) ($_GET['search'] ?? ''), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?> },
      language: {
        search: '_INPUT_',
        searchPlaceholder: 'Search...'
      }
    });
  </script>
</body>

</html>