<?php
/**
 * Created by PhpStorm.
 * News: Administrator
 * Date: 2017/11/27 0027
 * Time: 9:30
 */

namespace app\admin\controller;

use app\common\model\News;
use app\common\model\Image;

use app\common\model\Voice;
use EasyWeChat\Foundation\Application;
use think\Controller;
use think\Request;
use app\common\model\Materials;
use EasyWeChat\Message\Article;

class NewsController extends Controller
{
    //数据添加或修改时所使用的字段名称
    protected $files = ['abstract','title','content','image'];
    protected $files_news = ['id','abstract','title','content','image','video'];
    protected $fields=['title','thumb_media_id','content','digest','image'];
    private $options = [
        'debug'     => true,
        'app_id'  => 'wx55e34ebb4101999f',         // AppID
        'secret'  => '844f863ad448aefe327eec1d03698466',     // AppSecret
        'token'  => 'zhangccca',
    ];
    public function index()
    {
        //获取分页数据
        $pages = News::paginate(3);
        $this->assign('pages',$pages);
        return $this->fetch();
    }

//    //显示创建图文消息页面
//    public function viewnews()
//    {
//        return $this->fetch();
//    }
//    //保存数据
//    public function sendnews()
//    {
//        $newstext = new News();
//        foreach ($this->files as $f){
//            $newstext->$f = input($f);
//        }
//
//        $news = [
//            'media_id' => input('post.media_id'),
//            'title' =>
//            //
//        ];
//
//        $wxm = new \weixin\wxMaterial;
//        $response = $wxm->createNews($news);
//        $ret = json_encode($response, true);
//        if (isset($ret['errcode'])) {
//            exit($response);
//        }
//
//        $newstext->media_id = $ret['media_id'];
//        //调用API通过media_id获取图文素材内容，包含了url
//
//        if($newstext->save())
//        {
//            return $this->success('数据插入成功','/admin/news/material');
//        }else{
//            return $this->error('记录插入失败');
//        }
//    }
//
//    public function uploadImage()
//    {
//
//        //upload image
//
//
//        //end
//
//
//        return json_encode($result);
//    }
//
//    public function imageList()
//    {
//
//        return json_encode($image_list);
//    }

    //上传图文消息
    public function sendnews()
    {
        $app = new Application($this->options);
        $broadcast = $app->broadcast;
        // 上传单篇图文
        $material = $app->material;
        $articlenews=new News();
        foreach ($this->fields as $f){
            $articlenews->$f=input($f);
        }

        $file = realpath(ROOT_PATH . 'public/static/images/1.jpg');
        $result = $material->uploadThumb($file);

        $mediaId=$result->media_id;
//        echo $url;exit;
        $article = new Article([
            'title'=>$articlenews->title,
            'thumb_media_id'=>$mediaId,
            'digest'=>$articlenews->digest,
            'content'=>$articlenews->content,
        ]);
//        echo $articlenews->thumb_media_id;
        if(input('submit1')){
            if($articlenews->save()){
                $this->success('保存成功','http://zchi.wywwwxm.com/wx_tp/public/index.php/admin/news/material');
            }
        }
        elseif(input('submit2')){
            $ret=$material->uploadArticle($article);
            $str=trim($ret,'"{"media_id":"');
            $str1=trim($str,'"}');
            $articlenews->thumb_media_id=$str1;
            $articlenews->save();
            $broadcast->send('news','$articlenews->thumb_media_id');
        }

    }
    //图文消息显示
    public function viewnews()
    {
        $this->assign('image',Image::all());
        return $this->fetch();
    }


    public function material()
    {
        $materials=Materials::paginate(3);
        $this->assign('materials',$materials);
        return $this->fetch();
//        $app = new Application($this->options);
//        $broadcast = $app->broadcast;
////        $broadcast->sendNews($mediaId);
//        return $this->fetch();
    }
    //详细信息
    public function information()
    {
        $materials=Materials::paginate(3);
        $this->assign('materials',$materials);
        return $this->fetch();
    }
    //删除图文消息操作
    public function delete($id)
    {
        //获取指定数据
        $new = News::get($id);
        if($new->delete())
        {
            $this->success('删除成功','http://zchi.wywwwxm.com/wx_tp/public/index.php/admin/news/material');
        }else{
            $this->error('删除失败');
        }
   }

   //显示图片信息
    public function photos()
    {
        $images = Image::all();
        $this->assign("image",$images);
        return $this->fetch();
    }

    //上传图片
    public function uploadimage(Request $request)
    {
        $file = request()->file('image');
        if ($file){
            //移动文件到指定目录下(public目录为根页面)
            $file=$file->move('uploads');
//            dump($file);exit;
            $image = new Image();
            //$image->image= $file->getpathName();
            $image->image= str_replace('\\','/',$file->getpathName());
            $app = new Application($this->options);
            // 永久素材
            $material = $app->material;
            $result = $material->uploadImage("$image->image");
            $image->media_id=$result["media_id"];
//            dump($image->media_id);exit;
            if($image->save()){
                $this->success("上传成功!","http://zchi.wywwwxm.com/wx_tp/public/index.php/admin/news/photos","","1");
            }
        }
    }
    //删除图片
    public function deleteimg($id)
    {
        $img=Image::get($id);
        if($img->delete()){
            $this->success('删除成功','http://zchi.wywwwxm.com/wx_tp/public/index.php/admin/news/photos',"","1");
        }else{
            $this->error('删除失败');
        }
    }
    //上传声音
    public function uploadvoice(Request $request)
    {
        $file = request()->file('voice');
        if ($file){
            //移动文件到指定目录下(public目录为根页面)
            $file=$file->move('uploads');
//            dump($file);exit;
            $voice = new Voice();
            $voice->image=$file->getpathName();
            $app = new Application($this->options);
            // 永久素材
            $material = $app->material;
            $result = $material->uploadVoice(" ");
            $voice->media_id=$result["media_id"];
//            dump($image->media_id);exit;
            if($voice->save()){
                $this->success("成功!","http://zchi.wywwwxm.com/wx_tp/public/index.php/admin/news/voices","","1");
            }else{
                $this->error('','http://zchi.wywwwxm.com/wx_tp/public/index.php/admin/news/voices');
            }
        }
    }
    //音频消息显示
    public function voices()
    {
        $voices = voice::all();
        $this->assign('voice',$voices);
        return $this->fetch();
    }
    //删除音频
    public function deletevoice($Id)
    {
        $vvv=Voice::get($Id);
        if($vvv->delete()){
            $this->success('删除成功','http://zchi.wywwwxm.com/wx_tp/public/index.php/admin/news/voices',"","1");
        }else{
            $this->error('删除失败');
        }
    }

    //保存图文消息到数据库
    public function ntsave(Request $request)
    {
        $newstext = new News();
        $newstext->title = input('title');
        $newstext->abstract = input('abstract');
        $newstext->content = input('content');
        $newstext->image = input('mediaid');
//        if ($file) {
//            //移动文件到指定目录下(public目录为根页面)
//            $file = $file->move('uploads');
//             dump($file);exit;
//            $newstext->cover = $file->getpathName();
//        }
//        dump($newstext);exit;
        $result=$newstext->save();
        if ($result){
            $this->success("保存成功!",'http://zchi.wywwwxm.com/wx_tp/public/index.php/admin/news/material');
        }
    }

//    //保存到数据库并群发
//    public function ntsavesent(Request $request)
//    {
//        $newstext = new News();
//        $newstext->title = input('title');
//        $newstext->abstract = input('abstract');
//        $newstext->content = input('content');
//        $newstext->image = input('media_id');
////        $file = request()->file('cover');
////        if ($file) {
////            //移动文件到指定目录下(public目录为根页面)
////            $file = $file->move('uploads');
//////            dump($file);exit;
////            $newstext->cover = $file->getpathName();
////        }
//        $result=$newstext->save();
////        if ($result){
////            $this->success("保存成功!",'/admin/news/newstext');
////        }
//        $app = new Application($this->options);
//        $material = $app->material;
//        // 永久素材
////        $result = $material->uploadThumb("../public/static/images/y1.jpg");
//        $mediaid=$result['media_id'];
//        // 上传单篇图文
//        $article = new Article([
//            'title' => $newstext->title,
//            'thumb_media_id' => $mediaid,
//            "digest" => $newstext->abstract,
//            "show_cover_pic" => 1,
//            "content" => $newstext->content,
//        ]);
//        $media_Id=$material->uploadArticle($article);
//        $n=ltrim("$media_Id","{\"media_id\":\"");
//        $m=rtrim("$n","\"}");
////        return $m;
//        $broadcast = $app->broadcast;
//        $broadcast->send('news', "$m");
////        $broadcast = $app->broadcast;
////        $broadcast->sendNews($mediaId);
//
//    }

}