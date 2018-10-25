<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
<head>
<title>
<?php
  include 'config.php';
  echo "$title";
?>
</title>
<link rel="stylesheet" type="text/css" href="style.css" />
<link rel="favorite icon" href="favicon.png" />
</title>
</head>
<body id="main">
<h2>About <?php echo "$title"; ?></h2>
<div id="navbar">
  <a href="index.php">Home</a>
  <a href="search-post.php">Posts</a>
  <a href="tags.php">Tags</a>
  <a href="search-pool.php">Pools</a>
  <a href="upload.php">Upload</a>
  <a href="about.php">About</a>
</div><br />
<?php
  $link = mysqli_connect($mysql_host, $mysql_user, $mysql_password) or die('Could not connect: ' . mysqli_error($link));
  mysqli_select_db($link , $mysql_database) or die('Could not select database');  
  $result = mysqli_query($link , "SELECT COUNT(*) FROM postdata") or die(mysqli_error($link));
  $numimages = mysqli_fetch_array($result);
  echo "Total Number Of Entries In Database: $numimages[0]\n";
?>
</body>
</html>
