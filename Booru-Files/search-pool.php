<!DOCTYPE html>
<html>
	<head>
	<link href="sicon2.png" type="image/png" rel="icon">
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<link rel="stylesheet" type="text/css" href="css/buttons.css" />
	<link rel="stylesheet" type="text/css" href="css/tables.css" />
	<link rel="favorite icon" href="sicon2.png" />
	<?php
		include"config.php";
		include"functions/page-navigate.php";
		$link = mysqli_connect($mysql_host, $mysql_user, $mysql_password) or die('Could not connect: ' . mysqli_error($link));
		mysqli_select_db($link , $mysql_database) or die('Could not select database');
		echo"<title>$title - Pools</title>";
	?>
	</head>
	<body id="pools">
		<div id="header">
			<span id="title" style="font-size: 25px; font-weight: bold"><?php echo "$title"; ?></span>
			<div id="navbar">
				<a id='button-dark-2'  href="index.php">Home</a>
				<a id='button-dark-2' href="search-post.php">Posts</a>
				<a id='button-dark-2' href="tags.php">Tags</a>
				<a id='button-dark-2' href="search-pool.php">Pools</a>
				<a id='button-dark-2' href="upload.php">Upload</a>
				<a id='button-dark-2' href="about.php">About</a>
				<a id='button-dark-2' href="new-pool.php" >New Pool</a>
			</div>
		</div>
			<table class="basic-table" style="width:70%;" >
			<thead>
				<tr>
					<form action='search-pool.php' method='get'>
						<td style="width:35%;">Name
						<div style="display:inline-block;position:relative;left:10%;">
							<input style='color:#000;' type='text' name='q'>
							<input style='color:#000;' type='submit' value='Search'>
						</div>
						</td>
						<td style="width:5%;">Posts</td>
						<td style="width:2%;">Rating</td>
					</form>
				</tr>
			</thead>
			<?php
				$max_pool = 20;
				$numpoolsquery = "SELECT COUNT(*) FROM pools ";
				$query = "SELECT * FROM pools ORDER BY time DESC ";
				if(isset($_GET['p'])){
					$page = $_GET['p'];
					$query_limit = ($page-1) * $max_pool;
				}
				else{
					$page = 1;
					$query_limit = 0;
				}
				$query .= "LIMIT $query_limit , $max_pool ";
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
						echo "<tr>";
						echo "<td><a href='view-pool.php?id=$row[pool_id]' >$row[name]</a></td>";
						echo "<td>$row[count]</td>";
						echo"</tr>\n";
						$row = mysqli_fetch_array($poolsres);
					}
				}
				echo "</table>";
			?>
	</body>
	<footer>
		<?php
			$numpages = ceil($numpools / $max_pool);
			echo "<div id=\"pages\">\n";
			if($numpages != 0){
				$pass = array('current_page' => $page, 'php' =>'search-pool.php', 'max_page' => $numpages, 'get' =>$_GET,);
				page_navigate( $pass );
			}
			echo"</div>";
		?>
	</footer>
</html>
