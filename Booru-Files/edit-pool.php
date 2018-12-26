<!DOCTYPE html>
<html>
	<head>
		<?php
			include_once 'config.php';
			include_once './functions/display-post.php';
			$link = mysqli_connect($mysql_host, $mysql_user, $mysql_password) or die('Could not connect: ' . mysqli_error($link));
			mysqli_select_db($link , $mysql_database) or die('Could not select database');
			if(isset($_GET['id'])&&$_GET['id']!='') {
				$id = $_GET['id'];
				if(isset($_POST['posts'])){
					$posts = ' '.$_POST['posts'].' ';
					$posts = preg_replace('#\s\s+#',' ',$posts);
					$posts = substr($posts,1,strlen($posts)-2);
					$posts_array = explode(' ',$posts);
					$count = count($posts_array);
					mysqli_query($link,"UPDATE pools SET count = $count WHERE pool_id = $id")or die(mysqli_error($link));
					mysqli_query($link,"DELETE FROM poolmap WHERE pool_id = $id")or die(mysqli_error($link));
					$passed = array();
					foreach($posts_array as $loc => $post_id){
						if(!in_array($post_id,$passed)){
							$passed[] = $post_id;
							$poolsql = "INSERT INTO poolmap VALUES ( \"$id:$post_id\" ,$id , $post_id , $loc )";
							mysqli_query($link,$poolsql)or die(mysqli_error($link));
						}
					}
				}
				$query_pool = mysqli_query($link,"SELECT * FROM pools WHERE pool_id = $_GET[id] LIMIT 1");
				$query_pool_map = mysqli_query($link,"SELECT post_id FROM poolmap WHERE pool_id = $_GET[id] ORDER BY location ASC");
				
				$pool_result = mysqli_fetch_array($query_pool);
				$pool_map_result = mysqli_fetch_array($query_pool_map);
			}
		?>
		<title>
			<?php
			if(isset($_GET['id'])&&$_GET['id']!='') {
				echo "$title - Edit Pool $pool_result[name]";
			}
			else {
				echo "$title - Pool Not found";
			}
			?>
		</title>
		<link rel="stylesheet" type="text/css" href="style.css" />
		<link rel="favorite icon" href="favicon.png" />
	</head>
	<body class="edit">
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
			<div id="divider">
			</div>
			<div id="poolview">
				<div id="poolcontent">
					<?php
						if(isset($_GET['del'])&&$_GET['del']=='true'){
							$query = "DELETE FROM pools WHERE pool_id=$id";
							mysqli_query($link,$query)or die(mysqli_error($link));
							$query = "DELETE FROM poolmap WHERE pool_id=$id";
							mysqli_query($link,$query)or die(mysqli_error($link));
							echo "Pool $pool_result[name] $pool_result[id] Deleted";
						}
						else{
							$post_array = array();
							while($pool_map_result){
								$post_array[] = $pool_map_result['post_id'];
								$post_string = '';
								$query = "SELECT * FROM postdata WHERE idnum=$pool_map_result[post_id]";
								$postq = mysqli_query($link , $query) or die(mysqli_error($link));
								if (!mysqli_num_rows($postq)==0){
									$row = mysqli_fetch_array($postq);
									display_post($link,$metaterms, $postq, $row);
								}
								$pool_map_result = mysqli_fetch_array($query_pool_map);
							}
							echo "<form action='edit-pool.php?id=$id' method='POST'>";
							echo "<div>Posts<br /><textarea id='inbox' name='posts' rows='10' cols='40'>";
							foreach($post_array as $post){
								$post_string.=" $post ";
							}
							$post_string = preg_replace('#\s\s+#',' ',$post_string);
							$post_string = substr($post_string,1,strlen($post_string)-2);
							echo "$post_string</textarea><br />";
							?>
							
							<input id="uploadbutton" type="submit" value="Update" />
							</form>
							<?php
							echo "<br />Pool ID: $pool_result[pool_id]<br />";
							echo "Pool Name: $pool_result[name]<br />";
							echo "<a id='newpool' href='edit-pool.php?id=$id&del=true'>Delete?</a>";
							if(isset($_POST['posts'])){
								echo "<br />BEFORE: $_POST[posts]<br />AFTER :$posts<br />";
								print_r($posts_array);
							}
							echo "<pre>$post_string</pre>";
						}
					?>
				</div>
			</div>
		</div>
	</body>
</html>