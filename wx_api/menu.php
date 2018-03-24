<?php
require_once '../vendor/autoload.php';
$wxm = new weixin\wxMenu();
$menu = $wxm->getMenu();
echo $menu;