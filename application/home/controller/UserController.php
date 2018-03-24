<?php
/**
 * Created by PhpStorm.
 * UserController: Administrator
 * Date: 2017/11/22 0022
 * Time: 15:30
 */

namespace app\home\controller;

use think\Controller;
use think\Request;
use app\common\model\User;

class UserController extends Controller
{
    public function index()
    {
        return $this->fetch();
    }
    public function register()
    {
        return $this->fetch();
    }

    public function doregister(Request $request)
    {
        $user = new User();
        //获取表单数据
        $user->username = $request->param('username');
        $user->password = md5(input('password'));
        //插入到数据库中
        if ($user->save()){
            //注册成功
            return $this->success('注册成功！','http://zchi.wywwwxm.com/wx_tp/public/index.php/home/user/login');
        }else{
            //注册失败
            return $this->error('注册失败，请重试！');
        }
    }

    public function login()
    {
        return $this->fetch();
    }

    public function dologin()
    {
        $captcha= input('captcha');
        //校验验证码的有效性
        if (!captcha_check($captcha,'login')){
            //验证码输入错误
            $this->error('验证码输入错误，请重试！');
        }
        //获取表单提交数据
        $condition=[];
        $condition['username']=input('username');
        $condition['password']=md5(input('password'));
        //获取匹配记录
        $user = User::where($condition)->find();
        //判断
        if ($user){
            //写入session
            session('loginedUser',$user->username);
            //跳转
            return $this->success('用户登陆成功！','http://zchi.wywwwxm.com/wx_tp/public/index.php/admin/index/index');
        }else{
            return $this->error('用户名或密码错误!');
        }
    }

    public function logout()
    {
        session('loginedUser',null);
        return $this->redirect('/');
    }
}