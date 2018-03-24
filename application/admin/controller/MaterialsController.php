<?php
/**
 * Created by PhpStorm.
 * User: Princess-c
 * Date: 2017/11/30
 * Time: 10:28
 */
namespace app\admin\controller;

use app\common\model\Materials;
use think\Controller;
use think\Request;
use weixin\wxMaterial;

class Material extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $materials=Materials::paginate(3);
        $this->assign('materials',$materials);
        return $this->fetch();
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return $this->fetch();
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //tp5上传文件先 use think\File;
        $file = request()->file('file');// 获取表单提交过来的文件
        $error = $_FILES['file']['error']; // 如果$_FILES['file']['error']>0,表示文件上传失败
        if ($error) {
            echo "<script>alert('文件上传失败！');location.href='" . $_SERVER["HTTP_REFERER"] . "';</script>";// 返回上一页并刷新
        }
        //自定义素材上传本地路径
        $info = $file->move(ROOT_PATH . 'public/uploads');
        //返回和入口文件同一级别的素材路径
        $n=$info->getSaveName();
        $path = str_replace('\\', '/', $n);
        $p=ROOT_PATH.'public/uploads/'.$path;

        $type=input('type');
        $uploadMaterial=new wxMaterial();
        $results=$uploadMaterial->uploadMaterial($p,$type);
        gettype($results);
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
