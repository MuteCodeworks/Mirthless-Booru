<?php
	include_once'checkend.php';
	function map_tags($post_id , $tags , $link , $metaterms , $run_as){
		$tags = ' '.$tags.' ';
		$tags = preg_replace('/\s\s+/',' ',$tags);
		$tags = substr($tags , 1 , strlen($tags)-2);
		$tag_array = preg_split('/\s+/',$tags);
		$tag_array = array_unique($tag_array);
		$run = count($tag_array);
		$meta = count($metaterms);
		if($run_as=='UPLOAD'){
			for( $i = 0 ; $i < $run ; $i++ ){
				$has_meta = false;
				for( $o = 0 ; $o < $meta ; $o++ ){
					if(strstr($tag_array[$i],"$metaterms[$o]:")){
						$check = strstr($tag_array[$i],"$metaterms[$o]:");
					}
					if(isset($check)&&$check!=''){
						$type = substr($check,0, strrpos($check,':'));
						if($type!=$metaterms[$o]){

						}
						else{
							$tag = substr($check,strrpos($check,':')+1);
							$queryexists = "SELECT * FROM tags WHERE tagfull='$tag_array[$i]' AND type='$type' LIMIT 1";
							$sqlexists = mysqli_query($link, $queryexists)or die(mysqli_error($link));
							if(mysqli_num_rows($sqlexists)<1){

								$queryupdate = "INSERT INTO tags VALUES ( id ,'$type' , '$tag_array[$i]' , '$tag' )";
								mysqli_query($link, $queryupdate)or die(mysqli_error($link));
							}
							else{
								
							}
							$has_meta = true;
						}
					}
				}
				unset($check);
				if(!$has_meta){
					$type = "general";
					$queryexists = "SELECT * FROM tags WHERE tagfull='$tag_array[$i]' AND type='$type' LIMIT 1";
					$sqlexists = mysqli_query($link, $queryexists)or die(mysqli_error($link));
					if(mysqli_num_rows($sqlexists)==0){
						$queryupdate = "INSERT INTO tags VALUES ( id ,'$type' , '$tag_array[$i]' , '$tag_array[$i]' )";
						$update = mysqli_query($link, $queryupdate)or die(mysqli_error($link));
					}
					else{
						
					}
				}
				$query = "SELECT id FROM tags WHERE tagfull='$tag_array[$i]' AND type='$type' LIMIT 1";
				$result = mysqli_query($link,$query);
				$row = mysqli_fetch_array($result);
				
				$query = "INSERT INTO tagmap VALUES ( \"$post_id:UPLOAD:$i\" , $post_id , $row[id] )";
				$result = mysqli_query($link , $query) or die(mysqli_error($link));
			}
		}
		elseif($run_as=='EDIT'){
			$deletefrom = "DELETE FROM tagmap WHERE post_id = $post_id";
			for( $i = 0 ; $i < $run ; $i++ ){
				$has_meta = false;
				$querymap = "SELECT m.post_id , m.tag_id FROM tagmap m WHERE m.post_id = $post_id AND m.tag_id = (SELECT id FROM tags WHERE tagfull = \"$tag_array[$i]\" LIMIT 1)";
				$map_exists = mysqli_query($link,$querymap) or die(mysqli_error($link));
				if(mysqli_num_rows($map_exists)>0){
					$row = mysqli_fetch_array($map_exists);
					$deletefrom .= " AND tag_id != $row[tag_id]";
				}
				else{
					$query = "SELECT * FROM tags WHERE tagfull='$tag_array[$i]'LIMIT 1";
					$result_exists = mysqli_query($link,$query) or die(mysqli_error($link));
					if(mysqli_num_rows($result_exists)>0){
						$id_row = mysqli_fetch_array($result_exists);
						$query = "INSERT INTO tagmap VALUES ( \"$post_id:EDIT:$i\" , $post_id , $id_row[id] )";
						$deletefrom .= " AND tag_id != $id_row[id]";
						$result = mysqli_query($link , $query) or die(mysqli_error($link));
					}
					else{
						for( $o = 0 ; $o < $meta ; $o++ ){
							if(strstr($tag_array[$i],"$metaterms[$o]:")){
								$check = strstr($tag_array[$i],"$metaterms[$o]:");
							}
							if(isset($check)&&$check!=''){
								$type = substr($check,0, strrpos($check,':'));
								if($type!=$metaterms[$o]){

								}
								else{
									$tag = substr($check,strrpos($check,':')+1);
									$queryupdate = "INSERT INTO tags VALUES ( id ,'$type' , '$tag_array[$i]' , '$tag' )";
									mysqli_query($link, $queryupdate)or die(mysqli_error($link));
									$has_meta = true;
								}
							}
						}
						unset($check);
						if(!$has_meta){
							$type = "general";
							$queryupdate = "INSERT INTO tags VALUES ( id ,'$type' , '$tag_array[$i]' , '$tag_array[$i]' )";
							$update = mysqli_query($link, $queryupdate)or die(mysqli_error($link));
						}
						$query = "SELECT id FROM tags WHERE tagfull='$tag_array[$i]' AND type='$type' LIMIT 1";
						$result = mysqli_query($link,$query);
						$id_row = mysqli_fetch_array($result);
						
						$query = "INSERT INTO tagmap VALUES ( \"$post_id:UPLOAD:$i\" , $post_id , $id_row[id] )";
						$deletefrom .= " AND tag_id != $id_row[id]";
						$result = mysqli_query($link , $query) or die(mysqli_error($link));
					}
				}
			}
			mysqli_query($link,$deletefrom) or die(mysqli_error($link));
		}
		else{
			
		}
	}
?>