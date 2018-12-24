<?php
include "config.php";
$i = 0;
foreach (glob("./dump/*") as $filename) {
    ++$i;
    $filename = substr($filename,strlen($dump_dir)+1);
    echo "$filename size <br />\n";
}
echo $i;
?>
