<?php  
namespace Admin\Controller;
use Think\Controller;

class SystemController extends AdminController
{

     //系统统计表 
    public function index()
    {

        // 统计宠物分类
        $type = M('type')->field('count(*) num')->select();
        // 统计链接
        $link = M('link')->field('ctime')->select();

        $linknum  = $this->num($link);
        $linknum['num'] = count($link);

        // 统计宠物
        $info = M('info')->field('ctime')->select();
        $infonum = $this->num($info);
        $infonum['num'] = count($info);

        // 统计用户

        $user = M('user_role')->field('u.regtime ctime')->table('pet_user u, pet_user_role ur,pet_role r')->where('r.id=ur.rid and u.id=ur.uid and r.name="普通用户"')->select();
        $usernum = $this->num($user);
        $usernum['num'] = count($user);

        // 统计管理员
        $huser =M('user_role')->field('u.regtime ctime')->table('pet_user u, pet_user_role ur,pet_role r')->where('r.id=ur.rid and u.id=ur.uid and r.name="管理员"')->select();

        $husernum = $this->num($huser);
        $husernum['num'] = count($huser);

        // 总数
        $this->assign('type', $type[0]);
        $this->assign('link', $linknum);
        $this->assign('info', $infonum);
        $this->assign('user', $usernum);
        $this->assign('huser', $husernum);

        echo '<hr>';
        // var_dump($link);die;
        $this->display();
    }
    

    // 统计总分类下的宠物数  饼状图
    public function charpie()
    {

        $arr = [];
        $types = M('type')->where('pid=0')->select();

        foreach ($types as $v) {
        
            $arr[$v['id']]['name'] = $v['name'];
            $arr[$v['id']]['rgb'] = rand(5,230).','.rand(5,230).','.rand(5,230);

            // 拼接路径
            $where = $data[0]['path'].$v['id'];
            $array = M('type')->where("path like '%{$where}%'")->select();
            foreach ($array as $vs) {

                // 查询子分类下的宠物数
                $num = M('info')->field('count(id) count')->where('typeid='.$vs['id'])->select();
                // 加上子分类下的宠物数
                $arr[$v['id']]['count'] += $num[0]['count'];
            }      
        }
        
        $this->assign('data',$arr);
        

        $this->display();
    }

    // 计算今天 昨天 本周 上周
    private function num(array $array)
    {
         // 今天  
        $tstart = strtotime('today');
        $tend = strtotime('tomorrow');
        // 昨天 
        $ystart = strtotime('yesterday');
        $yend = strtotime('today');
        // 本周 
        $wstart = strtotime('Monday -1 week');
        $wend = strtotime('Monday');
        // 上周
        $lwstart = strtotime('last Monday -1 week');

        $arr['t']=0;
        $arr['y']=0;
        $arr['w']=0;
        $arr['lw']=0;
        foreach ($array as $va) {
            foreach ($va as $v) {
                if ($v<$tend && $v>=$tstart) {
                    $arr['t'] += 1;
                } elseif ($v<$yend && $v>=$ystart) {
                    $arr['y'] += 1;
                } 
                if ($v<$wend && $v>=$wstart) {
                    $arr['w'] += 1;
                } elseif ($v<$wstart && $v>=$lwstart) {
                    $arr['lw'] += 1;
                }
            } 
        }
        return $arr;
    }

   

}

