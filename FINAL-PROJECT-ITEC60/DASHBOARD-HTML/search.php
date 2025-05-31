<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include '../includes/db-connection.php'; // $con is your mysqli connection

header('Content-Type: application/json');

$q = trim($_GET['q'] ?? '');
$results = [];

if ($q) {
    // Users by email
    $stmt1 = $con->prepare("SELECT signup_email FROM tbl_signup_acc WHERE signup_email LIKE CONCAT('%', ?, '%') LIMIT 7");
    $stmt1->bind_param("s", $q);
    $stmt1->execute();
    $res1 = $stmt1->get_result();
    while ($row = $res1->fetch_assoc()) {
        $results[] = [
            'type' => 'user',
            'value' => $row['signup_email'],
            'url'   => 'signup-accounts.php?email=' . urlencode($row['signup_email'])
        ];
    }
    $stmt1->close();

    // Payments by reference number
    $stmt2 = $con->prepare("SELECT reference_no FROM tbl_payment WHERE reference_no LIKE CONCAT('%', ?, '%') LIMIT 7");
    $stmt2->bind_param("s", $q);
    $stmt2->execute();
    $res2 = $stmt2->get_result();
    while ($row = $res2->fetch_assoc()) {
        $results[] = [
            'type' => 'payment',
            'value' => $row['reference_no'],
            'url'   => 'signup-accounts.php?ref=' . urlencode($row['reference_no'])
        ];
    }
    $stmt2->close();

    $stmt3 = $con->prepare("SELECT title FROM tbl_movie_series WHERE title LIKE CONCAT('%', ?, '%') LIMIT 7");
    $stmt3->bind_param("s", $q);
    $stmt3->execute();
    $res3 = $stmt3->get_result();
    while ($row = $res3->fetch_assoc()) {
        $results[] = [
            'type' => 'movie',
            'value' => $row['title'],
            'url'   => 'web-movies.php?title=' . urlencode($row['title'])
        ];
    }
    $stmt3->close();
}

echo json_encode($results);