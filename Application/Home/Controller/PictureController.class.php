<?php 
namespace Home\Controller;
use \Think\Controller;

/**
* PictureController 用户相册
*
* @author xiao
* @version 1.0
*/
class PictureController extends HomeController
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
    	$pic =  M('picture')->where('uid='.session('home_user.id'))->order('id desc')->page($_GET['p'],12)->select();
        // 相册 分页
        $pic_count = M('picture')->where('uid='.session('home_user.id'))->count();
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
        $col_count = M('collection')->where('selfid='.session('home_user.id'))->count();
        // 动态
        $post_count = M('post')->where('uid='.session('home_user.id').' and state=1')->count();
        // 好友
        $fans_count = M('fans')->where('selfid='.session('home_user.id'))->count();

        $this->assign('fans_count', $fans_count);
        $this->assign('post_count', $post_count);
        $this->assign('pic_count', $pic_count);
        $this->assign('col_count', $col_count);
        // var_dump($pic);die;
        $this->assign('pics',$pic);
        $this->display();
    }


     public function add()
    {
        $this->display();
    }

    public function save()
    {
        $pic = M('picture');
        // 验证通过 可以进行其他数据操作
        if(!empty($_FILES['picname']['tmp_name'])){
            $config = array(
                'maxSize' => 5242880,
                'rootPath' => './Upload/img/pic/',
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
                $image->open("./Upload/img/pic/".$path);
                $path = time().$info['savename'];
                $image->thumb(160, 120)->save('./Upload/img/pic-thumb/'.$path);
            }
        }
        
        if($path != ''){
            $map['picname'] = $path;
            $map['uid'] = session('home_user')['id'];
            $pics = M('picture')->add($map);
            if ($pics > 0) {
                $this->success('添加成功'); 
            } else {
                $this->error('添加失败');
            }
            
        } else {
            $this->error('请上传图片');
        }

    }

    public function del()
    {
        $data['id'] = I('get.picid');
        $data['uid'] = session('home_user')['id'];
        $del = M('picture')->where($data)->delete();
        if ($del>0) {
            $this->redirect('Picture:index');
        } else {
            $this->redirect('Picture:index');
        }
    }
   
}