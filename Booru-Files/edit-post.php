<!DOCTYPE html>
<html>
	<head>
		<title>
			<?php
				include 'config.php';
				include 'functions/read-tags.php';
				include 'functions/map-tags.php';
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
		<link rel="stylesheet" type="text/css" href="css/style.css" />
		<link rel="stylesheet" type="text/css" href="css/buttons.css" />
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
				<a id='button-dark-2' href="index.php">Home</a>
				<a id='button-dark-2' href="search-post.php">Posts</a>
				<a id='button-dark-2' href="tags.php">Tags</a>
				<a id='button-dark-2' href="search-pool.php">Pools</a>
				<a id='button-dark-2' href="upload.php">Upload</a>
				<a id='button-dark-2' href="about.php">About</a>
			</div>
		</div>
		<div id="sidebar">
			<form action="search-post.php" method="get">
				<div id="searcharea">
				<input id="searchbox" name="q" size="22" type="text" /><br />
				<input id="button-dark-2" type="submit" value="Search" />
				</div>
			</form>
		</div>
		<div id="content">
			<?php
				if($noid) {
					echo "Couldn't find image to edit\n";
				}
				else {

					$query = "SELECT * FROM postdata WHERE idnum = $id LIMIT 1";
					$result = mysqli_query($link , $query) or die(mysqli_error($link));
					$data = mysqli_fetch_array($result);
					$name = $data['hash'];
					$ext = $data['type'];
					$thumb_name = $data['thumb'];

					$query = "SELECT * FROM `postdata` WHERE `idnum` = '$id' LIMIT 1";
					$result = mysqli_query($link , $query) or die(mysqli_error($link));
					$row = mysqli_fetch_array($result);
					if(isset($_GET['new'])) {
						$newu = $_GET['new'];
						$newu = preg_replace('/\s\s+/',' '," $newu ");
						$newu = $newu = substr($newu, 1, strlen($newu) - 1);
						$newa = preg_split('/\s+/', $newu);
						$newa = array_unique($newa);
						sort($newa);
						$new = implode(" ", $newa);
						if($new==''){
							$new = "tagme";
						}



						if(isset($_GET['pool_id'])&&$_GET['pool_id']!=''){
							$pool = $_GET['pool_id'];
							$query = "SELECT post_id , location FROM poolmap WHERE pool_id = $pool ORDER BY location ASC";
							$poolremap = mysqli_query($link,$query)or die(mysqli_error($link));
							$poolrow = mysqli_fetch_array($poolremap);
							$location_array = array();
							while($poolrow){
								$location_array[] = $poolrow['post_id'];
								$poolrow = mysqli_fetch_array($poolremap);
							}
							if(isset($_GET['append'])&&$_GET['append']!=''){
								if(isset($_GET['append'])&&($_GET['append']=='first'||$_GET['append']=='last')){
									if($_GET['append']=='first'){
										array_unshift($location_array,$id);
										$count = count($location_array);
										mysqli_query($link,"UPDATE pools SET count = $count WHERE pool_id = $pool")or die(mysqli_error($link));
										mysqli_query($link,"DELETE FROM poolmap WHERE pool_id = $pool")or die(mysqli_error($link));
										foreach($location_array as $loc => $post_id){
											$poolsql = "INSERT INTO poolmap VALUES ( \"$pool:$post_id\" ,$pool , $post_id , $loc )";
											mysqli_query($link,$poolsql)or die(mysqli_error($link));
										}
									}
									else{
										$location_array[] = $id;
										$count = count($location_array);
										mysqli_query($link,"UPDATE pools SET count = $count WHERE pool_id = $pool")or die(mysqli_error($link));
										mysqli_query($link,"DELETE FROM poolmap WHERE pool_id = $pool")or die(mysqli_error($link));
										foreach($location_array as $loc => $post_id){
											$poolsql = "INSERT INTO poolmap VALUES ( \"$pool:$post_id\" ,$pool , $post_id , $loc )";
											mysqli_query($link,$poolsql)or die(mysqli_error($link));
										}
									}
								}
								elseif($_GET['append']=='remove'){
									if(mysqli_num_rows(mysqli_query($link,"SELECT map_id FROM poolmap WHERE pool_id = $pool AND post_id = $id"))>0){
										if (in_array($id, $location_array)){
											unset($location_array[array_search($id,$location_array)]);
											$location_array = array_values($location_array);
											$count = count($location_array);
											mysqli_query($link,"UPDATE pools SET count = $count WHERE pool_id = $pool")or die(mysqli_error($link));
											mysqli_query($link,"DELETE FROM poolmap WHERE pool_id = $pool")or die(mysqli_error($link));
											foreach($location_array as $loc => $post_id){
												$poolsql = "INSERT INTO poolmap VALUES ( \"$pool:$post_id\" ,$pool , $post_id , $loc )";
												mysqli_query($link,$poolsql)or die(mysqli_error($link));
												echo "$loc<br />";
											}
										}
										else{
											echo "Post $id Not Found In Pool $pool<br />";
										}
									}
									else{
										echo "Post $id Not Found In Pool $pool<br />";
									}
									
								}
								else{
									echo "Please Use A Valid append Value";
								}
							}
						}

						if(!isset($_GET['rating'])&&$data['rating']==''){
							$rating = 'unrated';
						}
						else{
							if(isset($_GET['rating'])){
								$rating = $_GET['rating'];
							}
							else{
								$rating = 'unrated';
							}
						}

						map_tags($id , $new , $link , $metaterms , 'EDIT');
						$querydata = "UPDATE postdata SET rating = '$rating' WHERE idnum = '$id' LIMIT 1";

						$result = mysqli_query($link , $query) or die(mysqli_error($link));
						$result = mysqli_query($link , $querydata) or die(mysqli_error($link));

						if(isset($_GET['rethumb'])&&$_GET['rethumb']!=''){
							if($ext=='png'or$ext=='jpg'or$ext=='jpeg'or$ext=='jpe'or$ext=='tiff')
							{
								$image = new Imagick("$imgck/$imagedir/$name");
								$image->setImageCompressionQuality(0);
								$image->stripImage();
								$image->thumbnailImage(150, 150, true);
								$image->writeImage("$imgck/$thumbdir/$thumb_name");
							}
							elseif($ext=='gif'or$ext=='apng')
							{
								exec("ffmpeg -i $imgck/$imagedir/$name -vframes 1 $imgck/$thumbdir/$thumb_name");
								$image = new Imagick("$imgck/$thumbdir/$thumb_name");
								$image->setImageCompressionQuality(0);
								$image->stripImage();
								$image->thumbnailImage(150, 150, true);
								$image->writeImage("$imgck/$thumbdir/$thumb_name");
							}
							elseif($ext=='mp4'or$ext=='webm')
							{
								exec("ffmpeg -ss 00:00:01 -i $imgck/$imagedir/$name -vframes 1 $imgck/$thumbdir/$thumb_name");
								$image = new Imagick("$imgck/$thumbdir/$thumb_name");
								$image->setImageCompressionQuality(0);
								$image->stripImage();
								$image->thumbnailImage(150, 150, true);
								$image->writeImage("$imgck/$thumbdir/$thumb_name");
							}
							elseif($ext=='mp3'or$ext=='flac'){
								$getID3 = new getID3;
								$tags = $getID3->analyze("$imgck/$imagedir/$name");
								if (isset($tags['comments']['picture']['0']['data'])) {
									$image = $tags['comments']['picture']['0']['data'];
									file_put_contents("$imgck/$thumbdir/$thumb_name", $image);
								}
							}
						}



						echo "<p class='text'> Updated information successfully</p><br />\n";
					}
					else {

						$tags = read_tags($link,$metaterms,$id,'EDIT');

						$imgq = "SELECT given_name , hash FROM postdata WHERE idnum=$id";
						$imgs = mysqli_query($link , $imgq) or die(mysqli_error($link));
						$imga = mysqli_fetch_array($imgs);
						$imgf = $imga['hash'];
						echo "<img id='image' src=\"$imagedir/$imgf\" alt=\"$tags\" title=\"$tags\"><br />\n"







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
Make Child Of: <input style="color:black;" name="childof" size="8" type="text" /><br />
Add To Pool <select name="pool_id" style="color:black;">
<option value="">Select...</option>
<?php
	$poolquery = "SELECT name , pool_id FROM pools ORDER BY name";
	$countquery = "SELECT COUNT(pool_id) AS total FROM pools";
	$countpool = mysqli_query($link , $countquery) or die(mysqli_error($link));
	$countarr = mysqli_fetch_array($countpool);
	$listed = $countarr['total'];
	$poolresult = mysqli_query($link , $poolquery) or die(mysqli_error($link));
	$poolarr = mysqli_fetch_array($poolresult);
	$lastname =$poolarr['name'];
	echo "<option value='$poolarr[pool_id]'>$poolarr[name]</option>";

	for( $i = 0 ; $i < $listed -1 ; $i++ ){



		$nquery = "SELECT * FROM pools WHERE name>'$lastname' LIMIT 1";
		$poolresult = mysqli_query($link , $nquery) or die(mysqli_error($link));
		$poolarr = mysqli_fetch_array($poolresult);
		echo "<option value='$poolarr[pool_id]'>$poolarr[name]</option>";
	}
?>
</select>
<br />
<input type="radio" name="append" value="first" size=""19 >: Place First
<input type="radio" name="append" value="last" size=""19 checked=''>: Place Last
<input type="radio" name="append" value="remove" size=""19 >: Remove From Pool
<br />
Regenerate Thumbnail: <input type="checkbox" name="rethumb" value="rethumb" size=""19 >
<br />
<input id="button-light-2" type="submit" value="Update" /> Or
<?php
echo "<a id='button-light-2' href=\"remove.php?id=$id\">Delete</a><br />";
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
