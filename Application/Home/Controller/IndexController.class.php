<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends HomeController 
{
    public function index()
    {
    	$data = M('carousel')->where('state=1')->order('id desc')->limit(5)->select();
    	$notice = M('notice')->where('state=1')->order('id desc')->limit(1)->select();
    	$info =  M('info')->field('i.id, i.name, i.sex, i.descr, i.picname, i.ctime, t.name tname')->table('pet_type t, pet_info i')->where('i.state=2 and i.typeid=t.id')->order('id desc')->limit(10)->select();
    	$user = M('user')->field('u.id, u.name, u.sex, u.exp, u.picname')->table('pet_user u, pet_user_role ur, pet_role r')->where('u.state=1 and u.id=ur.uid and ur.rid=r.id and r.name="普通用户"')->order('exp desc')->limit(10)->select();
        $tips = M('tips')->field('id, title')->where('state=1')->order('ctime desc')->limit(8)->select();
    	$post = M('post')->field('u.name, u.exp, u.picname, p.id, p.uid, p.content, p.ctime')->table('pet_user u, pet_post p')->where('p.state=1 and p.uid=u.id')->order('id desc')->limit(3)->select();
    	// dump($notice);die;
    	$this->assign('notice',$notice[0]);
    	$this->assign('info',$info);
        $this->assign('first',$data[0]);
    	$this->assign('data',$data);
    	$this->assign('user',$user);
        $this->assign('tips',$tips);
    	$this->assign('post',$post);

        
        $this->display();
    }
}