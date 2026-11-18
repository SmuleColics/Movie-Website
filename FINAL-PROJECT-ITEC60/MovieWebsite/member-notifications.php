<?php
// Marks a member's "new arrival" notifications as read. Called by shared/notifications.js
// with fetch() (POST + CSRF header). The security setup already requires a signed-in member.
include '../includes/db-connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'POST only']);
    exit;
}

$memberId = (int) $_SESSION['user_id'];
$action   = $_POST['action'] ?? '';

if ($action === 'read_all') {
    $stmt = $con->prepare(
        "INSERT IGNORE INTO tbl_member_notif_reads (signup_id, movie_series_id)
         SELECT ?, movie_series_id FROM tbl_movie_series"
    );
    $stmt->bind_param('i', $memberId);
    $stmt->execute();
    $stmt->close();
} elseif ($action === 'read_one') {
    $titleId = (int) ($_POST['id'] ?? 0);
    $stmt = $con->prepare("INSERT IGNORE INTO tbl_member_notif_reads (signup_id, movie_series_id) VALUES (?, ?)");
    $stmt->bind_param('ii', $memberId, $titleId);
    $stmt->execute();
    $stmt->close();
} else {
    http_response_code(422);
    echo json_encode(['error' => 'Unknown action']);
    exit;
}

echo json_encode(['ok' => true, 'unread' => cv_member_unread_arrivals($con, $memberId)]);
