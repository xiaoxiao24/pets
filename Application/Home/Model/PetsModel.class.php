<?php  
namespace Home\Model;
use Think\Model;

class PetsModel extends Model
{   
	protected $tableName = 'info';
    
    protected $_validate = array(
        // array(验证字段2,验证规则,错误提示,[验证条件,附加规则,验证时间])
        array('typeid','require','请选择宠物类别'),
        array('name','require','请输入宠物昵称'),
        array('descr', '10,200', '简介长度不正确', 0, 'length'),
    );

    protected $_auto = array (
        // array(完成字段1,完成规则,[完成条件,附加规则]),
        
        array('atime', 'time', 1, 'function'), // 对ctime字段在新增数据的时候写入当前时间戳
    );
}