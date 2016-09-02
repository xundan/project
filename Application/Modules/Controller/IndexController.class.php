<?php
namespace Modules\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){

//        $user=D('User');
//        dump($user->select());
//        dump($user->getDbFields());
//        dump($user->db(1,'DB_2')->table('user')->select()); // 指定数据库和数据表

//        $this->createUser();
//        $this->invalidUsers(11);
//        $this->deleteUser(11);
//        $this->listUsers();
//        $this->showUser(10);

        $userModel=D('User');

//        $userModel->where("invalid_id=0")->select();
        $condition=array(
            'name'=>array('EQ','php'), // 利用表达式查询
            'invalid_id'=>'0',
        );
        $result=$userModel
            ->where($condition)
            ->order('record_time desc')
//            ->limit(2,3)
            ->page(6,10)
//            ->fetchSql(true) 不返回查询结果，返回sql语句
            ->select();
        echo ($userModel->getLastSql());
//        echo $result;

    }

    private function createUser(){
        $userAttribute=array(
            'name'=>'php_test',
            'wx_id'=>'php',
            'email'=>'xxx@xxx.c;om',
            'invalid_id'=>0,
        );
        D('User')->add($userAttribute);
    }

    private function listUsers(){
        dump(D('User')->select());
    }

    private function invalidUsers($userId){
        $userUpdateAttribute = array(
            'id'=>$userId,
            'invalid_id'=>1,
        );
        D('User')->save($userUpdateAttribute);
    }

    private function deleteUser($userId){
        D('User')->delete($userId);
    }

    private function showUser($userId){
        dump(D('User')->find($userId));
    }


    private function waysToCreateModel(){

        $user_model = new \Modules\Model\UserModel();

        $user_m_model = M('User');

        $user_d_model = D('User');

        $empty_model = new \Think\Model();
        $empty_m_model = M();
        $empty_d_model = D();
    }
}