<?php
require_once("classes/base.php");

// retrieves the parameters to the execution
$info_hash_b64 = $_GET["info_hash"];

$db = get_database();
$file = get_file($db, $info_hash_b64);
$db->close();

$smarty->assign("file", $file);
$smarty->display("file.html.tpl");
?>
