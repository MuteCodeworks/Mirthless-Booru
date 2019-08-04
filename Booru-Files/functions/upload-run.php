<?php
	include "config.php";
	include_once "swfheader.class.php";
	function upload( $link , $metaterms , $file , $ext , $rating , $tags , $description , $imagedir , $thumbdir , $imgck , $allowed_filetypes , $i , $runtype , $dump_type ){
		if(!in_array($ext, $allowed_filetypes)){
			echo "Unsupported Filetype .$ext";
		}
		else{
			if($runtype!='DUMP'){
				$thumb_name = hash_file('SHA3-256',$_FILES['file']['tmp_name'][$i]);
			}
			else{
				$thumb_name = hash_file('SHA3-256',"$file");
			}
			$tmpval = substr($thumb_name, 0, 4);
			$filedir2 = substr($tmpval, -2);
			$filedir1 = substr($tmpval, 0, 2); 
			$name = "$filedir1/$filedir2/".$thumb_name.".$ext";
			$vidname = $thumb_name;
			$result = mysqli_query($link , "SELECT `hash` FROM `postdata` WHERE `hash` = '$name'") or die(mysqli_error($link));
			if(mysqli_num_rows($result)>0){
				#echo "<div style='color:blue'><i><b>Duplicate file entry</b></i></div>";
				return 'dup';
			}
			else{
				if($runtype!='DUMP'){
					$file_loc = $_FILES['file']['tmp_name'][$i];
					$file_byte_size = $_FILES['file']['size'][$i];$file_size = $file_byte_size / 1024;$file_size /= 1024;
					$file_type = $_FILES['file']['type'][$i];
				}
				else {
					$file_byte_size = filesize("$file");$file_size = $file_byte_size / 1024;$file_size /= 1024;
					$file_type = filetype($file);
					$name = strtolower($name);
				}
				
				//check and make directorys
				if(!is_dir("$imagedir/$filedir1")){
					mkdir("$imagedir/$filedir1");
					mkdir("$imagedir/$filedir1/$filedir2");
				}
				else if(!is_dir("$imagedir/$filedir1/$filedir2")){
					mkdir("$imagedir/$filedir1/$filedir2");
				}
				else{
					//Nothing to do
				}
				if(!is_dir("$thumbdir/$filedir1")){
					mkdir("$thumbdir/$filedir1");
					mkdir("$thumbdir/$filedir1/$filedir2");
				}
				elseif(!is_dir("$thumbdir/$filedir1/$filedir2")){
					mkdir("$thumbdir/$filedir1/$filedir2");
				}
				else{
					//Nothing to do
				}
				//
				
				//move the file
				$thumb_name = "$filedir1/$filedir2/".$thumb_name;
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
				//
				
				if($move){
					//get file dimensions
					if($ext=='png'or$ext=='jpeg'or$ext=='gif'or$ext=='apng'or$ext=='jpg'or$ext=='jpe'or$ext=='tiff'or$ext=='tif'){
						$imgsize = getimagesize("$imagedir/$name");
						$width = $imgsize[0];
						$height = $imgsize[1];
					}
					if($ext=='swf'){
						$thumb_name = 'FlashThumb';
						$swfinfo = new swfheader(false);
						$swfinfo->loadswf("$imagedir/$name");
						$height = $swfinfo->height;
						$width = $swfinfo->width;
					}
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
									$tags=" broken_file ";
								}
								$oldname = $name;
								$name = "$filedir1/$filedir2/".$vidname."faststart.$ext";
								exec("ffmpeg -i $imagedir/$oldname -movflags +faststart -c copy $imagedir/$name");
					}
					if($ext=='mp3'or$ext=='flac'){
								$height = 0;
								$width = 0;
					}
					//
					
					//make thumbnails
					if($ext=='png'or$ext=='jpg'or$ext=='jpeg'or$ext=='jpe'or$ext=='tiff'or$ext=='tif'){
						$image = new Imagick("$imgck/$imagedir/$name");
						$image->setImageCompressionQuality(00);
						$image->stripImage();
						$image->thumbnailImage(150, 150, true);
						$image->writeImage("$imgck/$thumbdir/$thumb_name.png");
					}
					elseif($ext=='gif'or$ext=='apng'){
						exec("ffmpeg -i $imgck/$imagedir/$name -vframes 1 $imgck/$thumbdir/$thumb_name.png");
						if(file_exists(("$imgck/$thumbdir/$thumb_name.png"))){
							$image = new Imagick("$imgck/$thumbdir/$thumb_name.png");
							$image->setImageCompressionQuality(00);
							$image->stripImage();
							$image->thumbnailImage(150, 150, true);
							$image->writeImage("$imgck/$thumbdir/$thumb_name.png");
						}
					}
					elseif($ext=='mp4'or$ext=='webm'){
						exec("ffmpeg -ss 00:00:00 -i $imgck/$imagedir/$name -vframes 1 $imgck/$thumbdir/$thumb_name.png");
						if(file_exists(("$imgck/$thumbdir/$thumb_name.png"))){
							$image = new Imagick("$imgck/$thumbdir/$thumb_name.png");
							$image->setImageCompressionQuality(00);
							$image->stripImage();
							$image->thumbnailImage(150, 150, true);
							$image->writeImage("$imgck/$thumbdir/$thumb_name.png");
						}
					}
					elseif($ext=='mp3'or$ext=='flac'){
						$getID3 = new getID3;
						$getID3_tags = $getID3->analyze("$imgck/$imagedir/$name");
						if (isset($getID3_tags['comments']['picture']['0']['data'])) {
							$image = $getID3_tags['comments']['picture']['0']['data'];
							file_put_contents("$imgck/$thumbdir/$thumb_name.png", $image);
						}
					}
					else{
					}
					//
					
					//add to database
					$querypost = "INSERT INTO postdata VALUES ( idnum , NOW(3) , '$name' , '$file' , '$thumb_name.png' , '$ext' , $file_size , '$rating' , $height , $width , '$description' , 0 , ' ' , ' ' , ' ')";
					mysqli_query($link , $querypost) or die(mysqli_error($link));
					$query_new_id = "SELECT idnum FROM postdata WHERE hash = '$name' LIMIT 1";
					$row = mysqli_fetch_array(mysqli_query($link,$query_new_id));
					$new_id = $row['idnum'];
					map_tags($new_id , $tags , $link , $metaterms , 'UPLOAD');
					//
					
					return true;
				}
				else
				{
					return false;
				}
			}
		}
	}
?>
