<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
	<head>
		<title>
			<?php

			include 'config.php';
			include 'functions/display-post.php';
			include 'functions/search.php';
			echo "$title";
			$post_limit = 60;
			if(isset($_GET['q']))
			{
				$titleext = $_GET['q'];
				echo " - $titleext";
			}
			?>
		</title>
		<link rel="stylesheet" type="text/css" href="style.css" />
		<link rel="favorite icon" href="favicon.png" />
	</head>
	<body class="search">
		<?php //connect to DB
		error_reporting(E_ALL);
		$link = mysqli_connect($mysql_host, $mysql_user, $mysql_password) or die('Could not connect: ' . mysqli_error($link));
		mysqli_select_db($link , $mysql_database) or die('Could not select database');
		?>
		<div id="header">
			<span id="title" style="font-size: 25px; font-weight: bold"><?php echo "$title"; ?></span>
			<div id="navbar">
				<a href="./">Home</a>
				<a href="search-post.php">Posts</a>
				<a href="tags.php">Tags</a>
				<a href="search-pool.php">Pools</a>
				<a href="upload.php">Upload</a>
				<a href="about.php">About</a>
			</div>
		</div>
		<div id="sidebar">
			<form action="search-post.php" method="GET">
				<div id="searcharea">
					<input id="searchbox" style="width: 96%" name="q" type="text"
						<?php // Reinsert search terms

						if(isset($_GET['q'])){
						echo "value=\"$_GET[q]\"";
						}
						?>/>
					<br />
					<input id="button" type="submit" value="Search" />
				</div>
			</form>
		</div>
	<div id="content">
		<?php // Begin Main Function

		if(isset($_GET['q'])&&$_GET['q']!=''){ // Check url for the value of "q"
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
			$page = $_GET['p'] * $post_limit;
		}
		else{
			$page = 0;
		}
		$query .= "LIMIT $page , $post_limit ";
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


			$numpages = floor($numimages / $post_limit);
			$page /= $post_limit;
			if($numpages != 0){

				if(isset($_GET['p']) and $_GET['p']!=0){
					echo "<a href='search-post.php?";
					if(isset($_GET['q'])){
						echo "q=$_GET[q]&";
					}
					echo "p=0'>◄◄◄</a>";
				}

				if(isset($_GET['p'])){
					for( $i = 5 ; $i > 0 ; $i-- ){
						$bcknm = $_GET['p']-$i;
						$dispnm = $bcknm + 1;
						//if($bcknm > 0)
						if($bcknm <= -1){
							continue;
						}
						echo "<a href='search-post.php?";
						if(isset($_GET['q'])){
							echo "q=$_GET[q]&";
						}
						echo "p=$bcknm'>$dispnm</a>";

					}
				}

				if(isset($_GET['p'])){
					$dispnm = $_GET['p'] + 1;
				}
				else{
					$dispnm = 1;
				}
				echo "<div id='pagesid'>$dispnm</div>";

				if($numpages>1){
					for( $i = 0 ; $i < 5 ; $i++ ){
						if(isset($_GET['p'])){
							$bcknm = $_GET['p']+$i+1;
						}
						else{
							$bcknm = $i+1;
						}
						$dispnm = $bcknm + 1;
						//if($bcknm > 0)
						if($bcknm >= $numpages+1){
							continue;
						}
						echo "<a href='search-post.php?";
						if(isset($_GET['q'])){
							echo "q=$_GET[q]&";
						}
						echo "p=$bcknm'>$dispnm</a>";

					}
				}

				if(isset($_GET['p'])&&$_GET['p']!=$numpages){
					echo "<a href='search-post.php?";
					if(isset($_GET['q'])){
						echo "q=$_GET[q]&";
					}
					echo "p=$numpages'>►►►</a>";
				}
				if(!isset($_GET['p'])&&$numpages>=1){
					echo "<a href='search-post.php?";
					if(isset($_GET['q'])){
						echo "q=$_GET[q]&";
					}
					echo "p=$numpages'>►►►</a>";
				}

			}
		echo "</span>";
		}
		?>
		</div>
	</body>
</html>
