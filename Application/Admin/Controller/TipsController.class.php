<?php  
namespace Admin\Controller;
use Think\Controller;

/**
* TipsController 小知识管理
*
* @author xiao 
* @version 1.0
*/
class TipsController extends AdminController
{
    public function index()
    {
        $data = M('tips')->select();
        $this->assign('data',$data);
        $this->assign('title',小知识管路);
        $this->assign('part',小知识列表);
        $this->display();
    }

    /**
    * 小知识隐藏
    * @access public        
    * @return void
    */
    public function stop()
    {
        if (IS_AJAX) {
            $data['state'] = 0;
            $tips = M('tips')->where('id='.I('post.id'))->save($data);
            if ($tips == false) {
                $this->ajaxReturn(false);
            } else {
                $this->ajaxReturn(true);

            }
        } else {
            $this->ajaxReturn(false);
        }
        
    }

    /**
    * 小知识显示
    * @access public        
    * @return void
    */
    public function start()
    {
        if (IS_AJAX) {
            $data['state'] = 1;
            $tips = M('tips')->where('id='.I('post.id'))->save($data);
            if ($tips == false) {
                $this->ajaxReturn(false); 
            } else {
                $this->ajaxReturn(true);
            }
        } else {
            $this->ajaxReturn(false);
        }
    }

    /**
    * 删除小知识
    * @access public        
    * @return void
    */
    public function del()
    {
        if (IS_AJAX) {
            $tips = M('tips')->where('id='.I('post.id'))->delete();
            if ($tips == false) {
                $this->ajaxReturn(false); 
            } else {
                $this->ajaxReturn(true);
            }
        } else {
            $this->ajaxReturn(false);
        }
    }

    /**
    * 添加小知识页面
    * @access public        
    * @return void
    */
    public function addView()
    {
        $this->display('tips:add');
    }  


    /**
    * 处理添加小知识
    * @access public        
    * @return void
    */
    public function doAdd()
    {   
        $tips = D("tips");// 实例化User对象
        if (!$tips->create()){
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            if(IS_AJAX){    
                $this->ajaxReturn($tips->getError());
            }else{
                $this->error($tips->getError());
            }
        }else{
            // 验证通过 可以进行其他数据操作
            if(!IS_AJAX){
                $id = $tips->add();
                if($id){
                    M('tips')->add();
                    $this->success('添加成功');
                }else{
                    $this->error('添加失败');
                }
            }
        }
    }

    /**
    * 修改小知识页面
    * @access public        
    * @return void
    */
    public function editView()
    {
        $data = D('tips')->where('id='.I('get.id'))->find();
        $this->assign('data',$data);
        $this->display('tips:edit');
    }  


    /**
    * 处理修改小知识
    * @access public        
    * @return void
    */
    public function save()
    {   
        $tips = M("tips"); // 实例化User对象
        if (!$tips->create()){
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            if(IS_AJAX){    
                $this->ajaxReturn($tips->getError());
            }else{
                $this->error($tips->getError());
            }
        }else{
            // 验证通过 可以进行其他数据操作
            if(!IS_AJAX){
                $data = $tips->where('id='.I('post.id'))->save();
                if($data){
                    $this->success('修改成功');
                }else{
                    $this->error('修改失败');
                }
            }
        }
    }
}
