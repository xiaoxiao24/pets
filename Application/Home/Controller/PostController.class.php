<?php
namespace Home\Controller;
use Think\Controller;
class PostController extends HomeController 
{
    public function index()
    {
    	
    	//动态

        $list = M('post')->field('u.name uname,u.picname upic, p.uid, p.content, p.state, p.ctime')->table('pet_user u, pet_post p')->where('p.uid=u.id and p.state=1')->order('p.ctime desc')->page($_GET['p'],6)->select();

        // 分页
        $count = M('post')->table('pet_user u,pet_post p')->where('p.state=1 and p.uid=u.id')->count();

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

        // 天气
        $ch = curl_init();
        $c= empty($_POST['city'])?'longyan':$_POST['city'];

          // var_dump($c);exit;
        $location = $c;
        $url = 'http://apis.baidu.com/thinkpage/weather_api/suggestion?location='.$location.'&language=zh-Hans&unit=c&start=0&days=3';
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
        // dump($res);
        // exit;
        $data = $res->results;
        
        $city = $data[0]->location->name;
        $daily = $data[0]->daily;
        // dump($daily);
        $update = $data[0]->last_update;
        
        // $linklist=M('link')->select();
        // $this->assign('linklist',$linklist);
        $this->assign('city',$city);
        $this->assign('update',$update);
        $this->assign('daily',$daily);

        $this->display();
    }
}