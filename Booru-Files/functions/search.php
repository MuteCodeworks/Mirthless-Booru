<?php
	function simple_query($query,$link,$ret_type){
		$result = mysqli_query($link , $query) or die(mysqli_error($link));
		if($ret_type == "ALL"){
			$row = mysqli_fetch_all($result);
		}
		if($ret_type == "ARRAY"){
			$row = mysqli_fetch_array($result);
		}
		return $row;
	}
	
	function is_meta_search($string,$metaterms){
		$meta_search_array = array('type','height','width','rating');
		if(strpos($string,':')===false){
			return false;
		}
		else{
			$str_arr = explode(':',$string);
			if(!in_array($str_arr[0],$metaterms)&&in_array($str_arr[0],$meta_search_array)){
				return true;
			}
			elseif(in_array($str_arr[0],$metaterms)&&in_array($str_arr[0],$meta_search_array)){
				return -1;
			}
			else{
				return false;
			}
		}
	}
		
	function get_id_array_with_tag($keyword,$link){
		if(strpos($keyword,'%')===false){
			$row = simple_query("SELECT tm.post_id FROM tagmap tm , tags t WHERE tm.tag_id = t.id AND t.tagalias = '$keyword'",$link,"ALL");
		}
		else{
			$row = simple_query("SELECT tm.post_id FROM tagmap tm , tags t WHERE tm.tag_id = t.id AND t.tagalias LIKE '$keyword'",$link,"ALL");
		}
		#$row = mysqli_fetch_all(mysqli_query($link , "SELECT tm.post_id FROM tagmap tm , tags t WHERE tm.tag_id = t.id AND t.tagalias LIKE '$keyword'"));
		$ret_arr = array();
		foreach($row as $arr){
			$ret_arr[] = $arr[0];
		}
		return $ret_arr;
	}
	
	function get_id_array_with_type($keyword,$link){
		$row = simple_query("SELECT idnum FROM postdata WHERE type = '$keyword'",$link,"ALL");
		$ret_arr = array();
		foreach($row as $arr){
			$ret_arr[] = $arr[0];
		}
		return $ret_arr;
	}
	
	function get_id_array_with_xgtlt($oper,$val,$link){
		$row = simple_query("SELECT idnum FROM postdata WHERE width $oper $val",$link,"ALL");
		$ret_arr = array();
		foreach($row as $arr){
			$ret_arr[] = $arr[0];
		}
		return $ret_arr;
	}
	
	function get_id_array_with_ygtlt($oper,$val,$link){
		$row = simple_query("SELECT idnum FROM postdata WHERE height $oper $val",$link,"ALL");
		$ret_arr = array();
		foreach($row as $arr){
			$ret_arr[] = $arr[0];
		}
		return $ret_arr;
	}
	
	function get_id_array_with_rating($keyword,$link){
		$row = simple_query("SELECT idnum FROM postdata WHERE rating = '$keyword'",$link,"ALL");
		$ret_arr = array();
		foreach($row as $arr){
			$ret_arr[] = $arr[0];
		}
		return $ret_arr;
	}
	
	function do_meta_search($keyword,$link){
		$type = explode(':',$keyword)[0];
		$ret = array();
		$keyword = substr($keyword,strpos($keyword,':')+1);
		#echo $keyword."<br/>";
		switch($type){
			case "type":
				$ret = get_id_array_with_type($keyword,$link);
				break;
			case "height":
				if(strpos($keyword,'>')!==false&&strpos($keyword,'<')!==false){
					break;
				}
				else{
					if(strpos($keyword,'>')==0||strpos($keyword,'<')==0){
						if(strpos($keyword,'=')==1){
							$operand = substr($keyword,0,2);
							$value = (int)substr($keyword,2);
							if(is_int($value)){
								$ret = get_id_array_with_ygtlt($operand,$value,$link);
							}
							else{
								break;
							}
						}
						else{
							$operand = substr($keyword,0,1);
							$value = (int)substr($keyword,1);
							if(is_int($value)){
								$ret = get_id_array_with_ygtlt($operand,$value,$link);
							}
							else{
								break;
							}
						}
					}
					elseif(strpos($keyword,'=')!==false&&strpos($keyword,'=')==0){
						$operand = substr($keyword,0,1);
						$value = (int)substr($keyword,1);
						if(is_int($value)){
							$ret = get_id_array_with_ygtlt($operand,$value,$link);
						}
						else{
							break;
						}
					}
					else{
						break;
					}
				}
				break;
			case "width":
				if(strpos($keyword,'>')!==false&&strpos($keyword,'<')!==false){
					break;
				}
				else{
					if(strpos($keyword,'>')==0||strpos($keyword,'<')==0){
						if(strpos($keyword,'=')==1){
							$operand = substr($keyword,0,2);
							$value = (int)substr($keyword,2);
							if(is_int($value)){
								$ret = get_id_array_with_xgtlt($operand,$value,$link);
							}
							else{
								break;
							}
						}
						else{
							$operand = substr($keyword,0,1);
							$value = (int)substr($keyword,1);
							if(is_int($value)){
								$ret = get_id_array_with_xgtlt($operand,$value,$link);
							}
							else{
								break;
							}
						}
					}
					elseif(strpos($keyword,'=')!==false&&strpos($keyword,'=')==0){
						$operand = substr($keyword,0,1);
						$value = (int)substr($keyword,1);
						if(is_int($value)){
							$ret = get_id_array_with_xgtlt($operand,$value,$link);
						}
						else{
							break;
						}
					}
					else{
						break;
					}
				}
				break;
			case "rating":
				$check = array('s','q','e','u');
				$sub = array('safe','questionable','explicit','unrated');
				if(in_array($keyword,$check)){
					$keyword = str_ireplace($check,$sub,$keyword);
				}
				$ret = get_id_array_with_rating($keyword,$link);
				break;
		}
		return $ret;
	}
	
	function search($terms,$name_or_tag,$link,$meta_pass){
		$firstrun = true;
		if($name_or_tag=='TAG'){
			
			$includearray = array();
			$excludearray = array();
			$noninex = array();
			if($terms==''){
				$query = "SELECT idnum FROM postdata";
				$result = mysqli_query($link , $query) or die(mysqli_error($link));
				$row = mysqli_fetch_all($result);
				foreach($row as $arr){
					$includearray[] = $arr[0];
				}
				$array = array_unique($includearray);
				return $array;
			}
			else{
				$or_search = false;
				$negate_search = false;
				$base_search = false;
				
				$keywords = explode(" ", $terms);
				foreach($keywords as $key => $word){
					$query = "SELECT COUNT(*) FROM tagmap tm , tags t WHERE tm.tag_id = t.id AND t.tagalias = '$word'";
					$result = mysqli_query($link , $query) or die(mysqli_error($link));
					$row = mysqli_fetch_array($result);
					$keywords[$key] =  "$row[0] $word";
				}
				rsort($keywords);
				$run = count($keywords);
				for( $i = 0 ; $i < $run ; $i++ ){
					#echo $keywords[$i];
					$keywords[$i] = substr($keywords[$i],strpos($keywords[$i],' ')+1);
					$keywords[$i] = str_ireplace("'","''",$keywords[$i]);
					$keywords[$i] = str_ireplace("*","%",$keywords[$i]);
					if(!$firstrun){
						
					}
					if(substr($keywords[$i], 0, 1)=='-'){
						$negate_search = true;
						$keywords[$i] = substr($keywords[$i], 1);
						if(!$firstrun){
							
						}
						if(is_meta_search($keywords[$i],$meta_pass)===true){
							$excludearray = array_merge($excludearray,do_meta_search($keywords[$i],$link));
						}
						else{
							$excludearray = array_merge($excludearray,get_id_array_with_tag($keywords[$i],$link));
						}
						
					}
					elseif(substr($keywords[$i], 0, 1)=='~'){
						$or_search = true;
						$keywords[$i] = substr($keywords[$i], 1);
						if(!$firstrun){
							
						}
						if(is_meta_search($keywords[$i],$meta_pass)===true){
							$noninex = array_merge($noninex,do_meta_search($keywords[$i],$link));
						}
						else{
							$noninex = array_merge($noninex,get_id_array_with_tag($keywords[$i],$link));
						}
					}
					else{
						$base_search = true;
						if(is_meta_search($keywords[$i],$meta_pass)===true){
							if($firstrun){
								$includearray = do_meta_search($keywords[$i],$link);
							}
							else{
								$includearray = array_intersect(do_meta_search($keywords[$i],$link),$includearray);
							}
						}
						else{
							if($firstrun){
								$includearray = get_id_array_with_tag($keywords[$i],$link);
							}
							else{
								$includearray = array_intersect(get_id_array_with_tag($keywords[$i],$link),$includearray);
							}
						}
						$firstrun = false;
					}
				}

				if(!$base_search){
					$row = simple_query("SELECT idnum FROM postdata",$link,'ALL');
					foreach($row as $arr){
						$includearray[] = $arr[0];
					}
				}
				if($or_search){
					$noninex = array_unique($noninex);
					$includearray = array_intersect($includearray,$noninex);
				}	
				if($negate_search){
					$excludearray = array_unique($excludearray);
					$includearray = array_diff($includearray,$excludearray);
				}
				$array = array_unique($includearray);
				return $array;
			}
		}
		elseif($name_or_tag=='name'){

			$query .= "WHERE tags NOT LIKE '% $keywords[0] %' ";

		}
	}
?>