<?php
require("lib/Smarty.class.php");
require("base.php");

// retrieves the parameters to the execution
$info_hash_b64 = $_GET["info_hash"];

// checks if the database file path exits in case
// it does not (assumes new database)
$is_new = !file_exists($DATABASE_PATH);

$db = new SQLite3($DATABASE_PATH);
$is_new && create_database($db);
$is_new && create_configuration($db);
$file = get_file($db, $info_hash_b64);
$db->close();

$smarty = new Smarty();

$smarty->setTemplateDir("res/templates");
$smarty->setCompileDir("res/templates_c");
$smarty->setCacheDir("res/cache");
$smarty->setConfigDir("res/configs");

$smarty->assign("file", $file);
$smarty->display("file.tpl");
?>
