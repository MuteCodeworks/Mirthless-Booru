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
				$query = "SELECT * FROM pools ORDER BY time DESC";
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
						//$inpool = mysqli_query($link,"SELECT COUNT(*) AS inpool FROM poolmap WHERE map_id=$row[pool_id]")or die(mysqli_error($link));
						//$inpool = mysqli_fetch_array($inpool);
						echo "<tr>";
						echo "<td><a href='view-pool.php?id=$row[pool_id]' >$row[name]</a></td>";
						echo "<td>$row[count]</td>";
						echo"</tr>\n";
						$row = mysqli_fetch_array($poolsres);
					}
				}
			?>
			</table>
	</body>
</html>
