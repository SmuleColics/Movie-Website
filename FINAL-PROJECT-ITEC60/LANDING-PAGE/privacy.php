<?php
include 'SignIn/db-con.php';
require 'legal-layout.php';

cv_legal_page_start('Privacy Policy');
?>
<h1>Privacy Policy</h1>
<p class="legal-updated">Last updated: November 18, 2026</p>

<p>This policy explains what information CineVault collects, why, and what you can do about it. We follow the Data
  Privacy Act of 2012 (Republic Act No. 10173) of the Philippines.</p>

<h2>1. What we collect</h2>
<ul>
  <li><strong>Account details you give us:</strong> first name, last name, mobile number, email address and password.
    Your password is stored only as a secure one-way hash. Nobody, including our administrators, can read it.</li>
  <li><strong>Account record:</strong> the date your account was created and whether it is active or archived.</li>
  <li><strong>Favorite genres:</strong> the 3 genres you pick, saved on your account so we can recommend titles on any
    device. You can change them any time with "Change my genres" on the home page.</li>
  <li><strong>Viewing counts:</strong> each time a title or episode is played, its total view count goes up by one.
    These totals are not linked to your name.</li>
  <li><strong>Security records:</strong> when a sign-in or password-reset attempt fails, we keep the email address
    used, your IP address and the time. We use these to stop password-guessing attacks and delete them once they are
    no longer needed.</li>
  <li><strong>Password reset codes:</strong> when you ask to reset your password, we store a scrambled copy of the code
    for 15 minutes.</li>
</ul>

<h2>2. Cookies</h2>
<ul>
  <li><strong>Session cookie:</strong> keeps you signed in while you browse. It is deleted when you sign out or close
    your browser.</li>
  <li><strong>cv_remember:</strong> only set if you tick "Remember me" when signing in. It holds a random code that
    keeps you signed in on that device for up to 30 days. We store only a scrambled copy of the code. It is removed
    when you sign out, and every saved sign-in is removed when you reset your password.</li>
  <li><strong>preferred_genres:</strong> remembers the genres of titles you recently played, so the home page can
    suggest similar titles. It stays on your device for 30 days.</li>
</ul>
<p>We do not use advertising or third-party tracking cookies.</p>
<p>When you play a title, the video streams to your browser directly from the Internet Archive (archive.org), so
  their servers see your IP address and the video you are watching, as with any website. We do not send them your
  name, email or any other account details. See the Internet Archive's own privacy policy for how they handle
  this.</p>

<h2>3. How we use your information</h2>
<ul>
  <li>to create your account and sign you in;</li>
  <li>to send you emails you asked for, such as password reset codes;</li>
  <li>to recommend titles and show which titles are popular;</li>
  <li>to keep the site and your account secure.</li>
</ul>

<h2>4. Who can see it</h2>
<p>CineVault administrators can see your name, mobile number, email address and join date so they can manage
  accounts. We do not sell, rent or trade your personal information. We share it only if the law requires us to.</p>

<h2>5. How long we keep it</h2>
<p>We keep your account details while your account exists. If an account is archived, its details stay in our records
  so it can be restored, unless you ask us to delete it.</p>

<h2>6. Your rights</h2>
<p>Under the Data Privacy Act you may ask to see the information we hold about you, have it corrected, or have it
  deleted. You can change your name, mobile number and email yourself on your profile page. For anything else, please
  <?php echo cv_contact_line(); ?>.</p>

<h2>7. Security</h2>
<p>We protect your information with hashed passwords, limits on repeated sign-in attempts, and checks on every form.
  No website is perfectly secure, so please use a password you do not use anywhere else.</p>

<h2>8. Children</h2>
<p>If you are under 18, please use CineVault only with a parent or guardian's permission.</p>

<h2>9. Changes to this policy</h2>
<p>We may update this policy. The date at the top shows the latest version.</p>

<h2>10. Contact</h2>
<p>Questions about your privacy? Please <?php echo cv_contact_line(); ?>.</p>
<?php
cv_legal_page_end();
