<?php
	function search($terms,$name_or_tag){
		if($name_or_tag=='TAG'){
			$query = "";
			$firstrun = true;
			$keywords = explode(" ", $terms);
			$allnegative = TRUE;
			$negexists = FALSE;
			$run = count($keywords);
			for( $i = 0 ; $i < $run ; $i++ ){
				if(substr($keywords[$i], 0, 1)=='-'){
					$keywords[$i] = substr($keywords[$i], 1);
					if(!$firstrun){
						$query .="AND ";
					}
					$query .= "SUM(CASE WHEN t.tagalias = \"$keywords[$i]\" OR t.tagfull = \"$keywords[$i]\" THEN 1 ELSE 0 END) = 0 ";
				}
				elseif(substr($keywords[$i], 0, 1)=='~'){
					$keywords[$i] = substr($keywords[$i], 1);
					if(!$firstrun){
						$query .="OR ";
					}
					$query .= "SUM(CASE WHEN t.tagalias = \"$keywords[$i]\" OR t.tagfull = \"$keywords[$i]\" THEN 1 ELSE 0 END) > 0 ";
				}
				else{
					if(!$firstrun){
						$query .="AND ";
					}
					$query .= "SUM(CASE WHEN t.tagalias = \"$keywords[$i]\" OR t.tagfull = \"$keywords[$i]\" THEN 1 ELSE 0 END) > 0 ";
				}
				$firstrun = false;
			}
			return $query;
		}
		elseif($name_or_tag=='name'){

			$query .= "WHERE tags NOT LIKE \"% $keywords[0] %\" ";

		}
	}


?>