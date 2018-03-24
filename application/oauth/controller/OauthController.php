<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/14 0014
 * Time: 9:23
 */

namespace app\oauth\controller;

use app\common\model\helper;
use EasyWeChat\Foundation\Application;
use think\Controller;

class OauthController extends Controller
{
    private $options = [
        'debug'     => true,
        'app_id'  => 'wx55e34ebb4101999f',         // AppID
        'secret'  => '844f863ad448aefe327eec1d03698466',     // AppSecret
        'token'  => 'zhangccca',
        'oauth' => [
            'scopes'   => ['snsapi_userinfo'],
            'callback' => 'http://zchi.wywwwxm.com/wx_tp/public/index.php/oauth/oauth/oauthback',//授权回调的url
        ],
    ];
    //业务页面
    public function myoauth()
    {
        return $this->fetch();
    }

    //发起授权
    public function dooauth()
    {
        $app = new Application($this->options);
        $oauth = $app->oauth;
//        dump($oauth);exit;
        // 未登录
        if (empty($_SESSION['wechat_user'])) {
            $_SESSION['target_url'] = 'http://zchi.wywwwxm.com/wx_tp/public/index.php/oauth/oauth/myoauth';//回调url
            return $oauth->redirect();
            // $oauth->redirect()->send();
        }else{
            // 已经登录过
            $user = $_SESSION['wechat_user'];
//            dump($user);exit;
//            header('http://dzhao.wywwwxm.com/thinkphp/public/index.php/oauth/oauth/oauthback');
        }
    }
    //授权回调
    public function oauthback()
    {
        $app = new Application($this->options);
        $oauth = $app->oauth;
        $user = $oauth->user();
        $helper = new helper();
        $conditions = [];
        $conditions['openid'] = $user->getId();
        $helpers = $helper->where($conditions)->find();
//        dump($helpers);
        if (!$helpers){
            $helper->openid = $user->getId();  // 对应微信的 OPENID
            $helper->nickname = $user->getNickname(); // 对应微信的 nickname
            $helper->save();
        }
        $_SESSION['wechat_user'] = $user->toArray();
        //授权过跳
        $targetUrl = empty($_SESSION['target_url'])?'http://zchi.wywwwxm.com/wx_tp/public/index.php/oauth/oauth/myoauth':$_SESSION['target_url'];
        header('location:'. $targetUrl);
        return $this->fetch('myoauth');
    }
}