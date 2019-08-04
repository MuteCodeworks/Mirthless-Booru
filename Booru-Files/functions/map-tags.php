<?php
	include_once "config.php";
	function map_tags($post_id , $tags , $link , $metaterms , $run_as){
		$tags = ' '."$tags".' ';
		$tags = preg_replace('/\s\s+/',' ',"$tags");
		$tags = str_ireplace("'","''","$tags");
		$tags = str_ireplace("/","\/","$tags");
		$tags = str_ireplace("\\",'',"$tags");
		$tags = substr($tags , 1 , strlen("$tags")-2);
		$tag_array = preg_split('/\s+/',"$tags");
		$tag_array = array_unique($tag_array);
		$run = count($tag_array);
		$meta = count($metaterms);
		if($run_as=='EDIT'){
			mysqli_query($link,"DELETE FROM tagmap WHERE post_id = $post_id");
		}
		foreach($tag_array as $tagarr){
			$has_meta = false;
			for( $o = 0 ; $o < $meta ; $o++ ){
				if(strstr($tagarr,"$metaterms[$o]:")){
					$check = strstr($tagarr,"$metaterms[$o]:");
				}
				if(isset($check)&&$check!=''){
					$type = substr($check,0, strrpos($check,':'));
					if($type!=$metaterms[$o]){
						
					}
					else{
						$tag = substr($check,strrpos($check,':')+1);
						$queryexists = "SELECT * FROM tags WHERE tagfull='$tagarr' AND type='$type' LIMIT 1";
						$sqlexists = mysqli_query($link, $queryexists)or die(mysqli_error($link));
						if(mysqli_num_rows($sqlexists)<1){
							
							$queryupdate = "INSERT INTO tags VALUES ( id ,'$type' , '$tagarr' , '$tag' )";
							mysqli_query($link, $queryupdate)or die(mysqli_error($link));
						}
						$has_meta = true;
					}
				}
			}
			unset($check);
			if(!$has_meta){
				$type = "general";
				$queryexists = "SELECT * FROM tags WHERE tagfull='$tagarr' LIMIT 1";
				$sqlexists = mysqli_query($link, $queryexists)or die(mysqli_error($link));
				if(mysqli_num_rows($sqlexists)==0){
					
					$queryupdate = "INSERT INTO tags VALUES ( id ,'$type' , '$tagarr' , '$tagarr' )";
					$update = mysqli_query($link, $queryupdate)or die(mysqli_error($link));
				}
			}
			$query = "SELECT id FROM tags WHERE tagfull='$tagarr' LIMIT 1";
			$result = mysqli_query($link,$query);
			$row = mysqli_fetch_array($result);
			
			$query = "INSERT INTO tagmap VALUES ( \"$post_id:$row[id]\" , $post_id , $row[id] )";
			mysqli_query($link , $query) or die(mysqli_error($link));
		}
	}
?>