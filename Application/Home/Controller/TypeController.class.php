<?php
namespace Home\Controller;
use Think\Controller;

/**
*TypeController 分类控制器
* @author xiao
* @version 1.0
*/

class TypeController extends HomeController
{
    public function index()
    {
        if (empty(I('get.id'))) {
            $this->display('Public:404');
            exit;
        }
        $id=I('get.id');
        $name=M('type')->where("id=".$id)->field('name')->find();

        $data=M('info')->field('u.name uname,u.picname upic, i.uid, i.descr, i.state, i.ctime, i.id, i.sex, i.picname, i.name, t.name tname')->table('pet_user u, pet_info i, pet_type t')->where("i.typeid=t.id and i.state=2 and (t.pid=$id or t.id=$id) and u.id=i.uid")->order('i.ctime desc')->page($_GET['p'],6)->select();

        // var_dump($data);die;

        // 分页
        $count = M('info')->table('pet_info i, pet_type t')->where("i.typeid=t.id and i.state=2 and (t.pid=$id or t.id=$id)")->count();

        $Page = new \Think\Page($count,6);// 实例化分页类 传入总记录数和每页显示的记录数

        $Page->setConfig('first','首页');
        $Page->setConfig('last','尾页');
        $Page->setConfig('prev','');
        $Page->setConfig('next','');
        
        $Page->setConfig('theme','
            <nav class="cc">
              <ul class="pagination">
                <li>%FIRST%</li>
                <li>%UP_PAGE%</li>
                <li>%LINK_PAGE%</li>
                <li>%DOWN_PAGE%</li>
                <li>%END%</li>
              </ul>
            </nav>
        ');

        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);
        

        // 分类
        $type = M('type')->where('pid=0')->select();
        foreach ($type as $k => $v) {
            $type[$k]['se'] = M('type')->where('pid='.$v['id'])->select();
        }

        $this->assign('type',$type);
        $this->assign('name',$name['name']);

        $this->assign('list',$data);
        $this->display();
    }
	
    public function dosearch()
    {

       if(IS_AJAX){
            $name = I('post.name');
            // var_dump($name);
            $data=M('type')->where("name='$name'")->find();
            if($data){
                $id=$data['id'];
                $this->ajaxReturn($id);
            }else{
                $this->ajaxReturn(false);
            }
       } 
    }

    
}