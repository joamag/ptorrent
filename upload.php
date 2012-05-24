<?php
require("lib/Smarty.class.php");
require("base.php");

$smarty = new Smarty();

$smarty->setTemplateDir("res/templates");
$smarty->setCompileDir("res/templates_c");
$smarty->setCacheDir("res/cache");
$smarty->setConfigDir("res/configs");

$smarty->display("upload.tpl");
?>