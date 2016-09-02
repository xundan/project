<?php
/**
*
* 功能说明：买卖查新控制器。
*
**/
namespace Home\Controller;
use Think\Controller;
class InfoController extends ComController {
    /*
     * 信息查询首页
     */
    public function index()
       {
        //订单结构
//           dump(I("post."));exit;
        $messageModel=M("Messages");
        $role=$_SESSION['user_info']['role_id'];
        $districtId=(int)$_SESSION['user_info']['district_id'];
        $map=array();
        $map['m.status']=0;
        $nowTime=time();
        $map['m.deline_time']=array("EGT",$nowTime);
        //分页相关
        $isAjax=I("post.isAjax")?I("post.isAjax"):false;
        if(I("post.page")){
            $page=(int)I("post.page");
        }else{
            $page=1;
        }
        if(I("post.countRow")){
            $countRow=(int)I("post.countRow");
        }else{
            $countRow=10;
        }
        //排序相关
        $orderStr="m.publish_time desc";
        $orderCase=I("post.orderCondition")?I("post.orderCondition"):1;
        switch($orderCase){
            case 1:
            default:
                $orderStr="m.publish_time desc";
                break;
            case 2:
                $orderStr="district asc,m.publish_time desc";
                break;
        }
        //筛选相关
        $whereCase=I("post.whereOrigin")?I("post.whereOrigin"):"";
//        $selectStr="";
        if($whereCase){
            switch($whereCase){
            case "sy":
                $selectStr="1=1";
                break;
            case "gh":
            default:
                if($role==1){//车主不能看货源信息
                    $map['m.origin']=0;
                    $selectStr="m.origin=0";
                }
                break;
          /*  case "cy":
                $map['m.origin']=1;
                $selectStr="m.origin=1";
                break;*/
            case "cg":
                if($role==1){//车主不能看采购信息
                    $map['m.origin']=2;
                    $selectStr="m.origin=2";
                }
                break;
            /*case "qc":
                $map['m.origin']=3;
                $selectStr="m.origin=3";
                break;*/
            }
        }
        $beginStr=(int)(($page-1)*$countRow);
        $authWhere="1=1";
        /*
         * 权限相关
         */
        if($role==0){//车主不可见采购和供货信息，只能看求车信息和车源信息
            $authWhere="(m.origin=1 or m.origin=3)";
        }
        if($whereCase) {
            $lists = M()->query(
                "SELECT u.uid,u.headimgurl,u.role_id,u.name username,u.phone_number,m.*,
(CASE
WHEN m.area1='' THEN 2
WHEN m.area1=$districtId THEN 1
WHEN (select pid from su_districts where id=m.area1)=$districtId then 1
WHEN (select pid from su_districts where id=(select pid from su_districts where id=m.area1))=$districtId then 1
ELSE 2 END)  AS district
FROM su_messages m INNER JOIN su_users u on m.clients_id=u.uid
WHERE m.status = 0 AND m.deline_time >= $nowTime AND $selectStr AND $authWhere
ORDER BY $orderStr LIMIT $beginStr,$countRow"
            );
        }else{
            $lists = M()->query(
                "SELECT u.uid,u.headimgurl,u.role_id,u.name username,u.phone_number,m.*,
(CASE
WHEN m.area1='' THEN 2
WHEN m.area1=$districtId THEN 1
WHEN (select pid from su_districts where id=m.area1)=$districtId then 1
WHEN (select pid from su_districts where id=(select pid from su_districts where id=m.area1))=$districtId then 1
ELSE 2 END)  AS district
FROM su_messages m INNER JOIN su_users u on m.clients_id=u.uid
WHERE m.status = 0 AND m.deline_time >= $nowTime AND $authWhere
ORDER BY $orderStr LIMIT $beginStr,$countRow"
            );
        }
           $sqlStr=M()->getlastsql();
           foreach($lists as &$return){
               $uid = $return['uid'];
               $Dao = M();
               $list = $Dao->query("select avg(comment_star) as avg from su_orders where clients_id='$uid'");
               $list_str = substr($list['0']['avg'],0,3);
               if(empty($list_str)){
                   $return['avg']=0;
               }else{
                   $return['avg'] = $list_str;
               }
           }
        if(empty($lists)){
            $next=$page;
        }else{
            $next=$page+1;
        }
        foreach($lists as &$list){
            if($role==0){
                 if($list['origin']==3){
                     $list['is_allow']=1;
                }else{
                    $list['is_allow']=0;
                }
            }else{
                  if($list['origin']==0||$list['origin']==2||$list['origin']==1){
                     $list['is_allow']=1;
                   }else{
                    $list['is_allow']=0;
                  }
            }
            if($list['role_id']==0){
                $list['role_name']="个人车主";
            }else{
                $list['role_name']="个人货主";
            }
            switch($list['origin']){
                case 2:
                    if(!empty($list['caigou_area'])){
                        $list['area1_str']=$this->getAllAddress($list['caigou_area']);
                    }else{
                        $list['area1_str']="";
                    }
                    break;
                case 0:
                case 1:
                case 3://求车
                    if(!empty($list['area1'])){
                        $list['area1_str']=$this->getAllAddress($list['area1']);
                    }else{
                        $list['area1_str']="";
                    }
                    break;
            }
            $list['publish_time']=date("Y-m-d",$list['publish_time']);
            $list['deline_time']=date("Y-m-d",$list['deline_time']);
        }
       // dump($lists);
        if($isAjax){
            $returnArr=array();
            $returnArr['nextPage']=$next;
            $returnArr['orderCondition']=I("post.orderCondition");
            $returnArr['whereCase']=$whereCase;
            $returnArr['data']=$lists;
            $returnArr['sql']=$sqlStr;
            echo json_encode($returnArr,JSON_UNESCAPED_UNICODE);exit;
        }
        $this->assign('index',I("post.index"));
        $this->assign('next',$next);
        $this->assign('orderCondition',I("post.orderCondition"));
        $this->assign('whereCase',$whereCase);
        $this->assign('list',$lists);
        $this->display();
    }
    public function adddd(){
            $ins['clients_id'] = 1;
            $ins['message_id'] = 55;
            $ins['order_num'] = time();
            $ins['ctime'] = time();
            $order_info = M('orders')->add($ins);
        //     echo json_encode($ins);exit;
        // $this->ajax_order_goods(55);
    }
    public function ajax_order_goods($message){
        $returnArr=array();
        if(empty($message)){
            $returnArr['status']=0;
            $returnArr['msg']="参数错误。";
            echo jsonEcho($returnArr);exit;
        }
        $messageInfo=M("Messages")->where("status=0")->find($message);
        if(empty($messageInfo)){
            $returnArr['status']=0;
            $returnArr['msg']="信息不存在。";
            echo jsonEcho($returnArr);exit;
        }
        $role=$_SESSION['user_info']['role_id'];
        $uid=$_SESSION['user_info']['uid'];
        if($uid==$messageInfo['clients_id']){
            $returnArr['status']=0;
            $returnArr['msg']="操作不允许。";
            echo jsonEcho($returnArr);exit;
        }
        if($role==0){//车主
            if($messageInfo['origin']==0||$messageInfo['origin']==2||$messageInfo['origin']==1){//货源信息
                $returnArr['status']=0;
                $returnArr['msg']="您没有接单权限。";
                echo jsonEcho($returnArr);exit;
            }
        }
        if($role==1){//货主
            if($messageInfo['origin']==3){//车信息
                $returnArr['status']=0;
                $returnArr['msg']="您没有接单权限。";
                echo jsonEcho($returnArr);exit;
            }
        }
        $temp['message_id'] = $message;
        $temp['clients_id'] = $_SESSION['user_info']['uid'];
        $res = M('orders')->where($temp)->select();
        if(!empty($res))
        {
                $returnArr['status']=0;
                $returnArr['msg']="您已经接过这个单了。";
                echo jsonEcho($returnArr);exit;
        }
        else
        {
            $ins['clients_id'] = $_SESSION['user_info']['uid'];
            $ins['message_id'] = $message;
            $ins['order_num'] = time();
            $ins['ctime'] = time();
            $order_info = M('orders')->add($ins);
            if($order_info)
            {
                $returnArr['status']=1;
                $returnArr['msg']="接单成功。";
                echo jsonEcho($returnArr);exit;
            }
            else
            {
                $returnArr['status']=0;
                $returnArr['msg']="接单失败。";
                echo jsonEcho($returnArr);exit;
            }
        }
    }
}