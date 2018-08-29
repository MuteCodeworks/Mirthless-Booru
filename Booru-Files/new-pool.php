<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
	<head>
		<title>
			<?php
				include 'config.php';
				echo "$title";
				include('./blob_data_as_file_stream.php');
			?>
		</title>
		<link rel="stylesheet" type="text/css" href="style.css" />
		<link rel="favorite icon" href="favicon.png" />
	</head>
<body id="upload">
	<?php
		$link = mysqli_connect($mysql_host, $mysql_user, $mysql_password) or die('Could not connect: ' . mysqli_error($link));
		mysqli_select_db($link , $mysql_database) or die('Could not select database');
	?>
	<div id="header">
		<span id="title" style="font-size: 25px; font-weight: bold"><?php echo "$title"; ?></span>
		<div id="navbar">
			<a href="index.php">Home</a>
			<a href="search-post.php">Posts</a>
			<a href="search-pool.php">Pools</a>
			<a href="upload.php">Upload</a>
			<a href="about.php">About</a>
		</div>
	</div>
<div id="sidebar">
	<form action="search.php" method="get">
		<div id="searcharea">
			<input id="searchbox" name="q" size="22" type="text"
			<?php
				if(isset($_GET['q']))
				{
					echo "value=\"$_GET[q]\"";
				}
			?>/><br />
			<input id="button" type="submit" value="Search" />
		</div>
	</form>
</div>
<div id="content">
<?php
	if(!(isset($_POST['name']) && isset($_POST['tags']))){
?>
<form action="new-pool.php" method="POST" enctype="multipart/form-data">
	Name:<br /><input id="namebox" name="name" type="text" /><br />
	Rating:<br /><input type="radio" name="rating" value="safe">Safe
	<input type="radio" name="rating" value="questionable">Questionable
	<input type="radio" name="rating" value="explicit">Explicit
	<br /><button id="uploadbutton" type="submit" name="btn-up">Make Pool</button>
	<!--<input id="uploadbutton" type="submit" value="Upload" /><br />-->
</form>
<?php
  }
	if(isset($_POST['btn-up'])&&isset($_POST['rating'])){
		
		$poolname = $_POST['name'];
		if($poolname==''){
			echo "Pool Must Have A Name";
			$didit = false;
		}
		else{
		
			$rating = $_POST['rating'];
			
			$idcheck = mysqli_query($link , "select max(poolid) from pools ") or die(mysqli_error($link));
			$row = mysqli_fetch_array($idcheck);
			$id = (int) $row['max(poolid)'];
			
			$id = $id +1;
			$postids = " ";
			
			$querypool = "INSERT INTO pools VALUES ( $id , '$poolname' , '$postids' , '$rating' , NOW() , 0 )";
			mysqli_query($link , $querypool) or die(mysqli_error($link));
			$didit = true;
		}
	}
	else{
		$didit = false;
	}
	if($didit){
		echo "Pool Created Successfully";
	}
	if(isset($_POST['btn-up'])&&!isset($_POST['rating'])){
		echo "Please select a rating";
	}
?>
</div>
</div>
</body>
</html>
