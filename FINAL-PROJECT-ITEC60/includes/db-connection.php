<?php 
  $con = mysqli_connect("localhost", "root", "", "db_finalproject" );

  if (!$con) {
    die("Connection Error: " . mysqli_connect_error());
  }
?>
