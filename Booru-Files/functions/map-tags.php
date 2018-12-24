<?php
	function map_tags($post_id , $tags , $link , $metaterms , $run_as){
		if($run_as=='UPLOAD'){
			$tags = ' '.$tags.' ';
			$tags = preg_replace('/\s\s+/',' ',$tags);
			$tags = substr($tags , 1 , strlen($tags)-2);
			$tag_array = preg_split('/\s+/',$tags);
			$tag_array = array_unique($tag_array);
			$run = count($tag_array);

			for( $i = 0 ; $i < $run ; $i++ ){

				$has_meta = false;
				$meta = count($metaterms);
				for( $o = 0 ; $o < $meta ; $o++ ){

					$check = strstr($tag_array[$i],"($metaterms[$o])");
					if($check){

						$type = substr($check, strrpos($check,'(')+1 , strrpos($check,')')-strrpos($check,'(')-1);
						if($type!=$metaterms[$o]){

						}
						else{
							$tag = substr($tag_array[$i], 0, strrpos($tag_array[$i],"_($metaterms[$o])"));
							$queryexists = "SELECT * FROM tags WHERE tagfull='$tag_array[$i]' AND type='$type' LIMIT 1";
							$sqlexists = mysqli_query($link, $queryexists)or die(mysqli_error($link));
							if(mysqli_num_rows($sqlexists)==0){

								$queryupdate = "INSERT INTO tags VALUES ( id ,'$type' , '$tag_array[$i]' , '$tag' )";
								$update = mysqli_query($link, $queryupdate)or die(mysqli_error($link));
								$query = "SELECT MAX(id) AS id FROM tags WHERE tagfull='$tag_array[$i]' AND type='$type' LIMIT 1";
								$result = mysqli_query($link,$query);
								$row = mysqli_fetch_array($result);

							}
							else{
								$query = "SELECT id FROM tags WHERE tagfull='$tag_array[$i]' AND type='$type' LIMIT 1";
								$result = mysqli_query($link,$query);
								$row = mysqli_fetch_array($result);
							}
							$has_meta = true;
						}
					}
				}
				if(!$has_meta){
					$type = "general";
					$queryexists = "SELECT * FROM tags WHERE tagfull='$tag_array[$i]' AND type='$type' LIMIT 1";
					$sqlexists = mysqli_query($link, $queryexists)or die(mysqli_error($link));
					if(mysqli_num_rows($sqlexists)==0){

						$queryupdate = "INSERT INTO tags VALUES ( id ,'$type' , '$tag_array[$i]' , '$tag_array[$i]' )";
						$update = mysqli_query($link, $queryupdate)or die(mysqli_error($link));
						$query = "SELECT MAX(id) AS id FROM tags WHERE tagfull='$tag_array[$i]' AND type='$type' LIMIT 1";
						$result = mysqli_query($link,$query);
						$row = mysqli_fetch_array($result);

					}
					else{
						$query = "SELECT id FROM tags WHERE tagfull='$tag_array[$i]' AND type='$type' LIMIT 1";
						$result = mysqli_query($link,$query);
						$row = mysqli_fetch_array($result);
					}
				}




				$query = "INSERT INTO tagmap VALUES ( \"$post_id:UPLOAD:$i\" , $post_id , $row[id] )";
				$result = mysqli_query($link , $query) or die(mysqli_error($link));
			}
		}
		elseif($run_as=='EDIT'){

			$tags = ' '.$tags.' ';
			$tags = preg_replace('/\s\s+/',' ',$tags);
			$tags = substr($tags , 1 , strlen($tags)-2);
			$tag_array = preg_split('/\s+/',$tags);
			$tag_array = array_unique($tag_array);
			$run = count($tag_array);
			$update_tagmap = "DELETE FROM tagmap WHERE post_id = $post_id";


			for( $i = 0 ; $i < $run ; $i++ ){
				// Check for if tag is on post, if not add
				$query = "SELECT id , tag_id FROM tags t , tagmap tm WHERE t.tagfull='$tag_array[$i]' AND tm.tag_id=t.id";
				$tag_result = mysqli_query($link,$query) or die(mysqli_error($link));
				$tag_row = mysqli_fetch_array($tag_result);
				//need to check for remove better
				if(mysqli_num_rows($tag_result)==0){
					$query = "SELECT id FROM tags WHERE tagfull='$tag_array[$i]'LIMIT 1";
					$result_exists = mysqli_query($link,$query) or die(mysqli_error($link));
					if(mysqli_num_rows($result_exists)==0){

						$has_meta = false;
						$meta = count($metaterms);
						for( $o = 0 ; $o < $meta ; $o++ ){

							$check = strstr($tag_array[$i],"($metaterms[$o])");
							if($check){

								$type = substr($check, strrpos($check,'(')+1 , strrpos($check,')')-strrpos($check,'(')-1);
								if($type!=$metaterms[$o]){

								}
								else{
									$tag = substr($tag_array[$i], 0, strrpos($tag_array[$i],"_($metaterms[$o])"));
									$queryexists = "SELECT * FROM tags WHERE tagfull='$tag_array[$i]' AND type='$type' LIMIT 1";
									$sqlexists = mysqli_query($link, $queryexists)or die(mysqli_error($link));
									if(mysqli_num_rows($sqlexists)==0){

										$queryupdate = "INSERT INTO tags VALUES ( id ,'$type' , '$tag_array[$i]' , '$tag' )";
										$update = mysqli_query($link, $queryupdate)or die(mysqli_error($link));
										$query = "SELECT MAX(id) AS id FROM tags WHERE tagfull='$tag_array[$i]' AND type='$type' LIMIT 1";
										$result = mysqli_query($link,$query);
										$row = mysqli_fetch_array($result);

									}
									else{
										$query = "SELECT id FROM tags WHERE tagfull='$tag_array[$i]' AND type='$type' LIMIT 1";
										$result = mysqli_query($link,$query);
										$row = mysqli_fetch_array($result);
									}
									$has_meta = true;
								}
							}
						}
						if(!$has_meta){
							$type = "general";
							$queryexists = "SELECT * FROM tags WHERE tagfull='$tag_array[$i]' AND type='$type' LIMIT 1";
							$sqlexists = mysqli_query($link, $queryexists)or die(mysqli_error($link));
							if(mysqli_num_rows($sqlexists)==0){

								$queryupdate = "INSERT INTO tags VALUES ( id ,'$type' , '$tag_array[$i]' , '$tag_array[$i]' )";
								$update = mysqli_query($link, $queryupdate)or die(mysqli_error($link));
								$query = "SELECT MAX(id) AS id FROM tags WHERE tagfull='$tag_array[$i]' AND type='$type' LIMIT 1";
								$result = mysqli_query($link,$query);
								$row = mysqli_fetch_array($result);

							}
							else{
								$query = "SELECT id FROM tags WHERE tagfull='$tag_array[$i]' AND type='$type' LIMIT 1";
								$result = mysqli_query($link,$query);
								$row = mysqli_fetch_array($result);
							}
						}
						$query = "INSERT INTO tagmap VALUES ( \"$post_id:EDIT:$i\" , $post_id , $row[id] )";
						$result = mysqli_query($link , $query) or die(mysqli_error($link));
						$update_tagmap .=" AND tag_id != $row[id]";
					}
					else{
						$id_row = mysqli_fetch_array($result_exists);
						$query = "INSERT INTO tagmap VALUES ( \"$post_id:EDIT:$i\" , $post_id , $id_row[id] )";

						$result = mysqli_query($link , $query) or die(mysqli_error($link));
						$update_tagmap .=" AND tag_id != $id_row[id]";
					}
				}
				else{

					$update_tagmap .=" AND tag_id != $tag_row[id]";
				}
			}
			mysqli_query($link,$update_tagmap) or die(mysqli_error($link));
		}
		elseif($run_as=='REMOVE'){
			$query = "DELETE FROM tagmap WHERE post_id = $post_id";
			mysqli_query($link,$query) or die(mysqli_error($link));
		}
		else{
			echo "<br />PLEASE SELECT A RUN TYPE<br />";
		}
	}
?>
