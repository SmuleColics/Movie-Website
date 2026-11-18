<?php
include 'SignIn/db-con.php';
require 'legal-layout.php';

cv_legal_page_start('Terms of Service');
?>
<h1>Terms of Service</h1>
<p class="legal-updated">Last updated: November 18, 2026</p>

<p>These terms explain the rules for using CineVault. By creating an account or using the site, you agree to them.
  If you do not agree, please do not use CineVault.</p>

<h2>1. The service</h2>
<p>CineVault is a free website for browsing and watching classic movies and TV series. Every title is a full-length
  film or episode that is in the public domain or shared under an open license. There is no fee to sign up or to
  watch. We may add, change or remove titles and features at any time.</p>

<h2>2. Your account</h2>
<ul>
  <li>You must give your real name, a working mobile number and an email address you can access.</li>
  <li>Keep your password private. You are responsible for anything done through your account.</li>
  <li>One account per person. Do not share your account or sign in as someone else.</li>
  <li>If you are under 18, use CineVault only with a parent or guardian's permission. Each title shows an age
    rating. Please respect it.</li>
</ul>

<h2>3. What you may and may not do</h2>
<p>You may watch titles for your own personal, non-commercial viewing. You may not:</p>
<ul>
  <li>copy CineVault's own design, code or member data, or present the site as your own;</li>
  <li>try to get into accounts, pages or data that are not yours, including the admin area;</li>
  <li>upload or send harmful code, or try to overload, break or bypass the site's security;</li>
  <li>use bots or scripts to sign up, sign in or collect information from the site.</li>
</ul>

<h2>4. Content</h2>
<p>The videos stream from the Internet Archive (archive.org). They are in the public domain or shared under an open
  license such as Creative Commons; where a license asks for credit, the makers are credited on the title. Movie
  details, posters and title images come from TMDB. This product uses the TMDB API but is not endorsed or certified
  by TMDB. Posters and descriptions belong to their respective owners.</p>

<h2>5. Archiving accounts</h2>
<p>CineVault administrators may archive an account that breaks these terms or is used to harm the site or other
  members. An archived account cannot sign in. Administrators can restore it.</p>

<h2>6. No warranty</h2>
<p>CineVault is provided "as is". We try to keep it running and accurate, but we do not promise it will always be
  available, error-free, or that every title will stay in the library.</p>

<h2>7. Limitation of liability</h2>
<p>To the extent the law allows, CineVault and its administrators are not liable for indirect or incidental loss
  that comes from using, or not being able to use, the site.</p>

<h2>8. Privacy</h2>
<p>How we handle your information is explained in our <a href="privacy.php">Privacy Policy</a>.</p>

<h2>9. Changes to these terms</h2>
<p>We may update these terms. The date at the top shows the latest version. If you keep using CineVault after a
  change, you accept the updated terms.</p>

<h2>10. Governing law</h2>
<p>These terms follow the laws of the Republic of the Philippines.</p>

<h2>11. Contact</h2>
<p>Questions about these terms? Please <?php echo cv_contact_line(); ?>.</p>
<?php
cv_legal_page_end();
