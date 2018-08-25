<?php
$imagedir = "cbviewer";

 // create new directory with 744 permissions if it does not exist yet
 // owner will be the user/group the PHP script is run under
 if ( !file_exists($imagedir) ) {
     mkdir ($imagedir, 0777);
 }

 file_put_contents ($imagedir.'/test.txt', 'Hello File');
 

if (!extension_loaded('imagick')){
    echo 'imagick not installed';
}
 ?>