<?php
namespace Home\Controller;
use Think\Controller;
class AdoptController extends HomeController 
{
    public function index()
    {
        // 宠物

        $list = M('info')->field('u.name uname,u.picname upic, p.uid, p.descr, p.state, p.ctime, p.id, p.sex, p.picname, p.name, t.name tname')->table('pet_user u, pet_info p, pet_type t')->where('p.uid=u.id and p.state=2 and p.typeid=t.id')->order('p.ctime desc')->page($_GET['p'],6)->select();

        // 分页
        $count = M('info')->table('pet_user u,pet_info p, pet_type t')->where('p.state=2 and p.uid=u.id and p.typeid=t.id')->count();

        $Page = new \Think\Page($count,6);// 实例化分页类 传入总记录数和每页显示的记录数

        $Page->setConfig('first','首页');
        $Page->setConfig('last','尾页');
        $Page->setConfig('prev','');
        $Page->setConfig('next','');
        
        $Page->setConfig('theme','
            <nav class="cc">
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
        $this->assign('lists',$list);


        // 分类
        $type = M('type')->where('pid=0')->select();
        foreach ($type as $k => $v) {
            $type[$k]['se'] = M('type')->where('pid='.$v['id'])->select();
        }

        $this->assign('type',$type);
        $this->display();
    }

   
    //百度分享
    public function baidu()
    {
        $this->display('Adopt/index');
    }

    // 雷人笑话 接口
    public function joke()
    {
        
        $curl = curl_init();
        // var_dump($curl);//得到资源

        // 设置APIKEY url形式
        $apikey = "c7d6375287125e39a083e2c6e9372840";
        //URL 设置
        curl_setopt($curl, CURLOPT_URL, 'http://api.tianapi.com/txapi/joke/?key='.$apikey);
        //将 curl_exec() 获取的信息以 文件流的形式返回,而不是直接输出
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        //curl执行
        $data = curl_exec($curl);
        // var_dump($data);//得到的是一个json字串

        //关闭curl
        curl_close($curl);

        //处理JSON数据
        $jsonObj = json_decode($data);
        //提取文章列表
        $newslist = $jsonObj->newslist;
        // var_dump($newslist[0]);
        $list = $newslist[0];
        $arr['title'] = $list->title;
        $arr['type'] = $list->type;
        $arr['content'] = $list->content;
        $this->assign('list',$arr);
        $this->display();  
    }

    // 脑筋急转弯 接口
    public function word()
    {
        
        $curl = curl_init();
        // var_dump($curl);//得到资源

        // 设置APIKEY url形式
        $apikey = "c7d6375287125e39a083e2c6e9372840";
        //URL 设置
        curl_setopt($curl, CURLOPT_URL, 'http://api.tianapi.com/huabian/?key='.$apikey.'&num=10');
        //将 curl_exec() 获取的信息以 文件流的形式返回,而不是直接输出
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        //curl执行
        $data = curl_exec($curl);
        // var_dump($data);//得到的是一个json字串

        //关闭curl
        curl_close($curl);

        //处理JSON数据
        $jsonObj = json_decode($data);
        //提取文章列表
        $newslist = $jsonObj->newslist;
        
        foreach ($newslist as $k => $v) {
            $arr[$k]['ctime'] =$v->ctime;
            $arr[$k]['title'] =$v->title;
            $arr[$k]['url'] =$v->url;
            $arr[$k]['description'] =$v->description;
            $arr[$k]['picUrl'] =$v->picUrl;

        }
        
        $this->assign('list',$arr);
        $this->display();  
    }


    //机器人接口
    public function robot()
    {
       
        if($_POST){
           
            $ch = curl_init();  
            $apikey = "8e7c1adccc704ee89f6796def9201d8a";
            

            $info = $_POST['info'];
            var_dump($info);
            // $info = urlencode($info);
            // $apikey = "c7d6375287125e39a083e2c6e9372840";
            $url = 'http://www.tuling123.com/openapi/ap/?key='.$aplikey;
            curl_setopt ($ch, CURLOPT_URL, $url);
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
            $file_contents = curl_exec($ch);
            curl_close($ch);
            // $header = array(
            //     'apikey: b848d17197e4ead6adc43f2af62b4ac8',
            // );
            // 添加apikey到header
            // curl_setopt($ch, CURLOPT_HTTPHEADER  , $url);
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            
            // $res = curl_exec($ch);
            $res = json_decode($file_contents,true);
            var_dump($res);
            $data = $res->text_array;
            $news = $data[0]->text;
            var_dump($news);
            $this->assign('news',$news);
         }
         $this->display();
    }

    
    public function weixi()
    {
        $this->display();
    }
    //微信精选接口
    public function wx()
    {
        $apikey = "c7d6375287125e39a083e2c6e9372840";
        $num = 20; //返回数量
        $word = '宠物';
        $url = 'http://api.tianapi.com/wxnew/?key='.$apikey.'&num='.$num.'&rand=1&word='.$word.'&page=1&src=%E4%BA%BA%E6%B0%91%E6%97%A5%E6%8A%A5';     //API接口
        $ch = curl_init();  
        $timeout = 5;
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $file_contents = curl_exec($ch);
        curl_close($ch);

        $res = json_decode($file_contents,true);
        // var_dump($res);
        $infos = $res['newslist'];
        // var_dump($infos);
        $this->assign('infos',$infos);
       
        $this->display('Adopt/wx');
    }


    public function pet()
    {
        if (empty(I('get.id'))) {
            $this->display('Public:404');
            exit;
        }

        $info = M('info')->field('t.name tname, i.id, i.name, i.sex, i.descr, i.state, i.ctime, i.uid, i.picname, u.name uname, u.picname upic, u.bg')->table('pet_type t, pet_info i, pet_user u')->where('i.id='.I('get.id').' and t.id= i.typeid and u.id=i.uid')->find();
        $this->assign('info',$info);
        $this->display();
    }

    public function apply()
    {
        if (empty(I('post.pid'))) {
            $this->display('Public:404');
            exit;
        }
        if (empty(I('post.uid'))) {
            $this->display('Public:404');
            exit;
        }

        if (IS_AJAX) {
            $data['uid'] = I('post.uid');
            $data['pid'] = I('post.pid');
            $data['ctime'] = time();
            // 未获取好友id
            if (empty($data)) {
                $this->ajaxReturn(false);

            }else{
                $apply = M('info_user')->add($data);
            
                if ($apply == false) {
                    $this->ajaxReturn(false);
                } else {
                    $this->ajaxReturn(true);
                }
            }
            
        }

    }
}