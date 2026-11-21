<?php
include '../includes/db-connection.php';
include '../includes/dashboard-header-sidebar.php';
require_once '../includes/report-textual.php';

$d = cv_report_data($con);
$view = ($_GET['view'] ?? '') === 'textual' ? 'textual' : 'graphical';

// Brass, cream and bronze: the site's own colours for the charts.
$palette = ['#C9A24B', '#E8DCC0', '#8C6A2F', '#F0D58F', '#A07E52', '#D9B66A', '#5E4A2E', '#B3A68C', '#7A5C1C', '#F3EAD6'];

$cards = [
    ['Members', $d['members'], 'fa-user', 'Active members', 'signup-accounts.php'],
    ['New This Month', $d['members_new_month'], 'fa-user-plus', 'Joined in ' . date('F'), 'signup-accounts.php'],
    ['Movies', $d['movies'], 'fa-film', 'Full-length movies', 'web-movies.php?category=Movie'],
    ['Series', $d['series'], 'fa-tv', 'Classic TV series', 'web-movies.php?category=Series'],
    ['Episodes', $d['episodes'], 'fa-list-ol', 'Across all series', 'web-movies.php?category=Series'],
    ['Total Plays', $d['views'], 'fa-circle-play', 'Movies and episodes played', null],
];

// Charts: the busiest genres only, so the bars stay readable.
$genreTop = array_slice($d['genres'], 0, 12);
$viewGenres = array_values(array_filter($d['genres_by_views'], fn($g) => $g['movie_views'] + $g['series_views'] > 0));
$viewGenres = array_slice($viewGenres, 0, 8);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | CineVault Admin</title>
  <link rel="stylesheet" href="../DASHBOARD-CSS/dashboard.css?v=<?php echo CV_ASSET_VERSION; ?>">
  <link rel="stylesheet" href="../DASHBOARD-CSS/for-all.css?v=<?php echo CV_ASSET_VERSION; ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    .cv-dash-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; }
    .cv-dash-head h1 { font-size: 26px; margin: 0; }
    .cv-dash-sub { color: #B3A68C; font-size: 14px; margin: 2px 0 0; }
    .cv-view-toggle .btn { border-color: #4A3E2C; color: #F1E8D4; }
    .cv-view-toggle .btn.active { background: #C9A24B; border-color: #C9A24B; color: #1A140C; }
    .cv-stat-value { font-size: 26px; font-weight: 700; color: #F1E8D4; }
    .cv-chart-card { background: #1F1A14; border: 1px solid #3D3326; border-radius: 12px; padding: 16px; height: 100%; }
    .cv-chart-card h2 { font-size: 18px; margin: 0; color: #F1E8D4; }
    .cv-chart-card .cv-chart-sub { font-size: 13px; color: #B3A68C; margin: 2px 0 12px; }
    .cv-chart-box { position: relative; height: 280px; }
    .cv-empty { height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #B3A68C; text-align: center; gap: 8px; }
    .cv-empty i { font-size: 34px; color: #4A3E2C; }
    .cv-mini-table { width: 100%; font-size: 14px; }
    .cv-mini-table th { color: #DDB963; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: .04em; padding: 6px 4px; border-bottom: 1px solid #3D3326; }
    .cv-mini-table td { color: #F1E8D4; padding: 7px 4px; border-bottom: 1px solid #2A231A; }
    .cv-mini-table .num { text-align: right; white-space: nowrap; }
    /* Textual view: the report in words and tables, dark. */
    .cv-textual .rpt-block { background: #1F1A14; border: 1px solid #3D3326; border-radius: 12px; padding: 18px; margin-bottom: 16px; color: #F1E8D4; }
    .cv-textual .rpt-title { font-size: 20px; color: #DDB963; margin-bottom: 8px; }
    .cv-textual .rpt-text { color: #D8CCB0; line-height: 1.6; }
    .cv-textual .rpt-summary-row { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 12px; }
    .cv-textual .rpt-summary-box { flex: 1; min-width: 110px; border: 1px solid #3D3326; border-radius: 10px; padding: 12px; background: #2A231A; }
    .cv-textual .rpt-summary-value { font-size: 22px; font-weight: 700; }
    .cv-textual .rpt-summary-label { font-size: 11px; color: #B3A68C; text-transform: uppercase; letter-spacing: .05em; }
    .cv-textual .rpt-two { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; }
    .cv-textual table.rpt-table { width: 100%; font-size: 14px; border-collapse: collapse; margin-top: 6px; }
    .cv-textual table.rpt-table th { background: #3A2E1C; color: #DDB963; text-align: left; padding: 8px 10px; font-size: 12px; text-transform: uppercase; letter-spacing: .04em; }
    .cv-textual table.rpt-table td { padding: 7px 10px; border-bottom: 1px solid #2A231A; }
    .cv-textual table.rpt-table .rpt-num { text-align: right; }
    #printChoices .form-check-label { color: #F1E8D4; }
  </style>
</head>

<body>
  <main class="container-lg p-0 overflow-hidden">
    <div class="px-3 pt-3 cv-dash-head">
      <div>
        <h1 class="db-text-sec">Dashboard</h1>
        <p class="cv-dash-sub">As of <?php echo e($d['generated_at']); ?></p>
      </div>
      <div class="d-flex flex-wrap gap-2">
        <div class="btn-group btn-group-sm cv-view-toggle" role="group" aria-label="Show the dashboard as">
          <a class="btn btn-outline-light <?php echo $view === 'graphical' ? 'active' : ''; ?>" href="dashboard.php" <?php echo $view === 'graphical' ? 'aria-current="true"' : ''; ?>><i class="fa-solid fa-chart-column me-1"></i> Graphical</a>
          <a class="btn btn-outline-light <?php echo $view === 'textual' ? 'active' : ''; ?>" href="dashboard.php?view=textual" <?php echo $view === 'textual' ? 'aria-current="true"' : ''; ?>><i class="fa-solid fa-align-left me-1"></i> Textual</a>
        </div>
        <button type="button" class="btn btn-sm db-bg-primary" data-bs-toggle="modal" data-bs-target="#printChoices">
          <i class="fa-solid fa-print me-1"></i> Print / Save as PDF
        </button>
      </div>
    </div>

    <!-- CARD SECTION -->
    <section class="card-section p-3">
      <div class="row g-3">
        <?php foreach ($cards as [$label, $value, $icon, $note, $link]): ?>
          <div class="col-xl-4 col-md-6">
            <?php if ($link): ?><a class="cv-card-link" href="<?php echo e($link); ?>" aria-label="<?php echo e($label); ?>"><?php endif; ?>
            <div class="card bg-dark">
              <div class="card-body">
                <div class="card-top d-flex align-items-center justify-content-between">
                  <div class="card-icon-container flexbox-align" style="background-color: #C9A24B;">
                    <i class="fa-solid <?php echo e($icon); ?> db-card-icon"></i>
                  </div>
                  <div class="w-100 text-end db-text-sec">
                    <h6 class="card-subtitle fs-14 mb-1"><?php echo e($label); ?></h6>
                    <div class="cv-stat-value"><?php echo number_format($value); ?></div>
                  </div>
                </div>
                <div class="card-divider my-3"></div>
                <div class="d-flex align-items-center gap-2 db-text-secondary fs-12">
                  <i class="fa-solid fa-circle-info"></i>
                  <p class="m-0"><?php echo e($note); ?></p>
                </div>
              </div>
            </div>
            <?php if ($link): ?></a><?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <?php if ($view === 'textual'): ?>
      <!-- TEXTUAL VIEW -->
      <section class="px-3 pb-4 cv-textual">
        <?php echo cv_report_textual($d, array_keys(CV_REPORT_PARTS)); ?>
      </section>
    <?php else: ?>
      <!-- GRAPHICAL VIEW -->
      <section class="px-3 pb-4">
        <div class="row g-3">
          <div class="col-lg-6">
            <div class="cv-chart-card">
              <h2>Member sign-ups</h2>
              <p class="cv-chart-sub">New members per month, last 12 months</p>
              <div class="cv-chart-box"><canvas id="chartSignups"></canvas></div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="cv-chart-card">
              <h2>Most watched genres</h2>
              <p class="cv-chart-sub">Plays of movies and series, by genre</p>
              <div class="cv-chart-box">
                <?php if ($viewGenres): ?>
                  <canvas id="chartViews"></canvas>
                <?php else: ?>
                  <div class="cv-empty"><i class="fa-solid fa-film"></i><span>Nothing has been played yet. This fills in as members watch.</span></div>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <div class="col-lg-8">
            <div class="cv-chart-card">
              <h2>Library by genre</h2>
              <p class="cv-chart-sub">Movies and series in the <?php echo count($genreTop); ?> biggest genres</p>
              <div class="cv-chart-box"><canvas id="chartGenres"></canvas></div>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="cv-chart-card">
              <h2>Titles by decade</h2>
              <p class="cv-chart-sub">When the library's titles were released</p>
              <div class="cv-chart-box"><canvas id="chartDecades"></canvas></div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="cv-chart-card">
              <h2>Most watched titles</h2>
              <p class="cv-chart-sub">Top 10 by plays</p>
              <?php if ($d['top'] && (int) $d['top'][0]['views']): ?>
                <table class="cv-mini-table">
                  <thead><tr><th>#</th><th>Title</th><th>Type</th><th class="num">Plays</th></tr></thead>
                  <tbody>
                    <?php foreach ($d['top'] as $i => $t): if (!(int) $t['views']) break; ?>
                      <tr><td><?php echo $i + 1; ?></td><td><?php echo e($t['title']); ?></td><td><?php echo e($t['category']); ?></td><td class="num"><?php echo number_format($t['views']); ?></td></tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              <?php else: ?>
                <div class="cv-empty py-5"><i class="fa-solid fa-ranking-star"></i><span>No plays yet.</span></div>
              <?php endif; ?>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="cv-chart-card">
              <h2>Recently added</h2>
              <p class="cv-chart-sub">The 10 newest titles in the library</p>
              <table class="cv-mini-table">
                <thead><tr><th>Title</th><th>Type</th><th class="num">Year</th><th class="num">Added</th></tr></thead>
                <tbody>
                  <?php foreach ($d['recent'] as $t): ?>
                    <tr><td><?php echo e($t['title']); ?></td><td><?php echo e($t['category']); ?></td><td class="num"><?php echo e($t['date_released']); ?></td><td class="num"><?php echo e(date('M j', strtotime($t['date_posted']))); ?></td></tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </section>
    <?php endif; ?>
  </main>

  <!-- Print choices: which parts go on the printed sheet -->
  <div class="modal fade" id="printChoices" tabindex="-1" aria-labelledby="printChoicesLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form class="modal-content" action="report-print.php" method="get" target="_blank" id="printChoicesForm">
        <div class="modal-header">
          <h5 class="modal-title" id="printChoicesLabel">Print / Save as PDF</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="cv-dash-sub mb-3">Choose what goes on the report. It prints with the CineVault letterhead, the date and your name.</p>
          <div class="form-check mb-2 pb-2 border-bottom" style="border-color:#3D3326 !important">
            <input class="form-check-input" type="checkbox" id="printAll" checked>
            <label class="form-check-label fw-semibold" for="printAll">Everything</label>
          </div>
          <?php foreach (CV_REPORT_PARTS as $key => $label): ?>
            <div class="form-check">
              <input class="form-check-input cv-print-part" type="checkbox" name="parts[]" value="<?php echo e($key); ?>" id="part-<?php echo e($key); ?>" checked>
              <label class="form-check-label" for="part-<?php echo e($key); ?>"><?php echo e($label); ?></label>
            </div>
          <?php endforeach; ?>
          <p class="text-warning fs-14 mt-2 mb-0 d-none" id="printNone">Pick at least one part.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn db-bg-primary" id="printGo"><i class="fa-solid fa-print me-1"></i> Print</button>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Print choices: "Everything" ticks or clears all parts; Print needs at least one.
    (function () {
      const all = document.getElementById('printAll');
      const parts = [...document.querySelectorAll('.cv-print-part')];
      const go = document.getElementById('printGo');
      const none = document.getElementById('printNone');
      const sync = () => {
        const n = parts.filter(p => p.checked).length;
        all.checked = n === parts.length;
        all.indeterminate = n > 0 && n < parts.length;
        go.disabled = n === 0;
        none.classList.toggle('d-none', n > 0);
      };
      all.addEventListener('change', () => { parts.forEach(p => p.checked = all.checked); sync(); });
      parts.forEach(p => p.addEventListener('change', sync));
      document.getElementById('printChoicesForm').addEventListener('submit', () => {
        setTimeout(() => bootstrap.Modal.getInstance(document.getElementById('printChoices'))?.hide(), 100);
      });
      sync();
    })();
  </script>
  <?php if ($view === 'graphical'): ?>
  <script>
    // Charts in the site's colours.
    const palette = <?php echo json_encode($palette); ?>;
    Chart.defaults.color = '#D8CCB0';
    Chart.defaults.borderColor = 'rgba(241, 232, 212, .08)';
    Chart.defaults.font.family = "'Poppins', sans-serif";
    Chart.defaults.plugins.legend.labels.boxWidth = 14;
    const noDecimals = { precision: 0, beginAtZero: true };

    new Chart(document.getElementById('chartSignups'), {
      type: 'line',
      data: {
        labels: <?php echo json_encode(array_keys($d['signups_by_month'])); ?>,
        datasets: [{
          label: 'New members',
          data: <?php echo json_encode(array_values($d['signups_by_month'])); ?>,
          borderColor: '#C9A24B', backgroundColor: 'rgba(201, 162, 75, .18)',
          fill: true, tension: .3, pointRadius: 3, pointBackgroundColor: '#E0B95C'
        }]
      },
      options: { maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { ticks: noDecimals, suggestedMax: 5 } } }
    });

    <?php if ($viewGenres): ?>
    new Chart(document.getElementById('chartViews'), {
      type: 'doughnut',
      data: {
        labels: <?php echo json_encode(array_column($viewGenres, 'name')); ?>,
        datasets: [{
          data: <?php echo json_encode(array_map(fn($g) => $g['movie_views'] + $g['series_views'], $viewGenres)); ?>,
          backgroundColor: palette, borderColor: '#1F1A14', borderWidth: 2, hoverOffset: 6
        }]
      },
      options: { maintainAspectRatio: false, cutout: '58%', plugins: { legend: { position: 'right' } } }
    });
    <?php endif; ?>

    new Chart(document.getElementById('chartGenres'), {
      type: 'bar',
      data: {
        labels: <?php echo json_encode(array_column($genreTop, 'name')); ?>,
        datasets: [
          { label: 'Movies', data: <?php echo json_encode(array_column($genreTop, 'movies')); ?>, backgroundColor: '#C9A24B', borderRadius: 4 },
          { label: 'Series', data: <?php echo json_encode(array_column($genreTop, 'series')); ?>, backgroundColor: '#E8DCC0', borderRadius: 4 }
        ]
      },
      options: { maintainAspectRatio: false, scales: { x: { stacked: true }, y: { stacked: true, ticks: noDecimals } } }
    });

    new Chart(document.getElementById('chartDecades'), {
      type: 'bar',
      data: {
        labels: <?php echo json_encode(array_column($d['decades'], 'decade')); ?>,
        datasets: [
          { label: 'Movies', data: <?php echo json_encode(array_map('intval', array_column($d['decades'], 'movies'))); ?>, backgroundColor: '#C9A24B', borderRadius: 4 },
          { label: 'Series', data: <?php echo json_encode(array_map('intval', array_column($d['decades'], 'series'))); ?>, backgroundColor: '#E8DCC0', borderRadius: 4 }
        ]
      },
      options: { maintainAspectRatio: false, scales: { x: { stacked: true }, y: { stacked: true, ticks: noDecimals } } }
    });
  </script>
  <?php endif; ?>
</body>

</html>
