<!DOCTYPE html>
<html>
	<head>
	<link href="sicon2.png" type="image/png" rel="icon">
	<link rel="stylesheet" type="text/css" href="style.css" />
	<link rel="favorite icon" href="sicon2.png" />
	<?php
		include"config.php";
		$link = mysqli_connect($mysql_host, $mysql_user, $mysql_password) or die('Could not connect: ' . mysqli_error($link));
		mysqli_select_db($link , $mysql_database) or die('Could not select database');
		echo"<title>$title - Pools</title>";
	?>
	</head>
	<body id="pools">
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
		<div id="poolbox">
			<?php
				$numpoolsquery = "SELECT COUNT(*) FROM pools ";
				$query = "SELECT name , poolid , date , postid , rating FROM pools ORDER BY date DESC";
				$numpoolsres = mysqli_query($link , $numpoolsquery) or die(mysqli_error($link));
				$numpoolsarr = mysqli_fetch_array($numpoolsres);
				$numpools = $numpoolsarr[0];
				if($numpools==0){
					echo "Nothing here<br />";
				}
				else{
					$poolsres= mysqli_query($link , $query) or die(mysqli_error($link));
					$query = "";
					$listi = true;
					$row = mysqli_fetch_array($poolsres);
					echo "<div id='poolnote'><span id='poollist'>Name<div id='ppcount'>Posts</div><div id='poolrate'>Rating</div></span></div>\n";
					while($row){
						if($listi){
							$css = "pooldisplay";
							$listi = false;
						}
						else{
							$css = "pooldisplay2";
							$listi = true;
						}
						
						echo "<div id='$css'><span id='poollist'>";
						
						$numin = preg_split('/\s+/', $row['postid']);
						$num = count($numin)-2;
					
						echo "<a href=\"view-pool.php?id=$row[poolid]\">$row[name]</a><div id='ppcount'>$num</div>";
						if($row['rating']=='explicit'){
							echo "<div id='poolrate' style='color:red;'>E</div>";
						}
						elseif($row['rating']=='safe'){
							echo "<div id='poolrate' style='color:lightgreen;'>S</div>";
						}
						else{
							echo "<div id='poolrate' style='color:yellow;'>Q</div>";
						}
						

						echo"</span></div>\n";
						$row = mysqli_fetch_array($poolsres);
					}
				}
			?>
			
		</div>
		<a id="newpool" href="new-pool.php" >New Pool</a>
	</body>
</html>