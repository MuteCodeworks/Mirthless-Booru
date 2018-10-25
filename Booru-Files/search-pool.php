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
				<a href="tags.php">Tags</a>
				<a href="search-pool.php">Pools</a>
				<a href="upload.php">Upload</a>
				<a href="about.php">About</a>
				<a href="new-pool.php" >New Pool</a>
			</div>
		</div>
			<table class="basic-table" style="width:70%;" >
			<thead>
				<tr>
					<td style="width:35%;">Name</td>
					<td style="width:5%;">Posts</td>
					<td style="width:2%;">Rating</td>
				</tr>
			</thead>
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
					while($row){
						
						echo "<tr>";
						
						$numin = preg_split('/\s+/', $row['postid']);
						$num = count($numin)-2;
					
						echo "<td><a href=\"view-pool.php?id=$row[poolid]\">$row[name]</a><td>$num</td>";
						if($row['rating']=='explicit'){
							echo "<td style='color:red;'>E</td>";
						}
						elseif($row['rating']=='safe'){
							echo "<td id='poolrate' style='color:lightgreen;'>S</td>";
						}
						else{
							echo "<td id='poolrate' style='color:yellow;'>Q</td>";
						}
						

						echo"</tr>\n";
						$row = mysqli_fetch_array($poolsres);
					}
				}
			?>
			</table>
	</body>
</html>