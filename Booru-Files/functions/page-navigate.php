<?php
	// array('current_page' =>, 'php' => '', 'max_page' => , 'get' => $_GET,)
	function page_navigate( $argarray ){
		
		if(isset($argarray['current_page'])&&$argarray['current_page']>5){
			echo "<a id='button-light-1' href='$argarray[php]?";
			foreach($argarray['get'] as $var => $val){
				if($var=='p'){
				
				}
				else{
					echo "$var=$val&";
				}
			}
			echo "p=1'>◄◄◄</a>";
		}
		if($argarray['current_page']>1){
			echo "<a id='button-light-1' href='$argarray[php]?";
			foreach($argarray['get'] as $var => $val){
				if($var=='p'){
				
				}
				else{
					echo "$var=$val&";
				}
			}
			$bcknm = $argarray['current_page']-1;
			echo "p=$bcknm'>◄</a>";
			for( $i = 4 ; $i > 0 ; $i-- ){
				$bcknm = $argarray['current_page']-$i;
				if($bcknm <= 0){
					continue;
				}
				echo "<a id='button-light-1' href='$argarray[php]?";
				foreach($argarray['get'] as $var => $val){
					if($var=='p'){
					
					}
					else{
						echo "$var=$val&";
					}
				}
				echo "p=$bcknm'>$bcknm</a>";
			}
		}
		if(isset($argarray['current_page'])){
			$bcknm = $argarray['current_page'];
		}
		else{
			$bcknm = 1;
		}
		echo "<div id='pagesid'>$bcknm</div>";
		if($argarray['max_page']>1){
			for( $i = 0 ; $i < 4 ; $i++ ){
				if($argarray['current_page']!=''){
					$bcknm = $argarray['current_page']+$i+1;
				}
				else{
					$bcknm = $i+1;
				}
				if($bcknm >= $argarray['max_page']+1){
					continue;
				}
				echo "<a id='button-light-1' href='$argarray[php]?";
				foreach($argarray['get'] as $var => $val){
					if($var=='p'){
					
					}
					else{
						echo "$var=$val&";
					}
				}
				echo "p=$bcknm'>$bcknm</a>";
			}
		}
		if($argarray['current_page']<($argarray['max_page'])){
			echo "<a id='button-light-1' href='$argarray[php]?";
			foreach($argarray['get'] as $var => $val){
				if($var=='p'){
				
				}
				else{
					echo "$var=$val&";
				}
			}
			$bcknm = $argarray['current_page']+1;
			echo "p=$bcknm'>►</a>";
		}
		if($argarray['current_page']<($argarray['max_page']-4)){
			echo "<a id='button-light-1' href='$argarray[php]?";
			foreach($argarray['get'] as $var => $val){
				if($var=='p'){
				
				}
				else{
					echo "$var=$val&";
				}
			}
			echo "p=$argarray[max_page]'>►►►</a>";
		}
	}
?>
