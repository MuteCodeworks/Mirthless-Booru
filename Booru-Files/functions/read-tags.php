<?php
	function read_tags($link,$meta,$post_id,$read_type){
		if($read_type=='EDIT'){
			$string = "";
			$query = "SELECT t.tagfull FROM tags t , tagmap tm WHERE tm.post_id = $post_id AND tm.tag_id = t.id ORDER BY t.tagfull";
			$result = mysqli_query($link,$query) or die(mysqli_error($link));
			$row = mysqli_fetch_array($result);
			if($row){
				while($row){
					$string .= " $row[tagfull] ";
					$row = mysqli_fetch_array($result);
				}
			}
			$string = preg_replace('/\s\s+/'," ",$string);
			$string = substr($string,1,strlen($string)-2);
			return $string;
		}
		elseif($read_type=='SEARCH'){
			$string = "";
			$query = "SELECT t.tagalias FROM tags t , tagmap tm WHERE tm.post_id = $post_id AND tm.tag_id = t.id ORDER BY t.tagfull";
			$result = mysqli_query($link,$query) or die(mysqli_error($link));
			$row = mysqli_fetch_array($result);
			if($row){
				while($row){
					$string .= " $row[tagalias], ";
					$row = mysqli_fetch_array($result);
				}
			}
			$string = preg_replace('/\s\s+/'," ",$string);
			$string = substr($string,1,strlen($string)-3);
			return $string;
		}
		elseif($read_type=='VIEW'){
			foreach($meta as $var){
				$query_tags = "SELECT t.tagalias , t.type FROM tags t , tagmap tm WHERE tm.post_id = $post_id AND tm.tag_id = t.id AND t.type = \"$var\" ORDER BY tagalias";
				$tags_result = mysqli_query($link,$query_tags) or die(mysqli_error($link));
				$tag_row = mysqli_fetch_array($tags_result);
				if($tag_row){
					echo "<div class='tagtype-$tag_row[type]'><b>$tag_row[type]</b><br />";
					while($tag_row){
						echo "<a id='tags' href=\"search-post.php?q=$tag_row[tagalias]\">$tag_row[tagalias]</a><br />\n";
						$tag_row = mysqli_fetch_array($tags_result);
					}
				echo "</div>";
				}
			}
			$query_tags = "SELECT t.tagalias , t.type FROM tags t , tagmap tm WHERE tm.post_id = $post_id AND tm.tag_id = t.id AND t.type = \"general\" ORDER BY tagalias";
			$tags_result = mysqli_query($link,$query_tags) or die(mysqli_error($link));
				$tag_row = mysqli_fetch_array($tags_result);
				if($tag_row){
					echo "<div class='tagtype-$tag_row[type]'><b>$tag_row[type]</b><br />";
					while($tag_row){
					echo "<a id='tags' href=\"search-post.php?q=$tag_row[tagalias]\">$tag_row[tagalias]</a><br />\n";
					$tag_row = mysqli_fetch_array($tags_result);
				}
				echo "</div>";
				}
		}
		else{
			echo "<br /><b>read_tags accepts three read types: VIEW, SEARCH, AND EDIT</b><br />";
		}
	}
?>