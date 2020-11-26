<?php
require_once('../../config/config.inc.php');
require_once('../../init.php');
// echo rand(100,200);
$obj_mp = Module::getInstanceByName('martinsbanner');
echo $obj_mp->getFormConfirm(Tools::getValue('email'), Tools::getValue('telephone'));
die;