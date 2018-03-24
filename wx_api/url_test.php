<?php
/**
 * Created by PhpStorm.
 * User: Princess-c
 * Date: 2017/10/31
 * Time: 17:15
 */

require '../vendor/autoload.php';

$wxc=new weixin\wxCURL();

$url='http://zchi.wywwwxm.com/fileupload.php';

$file=dirname(__FILE__).'../image/123.jpg';

$ret=$wxc->upload($url,$file);

echo $ret;
