<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
	<head>
		<title>
			<?php
			
			include 'config.php';
			echo "$title";
			
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
	<body class="search"> <!-- Which page style to use -->
		<?php //connect to DB
		$link = mysqli_connect($mysql_host, $mysql_user, $mysql_password) or die('Could not connect: ' . mysqli_error($link));
		mysqli_select_db($link , $mysql_database) or die('Could not select database');
		?>
		<div id="header"> <!-- Create webpage top  -->
			<span id="title" style="font-size: 25px; font-weight: bold"><?php echo "$title"; ?></span>
			<div id="navbar">
				<a href="./">Home</a>
				<a href="search-post.php">Posts</a>
				<a href="search-pool.php">Pools</a>
				<a href="upload.php">Upload</a>
				<a href="about.php">About</a>
			</div>
		</div>
		<div id="sidebar"> <!-- Create webpage sidbar  -->
			<form action="search-post.php" method="GET"> <!-- Enter search terms -->
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
	<div id="content"> <!-- Display webpage content  -->
		<?php // Begin Main Function
	
		if(isset($_GET['q'])&&$_GET['q']!=''){ // Check url for the value of "q"	
			$keywords = explode(" ", $_GET['q']);
			if(substr($keywords[0], 0, 1)=='-'){ // Check for negator on first tag
				$keywords[0] = substr($keywords[0], 1);
				$query = "WHERE tags NOT LIKE \"% $keywords[0] %\" ";
			}
			elseif(substr($keywords[0], 0, 1)=='~'){
				$keywords[0] = substr($keywords[0], 1);
				$query = "WHERE tags LIKE \"% $keywords[0] %\" ";
			}
			else{
				$query = "WHERE tags LIKE \"% $keywords[0] %\" ";
			}
			for($i = 1; $i < count($keywords); $i++){ // Check subsequent tags	
				if(substr($keywords[$i], 0, 1)=='-'){ // Check for negator
					$keywords[$i] = substr($keywords[$i], 1);
					$query .= "AND tags NOT LIKE \"% $keywords[$i] %\" ";
				}
				elseif(substr($keywords[$i], 0, 1)=='~'){ // Check for or
					$keywords[$i] = substr($keywords[$i], 1);
					$query .= "OR tags LIKE \"% $keywords[$i] %\" ";
				}
				else{
					$query .= "AND tags LIKE \"% $keywords[$i] %\" ";
				}
			}
		}
		else{
			$query = "";
		}
		$numimagesquery = "SELECT COUNT(*) FROM posts " . $query;
		$query = "SELECT idnum , tags , thumb , name FROM posts " . $query;
	
		//this serves no purpose, why is it here? it's the biggest and really the only problem left with the post search
	
		$query .= "ORDER BY date DESC ";
	
		if(isset($_GET['p'])){ // get page number
			$page = $_GET['p'] * 60;
		}
		else{
			$page = 0;
		}
		// this give the post limit per page
		$query .= "LIMIT ";
		$query .= "$page";
		$query .= ",60";
		$numimagesres = mysqli_query($link , $numimagesquery) or die(mysqli_error($link));
		$numimagesarr = mysqli_fetch_array($numimagesres);
		$numimages = $numimagesarr[0];
		if($numimages == 0){ // this checks to see if there are any posts, and if not says so and breaks
			echo "<h2>Sorry! Nothing tagged with your search terms!</h2>\n";
		}
		else{ //this displays the main page
	
			echo "\n<br />\n<div id=\"thumbs\">\n";
			$result = mysqli_query($link , $query) or die(mysqli_error($link));
			$row = mysqli_fetch_array($result);
			while($row){ //this iterates the posts
		
				$tags = substr($row['tags'], 1, strlen($row['tags']) -2);
				$dataquery = "SELECT rating , type FROM postdata WHERE idnum=$row[idnum]";
				$infoget = mysqli_query($link , $dataquery) or die(mysqli_error($link));
				$inforow = mysqli_fetch_array($infoget);
				$rating = $inforow['rating'];
				$type = $inforow['type'];
			
				echo "<div id='thumbcon'><span class='thumb'>";
				
				if(!file_exists("thumbs/$row[thumb]")&&$type=="mp3") {
					echo "<a href=\"view-post.php?id=$row[idnum]\"><img src=\"thumbs/mp3thumb.png\" alt=\"$tags\" title=\"$row[tags]\">";
				}
				else {
					echo "<a href=\"view-post.php?id=$row[idnum]\"><img src=\"thumbs/$row[thumb]\" alt=\"$tags\" title=\"$row[tags]\">";
				}
				if($type=='gif'or$type=='webm'or$type=='mp4'or$type=='mp3'or$type=='cbz'or$type=='cbr'){
					echo "<span id='animtag'>$type</span>";
				}
				echo "</a><br />";
				echo "<p id='bar' ";
				if($rating=='safe'){
					echo "style='inline;color:lightgreen;'>S</p>";
				}
				elseif($rating=='questionable'){
					echo "style='inline;color:yellow;'>Q</p>";
				}
				else{
					echo "style='inline;color:red;'>E</p>";
				}
			
				echo"</span></div>\n";
				$row = mysqli_fetch_array($result);
			}
			echo "</div>\n<br /><span id=\"pages\">\n";
		
			//everything after this is for changing pag numbers
		
			$numpages = floor($numimages / 60);
			$page /= 60;
			if($page != 0){
				echo "<a href=\"search.php?p=";
				echo $page - 1;
			
				if(isset($_GET['q'])){
				echo "&q=" . $_GET['q'];
				}
				if(isset($_GET['s'])){
					echo "&s=" . $_GET['s'];
					echo "\"><</a>\n";
				}
			}
			else{
				echo "<\n";
			}
			if($numpages != 0){
				for($i = $numpages - 5, $numprinted = 0; $i <= $numpages && $numprinted <= 10; $i++, $numprinted++)
				{
					if($i < 0){
						$i = 0;
					}
					if($i == $page){
						echo "$i\n";
					}
					else{
						echo "<a href=\"search.php?p=$i";
						if(isset($_GET['q'])){
							echo "&q=" . $_GET['q'];
						}
						if(isset($_GET['s'])){
							echo "&s=" . $_GET['s'];
						}
						echo "\">$i</a>\n";
					}
				}
			}
			else{
				echo "0\n";
			}
			if($numpages > $page){
				echo "<a href=\"search.php?p=";
				echo $page + 1;
			
				if(isset($_GET['q'])){
					echo "&q=" . $_GET['q'];
				}
				if(isset($_GET['s'])){
					echo "&s=" . $_GET['s'];
					echo "\">></a>\n";
				}
			}
			else{
				echo ">\n";
			}
		echo "</span>";
		}
		?>
		</div>
	</body>
</html>
