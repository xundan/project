<?php
/**
 * Created by PhpStorm.
 * User: 呜呜呜
 * Date: 2016/9/26
 * Time: 11:50
 */

namespace Qlist\Controller;

use Think\Controller;

class LoginController extends Controller
{
    public function login(){
        $this->show();
    }
    public function tijiao(){
        //是否post方式传
        if(IS_POST){
            $name = $_POST["username"];
            $name_in_the_table = D("lxy")->where("username='$name'")->find();
//            dump($staff['password']);
            $this->assign('username',$name);
        }

//        $_POST["password"] = md5($_POST["password"]);
//        if ($_POST['password'] == $staff['password']) echo "if";
        //数据库中没有记录
        if(!$name_in_the_table){
            $this->error("用户名错误！返回登录",U('Login/Login'));
        }else       //有记录再检查密码
            if ($name_in_the_table['password'] != $_POST['password'] and $name_in_the_table['password'] != ""){
                $this->error("密码错误！返回登录");
                $this->redirect( 'Login/Login');
            }else{      //登录记录存到session里，并跳转至审核页面
                $_SESSION['cur_user']=$name_in_the_table;
                header("Content-Type:text/html; charset=utf-8");    //中文非乱码
                $this->redirect( 'Inside/inside_of_the_site',array('id'=>1),2,"您好 ".$name_in_the_table['username']."！ 请稍等，正在跳转。。。");
            }
    }

}