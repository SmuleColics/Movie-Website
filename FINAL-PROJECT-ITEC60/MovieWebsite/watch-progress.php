<?php
// Saves where a member is in a movie or episode, for "Continue Watching".
// POST (with the CSRF token): type=movie_series|episode, id, position, duration.
// Members only: the security setup already requires a signed-in member here,
// and every POST is CSRF-checked before this code runs.
include '../includes/db-connection.php';

header('Content-Type: application/json');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'POST only']);
    exit;
}

$member = (int) ($_SESSION['user_id'] ?? 0);
$type = $_POST['type'] ?? '';
$id = (int) ($_POST['id'] ?? 0);
$position = max(0, (int) round((float) ($_POST['position'] ?? 0)));
$duration = max(0, (int) round((float) ($_POST['duration'] ?? 0)));

if (!$member || $id <= 0 || !in_array($type, ['movie_series', 'top10', 'episode'], true) || $duration <= 0) {
    http_response_code(422);
    echo json_encode(['error' => 'Bad request']);
    exit;
}
$position = min($position, $duration);

if ($type === 'episode') {
    $stmt = $con->prepare('SELECT s.movie_series_id FROM tbl_movie_series_episodes e
        JOIN tbl_movie_series_seasons s ON s.season_id = e.season_id WHERE e.episode_id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$row) {
        http_response_code(404);
        echo json_encode(['error' => 'Not found']);
        exit;
    }
    $titleId = (int) $row['movie_series_id'];
    $episodeId = $id;

    $stmt = $con->prepare('INSERT INTO tbl_episode_progress (signup_id, episode_id, position_sec, duration_sec)
        VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE position_sec = VALUES(position_sec), duration_sec = VALUES(duration_sec)');
    $stmt->bind_param('iiii', $member, $episodeId, $position, $duration);
    $stmt->execute();
    $stmt->close();
} else {
    $stmt = $con->prepare('SELECT movie_series_id FROM tbl_movie_series WHERE movie_series_id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $exists = (bool) $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$exists) {
        http_response_code(404);
        echo json_encode(['error' => 'Not found']);
        exit;
    }
    $titleId = $id;
    $episodeId = null;
}

$stmt = $con->prepare('INSERT INTO tbl_watch_progress (signup_id, movie_series_id, episode_id, position_sec, duration_sec)
    VALUES (?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE episode_id = VALUES(episode_id), position_sec = VALUES(position_sec), duration_sec = VALUES(duration_sec), updated_at = NOW()');
$stmt->bind_param('iiiii', $member, $titleId, $episodeId, $position, $duration);
$stmt->execute();
$stmt->close();

echo json_encode(['ok' => true]);
