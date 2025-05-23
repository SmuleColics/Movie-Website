<?php

include '../includes/dashboard-header-sidebar.php';
include '../includes/db-connection.php';

// Validation variables (initialize as valid)
$email_error = $mobile_error = $fname_error = $mname_error = $lname_error = $fb_error = "";
$employee_id_error = $department_error = $gender_error = $marital_error = $nationality_error = "";
$valid_email = $valid_mobile = $valid_fname = $valid_mname = $valid_lname = $valid_fb = true;
$valid_employee_id = $valid_department = $valid_gender = $valid_marital = $valid_nationality = true;

$name_error = ""; // combined error message

$select = mysqli_query($con, "SELECT * FROM tbl_admin_acc WHERE admin_id = {$_SESSION['admin_id']}");
$row = mysqli_fetch_assoc($select);
$admin_email = $row['email'];
$admin_mobile = $row['mobile'];
$admin_fname = $row['first_name'];
$admin_mname = $row['middle_name'];
$admin_lname = $row['last_name'];
$admin_fb = $row['facebook_acc'];
$admin_password = $row['password'];
$admin_date_created = $row['date_created'];
$employee_id = $row['employee_id'];
$department = $row['department'];
$gender = $row['gender'];
$marital_status = $row['marital_status'];
$nationality = $row['nationality'];

// Optionals: default to empty, will be used for repopulation
// $employee_id = $department = $gender = $marital_status = $nationality = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save-changes'])) {
  // Sanitize and assign
  $admin_email = filter_input(INPUT_POST, "admin_email", FILTER_SANITIZE_EMAIL);
  $admin_mobile = trim($_POST['admin_mobile']);
  $admin_fname = trim($_POST['admin_fname']);
  $admin_mname = trim($_POST['admin_mname']);
  $admin_lname = trim($_POST['admin_lname']);
  $admin_fb = trim($_POST['admin_fb']);

  $employee_id = trim($_POST['employee-id']);
  $department = trim($_POST['department']);
  $gender = trim($_POST['gender']);
  $marital_status = trim($_POST['marital-status']);
  $nationality = trim($_POST['nationality']);

  // Email
  if (!filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
    $email_error = "Invalid email format";
    $valid_email = false;
  }

  // Mobile
  if (!preg_match('/^[0-9]{11}$/', $admin_mobile)) {
    $mobile_error = "Mobile number must be exactly 11 digits";
    $valid_mobile = false;
  }

  // Check First Name
  if (strlen($admin_fname) < 2) {
    $valid_fname = false;
    $name_error .= "First";
  }

  // Check Middle Name
  if (strlen($admin_mname) < 2) {
    $valid_mname = false;
    $name_error .= ($name_error ? ", " : "") . "Middle";
  }

  // Check Last Name
  if (strlen($admin_lname) < 2) {
    $valid_lname = false;
    $name_error .= ($name_error ? ", " : "") . "Last";
  }

  // Final message if there's an error
  if ($name_error) {
    $name_error .= " name" . (strpos($name_error, ",") !== false ? "s" : "") . " must be at least 2 characters.";
  }

  // Facebook
  if (strlen($admin_fb) < 2) {
    $fb_error = "Invalid Facebook account";
    $valid_fb = false;
  }

  // Optional fields: Validate only if not empty
  if (!empty($employee_id) && strlen($employee_id) < 2) {
    $employee_id_error = "Employee ID must be at least 2 characters";
    $valid_employee_id = false;
  }
  if (!empty($department) && strlen($department) < 2) {
    $department_error = "Department must be at least 2 characters";
    $valid_department = false;
  }
  if (!empty($gender) && strlen($gender) < 2) {
    $gender_error = "Gender must be at least 2 characters";
    $valid_gender = false;
  }
  if (!empty($marital_status) && strlen($marital_status) < 2) {
    $marital_error = "Marital status must be at least 2 characters";
    $valid_marital = false;
  }
  if (!empty($nationality) && strlen($nationality) < 2) {
    $nationality_error = "Nationality must be at least 2 characters";
    $valid_nationality = false;
  }

  // If all valid, update the database
  if (
    $valid_email && $valid_mobile && $valid_fname && $valid_mname && $valid_lname && $valid_fb &&
    $valid_employee_id && $valid_department && $valid_gender && $valid_marital && $valid_nationality
  ) {
    $admin_id = $_SESSION['admin_id'];
    $admin_email_esc = mysqli_real_escape_string($con, $admin_email);
    $admin_mobile_esc = mysqli_real_escape_string($con, $admin_mobile);
    $admin_fname_esc = mysqli_real_escape_string($con, $admin_fname);
    $admin_mname_esc = mysqli_real_escape_string($con, $admin_mname);
    $admin_lname_esc = mysqli_real_escape_string($con, $admin_lname);
    $admin_fb_esc = mysqli_real_escape_string($con, $admin_fb);

    $employee_id_esc = mysqli_real_escape_string($con, $employee_id);
    $department_esc = mysqli_real_escape_string($con, $department);
    $gender_esc = mysqli_real_escape_string($con, $gender);
    $marital_status_esc = mysqli_real_escape_string($con, $marital_status);
    $nationality_esc = mysqli_real_escape_string($con, $nationality);

    if ($employee_id === "") {
      $employee_id_esc = "NULL";
    } else {
      $employee_id_esc = "'" . mysqli_real_escape_string($con, $employee_id) . "'";
    }

    try {
      $update = mysqli_query($con, "UPDATE tbl_admin_acc SET 
            employee_id = $employee_id_esc,
            department = '$department_esc',
            email = '$admin_email_esc',
            mobile = '$admin_mobile_esc',
            first_name = '$admin_fname_esc',
            middle_name = '$admin_mname_esc',
            last_name = '$admin_lname_esc',
            facebook_acc = '$admin_fb_esc',
            gender = '$gender_esc',
            marital_status = '$marital_status_esc',
            nationality = '$nationality_esc'
          WHERE admin_id = '$admin_id'
        ");
      echo "<script>
            alert('Profile updated successfully!');
            window.location.href = 'my-profile.php';
        </script>";
      exit;
    } catch (mysqli_sql_exception $ex) {
      if ($ex->getCode() === 1062) {
        // Duplicate entry error
        $employee_id_error = "Employee ID is already used by another account.";
        $valid_employee_id = false;
      } else {
        echo "<script>alert('Something went wrong while updating. Please try again.');</script>";
      }
    }
  }
}
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
  <style>
    .text-error {
      transform: translateY(-16px);
      color: #dc3545 !important;
    }

    .input-error {
      border: 1px solid red !important;
      /* margin-bottom: 8px !important; */
    }

    select {
      padding: 4px 0;

    }

    select.edit-inputs {
      background-color: #212529 !important;
      color: #f4fff8 !i mportant;
      border: 1px solid #ced4da;
      height: 28px;
    }

    /* For some browsers, you can try to style the options as well: */
    select.edit-inputs option {
      background-color: #212529;
      color: #f4fff8;
    }
  </style>
</head>

<body>
  <form method="post" action="">
    <main class="container-lg p-0 overflow-hidden">
      <section class="my-profile-section p-3">
        <div class="d-flex justify-content-between my-3">
          <a href="my-profile.php" class="btn db-bg-primary text-white"><i class="fa-solid fa-chevron-left"></i>
            Go Back</a>
          <button class="btn btn-danger" type="button" data-bs-toggle="modal"
            data-bs-target="#adminAccDeleteModal">Delete Account</button>
        </div>

        <div class="row g-3">
          <div class="col">
            <div class="emp-info-con db-text-sec fs-18">
              <i class="fa-solid fa-circle-info db-text-primary"></i>
              Employee Information
            </div>
            <div class="card bg-dark">
              <div class="card-body d-flex">
                <div class="d-flex flex-column mp-info">
                  <p class="db-text-sec text-nowrap">Employee ID</p>
                  <p class="db-text-sec">Department</p>
                  <p class="db-text-sec">Position</p>
                  <p class="db-text-sec mb-0 text-nowrap">Date Joined</p>
                </div>
                <div class="d-flex flex-column edit-profile info-1" style="margin-left: 80px;">
                  <input class="edit-inputs bg-transparent mb-3<?php if (!$valid_employee_id)
                    echo ' input-error'; ?>" type="text" placeholder="Not Assigned" name="employee-id"
                    value="<?php echo htmlspecialchars($employee_id !== null ? $employee_id : '', ENT_QUOTES); ?>">
                  <?php if (!$valid_employee_id): ?>
                    <p class="mb-0 mt-1 text-error text-start" style="font-size: 14px;"><?php echo $employee_id_error; ?>
                    </p>
                  <?php endif; ?>

                  <input class="edit-inputs bg-transparent mb-3<?php if (!$valid_department)
                    echo ' input-error'; ?>" type="text" placeholder="Not Assigned" name="department"
                    value="<?php echo htmlspecialchars($department); ?>">
                  <?php if (!$valid_department): ?>
                    <p class="mb-0 mt-1 text-error text-start" style="font-size: 14px;"><?php echo $department_error; ?>
                    </p>
                  <?php endif; ?>

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
                <div class="d-flex flex-column edit-profile info-2" style="margin-left: 80px;">
                  <div class="d-flex fullname" style="min-width: 130px;">
                    <input class="name-edit-inputs bg-transparent mb-3 me-1<?php if (!$valid_fname)
                      echo ' input-error'; ?>" type="text" placeholder="First Name" name="admin_fname"
                      value="<?php echo htmlspecialchars($admin_fname) ?>">
                    <input class="name-edit-inputs bg-transparent mb-3<?php if (!$valid_mname)
                      echo ' input-error'; ?>" type="text" placeholder="Middle Name" name="admin_mname"
                      value="<?php echo htmlspecialchars($admin_mname) ?>">
                    <input class="name-edit-inputs bg-transparent mb-3 ms-1<?php if (!$valid_lname)
                      echo ' input-error'; ?>" type="text" placeholder="Last Name" name="admin_lname"
                      value="<?php echo htmlspecialchars($admin_lname) ?>">
                  </div>
                  <?php if ($name_error): ?>
                    <p class="mb-0 mt-1 text-error text-start text-nowrap" style="font-size: 14px;">
                      <?php echo $name_error; ?>
                    </p>
                  <?php endif; ?>

                  <select class="edit-inputs bg-transparent mb-3<?php if (!$valid_gender)
                    echo ' input-error'; ?>" name="gender">
                    <option value="" <?php echo ($gender === "" || $gender === null) ? "selected" : ""; ?>>Not Assigned
                    </option>
                    <option value="Male" <?php echo ($gender === "Male") ? "selected" : ""; ?>>Male</option>
                    <option value="Female" <?php echo ($gender === "Female") ? "selected" : ""; ?>>Female</option>
                  </select>

                  <input class="edit-inputs bg-transparent mb-3<?php if (!$valid_marital)
                    echo ' input-error'; ?>" type="text" placeholder="Not Assigned" name="marital-status"
                    value="<?php echo htmlspecialchars($marital_status); ?>">
                  <?php if (!$valid_marital): ?>
                    <p class="mb-0 mt-1 text-error text-start" style="font-size: 14px;"><?php echo $marital_error; ?></p>
                  <?php endif; ?>

                  <input class="edit-inputs bg-transparent mb-3<?php if (!$valid_nationality)
                    echo ' input-error'; ?>" type="text" placeholder="Not Assigned" name="nationality"
                    value="<?php echo htmlspecialchars($nationality); ?>">
                  <?php if (!$valid_nationality): ?>
                    <p class="mb-0 mt-1 text-error text-start" style="font-size: 14px;"><?php echo $nationality_error; ?>
                    </p>
                  <?php endif; ?>
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
                <div class="d-flex flex-column edit-profile">
                  <input class="edit-inputs bg-transparent mb-3<?php if (!$valid_email)
                    echo ' input-error'; ?>" type="text" placeholder="Email" name="admin_email"
                    value="<?php echo htmlspecialchars($admin_email) ?>">
                  <?php if (!$valid_email): ?>
                    <p class="mb-0 mt-1 text-error text-start" style="font-size: 14px;"><?php echo $email_error; ?></p>
                  <?php endif; ?>
                  <input class="edit-inputs bg-transparent mb-3<?php if (!$valid_mobile)
                    echo ' input-error'; ?>" type="text" placeholder="Phone" name="admin_mobile"
                    value="<?php echo htmlspecialchars($admin_mobile) ?>">
                  <?php if (!$valid_mobile): ?>
                    <p class="mb-0 mt-1 text-error text-start" style="font-size: 14px;"><?php echo $mobile_error; ?></p>
                  <?php endif; ?>
                  <input class="edit-inputs bg-transparent mb-3<?php if (!$valid_fb)
                    echo ' input-error'; ?>" type="text" placeholder="Facebook account" name="admin_fb"
                    value="<?php echo htmlspecialchars($admin_fb) ?>">
                  <?php if (!$valid_fb): ?>
                    <p class="mb-0 mt-1 text-error text-start" style="font-size: 14px;"><?php echo $fb_error; ?></p>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
          <div class="btn-container d-flex justify-content-end">
            <input type="submit" class="btn db-bg-primary text-white" name="save-changes" value="Save Changes">
          </div>
        </div>
      </section>
    </main>
  </form>

  <!-- ========== SIGNUP ACCOUNTS MODAL DELETE ========== -->
  <div class="modal fade" id="adminAccDeleteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header d-flex justify-content-between">
          <h1 class="modal-title fs-5 db-text-sec" id="staticBackdropLabel">Delete Admin Account</h1>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <?php
        if (isset($_POST['modal-delete-button'])) {
          $delete_id = $_SESSION['admin_id'];

          $delete_query = mysqli_query($con, "DELETE FROM tbl_admin_acc WHERE admin_id = $delete_id");

          if ($delete_query) {
            echo "<script>
                  alert('Your account has been deleted. You have been automatically logged out.');
                  window.location.href = 'admin-signin.php';
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
</body>

</html>