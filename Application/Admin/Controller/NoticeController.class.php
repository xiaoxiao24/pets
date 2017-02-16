<?php  
namespace Admin\Controller;
use Think\Controller;

/**
* NoticeController 公告管理
*
* @author xiao 
* @version 1.0
*/
class NoticeController extends AdminController
{
    public function index()
    {
        $data = M('notice')->select();
        $this->assign('data',$data);
        $this->assign('title',公告管路);
        $this->assign('part',公告列表);
        $this->display();
    }

    /**
    * 公告隐藏
    * @access public        
    * @return void
    */
    public function stop()
    {
        if (IS_AJAX) {
            $data['state'] = 0;
            $notice = M('notice')->where('id='.I('post.id'))->save($data);
            if ($notice == false) {
                $this->ajaxReturn(false);
            } else {
                $this->ajaxReturn(true);

            }
        } else {
            $this->ajaxReturn(false);
        }
        
    }

    /**
    * 公告显示
    * @access public        
    * @return void
    */
    public function start()
    {
        if (IS_AJAX) {
            $data['state'] = 1;
            $notice = M('notice')->where('id='.I('post.id'))->save($data);
            if ($notice == false) {
                $this->ajaxReturn(false); 
            } else {
                $this->ajaxReturn(true);
            }
        } else {
            $this->ajaxReturn(false);
        }
    }

    /**
    * 删除公告
    * @access public        
    * @return void
    */
    public function del()
    {
        if (IS_AJAX) {
            $notice = M('notice')->where('id='.I('post.id'))->delete();
            if ($notice == false) {
                $this->ajaxReturn(false); 
            } else {
                $this->ajaxReturn(true);
            }
        } else {
            $this->ajaxReturn(false);
        }
    }

    /**
    * 添加公告页面
    * @access public        
    * @return void
    */
    public function addView()
    {
        $this->display('notice:add');
    }  


    /**
    * 处理添加公告
    * @access public        
    * @return void
    */
    public function doAdd()
    {   
        $notice = D("notice");// 实例化User对象
        if (!$notice->create()){
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            if(IS_AJAX){    
                $this->ajaxReturn($notice->getError());
            }else{
                $this->error($notice->getError());
            }
        }else{
            // 验证通过 可以进行其他数据操作
            if(!IS_AJAX){
                $id = $notice->add();
                if($id){
                    M('notice')->add();
                    $this->success('添加成功');
                }else{
                    $this->error('添加失败');
                }
            }
        }
    }

    /**
    * 修改公告页面
    * @access public        
    * @return void
    */
    public function editView()
    {
        $data = D('notice')->where('id='.I('get.id'))->find();
        $this->assign('data',$data);
        $this->display('notice:edit');
    }  


    /**
    * 处理修改公告
    * @access public        
    * @return void
    */
    public function save()
    {   
        $notice = M("notice"); // 实例化User对象
        if (!$notice->create()){
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            if(IS_AJAX){    
                $this->ajaxReturn($notice->getError());
            }else{
                $this->error($notice->getError());
            }
        }else{
            // 验证通过 可以进行其他数据操作
            if(!IS_AJAX){
                $data = $notice->where('id='.I('post.id'))->save();
                if($data){
                    $this->success('修改成功');
                }else{
                    $this->error('修改失败');
                }
            }
        }
    }
}
