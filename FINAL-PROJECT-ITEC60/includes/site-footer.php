<?php
/*
 * Footer links shared by the landing page, sign in, sign up and legal pages.
 * $toLanding is the relative path from the current page to LANDING-PAGE/,
 * e.g. '' on the landing page itself, '../' inside LANDING-PAGE/SignIn/.
 */
function cv_site_footer_links($toLanding = '')
{
    $l = $toLanding;
    $year = date('Y');
    return <<<HTML
      <div class="row db-text-sec cv-footer-links">
        <div class="col-6 col-md-4 mb-3">
          <p class="cv-footer-title">Explore</p>
          <div class="pb-2"><a class="color-light-white" href="{$l}LandingPageMovie.php">Home</a></div>
          <div class="pb-2"><a class="color-light-white" href="{$l}LandingPageMovie.php#how-it-works">How it Works</a></div>
          <div class="pb-2"><a class="color-light-white" href="{$l}LandingPageMovie.php#faq">FAQ</a></div>
        </div>
        <div class="col-6 col-md-4 mb-3">
          <p class="cv-footer-title">Account</p>
          <div class="pb-2"><a class="color-light-white" href="{$l}SignIn/LP-SignIn.php">Sign In</a></div>
          <div class="pb-2"><a class="color-light-white" href="{$l}SignIn/LP-SignUp.php">Create Account</a></div>
          <div class="pb-2"><a class="color-light-white" href="{$l}SignIn/forgot-pass.php">Forgot Password</a></div>
        </div>
        <div class="col-6 col-md-4 mb-3">
          <p class="cv-footer-title">Legal</p>
          <div class="pb-2"><a class="color-light-white" href="{$l}terms.php">Terms of Service</a></div>
          <div class="pb-2"><a class="color-light-white" href="{$l}privacy.php">Privacy Policy</a></div>
        </div>
        <p class="pt-4 mb-0 color-light-white">&copy; {$year} CineVault Philippines</p>
      </div>
HTML;
}
