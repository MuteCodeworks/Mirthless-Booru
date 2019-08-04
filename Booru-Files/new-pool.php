<!DOCTYPE html>
<html>
	<head>
		<title>
			<?php
				include 'config.php';
				echo "$title: new pool";
			?>
		</title>
		<link rel="stylesheet" type="text/css" href="css/style.css" />
		<link rel="stylesheet" type="text/css" href="css/buttons.css" />
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
			<a id='button-dark-2' href="index.php">Home</a>
			<a id='button-dark-2' href="search-post.php">Posts</a>
			<a id='button-dark-2' href="tags.php">Tags</a>
			<a id='button-dark-2' href="search-pool.php">Pools</a>
			<a id='button-dark-2' href="upload.php">Upload</a>
			<a id='button-dark-2' href="about.php">About</a>
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
			<input id="button-dark-2" type="submit" value="Search" />
		</div>
	</form>
</div>
<div id="content">
<?php
	if(!isset($_POST['name'])){
?>
<form action="new-pool.php" method="POST" enctype="multipart/form-data">
	Name:<br /><input name="name" type="text" /><br />
	<br /><button id="button-light-2" type="submit" name="btn-up">Make Pool</button>
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
			$querypool = "INSERT INTO pools VALUES ( pool_id , \"$poolname\" , 0 , NOW(3) )";

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
