<!DOCTYPE html>
<html>
	<head>
		<title>
			<?php
				include_once 'config.php';
				include_once 'functions/map-tags.php';
				include_once "$getid3_path";
				include_once "functions/upload-run.php";
				include_once "functions/swfheader.class.php";
				echo "$title";

			?>
		</title>
		<link rel="stylesheet" type="text/css" href="css/style.css" />
		<link rel="stylesheet" type="text/css" href="css/buttons.css" />
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
				<input id="searchbox" name="q" size="22" type="text"
				<?php
					if(isset($_GET['q']))
					{
						echo "value=\"$_GET[q]\"";
					}
				?>/><br />
				<input id="button-dark-2" type="submit" value="Search" />
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
		Tags:<br /><textarea name="tags" rows="10" cols="40" /></textarea><br/>
		Description:<br/><textarea name="desc" rows="10" cols="40"/></textarea><br/>
		Rating:<br /><input type="radio" name="rating" value="safe">Safe
			<input type="radio" name="rating" value="questionable">Questionable
			<input type="radio" name="rating" value="explicit">Explicit
		<br /><button id="button-light-2" type="submit" name="btn-up">Upload</button>
	</form>
	<?php
	  }
		$check = array('<b>','</b>','<i>','</i>','<br />','	','    ',"\n","\r\n","'","\\","/");
		$sub = array('[b]','[/b]','[i]','[/i]','[br]','[t]','[t]','[br]','[br]',"''","\\\\","\/");
		if(isset($_POST['btn-up'])&&isset($_POST['rating'])){

			$total = count($_FILES['file']['name']);
			for($i = 0; $i < $total ; $i++ ){
				
				$desc = str_ireplace($check,$sub,$_POST['desc']);
				$tags = preg_replace("/\s\s+/" , " " , $_POST['tags']);
				$file = $_FILES['file']['name'][$i];
				$ext = strtolower(pathinfo($file)['extension']);
				$rating = $_POST['rating'];
				
				if(upload($link, $metaterms, $file , $ext , $rating, "$tags" ,$desc, $imagedir , $thumbdir , $imgck , $allowed_filetypes , $i , 'UPLOAD', $dump_type)){
					echo "File Successfully Uploaded to Server";
				}
			}
		}
		if(isset($_POST['btn-up'])&&!isset($_POST['rating'])){
			echo "Please select a rating";
		}
	?>
	</div>
	</body>
</html>
