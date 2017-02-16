<?php  
namespace Admin\Controller;
use Think\Controller;

/**
* UserController  用户表控制器
*
* @author xiao
* @version 1.0
*/

class UserController extends AdminController
{
    private $roleName = array('name'=>'');  // 角色名称

    // 普通用户显示列表
    public function index()
    {
        $this->assign('title', '用户管理');
        $this->assign('part', '用户列表');

        $this->roleName['name'] = array('eq', '普通用户');
        $data = M('User')->where('id in'.M('user_role')->field('uid')->where('rid in'.M('role')->field('id')->where($this->roleName)->buildSql())->buildSql())->select();

        // $nodelist = session('admin_user.nodelist'); //获取权限列表
        // if(in_array('stop',$nodelist['User'])){
        //     $flag = 0;
        // }elseif(in_array('start',$nodelist['User'])){
        //     $flag = 1;
        // }

        // $this->assign('flag', $flag);
        $this->assign('list', $data);
        $this->assign('num', count($data));
        $this->display();
    }

    // 显示添加页面
    public function addview()
    {
        $this->display('User:add');
    }

    // 用户添加操作
    public function doadd()
    {   
        $User = D("User"); // 实例化User对象
        if (!$User->create()){
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            if(IS_AJAX){    
                $this->ajaxReturn($User->getError());
            }else{
                $this->error($User->getError());
            }
        }else{
            // 验证通过 可以进行其他数据操作
            if(!IS_AJAX){
                $id = $User->add();
                if($id){
                    $data['uid'] = $id;
                    $rid = M('role')->field('id')->where(array('name'=>array('eq', '普通用户')))->find();
                    $data['rid'] = $rid['id'];

                    M('user_role')->add($data);
                    $this->success('添加成功');
                }else{
                    $this->error('添加失败');
                }
            }
        }
    }

    // 禁用帐号操作
    public function stop()
    {   
        // echo I('post.id');exit;
        $data['state'] = 1;
        $msg = M('User')->where('id='.I('post.id'))->save($data);
        if($msg === false){
            echo "停用失败";
            exit;
        }
    }

    // 启用帐号操作
    public function start()
    {   
        // echo I('post.id');exit;
        $data['state'] = 0;
        $msg = M('User')->where('id='.I('post.id'))->save($data);
        if($msg === false){
            echo "启用失败";
            exit;
        }
    }

    // 显示用户信息
    public function info()
    {
        $data = M('User')->where('id='.I('get.id'))->find();
        $this->assign('list', $data);
        $this->display();
    }

    // 用户信息修改
    public function editview()
    {
        $data = M('User')->where('id='.I('get.id'))->find();
        $this->assign('list', $data);
        $this->display('User:edit');
    }

    // 用户信息修改操作
    public function save()
    {   
        $User = D("User"); // 实例化User对象
        if (!$User->create()){
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            // if(IS_AJAX){    
                // $this->ajaxReturn($User->getError());
            // }else{
                $this->error($User->getError());
            // }
        }else{
            // 验证通过 可以进行其他数据操作
            if(!empty($_FILES['picname']['tmp_name'])){
                $config = array(
                    'maxSize' => 5242880,
                    'rootPath' => './Upload/img/avatar/',
                    'saveName' => array('uniqid',''),
                    'exts' => array('jpg', 'gif', 'png', 'jpeg'),
                    'autoSub' => true,
                    'subName' => array('date','Ymd'),
                );
                $upload = new \Think\Upload($config);// 实例化上传类
                // 上传单个文件
                $info = $upload->uploadOne($_FILES['picname']);
                if(!$info) {// 上传错误提示错误信息
                    $this->error($upload->getError());
                }else{// 上传成功 获取上传文件信息
                    $path = $info['savepath'].$info['savename'];
                    $image = new \Think\Image();
                    $image->open("./Upload/img/avatar/".$path);
                    $path = time().$info['savename'];
                    $image->thumb(140, 150)->save('./Upload/img/avatar-thumb/'.$path);
                }
            }
            
            if($path != ''){
                $map['picname'] = $path;
            }

            if($_POST['birthday'] != ''){
                $map['birthday'] = strtotime($_POST['birthday']);
            }

            if($_POST['rid'] != ''){
                $user_role['rid'] = $_POST['rid'];
                M('user_role')->where('uid='.$_POST['id'])->save($user_role);
            }

            $map['sex'] = $_POST['sex'];
            $map['email'] = $_POST['email'];
            $map['phone'] = $_POST['phone'];
            $map['code'] = $_POST['code'];
            $map['descr'] = $_POST['descr'];
            $User->where('id='.$_POST['id'])->save($map); // 根据条件更新记录
            $this->success('修改成功');
        }
    }

   

    // 经验值
    public function grade()
    {   
        $this->roleName['name'] = array(array('eq', '吧主'), array('eq', '普通用户'), 'or');
        $data = M('User')->field('id,name,exp,state')->where('id in'.M('user_role')->field('uid')->where('rid in'.M('role')->field('id')->where($this->roleName)->buildSql())->buildSql())->select();
        // V($data);exit;
        $this->assign('list', $data);
        $this->assign('num', count($data));
        $this->assign('title','用户管理');
        $this->assign('part','等级列表');
        $this->display();
    }
}
