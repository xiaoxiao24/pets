<?php
namespace Home\Controller;
use Think\Controller;
class FansController extends HomeController 
{

	 public function _initialize()
    {
        date_default_timezone_set('PRC');
        // 用户未登录
        if(empty(session('home_user'))){
            $this->error('请先登录', U('Login/index'));
        }
        
    }

    /**
    * 好友
    * @access public        
    * @return void
    */
    public function index()
    {
        $fans = M('fans')->field('f.id, f.uid, u.name, u.sex, u.exp, u.picname')->table('pet_user u, pet_fans f')->where('f.status=1 and f.selfid='.session('home_user.id').' and f.uid=u.id')->order('id desc')->page($_GET['p'],12)->select();
           
        // 好友 分页
        $fans_count = M('fans')->where('status=1 and selfid='.session('home_user.id'))->count();
        $Page = new \Think\Page($count,12);// 实例化分页类 传入总记录数和每页显示的记录数

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

        // 收藏
        $col_count = M('collection')->where('selfid='.session('home_user.id'))->count();
        // 动态
        $post_count = M('post')->where('uid='.session('home_user.id').' and state=1')->count();
        // 相册
        $pic_count = M('picture')->where('uid='.session('home_user.id'))->count();
        

        $this->assign('fans_count', $fans_count);
        $this->assign('post_count', $post_count);
        $this->assign('pic_count', $pic_count);
        $this->assign('col_count', $col_count);
        
        $this->assign('fans',$fans);
        $this->display();
    }

    /**
    * 好友消息
    * @access public        
    * @return void
    */
    public function news()
    {
        $news = M('fans')->field('f.id, f.selfid, f.status, u.name, u.picname')->table('pet_user u, pet_fans f')->where('f.status!=3 and f.uid='.session('home_user.id').' and f.selfid=u.id')->order('id desc')->page($_GET['p'],12)->select();
           
        // 分页
        $count = M('fans')->table('pet_user u, pet_fans f')->where('f.status=0 and f.uid='.session('home_user.id').' and f.selfid=u.id')->count();
        $Page = new \Think\Page($count,12);// 实例化分页类 传入总记录数和每页显示的记录数

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

        // 好友 
        $fans_count = M('fans')->where('status=1 and selfid='.session('home_user.id'))->count();
        // 收藏
        $col_count = M('collection')->where('selfid='.session('home_user.id'))->count();
        // 动态
        $post_count = M('post')->where('uid='.session('home_user.id').' and state=1')->count();
        // 相册
        $pic_count = M('picture')->where('uid='.session('home_user.id'))->count();
        

        $this->assign('fans_count', $fans_count);
        $this->assign('post_count', $post_count);
        $this->assign('pic_count', $pic_count);
        $this->assign('col_count', $col_count);
        
        $this->assign('fans',$fans);
        $this->display();
    }


    /**
    * 取消好友
    * @access public        
    * @return void
    */
    public function closebtn()
    {
        // 未获取好友id
        if (empty(I('get.uid'))) {
            $this->display('Public:404');
            exit;
        }

        if (IS_AJAX) {
            
            $new['status'] = 3;
            $up = M('fans')->where('uid='.I('get.uid').' and selfid='.session('home_user')['id'])->update($new);
            $follow = M('fans')->where('selfid='.I('get.uid').' and uid='.session('home_user')['id'])->update($new);
            if ($follow == false) {
                $this->ajaxReturn(false);
            } else {
                $this->ajaxReturn(true);
            }
            
        }
        
    }

    /**
    * 加好友
    * @access public        
    * @return void
    */
    public function startbtn()
    {
        // 未获取好友id
        if (empty(I('get.uid'))) {
            $this->display('Public:404');
            exit;
        }

        if (IS_AJAX) {
            $data['uid'] = I('post.uid');
            $data['selfid'] = session('home_user')['id'];

            $follow = M('fans')->add($data);
            if ($follow == false) {
                $this->ajaxReturn(false);
            } else {
                $this->ajaxReturn(true);
            }
            
        }
        
    }

    /**
    * 确认好友
    * @access public        
    * @return void
    */
    public function sure()
    {
        // 未获取好友id
        if (empty(I('get.uid'))) {
            $this->display('Public:404');
            exit;
        }

        if (IS_AJAX) {
            $data['uid'] = I('post.uid');
            $data['selfid'] = session('home_user')['id'];
            $data['status'] = 1;

            $new['status'] = 1;
            $follow = M('fans')->add($data);
            $up = M('fans')->where('id='.I('get.id'))->update($new);
            if ($follow == false) {
                $this->ajaxReturn(false);
            } else {
                $this->ajaxReturn(true);
            }
            
        }
        
    }

    /**
    * 拒绝好友
    * @access public        
    * @return void
    */
    public function confuse()
    {
        // 未获取好友id
        if (empty(I('get.id'))) {
            $this->display('Public:404');
            exit;
        }

        if (IS_AJAX) {

            $new['status'] = 2;
            $up = M('fans')->where('id='.I('get.id'))->update($new);
            if ($up == false) {
                $this->ajaxReturn(false);
            } else {
                $this->ajaxReturn(true);
            }
            
        }
        
    }


    /**
    * 好友资料
    * @access public        
    * @return void
    */
    public function fans()
    {
        // 未获取好友id
        if (empty(I('get.id'))) {
            $this->display('Public:404');
            exit;
        }

        $info = M('user')->where('id='.I('get.id'))->select();
        $this->assign('info',$info);

        $post = M('Post')->where('uid='.I('get.id').' and state=1')->order('ctime desc')->page($_GET['p'],5)->select();
        // 相册
        $pic_count = M('picture')->where('uid='.I('get.id'))->count();
        // 收藏
        $col_count = M('collection')->where('selfid='.I('get.id'))->count();
        // 分页
        $post_count = M('post')->where('uid='.I('get.id').' and state=1')->count();

        $Page = new \Think\Page($post_count,10);// 实例化分页类 传入总记录数和每页显示的记录数

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
        $fans_count = M('fans')->where('status=1 and selfid='.I('get.id'))->count();

        foreach ($post as $k => $v) {
            $post[$k]['collect_count'] = M('collection')->where('postid='.$v['id'])->count('id');
            $post[$k]['reply_count'] = M('reply')->where('postid='.$v['id'])->count('id');
        }

        $this->assign('post', $post);

        $this->assign('fans_count', $fans_count);
        $this->assign('post_count', $post_count);
        $this->assign('pic_count', $pic_count);
        $this->assign('col_count', $col_count);
        $this->display();
        
    }


    public function pic()
    {   
        // 未获取好友id
        if (empty(I('get.id'))) {
            $this->display('Public:404');
            exit;
        }

        $info = M('user')->where('id='.I('get.id'))->select();
        $this->assign('info',$info);

        $pic =  M('picture')->where('uid='.I('get.id'))->order('id desc')->page($_GET['p'],12)->select();
        // 相册 分页
        $pic_count = M('picture')->where('uid='.I('get.id'))->count();
        $Page = new \Think\Page($pic_count,12);// 实例化分页类 传入总记录数和每页显示的记录数

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

        // 收藏
        $col_count = M('collection')->where('selfid='.I('get.id'))->count();
        // 动态
        $post_count = M('post')->where('uid='.I('get.id').' and state=1')->count();
        // 好友
        $fans_count = M('fans')->where('status=1 and selfid='.I('get.id'))->count();

        $this->assign('fans_count', $fans_count);
        $this->assign('post_count', $post_count);
        $this->assign('pic_count', $pic_count);
        $this->assign('col_count', $col_count);
        // var_dump($pic);die;
        $this->assign('pics',$pic);
        $this->display();
    }

}