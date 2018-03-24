<?php

namespace weixin;

class wxMaterial extends wxCURL
{
    private $upload_temp_url = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token=';
    private $upload_media_url = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=';
    private $upload_news_url = 'https://api.weixin.qq.com/cgi-bin/material/add_news?access_token=';
    private $get_temp_url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token=';
    private $get_media_url = 'https://api.weixin.qq.com/cgi-bin/material/get_material?access_token=';
    private $remove_media_url = 'https://api.weixin.qq.com/cgi-bin/material/del_material?access_token=';
    private $set_news_url = 'https://api.weixin.qq.com/cgi-bin/material/update_news?access_token=';
    private $media_list_url = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=';
    private $media_total_url = 'https://api.weixin.qq.com/cgi-bin/material/get_materialcount?access_token=';
    private $cur_api = '';
    private $token = '';

    public function __construct()
    {
        $this->token = wxToken::getToken();
        //如果获取失败则退出并返回错信息
        if (empty($this->token)) {
            exit('Error: get token failed.');
        }
    }

    //上传永久素材
    public function uploadMaterial($file,$type)
    {
        $this->cur_api = $this->upload_media_url . $this->token . '&type=' . $type;
        $r = $this->upload($this->cur_api,$file,'media');
        return $r;
    }

    //创建图文素材
    public function createNews($news)
    {
        $this->cur_api = $this->upload_news_url . $this->token;
        if (is_array($news)) {
            $news = json_encode($news,JSON_UNESCAPED_UNICODE);
        }
        $r = $this->post($this->cur_api,$news);
        return $r;
    }

    //删除永久素材
    public function removeMaterial($media_id)
    {
        $this->cur_api = $this->remove_media_url . $this->token;
        $post_data = '{"media_id":"'.$media_id.'"}';
        $r = $this->post($this->cur_api, $post_data);
        return $r;
    }

    //获取永久素材
    public function getMaterial($media_id,$downpath, $filename)
    {
        $this->cur_api = $this->get_media_url . $this->token;

        $r = $this->download($this->cur_api,$downpath,$filename,'{"media_id":"'.$media_id.'"}');
        return $r;
    }

    /*
        更新永久图文素材，参数格式参考微信开发者文档，
        此格式与创建图文素材不同，要加入media_id和index参数。
    */
    public function setNews($news)
    {
        $this->cur_api = $this->set_news_url . $this->token;
        return $this->post($this->cur_api, json_encode($news));
    }

    //获取素材总数
    public function getTotalMaterial()
    {
        $this->cur_api = $this->media_total_url . $this->token;
        return $this->get($this->cur_api);
    }

    //获取素材列表
    public function materialList($type, $offset = 0, $count = 20)
    {
        $this->cur_api = $this->media_list_url . $this->token;
        $data = [
            'type'=>$type,
            'offset'=>$offset,
            'count'=>$count
        ];
        $r = $this->post( $this->cur_api, json_encode($data) );
        return $r;
    }

}
