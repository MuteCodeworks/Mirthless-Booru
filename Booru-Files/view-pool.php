<!DOCTYPE html>
<html>
	<head>
	<?php
		include 'config.php';
		include 'functions/display-post.php';
		include 'functions/page-navigate.php';
		$max_pool = 30;
		$link = mysqli_connect($mysql_host, $mysql_user, $mysql_password) or die('Could not connect: ' . mysqli_error($link));
		mysqli_select_db($link , $mysql_database) or die('Could not select database');
		if(isset($_GET['id'])) {
			$id = $_GET['id'];
			$query = "SELECT * FROM pools WHERE pool_id = '$id' LIMIT 1";
			$result = mysqli_query($link , $query) or die(mysqli_error($link));
			$query = "SELECT post_id , location FROM poolmap WHERE pool_id=$id ";
			if(isset($_GET['p'])){
			$page = $_GET['p'];
				$query_limit = ($page-1) * $max_pool;
			}
			else{
				$page = 1;
				$query_limit = 0;
			}
			$query .= "LIMIT $query_limit , $max_pool ";
			$pooldata = mysqli_query($link , $query) or die(mysqli_error($link));
			$querynum = mysqli_query($link,"SELECT COUNT(*) AS count FROM poolmap WHERE pool_id=$id")or die(mysqli_error($link));
			$numposts = mysqli_fetch_array($querynum);
			$numposts = $numposts['count'];
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
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<link rel="stylesheet" type="text/css" href="css/buttons.css" />
	<link rel="favorite icon" href="favicon.png" />
	</head>
	<body id="view">
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
								display_post($link,$metaterms,$row['idnum'], $thumbdir);
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
		<?php
			$numpages = ceil($numposts / $max_pool);
			echo "<div id=\"pages\">\n";
			if($numpages != 0){
				$pass = array('current_page' => $page, 'php' =>'view-pool.php', 'max_page' => $numpages, 'get' =>$_GET,);
				page_navigate( $pass );
			}
			echo"</div>";
		?>
	</body>
</html>
