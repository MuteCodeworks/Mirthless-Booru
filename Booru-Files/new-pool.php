<!DOCTYPE html>
<html>
	<head>
		<title>
			<?php
				include 'config.php';
				echo "$title: new pool";
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
			<a href="tags.php">Tags</a>
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
	if(!isset($_POST['name'])){
?>
<form action="new-pool.php" method="POST" enctype="multipart/form-data">
	Name:<br /><input id="namebox" name="name" type="text" /><br />
	<br /><button id="uploadbutton" type="submit" name="btn-up">Make Pool</button>
	<!--<input id="uploadbutton" type="submit" value="Upload" /><br />-->
</form>
<?php
  }
	if(isset($_POST['btn-up'])) {

		$poolname = $_POST['name'];
		if($poolname==''){
			echo "Pool Must Have A Name";
			$didit = false;
		}
		else{
			$querypool = "INSERT INTO pools VALUES ( pool_id , \"$poolname\" , 0 , NOW() )";

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
?>
</div>
</div>
</body>
</html>
