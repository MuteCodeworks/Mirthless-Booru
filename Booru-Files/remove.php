<!DOCTYPE html>
<html>
<head>
<title>
<?php
  include 'config.php';
  include 'functions/map-tags.php';
  if(isset($_GET['id'])) {
    $id = $_GET['id'];
    echo "$title - Remove $id";
    $noid = 0;
  }
  else {
    echo "$title - Not found";
    $noid = 1;
  }
?>
</title>
<link rel="stylesheet" type="text/css" href="css/style.css" />
<link rel="stylesheet" type="text/css" href="css/buttons.css" />
<link rel="favorite icon" href="favicon.png" />
</title>
</head>
<body id="remove">
<?php
  $link = mysqli_connect($mysql_host, $mysql_user, $mysql_password) or die('Could not connect: ' . mysqli_error($link));
  mysqli_select_db($link , $mysql_database) or die('Could not select database');
?>
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
  <form action="search.php" method="get">
    <div id="searcharea">
      <input id="searchbox" name="q" size="22" type="text" /><br />
      <input id="button-dark-2" type="submit" value="Search" />
    </div>
  </form>
</div>
<div id="content">
<?php
  if($noid) {
    echo "Couldn't find image to remove";
  }
  else {
    $query = "SELECT * FROM `postdata` WHERE `idnum` = '$id' LIMIT 1";
    $result = mysqli_query($link , $query) or die(mysqli_error($link));
    $row = mysqli_fetch_array($result);
	$dataquery = "SELECT * FROM `postdata` WHERE `idnum` = '$id' LIMIT 1";
	$datares = mysqli_query($link , $dataquery) or die(mysqli_error($link));
	$datarow = mysqli_fetch_array($datares);
	$filename = $row['hash'];
	$thumbname = $row['thumb'];
	$filetype = $datarow['type'];
    if(isset($_GET['s'])) {
      if($_GET['s'] != 1) {
        $tags = $row['tag'];
        echo "Are You Sure You Want To Delete This Post?<br />\n";
        echo "<a href=\"remove.php?id=$filename&s=1\">Yes</a> <a href=\"search-post.php?q=\">No</a><br />\n";
        echo "<img src=\"$imagedir/$filename\" alt=\"$tag\" title=\"$id\"><br />\n";
      }
      else {

		$queryisinpool = "SELECT isinpool FROM postdata WHERE idnum=$id";
		$sqlinpool = mysqli_query($link , $queryisinpool) or die(mysqli_error($link));
		$inpollarr = mysqli_fetch_array($sqlinpool);
		if(!$inpollarr['isinpool']==''){

			$poolidarr = preg_split('/\s+/' , $inpollarr['isinpool']);
			$total = count($poolidarr);
			for($i = 1; $i < $total -1 ; $i++ ){
				$querypool = "SELECT postid FROM pools WHERE poolid=$poolidarr[$i]";
				$sqlpost = mysqli_query($link , $querypool) or die(mysqli_error($link));
				$postidarr = mysqli_fetch_array($sqlpost);
				$splitby = "/(?:\W|^)(\Q$id\E)(?:\W|$)/i";
				$oldidarr = preg_split($splitby, $postidarr['postid'], 2);
				$newidstr = implode(" ", $oldidarr);
				$newidstr = preg_replace("/\s\s+/" , " " ,"$newidstr");
				$poolupdate = "UPDATE pools SET postid = '$newidstr' WHERE poolid = $poolidarr[$i] LIMIT 1";
				mysqli_query($link , $poolupdate) or die(mysqli_error($link));

			}

		}
    map_tags($id,'$tags',$link,$metaterms,'REMOVE');
        $querypost = "DELETE FROM `postdata` WHERE `idnum` = '$id' LIMIT 1 ";
		 $querydata = "DELETE FROM tagmap WHERE post_id = '$id'";
        mysqli_query($link , $querypost) or die(mysqli_error($link));
		mysqli_query($link , $querydata) or die(mysqli_error($link));

        if(!is_writable("$imagedir/$filename"))
          echo "Removed from database, but image file not removed. You should remove it manually from $imagedir/$filename";
        else {
			if($filetype!='swf'&&$filetype!='txt'){
				if(!unlink("$imagedir/$filename")and!unlink("$thumbdir/$thumbname"))
					echo "Removed from database, but image file not removed. You should remove it manually from $imagedir/$filename";
				else {
					echo "Removed successfully. <a href=\"search-post.php?q=\">Click here</a> to return";
				}
			}
			else{
				if(!unlink("$imagedir/$filename")){
					echo "Removed from database, but image file not removed. You should remove it manually from $imagedir/$filename";
				}
				else {
					echo "Removed successfully. <a href=\"search-post.php?q=\">Click here</a> to return";
				}
			}
        }
      }
    }
    else {
      echo "Are You Sure You Want To Delete This Post?<br />\n";
      echo "<a id='button-light-2' href=\"remove.php?id=$id&s=1\">Yes</a> <a id='button-light-2' href=\"search-post.php?q=\">No</a><br />\n";
      echo "<img src=\"$imagedir/$filename\" ><br />\n";
    }
  }
?>
</div>
</div>
</body>
</html>
