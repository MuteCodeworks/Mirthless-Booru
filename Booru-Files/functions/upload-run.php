<?php
	include "config.php";
	function upload( $link , $metaterms , $file , $ext , $rating , $tags , $imagedir , $thumbdir , $imgck , $allowed_filetypes , $i , $runtype , $dump_type){
		if(!in_array($ext, $allowed_filetypes)){
			echo "Unsupported Filetype .$ext";
		}
		else{

			$thumb_name = hash('sha512',$file);
			$name = $thumb_name.".$ext";

			echo "<br />$name<br />";
			$result = mysqli_query($link , "SELECT `hash` FROM `postdata` WHERE `hash` = '$name'") or die(mysqli_error($link));
			if(mysqli_fetch_array($result)){
				echo "Duplicate file entry detected\n";
			}
			else{
				if($runtype!='DUMP'){
					$file_loc = $_FILES['file']['tmp_name'][$i];
					$file_byte_size = $_FILES['file']['size'][$i];$file_size = $file_byte_size / 1024;$file_size /= 1024;
					echo $file_size;
					$file_type = $_FILES['file']['type'][$i];
				}
				else {
					$file_byte_size = filesize("$file");$file_size = $file_byte_size / 1024;$file_size /= 1024;
					echo $file_size;
					$file_type = filetype($file);
					$name = strtolower($name);
				}

				if($runtype!='DUMP'){
					$move = move_uploaded_file($file_loc,"$imagedir/$name");
				}
				else{
					if($dump_type=='COPY'){
						$move = copy($file,"$imagedir/$name");
					}
					else{
						$move = rename($file,"$imagedir/$name");
					}
				}
				if($move){

					if($ext=='png'or$ext=='jpeg'or$ext=='gif'or$ext=='apng'or$ext=='jpg'or$ext=='jpe'){
						$imgsize = getimagesize("$imagedir/$name");
						$width = $imgsize[0];
						$height = $imgsize[1];
					}
					echo "<br />Check flash: =>";
					if($ext=='swf'){
						$thumb_name = 'FlashThumb';
						$swfinfo = new swfheader(false);
						$swfinfo->loadswf("$imagedir/$name");
						$height = $swfinfo->height;
						$width = $swfinfo->width;
					}
					echo " END";
					if($ext=='txt'){
						$filethumb = "TextThumb";
						$width = 0;
						$height = 0;
					}
					if($ext=='mp4'or$ext=='webm'){
								$dime = shell_exec("ffprobe -v error -select_streams v:0 -show_entries stream=width,height -of csv=s=x:p=0 $imagedir/$name");
								sleep(1);
								$dima = preg_split('/x+/',$dime);
								$height = $dima[1];
								$width = $dima[0];
								if($height>=2200 or $file_size>=1073741824){
									$tags=" break ";
								}

								exec("ffmpeg -i $imagedir/$name -movflags +faststart -c copy $imagedir/faststart$name");
								$tmpname = 'faststart'."$name";
								$name = $tmpname;
					}
					if($ext=='mp3'or$ext=='flac'){
								$height = 0;
								$width = 0;
					}
					/*
					$query_new_id = "SELECT MAX(idnum) AS id FROM postdata LIMIT 1";
					$row = mysqli_fetch_array(mysqli_query($link,$query_new_id));
					$new_id = $row['id']+1;
					*/

					$querypost = "INSERT INTO postdata VALUES ( idnum , NOW(3) , '$name' , '$file' , '$thumb_name.png' , '$ext' , $file_size , '$rating' , $height , $width , 0 , ' ' , ' ' , ' ')";
					echo "<br />INSERT: $querypost<br />";
					mysqli_query($link , $querypost) or die(mysqli_error($link));
					$query_new_id = "SELECT idnum FROM postdata WHERE hash = '$name' LIMIT 1";
					echo "<br />QUERY: $query_new_id<br />";
					$row = mysqli_fetch_array(mysqli_query($link,$query_new_id));
					$new_id = $row['idnum'];
					echo "running map tags... ";
					map_tags($new_id , $tags , $link , $metaterms , 'UPLOAD');
					echo " Done";
					if($ext=='png'or$ext=='jpg'or$ext=='jpeg'or$ext=='jpe'or$ext=='tiff')
					{
						$image = new Imagick("$imgck/$imagedir/$name");
						$image->setImageCompressionQuality(0);
						$image->stripImage();
						$image->thumbnailImage(150, 150, true);
						$image->writeImage("$imgck/$thumbdir/$thumb_name.png");
						echo $file_type.' ';
					}
					elseif($ext=='gif'or$ext=='apng')
					{/*
						exec("ffmpeg -i $imgck/$imagedir/$name -vframes 1 $imgck/$thumbdir/$thumb_name.png");
						$image = new Imagick("$imgck/$thumbdir/$thumb_name.png");
						$image->setImageCompressionQuality(0);
						$image->stripImage();
						$image->thumbnailImage(150, 150, true);
						$image->writeImage("$imgck/$thumbdir/$thumb_name.png");
						echo $file_type.' ';
					*/}
					elseif($ext=='mp4'or$ext=='webm')
					{
						exec("ffmpeg -ss 00:00:01 -i $imgck/$imagedir/$name -vframes 1 $imgck/$thumbdir/$thumb_name.png");
						$image = new Imagick("$imgck/$thumbdir/$thumb_name.png");
						$image->setImageCompressionQuality(0);
						$image->stripImage();
						$image->thumbnailImage(150, 150, true);
						$image->writeImage("$imgck/$thumbdir/$thumb_name.png");
						echo $file_type.' ';
					}
					elseif($ext=='mp3'or$ext=='flac'){
						$getID3 = new getID3;
						$tags = $getID3->analyze("$imgck/$imagedir/$name");
						if (isset($tags['comments']['picture']['0']['data'])) {
							$image = $tags['comments']['picture']['0']['data'];
							file_put_contents("$imgck/$thumbdir/$thumb_name.png", $image);
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
					echo "Could not move file to $imagedir/ or create thumbnail in $thumbdir/ <br />";
				}
			}
		}
	}
?>
