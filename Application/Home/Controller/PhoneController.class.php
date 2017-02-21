<?php 

namespace Home\Controller;
use Think\Controller;

    /**
    * 申请吧主 短信验证
    * @author xiao
    */ 

class PhoneController extends Controller{


   protected function shortMessage($tel)
    {
        //生成一个4位的随机数，作为验证码
        $Random = mt_rand(1000,9999);   
        // var_dump($tel);die;
        $this->sendTemplateSMS($tel,array($Random,'1'),'1');//手机号码，替换内容数组，模板ID

        //返回随机数的值
        return $Random;
    }

    /**
      * 发送模板短信
      * @param to 手机号码集合,用英文逗号分开
      * @param datas 内容数据 格式为数组 例如：array('Marry','Alon')，如不需替换请填 null
      * @param $tempId 模板Id,测试应用和未上线应用使用测试模板请填写1，正式应用上线后填写已申请审核通过的模板ID
    */       
    function sendTemplateSMS($to,$datas,$tempId)
    {
        // var_dump($to);die;

        //主帐号,对应开官网发者主账号下的 ACCOUNT SID
        $accountSid= '8a216da8588b296f0158941a3f7105dd';

        //主帐号令牌,对应官网开发者主账号下的 AUTH TOKEN
        $accountToken= 'e8e96945714e435283714faa64d38c5d';

        //应用Id，在官网应用列表中点击应用，对应应用详情中的APP ID
        //在开发调试的时候，可以使用官网自动为您分配的测试Demo的APP ID
        $appId='8a216da8588b296f0158941a3fb705e1';

        //请求地址
        //沙盒环境（用于应用开发调试）：sandboxapp.cloopen.com
        //生产环境（用户应用上线使用）：app.cloopen.com
        $serverIP='sandboxapp.cloopen.com';


        //请求端口，生产环境和沙盒环境一致
        $serverPort='8883';

        //REST版本号，在官网文档REST介绍中获得。
        $softVersion='2013-12-26';

        // 初始化REST SDK
        //global $accountSid,$accountToken,$appId,$serverIP,$serverPort,$softVersion;
        $rest = new \Home\Verdor\Sms\REST($serverIP,$serverPort,$softVersion);
        $rest->setAccount($accountSid,$accountToken);
        $rest->setAppId($appId);

        // 发送模板短信
        //echo "Sending TemplateSMS to $to <br/>";
        $result = $rest->sendTemplateSMS($to,$datas,$tempId);
        if($result == NULL ) {
            $this->error('发送失败');
        }
        if($result->statusCode!=0) {
            //echo "error code :" . $result->statusCode . "<br>";
            echo "error msg :" . $result->statusMsg . "<br>";
            //TODO 添加错误处理逻辑
            $this->error('发送失败11');
        }else{
            //echo "Sendind TemplateSMS success!<br/>";
            // 获取返回信息
            $smsmessage = $result->TemplateSMS;
            //$this->success('短信已发送，请在1分钟内填写');
            //echo "dateCreated:".$smsmessage->dateCreated."<br/>";
            //echo "smsMessageSid:".$smsmessage->smsMessageSid."<br/>";
            //TODO 添加成功处理逻辑
        }
    }

    public function sendSms()
    {
        if (!IS_AJAX) {
            $this->error('发送失败',U('Index/index'));
            exit;
        }

        //正则验证 
        $tel = I('post.val');
        if(!preg_match('/^1[3|4|5|7|8][0-9]\d{8}$/', $tel)){
            $this->ajaxReturn('false');
            exit;
        }else{
            $sms = $this->shortMessage($tel);
            session('sms',$sms);
            $this->ajaxReturn('true');
        }

    }


    public function phoneyzm(){
        $sms = I('post.sms');
        if ($sms == session('sms')) {
            $this->ajaxReturn(true);
        }else{
            $this->ajaxReturn(false);
        }

    }

}