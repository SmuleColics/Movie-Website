<?php
include '../includes/db-connection.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$type = isset($_GET['type']) ? $_GET['type'] : '';

if ($id && $type === 'movie_series') {
    $result = mysqli_query($con, "SELECT 
        g1.genre_name AS genre_1, 
        g2.genre_name AS genre_2, 
        g3.genre_name AS genre_3 
        FROM tbl_movie_series ms
        LEFT JOIN tbl_movie_series_genre g1 ON ms.genre_id1 = g1.genre_id
        LEFT JOIN tbl_movie_series_genre g2 ON ms.genre_id2 = g2.genre_id
        LEFT JOIN tbl_movie_series_genre g3 ON ms.genre_id3 = g3.genre_id
        WHERE ms.movie_series_id = $id LIMIT 1
    ");
    if ($row = mysqli_fetch_assoc($result)) {
        // Get the first available genre
        $genre = $row['genre_1'] ?: ($row['genre_2'] ?: $row['genre_3']);
        if ($genre) {
            // Get current preferred genres from cookie
            $preferred = [];
            if (isset($_COOKIE['preferred_genres']) && $_COOKIE['preferred_genres'] !== '') {
                $preferred = array_map('trim', explode(',', $_COOKIE['preferred_genres']));
            }
            // Remove if already present
            $preferred = array_filter($preferred, function($g) use ($genre) {
                return $g !== $genre;
            });
            // Add new genre at the end
            $preferred[] = $genre;
            // Keep only last 3
            if (count($preferred) > 3) {
                $preferred = array_slice($preferred, -3);
            }
            setcookie("preferred_genres", implode(',', $preferred), time() + 60 * 60 * 24 * 30, "/");
        }
    }
}

// Which video to play. For movies, series and episodes the video is looked
// up by id in the database (an uploaded file or a free archive.org stream),
// so the link can't be pointed at some other file or site.
$type = isset($_GET['type']) ? (string) $_GET['type'] : 'movie_series';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$videoPath = '';
$videoTitle = 'CineVault';
$isStream = false;
$parentId = 0;
$member = (int) ($_SESSION['user_id'] ?? 0);

// "Play" on a series opens the episode the member was last watching, or
// the first episode, so progress is kept per episode.
if ($id && ($type === 'movie_series' || $type === 'top10')) {
    $stmt = $con->prepare("SELECT
            (SELECT wp.episode_id FROM tbl_watch_progress wp WHERE wp.signup_id = ? AND wp.movie_series_id = m.movie_series_id) AS last_ep,
            (SELECT e.episode_id FROM tbl_movie_series_seasons s JOIN tbl_movie_series_episodes e ON e.season_id = s.season_id
              WHERE s.movie_series_id = m.movie_series_id ORDER BY s.season_number, e.episode_id LIMIT 1) AS first_ep
        FROM tbl_movie_series m WHERE m.movie_series_id = ? AND m.category = 'Series'");
    $stmt->bind_param('ii', $member, $id);
    $stmt->execute();
    $series = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $goTo = $series ? (int) ($series['last_ep'] ?: $series['first_ep']) : 0;
    if ($goTo) {
        header('Location: play-vid.php?type=episode&id=' . $goTo);
        exit;
    }
}

if ($id && ($type === 'movie_series' || $type === 'top10')) {
    $stmt = $con->prepare('SELECT title, video FROM tbl_movie_series WHERE movie_series_id = ? LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    if ($row = $stmt->get_result()->fetch_assoc()) {
        $videoTitle = $row['title'];
        $videoPath = cv_video_src($row['video'], 'MOVIE_SERIES_VIDEO');
        $isStream = cv_is_stream_url($row['video']);
        $parentId = $id;
    }
    $stmt->close();
} elseif ($id && $type === 'episode') {
    $stmt = $con->prepare('SELECT e.episode_title, e.episode_video, s.movie_series_id, m.title
        FROM tbl_movie_series_episodes e
        JOIN tbl_movie_series_seasons s ON s.season_id = e.season_id
        JOIN tbl_movie_series m ON m.movie_series_id = s.movie_series_id
        WHERE e.episode_id = ? LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    if ($row = $stmt->get_result()->fetch_assoc()) {
        $videoTitle = $row['title'] . ': ' . $row['episode_title'];
        $videoPath = cv_video_src($row['episode_video'], 'MOVIE_SERIES_EPISODE');
        $isStream = cv_is_stream_url($row['episode_video']);
        $parentId = (int) $row['movie_series_id'];
    }
    $stmt->close();
} elseif ($type === 'trend' && isset($_GET['video'])) {
    $videoPath = '../DASHBOARD-HTML/TREND_VIDEOS/' . basename((string) $_GET['video']);
}

// Local files must exist; streams are checked by cv_is_stream_url().
if ($videoPath !== '' && !$isStream && !(preg_match('/\.(mp4|webm|m4v)$/i', $videoPath) && is_file($videoPath))) {
    $videoPath = '';
}

if ($videoPath !== '') {
    if ($type === 'episode') {
        $stmt = $con->prepare('UPDATE tbl_movie_series_episodes SET views = views + 1 WHERE episode_id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }
    if ($parentId) {
        $stmt = $con->prepare('UPDATE tbl_movie_series SET views = views + 1 WHERE movie_series_id = ?');
        $stmt->bind_param('i', $parentId);
        $stmt->execute();
        $stmt->close();
    }
} else {
    $videoPath = 'default.mp4';
}

// Where this member stopped last time (Continue Watching).
$resumeAt = 0;
$trackType = $type === 'episode' ? 'episode' : 'movie_series';
$trackId = $type === 'episode' ? $id : $parentId;
if ($member && $trackId && empty($_GET['restart'])) {
    if ($trackType === 'episode') {
        $stmt = $con->prepare('SELECT position_sec, duration_sec FROM tbl_episode_progress WHERE signup_id = ? AND episode_id = ?');
    } else {
        $stmt = $con->prepare('SELECT position_sec, duration_sec FROM tbl_watch_progress WHERE signup_id = ? AND movie_series_id = ? AND episode_id IS NULL');
    }
    $stmt->bind_param('ii', $member, $trackId);
    $stmt->execute();
    $p = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    // Resume unless they had barely started or had reached the end credits.
    if ($p && $p['position_sec'] >= 30 && $p['position_sec'] < $p['duration_sec'] - 90) {
        $resumeAt = (int) $p['position_sec'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo e($videoTitle); ?> | CineVault</title>
  <link rel="icon" href="../MOVIE-IMG/HEADER-IMG/CINEVAULT-LOGO.svg">
  <style>
    body { margin: 0; padding: 0; overflow: hidden; background-color: #000; font-family: 'Poppins', Arial, sans-serif; }
    .video-container { width: 100vw; height: 100vh; }
    video { width: 100%; height: 100%; object-fit: contain; background: #000; }
    .cv-player-bar { position: fixed; top: 0; left: 0; right: 0; display: flex; align-items: center; gap: 14px; padding: 14px 18px;
      background: linear-gradient(to bottom, rgba(0,0,0,.75), rgba(0,0,0,0)); color: #fff; z-index: 5; transition: opacity .3s; }
    .cv-player-bar a { color: #fff; text-decoration: none; font-size: 15px; padding: 6px 12px; border-radius: 999px; background: rgba(255,255,255,.12); }
    .cv-player-bar a:hover { background: rgba(255,255,255,.22); }
    .cv-player-title { font-size: 16px; font-weight: 600; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .cv-player-credit { margin-left: auto; font-size: 12px; color: #c9d1d9; white-space: nowrap; }
    body.idle .cv-player-bar { opacity: 0; }
    .cv-resume { position: fixed; left: 50%; bottom: 84px; transform: translateX(-50%); display: flex; align-items: center; gap: 12px;
      padding: 10px 16px; border-radius: 999px; background: rgba(31, 26, 20, .92); border: 1px solid #3D3326; color: #F1E8D4;
      font-size: 14px; z-index: 6; transition: opacity .4s; }
    .cv-resume button { background: #C9A24B; color: #1A140C; border: 0; border-radius: 999px; padding: 5px 12px; font: inherit; font-weight: 600; cursor: pointer; }
    .cv-resume.gone { opacity: 0; pointer-events: none; }
  </style>
</head>
<body>
  <div class="cv-player-bar">
    <a href="javascript:history.length > 1 ? history.back() : location.assign('FirstProject.php')">&larr; Back</a>
    <span class="cv-player-title"><?php echo e($videoTitle); ?></span>
    <?php if ($isStream): ?>
      <span class="cv-player-credit">Public domain, streaming from the Internet Archive</span>
    <?php endif; ?>
  </div>
  <div class="video-container">
    <video class="video" id="cv-video" autoplay controls playsinline>
      <source src="<?php echo e($videoPath); ?>" type="video/mp4">
      Your browser does not support the video tag.
    </video>
  </div>
  <?php if ($resumeAt): ?>
    <div class="cv-resume" id="cv-resume">
      <span>Resumed from <?php echo e(sprintf('%d:%02d', intdiv($resumeAt, 60), $resumeAt % 60)); ?></span>
      <button type="button" id="cv-restart">Start over</button>
    </div>
  <?php endif; ?>
  <script>
    // Continue Watching: resume where the member stopped, and save the spot
    // every 15 seconds, on pause, and when leaving the page.
    (function () {
      const video = document.getElementById('cv-video');
      const resumeAt = <?php echo (int) $resumeAt; ?>;
      const track = { type: <?php echo json_encode($trackType); ?>, id: <?php echo (int) $trackId; ?> };
      const token = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
      if (!video || !track.id || !<?php echo $member ? 'true' : 'false'; ?>) return;

      if (resumeAt > 0) {
        video.addEventListener('loadedmetadata', () => { if (video.currentTime < 5) video.currentTime = resumeAt; }, { once: true });
        const box = document.getElementById('cv-resume');
        document.getElementById('cv-restart').onclick = () => { video.currentTime = 0; video.play(); box.classList.add('gone'); };
        setTimeout(() => box && box.classList.add('gone'), 8000);
      }

      let lastSent = -1;
      function save(useBeacon) {
        const pos = Math.floor(video.currentTime || 0);
        const dur = Math.floor(video.duration || 0);
        if (!dur || !isFinite(dur) || pos === lastSent) return;
        lastSent = pos;
        const body = new FormData();
        body.append('csrf_token', token);
        body.append('type', track.type);
        body.append('id', track.id);
        body.append('position', pos);
        body.append('duration', dur);
        if (useBeacon && navigator.sendBeacon) {
          navigator.sendBeacon('watch-progress.php', body);
        } else {
          fetch('watch-progress.php', { method: 'POST', body, headers: { 'Accept': 'application/json' }, keepalive: true }).catch(() => {});
        }
      }
      setInterval(() => { if (!video.paused) save(false); }, 15000);
      video.addEventListener('pause', () => save(false));
      video.addEventListener('ended', () => save(false));
      window.addEventListener('pagehide', () => save(true));
      document.addEventListener('visibilitychange', () => { if (document.visibilityState === 'hidden') save(true); });
    })();
  </script>
  <script>
    // Hide the top bar while the movie plays and the mouse is still.
    (function () {
      let t;
      const wake = () => { document.body.classList.remove('idle'); clearTimeout(t); t = setTimeout(() => document.body.classList.add('idle'), 2500); };
      ['mousemove', 'touchstart', 'keydown'].forEach(ev => document.addEventListener(ev, wake));
      wake();
    })();
  </script>
</body>
</html>
