<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/7/18
 * Time: 10:22
 */

namespace Home\Controller;

use Think\Controller;
class UserController extends Controller
{
    public function index(){
        // 如果一个页面（方法）里有两个跳转，则不会执行第二个
//        echo "user.index";
//        $this->redirect('edit','',2,'jump to...');
//        $this->success('success to...',U('User/login'),3);
//        $this->error('error to...',U('User/login'),5);

        //ajax数据返回
//        $this->ajaxReturn(getTestData(),'json');
        $this->ajaxReturn(getTestData(),'xml');
    }

    public function edit(){
//        echo "user.edit";

//        $server = I('server.');
        $server = I('server.COMSPEC');
        dump($server);
    }

    public function login(){
//        echo "user.login";

        $user=I('get.user',null);
        if(!$user){
            $this->error('there\'s no user',U('User/test'),5);
        }elseif($user==='xcl'){
            $this->success('Welcome back!',U('User/index'),3);
        }else{
            $this->error('you are not xcl.',U('User/test'),5);
        }
    }

    public function test(){
        echo "User/login: <a href=\"".U('Home/User/login/user/xcl')."\">".U('Home/User/login/user/xcl')."</a>";
        echo '<hr>';
        echo "User/login: <a href=\"".U('Home/User/login/user/lcx')."\">".U('Home/User/login/user/lcx')."</a>";
        echo '<hr>';
    }
}