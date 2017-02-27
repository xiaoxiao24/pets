<?php
namespace Home\Controller;
use Think\Controller;
class TipsController extends HomeController 
{
    public function index()
    {
        $tips = M('tips')->field('id, title')->where('state=1')->order('ctime desc')->page($_GET['p'],10)->select();

        // 分页
        $count = M('tips')->where('state=1')->count();

        $Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数

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
        $this->assign('tips',$tips);

     
        // 新闻接口
        $curl=curl_init();
        // var_dump($curl);

        // 设置APIKEY url 形式
        $apikey="85b64c40553fa3027b064ca0d7e53b7e";
        $word = '宠物';

        // URL设置
        curl_setopt($curl, CURLOPT_URL, 'http://api.tianapi.com/huabian/?key='.$apikey.'&word='.$word.'&num=5');
        // 将curl_exec()获取的信息以文件流的形式返回，而不是直接输出
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // curl 执行
        $datas=curl_exec($curl);
        // var_dump($data);

        // 关闭curl
        curl_close($curl);
        // 处理JSON数据
        $jsonObj=json_decode($datas);
        // 提取文章列表
        $newslist=$jsonObj->newslist;
        // var_dump($newslist);
        foreach ($newslist as $key => $value) {
            $array=[];
            foreach ($value as $k => $v) {
                $array[$k]=$v;
            }
            // dump($arr);
            $c[]=$array;
            // dump($c);
        }

        $this->assign('c',$c);

        // 微信
        $url = 'http://api.tianapi.com/wxnew/?key='.$apikey.'&num=5&rand=1&word='.$word.'&page=1&src=%E4%BA%BA%E6%B0%91%E6%97%A5%E6%8A%A5';     //API接口
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

        $this->display();
    }

   
    //百度分享
    public function baidu()
    {
        $this->display('Tips/detail');
    }

    public function detail()
    {
    	if (empty(I('get.id'))) {
    		$this->display('Public:404');
            exit;
    	}

    	$tips = M('tips')->where('state=1 and id='.I('get.id'))->find();
    	if (empty($tips)) {
    		$this->display('Public:404');
            exit;
    	}

    	$this->assign('detail',$tips);
    	$this->display();
    }

    
}