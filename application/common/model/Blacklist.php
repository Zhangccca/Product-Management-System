<?php
/**
 * Created by PhpStorm.
 * User: Princess-c
 * Date: 2017/12/13
 * Time: 16:01
 */

namespace app\common\model;
use think\Model;
use traits\model\SoftDelete;

class Blacklist extends Model
{

    protected $table='blacklist';
    //是否开启自动维护的两个时间戳
    protected $autoWriteTimestamp= 'datetime';//使用 timestopm类型
    protected $createTime = 'create_time';//插入记录时，自动维护字段
    protected $updateTime = 'update_time';//更新记录时，自动维护字段
    protected $deleteTime = 'delete_time';//软删除字段,时间戳
    use SoftDelete;
}
