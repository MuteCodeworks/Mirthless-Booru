<!DOCTYPE html>
<html>
	<head>
	<?php
		include 'config.php';
		$link = mysqli_connect($mysql_host, $mysql_user, $mysql_password) or die('Could not connect: ' . mysqli_error($link));
		mysqli_select_db($link , $mysql_database) or die('Could not select database');
		if(isset($_GET['id'])) {
			$id = $_GET['id'];
			$query = "SELECT * FROM pools WHERE poolid = '$id' LIMIT 1";
			$result = mysqli_query($link , $query) or die(mysqli_error($link));
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
	<link rel="stylesheet" type="text/css" href="style.css" />
	<link rel="favorite icon" href="favicon.png" />
	</head>
	<body id="view">
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
		<div id="divider">
		</div>
		<div id="poolview">
			<div id="poolcontent">
				<?php
					echo "<div id='plnmbr'>$row[name]</div><br />";
					if($row['postid']!=""){
						$newa = preg_split('/\s+/', $row['postid']);
						$total = count($newa);
						
						
					
						for($i = 1; $i < $total -1 ; $i++ ){
							
						
							$query = "SELECT * FROM posts WHERE idnum=$newa[$i]";
							$postq = mysqli_query($link , $query) or die(mysqli_error($link));
							if (!mysqli_num_rows($postq)==0){
								
								
								$row = mysqli_fetch_array($postq);
						
						
								$tags = substr($row['tags'], 1, strlen($row['tags']) -2);
								$dataquery = "SELECT rating , type FROM postdata WHERE idnum=$row[idnum]";
								$infoget = mysqli_query($link , $dataquery) or die(mysqli_error($link));
								$inforow = mysqli_fetch_array($infoget);
								$rating = $inforow['rating'];
								$type = $inforow['type'];
						
						
								echo "<div id='thumbcon'><span class=\"thumb\">";
								
								echo "<a href=\"view-post.php?id=$row[idnum]\"><img src=\"thumbs/$row[thumb]\" alt=\"$tags\" title=\"$row[tags]\">";
								if($type=='gif'or$type=='webm'or$type=='mp4'){
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
	</body>
</html>