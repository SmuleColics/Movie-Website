<?php
// Set the directory where your videos are stored
$type = isset($_GET['type']) ? $_GET['type'] : 'top10'; // default to top10 if not set
if ($type === 'trend') {
    $videoDir = "../DASHBOARD-HTML/TREND_VIDEOS/";
} else {
    $videoDir = "../DASHBOARD-HTML/TOP10_VIDEOS/";
}

// Get the video filename from the GET parameter
$videoFile = isset($_GET['video']) ? basename($_GET['video']) : '';

// Build the expected full path (for server-side checks)
$fullPath = $videoDir . $videoFile;

// Only allow playback if the file exists and the extension is .mp4
if ($videoFile && preg_match('/\.mp4$/i', $videoFile) && file_exists($fullPath)) {
    $videoPath = $fullPath;
} else {
    $videoPath = "default.mp4"; // fallback video
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