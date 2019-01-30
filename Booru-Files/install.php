<!DOCTYPE html>
<html style="background:#000;color:#fff;font-size:16px;font-family:times-new-roman">
	<head>
		<?php
		// <div>|| if you can see this php is not installed properly ||</div><br />
			$passedcheck = true;
			$supported_filetypes = array("jpe", "jpeg", "jpg", "png", "tiff", "gif", "apng", "mp4", "webm", "flac", "mp3", "swf", "txt",);
			$varcheckarr = array("sqlhost", "sqldb", "sqlusr", "sitename", "storedir", "thumbdir", "getidpath", "pathto",);
			$tables = array("CREATE TABLE poolmap ( map_id TEXT NOT NULL,pool_id INT NOT NULL,post_id INT NOT NULL,location INT NOT NULL)","CREATE TABLE pools (pool_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,name TEXT NOT NULL,count INT NULL,time DATETIME(3) NOT NULL)","CREATE TABLE postdata (idnum INT NOT NULL AUTO_INCREMENT PRIMARY KEY,date DATETIME(3) NOT NULL,hash TEXT NOT NULL,given_name TEXT NOT NULL,thumb TEXT NOT NULL,type TEXT NOT NULL,size DOUBLE NOT NULL,rating TEXT NOT NULL,height INT NOT NULL,width INT NULL,ishidden TINYINT NOT NULL,parentof TEXT NOT NULL,childto TEXT NOT NULL,isinpool TEXT NOT NULL)","CREATE TABLE tagmap (map_id TEXT NOT NULL,post_id INT NOT NULL,tag_id INT NOT NULL)","CREATE TABLE tags (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,type TEXT NOT NULL,tagfull TEXT NOT NULL,tagalias TEXT NOT NULL)",);
			foreach($varcheckarr as $check){
				if(!isset($_POST["$check"])){
					$passedcheck = false;
				}
			}
			if(!$passedcheck ){
				echo "Please Fill Out All Required Fields";
			}
			else{
				if($link = mysqli_connect($_POST['sqlhost'],$_POST['sqlusr'],$_POST['sqlpass'])){
					if(mysqli_query($link,"SHOW DATABASES LIKE '$_POST[sqldb]'")){
						mysqli_query($link,"DROP DATABASE $_POST[sqldb]");
					}
					if(mysqli_query($link,"CREATE DATABASE $_POST[sqldb] CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;")){
						$madetables = true;
						mysqli_select_db($link,$_POST['sqldb']);
						foreach($tables as $mktables){
							$bool = mysqli_query($link,$mktables)or die(mysqli_error($link));
						}
						if(!$bool){
							echo "Could not make tables<br />";
						}
						else{
							$os = PHP_OS;
							if(substr($os,0,3)=='WIN'){
								chmod($_PATH['pathto'],777);
							}
							else{
							}
							if(!is_dir("./$_POST[storedir]")){
								if(!mkdir("./$_POST[storedir]")){
									echo "Could not make $_POST[storedir]<br />";
								}
							}
							if(!is_dir("./$_POST[thumbdir]")){
								if(!rename("./thumbs","./$_POST[thumbdir]")){
									echo "Could not rename thumbs to: $_POST[thumbdir]<br />";
								}
							}
							if($_POST['dmpdir']==''){
								$dmpdir = "dump";
								mkdir("./$dmpdir");
							}
							else{
								$dmpdir = $_POST['dmpdir'];
								mkdir("./$dmpdir");
							}
							$metastr = '';
							$typestr = '';
							$dtstr = '';
							if($_POST['dmpdftgs']!=''){
								$dumpstr = " ".$_POST['dmpdftgs']." ";
								$dumpstr = preg_replace('#\s\s+#',' ',$dumpstr);
								$dumpstr = substr($dumpstr,1,strlen($dumpstr)-2);
								$dumparr = explode(' ',$dumpstr);
								foreach($dumparr as $key => $str){
									$dtstr .= "$str"."\r\n";
								}
							}
							if(!$_POST['metastr']==''){
								$meta = " ".$_POST['metastr']." ";
								$meta = preg_replace('#\s\s+#',' ',$meta);
								$meta = substr($meta,1,strlen($meta)-2);
								$metaarr = explode(' ',$meta);
								foreach($metaarr as $key => $str){
									$metastr .= "$key => '$str',\n";
								}
							}
							foreach($_POST['filetype'] as $str){
								$typestr .= "'$str',";
							}
							if($_POST['dmptyp']!='COPY'or$_POST['dmptyp']!='MOVE'){
								$dmpt = 'COPY';
							}
							else{
								$dmpt = $_POST['dmptyp'];
							}
							$config = fopen("config.php",'w');
							fwrite($config,
"<?php
//paths
\$title = \"$_POST[sitename]\";
\$imagedir =\" $_POST[storedir]\";
\$thumbdir = \"$_POST[thumbdir]\";
\$imgck = \"$_POST[pathto]\";
\$getid3_path = \"$_POST[getidpath]\";
\$dump_dir = \"$dmpdir\";
\$dtags_file = \"dump_tags.txt\";
//settings
\$metaterms = array(\n$metastr);
\$allowed_filetypes = array($typestr);
\$dump_type = \"$dmpt\";//COPY or MOVE
\$OS = \"$os\";
//sql
\$mysql_host = \"$_POST[sqlhost]\";
\$mysql_database = \"$_POST[sqldb]\";
\$mysql_user = \"$_POST[sqlusr]\";
\$mysql_password = \"$_POST[sqlpass]\";
?>");
							fclose($config);
							$dtags = fopen("dump_tags.txt",'w');
							fwrite($dtags,$dtstr);
							fclose($dtags);
							header("location: ./index.php");
						}
					}
					else{
						echo "Could not create database";
					}
				}
				else{
					die(mysqli_connect_error());
				}
			}
		?>
	</head>
	<body>
		<div style="width:75%;position:absolute;left:50%;transform:translate(-50%,50%);text-align:center;border:#fff 1px dashed;">
			<form action="install.php" method="post">
				<div style="display:inline-block;max-width:80%;">
					<p>SQL Setup: Please supply the database host and name you wish to use, along with the username and password (if there is one)</p>
					*<input type="text" name="sqlhost" placeholder="Hostname" style="color:#000;">
					*<input type="text" name="sqldb" placeholder="Database" style="color:#000;">
					*<input type="text" name="sqlusr" placeholder="User" style="color:#000;">
					<input type="text" name="sqlpass" placeholder="Password" style="color:#000;">
				</div>
				<br />
				<div style="display:inline-block;max-width:80%;">
					<p>Site Setup: Please supply the site name and metatags, separate metatags by spaces</p>
					*<input type="text" name="sitename" placeholder="Site Name" style="color:#000;">
					<input type="text" name="metastr" placeholder="Meta Tags" style="color:#000;">
					<br />
					<p>To customize the colours of metatags edit /css/tags.css</p>
				</div>
				<br />
				<div style="display:inline-block;max-width:80%;">
					<p>Path Setup: Please specify the names you wish to use for file storage and thumb storage along with the path to getid3 and the path to the install didrectory of this booru</p>
					*<input type="text" name="storedir" placeholder="Storage Name" style="color:#000;">
					*<input type="text" name="thumbdir" placeholder="Thumb Name" style="color:#000;">
					*<input type="text" name="getidpath" placeholder="getid3 Path" style="color:#000;">
					*<input type="text" name="pathto" placeholder="Install Path" style="color:#000;">
				</div>
				<br />
				<div style="display:inline-block;max-width:80%;">
					<p>File Dump Setup: These are for file dumping and are not necessary to run the booru, however they allow you bulk upload files very quickly. separate tags with a space</p>
					<input type="text" name="dmpdir" placeholder="Dump Directory" style="color:#000;">
					<input type="text" name="dmptyp" placeholder="Move or Copy" style="color:#000;">
					<input type="text" name="dmpdftgs" placeholder="Default Tags" style="color:#000;">
				</div>
				<br />
				<div style="display:inline-block;max-width:80%;">
				<p>Optional Settings: This currently consists of file type selection, uncheck the file types you do not wish to allow on this booru</p>
				<?php
					foreach($supported_filetypes as $type){
						echo " $type:<input type='checkbox' name='filetype[]' value='$type' checked> ";
					}
				?>
				</div>
				<div style="margin:6px;">
					<input type="submit" value="Install">
				</div>
			</form>
		</div>
	</body>
</html>
