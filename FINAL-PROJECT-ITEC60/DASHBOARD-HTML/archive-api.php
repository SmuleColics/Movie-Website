<?php
// Admin-only JSON endpoint for the "Free full movie" box on the add/edit form.
//   ?action=search&q=Detour           -> public domain films on the Internet Archive
//   ?action=videos&id=Detour          -> the playable MP4 links of one item
// The security setup already requires a signed-in admin for this folder.
include '../includes/db-connection.php';
require_once '../includes/archive.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

try {
    if ($action === 'search') {
        $q = trim((string) ($_GET['q'] ?? ''));
        echo json_encode(['results' => mb_strlen($q) < 2 ? [] : archive_search($q)]);
    } elseif ($action === 'videos') {
        echo json_encode(['item' => archive_videos((string) ($_GET['id'] ?? ''))]);
    } else {
        http_response_code(422);
        echo json_encode(['error' => 'Unknown action.']);
    }
} catch (ArchiveException $ex) {
    http_response_code(502);
    echo json_encode(['error' => $ex->getMessage()]);
}
