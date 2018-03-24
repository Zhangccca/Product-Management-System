<?php
/**
 * Created by PhpStorm.
 * User: Princess-c
 * Date: 2017/12/20
 * Time: 10:39
 */

namespace app\admin\controller;


use think\Controller;
use app\common\model\Question;
class OauthController extends Controller
{
    public function question()
    {
        $question = new Question();
        $question->username=input('username');
        $question->sex=input('sex');
        $question->phone=input('phone');
        $question->buytime=input('buytime');
        $question->question=input('question');
        $question->qq=input('qq');
        $question->number=input('number');
//        dump($question);exit;
        dump($question->save());

    }
    //后台显示用户问题
    public function show()
    {
        //获取分页数据
        $show = Question::paginate(8);
        $this->assign('show',$show);
        return $this->fetch();
    }

    public function respose()
    {
        return $this->fetch();
    }
    public function answer()
    {
        return $this->fetch();
    }

}