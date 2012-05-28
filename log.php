<?php
function log_message(&$message) {
    $filePath = "log/ptorrent.log";
    $file = fopen($filePath, "a+") or die("can't open file");
    fwrite($file, $message);
    fclose($file);
}
?>
