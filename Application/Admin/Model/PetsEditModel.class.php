<?php  
namespace Admin\Model;
use Think\Model;

class PetsEditModel extends Model
{
    protected $tableName = 'info';
    
    protected $_validate = array(
        // array(验证字段2,验证规则,错误提示,[验证条件,附加规则,验证时间])
        array('typeid','require','请选择宠物类别'),
        array('name','require','请输入宠物昵称'),
        
    );
}