<?php
require("lib/Smarty.class.php");

$smarty = new Smarty();
$smarty->setTemplateDir("res/templates");
$smarty->setCompileDir("res/templates_c");
$smarty->setCacheDir("res/cache");
$smarty->setConfigDir("res/configs");
?>
