<?php
include '../includes/dashboard-header-sidebar.php';
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
    .sidebar-collapse-acc:nth-child(3) {
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
                  <th class="db-text-primary text-align-left" scope="col"">Cash Tendered</th>
                </tr>
              </thead>
              <tbody>
                <tr">
                  <th class="db-text-primary text-align-left" scope="row">1</th>
                  <td>james.macalintal@cvsu.edu.ph</td>
                  <td class="text-align-left">100</td>
                </tr>
                <tr>
                  <th class="db-text-primary text-align-left" scope="row">2</th>
                  <td>ewankosayo@cvsu.edu.ph</td>
                  <td class="text-align-left">100</td>
                </tr>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </section>
  </main>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!--  ========== DATA TABLES CDN  ========== -->
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.min.js"></script>

  <!--  ========== Bootstrap 5.3 JS Bundle (includes Popper)  ========== -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

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