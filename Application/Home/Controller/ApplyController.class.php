<?php
namespace Home\Controller;
use Think\Controller;
class AdoptController extends HomeController 
{

    // 申请宠物领养
    public function index() 
    {
        // if(empty(session('home_user'))){
        //     $this->error('请先登录', U('Login/index'));
        // }

        $type = D('Type');
        $list = $type->getAdminCate();
        $this->assign('list',$list);
        $this->display();
    }


    /**
    * 执行创建宠物
    * @access public        
    * @return void
    */
    public function doAdd()
    {

        //得到数据模型
        $pets = D('apply');
        //过滤数据,数据验证
        if (!$pets->create()) {
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            if(IS_AJAX){   
                $this->ajaxReturn($pets->getError());
            }else{
                $this->error($pets->getError());
            }
               
        } else {
            // 验证通过 可以进行其他数据操作
            if(!IS_AJAX){
                // 验证通过 可以进行其他数据操作
                if($_FILES['picname']['name'] != ''){
                    $config = array(
                        'maxSize' => 3145728,
                        'rootPath' => './Upload/img/pets/',
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
                    } else {// 上传成功 获取上传文件信息
                        $path = $info['savepath'].$info['savename'];
                        $image = new \Think\Image();
                        $image->open("./Upload/img/pets/".$path);
                        // 按照原图的比例生成一个最大为90*90的缩略图并保存为thumb.jpg
                        $path = time().$info['savename'];
                        $image->thumb(100, 100)->save('./Upload/img/pets-thumb/'.$path);
                    }
                }
                
                if($path != ''){
                    $data['picname'] = $path;
                    $data['name'] = I('post.name');
                    $data['typeid'] = I('post.typeid');
                    $data['descr'] = I('post.descr');
                    $data['state'] = 0;
                    $data['begstate'] = 0;
                    $data['atime'] = time();
                    $data['uid'] = session('home_user')['id']; 
                    // 执行添加
                    if ($pets->add($data) > 0) {
                        $this->success('添加成功', U('addView'));
                    } else {
                        $this->error('添加失败');
                    }   
                } else {
                    $this->error('请上传图片');
                }
                
            }
        }
    }

}