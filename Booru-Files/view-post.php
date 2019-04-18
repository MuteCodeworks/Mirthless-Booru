<!DOCTYPE html>
<html>
<head>
<?php
  include_once 'config.php';
  include 'functions/read-tags.php';
  $link = mysqli_connect($mysql_host, $mysql_user, $mysql_password) or die('Could not connect: ' . mysqli_error($link));
  mysqli_select_db($link , $mysql_database) or die('Could not select database');
  if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM `postdata` WHERE `idnum` = '$id' LIMIT 1";
    $result = mysqli_query($link , $query) or die(mysqli_error($link));
    if($row = mysqli_fetch_array($result)) {
      $titleext = "Post: $row[idnum]";
    }
    else {
      $notfound = true;
      $titleext = "Not Found";
    }
    echo "<title>$title - $titleext";
  }
?>
</title>
<link rel="stylesheet" type="text/css" href="css/style.css" />
<link rel="stylesheet" type="text/css" href="css/buttons.css" />
<link rel="stylesheet" type="text/css" href="css/tags.css" />
<link rel="stylesheet" type="text/css" href="css/wrappers.css" />
<link rel="favorite icon" href="favicon.png" />
</title>
</head>
<body id="view">
<div id="header">
  <span id="title" style="font-size: 25px; font-weight: bold"><?php echo "$title"; ?></span>
  <div id="navbar">
    <a id='button-dark-2' href="index.php">Home</a>
    <a id='button-dark-2' href="search-post.php">Posts</a>
	<a id='button-dark-2' href="tags.php">Tags</a>
	<a id='button-dark-2' href="search-pool.php">Pools</a>
    <a id='button-dark-2' href="upload.php">Upload</a>
    <a id='button-dark-2' href="about.php">About</a>
  </div>
</div>
<div id="sidebar">
		<?php
			$poolcheck = "SELECT ps.name , ps.pool_id FROM pools ps, poolmap pm  WHERE ps.pool_id = pm.pool_id AND pm.post_id = $id";
			$poolsquery = mysqli_query($link,$poolcheck)or die(mysqli_error($link));
			$poolsrow = mysqli_fetch_array($poolsquery);
			while($poolsrow){
				echo "<div id='poolnav'>";
				echo "<a href='view-pool.php?id=$poolsrow[pool_id]'>Pool: $poolsrow[name]</a><br />";
				$tmparr = array();
				$pagesquery = mysqli_query($link,"SELECT post_id FROM poolmap WHERE pool_id = $poolsrow[pool_id] ORDER BY location ASC");
				$pagesnav = mysqli_fetch_array($pagesquery);
				while($pagesnav){
					$tmparr[] = $pagesnav['post_id'];
					$pagesnav = mysqli_fetch_array($pagesquery);
				}
				$currentloc = array_search($id,$tmparr);
				$back = $currentloc -1;
				$forth = $currentloc +1;
				if(array_key_exists($back,$tmparr)){
					echo "<a href='view-post.php?id=$tmparr[$back]'> <b><<-</b> </a>";
				}
				else{
					echo " <<- ";
				}
				echo " | ";
				if(array_key_exists($forth,$tmparr)){
					echo "<a  href='view-post.php?id=$tmparr[$forth]'> <b>->></b> </a>";
				}
				else{
					echo " >> ";
				}
				echo "</div>";
				$poolsrow = mysqli_fetch_array($poolsquery);
			}
			$poolcheckquery = mysqli_query($link, $poolcheck) or die(mysqli_error($link));
			$check = mysqli_fetch_array($poolcheckquery);
		?>
  <form action="search-post.php" method="get">
    <div id="searcharea">
      <input id="searchbox" name="q" size="22" type="text" /><br />
      <input id="button-dark-2" type="submit" value="Search" />
    </div>
  </form>
  <div id="tagbox">
<?php
	read_tags($link,$metaterms,$id,'VIEW');
?>
  </div>
  <?php
  echo "<div id='controls'><a href=\"$imagedir/$row[hash]\">View Full</a><br />\n";
  echo "<a href=\"edit-post.php?id=$id\">Edit</a></div>\n";
  echo "$row[date]\n"
  ?>
</div>
<div id="content">
<?php

	$filequery = "SELECT type , height , width FROM postdata WHERE idnum=$id";
	$queryresult = mysqli_query($link , $filequery) or die(mysqli_error($link));
	$rowtype = mysqli_fetch_array($queryresult);
	$filetype = $rowtype['type'];
	$filename = $row['hash'];
	$filethumb = $row['thumb'];
	$height = $rowtype['height'];
	$width = $rowtype['width'];

	if($filetype=='webm'or$filetype=='mp4'){
		echo "<video id='image' src=\"$imagedir/$filename\" controls='true' loop alt=\"$titleext\" title=\"$titleext\"><br />\n";
	}
	elseif($filetype=='swf'){
		echo "<embed id='flash' name='plugin' src=\"$imagedir/$filename\" type='application/x-shockwave-flash' width='$width' height='$height' allowscriptaccess='never' ><br />\n";
	}
	elseif($filetype=='txt'){
		echo "<div id='textwrapper'>";

			$fh = fopen("$imagedir/$filename", 'r');

			while ( $line = fgets($fh, 1000) ) {
				echo $line;
			}
		echo "</div>";
	}
	elseif($filetype=='mp3'or$filetype=='flac'){
		echo "<div id='mp3wrapper'>";
		echo "<div id=nametext>$row[given_name]</div>";
		if(file_exists("$thumbdir/$filethumb")){
			echo "<img id='audioim' src='$thumbdir/$filethumb'><br />\n";
		}
		else{
			echo "<img id='audioim' style='width:602px;' src='$thumbdir/mp3thumb.png'><br />\n";
		}
		echo "<audio id='audiopl' controls><source src=\"$imagedir/$filename\" type='audio/mp3'></audio>\n";
		echo "</div>";
	}
	else {
	echo "<img id='image' src=\"$imagedir/$filename\" alt=\"$titleext\" title=\"$titleext\"><br />\n";
	}
?>
</div>
</body>
</html>
