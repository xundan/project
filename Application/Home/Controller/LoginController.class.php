<?php
/**
*
* 版权所有：恰维网络<qwadmin.qiawei.com>
* 作    者：寒川<hanchuan@qiawei.com>
* 日    期：2016-01-21
* 版    本：1.0.0
* 功能说明：前台控制器演示。
*
**/
namespace Home\Controller;
use Think\Controller;
header("Content-type: text/html; charset=utf-8"); 
class LoginController extends Controller {
    //密码登录首页
    public function login_pas()
	{
		$this -> display();
    }
    //手机发送验证码登录页面
    public function login_yzm()
    {
//        dump($_SESSION);
        $this->display();
    }
    //注册页面
    public function register()
    {
        $user_info = $_SESSION['user_info']['role_id'];
        $this->assign('user_info',$user_info);
        $this->display();
    }
    public function randomCode()
    {
        $phone=I("post.phone",'','strip_tags');
        $randStr = str_shuffle('1234567890');
        $str = substr($randStr,0,6);
        session(md5($phone),$str);
        vendor("test");
        sendCode($phone,"'".$str."'");
    }
    /*
     * 注册验证码
     */
    public function randomCodeReg()
    {
        $phone=I("post.phone");
        $randStr = str_shuffle('1234567890');
        $str = substr($randStr,0,6);
        session(md5($phone),$str);
        vendor("test");
        sendCode($phone,"'".$str."'");
        //echo $str." ".$phone;
    }
    public function echoStr(){
        vendor("callback.index");
    }
    public function getInfo(){
        vendor("callback.getInfo");
        $str=getInfo("kdt.items.onsale.get");
    }
    //清空session
    public function clearSession()
    {
        session('randomCode',null);
        session('phone',null);
    }
    //手机验证码判断处理页面
    public function login_yzm_do(){
        if(IS_POST){
            $codes = I('post.code','','strip_tags');
            $phones = I('post.phone','','strip_tags');
            $code = session(md5($phones));
                if($codes==$code)
                {
                    $data['phone_number'] = $phones;
                    $res = M('users')->where($data)->find();
                    $_SESSION['user_info'] = $res;
                    $this->success('登录成功',U('Home/Index/index'));
                }
                else
                {
                    $this->error("验证码填写错误");
                }
        }else{
            $this->error("信息错误");
        }
    }
    //密码登录判断处理页面
    public function login_pas_do(){
        if(IS_POST){
            $phone_number = I('post.phone_number','','strip_tags');
            $data['phone_number'] = $phone_number;
            $res = M('users')->where($data)->find();
            $_SESSION['user_info'] = $res;
            $_SESSION['role_id'] = $res['role_id'];
            echo "<script>location.href='".__MODULE__."/Index/index'</script>";

        }else{
            $this->error("信息错误");
        }
    }
    //ajax验证手机号和密码是否相符
    public function ajax_phone_pas_do($phone_number,$password)
    {
        $data[phone_number] = $phone_number;
        $res = M('users')->where($data)->find();
        if(empty($res))
        {
            echo $ajax_res = 0;
        }
        else
        {
            if($res['password']!=md5($password))
            {
                echo $ajax_res = 2;
            }
            else
            {
                echo $ajax_res = 3;
            }
        }
    }
    //注册表单提交处理页面
    public function register_do()
    {
        $phone_numbers = I('post.phone_number','','strip_tags');
        $clients_id = I('post.invitation_code','','strip_tags')?I('post.invitation_code','','strip_tags'):"";//邀请码
        $code = session(md5($phone_numbers));
        $codes = I('post.code','','strip_tags');
        $returnArr=array();
            if($codes==$code)
            {
                $userModel=M("Users");
                $r=$userModel->where(array("phone_number"=>$phone_numbers))->find();
                if(!empty($r)){//验证用户是否注册
                    $returnArr['status']=0;
                    $returnArr['msg']="该手机号已经注册过。";
                    echo jsonEcho($returnArr);exit;
                }
                $data['phone_number'] = $phone_numbers;
                $data['role_id'] = I('post.type','','strip_tags');
                $data['clients_id'] = $clients_id;
                $res = $userModel->add($data);
                if($res)
                {
                    $returnArr['status']=1;
                    $returnArr['msg']="注册成功。";
                    $tesp['phone_number'] = $phone_numbers;
                    $res = $userModel->where($tesp)->find();
                    $_SESSION['user_info'] = $res;
                    $_SESSION['role_id'] = $res['role_id'];
                    echo jsonEcho($returnArr);exit;
                }else{
                    $returnArr['status']=0;
                    $returnArr['msg']="注册失败。";
                    echo jsonEcho($returnArr);exit;
                }
            }
            else
            {
                //验证码填写错误
                $returnArr['status']=0;
                $returnArr['msg']="验证码错误。";
                echo jsonEcho($returnArr);exit;
            }
    }
}