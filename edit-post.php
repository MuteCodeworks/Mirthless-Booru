<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
	<head>
		<title>
			<?php
				include 'config.php';
				if(isset($_GET['id'])) {
					$id = $_GET['id'];
					echo "$title - Edit $id";
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
				<a href="search-pool.php">Pools</a>
				<a href="upload.php">Upload</a>
				<a href="about.php">About</a>
			</div>
		</div>
		<div id="sidebar">
			<form action="search.php" method="get">
				<div id="searcharea">
				<input id="searchbox" name="q" size="22" type="text" /><br />
				<input id="button" type="submit" value="Search" />
				</div>
			</form>
		</div>
		<div id="content">
			<?php
				if($noid) {
					echo "Couldn't find image to edit\n";
				}
				else {
	  
    $query = "SELECT * FROM `posts` WHERE `idnum` = '$id' LIMIT 1";
	$querydata = "SELECT rating FROM `postdata` WHERE `idnum` = '$id' LIMIT 1";
    $result = mysqli_query($link , $query) or die(mysqli_error($link));
    $row = mysqli_fetch_array($result);
    if(isset($_GET['new'])) {
      $newu = $_GET['new'];
      $newa = preg_split('/\s+/', $newu);
      $newa = array_unique($newa);
      sort($newa);
      $new = implode(" ", $newa);
	  if($new==''){
		  $new = "tagme";
	  }
	  if(isset($_GET['poolid'])&&$_GET['poolid']!=''){
		$pool = $_GET['poolid'];
		$queryoldids = "SELECT postid FROM pools WHERE poolid = '$pool' LIMIT 1";
		$queryoldpools = "SELECT isinpool FROM postdata WHERE idnum = '$id' LIMIT 1";
		$resultoldids = mysqli_query($link , $queryoldids) or die(mysqli_error($link));
		$resultoldpools = mysqli_query($link , $queryoldpools) or die(mysqli_error($link));
		
		if($_GET['append']=='remove'){
			
			$oldpoolarr = mysqli_fetch_array($resultoldpools);
			$oldpat = "/(?:\W|^)(\Q$pool\E)(?:\W|$)/i";
			$upgradepoolstr = preg_replace($oldpat , ' ' ," $oldpoolarr[isinpool] ");
			$newpoolarr = preg_split('/\s+/', " $upgradepoolstr ");
			$newpoolstr = implode(" ", $newpoolarr);
			$newpoolstr = preg_replace("/\s\s+/" , " " ," $newpoolstr ");
			echo "$newpoolstr<br />";
		}
		else{
			
			$oldpoolarr = mysqli_fetch_array($resultoldpools);
			$oldpat = "/(?:\W|^)(\Q$pool\E)(?:\W|$)/i";
			$upgradepoolstr = preg_replace($oldpat , ' ' ," $oldpoolarr[isinpool] ");
			$newpoolarr = preg_split('/\s+/', " $pool "." $upgradepoolstr ");
			$newpoolstr = implode(" ", $newpoolarr);
			$newpoolstr = preg_replace("/\s\s+/" , " " ," $newpoolstr ");
			echo "$newpoolstr<br />";
		}
		
		
		if (!mysqli_num_rows($resultoldids)==0){
			if($_GET['append']=='first'){
				$idarr = mysqli_fetch_array($resultoldids);
				$reoldpat = "/(?:\W|^)(\Q$id\E)(?:\W|$)/i";
				$newidara = preg_replace($reoldpat , ' ' ," $idarr[postid] ");
				$newidarr = preg_split('/\s+/', " $id "." $newidara ");
				$newidstr = implode(" ", $newidarr);
				$newidstr = preg_replace("/\s\s+/" , " " ," $newidstr ");
			}
			elseif($_GET['append']=='remove'){
				$idarr = mysqli_fetch_array($resultoldids);
				$reoldpat = "/(?:\W|^)(\Q$id\E)(?:\W|$)/i";
				$newidara = preg_replace($reoldpat , ' ' ," $idarr[postid] ");
				$newidarr = preg_split('/\s+/', " $newidara ");
				$newidstr = implode(" ", $newidarr);
				$newidstr = preg_replace("/\s\s+/" , " " ,"$newidstr");
			}
			else{
				$idarr = mysqli_fetch_array($resultoldids);
				$reoldpat = "/(?:\W|^)(\Q$id\E)(?:\W|$)/i";
				$newidara = preg_replace($reoldpat , ' ' ," $idarr[postid] ");
				$newidarr = preg_split('/\s+/', " $newidara "." $id ");
				$newidstr = implode(" ", $newidarr);
				$newidstr = preg_replace("/\s\s+/" , " " ," $newidstr ");
			}
			}
			else{
				$newidstr = '';
			}
			
			$poolupdate = "UPDATE pools SET postid = '$newidstr' WHERE poolid = $pool LIMIT 1";
			$postupdate = "UPDATE postdata SET isinpool = '$newpoolstr' WHERE idnum = $id LIMIT 1";
			$result = mysqli_query($link , $poolupdate) or die(mysqli_error($link));
			$result = mysqli_query($link , $postupdate) or die(mysqli_error($link));
		}
		
	  $rating = $_GET['rating'];
	  
      $query = "UPDATE `posts` SET `tags` = ' $new ' WHERE `idnum` = '$id' LIMIT 1";
	  $querydata = "UPDATE postdata SET rating = '$rating' WHERE idnum = '$id' LIMIT 1";
	  
      $result = mysqli_query($link , $query) or die(mysqli_error($link));
	  $result = mysqli_query($link , $querydata) or die(mysqli_error($link));
	  
	 
	  
	  
      echo "<p class='text'> Updated information successfully</p><br />\n";
    }
    else {
	$tags = $tags = substr($row['tags'], 1, strlen($row['tags']) - 2);

      $tags = $tags = substr($row['tags'], 1, strlen($row['tags']) - 2);
	  $imgq = "SELECT origname , name FROM posts WHERE idnum=$id";
	  $imgs = mysqli_query($link , $imgq) or die(mysqli_error($link));
	  $imga = mysqli_fetch_array($imgs);
	  $imgf = $imga['name'];
      echo "<img src=\"$imagedir/$imgf\" alt=\"$tags\" title=\"$tags\"><br />\n"
	
	  
	  
	  
	  
	  
	  
?>
<form action="edit-post.php" method="GET">
Tags:<br /><textarea id="inbox" name="new" rows="10" cols="40">
<?php
    echo "$tags</textarea><br />\n";
    echo "<input type=\"hidden\" name=\"id\" value=\"$id\" />";
	$querydata = "SELECT rating FROM `postdata` WHERE `idnum` = '$id' LIMIT 1";
	$rresult = mysqli_query($link , $querydata) or die(mysqli_error($link));
	$rrow = mysqli_fetch_array($rresult);
	$prating = $rrow['rating'];
	
?>
Rating:<br /><input type="radio" name="rating" value="safe" <?php echo($prating=='safe')?'checked':'' ?> size="19" >Safe
<input type="radio" name="rating" value="questionable" <?php echo($prating=='questionable')?'checked':'' ?> size="19" >Questionable
<input type="radio" name="rating" value="explicit" <?php echo($prating=='explicit')?'checked':'' ?> size="19" >Explicit
<br />
Add To Pool <select name="poolid" style="color:black;">
<option value="">Select...</option>
<?php
	$poolquery = "SELECT name , poolid , postid FROM pools ORDER BY name";
	$countquery = "SELECT COUNT(poolid) AS total FROM pools WHERE isclosed<=0";
	$countpool = mysqli_query($link , $countquery) or die(mysqli_error($link));
	$countarr = mysqli_fetch_array($countpool);
	$listed = $countarr['total'];
	$poolresult = mysqli_query($link , $poolquery) or die(mysqli_error($link));
	$poolarr = mysqli_fetch_array($poolresult);
	$lastname =$poolarr['name'];
	echo "<option value='$poolarr[poolid]'>$poolarr[name]</option>";
	
	for( $i = 0 ; $i < $listed -1 ; $i++ ){
		
		
			
		$nquery = "SELECT name , poolid FROM pools WHERE isclosed<=0 AND name>'$lastname' LIMIT 1";
		$poolresult = mysqli_query($link , $nquery) or die(mysqli_error($link));
		$poolarr = mysqli_fetch_array($poolresult);
		echo "<option value='$poolarr[poolid]'>$poolarr[name]</option>";
	}
?>
</select>
<br />
<input type="radio" name="append" value="first" size=""19 >: Place First
<input type="radio" name="append" value="last" size=""19 checked=''>: Place Last
<input type="radio" name="append" value="remove" size=""19 >: Remove From Pool
<br />

<input id="uploadbutton" type="submit" value="Update" /> Or 
<?php
echo "<a id='deletebutton' href=\"remove.php?id=$id\">Delete</a><br />";
?>

</form>
<?php
    }
  }
?>
</div>
</div>
</body>
</html>
