<?php  
namespace Admin\Controller;
use Think\Controller;

/**
* PetsController 宠物操作
*
* @author xiao
* @version 1.0
*/
class PetsController extends AdminController
{
    protected $petsoption='and i.begstate>=0';//创建宠物请求分类查询

    /**
    * 显示宠物列表
    * @access public        
    * @return void
    */
    public function index()
    {
        $user = D('pets');
        $user = $user->field('u.name uname, t.name tname, p.id, p.name, p.descr, p.picname, p.atime, p.state, p.sex')->table('pet_info p, pet_type t, pet_user u')->where('p.typeid=t.id and p.uid=u.id and p.state!=0')->select();
        $this->assign('title','宠物管理');
        $this->assign('part','宠物列表');
        $this->assign('user',$user);
        $this->display();
    }

    /**
    * 宠物已领养
    * @access public        
    * @return void
    */
    public function stop()
    {
        if (IS_AJAX) {
            $data['state'] = 1;
            $user = M('info')->where('id='.I('post.id'))->save($data);
            if ($user == false) {
                echo '启动已领养失败';
                exit;
            }
        }
        
    }

    /**
    * 宠物可领养
    * @access public        
    * @return void
    */
    public function start()
    {
        if (IS_AJAX) {
            $data['state'] = 2;
            $user = M('info')->where('id='.I('post.id'))->save($data);
            if ($user == false) {
                echo '启用可领养失败';
                exit;
            }
        }
        
    }


    /**
    * 创建宠物页面
    * @access public        
    * @return void
    */
    public function addView()
    {
        $type = D('Type');
        $list = $type->getAdminCate();
        $this->assign('list',$list);
        $this->display('Pets:add');
    }

    /**
    * 执行创建宠物
    * @access public        
    * @return void
    */
    public function doAdd()
    {
        //得到数据模型
        $pets = D('pets');
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
                    $data['state'] = I('post.state');
                    $data['begstate'] = 0;
                    $data['ctime'] = time();
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


    /**
    * 宠物查看页面
    * @access public        
    * @return void
    */
    public function show()
    {
        $data = M('info')->where('id='.I('get.id'))->find();
        $this->assign('list', $data);
        $type = M('type')->Field('name')->where('id='.$data['typeid'])->find();
        $this->assign('type',$type);
        $this->display();
    }


    /**
    * 修改宠物信息页面
    * @access public        
    * @return void
    */
    public function editView()
    {
        $list = M('info')->field('u.name uname, t.name tname, i.id, i.name, i.descr, i.picname, i.ctime, i.state, i.sex')->table('pet_info i, pet_type t, pet_user u')->where('i.typeid=t.id and i.uid=u.id and i.id='.I('get.id'))->find();
        $this->assign('list',$list);
        $this->display('Pets:edit');
    }

    /**
    * 执行修改宠物
    * @access public        
    * @return void
    */
    public function save()
    {
        //得到数据模型
        $pets = D('petsEdit');
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
                }
                
                
                $data['descr'] = I('post.descr');
                $data['state'] = I('post.state');
                // 执行添加
                if ($pets->where('id='.I('post.id'))->save($data) > 0) {
                    $this->success('修改成功', U('editView').'?id='.I('post.id'));
                } else {
                    $this->error('修改失败');
                }   
            }
        }
    }

    /**
    * 用户创建宠物请求页面
    * @access public        
    * @return void
    */  
  	public function petsBeg($option='')
    {
        //创建宠物请求分类查询
        if (!empty(I('get.option'))) {
            switch(I('get.option')){
                case 'agreebtn':
                    $this->petsoption='and i.begstate=2';
                    break;
                case 'refusebtn':
                    $this->petsoption='and i.begstate=1';
                    break;
                case 'ingbtn':
                    $this->petsoption='and i.begstate=0';
                    break;
                case 'allbtn':
                    $this->petsoption='and i.begstate>=0';
                    break;
                default:
                    break;
            }
        }
        $data = D('pets');
        $data = $data->field('u.name uname, t.name tname, i.id, i.name, i.descr, i.picname, i.ctime, i.begstate, i.uid, i.sex')->table('pet_info i, pet_type t, pet_user u')->where("i.typeid=t.id and i.uid=u.id $this->petsoption")->select();
        $this->assign('data',$data);
       	$this->assign('title','宠物管理');
        $this->assign('part','创建宠物请求');
        $this->display('Pets:beg-pets');
    }


    /**i
    * 拒绝用户创建宠物要求
    * @access public        
    * @return void
    */
    public function refusePets()
    {
        if (IS_AJAX) {
            $data['begstate'] = 1;
            $user = M('info')->where('id='.I('post.id'))->save($data);
            if ($user == false) {
                $this->ajaxReturn(false);
                
            } else {
                $this->ajaxReturn(true);
            }
        }
        
    }

    /**
    * 同意用户创建宠物要求
    * @access public        
    * @return void
    */
    public function agreePets()
    {
        if (IS_AJAX) {
            
            $data['begstate'] = 2;
            $data['state'] = 2;
            $data['ctime'] = time();

            $user = M('info')->where('id='.I('post.id'))->save($data);
            if ($user == false) {
                $this->ajaxReturn(false);                
            } else {
                $this->ajaxReturn(true);  
            } 
        }
           
    }


}