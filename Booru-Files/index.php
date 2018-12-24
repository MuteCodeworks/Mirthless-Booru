<!DOCTYPE html>
<html>
	<?php
	
	include "config.php";
	
	$link = mysqli_connect($mysql_host, $mysql_user, $mysql_password) or die('Could not connect: ' . mysqli_error($link));
	mysqli_select_db($link , $mysql_database) or die('Could not select database');
	
	echo
		"<head>
			<title>$title</title>
			<link href='$stylesheet' rel='stylesheet' type='text/css'>
		</head>";
		
	?>
	<body>
		<div class="hanger">
		<span><?php echo "$title"; ?></span>
		</div>
		
		<div class="bbar">
			<div class="centerblock">
				<ul id='bar'>
					<li><a href="search-post.php">Posts</a></li>
					<li><a href="tags.php">Tags</a></li>
					<li><a href="search-pool.php">Pools</a></li>
					<li><a href="upload.php">Upload</a></li>
					<li><a href="about.php">About</a></li>
				</ul>
			</div>
		</div>
		
		<div id='imed'>
		<?php
			
			$querydat = "SELECT type , idnum FROM postdata WHERE type NOT LIKE '%swf%' AND type NOT LIKE '%txt%' AND type NOT LIKE '%mp3%' AND type NOT LIKE '%flac%' ORDER BY RAND() LIMIT 1";
			$resultdata = mysqli_query($link , $querydat) or die(mysqli_error($link));
			if (!mysqli_num_rows($resultdata)==0) {
					
			$displaydat = mysqli_fetch_array($resultdata);
			
			$query = "SELECT hash FROM postdata WHERE idnum=$displaydat[idnum]";
			
			$result = mysqli_query($link , $query) or die(mysqli_error($link));
			
			$diplaycon = mysqli_fetch_array($result);
			
			if($displaydat[0]=='webm'or$displaydat[0]=='mp4'){
				$vidimage = "video";
				$contype = $displaydat[0];
				$extdata = "loop muted autoplay ";
			}
			else{
				$vidimage = "img";
				$contype = $displaydat['type'];
				$extdata = "";
			}
			
			echo "<$vidimage src='$imagedir/$diplaycon[0]' $extdata type='$contype'>";
			}
		?>
		</div>
		
	</body>
	<footer>
		<p>Running Mirthless Booru</p>
	</footer>
</html>
