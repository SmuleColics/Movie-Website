<?php
// Marks admin notifications as read. Called from the bell with fetch() (POST + CSRF header).
include '../includes/db-connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'POST only']);
    exit;
}

$adminId = (int) $_SESSION['admin_id'];
$action  = $_POST['action'] ?? '';

if ($action === 'read_all') {
    $stmt = $con->prepare(
        "INSERT IGNORE INTO tbl_admin_notif_reads (admin_id, signup_id)
         SELECT ?, signup_id FROM tbl_signup_acc"
    );
    $stmt->bind_param('i', $adminId);
    $stmt->execute();
    $stmt->close();
} elseif ($action === 'read_one') {
    $signupId = (int) ($_POST['id'] ?? 0);
    $stmt = $con->prepare("INSERT IGNORE INTO tbl_admin_notif_reads (admin_id, signup_id) VALUES (?, ?)");
    $stmt->bind_param('ii', $adminId, $signupId);
    $stmt->execute();
    $stmt->close();
} else {
    http_response_code(422);
    echo json_encode(['error' => 'Unknown action']);
    exit;
}

$stmt = $con->prepare(
    "SELECT COUNT(*) AS n FROM tbl_signup_acc s
     LEFT JOIN tbl_admin_notif_reads r ON r.signup_id = s.signup_id AND r.admin_id = ?
     WHERE s.is_archived = 0 AND r.signup_id IS NULL"
);
$stmt->bind_param('i', $adminId);
$stmt->execute();
$unread = (int) $stmt->get_result()->fetch_assoc()['n'];
$stmt->close();

echo json_encode(['ok' => true, 'unread' => $unread]);
