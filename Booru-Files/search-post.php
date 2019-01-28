<!DOCTYPE html>
<html>
	<head>
		<title>
			<?php

			include 'config.php';
			include 'functions/display-post.php';
			include 'functions/search.php';
			include 'functions/page-navigate.php';
			echo "$title";
			$post_limit = 60;
			if(isset($_GET['q']))
			{
				$titleext = $_GET['q'];
				echo " - $titleext";
			}
			?>
		</title>
		<link rel="stylesheet" type="text/css" href="css/style.css" />
		<link rel="stylesheet" type="text/css" href="css/buttons.css" />
		<link rel="favorite icon" href="favicon.png" />
	</head>
	<body class="search">
		<?php
		error_reporting(E_ALL);
		$link = mysqli_connect($mysql_host, $mysql_user, $mysql_password) or die('Could not connect: ' . mysqli_error($link));
		mysqli_select_db($link , $mysql_database) or die('Could not select database');
		?>
		<div id="header">
			<span id="title" style="font-size: 25px; font-weight: bold"><?php echo "$title"; ?></span>
			<div id="navbar">
				<a id='button-dark-2' href="./">Home</a>
				<a id='button-dark-2' href="search-post.php">Posts</a>
				<a id='button-dark-2' href="tags.php">Tags</a>
				<a id='button-dark-2' href="search-pool.php">Pools</a>
				<a id='button-dark-2' href="upload.php">Upload</a>
				<a id='button-dark-2' href="about.php">About</a>
			</div>
		</div>
		<div id="sidebar">
			<form action="search-post.php" method="GET">
				<div id="searcharea">
					<input id="searchbox" style="width: 96%" name="q" type="text"
						<?php

						if(isset($_GET['q'])){
						echo "value=\"$_GET[q]\"";
						}
						?>/>
					<br />
					<input id="button-dark-2" type="submit" value="Search" />
				</div>
			</form>
		</div>
	<div id="content">
		<?php

		if(isset($_GET['q'])&&$_GET['q']!=''){
			$query = search($_GET['q'],'TAG');
			$query = "SELECT idnum , thumb , hash FROM postdata db , tagmap tm , tags t WHERE tm.tag_id = t.id AND db.idnum = tm.post_id GROUP BY db.idnum HAVING " . $query;
			$numimagesquery = "SELECT COUNT(*) FROM( " . $query.") as table_#";
		}
		else{
			$query = "SELECT idnum ,thumb , hash FROM postdata ";
			$numimagesquery = "SELECT COUNT(*) FROM postdata";
		}
		$query .= "ORDER BY date DESC ";

		if(isset($_GET['p'])){
			$page = $_GET['p'];
			$query_limit = ($page-1) * $post_limit;
		}
		else{
			$page = 1;
			$query_limit = 0;
		}
		$query .= "LIMIT $query_limit , $post_limit ";
		$numimagesres = mysqli_query($link , $numimagesquery) or die(mysqli_error($link));
		$numimagesarr = mysqli_fetch_array($numimagesres);
		$numimages = $numimagesarr[0];
		if($numimages == 0){
			echo "<h2>Sorry! Nothing tagged with your search terms!</h2>\n";
		}
		else{
			echo "\n<br />\n<div id=\"thumbs\">\n";
			$result = mysqli_query($link , $query) or die(mysqli_error($link));
			$row = mysqli_fetch_array($result);

			while($row){
				$row = display_post($link , $metaterms , $result, $row);
			}
			echo "</div>\n<br /><span id=\"pages\">\n";


			$numpages = ceil($numimages / $post_limit);
			if($numpages != 0){
				$pass = array('current_page' => $page, 'php' => 'search-post.php', 'max_page' => $numpages, 'get' => $_GET,);
				page_navigate( $pass );
			}
		echo "</span>";
		}
		?>
		</div>
	</body>
</html>
