<?php 
namespace Home\Controller;
use \Think\Controller;

/**
* WordController 留言板
*
* @author xiao
* @version 1.0
*/
class WordController extends HomeController
{
    public function word()
    {
        if (empty(I('get.id'))) {
            $this->display('Public:404');
            exit;
        }

        $pic_count = M('picture')->where('uid='.I('get.id'))->count();
        // 收藏
        $col_count = M('collection')->where('selfid='.I('get.id'))->count();
        // 动态
        $post_count = M('post')->where('uid='.I('get.id').' and state=1')->count();
        // 好友
        $fans_count = M('fans')->where('selfid='.I('get.id'))->count();

        $this->assign('fans_count', $fans_count);
        $this->assign('post_count', $post_count);
        $this->assign('pic_count', $pic_count);
        $this->assign('col_count', $col_count);

        $info = M('user')->where('id='.I('get.id'))->find();
        $this->assign('info',$info);

        if (empty(session('home_user'))) {
            $follow = "";
        } else {
            $follow = M('fans')->where('selfid='.session('home_user.id').' and uid='.I('get.id'))->find();
        }
        
        $this->assign('follow',$follow);
        $this->display();
        
    }

    /*
		用户留言操作
    */ 
    public function wordsave()
    {   
        if(empty(session('home_user'))){
            $this->error('请先登录', U('Login/index'));
        }

        $data = $_POST;
        
        $post = D("Word"); // 实例化User对象
        if (!$post->create($data)){ 
            $this->error($post->getError());
        }else{
            // 执行添加
            if ($post->add() > 0) {
                $exps = M('User')->where('id='.session('home_user.id'))->find();
                $exp['exp']=10+$exps['exp'];
                M('User')->where('id='.session('home_user.id'))->save($exp);
                $this->redirect('Fans/pic', array('id' => I('post.uid')), 5, '添加成功');
            } else {
                $this->error('添加失败');
            }   
        }
    }

    /*
        留言板
    */ 
    public function index()
    {
        if(empty(session('home_user'))){
            $this->error('请先登录', U('Login/index'));
        }

        if (I('get.status') == 1) {
            $where = 'w.selfid='.session('home_user.id').' and w.uid=u.id';
        } elseif(I('get.status') == 2 or I('get.status') == '') {
            $where = 'w.selfid=u.id and w.uid='.session('home_user.id');
        }
       
        $datas = M('word')->field('u.id, u.name, u.picname, w.content, w.ctime, w.id wid')->table('pet_user u, pet_word w')->where($where)->order('w.ctime desc')->page($_GET['p'],10)->select();

        // 回复
        foreach ($datas as $k => $v) {
            $datas[$k]['r'] = M('reply')->field('u.name, u.picname, r.content, u.id, r.id rid')->table('pet_user u,pet_reply r')->where('r.wid='.$v['wid'].' and r.uid=u.id and r.state=1')->select();
            
        }

        // var_dump($datas);die;
            // 分页
        $count = M('word')->where('uid='.session('home_user.id'))->count();

        $Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数

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
        $this->assign('datas',$datas);
        $this->assign('status',I('get.status'));

        // 相册
        $pic_count = M('picture')->where('uid='.session('home_user.id'))->count();
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
        $this->display();
    }

}