<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
	<head>
		<title>
			<?php
				include 'config.php';
				echo "$title";
				include("../../getid3/getid3.php");
			?>
		</title>
		<link rel="stylesheet" type="text/css" href="style.css" />
		<link rel="favorite icon" href="favicon.png" />
	</head>
<body id="upload">
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
			<input id="searchbox" name="q" size="22" type="text"
			<?php
				if(isset($_GET['q']))
				{
					echo "value=\"$_GET[q]\"";
				}
			?>/><br />
			<input id="button" type="submit" value="Search" />
		</div>
	</form>
</div>
<div id="content">
<?php
  if(!(isset($_FILES['file']) && isset($_POST['tags'])))
  {
?>
<form action="upload.php" method="POST" enctype="multipart/form-data">
	Note: Uploading Multiple Files May Require Editing Tags And Rating Individualy<br />
	File: <input name="file[]" type="file" multiple="multiple"/><br />
	Tags:<br /><textarea id="inbox" name="tags" rows="10" cols="40" /></textarea><br />
	Rating:<br /><input type="radio" name="rating" value="safe">Safe
		<input type="radio" name="rating" value="questionable">Questionable
		<input type="radio" name="rating" value="explicit">Explicit
	<br /><button id="uploadbutton" type="submit" name="btn-up">Upload</button>
	<!--<input id="uploadbutton" type="submit" value="Upload" /><br />-->
</form>
<?php
  } 
	
	if(isset($_POST['btn-up'])&&isset($_POST['rating'])){
		
		$files = array_filter($_FILES['file']);
		$total = count($_FILES['file']['name']);
		for($i = 0; $i < $total ; $i++ ){
			
			$tags = $_POST['tags'].' tagme ';
			$allowed_filetypes = array("jpe" , "gif", "jpg", "jpeg", "png" , "mp4", "webm", "mp3", "swf", "txt");
			$file = $_FILES['file']['name'][$i];
			$ext = strtolower(pathinfo($file)['extension']);
			$rating = $_POST['rating'];
			$length = "0";
		
			if(!in_array($ext, $allowed_filetypes))
			{
				echo "Unsupported Filetype .$ext";
			}
			else
			{
				$newname = $_FILES['file']['name'][$i];
				
				$file = md5_file($_FILES['file']['tmp_name'][$i]).".$ext";
				$filethumb = md5_file($_FILES['file']['tmp_name'][$i]);
				

				$result = mysqli_query($link , "SELECT `name` FROM `posts` WHERE `name` = '$file'") or die(mysqli_error($link));
				if(mysqli_fetch_array($result)){
					echo "Duplicate file entry detected\n";
				}
					
				else
				{
					$newa = preg_split('/\s+/', $tags);
					$newa = array_unique($newa);
					sort($newa);
					$tags = implode(" ", $newa);
					
					$file_loc = $_FILES['file']['tmp_name'][$i];
					$file_size = $_FILES['file']['size'][$i];
					$file_type = $_FILES['file']['type'][$i];
					
					$new_size = $file_size/1024;
					
					$new_file_name = strtolower($file);
					
					if(move_uploaded_file($file_loc,$imagedir.$new_file_name))
					{	
						if($ext=='png'or$ext=='jpeg'or$ext=='gif'or$ext=='jpg'or$ext=='jpe'){
							$imgsize = getimagesize("$imagedir/$new_file_name");
							$width = $imgsize[0];
							$height = $imgsize[1];
						}
						if($ext=='swf'){
							
							$getID3 = new getid3;
							$ThisFileInfo = $getID3->analyze("$imagedir/$new_file_name");
							$width = $ThisFileInfo['video']['resolution_x'];
							$height = $ThisFileInfo['video']['resolution_y'];
						}
						if($ext=='txt'){
							$filethumb = "TextThumb";
							$width = 0;
							$height = 0;
						}
						if($ext=='mp4'or$ext=='webm'){
							$dime = shell_exec("ffprobe -v error -select_streams v:0 -show_entries stream=width,height -of csv=s=x:p=0 $imagedir/$new_file_name");
							sleep(1);
							$dima = preg_split('/x+/',$dime);
							$height = $dima[1];
							$width = $dima[0];
							if($height>=2200 or $file_size>=1073741824){
								$tags=" non-playable";
							}
							
							exec("ffmpeg -i $imagedir/$new_file_name -movflags +faststart -c copy $imagedir/faststart$new_file_name");
							$tmpname = 'faststart'."$new_file_name";
							$new_file_name = $tmpname;
						}
						if($ext=='mp3'){
							$height = 0;
							$width = 0;
							
						}
						$idcheck = mysqli_query($link , "select max(idnum) from posts ") or die(mysqli_error($link));
						$row = mysqli_fetch_array($idcheck);
						$id = (int) $row['max(idnum)'];
						$id = $id +1;
						$querypost = "INSERT INTO posts VALUES ( $id , '$newname' , '$new_file_name' , '$filethumb.png' , NOW() , '$tags ' )";
						$querydata = "INSERT INTO postdata values ( $id , '$ext' , $new_size , '$rating' , $height , $width , 0 , ' ' )";
						mysqli_query($link , $querypost) or die(mysqli_error($link));
						mysqli_query($link , $querydata) or die(mysqli_error($link));
						
						if($ext=='png'or$ext=='jpg'or$ext=='jpeg'or$ext=='jpe')
						{
							$image = new Imagick("$imgck/$imagedir/$new_file_name");
							$image->setImageCompressionQuality(0);
							$image->stripImage();
							$image->thumbnailImage(150, 150, true);
							$image->writeImage("$imgck/thumbs/$filethumb.png");
							echo $file_type.' ';
						}
						elseif($ext=='gif')
						{
							
							exec("ffmpeg -i $imgck/$imagedir/$new_file_name -vframes 1 $imgck/thumbs/$filethumb.png");
							$image = new Imagick("$imgck/thumbs/$filethumb.png");
							$image->setImageCompressionQuality(0);
							$image->stripImage();
							$image->thumbnailImage(150, 150, true);
							$image->writeImage("$imgck/thumbs/$filethumb.png");
							echo $file_type.' ';
						}
						elseif($ext=='mp4'or$ext=='webm')
						{
							exec("ffmpeg -ss 00:00:01 -i $imgck/$imagedir/$new_file_name -vframes 1 $imgck/thumbs/$filethumb.png");
							$image = new Imagick("$imgck/thumbs/$filethumb.png");
							$image->setImageCompressionQuality(0);
							$image->stripImage();
							$image->thumbnailImage(150, 150, true);
							$image->writeImage("$imgck/thumbs/$filethumb.png");
							echo $file_type.' ';
						}
						elseif($ext=='mp3'){
							$getID3 = new getID3;
							$tags = $getID3->analyze("$imgck/$imagedir/$new_file_name");
							if (isset($tags['comments']['picture']['0']['data'])) {
									$image = $tags['comments']['picture']['0']['data'];
									file_put_contents("$imgck/thumbs/$filethumb.png", $image);
									echo $file_type.' ';
							}
						}
						else{
							echo $file_type.' ';
						}

						echo "File uploaded and added to database successfully\n <br />";

					}
					else
					{
						echo "Could not move file to $imagedir or create thumbnail in thumbs/ <br />";
					}
				}
			}
		}
	}
	if(isset($_POST['btn-up'])&&!isset($_POST['rating'])){
		echo "Please select a rating";
	}
?>
</div>
</div>
</body>
</html>
