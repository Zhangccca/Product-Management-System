<?php
/**
 * Created by PhpStorm.
 * User: Princess-c
 * Date: 2017/12/21
 * Time: 10:10
 */

namespace app\home\controller;

use think\Controller;
use app\common\model\Materials;

class MaterialsController extends Controller
{
    public function index()
    {
        $materials = Materials::all();
        $this->assign('materials',$materials);
        return $this->fetch();
    }
    //显示图文消息
    public function show($id)
    {
        $materials = Materials::get($id);
        $this->assign('materials',$materials);
        return $this->fetch();
    }
}