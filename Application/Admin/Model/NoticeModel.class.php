<?php  
namespace Admin\Model;
use Think\Model;

class NoticeModel extends Model
{   
    protected $_validate = array(
        // array(验证字段2,验证规则,错误提示,[验证条件,附加规则,验证时间])
        array('name','require','请您填写公告内容'),
        
    );

    protected $_auto = array (
        // array(完成字段1,完成规则,[完成条件,附加规则]),
        array('ctime', 'time', 3, 'function'), // 对regtime字段在创建的时候写入当前时间戳
    );
}