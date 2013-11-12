<?php
require_once("classes/base.php");

// retrieves the reference to the database and
// flushes the complete set of torrent files to
// the logical structures making sure that the
// represented files exist in the data source
$db = get_database();
flush_files($db, $path = "res/torrents");
$db->close();
?>
