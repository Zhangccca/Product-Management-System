<?php
/**
 * Created by PhpStorm.
 * User: Princess-c
 * Date: 2017/12/13
 * Time: 15:00
 */

namespace app\common\model;
use think\Model;
use traits\model\SoftDelete;

class User extends Model
{
    //默认情况下当前模型类对应的数据表是 模型类的名称
    protected $table = 'users'; //当前操作的数据表的名称
    //是否开启自动维护的两个时间戳
    protected $autoWriteTimestamp= 'datetime';//使用 timestopm类型
    protected $createTime = 'create_time';//插入记录时，自动维护字段
    protected $updateTime = 'update_time';//更新记录时，自动维护字段
    protected $deleteTime = 'delete_time';//软删除字段,时间戳

    //引入软删除trait
    use SoftDelete;
}