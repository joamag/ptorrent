<?php
require("classes/base.php");

$db = get_database();
$files = get_files($db);
$db->close();

$smarty->assign("files", $files);
$smarty->display("files.tpl");
?>
