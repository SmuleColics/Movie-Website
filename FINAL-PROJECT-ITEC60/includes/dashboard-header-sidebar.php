<?php
session_start();
include 'db-connection.php';

if (isset($_GET['logout'])) {
  session_unset();
  session_destroy();
  header("Location: admin-signin.php");
  exit;
}


$notif_sql = "SELECT s.signup_id, s.signup_email, p.date_created FROM tbl_signup_acc AS s JOIN tbl_payment AS p ON s.signup_id = p.signup_id ORDER BY signup_id DESC LIMIT 5";
$notif_result = $con->query($notif_sql);
$notif_count_sql = "SELECT COUNT(*) as cnt FROM tbl_signup_acc WHERE is_archived=0"; // Only count non-archived signups
$notif_count = $con->query($notif_count_sql)->fetch_assoc()['cnt'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CineVault Admin</title>
  <!-- ========== CSS LINK ========== -->
  <link rel="stylesheet" href="../DASHBOARD-CSS/dashboard-header-sidebar.css" />
  <link rel="stylesheet" href="../DASHBOARD-CSS/for-all.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
  <link rel="icon" href="../MOVIE-IMG/HEADER-IMG/CINEVAULT-LOGO.svg">
  <style>
    #search-results {
      position: absolute;
      left: 0;
      right: 0;
      background: #222;
      color: #fff;
      z-index: 9999;
      border-radius: 0 0 4px 4px;
      max-height: 220px;
      overflow-y: auto;
      min-width: 220px;
    }
    #search-results div {
      padding: 6px 12px;
      cursor: pointer;
    }
    #search-results div:hover {
      background: #607ebc;
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-dark fixed-top" style="background-color: #181818; z-index: 1050;">
    <div class="container-fluid">
      <header class="d-flex align-items-center justify-content-between px-3 w-100">

        <div class="left-header d-flex align-items-center">
          <button class="navbar-toggler d-lg-none me-2" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#dashboard-offcanvas" aria-controls="offcanvasDarkNavbar" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <a href="#">
            <img class="me-2" src="../MOVIE-IMG/HEADER-IMG/CINEVAULT-LOGO.svg" alt="CineVault Logo"
              style="width: 36px;">
          </a>
          <a class="navbar-brand fw-semibold" href="#">
            <span class="d-none d-sm-inline-block">
              Cine<span class="db-text-primary">Vault</span>
            </span>
            Admin
          </a>
        </div>

        <div class="right-header text-light d-flex align-items-center gap-1 gap-md-2">
          <!-- ========== SEARCH ========== -->
          <div class="search-container position-relative" style="min-width:220px;">
            <label id="search-icon" for="dashboard-search">
              <i class="fas fa-search"></i>
            </label>
            <input id="dashboard-search" type="text" class="form-control bg-transparent border-color text-light"
              placeholder="Search...">
            <div id="search-results" style="display:none;"></div>
          </div>

          <!-- ========== NOTIFICATIONS ========== -->
          <div class="d-flex dropdown-center">
            <div id="bell-container">
              <button class="btn border-0 p-0 flexbox-align" role="button" data-bs-toggle="dropdown"
                aria-expanded="false">
                <div id="button-bell" class="position-relative flexbox-align" data-bs-toggle="tooltip"
                  data-bs-placement="bottom" data-bs-title="Notifications"
                  onmouseover="this.style.backgroundColor='#607EBC'; this.style.color='white';"
                  onmouseout="this.style.backgroundColor='transparent'; this.style.color='black';">
                  <i class="fa-solid fa-bell fs-5 text-light"></i>
                  <span class="badge bg-danger rounded-pill badge-bell position-absolute">
                    <?php echo $notif_count; ?>
                  </span>
                </div>
              </button>

              <ul class="dropdown-menu dropdown-menu-dark mt-1 notif-dropdown" style="transform: translateX(-154px);">
                <p class="fs-5 ps-3 mb-2 notif-text">New Signups</p>
                <?php if ($notif_result->num_rows > 0): ?>
                  <?php while($row = $notif_result->fetch_assoc()): ?>
                    <li>
                      <a class="dropdown-item" href="signup-accounts.php">
                        <?php echo htmlspecialchars($row['signup_email']); ?>
                        <span class="text-secondary small">(ID: <?php echo $row['signup_id']; ?>)</span>
                      </a>
                    </li>
                  <?php endwhile; ?>
                <?php else: ?>
                  <li><a class="dropdown-item" href="#">No new signups</a></li>
                <?php endif; ?>
              </ul>
            </div>
          </div>

          <!-- ========== PROFILE MENU ========== -->
          <div class="dropdown-center">
            <button class="btn p-0 border-0" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <div class="header-user bg-secondary rounded-circle flexbox-align ms-1" style="padding: 11px">
                <i class="fa-solid fa-user db-text-sec fs-18"></i>
              </div>
            </button>
            <ul class="dropdown-menu dropdown-menu-dark mt-1 profile-dropdown" style="transform: translateX(-130px);">
              <li class="dropdown-profile-top d-flex mb-1">
                <a class="dropdown-item d-flex align-items-center" href="../DASHBOARD-HTML/my-profile.php">
                  <div class="bg-secondary rounded-circle flexbox-align ms-1"
                    style="padding: 9px; transform: translateX(-9px);">
                    <i class="fa-solid fa-user db-text-sec fs-18"></i>
                  </div>
                  <div class="dropdown-profile-text" style="margin-left: -4px;">
                    <p class="fs-18 view-profile-text">View Profile</p>
                    <p class="m-0 fs-14 db-text-secondary">Administrator</p>
                  </div>
                </a>
              </li>
              <li class="mb-1">
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <i class="fa-solid fa-question ms-1 me-2 fs-22"></i>
                  <span class="fs-18 d-inline-block ms-1">Help & Support</span>
                </a>
              </li>
              <li class="mb-1">
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <i class="fa-solid fa-gear me-2 fs-22 "></i>
                  <span class="fs-18 d-inline-block">Settings</span>
                </a>
              </li>
              <li class="mb-1">
                <a class="dropdown-item d-flex align-items-center" href="admin-signin.php?logout=true">
                  <i class="fa-solid fa-right-from-bracket me-2 fs-22"></i>
                  <span class="fs-18 d-inline-block">Log out</span>
                </a>
              </li>
            </ul>
          </div>

        </div>

      </header>

      <div class="offcanvas offcanvas-start text-bg-dark" tabindex="-1" id="dashboard-offcanvas"
        aria-labelledby="offcanvasDarkNavbarLabel" style="background-color: #181818">
        <div class="offcanvas-header" style="background-color: #181818">
          <button class="navbar-toggler d-lg-none me-2" type="button" data-bs-dismiss="offcanvas">
            <span class="navbar-toggler-icon"></span>
          </button>
          <a href="#">
            <img class="me-2" src="../MOVIE-IMG/HEADER-IMG/CINEVAULT-LOGO.svg" alt="CineVault Logo"
              style="width: 36px;">
          </a>
          <a class="navbar-brand fw-semibold" href="#">
            <span>
              Cine<span class="db-text-primary">Vault</span>
            </span>
            Admin
          </a>
        </div>
        <div class="offcanvas-body" style="background-color: #181818">
          <ul class="offcanvas-content list-unstyled fs-20">
            <li class="sidebar-content-item d-flex align-items-center mb-1">
              <a href="../DASHBOARD-HTML/dashboard.php" class="sidebar-anchor">
                <span class="aside-icon material-symbols-outlined text-center">
                  dashboard
                </span>
                <span class="sidebar-text ms-2">Dashboard</span>
              </a>
            </li>
            <li class="sidebar-content-item d-flex align-items-center mb-1">
              <a href="../DASHBOARD-HTML/web-movies.php" class="sidebar-anchor">
                <span class="material-symbols-outlined">
                  movie
                </span>
                <span class="sidebar-text" style="margin-left: 12px">Movies</span>
              </a>
            </li>
            <li class="sidebar-content-item sidebar-collapse d-flex align-items-center justify-content-between mb-1 movies-db"
              aria-expanded="true">
              <a href="../DASHBOARD-HTML/web-genre.php" class="sidebar-anchor">
                <div class="d-flex align-items-center">
                  <span class="aside-icon material-symbols-outlined">
                    category
                  </span>
                  <span class="sidebar-text ms-2">Genre</span>
                </div>
              </a>
            </li>
            <li class="sidebar-content-item sidebar-collapse d-flex align-items-cente justify-content-between mb-1"
              aria-expanded="true">
              <a href="../DASHBOARD-HTML/signup-accounts.php" class="sidebar-anchor">
                <div class="d-flex align-items-center">
                  <span class="aside-icon material-symbols-outlined">
                    manage_accounts
                  </span>
                  <span class="sidebar-text ms-2">Manage Accounts</span>
                </div>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </nav>
  <!-- ========== END OF HEADER ========== -->

  <!-- ========== START OF SIDEBAR ========== -->
  <aside class="sidebar" style="background-color: #181818;">
    <div class="sidebar-container text-light">
      <ul class="sidebar-content list-unstyled fs-20">
        <li class="sidebar-content-item d-flex align-items-center mb-1">
          <a href="../DASHBOARD-HTML/dashboard.php" class="sidebar-anchor">
            <span class="aside-icon material-symbols-outlined text-center">
              dashboard
            </span>
            <span class="sidebar-text ms-2">Dashboard</span>
          </a>
        </li>
        <li class="sidebar-content-item d-flex align-items-center mb-1">
          <a href="../DASHBOARD-HTML/web-movies.php" class="sidebar-anchor">
            <span class="material-symbols-outlined">
              movie
            </span>
            <span class="sidebar-text" style="margin-left: 12px">Movies</span>
          </a>
        </li>
        <li class="sidebar-content-item sidebar-collapse d-flex align-items-cente justify-content-between mb-1 movies-db"
          aria-expanded="true">
          <a href="../DASHBOARD-HTML/web-genre.php" class="sidebar-anchor">
            <div class="d-flex align-items-center">
              <span class="aside-icon material-symbols-outlined">
                category
              </span>
              <span class="sidebar-text ms-2">Genre</span>
            </div>
          </a>
        </li>
        <li class="sidebar-content-item sidebar-collapse d-flex align-items-cente justify-content-between mb-1">
          <a href="../DASHBOARD-HTML/signup-accounts.php" class="sidebar-anchor">
            <div class="d-flex align-items-center">
              <span class="aside-icon material-symbols-outlined">
                manage_accounts
              </span>
              <span class="sidebar-text ms-2">Manage Accounts</span>
            </div>
          </a>
        </li>
      </ul>
    </div>
  </aside>
  <!-- ========== END OF SIDEBAR ========== -->

</body>

<!-- ========== SCRIPTS ========== -->
<script>
  const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
  tooltipTriggerList.forEach((el) => new bootstrap.Tooltip(el));

  // SIDEBAR AUTOHIDE ON LARGE SCREEN
  let sidebar = document.querySelector(".sidebar");
  let sidebarText = document.querySelectorAll(".sidebar-text");
  let asideIcon = document.querySelectorAll(".aside-icon");

  window.addEventListener("load", function () {
    const offcanvasElement = document.getElementById("dashboard-offcanvas");
    let offcanvasInstance = bootstrap.Offcanvas.getOrCreateInstance(offcanvasElement);

    function toggleOffcanvas() {
      if (window.innerWidth >= 992) {
        if (offcanvasElement.classList.contains("show")) {
          offcanvasInstance.hide();
        }
      }
    }
    toggleOffcanvas();
    window.addEventListener("resize", function () {
      toggleOffcanvas();
    });
  });

document.getElementById('dashboard-search').addEventListener('input', function() {
  const q = this.value.trim();
  const resultsBox = document.getElementById('search-results');
  if (q.length < 2) {
    resultsBox.style.display = 'none';
    resultsBox.innerHTML = '';
    return;
  }
  fetch('search.php?q=' + encodeURIComponent(q))
    .then(res => res.json())
    .then(results => {
      if (results.length === 0) {
        resultsBox.innerHTML = '<div>No results</div>';
      } else {
        resultsBox.innerHTML = results.map(r =>
          `<div><a href="${r.url}" style="color:inherit; text-decoration:none;">${r.value}</a></div>`
        ).join('');
      }
      resultsBox.style.display = 'block';
    });
});

  document.addEventListener('click', function(e) {
    if (!e.target.closest('.search-container')) {
      document.getElementById('search-results').style.display = 'none';
    }
  });
</script>
</html>