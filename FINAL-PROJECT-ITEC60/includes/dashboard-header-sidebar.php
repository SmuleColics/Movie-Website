<?php
include 'db-connection.php';

// Signing out happens in DASHBOARD-HTML/admin-logout.php, after the "Sign out?" popup.

$cv_admin_id = (int) ($_SESSION['admin_id'] ?? 0);

// Which sidebar item to highlight. Movies and Series are separate items;
// the add/edit form counts as Series when the record (or ?category=) is one.
$cv_page = basename($_SERVER['SCRIPT_NAME'] ?? '');
$cv_cat = $_GET['category'] ?? '';
if ($cv_page === 'web-movie-series.php' && $cv_cat === '' && !empty($_GET['id'])) {
    $st = $con->prepare('SELECT category FROM tbl_movie_series WHERE movie_series_id = ?');
    $cv_rec = (int) $_GET['id'];
    $st->bind_param('i', $cv_rec);
    $st->execute();
    $cv_cat = (string) ($st->get_result()->fetch_column() ?: '');
    $st->close();
}
$cv_nav = [
    'dashboard.php' => 'dashboard',
    'web-movies.php' => $cv_cat === 'Series' ? 'series' : 'movies',
    'web-movie-series.php' => $cv_cat === 'Series' ? 'series' : 'movies',
    'web-genre.php' => 'genres',
    'signup-accounts.php' => 'accounts',
    'archive-accounts.php' => 'accounts',
][$cv_page] ?? '';
function cv_nav_active($key)
{
    global $cv_nav;
    return $cv_nav === $key ? ' is-active' : '';
}

// Signed-in admin, for the account chip.
$stmt = $con->prepare("SELECT first_name, last_name, email FROM tbl_admin_acc WHERE admin_id = ?");
$stmt->bind_param('i', $cv_admin_id);
$stmt->execute();
$cv_admin = $stmt->get_result()->fetch_assoc() ?: ['first_name' => 'Admin', 'last_name' => '', 'email' => ''];
$stmt->close();
$cv_admin_first = trim(explode(' ', trim($cv_admin['first_name']))[0] ?? 'Admin');
$cv_admin_full  = trim($cv_admin['first_name'] . ' ' . $cv_admin['last_name']);
$cv_admin_initials = strtoupper(substr($cv_admin['first_name'], 0, 1) . substr($cv_admin['last_name'], 0, 1));

// Notifications: newest member sign ups, with read/unread per admin.
$stmt = $con->prepare(
  "SELECT s.signup_id, s.first_name, s.last_name, s.signup_email, s.date_created,
          (r.signup_id IS NOT NULL) AS is_read
   FROM tbl_signup_acc s
   LEFT JOIN tbl_admin_notif_reads r ON r.signup_id = s.signup_id AND r.admin_id = ?
   WHERE s.is_archived = 0
   ORDER BY s.date_created DESC
   LIMIT 8"
);
$stmt->bind_param('i', $cv_admin_id);
$stmt->execute();
$cv_notifs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$stmt = $con->prepare(
  "SELECT COUNT(*) AS n FROM tbl_signup_acc s
   LEFT JOIN tbl_admin_notif_reads r ON r.signup_id = s.signup_id AND r.admin_id = ?
   WHERE s.is_archived = 0 AND r.signup_id IS NULL"
);
$stmt->bind_param('i', $cv_admin_id);
$stmt->execute();
$notif_count = (int) $stmt->get_result()->fetch_assoc()['n'];
$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CineVault Admin</title>
  <!-- ========== CSS LINK ========== -->
  <link rel="stylesheet" href="../DASHBOARD-CSS/dashboard-header-sidebar.css?v=<?php echo CV_ASSET_VERSION; ?>" />
  <link rel="stylesheet" href="../DASHBOARD-CSS/for-all.css?v=<?php echo CV_ASSET_VERSION; ?>">
  <link rel="stylesheet" href="../DASHBOARD-CSS/admin-theme.css?v=<?php echo CV_ASSET_VERSION; ?>">
  <link rel="stylesheet" href="../shared/notifications.css?v=<?php echo CV_ASSET_VERSION; ?>">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
  <link rel="icon" href="../MOVIE-IMG/HEADER-IMG/CINEVAULT-LOGO.svg">
</head>

<body class="cv-admin">
  <nav class="navbar navbar-dark fixed-top" style="background-color: #17130F; z-index: 1050;">
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
          <!-- ========== SIGNED-IN ACCOUNT (left of the bell) ========== -->
          <div class="dropdown cv-account-dropdown">
            <button type="button" class="cv-account" data-bs-toggle="dropdown" data-bs-display="static"
              aria-expanded="false" title="Signed in as <?php echo e($cv_admin_first); ?> (Administrator)">
              <span class="cv-account-avatar"><?php echo e($cv_admin_initials ?: 'A'); ?></span>
              <span class="cv-account-text">
                <span class="cv-account-name"><?php echo e($cv_admin_first); ?></span>
                <span class="cv-account-role">Administrator</span>
              </span>
              <i class="fa-solid fa-chevron-down cv-account-chevron"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end cv-account-menu">
              <li class="cv-account-head">
                <span class="cv-account-avatar cv-account-avatar-lg"><?php echo e($cv_admin_initials ?: 'A'); ?></span>
                <div class="cv-account-head-text">
                  <p class="cv-account-head-name"><?php echo e($cv_admin_full); ?></p>
                  <p class="cv-account-head-email"><?php echo e($cv_admin['email']); ?></p>
                  <span class="cv-account-head-role">Administrator</span>
                </div>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <a class="dropdown-item d-flex align-items-center gap-2" href="../DASHBOARD-HTML/my-profile.php">
                  <i class="fa-solid fa-user-gear"></i> View Profile
                </a>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <a class="dropdown-item d-flex align-items-center gap-2 text-danger" href="#" data-bs-toggle="modal"
                  data-bs-target="#cvLogoutModal">
                  <i class="fa-solid fa-right-from-bracket"></i> Log out
                </a>
              </li>
            </ul>
          </div>

          <!-- ========== NOTIFICATIONS (last item on the bar) ========== -->
          <div class="dropdown">
            <button type="button" class="cv-bell" data-bs-toggle="dropdown" data-bs-display="static"
              aria-expanded="false" title="Notifications">
              <i class="fa-solid fa-bell"></i>
              <span class="cv-bell-badge" id="cv-bell-badge" <?php echo $notif_count ? '' : 'hidden'; ?>>
                <?php echo $notif_count > 99 ? '99+' : $notif_count; ?>
              </span>
            </button>
            <div class="dropdown-menu dropdown-menu-end cv-notif-menu" data-notif-endpoint="../DASHBOARD-HTML/admin-notifications.php">
              <div class="cv-notif-header">
                <span>Notifications</span>
                <a href="#" id="cv-notif-read-all" class="cv-notif-read-all" <?php echo $notif_count ? '' : 'hidden'; ?>>Mark all as read</a>
              </div>
              <div class="cv-notif-list">
                <?php if ($cv_notifs): ?>
                  <?php foreach ($cv_notifs as $n):
                    $who = trim($n['first_name'] . ' ' . $n['last_name']) ?: $n['signup_email']; ?>
                    <a href="../DASHBOARD-HTML/signup-accounts.php?search=<?php echo urlencode($n['signup_email']); ?>"
                      class="cv-notif-item js-notif-item<?php echo $n['is_read'] ? '' : ' unread'; ?>"
                      data-notif-id="<?php echo (int) $n['signup_id']; ?>">
                      <span class="cv-notif-icon"><i class="fa-solid fa-user-plus"></i></span>
                      <span class="cv-notif-body">
                        <span class="cv-notif-title">New member: <?php echo e($who); ?></span>
                        <span class="cv-notif-detail"><?php echo e($n['signup_email']); ?></span>
                        <span class="cv-notif-time"><?php echo e(cv_time_ago($n['date_created'])); ?></span>
                      </span>
                      <span class="cv-notif-dot" aria-label="Unread"></span>
                    </a>
                  <?php endforeach; ?>
                <?php else: ?>
                  <div class="text-center small py-4 cv-notif-empty">No notifications yet.</div>
                <?php endif; ?>
              </div>
              <a href="../DASHBOARD-HTML/signup-accounts.php" class="cv-notif-view-all">View all members</a>
            </div>
          </div>
        </div>

      </header>
      <?php $cvLogoutAction = 'admin-logout.php'; $cvLogoutMessage = 'You will need to sign in again to manage CineVault.'; include __DIR__ . '/logout-modal.php'; ?>

      <div class="offcanvas offcanvas-start text-bg-dark" tabindex="-1" id="dashboard-offcanvas"
        aria-labelledby="offcanvasDarkNavbarLabel" style="background-color: #17130F">
        <div class="offcanvas-header" style="background-color: #17130F">
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
        <div class="offcanvas-body" style="background-color: #17130F">
          <ul class="offcanvas-content list-unstyled fs-20">
            <li class="sidebar-content-item d-flex align-items-center mb-1<?php echo cv_nav_active('dashboard'); ?>">
              <a href="../DASHBOARD-HTML/dashboard.php" class="sidebar-anchor">
                <span class="aside-icon material-symbols-outlined text-center">
                  dashboard
                </span>
                <span class="sidebar-text ms-2">Dashboard</span>
              </a>
            </li>
            <li class="sidebar-content-item d-flex align-items-center mb-1<?php echo cv_nav_active('movies'); ?>">
              <a href="../DASHBOARD-HTML/web-movies.php?category=Movie" class="sidebar-anchor">
                <span class="material-symbols-outlined">
                  movie
                </span>
                <span class="sidebar-text" style="margin-left: 12px">Movies</span>
              </a>
            </li>
            <li class="sidebar-content-item d-flex align-items-center mb-1<?php echo cv_nav_active('series'); ?>">
              <a href="../DASHBOARD-HTML/web-movies.php?category=Series" class="sidebar-anchor">
                <span class="material-symbols-outlined">
                  live_tv
                </span>
                <span class="sidebar-text" style="margin-left: 12px">Series</span>
              </a>
            </li>
            <li class="sidebar-content-item sidebar-collapse d-flex align-items-center justify-content-between mb-1 movies-db<?php echo cv_nav_active('genres'); ?>"
              aria-expanded="true">
              <a href="../DASHBOARD-HTML/web-genre.php" class="sidebar-anchor">
                <div class="d-flex align-items-center">
                  <span class="aside-icon material-symbols-outlined">
                    category
                  </span>
                  <span class="sidebar-text ms-2">Genres</span>
                </div>
              </a>
            </li>
            <li class="sidebar-content-item sidebar-collapse d-flex align-items-cente justify-content-between mb-1<?php echo cv_nav_active('accounts'); ?>"
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
  <aside class="sidebar" style="background-color: #17130F;">
    <div class="sidebar-container text-light">
      <ul class="sidebar-content list-unstyled fs-20">
        <li class="sidebar-content-item d-flex align-items-center mb-1<?php echo cv_nav_active('dashboard'); ?>">
          <a href="../DASHBOARD-HTML/dashboard.php" class="sidebar-anchor">
            <span class="aside-icon material-symbols-outlined text-center">
              dashboard
            </span>
            <span class="sidebar-text ms-2">Dashboard</span>
          </a>
        </li>
        <li class="sidebar-content-item d-flex align-items-center mb-1<?php echo cv_nav_active('movies'); ?>">
          <a href="../DASHBOARD-HTML/web-movies.php?category=Movie" class="sidebar-anchor">
            <span class="material-symbols-outlined">
              movie
            </span>
            <span class="sidebar-text" style="margin-left: 12px">Movies</span>
          </a>
        </li>
        <li class="sidebar-content-item d-flex align-items-center mb-1<?php echo cv_nav_active('series'); ?>">
          <a href="../DASHBOARD-HTML/web-movies.php?category=Series" class="sidebar-anchor">
            <span class="material-symbols-outlined">
              live_tv
            </span>
            <span class="sidebar-text" style="margin-left: 12px">Series</span>
          </a>
        </li>
        <li class="sidebar-content-item sidebar-collapse d-flex align-items-cente justify-content-between mb-1 movies-db<?php echo cv_nav_active('genres'); ?>"
          aria-expanded="true">
          <a href="../DASHBOARD-HTML/web-genre.php" class="sidebar-anchor">
            <div class="d-flex align-items-center">
              <span class="aside-icon material-symbols-outlined">
                category
              </span>
              <span class="sidebar-text ms-2">Genres</span>
            </div>
          </a>
        </li>
        <li class="sidebar-content-item sidebar-collapse d-flex align-items-cente justify-content-between mb-1<?php echo cv_nav_active('accounts'); ?>">
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

</script>
<script src="../shared/notifications.js?v=<?php echo CV_ASSET_VERSION; ?>"></script>
<script src="../shared/ellipsis-tooltips.js?v=<?php echo CV_ASSET_VERSION; ?>" defer></script>
</html>