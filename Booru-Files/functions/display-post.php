<?php
function display_post($link , $meta , $result , $row){
	include_once 'read-tags.php';
	$tags = read_tags($link,$meta,$row['idnum'],'SEARCH');
	$dataquery = "SELECT rating , type FROM postdata WHERE idnum=$row[idnum]";
	$infoget = mysqli_query($link , $dataquery) or die(mysqli_error($link));
	$inforow = mysqli_fetch_array($infoget);
	$rating = $inforow['rating'];
	$type = $inforow['type'];

	echo "<div id='thumbcon'><span class='thumb'>";
	
	if(!file_exists("thumbs/$row[thumb]")&&$type=="mp3"or!file_exists("thumbs/$row[thumb]")&&$type=="flac") {
	echo "<a href=\"view-post.php?id=$row[idnum]\"><img src=\"thumbs/mp3thumb.png\" alt=\"$tags\" title=\"$tags\">";
	}
	else {
	echo "<a href=\"view-post.php?id=$row[idnum]\"><img src=\"thumbs/$row[thumb]\" alt=\"$tags\" title=\"$tags\">";
	}
	if($type=='gif'or$type=='webm'or$type=='mp4'or$type=='mp3'or$type=='cbz'or$type=='cbr'){
		echo "<span id='animtag'>$type</span>";
	}
	echo "</a><br />";
	echo "<p id='bar' ";
	if($rating=='safe'){
		echo "style='inline;color:lightgreen;'>S</p>";
	}
	elseif($rating=='questionable'){
		echo "style='inline;color:yellow;'>Q</p>";
	}
	else{
		echo "style='inline;color:red;'>E</p>";
	}
	
	echo"</span></div>\n";
	$row = mysqli_fetch_array($result);
	return $row;
}
?>