<!DOCTYPE html>
<html>
		<head>
			<?php
				if(!file_exists('config.php')){
					header("Location:./install.php");
				}
				include "config.php";
	
				$link = mysqli_connect($mysql_host, $mysql_user, $mysql_password) or die('Could not connect: ' . mysqli_error($link));
				mysqli_select_db($link , $mysql_database) or die('Could not select database');
	
				echo"<title>$title</title>";
			?>
				<link href='css/style.css' rel='stylesheet' type='text/css'>
				<link href='css/buttons.css' rel='stylesheet' type='text/css'>
				<link href='css/wrappers.css' rel='stylesheet' type='text/css'>
		</head>
	<body>
		<div class="hanger">
		<span><?php echo "$title"; ?></span>
		</div>
		
		<div class="bbar">
			<div class="centerblock">
				<a id='button-dark-1' href="search-post.php">Posts</a>
				<a id='button-dark-1' href="tags.php">Tags</a>
				<a id='button-dark-1' href="search-pool.php">Pools</a>
				<a id='button-dark-1' href="upload.php">Upload</a>
				<a id='button-dark-1' href="about.php">About</a>
			</div>
		</div>
		
		<div id='index-container'>
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
		<p style="font-size:8px;">Running Mirthless Booru</p>
	</footer>
</html>
