<?php  
namespace Home\Controller;
use Think\Controller;

/**
* CollectionController 收藏操作
*
* @author xiao
* @version 1.0
*/
class CollectionController extends HomeController
{

	public function index()
    {
        $collection = M('Collection');

        $list = $collection->table('pet_collection c, pet_tips t')->where("c.selfid=".session('home_user.id').' and c.tid=t.id')->page($_GET['p'].',10')->select();
        $this->assign('list',$list);
        $count = $collection->where("c.selfid=".session('home_user.id'))->count();// 查询满足要求的总记录数
        $Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数

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
        $this->assign('page',$show);// 赋值分页输出
        
        $fans_count = M('fans')->where('selfid='.session('home_user.id'))->count();
		
        // 动态
        // $post_count = M('post')->where('uid='.session('home_user.id').' and state=1')->count();
        // 相册
        $pic_count = M('picture')->where('uid='.session('home_user.id'))->count();
        

        $this->assign('fans_count', $fans_count);
        // $this->assign('post_count', $post_count);
        $this->assign('pic_count', $pic_count);
        $this->assign('col_count', $count);

        $this->display();
    }

    /**
    * 小知识取消收藏
    * @access public        
    * @return void
    */
    public function delcoll()
    {
        if (empty($_SESSION['home_user'])) {
            $this->error('登录后再发帖，请先登录！！',U('Login/index'));
            exit;
        }
        if (IS_AJAX) {
            $del = M('collection')->where('tid='.I('post.tid').' and selfid='.I('post.selfid'))->delete();
            if ($del == false) {
                $this->ajaxReturn(false);
            } else {
                $this->ajaxReturn(true);
            }
        }
        
    }

    /**
    * 小知识收藏
    * @access public        
    * @return void
    */
    public function coll()
    {
        if (empty($_SESSION['home_user'])) {
            $this->error('登录后再发帖，请先登录！！',U('Login/index'));
            exit;
        }
        if (IS_AJAX) {
            $data['selfid'] = I('post.selfid');
            $data['tid'] = I('post.tid');

            $coll = M('collection')->add($data);
            if ($coll == false) {
                $this->ajaxReturn(false);
            } else {
                $this->ajaxReturn(true);
            }
            
        }
        
    }
}