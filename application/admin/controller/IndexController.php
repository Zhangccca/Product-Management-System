<?php
/**
 * Created by PhpStorm.
 * News: Administrator
 * Date: 2017/11/22 0022
 * Time: 15:37
 */

namespace app\admin\controller;

use think\Controller;
use EasyWeChat\Foundation\Application;


class IndexController extends Controller
{
    private $options = [
        'debug'     => true,
        'app_id'  => 'wx55e34ebb4101999f',         // AppID
        'secret'  => '844f863ad448aefe327eec1d03698466',     // AppSecret
        'token'  => 'zhangccca',
    ];

    public function index()
    {
        $app = new Application($this->options);
        // 获取 access token 实例
        $accessToken = $app->access_token; // EasyWeChat\Core\AccessToken 实例
        $token = $accessToken->getToken(); // token 字符串
        return $this->fetch();
    }

    public function createmine()
    {
        return $this->fetch();
    }
    public function docreate()
    {
        $app = new Application($this->options);
        $menu = $app->menu;
        $buttons = [
            [
                "name"=>"手机型号",
                "sub_button"=>[
                    [
                        'type'=>'view',
                        'name'=>'苹果官网',
                        'url'=>'https://www.apple.com'
                    ],
                    [
                        'type'=>'view',
                        'name'=>'HUAWEI官网',
                        'url'=>'http://www.huawei.com'
                    ],
                    [
                        'type'=>'view',
                        'name'=>'小米官网',
                        'url'=>'https://www.mi.com'
                    ]
                ],
            ],
            [
                "name"=>"手机商城",
                "type"=>"view",
                "url"=>"https://www.taobao.com/"
            ],
            [
                "name"=>"更多服务",
                "sub_button"=>[
                    [
                        "type"=>"view",
                        "name"=>"历史文章",
                        "url"=>"http://zchi.wywwwxm.com/wx_tp/public/index.php/home/materials/index"
                    ],
                    [
                        "type"=>"view",
                        "name"=>"联系我们",
                        "url"  => "http://zchi.wywwwxm.com/wx_tp/public/index.php/oauth/oauth/myoauth"
                    ]
                ]
            ]
        ];
        //$menu->add($buttons);
        return $this->fetch();
    }

}