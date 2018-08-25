<!DOCTYPE html>
<html>
	<body>
			<?php
				$tagstr = " rand_(pop) 3d rand2_(pop) flowers rand_(allo) animated rond_(lol) ";
				$sqlarr = array('artist','tksvhc_(artist)','tksvhc', 3 );
				$tagarr = preg_split('/\s+/', $tagstr);
				$total = count($tagarr);
				$terms = array('(pop)','(copy)','(allo)','(lol)');
				$run = count($terms);
				$istrue = false;
				
				for( $i = 1 ; $i < $total -1; $i++ ){
					for( $o = 0 ; $o < $run ; $o++ ){
						$check = strstr($tagarr[$i],$terms[$o]);
						if($check){
							$oldtype = preg_replace('/(\(|\))/','', $check);
							$oldtag = preg_replace('@\_.*?\)@','',$tagarr[$i]);
							$type = $oldtype;
							$tag = $oldtag;
							$istrue = true;
							echo "$tagarr[$i]: $check: $tag - $type<br />";
						}
					}
					if(!$istrue){
						$type = "gen";
						echo "$tagarr[$i]: $check: $tag - $type<br />";
					}
					$istrue = false;
				}
			?>
	</body>
</html>