<?php
// Admin sign out. Only accepts the confirmed POST from the sign-out popup
// (the security setup already checks its form token and that an admin is signed in).
include '../includes/db-connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

unset($_SESSION['admin_id'], $_SESSION['admin_email'], $_SESSION['admin_logged_in']);
session_regenerate_id(true);

cv_flash('You have signed out of the admin side.', 'info');
header('Location: ../LANDING-PAGE/SignIn/LP-SignIn.php');
exit;
