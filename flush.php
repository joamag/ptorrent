<?php
require("classes/base.php");

// checks if the database file path exits in case
// it does not (assumes new database)
$is_new = !file_exists($DATABASE_PATH);

$db = new SQLite3($DATABASE_PATH);
$is_new && create_database($db);
$is_new && create_configuration($db);
flush_files($db, $path = "res/torrents");
$db->close();
?>
