<?php  
namespace Admin\Controller;
use Think\Controller;

class BgController extends AdminController
{
    public function index()
    {
        $this->assign('title', '背景图管理');
        $this->assign('part', '背景图');

        $data = M('bg')->select();
        $this->assign('list', $data);
        $this->assign('num', count($data));
        $this->display();
    }

    
    public function add()
    {
      $this->display();
    }

     /**
    * 执行添加图片
    * @access public        
    * @return void
    */
    public function doadd()
    {
	    // 得到数据
	    $bg=M('bg');
	      
	    if($_FILES['picname']['name']!=''){
	        $config = array (
	            'maxSize' => 3145728,
	            'rootPath' => './Upload/img/bg/',
	            'saveName' => array('uniqid',''),
	            'exts' => array('jpg', 'gif', 'png', 'jpeg'),
	            'autoSub' => true,
	            'subName' => array('date','Ymd'),
	          );
	        $upload = new \Think\Upload($config);// 实例化上传类
	        //上传单个文件
	        $info = $upload->uploadOne($_FILES['picname']);
	        if(!$info) {
	          // 上传错误提示错误信息
	          $this->error($upload->getError());
	        } else {
	          // 上传成功 获取文件信息
	          $path = $info['savepath'].$info['savename'];
	          $image = new \Think\Image();
	          $image->open("./Upload/img/bg/".$path);
	          // 按照原图的比例生成一个最大为350*235的缩略图并保存为thumb.jpg
	          $path = time().$info['savename'];
	          $image->thumb(480, 270, \Think\Image::IMAGE_THUMB_FIXED)->save('./Upload/img/bg-thumb/'.$path);
	        }
	    }

	    if($path != '') {
	        
	        $data['picname'] = $path;

	        // 执行添加
	        if($bg->add($data) >0) {
	          $this->success('添加成功',U('add'));
	        } else {
	          $this->error('添加失败');
	        }
	    } else {
	        $this->error('请上传图片');
	    }
	    
	}

}

