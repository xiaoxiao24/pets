<?php
namespace Home\Controller;
use Think\Controller;

/**
* Home/Controller下所有控制器的基类
*
* @author xiao
* @version 1.0
*/
class HomeController extends Controller 
{
    /**
    * 当系统找不到请求的操作名称的时候，系统会尝试定位空操作
    */
    public function _empty($name)
    {
        $this->display('Public:404');
    }
}