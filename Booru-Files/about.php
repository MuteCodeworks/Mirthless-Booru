<!DOCTYPE html>
<html>
	<head>
		<title>
			<?php
			  include 'config.php';
			  echo "$title";
			?>
		</title>
			<link rel="stylesheet" type="text/css" href="css/style.css" />
			<link rel="stylesheet" type="text/css" href="css/buttons.css" />
			<link rel="favorite icon" href="favicon.png" />
		</title>
	</head>
<body id="main">
<h2>About <?php echo "$title"; ?></h2>
<div id="navbar">
  <a id='button-dark-2' href="index.php">Home</a>
  <a id='button-dark-2' href="search-post.php">Posts</a>
  <a id='button-dark-2' href="tags.php">Tags</a>
  <a id='button-dark-2' href="search-pool.php">Pools</a>
  <a id='button-dark-2' href="upload.php">Upload</a>
  <a id='button-dark-2' href="about.php">About</a>
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
