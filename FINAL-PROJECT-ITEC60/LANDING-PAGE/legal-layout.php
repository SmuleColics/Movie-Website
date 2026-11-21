<?php
// Shared page frame for the Terms of Service and Privacy Policy.
require_once __DIR__ . '/../includes/site-footer.php';

function cv_contact_line()
{
    $email = cv_config('mail_username');
    return $email
        ? 'email us at <a class="db-text-primary" href="mailto:' . e($email) . '">' . e($email) . '</a>'
        : 'contact the CineVault administrators';
}

function cv_legal_page_start($title)
{
    ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo e($title); ?> | CineVault</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="LandingPage.css?v=<?php echo CV_ASSET_VERSION; ?>">
  <link rel="stylesheet" href="../DASHBOARD-CSS/for-all.css?v=<?php echo CV_ASSET_VERSION; ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="icon" href="../MOVIE-IMG/HEADER-IMG/CINEVAULT-LOGO.svg?v=<?php echo CV_ASSET_VERSION; ?>">
  <style>
    .legal-header { height: 64px; border-bottom: 1px solid var(--border-color); }
    .legal-wrap { max-width: 860px; margin: 0 auto; padding: 40px 20px 80px; color: var(--box-text); line-height: 1.7; }
    .legal-wrap h1 { color: #fff; font-weight: 700; }
    .legal-wrap h2 { color: #fff; font-size: 20px; font-weight: 600; margin-top: 36px; }
    .legal-wrap a { color: var(--dashboard-primary); }
    .legal-updated { color: var(--placeholder-color); }
    .legal-back { color: var(--box-text); text-decoration: none; }
    .legal-back:hover { color: #fff; text-decoration: underline; }
  </style>
</head>

<body class="bg-dark">
  <header class="legal-header d-flex justify-content-between align-items-center px-4 px-md-5">
    <a class="navbar-brand fs-20 text-white fw-semibold" href="LandingPageMovie.php">Cine<span class="db-text-primary">Vault</span></a>
    <a class="legal-back" href="LandingPageMovie.php"><i class="fa-solid fa-chevron-left me-1"></i> Back to home</a>
  </header>
  <main class="legal-wrap">
    <?php
}

function cv_legal_page_end()
{
    ?>
  </main>
  <footer class="footer-lp">
    <?php echo cv_site_footer_links(''); ?>
  </footer>
</body>

</html>
    <?php
}
