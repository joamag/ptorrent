<?php
require_once("classes/base.php");

if($_SERVER["REQUEST_METHOD"] == "POST") {
    // retrieves the both the name of the file
    // and the path to it, then uses those values
    // to copy the file into the appropriate path
    $file_name = $_FILES["file"]["name"];
    $file_path = $_FILES["file"]["tmp_name"];
    copy($file_path, "res/torrents/".$file_name);

    // operates the database updating all the required structure
    // values according to the client loop
    $db = get_database();
    flush_files($db, $path = "res/torrents");
    $db->close();
}

$smarty->display("upload.html.tpl");
?>
