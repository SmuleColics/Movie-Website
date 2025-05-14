<?php 
  include('db-con.php');
?>

<!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
  </head>
  <body>
    <?php 
      $select_query = mysqli_query($con, "SELECT file_poster FROM tbl_upload");

      while ($row = mysqli_fetch_assoc($select_query)) {
    ?>
    <!-- <img src="../../DASHBOARD-HTML/TRENDING_IMAGES/LandingPageWallpaper.jpg" alt="" style="width:500px; height:500px;"> -->
    <img src="../../DASHBOARD-HTML/TRENDING_IMAGES/<?php echo $row['file_poster']?>" alt="" style="width:500px; height:500px;">

    <a href="FINAL-PROJECT-ITEC60/LANDING-PAGE/LandingPageMovie.php">Click me</a>
    <?php } ?>
  </body>
</html>