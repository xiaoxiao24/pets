<?php  
namespace Admin\Controller;
use Think\Controller;

/**
* LeftwordController 留言板管理
*
* @author xiao 
* @version 1.0
*/
class LeftwordController extends AdminController
{
    public function index()
    {
        $data = M('leftword')->field('u.name, l.ctime, l.content, l.id')->table('pet_user u, pet_leftword l')->select();
        $this->assign('data',$data);
        $this->assign('title',留言板);
        $this->assign('part',留言板列表);
        $this->display();
    }

    
}
