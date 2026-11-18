<?php
include '../includes/db-connection.php';

$user_id = $_SESSION['user_id'];

// "New arrivals" bell: newest titles, read/unread per member (same bell as the admin side).
$cv_arrivals = cv_member_arrivals($con, (int) $user_id);
$notif_count = 0;
foreach ($cv_arrivals as $cv_a) {
  $notif_count += $cv_a['is_read'] ? 0 : 1;
}
// Signing out happens in LANDING-PAGE/SignIn/logout.php, after the "Sign out?" popup.

// Signed-in member, for the account chip.
$cv_stmt = $con->prepare("SELECT first_name, last_name, signup_email FROM tbl_signup_acc WHERE signup_id = ?");
$cv_uid = (int) $user_id;
$cv_stmt->bind_param('i', $cv_uid);
$cv_stmt->execute();
$cv_member = $cv_stmt->get_result()->fetch_assoc() ?: [];
$cv_stmt->close();
$cv_member_first = trim(explode(' ', trim($cv_member['first_name'] ?? ''))[0]) ?: 'Member';
$cv_member_full = trim(($cv_member['first_name'] ?? '') . ' ' . ($cv_member['last_name'] ?? '')) ?: ($cv_member['signup_email'] ?? 'Member');
$cv_member_initials = strtoupper(substr($cv_member['first_name'] ?? 'M', 0, 1) . substr($cv_member['last_name'] ?? '', 0, 1)) ?: 'M';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CineVault</title>
  <link rel="icon" href="../MOVIE-IMG/HEADER-IMG/CINEVAULT-LOGO.svg">
  <!-- Font Awesome CDN (for icons) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../shared/notifications.css?v=<?php echo CV_ASSET_VERSION; ?>">
  <style>
    .middle-header a.active {
      color: #C9A24B !important;
    }
    .offcanvas-body .nav-link.active {
      color: #C9A24B !important;
    }
    .notif-badge {
      font-size: 12px;
      padding: 2px 6px;
      position: absolute;
      top: -6px;
      right: -6px;
    }
  </style>
</head>

<body style="margin-top: 61px;">
  <!-- START OF HEADER -->
  <nav id="navbar" class="navbar navbar-dark fixed-top" style="background-color: #1F1A14;">
    <div class="container-fluid">
      <header class="d-flex justify-content-between align-items-center w-100 px-md-5 bg-transparent">
        <div class="left-header">
          <a class="navbar-brand cv-brand" href="FirstProject.php">Cine<span>Vault</span></a>
        </div>

        <div class="middle-header position-relative d-none d-lg-block">
          <ul class="d-flex list-unstyled fs-18 m-0">
            <li>
              <a href="FirstProject.php" class="text-decoration-none text-white fw-bold me-2">Home</a>
            </li>
            <li>
              <a href="Movies.php" class="text-decoration-none text-white fw-bold mx-2">Movies</a>
            </li>
            <li>
              <a href="Series.php" class="text-decoration-none text-white fw-bold mx-2">Series</a>
            </li>
            <li>
              <a href="Categories.php" class="text-decoration-none text-white fw-bold ms-2">Categories</a>
            </li>
          </ul>
        </div>

        <div class="right-header d-flex">
          <div class="d-flex align-items-center gap-2 gap-md-3">
            <!-- ========== SEARCH ========== -->
            <!-- Phones: a search icon that opens the search page (the box would not fit) -->
            <a href="Website-Search.php" class="cv-icon-btn cv-search-mobile" title="Search" aria-label="Search">
              <i class="fa-solid fa-magnifying-glass"></i>
            </a>
            <form method="get" action="Website-Search.php" class="cv-search mb-0" role="search">
              <i class="fa-solid fa-magnifying-glass cv-search-icon" aria-hidden="true"></i>
              <input type="search" name="search" placeholder="Search titles or cast" aria-label="Search"
                value="<?php echo isset($_GET['search']) ? e($_GET['search']) : ''; ?>">
            </form>

            <!-- ========== NOTIFICATIONS (new arrivals; same bell as the admin side) ========== -->
            <div class="dropdown">
              <button type="button" class="cv-bell cv-bell-member" data-bs-toggle="dropdown" data-bs-display="static"
                aria-expanded="false" title="New arrivals">
                <i class="fa-solid fa-bell"></i>
                <span class="cv-bell-badge" id="cv-bell-badge" <?php echo $notif_count ? '' : 'hidden'; ?>><?php echo $notif_count > 99 ? '99+' : $notif_count; ?></span>
              </button>
              <div class="dropdown-menu dropdown-menu-end cv-notif-menu" data-notif-endpoint="member-notifications.php">
                <div class="cv-notif-header">
                  <span>New Arrivals</span>
                  <a href="#" id="cv-notif-read-all" class="cv-notif-read-all" <?php echo $notif_count ? '' : 'hidden'; ?>>Mark all as read</a>
                </div>
                <div class="cv-notif-list">
                  <?php if ($cv_arrivals): ?>
                    <?php foreach ($cv_arrivals as $a):
                      $kind = $a['category'] === 'Series' ? 'Series' : 'Movie'; ?>
                      <a href="Website-Search.php?search=<?php echo urlencode($a['title']); ?>"
                        class="cv-notif-item js-notif-item<?php echo $a['is_read'] ? '' : ' unread'; ?>"
                        data-notif-id="<?php echo (int) $a['movie_series_id']; ?>">
                        <span class="cv-notif-icon"><i class="fa-solid <?php echo $kind === 'Series' ? 'fa-tv' : 'fa-film'; ?>"></i></span>
                        <span class="cv-notif-body">
                          <span class="cv-notif-title">New <?php echo strtolower($kind); ?>: <?php echo e($a['title']); ?></span>
                          <span class="cv-notif-detail"><?php echo e($kind . ($a['date_released'] ? ' · ' . $a['date_released'] : '')); ?></span>
                          <span class="cv-notif-time">Added <?php echo e(strtolower(cv_time_ago($a['date_posted']))); ?></span>
                        </span>
                        <span class="cv-notif-dot" aria-label="Unread"></span>
                      </a>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <div class="text-center small py-4 cv-notif-empty">No new arrivals yet.</div>
                  <?php endif; ?>
                </div>
                <a href="Website-Search.php" class="cv-notif-view-all">Browse all titles</a>
              </div>
            </div>

            <!-- ========== ACCOUNT ========== -->
            <div class="dropdown">
              <button type="button" class="cv-member-chip" data-bs-toggle="dropdown" data-bs-display="static"
                aria-expanded="false" title="Signed in as <?php echo e($cv_member_first); ?>">
                <span class="cv-member-avatar"><?php echo e($cv_member_initials); ?></span>
                <span class="cv-member-name d-none d-xl-inline"><?php echo e($cv_member_first); ?></span>
                <i class="fa-solid fa-chevron-down cv-member-chevron d-none d-xl-inline"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-end cv-menu cv-member-menu">
                <li class="cv-member-head">
                  <span class="cv-member-avatar cv-member-avatar-lg"><?php echo e($cv_member_initials); ?></span>
                  <div class="cv-member-head-text">
                    <p class="cv-member-head-name"><?php echo e($cv_member_full); ?></p>
                    <p class="cv-member-head-email"><?php echo e($cv_member['signup_email'] ?? ''); ?></p>
                  </div>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <a class="dropdown-item" href="user-view-profile.php"><i class="fa-solid fa-user-pen"></i> My Profile</a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#cvLogoutModal">
                    <i class="fa-solid fa-right-from-bracket"></i> Log out
                  </a>
                </li>
              </ul>
            </div>
          </div>
          <!-- ========== END PROFILE MENU ========== -->
          <button class="navbar-toggler text-light d-lg-none ms-2" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#offcanvasDarkNavbar" aria-controls="offcanvasDarkNavbar">
            <span class="navbar-toggler-icon"></span>
          </button>
        </div>
      </header>
      <?php $cvLogoutAction = "../LANDING-PAGE/SignIn/logout.php"; include __DIR__ . "/../includes/logout-modal.php"; ?>
      <script>
        // Turn on Bootstrap tooltips (e.g. the full description on the carousel).
        document.addEventListener('DOMContentLoaded', function () {
          if (!window.bootstrap) return;
          document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
            bootstrap.Tooltip.getOrCreateInstance(el);
          });
        });

        // Top bar: solid background once the page is scrolled (see #navbar.is-scrolled in FirstProject.css).
        (function () {
          var nav = document.getElementById('navbar');
          function update() { nav.classList.toggle('is-scrolled', window.scrollY > 10); }
          update();
          window.addEventListener('scroll', update, { passive: true });
        })();
      </script>
      <script src="../shared/notifications.js?v=<?php echo CV_ASSET_VERSION; ?>"></script>
      <script src="../shared/ellipsis-tooltips.js?v=<?php echo CV_ASSET_VERSION; ?>" defer></script>

      <div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="offcanvasDarkNavbar"
        aria-labelledby="offcanvasDarkNavbarLabel">
        <div class="offcanvas-header">
          <h5 class="offcanvas-title" id="offcanvasDarkNavbarLabel">
            Cine<span class="text-primary">Vault</span>
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
            aria-label="Close"></button>
        </div>
        <div class="offcanvas-body text-center">
          <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
            <li class="nav-item">
              <a class="nav-link text-white" href="FirstProject.php">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white" href="Movies.php">Movies</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white" href="Series.php">Series</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white" href="Categories.php">Categories</a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </nav>
  <!-- END OF HEADER -->

  <script src="header-scroll.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // For desktop
      const desktopLinks = document.querySelectorAll('.middle-header a');
      // For offcanvas
      const offcanvasLinks = document.querySelectorAll('.offcanvas-body .nav-link');

      function setActive(links) {
        let found = false;
        links.forEach(link => {
          const linkPage = link.getAttribute('href').split('?')[0];
          const currentPage = window.location.pathname.split('/').pop();
          if (linkPage === currentPage) {
            link.classList.add('active');
            found = true;
          } else {
            link.classList.remove('active');
          }
        });
        // If no match, set Home as active by default
        if (!found && links.length > 0) {
          links[0].classList.add('active');
        }
      }

      setActive(desktopLinks);
      setActive(offcanvasLinks);
    });
  </script>
</body>
</html>