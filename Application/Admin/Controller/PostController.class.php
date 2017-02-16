<?php  
namespace Admin\Controller;
use Think\Controller;

class PostController extends AdminController
{

    public function index($state='')
    {
        // 根据说说状态  查看
        if (!empty(I('get.state'))) {
            // 说说状态为显示
            if (I('get.state') == 'showbtn') {
                $state = ' and p.state=1';
            } elseif (I('get.state') == 'hidebtn') { //说说状态为隐藏
                $state = ' and p.state=0';
            }
        }
        $post = M('post')->field('u.name uname, u.picname upic, p.id, p.ctime, p.state, p.uid, p.content')->table('pet_post p, pet_user u')->where('u.id=p.uid'.$state)->select();
        $this->assign('title','说说管理');
        $this->assign('part','说说列表');
        $this->assign('post',$post);
        $this->display();
    }

    
    /**
    * 说说隐藏
    * @access public        
    * @return void
    */
    public function stop()
    {
        if (IS_AJAX) {
            $data['state'] = 0;
            $user = M('post')->where('id='.I('post.id'))->save($data);
            if ($user == false) {
                $this->ajaxReturn(false);
            } else {
                $this->ajaxReturn(true);

            }
        } else {
            $this->ajaxReturn(false);
        }
        
    }

    /**
    * 说说显示
    * @access public        
    * @return void
    */
    public function start()
    {
        if (IS_AJAX) {
            $data['state'] = 1;
            $user = M('post')->where('id='.I('post.id'))->save($data);
            if ($user == false) {
                $this->ajaxReturn(false); 
            } else {
                $this->ajaxReturn(true);
            }
        } else {
            $this->ajaxReturn(false);
        }
    }  
}