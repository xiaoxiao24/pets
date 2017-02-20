<?php 
namespace Home\Controller;
use \Think\Controller;

/**
* UserController 用户资料
*
* @author xiao
* @version 1.0
*/
class UserController extends HomeController
{
    public function _initialize()
    {
        date_default_timezone_set('PRC');
        if(empty(session('home_user'))){
            $this->error('请先登录', U('Login/index'));
        }
    }

    public function index()
    {   
        $post = M('Post')->where('uid='.session('home_user.id').' and state=1')->order('ctime desc')->page($_GET['p'],10)->select();
        // 相册
        $pic_count = M('picture')->where('uid='.session('home_user.id'))->count();
        // 收藏
        $col_count = M('collection')->where('selfid='.session('home_user.id'))->count();
        // 分页
        $post_count = M('post')->where('uid='.session('home_user.id').' and state=1')->count();

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
        $fans_count = M('fans')->where('status=1 and selfid='.session('home_user.id'))->count();

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

    /*
        个人资料更新
    */ 
    public function info()
    {
        $User = M('User')->where('id='.session('home_user.id'))->find();
        $this->assign('user',$User);

        // 相册
        $pic_count = M('picture')->where('uid='.session('home_user.id'))->count();
        // 收藏
        $col_count = M('collection')->where('selfid='.session('home_user.id'))->count();
        // 动态
        $post_count = M('post')->where('uid='.session('home_user.id').' and state=1')->count();
        // 好友
        $fans_count = M('fans')->where('status=1 and selfid='.session('home_user.id'))->count();

        $this->assign('fans_count', $fans_count);
        $this->assign('post_count', $post_count);
        $this->assign('pic_count', $pic_count);
        $this->assign('col_count', $col_count);
        $this->display();
    }

    public function save()
    {
        $User = D("User"); // 实例化User对象
        if (!$User->create()){
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            $this->error($User->getError());
        }else{
            // 验证通过 可以进行其他数据操作
            $id = I('post.id');
            $map['sex'] = I('post.sex');
            $map['code'] = I('post.code');
            $map['birthday'] = strtotime(I('post.birthday'));
            $map['descr'] = I('post.descr');
            if($User->where("id=".$id)->save($map)){
                $this->success('修改成功');
                $data = $User->where('id='.I('post.id'))->find();
                $_SESSION['home_user']=$data;
            }else{
                $this->error('修改失败');
            }
        }
    }

    // 密码显示
    public function pwd()
    {
        $this->display('User:pwd');
    }

    // 密码修改操作
    public function pwdsave()
    {
        if(md5(I('post.prepasswd')) == session('home_user.passwd')){
            if(I('post.passwd') != I('post.confirmpasswd')){
                $this->error('两次密码输入不一致');
            }

            $map['passwd'] = md5(I('post.passwd'));
            M('User')->where('id='.session('home_user.id'))->save($map);
            session(null);
            $this->success("密码修改成功", U('Login/index'));
        }else{  
            $this->error('原密码错误');
        }
    }

    /*
        头像页
    */ 
    public function head()
    {
        $this->display('User:head');
    }

    // 头像更新操作
    public function headsave()
    {
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
                    session('home_user.picname', $path);
                    M('User')->where('id='.session('home_user.id'))->save(array('picname'=>$path));
                    $this->success('上传头像成功');
                }
        }else{
            $this->error('请选择图片');
        }
    }

    
}