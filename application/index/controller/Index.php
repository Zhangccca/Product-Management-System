<?php

namespace app\index\controller;

class Index
{
    //填写微信公众号设置好的token
    private $token = 'zhangccca';

    private $textTpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            </xml>";
    private $imageTpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Image>
            <MediaId><![CDATA[%s]]></MediaId>
            </Image>
            </xml>";
    private $voiceTpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Voice>
            <MediaId><![CDATA[%s]]></MediaId>
            </Voice>
            </xml>";

    private $msgTpl='';
    private $msgType = 'text';

    //验证流程开始
    private function checkSignature()
    {
        $signature = request_data('get','signature');
        $timestamp = request_data('get','timestamp');
        $nonce = request_data('get','nonce');
        $tmpArr = array($this->token, $timestamp, $nonce);
        sort($tmpArr);//字典排序
        $tmpStr = implode( $tmpArr );//拼接字符串
        $tmpStr = sha1( $tmpStr );
        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    public function valid()
    {
        $echoStr = request_data('get','echostr');
        if($this->checkSignature()){
            exit($echoStr);
        }
    }
    //end

    public function responseMsg()
    {
        $this->valid();
        exit;
        $postStr = file_get_contents('php://input', 'r');
        if (!empty($postStr)){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            //把 PHP对象的变量转换成关联数组
            $wxmsg = get_object_vars($postObj);

            //预处理方法进行消息处理
            $ret = $this->preMsgHandle($wxmsg);

            $mtpl = $this->msgTpl;
            $resultStr = sprintf(
                $this->$mtpl,
                $wxmsg['FromUserName'],
                $wxmsg['ToUserName'],
                time(),
                $this->msgType,
                $ret);
            exit($resultStr);
        } else {
            exit('');
        }
    }

    //消息预处理方法
    private function preMsgHandle($wxmsg)
    {
        //动态设置消息模板变量
        $this->msgTpl = $wxmsg['MsgType'] . 'Tpl';
        $this->msgType = $wxmsg['MsgType'];
        switch ($wxmsg['MsgType']) {
            case 'text':
                //分发给文本消息处理方法
                return $this->textHandle($wxmsg);
                break;
            case 'voice':
                return $wxmsg['MediaId'];
                break;
            case 'image':
                return $wxmsg['MediaId'];
                break;
            case 'video':
                //如果是视频消息，返回文本消息错误提示
                $this->msgTpl = 'textTpl';
                $this->msgType = 'text';
                return '该类型不被支持';
                break;
            case 'event':
                return $this->eventHandle($wxmsg);
                break;
            default: return 'null';
        }
    }

    //文本消息处理方法，实现关键词自动回复
    private function textHandle($wxmsg)
    {
        switch($wxmsg['Content'])
        {
            case '?':
            case 'help':
                return $this->help();
                break;
            case 'info':
                return 'programmer';
                break;
            default:
                return $wxmsg['Content'];
        }
    }

    private function help()
    {
        return "enter help to get help doc\n" .
            "info to get my info.";
    }

    //处理事件消息的方法
    private function eventHandle($wxmsg)
    {
        //保存事件信息
        $event_log = time() . " | " . $wxmsg['Event'];

        switch($wxmsg['Event'])
        {
            //页面跳转事件
            case 'VIEW':
                $event_log .= " | " . $wxmsg['EventKey'];
                break;
            //位置信息事件
            case 'LOCATION':
                $event_log .= " | lat<" .
                    $wxmsg['Latitude'] .
                    "> lng<" .
                    $wxmsg['Longitude'] .
                    ">";
                break;
            //关注公众号
            case 'subscrible':
                $event_log .= " | " .
                    $wxmsg['FromUserName'];
                break;
            //取消关注公众号
            case 'unsubscrible':
                break;
            //点击菜单返回消息事件
            case 'CLICK':
                $event_log .= $wxmsg['EventKey'];
                break;
            case 'SCAN':
                break;
            default: ;
        }
        $event_log .= "\n";
        file_put_contents('wx_event.log', $event_log,FILE_APPEND);
        /*事件消息返回测试消息
            实际测试发现只有关注公众号时的事件通知支持返回消息
            以下几行代码在生产环境的事件处理中可以去掉
        */
        $this->msgTpl = 'textTpl';
        $this->msgType='text';
        return 'this is test info.';
    }


}

//获取GET/POST数据，type:get/post
//ind:数组索引；dval：默认值，没有此参数则返回默认值
function request_data($type,$ind,$dval=''){
    $type=strtolower($type);
    if(empty($ind) || !is_string($ind)){
        return $dval;
    }
    if($type=='get'){
        return (isset($_GET[$ind])?$_GET[$ind]:$dval);
    }
    elseif($type=='post'){
        return (isset($_POST[$ind])?$_POST[$ind]:$dval);
    }
    return $dval;
}
