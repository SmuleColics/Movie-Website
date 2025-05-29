<?php
session_start();

// if (isset($_SESSION['admin_id'])) {
//   echo "<script>alert('{$_SESSION['admin_id']}')</script>";
// } else {
//     echo "<script>alert('id is not set')</script>";
// }

if (isset($_GET['logout'])) {
  session_unset();     // remove all session variables
  session_destroy();   // destroy the session
  header("Location: admin-signin.php"); // redirect to login page (or homepage)
  exit;
}

$hrefs = ["../DASHBOARD-HTML/genre-action.php"];

$genres = ["Action", "Adventure", "Animation", "Comedy", "Crime", "Documentary", "Drama", "Family", "Fantasy", "History", "Terror", "Music", "Mystery", "Science fiction", "Cinema TV", "Thriller", "War", "Western", "Kids", "News", "Reality", "Romance", "Sci-Fi & Fantasy", "Soap", "Talk", "War & Politics"];
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
  <!-- ========== FONTAWESOME LINK ========== -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <!-- ========== BOOTSTRAP LINK ========== -->
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" />

  <!-- Bootstrap Bundle JS (with Popper) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
  <!-- ========== GOOGLE FONT LINK ========== -->
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
  <!-- ========== ICON FOR PAGE LINK ========== -->
  <link rel="icon" href="../MOVIE-IMG/HEADER-IMG/CINEVAULT-LOGO.svg">
</head>

<body>
  <!-- ========== START OF HEADER ========== -->
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
          <div class="search-container position-relative">
            <label id="search-icon" for="dashboard-search">
              <i class="fas fa-search"></i>
            </label>
            <input id="dashboard-search" type="text" class="form-control bg-transparent border-color text-light"
              placeholder="Search">
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
                  <span class="badge bg-danger rounded-pill badge-bell position-absolute">3</span>
                </div>
              </button>

              <ul class="dropdown-menu dropdown-menu-dark mt-1 notif-dropdown" style="transform: translateX(-154px);">
                <p class="fs-5 ps-3 mb-2 notif-text">Notifications</p>
                <li><a class="dropdown-item" href="#">Blanco responded to your email</a></li>
                <li><a class="dropdown-item" href="#">You have 5 new tasks</a></li>
                <li><a class="dropdown-item" href="#">Meeting schedule at 5 p.m. </a></li>
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
              <a href="../DASHBOARD-HTML/web-home.php" class="sidebar-anchor">
                <span class="material-symbols-outlined">
                  movie
                </span>
                <span class="sidebar-text" style="margin-left: 12px">Movies</span>
              </a>
            </li>

            <li
              class="sidebar-content-item sidebar-collapse d-flex align-items-center justify-content-between mb-1 movies-db"
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
  <!-- ========== START OF HEADER ========== -->

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
          <a href="../DASHBOARD-HTML/web-home.php" class="sidebar-anchor">
            <span class="material-symbols-outlined">
              movie
            </span>
            <span class="sidebar-text" style="margin-left: 12px">Movies</span>
          </a>
        </li>

        <li
          class="sidebar-content-item sidebar-collapse d-flex align-items-cente justify-content-between mb-1 movies-db"
          aria-expanded="true">
          <a href="../DASHBOARD-HTML/web-genre.php" class="sidebar-anchor">
            <div class="d-flex align-items-center">
              <span class="aside-icon material-symbols-outlined">
                category
              </span>
              <span class="sidebar-text ms-2">Genre</span>
            </div>
          </a </li>


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

<script>  ;

  const tooltipTriggerList = document.querySelectorAll(
    '[data-bs-toggle="tooltip"]'
  );
  tooltipTriggerList.forEach((el) => new bootstrap.Tooltip(el));

  /*  ========== SIDEBAR MINIMIZE FUNCTION ========== */
  let sidebar = document.querySelector(".sidebar");
  let sidebarText = document.querySelectorAll(".sidebar-text");
  let asideIcon = document.querySelectorAll(".aside-icon");

  /*  ========== OFFCANVAS AUTO HIDE ON LARGER SCREENS ========== */
  window.addEventListener("load", function () {
    /*  ========== BOOTSTRAP COLLAPSE ========== */
    const collapseElementList = document.querySelectorAll('.collapse');
    const collapseList = [...collapseElementList].map(el => new bootstrap.Collapse(el, {
      toggle: false // Optional: prevents it from auto-toggling
    }));

    const offcanvasElement = document.getElementById("dashboard-offcanvas");
    let offcanvasInstance =
      bootstrap.Offcanvas.getOrCreateInstance(offcanvasElement);

    function toggleOffcanvas() {
      if (window.innerWidth >= 992) {
        // Auto-hide the offcanvas if screen is larger than or equal to 768px
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

</script>

</html>