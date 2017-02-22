<?php
namespace Home\Controller;
use Think\Controller;

/**
*SearchController 搜索控制器
* @author yn
* @version 1.0
*/

class SearchController extends HomeController
{
	
    public function dosearch()
    {

       if(IS_AJAX){
        $name = I('post.name');
        // var_dump($name);
        $data=M('info')->field('i.uid, i.picname ipic, i.name iname, i.ctime ictime, i.descr, u.name uname, u.picname upic, t.name tname')->table('pet_info i, pet_type t, pet_user u')->where("(i.name like '%$name%') or (t.name like '%$name%' and t.id=i.typeid) and u.id=i.uid")->select();
        if($data){
            $id=$data['id'];
            $this->ajaxReturn($id);
        }else{
            $this->ajaxReturn(false);
        }
       } 
    }

    
}