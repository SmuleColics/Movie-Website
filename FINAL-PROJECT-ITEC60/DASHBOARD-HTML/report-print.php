<?php
// The dashboard report as a printable sheet (Print / Save as PDF).
// ?parts[]=summary&parts[]=top ... picks which parts to include (see CV_REPORT_PARTS).
// Admins only: the security setup requires a signed-in admin for this folder.
include '../includes/db-connection.php';
require_once '../includes/report-textual.php';
require_once '../includes/print-letterhead.php';

$parts = array_values(array_intersect(array_keys(CV_REPORT_PARTS), (array) ($_GET['parts'] ?? [])));
if (!$parts) {
    $parts = array_keys(CV_REPORT_PARTS);
}

$stmt = $con->prepare('SELECT first_name, last_name FROM tbl_admin_acc WHERE admin_id = ?');
$adminId = (int) ($_SESSION['admin_id'] ?? 0);
$stmt->bind_param('i', $adminId);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc() ?: ['first_name' => 'Admin', 'last_name' => ''];
$stmt->close();
$adminName = trim($admin['first_name'] . ' ' . $admin['last_name']);

$data = cv_report_data($con);
$included = array_map(fn($p) => CV_REPORT_PARTS[$p], $parts);
?>
<!DOCTYPE html>
<html lang="en" data-cv-plain>
<head>
  <meta charset="UTF-8">
  <title><?php echo e(cv_setting('site_name', 'CineVault')); ?> Report, <?php echo e(date('F j, Y')); ?></title>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&display=swap">
  <link rel="stylesheet" href="../shared/print-sheet.css?v=<?php echo CV_ASSET_VERSION; ?>">
</head>
<body>
  <div class="no-print"><button type="button" onclick="window.print()">Print / Save as PDF</button></div>

  <?php echo cv_print_letterhead('../'); ?>

  <p class="rpt-subject">Dashboard Report</p>
  <div class="rpt-meta-row">
    <div><span class="rpt-meta-label">Date</span><span class="rpt-meta-value"><?php echo e(date('F j, Y')); ?></span></div>
    <div><span class="rpt-meta-label">Prepared by</span><span class="rpt-meta-value"><?php echo e($adminName); ?></span></div>
    <div><span class="rpt-meta-label">Includes</span><span class="rpt-meta-value"><?php echo e(implode(', ', $included)); ?></span></div>
  </div>

  <?php echo cv_report_textual($data, $parts); ?>

  <?php echo cv_print_footer($adminName); ?>

  <script>
    // Open the print window as soon as the fonts are in.
    if (!new URLSearchParams(location.search).has('preview')) {
      window.addEventListener('load', () => setTimeout(() => window.print(), 300));
    }
  </script>
</body>
</html>
