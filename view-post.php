<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
<head>
<?php
  include 'config.php';
  $link = mysqli_connect($mysql_host, $mysql_user, $mysql_password) or die('Could not connect: ' . mysqli_error($link));
  mysqli_select_db($link , $mysql_database) or die('Could not select database');
  if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM `posts` WHERE `idnum` = '$id' LIMIT 1";
    $result = mysqli_query($link , $query) or die(mysqli_error($link));
    if($row = mysqli_fetch_array($result)) {
      $titleext = $row['tags'];
    }
    else {
      $notfound = true;
      $titleext = "Not Found";
    }
    echo "<title>$title - $titleext";
  }
?>
</title>
<link rel="stylesheet" type="text/css" href="style.css" />
<link rel="favorite icon" href="favicon.png" />
</title>
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
<div id="sidebar">
		<?php
			//this all needs worked on
			
			$poolcheck = "SELECT isinpool FROM postdata WHERE `idnum` = '$id' LIMIT 1";
			$poolcheckquery = mysqli_query($link, $poolcheck) or die(mysqli_error($link));
			$check = mysqli_fetch_array($poolcheckquery);
			$newa = preg_split('/\s+/', $check['isinpool']);
			$total = count($newa);
			//debug
			//echo $total-2;
			//echo "<br/>";
			//
			if(!$newa[1]==''){
			//debug
			//echo "start <br />";
			for($i = 1; $i < $total -1 ; $i++ ){
				
				$nquery = "SELECT name , poolid , postid FROM pools WHERE isclosed<=0 AND poolid = $newa[$i] LIMIT 1";
				$poolresult = mysqli_query($link , $nquery) or die(mysqli_error($link));
				if (!mysqli_num_rows($poolresult)==0){
					echo "<div id='poolnav'>";
					$poolarr = mysqli_fetch_array($poolresult);
					echo "<a href='view-pool.php?id=$poolarr[poolid]'>Pool: $poolarr[name]</a><br/ >";
					$postidarr = preg_split('/\s+/', $poolarr['postid']);
					$currentarray = array_search($id , $postidarr);
					$back = $currentarray -1;
					$forth = $currentarray +1;
					if(!$postidarr[$back]==''){
						echo "<a href='view-post.php?id=$postidarr[$back]'> <<- </a>";
					}
					else{
						echo " <<- ";
					}
					echo " | ";
					if(!$postidarr[$forth]==''){
						echo "<a  href='view-post.php?id=$postidarr[$forth]'> ->> </a>";
					}
					else{
						echo " ->> ";
					}
					echo "</div>";
				}
			}
			//debug
			//echo "end";
			}

			// like... ALL of it
			
		?>
  <form action="search.php" method="get">
    <div id="searcharea">
      <input id="searchbox" name="q" size="22" type="text" /><br />
      <input id="button" type="submit" value="Search" />
    </div>
  </form>
  <div id="tags">
<?php
  $tags = substr($row['tags'], 1, strlen($row['tags']) - 2);
  $tags = explode(' ', $tags);
  $full = $row['name'];
  foreach($tags as $tag) {
    echo "<a id='tagbox' href=\"search.php?q=$tag\">$tag</a><br />\n";
  }
?>
  </div>
  <?php
  echo "<div id='controls'><a href=\"$imagedir$full\">View Full</a><br />\n";
  echo "<a href=\"edit-post.php?id=$id\">Edit</a></div>\n"; 
  ?>
</div>
<div id="content">
<?php
	
	$filequery = "SELECT type , height , width FROM postdata WHERE idnum=$id";
	$queryresult = mysqli_query($link , $filequery) or die(mysqli_error($link));
	$rowtype = mysqli_fetch_array($queryresult);
	$filetype = $rowtype['type'];
	$filename = $row['name'];
	$filethumb = $row['thumb'];
	$height = $rowtype['height'];
	$width = $rowtype['width'];
	
	if($filetype=='webm'or$filetype=='mp4'){
		echo "<video id='image' src=\"$imagedir/$filename\" controls='true' loop alt=\"$titleext\" title=\"$titleext\"><br />\n";
	}
	elseif($filetype=='swf'){
		echo "<embed id='flash' name='plugin' src=\"$imagedir$filename\" type='application/x-shockwave-flash' width='$width' height='$height' allowscriptaccess='never' id='flash'><br />\n";
	}
	elseif($filetype=='txt'){
		echo "<div id='textwrapper'>";
		
			$fh = fopen("$imagedir/$filename", 'r');
			
			while ( $line = fgets($fh, 1000) ) {
				echo $line;
				}
			/*
			$theData = fread($fh, 1);
			fclose($fh);
			echo $theData;
			*/
		
		echo "</div>";
	}
	elseif($filetype=='mp3'){
		echo "<div id='mp3wrapper'>";
		echo "<div id=nametext>$row[origname]</div>";
		echo "<img id='audioim' src='$thumbdir$filethumb'><br />\n";
		echo "<audio id='audiopl' controls><source src=\"$imagedir$filename\" type='audio/mp3'></audio>\n";
		echo "</div>";
	}
	else {
	echo "<img id='image' src=\"$imagedir/$filename\" alt=\"$titleext\" title=\"$titleext\"><br />\n";
	}
?>
</div>
</body>
</html>
