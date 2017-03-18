<?php
namespace Admin\Controller;
use Think\Controller;

/**
* 后台登录操作
*
* @author xiao <chnljx@126.com>
* @version 1.0
*/
class LoginController extends AdminController 
{
    /**
    * 进入页面时判断是否已经登录
    * @return void
    */
    public function _initialize()
    {
        if(session('?admin_user'))
        {
            $this->redirect('Index/index');
        }
    }

    /**
     * 显示登录界面
     * @access public        
     * @return void
     */
    public function index()
    {
        $this->display();
    }

    /**
     * 验证码
     * @access public        
     * @return void
     */
    public function getVerify()
    {
        $config = array(
            'length'        => 1,           // 验证码位数
            'fontSize'      => 20,          // 验证码字体大小（像素）
            'imageW'        => 205,         // 验证码宽度
            'imageH'        => 41,          // 验证码高度 
        );
        $Verify = new \Think\Verify($config);
        $Verify->entry();
    }

    /**
     * ajax验证
     * @access public        
     * @return void
     */
    public function doLogin()
    {   
        cookie('name', I('post.name'));
        $User = D("Login"); // 实例化User对象
        if (!$User->create()){
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            if(IS_AJAX){
                $this->ajaxReturn($User->getError());
            }else{
                $this->error($User->getError(), U('Login/index'));
            }
        }else{
            // 验证通过 可以进行其他数据操作
            if(!IS_AJAX){
                $map = [];
                $map['name'] = I('post.name');
                $map['passwd'] = md5(I('post.passwd'));
                // var_dump($map);
                $data = $User->where($map)->find();
                // var_dump($data);die;

                $data = M('user_role')->field('u.id, u.name, u.passwd, u.regtime, u.sex, u.addr, u.code, u.phone, u.descr, u.state, u.picname, u.email, u.exp, u.lastip, u.lastdate, u.loginnum')->table('pet_user_role ur, pet_role r, pet_user u')->where('ur.uid='.$data['id'].' and ur.rid=r.id and r.name!="普通用户" and u.id=ur.uid')->find();
                // var_dump($data);die;

                // 如果用户名和密码匹配则进入，否则显示错误
                if ($data) {
                    
                    if($data['state'] == 0){
                        $this->error('账号已被禁用', U('Login/index'));
                    }

                    $data['loginnum'] += 1;
                    session('admin_user', $data);

                    // 登录次数自增
                    $User->where(session('admin_user.id'))->setInc('loginnum', 1);
                    // 更新登录时间和IP
                    $last['lastdate'] = time();
                    $last['lastip'] = get_client_ip();
                    $User->where(session('admin_user.id'))->save($last);

                    //根据用户id获取对应的节点信息
                    $list = M('node')->field('mname,aname')->where('status = 1 and id in'.M('role_node')->field('nid')->where("rid in ".M('user_role')->field('rid')->where(array('uid'=>array('eq',$data['id'])))->buildSql())->buildSql())->select();

                    //控制器名转换为大写
                    foreach ($list as $key => $val) {
                        $list[$key]['mname'] = ucfirst($val['mname']);
                    }

                    //查看查询出来的信息

                    $nodelist = array();
                    $nodelist['Index'] = array('index','desktop');
                    $nodelist['Logout'] = array('index','desktop');
                    //遍历重新拼装
                    foreach($list as $v){
                        $nodelist[$v['mname']][] = $v['aname'];
                        //把修改和执行修改 添加和执行添加 拼装到一起
                        if($v['aname']=="editview"){
                            $nodelist[$v['mname']][]="save";
                        }
                        if($v['aname']=="addview"){
                            $nodelist[$v['mname']][]="doadd";
                        }
                    }

                    //将权限信息放置到session中
                    $_SESSION['admin_user']['nodelist'] = $nodelist;

                    //重组的信息
                    // V($_SESSION);exit;

                    $this->redirect('Index/index');
                } else {
                    $this->error('账号或密码错误', U('Login/index'));
                }
                
            }
        }
    }
}