<?php
// Member sign out. Only accepts the confirmed POST from the sign-out popup
// (the security setup already checks its form token).
include 'db-con.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../LandingPageMovie.php');
    exit;
}

cv_forget_me($con); // also ends "Remember me" on this device
unset($_SESSION['user_id'], $_SESSION['user_email'], $_SESSION['is_logged_in'], $_SESSION['genre_picker_skipped']);
session_regenerate_id(true);

cv_flash('You have signed out. See you again soon!', 'info');
header('Location: ../LandingPageMovie.php');
exit;
