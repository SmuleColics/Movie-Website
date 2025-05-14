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
    .sidebar-collapse-acc:nth-child(2) {  
      background-color: var(--dashboard-primary);
    }
  </style>
</head>

<body>
  <main class="container-lg p-0 overflow-hidden">
    <!-- ========== PAID ACCOUNTS SECTION ========== -->
    <section class="signin-accounts-section text-left p-3">
      <div class="card bg-dark">
        <div class="card-body">

          <div class="table-task-top db-text-sec">
            <div class="py-2 ps-3">
              <p class="m-0 fs-20">Paid Accounts</p>
              <p class="m-0 fs-14" style="transform: translateY(-2px)">Authorized Paid Accounts</p>
            </div>
          </div>

          <div class="table-responsive mt-2">
            <table class="table table-hover display" id="table-signin-acc">
              <thead>
                <tr class="text-align-left">
                  <th class="db-text-primary text-align-left" scope="col">#</th>
                  <th class="db-text-primary" scope="col">Email</th>
                  <th class="db-text-primary text-center" scope="col"">Cash Tendered</th>
                  <th class="db-text-primary text-start" scope="col"">Reference No.</th>
                  <th class="db-text-primary text-start" scope="col"">Date Created</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                  $select_query = mysqli_query($con, "SELECT p.payment_id, s.signup_email, p.payment_amount, p.reference_no, p.date_created FROM tbl_signup_acc AS s JOIN tbl_payment AS p ON s.signup_id = p.signup_id");

                  while ($row = mysqli_fetch_assoc($select_query)) {
                ?>
                <tr>
                  <th class="db-text-primary text-align-left" scope="row"> <?php echo $row['payment_id'] ?> </th>
                  <td class="text-left"><?php echo htmlspecialchars($row['signup_email']) ?></td>
                  <td class="text-center"><?php echo $row['payment_amount'] ?></td>
                  <td class="text-start"><?php echo htmlspecialchars( $row['reference_no']) ?></td>
                  <td class="text-start"><?php echo $row['date_created'] ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </section>
  </main>
    
  <!--  ========== DATA TABLES CDN  ========== -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.min.js"></script>

  <script>
    new DataTable('#table-signin-acc', {
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