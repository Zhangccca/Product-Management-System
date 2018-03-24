<?php
/**
 * Created by PhpStorm.
 * User: Princess-c
 * Date: 2017/10/25
 * Time: 14:17
 */
require '../vendor/autoload.php';
//调用token类型获取token
$token = weixin\wxToken::getToken();

echo $token;

if (false == $token){

}