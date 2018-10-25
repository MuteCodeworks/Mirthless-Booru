<?php
		function database_tag($raw_tag, $link ,$metaterms , &$tag_id){
		$has_meta = false;
		$run = count($metaterms);
		for( $i = 0 ; $i < $run ; $i++ ){
			$check = strstr($raw_tag,"($metaterms[$i])");
			if($check){
				
				$type = substr($check, strrpos($check,'(')+1 , strrpos($check,')')-strrpos($check,'(')-1);
				if($type!=$metaterms[$i]){
					
				}
				else{
					$tag = substr($raw_tag, 0, strrpos($raw_tag,'($metaterms[$i])'));
					$queryexists = "SELECT * FROM tags WHERE tagfull='$raw_tag' AND type='$type' LIMIT 1";
					$sqlexists = mysqli_query($link, $queryexists)or die(mysqli_error($link));
					if(mysqli_num_rows($sqlexists)==0){
					
						$queryupdate = "INSERT INTO tags VALUES ( id ,'$type' , '$raw_tag' , '$tag' )";
						$update = mysqli_query($link, $queryupdate)or die(mysqli_error($link));
						$query = "SELECT MAX(id) AS id FROM tags WHERE tagfull='$raw_tag' AND type='$type' LIMIT 1";
						$result = mysqli_query($link,$query);
						$row = mysqli_fetch_array($result);
					
					}
					else{
						$query = "SELECT id AS id FROM tags WHERE tagfull='$raw_tag' AND type='$type' LIMIT 1";
						$result = mysqli_query($link,$query);
						$row = mysqli_fetch_array($result);
					}
					$has_meta = true;
				}
			}
		}
		if(!$has_meta){
			$type = "general";
			$queryexists = "SELECT * FROM tags WHERE tagfull='$raw_tag' AND type='$type' LIMIT 1";
			$sqlexists = mysqli_query($link, $queryexists)or die(mysqli_error($link));
			if(mysqli_num_rows($sqlexists)==0){
				
				$queryupdate = "INSERT INTO tags VALUES ( id ,'$type' , '$raw_tag' , '$tag' )";
				$update = mysqli_query($link, $queryupdate)or die(mysqli_error($link));
				$query = "SELECT MAX(id) AS id FROM tags WHERE tagfull='$raw_tag' AND type='$type' LIMIT 1";
				$result = mysqli_query($link,$query);
				$row = mysqli_fetch_array($result);
				
			}
			else{
				$query = "SELECT MAX(id) AS id FROM tags WHERE tagfull='$raw_tag' AND type='$type' LIMIT 1";
				$result = mysqli_query($link,$query);
				$row = mysqli_fetch_array($result);
			}
		}
		echo "database id:".$row['id']."<br />";
		$tag_id = $row['id'];
	}
?>