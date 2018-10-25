<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
	<head>
		<title>
			<?php
			include_once 'config.php';
			if(isset($_GET['id'])) {
				$id = $_GET['id'];
				echo "$title - Edit Pool $id";
				$noid = 0;
			}
			else {
				echo "$title - Not found";
				$noid = 1;
			}
			?>
		</title>
		<link rel="stylesheet" type="text/css" href="style.css" />
		<link rel="favorite icon" href="favicon.png" />
	</head>
	<body class="edit">
		<?php
			$link = mysqli_connect($mysql_host, $mysql_user, $mysql_password) or die('Could not connect: ' . mysqli_error($link));
			mysqli_select_db($link , $mysql_database) or die('Could not select database');
		?>
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
		<div>
		<?php
			if(isset($_GET['del'])&&$_GET['del']=='true'){
				$update = "DELETE FROM pools WHERE poolid=$id LIMIT 1";
				// I literally just copy/pasted this and changed the sql
				$queryisinpool = "SELECT postid FROM pools WHERE poolid=$id";
				$sqlinpool = mysqli_query($link , $queryisinpool) or die(mysqli_error($link));
				$inpollarr = mysqli_fetch_array($sqlinpool);
				
				if(!$inpollarr['postid']==''){
			
					$poolidarr = preg_split('/\s+/' , $inpollarr['postid']);
					$total = count($poolidarr);
					for($i = 1; $i < $total -1 ; $i++ ){
						$querypool = "SELECT isinpool FROM postdata WHERE idnum=$poolidarr[$i]";
						$sqlpost = mysqli_query($link , $querypool) or die(mysqli_error($link));
						$postidarr = mysqli_fetch_array($sqlpost);
						$splitby = "/(?:\W|^)(\Q$id\E)(?:\W|$)/i";
						$oldidarr = preg_split($splitby, $postidarr['isinpool'], 2);
						$newidstr = implode(" ", $oldidarr);
						$newidstr = preg_replace("/\s\s+/" , " " ,"$newidstr");
						$poolupdate = "UPDATE postdata SET isinpool = '$newidstr' WHERE idnum = $poolidarr[$i] LIMIT 1";
						mysqli_query($link , $poolupdate) or die(mysqli_error($link));
						
					}
		
				}
				
				
				
				
				$result = mysqli_query($link, $update) or die(mysqli_error($link));
				if($result){
					echo "Pool Deleted";
				}
				else{
					echo "There Was an Error";
				}
			}
			else{
		
				$queryinfo = "SELECT * FROM pools WHERE poolid=$id LIMIT 1";
				$result = mysqli_query($link , $queryinfo) or die(mysqli_error($link));
				$infoarr = mysqli_fetch_array($result);
			
				echo "Pool ID: $infoarr[poolid]<br /> Post ID: $infoarr[postid]";
				echo "<a id='newpool' href='edit-pool.php?id=$id&del=true'>Delete?</a>";
			}
		?>
		</div>
	</body>
</html>