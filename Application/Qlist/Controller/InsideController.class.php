<?php
/**
 * Created by PhpStorm.
 * User: 呜呜呜
 * Date: 2016/9/26
 * Time: 16:18
 */

namespace Qlist\Controller;
use Think\Controller;
header("Content-Type:text/html; charset=utf-8");    //中文非乱码
class InsideController extends Controller{
/*    public function vo(){
        $User = M('daily_tasks');
        $list = $User->limit(10)->select();
        $this->assign('list',$list);
    }*/
    public function inside_of_the_site(){
        $username=$_SESSION['cur_user']['username'];
        $this->assign('asd',$username);
        $str='completeted_or_not!=5';
        $id1=M("daily_tasks")->where('completed_or_not<>5')->order('creat_time desc')->select();
//       dump(M("daily_tasks")->getLastSql());exit;
//        dump($id1);exit;
        foreach($id1 as &$temp){
            $temp["completeted_time"]=date("Y-m-d",$temp['completeted_time']);
            $temp["creat_time"]=date("Y-m-d",$temp['creat_time']);
        }
        $this->assign('xijie',$id1);
        $this->show();

    }

    public function tijiaowenti(){
        $daily_tasks["detail"]=$_POST["detail"];
        $username=$_SESSION['cur_user']['username'];
        $daily_tasks["username"]=$username;
        $daily_tasks["creat_time"]=time();
        $daily_tasks["completed_or_not"] = 0;

        $num = M("daily_tasks")->add($daily_tasks);/*dump($num);*/
//        if($detail!=false && !=null){
//            echo"插入成功";
//        }else{
//            echo"插入失败";
//        }
        if($num) {
            $this->success("成功提交问题", U('Inside/inside_of_the_site')); /*控制器/同名方法*/
        }

    }
public function three_buttons(){      /*  dump($_POST);*/
    /*    echo "SUC";
        dump($_POST);
        dump($_GET);
        exit;*/
    if ($_POST['qiehuan']) {
//        echo 1;
        $dijihang["id"] = $_POST['qiehuan'];
        $yuanlaide = M("daily_tasks")->where($dijihang)->find();
        if ($yuanlaide["completed_or_not"] == 1) {
            $yuanlaide["completed_or_not"] = 0;
            $yuanlaide["creat_time"] = time();
        } else {
            $yuanlaide["completed_or_not"] = 1;
            $yuanlaide["completeted_time"] = time();

        }
        $xinde = M("daily_tasks")->where($dijihang)->save($yuanlaide);
//        dump($xinde);exit;
        if ($xinde) {
            $this->success("成功", U('Inside/inside_of_the_site')); /*控制器/同名方法*/
        }
    }
    elseif ($_POST['changes']) {
/*        dump($_POST);
        exit;*/
/*        echo 2;*/
        $dijihang["id"] = $_GET['id'];
        $yuanlaide = M("daily_tasks")->where($dijihang)->find();
        $yuanlaide["detail"]=$_POST['changes'];
        $yuanlaide["creat_time"] = time();
        $xinde = M("daily_tasks")->where($dijihang)->save($yuanlaide);
        if ($xinde) {
            $this->success("成功修改内容", U('Inside/inside_of_the_site'));

        }
        elseif ($_POST['shanchu']) {
            echo "删了";
            $dijihang["id"] = $_POST['shanchu'];

            $yuanlaide = M("daily_tasks")->where($dijihang)->find();
            $yuanlaide["completed_or_not"] = 5;
            $xinde = M("daily_tasks")->where($dijihang)->save($yuanlaide);
            if ($xinde) {
                $this->success("成功", U('Inside/inside_of_the_site')); /*控制器/同名方法*/
            }
        } else {
            dump($_POST);
            exit;
            echo 'postshibai';
        }
//    dump($_POST);exit;

    }
}
public  function  update(){
    echo "update";
    dump($_POST);
}
//    dump($_POST);exit;
    public function shan(){
        echo"删了";
        $dijihang["id"] = $_POST['shanchu'];

        $yuanlaide=M("daily_tasks")->where($dijihang)->find();
/*        if ($yuanlaide["completed_or_not"]==1){
            $yuanlaide["completed_or_not"]=0;
            $yuanlaide["creat_time"]=time();
        }else{
            $yuanlaide["completed_or_not"]=1;
            $yuanlaide["completeted_time"]=time();

        }*/
        $yuanlaide["completed_or_not"]=5;
        $xinde=M("daily_tasks")->where($dijihang)->save($yuanlaide);
        if ($xinde){
            $this->success("成功",U('Inside/inside_of_the_site')); /*控制器/同名方法*/
        }
}
//    public function show_name(){
//        //是否post方式传
//        if(IS_POST){
//            $name = $_POST["username"];
//            $name_in_the_table = D("lxy")->where("username='$name'")->find();
////            dump($staff['password']);
//            $this->assign('username',$name);
//        }
//
////        $_POST["password"] = md5($_POST["password"]);
////        if ($_POST['password'] == $staff['password']) echo "if";
//        //数据库中没有记录
//        if(!$name_in_the_table){
//            $this->error("用户名错误！返回登录",U('Login/Login'));
//        }else       //有记录再检查密码
//            if ($name_in_the_table['password'] != $_POST['password'] and $name_in_the_table['password'] != ""){
//                $this->error("密码错误！返回登录");
//                $this->redirect( 'Login/Login');
//            }else{      //登录记录存到session里，并跳转至审核页面
//                $_SESSION['cur_user']=$name_in_the_table;
//                header("Content-Type:text/html; charset=utf-8");    //中文非乱码
//                $this->redirect( 'Inside/inside_of_the_site',array('id'=>1),2,"您好 ".$name_in_the_table['username']."！ 请稍等，正在跳转。。。");
//            }
//    }

}
