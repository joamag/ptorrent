<?php
require("lib/Smarty.class.php");
require("base.php");

// checks if the database file path exits in case
// it does not (assumes new database)
$is_new = !file_exists($DATABASE_PATH);

$db = new SQLite3($DATABASE_PATH);
$is_new && create_database($db);
$is_new && create_configuration($db);
$files = get_files($db);
$db->close();

$smarty = new Smarty();

$smarty->setTemplateDir("res/templates");
$smarty->setCompileDir("res/templates_c");
$smarty->setCacheDir("res/cache");
$smarty->setConfigDir("res/configs");

$smarty->assign("files", $files);
$smarty->display("files.tpl");
?>
