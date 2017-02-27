<?php
namespace Home\Controller;
use Think\Controller;
class ApplyController extends HomeController 
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
        $data = $_POST;
        
        //得到数据模型
        $pets = D('pets');
        //过滤数据,数据验证
        if (!$pets->create($data)) {
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
                   
                    $data['state'] = 0;
                    $data['begstate'] = 0;
                    $data['atime'] = time();
                    $data['uid'] = session('home_user')['id']; 
                    // 执行添加
                    if ($pets->add($data) > 0) {
                        $this->success('添加成功', U('index'));
                    } else {
                        $this->error('添加失败');
                    }   
                } else {
                    $this->error('请上传图片');
                }
                
            }
        }
    }

    public function list()
    {
        $apply = M('info')->field('i.id, i.name, i.sex, i.ctime, i.picname, i.descr, i.state, i.begstate, t.name tname')->table('pet_info i, pet_type t')->where('i.uid='.session('home_user.id').' and t.id=i.typeid')->order('i.id desc')->select();
        // var_dump($apply);die;
        $count = M('info')->table('pet_info i, pet_type t')->where('i.uid='.session('home_user.id').' and t.id=i.typeid')->count();

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
        $this->assign('apply',$apply);
        $this->display();
    }

    public function ok()
    {
        if (IS_AJAX) {
            $data['state'] = 1;
            $info = M('info')->where('id='.I('post.id'))->save($data);
            if ($info == false) {
                $this->ajaxReturn(false);
                
            } else {
                $this->ajaxReturn(true);
            }
        }
    }
}