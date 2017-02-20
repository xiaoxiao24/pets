<?php
namespace Home\Controller;
use Think\Controller;
class NoticeController extends HomeController 
{
    public function index()
    {
    	$notice = M('notice')->where('state=1')->order('ctime desc')->limit(5)->select();

    	// 分页
        $count = M('notice')->where('state=1')->count();

        $Page = new \Think\Page($count,5);// 实例化分页类 传入总记录数和每页显示的记录数

        $Page->setConfig('first','首页');
        $Page->setConfig('last','尾页');
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        
        $Page->setConfig('theme','
            <nav>
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
    	$this->assign('notice',$notice);
        $this->display();
    }

    // 添加动态
    public function doAdd()
    {
        if (empty($_SESSION['home_user'])) {
            $this->error('登录后再发帖，请先登录！！',U('Login/index'));
            exit;
        }

        $data = $_POST;
        
        $post = D("Leftword"); // 实例化User对象
        if (!$post->create($data)){ 
            $this->error($post->getError());
        }else{
            // 执行添加
            if ($post->add() > 0) {
                $exps = M('User')->where('id='.$data['uid'])->find();
                $exp['exp']=10+$exps['exp'];
                M('User')->where('id='.$data['uid'])->save($exp);
                $this->success('添加成功', U('Notice/index'));
            } else {
                $this->error('添加失败');
            }   
        }
    }
}