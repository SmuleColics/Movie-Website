<?php
/*
 * The dashboard report in words and tables: the same figures the charts are
 * drawn from. Used on the dashboard (Textual view) and on the printed sheet.
 * $parts: which CV_REPORT_PARTS to show (all of them on the dashboard).
 */
require_once __DIR__ . '/report-data.php';

function cv_report_textual(array $d, array $parts)
{
    $show = fn($p) => in_array($p, $parts, true);
    $pct = fn($n, $total) => $total ? round($n / $total * 100) . '%' : '0%';
    ob_start();

    if ($show('summary')): ?>
<section class="rpt-block">
  <h3 class="rpt-title">Summary</h3>
  <div class="rpt-summary-row">
    <div class="rpt-summary-box"><div class="rpt-summary-value"><?php echo number_format($d['members']); ?></div><div class="rpt-summary-label">Members</div></div>
    <div class="rpt-summary-box"><div class="rpt-summary-value"><?php echo number_format($d['movies']); ?></div><div class="rpt-summary-label">Movies</div></div>
    <div class="rpt-summary-box"><div class="rpt-summary-value"><?php echo number_format($d['series']); ?></div><div class="rpt-summary-label">Series</div></div>
    <div class="rpt-summary-box"><div class="rpt-summary-value"><?php echo number_format($d['episodes']); ?></div><div class="rpt-summary-label">Episodes</div></div>
    <div class="rpt-summary-box"><div class="rpt-summary-value"><?php echo number_format($d['views']); ?></div><div class="rpt-summary-label">Total plays</div></div>
  </div>
  <p class="rpt-text">
    CineVault has <?php echo cv_count($d['members'], 'active member'); ?>,
    <?php echo cv_count($d['members_new_month'], 'of them', 'of them'); ?> new in <?php echo date('F'); ?>.
    The library holds <?php echo cv_count($d['movies'], 'full-length movie'); ?> and
    <?php echo cv_count($d['series'], 'series', 'series'); ?> with <?php echo cv_count($d['episodes'], 'episode'); ?>,
    played <?php echo cv_count($d['views'], 'time'); ?> in total.
    <?php if ($d['watching']): ?><?php echo cv_count($d['watching'], 'member has', 'members have'); ?> watched something in the last 30 days.<?php endif; ?>
    <?php if ($d['members_archived'] || $d['titles_archived']): ?>
      In the archive: <?php echo cv_count($d['members_archived'], 'member'); ?> and <?php echo cv_count($d['titles_archived'], 'title'); ?>.
    <?php endif; ?>
  </p>
</section>
<?php endif;

    if ($show('members')):
        $byDay = $d['signups_by_day'];
        $daysTotal = array_sum($byDay);
        $busiestDay = $daysTotal ? array_search(max($byDay), $byDay, true) : null;
        $byMonth = $d['signups_by_month'];
        $monthTotal = array_sum($byMonth); ?>
<section class="rpt-block">
  <h3 class="rpt-title">Member sign-ups</h3>
  <p class="rpt-text">
    <?php if ($daysTotal): ?>
      Of the <?php echo cv_count($daysTotal, 'active member'); ?>, most joined on a <?php echo e($busiestDay); ?>
      (<?php echo $byDay[$busiestDay]; ?>). In the last 12 months, <?php echo cv_count($monthTotal, 'member'); ?> joined.
    <?php else: ?>
      No active members yet.
    <?php endif; ?>
  </p>
  <div class="rpt-two">
    <table class="rpt-table">
      <thead><tr><th>Day of the week</th><th class="rpt-num">Members</th></tr></thead>
      <tbody><?php foreach ($byDay as $day => $n): ?><tr><td><?php echo e($day); ?></td><td class="rpt-num"><?php echo $n; ?></td></tr><?php endforeach; ?></tbody>
    </table>
    <table class="rpt-table">
      <thead><tr><th>Month</th><th class="rpt-num">Members</th></tr></thead>
      <tbody><?php foreach ($byMonth as $m => $n): ?><tr><td><?php echo e($m); ?></td><td class="rpt-num"><?php echo $n; ?></td></tr><?php endforeach; ?></tbody>
    </table>
  </div>
</section>
<?php endif;

    if ($show('library')):
        $totalTitles = $d['movies'] + $d['series'];
        $biggest = $d['genres'][0] ?? null; ?>
<section class="rpt-block">
  <h3 class="rpt-title">Library by genre</h3>
  <p class="rpt-text">
    <?php if ($biggest): ?>
      <?php echo e($biggest['name']); ?> is the biggest genre, with <?php echo cv_count($biggest['movies'], 'movie'); ?>
      and <?php echo cv_count($biggest['series'], 'series', 'series'); ?>. A title can belong to up to three genres,
      so the rows add up to more than the <?php echo cv_count($totalTitles, 'title'); ?> in the library.
    <?php else: ?>
      The library is empty.
    <?php endif; ?>
  </p>
  <table class="rpt-table">
    <thead><tr><th>Genre</th><th class="rpt-num">Movies</th><th class="rpt-num">Series</th><th class="rpt-num">Total</th></tr></thead>
    <tbody>
      <?php foreach ($d['genres'] as $g): ?>
        <tr><td><?php echo e($g['name']); ?></td><td class="rpt-num"><?php echo $g['movies']; ?></td><td class="rpt-num"><?php echo $g['series']; ?></td><td class="rpt-num"><?php echo $g['movies'] + $g['series']; ?></td></tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>
<?php endif;

    if ($show('views')):
        $viewsTotal = array_sum(array_map(fn($g) => $g['movie_views'] + $g['series_views'], $d['genres_by_views']));
        $topGenre = $d['genres_by_views'][0] ?? null; ?>
<section class="rpt-block">
  <h3 class="rpt-title">Views by genre</h3>
  <p class="rpt-text">
    <?php if ($viewsTotal && $topGenre): ?>
      <?php echo e($topGenre['name']); ?> is the most watched genre, with
      <?php echo cv_count($topGenre['movie_views'] + $topGenre['series_views'], 'play'); ?>
      (<?php echo $pct($topGenre['movie_views'] + $topGenre['series_views'], $viewsTotal); ?> of plays counted by genre).
    <?php else: ?>
      Nothing has been played yet, so there are no views to compare.
    <?php endif; ?>
  </p>
  <?php if ($viewsTotal): ?>
  <table class="rpt-table">
    <thead><tr><th>Genre</th><th class="rpt-num">Movie plays</th><th class="rpt-num">Series plays</th><th class="rpt-num">Share</th></tr></thead>
    <tbody>
      <?php foreach ($d['genres_by_views'] as $g): if (!($g['movie_views'] + $g['series_views'])) continue; ?>
        <tr><td><?php echo e($g['name']); ?></td><td class="rpt-num"><?php echo number_format($g['movie_views']); ?></td><td class="rpt-num"><?php echo number_format($g['series_views']); ?></td><td class="rpt-num"><?php echo $pct($g['movie_views'] + $g['series_views'], $viewsTotal); ?></td></tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</section>
<?php endif;

    if ($show('decades')):
        $maxDecade = null;
        foreach ($d['decades'] as $row) {
            if (!$maxDecade || $row['movies'] + $row['series'] > $maxDecade['movies'] + $maxDecade['series']) {
                $maxDecade = $row;
            }
        } ?>
<section class="rpt-block">
  <h3 class="rpt-title">Titles by decade</h3>
  <p class="rpt-text">
    <?php if ($maxDecade): ?>
      Most of the library comes from the <?php echo e($maxDecade['decade']); ?>:
      <?php echo cv_count((int) $maxDecade['movies'], 'movie'); ?> and <?php echo cv_count((int) $maxDecade['series'], 'series', 'series'); ?>.
    <?php else: ?>
      No release years on record yet.
    <?php endif; ?>
  </p>
  <table class="rpt-table">
    <thead><tr><th>Decade</th><th class="rpt-num">Movies</th><th class="rpt-num">Series</th></tr></thead>
    <tbody><?php foreach ($d['decades'] as $row): ?><tr><td><?php echo e($row['decade']); ?></td><td class="rpt-num"><?php echo (int) $row['movies']; ?></td><td class="rpt-num"><?php echo (int) $row['series']; ?></td></tr><?php endforeach; ?></tbody>
  </table>
</section>
<?php endif;

    if ($show('top')): ?>
<section class="rpt-block">
  <h3 class="rpt-title">Most watched titles</h3>
  <?php if (!$d['top'] || !(int) $d['top'][0]['views']): ?>
    <p class="rpt-text">Nothing has been played yet.</p>
  <?php else: ?>
    <p class="rpt-text"><?php echo e($d['top'][0]['title']); ?> is the most watched title, played <?php echo cv_count((int) $d['top'][0]['views'], 'time'); ?>.</p>
    <table class="rpt-table">
      <thead><tr><th class="rpt-num">#</th><th>Title</th><th>Type</th><th class="rpt-num">Year</th><th class="rpt-num">Plays</th></tr></thead>
      <tbody><?php foreach ($d['top'] as $i => $t): if (!(int) $t['views']) break; ?><tr><td class="rpt-num"><?php echo $i + 1; ?></td><td><?php echo e($t['title']); ?></td><td><?php echo e($t['category']); ?></td><td class="rpt-num"><?php echo e($t['date_released']); ?></td><td class="rpt-num"><?php echo number_format($t['views']); ?></td></tr><?php endforeach; ?></tbody>
    </table>
  <?php endif; ?>
</section>
<?php endif;

    if ($show('recent')): ?>
<section class="rpt-block">
  <h3 class="rpt-title">Recently added titles</h3>
  <table class="rpt-table">
    <thead><tr><th>Title</th><th>Type</th><th class="rpt-num">Year</th><th>Added</th></tr></thead>
    <tbody><?php foreach ($d['recent'] as $t): ?><tr><td><?php echo e($t['title']); ?></td><td><?php echo e($t['category']); ?></td><td class="rpt-num"><?php echo e($t['date_released']); ?></td><td><?php echo e(date('M j, Y', strtotime($t['date_posted']))); ?></td></tr><?php endforeach; ?></tbody>
  </table>
</section>
<?php endif;

    return ob_get_clean();
}
