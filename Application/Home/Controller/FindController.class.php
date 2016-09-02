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
class FindController extends ComController {
    //司机找活页面
    public function driver_find_live()
    {
        $subInfo=I("post.");
        if($subInfo['page']){
            $page=(int)$subInfo['page'];
        }else{
            $page=1;
        }
        if($subInfo['countRow']){
            $countRow=(int)$subInfo['countRow'];
        }else{
            $countRow=10;
        }
        $orderCase=$subInfo['orderCondition']?$subInfo['orderCondition']:"";
        $orderStr="sm.publish_time desc";
        if($orderCase){
            switch($orderCase){
                case 1:
                default:
                    $orderStr="sm.publish_time desc";
                    break;
                case 2:
                    $orderStr="district asc,sm.publish_time desc";
                    break;
            }
        }
        /*
         * 筛选条件
         */
        $whereCondition="1=1";
        $tempWhereConditon=I("post.whereCondition")?I("post.whereCondition"):"";
//        echo $tempWhereConditon;exit;
        if($tempWhereConditon){
            switch($tempWhereConditon){
                case 1:
                    $whereCondition="sm.coal_quantity<100";
                    break;
                case 2:
                    $whereCondition="100<=sm.coal_quantity AND sm.coal_quantity<1000";
                    break;
                case 3:
                    $whereCondition="sm.coal_quantity>1000";
                    break;
                case 5:
                default:
                   $whereCondition="1=1";
            }
        }
        $isAjax=$subInfo['isAjax']?$subInfo['isAjax']:"";//是否是Ajax请求
        $returnArr=array();
        $uid = $_SESSION['user_info']['uid'];
		$role_id = $_SESSION['user_info']['role_id'];
        $districtId=(int)$_SESSION['user_info']['district_id'];
        //排序筛选
        $beginStr=(int)(($page-1)*$countRow);
        $temp['sm.status'] = 0;
        $temp['sm.origin'] = 3;
        $time = time();
        $lists=M()->query(
            "SELECT su.role_id,su.headimgurl,su.phone_number,su.uid,sm.id,sm.area1,sm.area2,sm.detail_area1,sm.detail_area2,sm.coal_quantity,sm.loading_time,sm.publish_time,
(CASE
WHEN sm.area1='' THEN 2
WHEN sm.area1=$districtId THEN 1
WHEN (select pid from su_districts where id=sm.area1)=$districtId then 1
WHEN (select pid from su_districts where id=(select pid from su_districts where id=sm.area1))=$districtId then 1
ELSE 2 END)  AS district
FROM su_messages sm left JOIN su_users su on sm.clients_id=su.uid WHERE sm.status = 0 AND sm.origin = 3 AND sm.deline_time > $time AND $whereCondition ORDER BY $orderStr LIMIT $beginStr,$countRow"
        );
        $sqlStr=M()->getLastSql();
        foreach($lists as &$list){
            if(!empty($list['area1'])){
                $list['area1_str']=$this->getAllAddress($list['area1']);
            }
            if(!empty($list['area2'])){
                $list['area2_str']=$this->getAllAddress($list['area2']);
            }
            if(empty($list['publish_time'])){
                $list['publish_time'] = '暂无时间';
            }
            else{
                $list['publish_time']=date("Y-m-d",$list['publish_time']);
            }
            if(empty($list['loading_time'])){
                $list['loading_time'] = '暂无时间';
            }
            else{
                $list['loading_time']=date("Y-m-d",$list['loading_time']);
            }
			if($list['role_id']==0){
                $list['role_name'] = '车主';
            }
            else{
                $list['role_name'] = '货主';
            }
			$avg = M()->query("select avg(comment_star) as avg from su_orders where clients_id='$uid'");
            $list_str = substr($avg['0']['avg'],0,3);
			$list['avg']=$list_str;
        }
        if(empty($lists)){
            $nextPage=$page;
        }else{
            $nextPage=$page+1;
        }
        if($isAjax){
            $returnArr['status']=1;
            $returnArr['data']=$lists;
            $returnArr['nextPage']=$nextPage;
            $returnArr['orderCase']=$orderCase;
            $returnArr['whereCondition']=$tempWhereConditon;
            $returnArr['sql']=$sqlStr;
            echo jsonEcho($returnArr);exit;
        }
        $this->assign('lists',$lists);
        $this->assign('nextPage',$nextPage);
        $this->assign('tempWhereConditon',$tempWhereConditon);
        $this->assign('orderCase',$orderCase);
        //货物类型查询下拉
        $type = M('product_type')->select();
        $this->assign('role_id',$role_id);
        $this->display();
    }
    //司机找活ajax接单
    public function ajax_order($message)
    {
        $role=$_SESSION['user_info']['role_id'];
        $messageInfo=M("Messages")->where("status=0")->find($message);
        $uid=$_SESSION['user_info']['uid'];
        if($uid==$messageInfo['clients_id']){
            echo 1024;exit;
        }
        if(empty($messageInfo)){
            echo 2048;exit;//消息撤销或不存在
        }
        if($role==0){//车主只能接求车单。
            if($messageInfo['origin']!=3){
                echo 1024;exit;
            }
        }
        if($role==1){//货主不能接求车单
            if($messageInfo['origin']==3){
                echo 1024;exit;
            }
        }
        $temp['message_id'] = $message;
        $temp['clients_id'] = $_SESSION['user_info']['uid'];
        $res = M('orders')->where($temp)->select();
        if(!empty($res))
        {
            echo $result = 0;
        }
        else
        {
            $data['clients_id'] = $_SESSION['user_info']['uid'];
            $data['message_id'] = $message;
            $data['order_num'] = time();
            $data['ctime'] = time();
            $order_info = M('orders')->add($data);
            if($order_info)
            {
                echo $result = 1;
            }
            else
            {
                echo $result = 2;
            }
        }
    }
    //货源找车页面
    public function goods_find_car()
    {
        //分页相关
        $subInfo=I("post.");
        if($subInfo['page']){
            $page=(int)$subInfo['page'];
        }else{
            $page=1;
        }
        if($subInfo['countRow']){
            $countRow=(int)$subInfo['countRow'];
        }else{
            $countRow=10;
        }
        $isAjax=$subInfo['isAjax'];
        $uid = $_SESSION['user_info']['uid'];
        $role_id = $_SESSION['user_info']['role_id'];
        $districtId=(int)$_SESSION['user_info']['district_id'];
        $orderCase=$subInfo['orderCondition']?$subInfo['orderCondition']:"";
        $orderStr="sm.publish_time desc";
        if($orderCase){
            switch($orderCase){
                case 1:
                default:
                    $orderStr="sm.publish_time asc";
                    break;
                case 2:
                    $orderStr="district asc,sm.publish_time desc";
                    break;
            }
        }
        //排序筛选
        $time = time();
        $selectInfo="1=1";
        $tempWhere=I("post.whereOrigin")?I("post.whereOrigin"):"";
        if(!empty($tempWhere)){
            if($tempWhere=="all"){
               $selectInfo="1=1";
            }else{
            $whereCase=I("post.whereOrigin");
            $selectInfo="sm.sendcar_carinfo_typeid = $whereCase";
            }
        }
        $temp['sm.status'] = 0;
        $temp['sm.origin'] = 1;
           $beginStr=(int)(($page-1)*$countRow);
           $lists=M()->query(
               "SELECT srct.car_type,su.role_id,su.headimgurl,su.phone_number,su.uid,sm.id,sm.detail_area1,sm.publish_time,sm.area1,
(CASE
WHEN sm.area1='' THEN 2
WHEN sm.area1=$districtId THEN 1
WHEN (select pid from su_districts where id=sm.area1)=$districtId then 1
WHEN (select pid from su_districts where id=(select pid from su_districts where id=sm.area1))=$districtId then 1
ELSE 2 END)  AS district
FROM su_messages sm left JOIN su_users su on sm.clients_id=su.uid INNER JOIN su_relation_carinfo_type srct on srct.id = sm.sendcar_carinfo_typeid WHERE sm.status = 0 AND sm.origin = 1 AND $selectInfo AND sm.deline_time > $time ORDER BY $orderStr LIMIT $beginStr,$countRow"
           );
        $sql=M()->getLastSql();
        if(empty($lists)){
            $nextPage=$page;
        }else{
            $nextPage=$page+1;
        }
        foreach($lists as &$list){
            if(empty($list['publish_time'])){
                $list['publish_time'] = '暂无时间';
            }
            else{
                $list['publish_time']=date("Y-m-d",$list['publish_time']);
            }
			if($list['role_id']==0){
                $list['role_name'] = '车主';
            }
            else{
                $list['role_name'] = '货主';
            }
			$avg = M()->query("select avg(comment_star) as avg from su_orders where clients_id='$uid'");
            $list_str = substr($avg['0']['avg'],0,3);
			$list['avg']=$list_str;
        }
        if($isAjax){
            $returnArr['status']=1;
            $returnArr['data']=$lists;
            $returnArr['nextPage']=$nextPage;
            $returnArr['orderCase']=$orderCase;
            $returnArr['whereOrigin']=$tempWhere;
            $returnArr['sql']=$sql;
            echo jsonEcho($returnArr);exit;
        }
        $this->assign('lists',$lists);
        //货物类型查询下拉
        $type = M('product_type')->select();
        $this->assign('role_id',$role_id);
        $this->assign('orderCase',$orderCase);
        $this->assign('nextPage',$nextPage);
        $this->assign('nextPage',$nextPage);
        //货源找车车辆类型查询
        $car_type = M('relation_carinfo_type')->select();
        $this->assign('car_type',$car_type);
        $this->assign('tempWhere',$tempWhere);
        //身份判定权限接单电话管理
        $uid = $_SESSION['user_info']['uid'];
        $addd['uid'] = $uid;
        $user_role = M('users')->where($addd)->find();
        $this->assign('user_role',$user_role);
        $this->display();
    }
    //货源找车ajax接单
    public function ajax_order_goods($message)
    {
        $temp['message_id'] = $message;
        $temp['clients_id'] = $_SESSION['user_info']['uid'];
        $res = M('orders')->where($temp)->select();
        if(!empty($res))
        {
            echo $result = 0;
        }
        else
        {
            $data['clients_id'] = $_SESSION['user_info']['uid'];
            $data['message_id'] = $message;
            $data['order_num'] = time();
            $data['ctime'] = time();
            $order_info = M('orders')->add($data);
            if($order_info)
            {
                echo $result = 1;
            }
            else
            {
                echo $result = 2;
            }
        }
    }
}