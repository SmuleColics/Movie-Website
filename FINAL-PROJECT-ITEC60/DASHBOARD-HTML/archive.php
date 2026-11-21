<?php
// Archive: everything archived on the admin side, with Restore.
// Tabs: movies, series, genres, members, admins (?tab=...).
include '../includes/db-connection.php';

$tabs = [
    'movies'  => 'Movies',
    'series'  => 'Series',
    'genres'  => 'Genres',
    'members' => 'Members',
    'admins'  => 'Admins',
];
$tab = isset($tabs[$_GET['tab'] ?? '']) ? $_GET['tab'] : 'movies';

// Restore (a POST from the Restore window; the security setup checks its token).
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['restore-kind'], $_POST['restore-id'])) {
    $kind = $_POST['restore-kind'];
    $id = (int) $_POST['restore-id'];
    // Titles and genres sit in their own *_archived tables; members and admins have an is_archived flag.
    $targets = [
        'title'  => ['tbl_movie_series_archived', 'movie_series_id', 'title'],
        'genre'  => ['tbl_movie_series_genre_archived', 'genre_id', 'genre_name'],
        'member' => ['tbl_signup_acc', 'signup_id', "CONCAT(first_name, ' ', last_name)"],
        'admin'  => ['tbl_admin_acc', 'admin_id', "CONCAT(first_name, ' ', last_name)"],
    ];
    if (isset($targets[$kind]) && $id > 0) {
        [$table, $key, $nameSql] = $targets[$kind];
        $name = (string) (mysqli_fetch_row(mysqli_query($con, "SELECT $nameSql FROM $table WHERE $key = $id AND is_archived = 1"))[0] ?? '');
        if ($name !== '') {
            if (isset(CV_ARCHIVE_TABLES[$kind])) {
                if (!cv_restore_row($con, $kind, $id)) {
                    cv_alert_redirect('"' . $name . '" could not be restored. Try again, or check that a record with the same number isn\'t already in the library.', 'archive.php?tab=' . urlencode($tab), 'danger');
                }
            } else {
                mysqli_query($con, "UPDATE $table SET is_archived = 0, archived_at = NULL WHERE $key = $id");
            }
            cv_alert_redirect('"' . $name . '" was restored.', 'archive.php?tab=' . urlencode($tab));
        }
    }
    cv_alert_redirect('That item is not in the Archive.', 'archive.php?tab=' . urlencode($tab), 'warning');
}

include '../includes/dashboard-header-sidebar.php';

// The rows for the open tab.
$when = fn($row) => $row['archived_at'] ? date('M j, Y g:i A', strtotime($row['archived_at'])) : 'Before archive dates were kept';
switch ($tab) {
    case 'movies':
    case 'series':
        $cat = $tab === 'movies' ? 'Movie' : 'Series';
        $stmt = $con->prepare("SELECT m.movie_series_id AS id, m.title, m.date_released, m.archived_at, COALESCE(g.genre_name, ga.genre_name) AS genre_name
            FROM tbl_movie_series_archived m
            LEFT JOIN tbl_movie_series_genre g ON g.genre_id = m.genre_id1
            LEFT JOIN tbl_movie_series_genre_archived ga ON ga.genre_id = m.genre_id1
            WHERE m.category = ? ORDER BY m.archived_at DESC, m.title");
        $stmt->bind_param('s', $cat);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $kind = 'title';
        $cols = ['#', 'Title', 'Year', 'Genre', 'Archived'];
        $cells = fn($r) => [$r['id'], $r['title'], $r['date_released'], $r['genre_name'], $when($r)];
        $nameOf = fn($r) => $r['title'];
        break;
    case 'genres':
        $rows = mysqli_fetch_all(mysqli_query($con, "SELECT genre_id AS id, genre_name, archived_at FROM tbl_movie_series_genre_archived ORDER BY archived_at DESC, genre_name"), MYSQLI_ASSOC);
        $kind = 'genre';
        $cols = ['#', 'Genre', 'Archived'];
        $cells = fn($r) => [$r['id'], $r['genre_name'], $when($r)];
        $nameOf = fn($r) => $r['genre_name'];
        break;
    case 'members':
        $rows = mysqli_fetch_all(mysqli_query($con, "SELECT signup_id AS id, first_name, last_name, signup_email, date_created, archived_at FROM tbl_signup_acc WHERE is_archived = 1 ORDER BY archived_at DESC, date_created DESC"), MYSQLI_ASSOC);
        $kind = 'member';
        $cols = ['#', 'Name', 'Email', 'Joined', 'Archived'];
        $cells = fn($r) => [$r['id'], trim($r['first_name'] . ' ' . $r['last_name']), $r['signup_email'], date('M j, Y', strtotime($r['date_created'])), $when($r)];
        $nameOf = fn($r) => trim($r['first_name'] . ' ' . $r['last_name']);
        break;
    default: // admins
        $rows = mysqli_fetch_all(mysqli_query($con, "SELECT admin_id AS id, first_name, last_name, email, archived_at FROM tbl_admin_acc WHERE is_archived = 1 ORDER BY archived_at DESC"), MYSQLI_ASSOC);
        $kind = 'admin';
        $cols = ['#', 'Name', 'Email', 'Archived'];
        $cells = fn($r) => [$r['id'], trim($r['first_name'] . ' ' . $r['last_name']), $r['email'], $when($r)];
        $nameOf = fn($r) => trim($r['first_name'] . ' ' . $r['last_name']);
}

// How many are in each tab, for the tab labels.
$counts = [
    'movies'  => (int) mysqli_fetch_row(mysqli_query($con, "SELECT COUNT(*) FROM tbl_movie_series_archived WHERE category = 'Movie'"))[0],
    'series'  => (int) mysqli_fetch_row(mysqli_query($con, "SELECT COUNT(*) FROM tbl_movie_series_archived WHERE category = 'Series'"))[0],
    'genres'  => (int) mysqli_fetch_row(mysqli_query($con, "SELECT COUNT(*) FROM tbl_movie_series_genre_archived"))[0],
    'members' => (int) mysqli_fetch_row(mysqli_query($con, "SELECT COUNT(*) FROM tbl_signup_acc WHERE is_archived = 1"))[0],
    'admins'  => (int) mysqli_fetch_row(mysqli_query($con, "SELECT COUNT(*) FROM tbl_admin_acc WHERE is_archived = 1"))[0],
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Archive | CineVault Admin</title>
  <link rel="stylesheet" href="../DASHBOARD-CSS/dashboard.css?v=<?php echo CV_ASSET_VERSION; ?>">
  <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.min.css">
  <style>
    .cv-tabs { display: flex; flex-wrap: wrap; gap: 6px; }
    .cv-tabs a { padding: 6px 14px; border-radius: 999px; border: 1px solid #4A3E2C; color: #F1E8D4; text-decoration: none; font-size: 14px; }
    .cv-tabs a.active { background: #C9A24B; border-color: #C9A24B; color: #1A140C; font-weight: 600; }
    .cv-tabs .count { opacity: .75; margin-left: 4px; }
  </style>
</head>

<body>
  <main class="container-lg p-0 overflow-hidden">
    <section class="text-left p-3">
      <div class="card bg-dark">
        <div class="card-body">
          <div class="table-task-top db-text-sec">
            <div class="py-2 ps-3 pe-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
              <div>
                <p class="m-0 fs-20">Archive</p>
                <p class="m-0 fs-14">Archived items are hidden from members. Restore puts them back.</p>
              </div>
              <button type="button" class="btn btn-sm btn-outline-light" data-print-table="#table-archive"
                data-print-title="Archived <?php echo e($tabs[$tab]); ?>"><i class="fa-solid fa-print me-1"></i> Print</button>
            </div>
          </div>

          <nav class="cv-tabs my-3" aria-label="Archive sections">
            <?php foreach ($tabs as $key => $label): ?>
              <a href="archive.php?tab=<?php echo $key; ?>" class="<?php echo $key === $tab ? 'active' : ''; ?>" <?php echo $key === $tab ? 'aria-current="page"' : ''; ?>>
                <?php echo e($label); ?><span class="count">(<?php echo $counts[$key]; ?>)</span>
              </a>
            <?php endforeach; ?>
          </nav>

          <div class="table-responsive">
            <table class="table table-hover display" id="table-archive">
              <thead>
                <tr>
                  <?php foreach ($cols as $c): ?><th class="db-text-primary" scope="col"><?php echo e($c); ?></th><?php endforeach; ?>
                  <th class="db-text-primary text-center" scope="col">Restore</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($rows as $r): ?>
                  <tr>
                    <?php foreach ($cells($r) as $i => $v): ?>
                      <<?php echo $i === 0 ? 'th scope="row" class="db-text-primary"' : 'td'; ?>><?php echo e($v); ?></<?php echo $i === 0 ? 'th' : 'td'; ?>>
                    <?php endforeach; ?>
                    <td class="text-center">
                      <button type="button" class="btn p-0 border-0 db-text-primary cv-restore" title="Restore"
                        data-bs-toggle="modal" data-bs-target="#restoreModal"
                        data-id="<?php echo (int) $r['id']; ?>" data-name="<?php echo e($nameOf($r)); ?>">
                        <i class="fa-solid fa-rotate-left"></i>
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- Restore confirmation -->
  <div class="modal fade" id="restoreModal" tabindex="-1" aria-labelledby="restoreTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form class="modal-content" method="post" action="archive.php?tab=<?php echo e($tab); ?>">
        <div class="modal-header">
          <h1 class="modal-title fs-5 db-text-sec" id="restoreTitle">Restore</h1>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="restore-kind" value="<?php echo e($kind); ?>">
          <input type="hidden" name="restore-id" id="restore-id">
          <p class="db-text-sec fs-5 text-center my-3">Restore <strong id="restore-name"></strong>?</p>
          <p class="db-text-secondary text-center mb-0">
            <?php echo $kind === 'title' ? 'It will show on the site again.' : ($kind === 'genre' ? 'It will be available to pick again.' : 'They will be able to sign in again.'); ?>
          </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn db-bg-primary">Restore</button>
        </div>
      </form>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.min.js"></script>
  <script>
    new DataTable('#table-archive', {
      order: [],
      language: { emptyTable: 'Nothing in this part of the Archive.' },
      columnDefs: [{ targets: -1, orderable: false, searchable: false }]
    });
    document.addEventListener('click', e => {
      const b = e.target.closest('.cv-restore');
      if (!b) return;
      document.getElementById('restore-id').value = b.dataset.id;
      document.getElementById('restore-name').textContent = b.dataset.name;
    });
  </script>
</body>

</html>
