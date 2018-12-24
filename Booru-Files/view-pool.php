<!DOCTYPE html>
<html>
	<head>
	<?php
		include 'config.php';
		include 'functions/display-post.php';
		$link = mysqli_connect($mysql_host, $mysql_user, $mysql_password) or die('Could not connect: ' . mysqli_error($link));
		mysqli_select_db($link , $mysql_database) or die('Could not select database');
		if(isset($_GET['id'])) {
			$id = $_GET['id'];
			$query = "SELECT * FROM pools WHERE pool_id = '$id' LIMIT 1";
			$result = mysqli_query($link , $query) or die(mysqli_error($link));
			$query = "SELECT post_id , location FROM poolmap WHERE pool_id=$id";
			$pooldata = mysqli_query($link , $query) or die(mysqli_error($link));
			if($row = mysqli_fetch_array($result)) {
				$titleext = $row['name'];
			}
			else {
				$notfound = true;
				$titleext = "Not Found";
			}
		echo "<title>$title - $titleext</title>";
		}
	?>
	<link rel="stylesheet" type="text/css" href="style.css" />
	<link rel="favorite icon" href="favicon.png" />
	</head>
	<body id="view">
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
		<div id="divider">
		</div>
		<div id="poolview">
			<div id="poolcontent">
				<?php
					echo "<div id='plnme'>$row[name]</div><br />";
					$poolrow = mysqli_fetch_array($pooldata);
					if(mysqli_num_rows($pooldata)!=0){
						while($poolrow){
							$query = "SELECT * FROM postdata WHERE idnum=$poolrow[post_id]";
							$postq = mysqli_query($link , $query) or die(mysqli_error($link));
							if (!mysqli_num_rows($postq)==0){
								$row = mysqli_fetch_array($postq);
								display_post($link,$metaterms, $postq, $row);
							}
							$poolrow = mysqli_fetch_array($pooldata);
						}
					}
					else{
						echo "Nothing In Pool";
					}
				?>
			</div>
		</div>
		<div id="pooledit">
			<?php

			echo "<a id='controls' href='edit-pool.php?id=$id'>Edit</a>"

			?>
		</div>
	</body>
</html>
