<?php
function display_post($link , $meta , $id ,$thumbdir){
	$output = '';
	include_once 'read-tags.php';
	$dataquery = "SELECT * FROM postdata WHERE idnum=$id LIMIT 1";
	$infoget = mysqli_query($link , $dataquery) or die(mysqli_error($link));
	$row = mysqli_fetch_array($infoget);
	$tags = read_tags($link,$meta,$row['idnum'],'SEARCH');

	$output .= "<div id='thumbcon'><span class='thumb'>";
	
	if(!file_exists("$thumbdir/$row[thumb]")&&$row['type']=="mp3"or!file_exists("$thumbdir/$row[thumb]")&&$row['type']=="flac") {
		$output .= "<a href=\"view-post.php?id=$row[idnum]\"><img src=\"$thumbdir/mp3thumb.png\" type=\"image/png\" alt=\"$tags\" title=\"$tags\">";
	}
	elseif(!file_exists("$thumbdir/$row[thumb]")&&$row['type']=="webm"or!file_exists("$thumbdir/$row[thumb]")&&$row['type']=="mp4"){
		$output .= "<a href=\"view-post.php?id=$row[idnum]\"><img src=\"$thumbdir/VidThumb.png\" type=\"image/png\" alt=\"$tags\" title=\"$tags\">";
	}
	else {
		$output .= "<a href=\"view-post.php?id=$row[idnum]\"><img src=\"$thumbdir/$row[thumb]\" type=\"image/png\" alt=\"$tags\" title=\"$tags\">";
	}
	if($row['type']=='gif'or$row['type']=='webm'or$row['type']=='mp4'or$row['type']=='mp3'or$row['type']=='cbz'or$row['type']=='cbr'){
		$output .= "<span id='animtag'>$row[type]</span>";
	}
	$output .= "</a><br /><p id='bar' ";
	if($row['rating']=='safe'){
		$output .= "style='inline;color:lightgreen;'>S</p>";
	}
	elseif($row['rating']=='questionable'){
		$output .= "style='inline;color:yellow;'>Q</p>";
	}
	else{
		$output .= "style='inline;color:red;'>E</p>";
	}
	$endtime = microtime(true);
	$output .= "</span></div>\n";
	return $output;
}
?>
