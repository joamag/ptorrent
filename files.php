<?php
include "lib/bencode.php";
include "base.php";

// checks if the database file path exits in case
// it does not (assumes new database)
$is_new = !file_exists($DATABASE_PATH);

$db = new SQLite3($DATABASE_PATH);
$is_new && create_database($db);
$is_new && create_configuration($db);
$files = get_files($db);
$db->close();
print_r($files);

foreach($files as &$file) {
    printf($file["info_hash"]);
    printf($file["size"]);
}

?>