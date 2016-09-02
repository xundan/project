<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2016/8/17
 * Time: 17:40
 */

namespace Views\Controller;

use Think\Controller;

class StaffsLoginController extends Controller
{
    public function Login()
    {
        $this->display();
    }

    public function doRegister()
    {
        if (IS_POST) {

            $Staff = D('StaffsLogin');

//            $name = $_SESSION['username'];
            $name = $_POST['username'];
            $data['username'] = I('post.username');
            $data['password'] = I('post.password', '', 'strip_tags');
            $data['phone'] = I('post.phone', '', 'strip_tags');
            $data['name'] = I('post.name');
            $data['email'] = I('post.email');
//            dump($name);
            $result = $Staff->where("username='$name'")->find();
//            dump($result);
            if ($result){
                $this->error('用户名已使用,返回注册', 'Register.html', 3);
            }else{
//                $Staff->create()->add($data);
                $Staff->add($data);
//                $this->clearSession();
                //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
                $this->success('注册成功，前往登录界面', 'after_success', 3);
//                            dump($result);
            }
        }
    }

    public function doLogin(){
        //是否post方式传
        if(IS_POST){
            $name = $_POST["username"];
            $staff = D("StaffsLogin")->where("username='$name'")->find();
//            dump($staff['password']);
            $this->assign('username',$name);
        }

//        $_POST["password"] = md5($_POST["password"]);
//        if ($_POST['password'] == $staff['password']) echo "if";
        //数据库中没有记录
        if(!$staff){
            $this->error("用户名错误！返回登录",U('StaffsLogin/Login'));
        }else       //有记录再检查密码
        if ($staff['password'] != $_POST['password'] and $staff['password'] != ""){
            $this->error("密码错误！返回登录");
            $this->redirect( 'StaffsLogin/Login');
        }else{      //登录记录存到session里，并跳转至审核页面
            $_SESSION['cur_user']=$staff;
            header("Content-Type:text/html; charset=utf-8");    //中文非乱码
            $this->redirect( 'DisplayMessages/showDemo',array('id'=>1),2,"您好 ".$staff['username']."！ 请稍等，正在跳转。。。");
        }
    }
    //登出
    public function logout(){
        unset($_SESSION['cur_user']);
        $this->redirect('StaffsLogin/Login');
    }

    public function after_success()
    {
        header("Location: Login.html"); /* 跳转 */
        exit;/* 确保其他php代码不会执行. */
    }

}