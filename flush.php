<?php
require("classes/base.php");

$db = get_database();
flush_files($db, $path = "res/torrents");
$db->close();
?>
