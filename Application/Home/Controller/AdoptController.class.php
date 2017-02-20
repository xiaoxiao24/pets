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
            $info = $_POST['info'];
            $info = urlencode($info);
            // $apikey = "c7d6375287125e39a083e2c6e9372840";
            $url = 'http://apis.baidu.com/turing/turing/turing?key=879a6cb3afb84dbf4fc84a1df2ab7319&info='.$info;
            $header = array(
                'apikey: b848d17197e4ead6adc43f2af62b4ac8',
            );
            // 添加apikey到header
            curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            // 执行HTTP请求
            curl_setopt($ch , CURLOPT_URL , $url);
            $res = curl_exec($ch);
            $res = json_decode($res);
            $data = $res->text_array;
            $news = $data[0]->text;
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
        $ch = curl_init();
        $infos = '随便';
        $infos = urlencode($infos);
        $url = 'http://apis.baidu.com/txapi/weixin/wxhot?num=10&rand=1&word='.$infos.'&page=1&src=%E4%BA%BA%E6%B0%91%E6%97%A5%E6%8A%A5';
        $header = array(
            'apikey: c7d6375287125e39a083e2c6e9372840',
        );
        // 添加apikey到header
        curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 执行HTTP请求
        curl_setopt($ch , CURLOPT_URL , $url);
        $res = curl_exec($ch);
        $res = json_decode($res);
        $infos = $res->newslist;
        // var_dump($info);
        $this->assign('infos',$infos);
       
        $this->display('Adopt/wx');
    }
}