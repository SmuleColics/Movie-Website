<?php

include '../includes/dashboard-header-sidebar.php';
include '../includes/db-connection.php';

$select = mysqli_query($con, "SELECT * FROM tbl_admin_acc WHERE admin_id = {$_SESSION['admin_id']}");
$row = mysqli_fetch_assoc($select);
$admin_email = $row['admin_email'];
$admin_mobile = $row['admin_mobile'];
$admin_fname = $row['admin_fname'];
$admin_mname = $row['admin_mname'];
$admin_lname = $row['admin_lname'];
$admin_fb = $row['admin_fb'];
$admin_password = $row['admin_password'];
$admin_date_created = $row['admin_date_created'];
?>

<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Company Profile</title>
    <!-- ========== CSS LINK ========== -->
    <link rel="stylesheet" href="../DASHBOARD-CSS/dashboard.css">
    <!-- <link rel="stylesheet" href="../DASHBOARD-CSS/for-all.css"> -->
  </head>

  <body>
    <main class="container-lg p-0 overflow-hidden">
      <section class="my-profile-section p-3">
        <div class="row g-3">

          <div class="col-lg-4 col-md-4">
            <div class="card bg-dark">
              <div class="card-body">
                <div class="mp-top-container flexbox-align flex-column">
                  <div class="admin-user-container">
                    <div class="fa-user-container db-bg-primary flexbox-align rounded-circle">
                      <i class="fa-solid fa-user db-text-sec"></i>
                    </div>
                  </div>
                  <p class="db-text-sec fs-18 mt-3 mb-0">Admin User</p>
                  <p class="db-text-secondary">Administrator</p>

                  <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-envelope db-text-sec"></i>
                    <p class="db-text-sec mb-0"><?php echo $admin_email ?></p>
                  </div>

                  <div class="d-flex align-items-center gap-2 mt-2 mb-3">
                    <i class="fa-solid fa-phone db-text-sec"></i>
                    <p class="db-text-sec mb-0"><?php echo $admin_mobile ?></p>
                  </div>

                  <div class="d-grid db-bg-primary w-100 rounded-1">
                    <center class="py-1">
                      <a href="#" class="db-text-sec text-decoration-none">  
                        <i class="fa-solid fa-pen-to-square"></i>
                        Edit Profile
                      </a>
                    </center>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-xl-8 col-md-8">
            <div class="emp-info-con db-text-sec fs-18">
              <i class="fa-solid fa-circle-info db-text-primary"></i>
              Employee Information
            </div>

            <div class="card bg-dark">
              <div class="card-body d-flex">
                <div class="d-flex flex-column mp-info">
                  <p class="db-text-sec">Employee ID</p>
                  <p class="db-text-sec">Department</p>
                  <p class="db-text-sec">Position</p>
                  <p class="db-text-sec mb-0">Date Joined</p>
                </div>

                <div class="d-flex flex-column">
                  <p class="db-text-secondary">Not Assigned</p>
                  <p class="db-text-secondary">Not Assigned</p>
                  <p class="db-text-secondary">Administrator</p>
                  <p class="db-text-secondary mb-0"><?php echo $admin_date_created ?></p>
                </div>
              </div>
            </div>

            <div class="emp-info-con db-text-sec fs-18 mt-3">
              <i class="fa-solid fa-user db-text-primary"></i>
              Personal Information
            </div>

            <div class="card bg-dark">
              <div class="card-body d-flex">
                <div class="d-flex flex-column mp-info">
                  <p class="db-text-sec">Full Name</p>
                  <p class="db-text-sec">Gender</p>
                  <p class="db-text-sec">Marital Status</p>
                  <p class="db-text-sec mb-0">Nationality</p>
                </div>

                <div class="d-flex flex-column">
                  <p class="db-text-secondary"><?php echo $admin_fname . " " . $admin_mname . " " . $admin_lname ?></p>
                  <p class="db-text-secondary">Not Assigned</p>
                  <p class="db-text-secondary">Not Assigned</p>
                  <p class="db-text-secondary mb-0">Not Assigned</p>
                </div>
              </div>
            </div>

            <div class="emp-info-con db-text-sec fs-18 mt-3">
              <i class="fa-solid fa-address-card db-text-primary"></i>
              Contact Information
            </div>

            <div class="card bg-dark">
              <div class="card-body d-flex">
                <div class="d-flex flex-column mp-info-last">
                  <p class="db-text-sec">Email</p>
                  <p class="db-text-sec">Phone</p>
                  <p class="db-text-sec mb-0">Facebook account</p>
                </div>

                <div class="d-flex flex-column">
                  <p class="db-text-secondary"><?php echo $admin_email ?></p>
                  <p class="db-text-secondary"><?php echo $admin_mobile ?></p>
                  <p class="db-text-secondary mb-0"><?php echo $admin_fb ?></p>
                </div>
              </div>
            </div>
          </div>

        </div>
      </section>
    </main>
  </body>

</html>
