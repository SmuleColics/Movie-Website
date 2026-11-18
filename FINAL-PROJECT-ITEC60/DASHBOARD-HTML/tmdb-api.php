<?php
// Admin-only JSON endpoint for the "Search TMDB" box on the add/edit movie form.
//   ?action=search&q=Moana            -> list of matching movies and series
//   ?action=details&type=movie&id=123 -> details shaped for the form
// The security setup already requires a signed-in admin for this folder.
include '../includes/db-connection.php';
require_once '../includes/tmdb.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

try {
    if ($action === 'search') {
        $q = trim((string) ($_GET['q'] ?? ''));
        if (mb_strlen($q) < 2) {
            echo json_encode(['results' => []]);
            exit;
        }
        echo json_encode(['results' => tmdb_search($q)]);
    } elseif ($action === 'details') {
        $type = ($_GET['type'] ?? '') === 'tv' ? 'tv' : 'movie';
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            throw new TmdbException('Missing TMDB id.');
        }
        echo json_encode(['details' => tmdb_details($con, $type, $id)]);
    } elseif ($action === 'status') {
        echo json_encode(['configured' => tmdb_configured()]);
    } else {
        http_response_code(422);
        echo json_encode(['error' => 'Unknown action.']);
    }
} catch (TmdbException $ex) {
    http_response_code(502);
    echo json_encode(['error' => $ex->getMessage()]);
}
