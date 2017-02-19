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
        // 未获取好友id
        if (empty(I('get.uid'))) {
            $this->display('Public:404');
            exit;
        }

    }

    /**
    * 取消好友
    * @access public        
    * @return void
    */
    public function closebtn()
    {
        if (IS_AJAX) {
            $del = M('fans')->where('uid='.I('post.uid').' and selfid='.I('post.selfid'))->delete();
            if ($del == false) {
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
        if (IS_AJAX) {
            $data['uid'] = I('post.uid');
            $data['selfid'] = I('post.selfid');

            $follow = M('fans')->add($data);
            if ($follow == false) {
                $this->ajaxReturn(false);
            } else {
                $this->ajaxReturn(true);
            }
            
        }
        
    }


}