<!DOCTYPE html>
<html>
	<head>
	<link href="sicon2.png" type="image/png" rel="icon">
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<link rel="stylesheet" type="text/css" href="css/buttons.css" />
	<link rel="stylesheet" type="text/css" href="css/tags.css" />
	<link rel="stylesheet" type="text/css" href="css/tables.css" />
	<link rel="favorite icon" href="sicon2.png" />
	<?php
		include"config.php";
		$link = mysqli_connect($mysql_host, $mysql_user, $mysql_password) or die('Could not connect: ' . mysqli_error($link));
		mysqli_select_db($link , $mysql_database) or die('Could not select database');
		echo"<title>$title - Tags</title>";
	?>
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
		<div>
			<div id="sidebar">
				<form action="tags.php" method="GET">
					<table> </table>
					<input id="searchbox" style="width: 96%" name="q" type="text" <?php if(isset($_GET['q'])){echo "value=\"$_GET[q]\"";}?>/>
					<br />
					<select class="droplist" name="order" style="color:black;">
						<option value="">Order...</option>
						<option value="post"<?php if(isset($_GET['order'])&&$_GET['order']=='post'){echo "selected='selected'";} ?>>Post Count</option>
						<option value="type"<?php if(isset($_GET['order'])&&$_GET['order']=='type'){echo "selected='selected'";} ?>>Tag Type</option>
						<option value="tag"<?php if(isset($_GET['order'])&&$_GET['order']=='tag'){echo "selected='selected'";} ?>>Tag Name</option>
					</select>
					<br />
					<select class="droplist" name="by" style="color:black;">
						<option value="">By...</option>
						<option value="asc"<?php if(isset($_GET['by'])&&$_GET['by']=='asc'){echo "selected='selected'";} ?>>Ascending</option>
						<option value="desc"<?php if(isset($_GET['by'])&&$_GET['by']=='desc'){echo "selected='selected'";} ?>>Descending</option>
					</select>
					<br />
					<input id="button-light-2" type="submit" value="Search"/>
				</form>
			</div>
			<table class="basic-table">
				<thead>
					<tr>
						<td style="width:5%;"><b> Posts </b></td>
						<td style="width:10%;"><b> Type </b></td>
						<td style="width:25%;"><b> Tag </b></td>
					</tr>
				</thead>
				<?php
					
					if(isset($_GET['q'])&&$_GET['q']!=''){
						$search = "WHERE tagalias LIKE '$_GET[q]%'";
					}
					else{
						$search = '';
					}
					if(isset($_GET['order'])&&$_GET['order']!=''){
						if($_GET['order']=='post'){
							$order = "(SELECT COUNT(post_id) FROM(SELECT post_id , tag_id FROM tagmap tm , tags t WHERE tm.tag_id = t.id ) as table_a )";
						}
						if($_GET['order']=='type'){
							$order = "type";
						}
						if($_GET['order']=='tag'){
							$order = "tagfull";
						}
					}
					else{
						$order = "tagfull";
					}
					if(isset($_GET['by'])&&$_GET['by']!=''){
						if($_GET['by']=='asc'){
							$by = "DESC";
						}
						if($_GET['by']=='desc'){
							$by = "ASC";
						}
					}
					else{
						$by = "ASC";
					}
					$query = "SELECT * FROM tags $search GROUP BY tagfull ORDER BY $order $by";
					$result = mysqli_query($link,$query) or die(mysqli_error($link));
					$row = mysqli_fetch_array($result);
					while($row){
						echo "<tr>";
						$query_num = "SELECT COUNT(*) FROM(SELECT post_id FROM tagmap tm , tags t WHERE tm.tag_id = t.id AND t.tagfull = \"$row[tagfull]\" GROUP BY post_id ) as table_#";
						$num_result = mysqli_query($link,$query_num) or die(mysqli_error($link));
						$num_used = mysqli_fetch_array($num_result);
						
						echo "<td style='text-align:center;'> $num_used[0]</td>
						<td class=\"tagtype-$row[type]\"> $row[type] </td>
						<td><a href=\"search-post.php?q=$row[tagalias]\">$row[tagalias]</a></td> ";
						$row = mysqli_fetch_array($result);
						echo "</tr>";
					}
				?>
			</table>
		</div>
	</body>
</html>
