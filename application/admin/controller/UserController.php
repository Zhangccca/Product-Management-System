<?php
/**
 * Created by PhpStorm.
 * User: Princess-c
 * Date: 2017/11/30
 * Time: 15:00
 */

namespace app\admin\controller;

use app\common\model\Blacklist;
use app\common\model\User;
use app\common\model\Userlist;
use think\Controller;
use think\Request;
use EasyWeChat\Foundation\Application;

class UserController extends Controller
{
    private $fields=['nickname','note','openid','sex','city','province','country'];
    private $options = [
        'debug'     => true,
        'app_id'  => 'wx55e34ebb4101999f',         // AppID
        'secret'  => '844f863ad448aefe327eec1d03698466',     // AppSecret
        'token'  => 'zhangccca',
        'log' => [
            'level' => 'debug',
            'file'  => '/tmp/easywechat.log', // XXX: 绝对路径！！！！
        ],
    ];
    //登录
    public function login()
    {
        //$this->view->engine->layout(false);
        //$this->disableLayout();
        return $this->fetch();
    }
    public function dologin()
    {
        $captcha=input('captcha');
        //校验验证码的有效性
        if(!captcha_check($captcha,'login')){
            //验证码输入错误
            return $this->error('验证码输入错误，请重试');
        }
        //构造条件
        $condition=[];
        //获取表单数据
        $condition['username']=input('username');
        $condition['password']=input('password');
        //获取匹配记录
        $user=User::where($condition)->find();
        //判断
        if($user){//登陆成功
            //写入session
            session('loginedUser',$user->username);
            //跳转
            return $this->success('用户登陆成功','http://zchi.wywwwxm.com/wx_tp/public/index.php/admin/index');
        }else{
            return $this->error('用户名或密码错误');
        }
    }
    public function logout()
    {
        session('loginedUser',null);
        return $this->redirect('http://zchi.wywwwxm.com/wx_tp/public/index.php/admin/index');
    }
    /*
     * 用户管理*/
    //获取用户列表
    public function getuserlist()
    {
        $app = new Application($this->options);
        $userService = $app->user;
        $users = $userService->lists();
//        return $users;exit;
//        $user_list=json_decode($users,true);
        $openId=$users['data']['openid'];
//        echo $openId;exit;
        for($i=0;$i<$users->count;$i++){
            $user = $userService->get($openId[$i]);
//            dump($user);continue;
            $userlist=new Userlist();
            foreach ($this->fields as $f){
                $userlist->$f=$user->$f;
            }
            $conditions=[];
            $conditions['openid']=$openId[$i];
            $userlists=$userlist->where($conditions)->find();
            if($userlists){
                continue;
            }else{
                $userlist->save();
            }
        }
        $this->assign('user',Userlist::all());
        return $this->fetch();
    }
    //移除用户
    public function delete($id)
    {
        $userlist=Userlist::get($id);
        if($userlist->delete()){
            return $this->success('删除成功','http://zchi.wywwwxm.com/wx_tp/public/index.php/admin/user/getuserlist');
        }else{
            return $this->error('删除失败');
        }
    }
    public function edit($id)
    {
        $this->assign('userlist',Userlist::all());
        $this->assign('row',Userlist::get($id));
        return $this->fetch();
    }
    //修改备注
    public function update($id)
    {
        $app = new Application($this->options);
        $userService = $app->user;
        $userlist=Userlist::get($id);
        $userlist->note=input('note');
        $userService->remark($userlist->openid,$userlist->note);
        if($userlist->save()){
            $this->success('修改备注成功','http://zchi.wywwwxm.com/wx_tp/public/index.php/admin/user/getuserlist');
        }else{
            $this->error('修改备注失败');
        }
    }
    //获取黑名单列表
    public function getblacklist()
    {
        $app = new Application($this->options);
        $userService = $app->user;
        $blacklist = $userService->blacklist();
//        dump($blacklist);exit;
//        return $blacklist->count;exit;
        $openId=$blacklist['data']['openid'];
        for($i=0;$i<$blacklist->count;$i++){
            $black=new Blacklist();
            $userlist=new Userlist();
            $condition=[];
            $condition['openid']=$openId[$i];
            $user=$userlist->where($condition)->find();
//            dump($user['note']);exit;
//            return $user;
            foreach ($this->fields as $f){
                $black->$f=$user->$f;
            }
            $conditions=[];
            $conditions['openid']=$openId[$i];
            $blacklists=$black->where($conditions)->find();
            if($blacklists){
                continue;
            }else{
                $black->save();
            }
        }
        $this->assign('blacklist',Blacklist::all());
        return $this->fetch();
    }
    //拉黑用户
    public function batchblack($id)
    {
        $app = new Application($this->options);
        $userService = $app->user;
        $userlist=Userlist::get($id);
        $openId=$userlist->openid;
        if($userService->batchBlock([$openId])){
            $this->success('拉黑成功','http://zchi.wywwwxm.com/wx_tp/public/index.php/admin/user/getblacklist');
        }else{
            $this->error('拉黑失败');
        }
    }
    //取消拉黑
    public function unbatchblack($id)
    {
        $app = new Application($this->options);
        $userService = $app->user;
        $blacklist=Blacklist::get($id);
        $openId=$blacklist->openid;
        if($userService->batchUnblock([$openId])){
            $blacklist->delete();
            $this->success('移出成功','http://zchi.wywwwxm.com/wx_tp/public/index.php/admin/user/getblacklist');
        }else{
            $this->error('移出失败');
        }
    }
}
