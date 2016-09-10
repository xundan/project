<?php
namespace Home\Controller;
use Think\Controller;
header("Content-type: text/html; charset=utf-8"); 
class IndexController extends ComController {
    //登录成功进去的首页
    public function index()
	{
        //身份判定
        $uid = $_SESSION['user_info']['uid'];
        $temp['uid'] = $uid;
        $user_info = M('users')->where($temp)->find();
        $this->assign('user_info',$user_info);
		$this->display();
    }

}