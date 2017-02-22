<?php
namespace Home\Controller;
use Think\Controller;
class TipsController extends HomeController 
{
    public function index()
    {
        $tips = M('tips')->field('id, title')->where('state=1')->order('ctime desc')->page($_GET['p'],20)->select();

        // 分页
        $count = M('tips')->where('state=1')->count();

        $Page = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数

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
        $this->assign('tips',$tips);

        $this->display();
    }

   
    //百度分享
    public function baidu()
    {
        $this->display('Tips/detail');
    }

    public function detail()
    {
    	if (empty(I('get.id'))) {
    		$this->display('Public:404');
            exit;
    	}

    	$tips = M('tips')->where('state=1 and id='.I('get.id'))->find();
    	if (empty($tips)) {
    		$this->display('Public:404');
            exit;
    	}

    	$this->assign('detail',$tips);
    	$this->display();
    }
}