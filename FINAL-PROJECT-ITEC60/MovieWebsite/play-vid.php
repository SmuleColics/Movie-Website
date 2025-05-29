<?php
include '../includes/db-connection.php';

$type = isset($_GET['type']) ? $_GET['type'] : 'movie_series';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

switch ($type) {
    case 'trend':
        $videoDir = "../DASHBOARD-HTML/TREND_VIDEOS/";
        break;
    case 'top10':
    case 'movie_series':
        $videoDir = "../DASHBOARD-HTML/MOVIE_SERIES_VIDEO/";
        break;
    case 'episode':
        $videoDir = "../DASHBOARD-HTML/MOVIE_SERIES_EPISODE/";
        break;
    default:
        $videoDir = "../DASHBOARD-HTML/MOVIE_SERIES_VIDEO/";
        break;
}

$videoFile = isset($_GET['video']) ? basename($_GET['video']) : '';
$fullPath = $videoDir . $videoFile;

if ($videoFile && preg_match('/\.mp4$/i', $videoFile) && file_exists($fullPath)) {
    $videoPath = $fullPath;

    // Increment views (Option 1: Always increment parent on episode view)
    if ($type === 'movie_series' || $type === 'top10') {
        if ($id) {
            mysqli_query($con, "UPDATE tbl_movie_series SET views = views + 1 WHERE movie_series_id = $id");
        }
    } else if ($type === 'episode') {
        if ($id) {
            // Increment episode views
            mysqli_query($con, "UPDATE tbl_movie_series_episodes SET views = views + 1 WHERE episode_id = $id");
            // Also increment parent movie/series views
            $result = mysqli_query($con, "SELECT season_id FROM tbl_movie_series_episodes WHERE episode_id = $id");
            if ($result && $row = mysqli_fetch_assoc($result)) {
                $season_id = $row['season_id'];
                $result2 = mysqli_query($con, "SELECT movie_series_id FROM tbl_movie_series_seasons WHERE season_id = $season_id");
                if ($result2 && $row2 = mysqli_fetch_assoc($result2)) {
                    $movie_series_id = $row2['movie_series_id'];
                    mysqli_query($con, "UPDATE tbl_movie_series SET views = views + 1 WHERE movie_series_id = $movie_series_id");
                }
            }
        }
    }
} else {
    $videoPath = "default.mp4";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Play Video</title>
  <style>
    body { margin:0; padding:0; overflow: hidden; background-color: #1F2A37;}
    .video-container { width: 100vw; height: 100vh; }
    video { width: 100%; height: 100%; object-fit: cover; }
  </style>
</head>
<body>
  <div class="video-container">
    <video class="video" autoplay controls>
      <source src="<?php echo htmlspecialchars($videoPath); ?>" type="video/mp4">
      Your browser does not support the video tag.
    </video>
  </div>
</body>
</html>