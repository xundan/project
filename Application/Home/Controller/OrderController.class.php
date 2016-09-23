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
class OrderController extends   ComController {
    //车主订单页面
    public function order_car()
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
        $isAjax=$subInfo['isAjax']?$subInfo['isAjax']:"";//是否是Ajax请求
        $returnArr=array();
        $uid = $_SESSION['user_info']['uid'];
        $data['so.clients_id'] = $uid;
        $data['sm.origin'] = 3;
        $order_list = M('orders')->table('su_orders so')->field('so.id,so.ctime,sm.area1,sm.area2,sm.detail_area1,sm.detail_area2,scc.name,sm.purchasing_tonnage,sm.path_price,sm.coal_quantity')->where($data)
            ->join('su_messages sm on sm.id = so.message_id')
            ->join('su_users su on su.uid = sm.clients_id','left')
            ->join('su_client_company scc on scc.id = su.company_id',"LEFT")
            ->order('ctime desc')
            ->limit(($page-1)*$countRow,$countRow)
            ->select();//dump($order_list);
//        echo M()->getlastsql();exit;
        foreach($order_list as &$list){
            if(!empty($list['area1'])){
                $list['area1_str']=$this->getAllAddress($list['area1']);
            }
            if(!empty($list['area2'])){
                $list['area2_str']=$this->getAllAddress($list['area2']);
            }
            $list['ctime']=date("Y-m-d",$list['ctime']);
        }
        if(empty($order_list)){
            $nextPage=$page;
        }else{
            $nextPage=$page+1;
        }
        if($isAjax){
            $returnArr['status']=1;
            $returnArr['data']=$order_list;
            $returnArr['nextPage']=$nextPage;
            echo jsonEcho($returnArr);exit;
        }
        $this->assign('order_list',$order_list);
        $this->assign('nextPage',$nextPage);
        $this->display();
    }
    //货主订单页面
    public function order_goods()
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
        $isAjax=$subInfo['isAjax']?$subInfo['isAjax']:"";//是否是Ajax请求
        $getType=$subInfo['getType']?$subInfo['getType']:"";//请求类型1配货订单2交易订单
        $returnArr=array();
        //配货订单查询
        $uid = $_SESSION['user_info']['uid'];
        $data['so.clients_id'] = $uid;
        $data['so.is_deal'] = array('neq',3);
        $data['sm.origin'] = 1;
        $order_list = M('orders')->table('su_orders so')->field('so.id,so.ctime,sm.area1,sm.area2,sm.detail_area1,sm.detail_area2,sm.loading_time,srct.car_type')->where($data)
            ->join('su_messages sm on sm.id = so.message_id')
            ->join('su_relation_carinfo_type srct on srct.id = sm.sendcar_carinfo_typeid','left')
            ->limit(($page-1)*$countRow,$countRow)
            ->order('ctime desc')
            ->select();
//        dump($order_list);exit;
        $lastSql=M()->getLastSql();
        foreach($order_list as &$lists){
            if(!empty($lists['area1'])){
                $lists['area1_str']=$this->getAllAddress($lists['area1']);
            }
            if(!empty($lists['area2'])){
                $lists['area2_str']=$this->getAllAddress($lists['area2']);
            }
            $lists['ctime']=date("Y-m-d",$lists['ctime']);
			$lists['loading_time']=date("Y-m-d",$lists['loading_time']);
            $returnInfo[] = $lists;
        }//dump($returnInfo);
        if(empty($order_list)){
            $nextPage=$page;
        }else{
            $nextPage=$page+1;
        }
        $this->assign('order_list',$returnInfo);
        if($isAjax==1&&$getType==1){
            $returnArr['status']=1;
            $returnArr['data']=$order_list;
            $returnArr['nextPage']=$nextPage;
            $returnArr['sql']=$lastSql;
            echo jsonEcho($returnArr);
            exit;
        }
        //交易订单查询
        $temp['so.clients_id'] = $uid;
        $temp['sm.origin'] = 0;
        $temp['so.is_deal'] = array('neq',3);
        $temp1['so.clients_id'] = $uid;
        $temp1['sm.origin'] = 2;
        $temp1['so.is_deal'] = array('neq',3);
        $asp[] = $temp;
        $asp[] = $temp1;
        $asp['_logic'] = 'or';
        $order_info = M('orders')->table('su_orders so')->where($asp)->field('so.id,so.ctime,spd.descri,sm.purchasing_tonnage,sm.origin,sm.area1,sm.detail_area1,sptt.type,spt.type as types,spg.granularity')
            ->join('su_messages sm on sm.id = so.message_id','left')
            ->join('su_product sp on sp.id = sm.product_id','left')
            ->join('su_product_descri spd on spd.id = sp.descri_id')
            ->join('su_product_type spt on spt.id = sp.protype_id','left')
            ->join('su_product_type sptt on sptt.id = sm.product_type_id','left')
            ->join('su_product_granularity spg on spg.id = sp.granularity_id')
            ->limit(($page-1)*$countRow,$countRow)
            ->order('so.ctime desc')
            ->select();
        foreach($order_info as &$list){
            if(!empty($list['area1'])){
                $list['area1_str']=$this->getAllAddress($list['area1']);
            }
            if($list['origin']==0){
                $list['typee']=$list['types'];
            }
            else
            {
                $list['typee']=$list['type'];
            }
            $list['ctime']=date("Y-m-d",$list['ctime']);
            $jiaoYi[] = $list;
        }
        $this->assign('jiaoYi',$jiaoYi);
        if(empty($order_info)){
            $nextPage2=$page;
        }else{
            $nextPage2=$page+1;
        }
//        dump($order_info);
        $this->assign('order_info',$order_info);
        $this->assign('nextPage',$nextPage);
        if($isAjax==1&&$getType==2){
            $returnArr['status']=1;
            $returnArr['data']=$order_info;
            $returnArr['nextPage']=$nextPage2;
            echo jsonEcho($returnArr);
            exit;
        }
        $this->display();
    }
    //货主配货订单详情
    public function order_goods_sale()
    {
        //当前登录人id
        $uid = $_SESSION['user_info']['uid'];
        $data['so.id'] = $_GET['ids'];
        $order_lists = M('orders')->table('su_orders so')->where($data)->field('sm.undertake_weight,sm.loading_time,so.id,so.ctime,srct.car_type,sc.name,spt.type,sm.purchasing_tonnage,sm.short_location,sm.clients_id,suu.name as uname,suu.role_id,suu.headimgurl,suu.phone_number')
            ->join('su_messages sm on sm.id = so.message_id')
            ->join('su_users su on su.uid = so.clients_id','left')
            ->join('su_users suu on suu.uid = sm.clients_id','left')
            ->join('su_client_company sc on sc.id = su.company_id','left')
            ->join('su_product_type spt on spt.id = sm.sendcar_goods_typeid','left')
            ->join('su_relation_carinfo_type srct on srct.id = sm.sendcar_carinfo_typeid','left')
            ->order('ctime desc')
            ->select();
        foreach($order_lists as $list){
            if($list['role_id']==0){
                $list['role_name']="个人车主";
            }else{
                $list['role_name']="个人货主";
            }
            if($list['short_location']==0){
                $list['short_location']="是";
            }else{
                $list['short_location']="否";
            }
            if(!empty($list['place_origin_id'])){
                $list['place_origin_id_str']=$this->getAllAddress($list['place_origin_id']);
            }
            $list['ctime']=date("Y-m-d",$list['ctime']);
            if($list['loading_time']){
                $list['loading_time']=date("Y-m-d",$list['loading_time']);
            }else{
                $list['loading_time']='未填写';
            }

        }
        $this->assign('list',$list);
        //订单数量查询
        $uidd = $order_lists[0]['clients_id'];
        $web['clients_id'] = $uidd;
        $order_count = M('orders')->where($web)->count();
        $this->assign('order_count',$order_count);
        //评价选项显示
        $comment_info = M('orders_official_comment')->select();
        //dump($comment_info);
        $this->assign('comment_info',$comment_info);
        //平均星级分计算
        $Dao = M();
        $list = $Dao->query("select avg(comment_star) as avg from su_orders where clients_id='$uidd'");
        $list_str = substr($list['0']['avg'],0,3);
        $this->assign('list_str',$list_str);
        $this->display();
    }
    //在订单详情撤销已经接的单
    public function ajax_message($order_id)
    {
        $where['id'] = $order_id;
        $order['is_deal'] = 3;
        $messages_id = M('orders')->where($where)->save($order);
        if($messages_id)
        {
            echo $res = 0;
        }else{
            echo $res = 1;
        }
    }
    //货主交易订单详情
    public function order_goods_car()
    {
        //当前登录人id
        $uid = $_SESSION['user_info']['uid'];
        $data['so.id'] = $_GET['ids'];
        $order_lists = M('orders')->table('su_orders so')->where($data)->field('sgwt.gooder_work_type,so.id,so.ctime,scc.name as names,su.name,su.role_id,su.phone_number,su.headimgurl,srcc.plate_number,sm.id as idd,sm.caigou_area,sm.short_location,sm.short_location,sm.clients_id,spt.type,sp.place_origin_id')
            ->join('su_messages sm on sm.id = so.message_id','left')
            ->join('su_users su on su.uid = sm.clients_id','left')
            ->join('su_client_company scc on scc.id = su.company_id','left')
            ->join('su_relation_carinfo_client srcc on srcc.id = su.car_id','left')
            ->join('su_product sp on sp.id = sm.product_id','left')
            ->join('su_product_type spt on spt.id = sp.protype_id','left')
            ->join('su_gooder_work_type sgwt on sgwt.id = su.gooder_work_type_id','left')
            ->order('ctime desc')
            ->select();
//        dump($order_lists);
        foreach($order_lists as $list){
            if($list['role_id']==0){
                $list['role_name']="个人车主";
            }else{
                $list['role_name']="个人货主";
            }
            if($list['short_location']==0)
            {
                $list['short_location']='是';
            }else{
                $list['short_location'] = '否';
            }
            if(!empty($list['place_origin_id'])){
                $list['chandi']=$this->getAllAddress($list['place_origin_id']);
            }
            if(!empty($list['caigou_area'])){
                $list['chandi']=$this->getAllAddress($list['caigou_area']);
            }
            $list['ctime']=date("Y-m-d",$list['ctime']);
        }
        $this->assign('list',$list);
//        dump($list);exit;
        //订单数量查询
        $uidd = $order_lists[0]['clients_id'];
        $web['clients_id'] = $uidd;
        $order_count = M('orders')->where($web)->count();
        $this->assign('order_count',$order_count);
        //评价选项显示
        $comment_info = M('orders_official_comment')->select();
        //dump($comment_info);
        $this->assign('comment_info',$comment_info);
        //平均星级分计算
        $Dao = M();
        $list = $Dao->query("select avg(comment_star) as avg from su_orders where clients_id='$uid'");
        $list_str = substr($list['0']['avg'],0,3);
        $this->assign('list_str',$list_str);//dump($list_str);
        $this->display();
    }
    //评论提交方法
    public function order_good_sale_do()
    {
        $subInfo = I("post.","",'strip_tags');
        $returnArr = array();
        if(empty($subInfo['star_info'])){
            $returnArr['status'] = 0;
            $returnArr['msg'] = "您没有评星。";
            echo jsonEcho($returnArr);exit;
        }
        $count_arr = count($_POST['official_comment_id']);
        $res_str = array_filter($_POST['official_comment_id']);
        $aids = implode(',',$res_str);
        $data['id'] = I('post.order_infos','','strip_tags');
        if(!empty($_POST['official_comment_id'])){$web['official_comment_id'] = $aids;}//评价内容id
        $web['comment_star'] = I('post.star_info','','strip_tags');//星级数量
        if(!empty($_POST['comment_content'])){$web['comment'] = I('post.comment_content','','strip_tags');}//评论
        if($_POST['star_info']==5){
            $ass['five_str'] = 1;
            $res_star = M('orders')->where($data)->save($ass);
        }
        $comment_add = M('orders')->where($data)->save($web);
        if($comment_add)
        {
            $returnArr['status']=1;
            $returnArr['msg']="评价成功。";
            echo jsonEcho($returnArr);
        }elseif($comment_add === 0){
            $returnArr['status']=0;
            $returnArr['msg']="请不要重复评价！";
            echo jsonEcho($returnArr);
//            M("orders")->delete($comment_add);
        }else{
            $returnArr['status']=0;
            $returnArr['msg']="评价错误。";
        }
    }
//    public function order_good_sale_do()
//    {
//        $count_arr = count($_POST['official_comment_id']);
//        $res_str = array_filter($_POST['official_comment_id']);
//        print_r(array_filter($_POST['official_comment_id']));
//        $aids = implode(',',$res_str);
//        $data['id'] = I('post.order_infos','','strip_tags');
//        //dump($data);
//        if(!empty($_POST['official_comment_id'])){$web['official_comment_id'] = $aids;}//评价内容id
//        if(!empty($_POST['star_info'])){$web['comment_star'] = I('post.star_info','','strip_tags');}//星级数量
//        if(!empty($_POST['comment'])){$web['comment'] = I('post.comment','','strip_tags');}//评论
//        $comment_add = M('orders')->where($data)->save($web);
//        if($comment_add)
//        {
//            $this->success('评价成功',U('Home/Order/order_car'));
//        }else{
//            $this->error('评价失败');
//        }
//    }
    //车主订单详情评论提交方法
    public function  order_car_comment_do()
    {
        $subInfo = I("post.","",'strip_tags');
        $returnArr = array();
        if(empty($subInfo['star_info'])){
            $returnArr['status'] = 0;
            $returnArr['msg'] = "您没有评星。";
            echo jsonEcho($returnArr);exit;
        }
//        if(empty($subInfo['official_comment_id'])){
//            $returnArr['status'] = 0;
//            $returnArr['msg'] = "您没有选评价项。";
//            echo jsonEcho($returnArr);exit;
//        }
        $count_arr = count($_POST['official_comment_id']);
        $res_str = array_filter($_POST['official_comment_id']);
        $aids = implode(',',$res_str);
        $data['id'] = I('post.order_infos','','strip_tags');
        if(!empty($_POST['official_comment_id'])){$web['official_comment_id'] = $aids;}//评价内容id
        $web['comment_star'] = I('post.star_info','','strip_tags');//星级数量
        if(!empty($_POST['comment_content'])){$web['comment'] = I('post.comment_content','','strip_tags');}//评论
//        $orderRecord=M("orders")->where($data)->find();
////        $returnArr['status']=3;
////        $returnArr['msg']=$orderRecord;
////        echo jsonEcho($returnArr);exit;
//        if(!empty($orderRecord)){
//            if($orderRecord['comment_star']==$web['comment_star']
//                &&$orderRecord['official_comment_id']==$aids
//                &&$orderRecord['comment']==$web['comment']
//            ){
//                    $returnArr['status']=1;
//                    $returnArr['msg']="请不要重复评价。";
//                    echo jsonEcho($returnArr);exit;
//            }
//        }
        if($_POST['star_info']==5){
            $ass['five_str'] = 1;
            $res_star = M('orders')->where($data)->save($ass);
        }
        $comment_add = M('orders')->where($data)->save($web);
        if($comment_add)
        {
            $returnArr['status']=1;
            $returnArr['msg']="评价成功。";
            echo jsonEcho($returnArr);
        }elseif($comment_add === 0){
            $returnArr['status']=0;
            $returnArr['msg']="请不要重复评价！";
            echo jsonEcho($returnArr);
//            M("orders")->delete($comment_add);
        }else{
            $returnArr['status']=0;
            $returnArr['msg']="评价错误。";
        }

    }
//    public function order_car_comment_do()
//    {
////        dump($_POST);exit;
//        $count_arr = count($_POST['official_comment_id']);
//        $res_str = array_filter($_POST['official_comment_id']);
//        $aids = implode(',',$res_str);dump($aids);exit;
//        $data['id'] = I('post.order_infos','','strip_tags');
//        if(!empty($_POST['official_comment_id'])){$web['official_comment_id'] = $aids;}//评价内容id
//        if(!empty($_POST['star_info'])){$web['comment_star'] = I('post.star_info','','strip_tags');}//星级数量
//        if(!empty($_POST['comment_content'])){$web['comment'] = I('post.comment_content','','strip_tags');}//评论
//        if($_POST['star_info']==5){$ass['five_str'] = 1;$res_star = M('orders')->where($data)->save($ass);}
//        $comment_add = M('orders')->where($data)->save($web);
//        if($comment_add)
//        {
//            $this->success('评价成功',U('Home/Order/order_car'));
//        }else{
//            $this->error('评价失败');
//        }
//    }
    //货主租车评论提交方法
    public function order_good_sales_do()
    {
        $subInfo = I("post.","",'strip_tags');
        $returnArr = array();
        if(empty($subInfo['star_info'])){
            $returnArr['status'] = 0;
            $returnArr['msg'] = "您没有评星。";
            echo jsonEcho($returnArr);exit;
        }
        $count_arr = count($_POST['official_comment_id']);
        $res_str = array_filter($_POST['official_comment_id']);
        $aids = implode(',',$res_str);
        $data['id'] = I('post.order_infos','','strip_tags');
        if(!empty($_POST['official_comment_id'])){$web['official_comment_id'] = $aids;}//评价内容id
        $web['comment_star'] = I('post.star_info','','strip_tags');//星级数量
        if(!empty($_POST['comment_content'])){$web['comment'] = I('post.comment_content','','strip_tags');}//评论
        if($_POST['star_info']==5){
            $ass['five_str'] = 1;
            $res_star = M('orders')->where($data)->save($ass);
        }
        $comment_add = M('orders')->where($data)->save($web);
        if($comment_add)
        {
            $returnArr['status']=1;
            $returnArr['msg']="评价成功。";
            echo jsonEcho($returnArr);
        }elseif($comment_add === 0){
            $returnArr['status']=0;
            $returnArr['msg']="请不要重复评价！";
            echo jsonEcho($returnArr);
//            M("orders")->delete($comment_add);
        }else{
            $returnArr['status']=0;
            $returnArr['msg']="评价错误。";
        }
    }
//    public function order_good_sales_do()
//    {
//        $count_arr = count($_POST['official_comment_id']);
//        $res_str = array_filter($_POST['official_comment_id']);
//        $aids = implode(',',$res_str);
//        $data['id'] = I('post.order_infos','','strip_tags');
//        if(!empty($_POST['official_comment_id'])){$web['official_comment_id'] = $aids;}
//        if(!empty($_POST['star_info'])){$web['comment_star'] = I('post.star_info','','strip_tags');}
//        if(!empty($_POST['comment'])){$web['comment'] = I('post.comment','','strip_tags');}
//        if($_POST['star_info']==5){$ass['five_str'] = 1;$res_star = M('orders')->where($data)->save($ass);}
//        if($_POST['star_info']==5){$ass['five_str'] = 1;$res_star = M('orders')->where($data)->save($ass);}
//        $comment_add = M('orders')->where($data)->save($web);
//        if($comment_add)
//        {
//            $this->success('评价成功',U('Home/Order/order_goods'));
//        }else{
//            $this->error('评价失败');
//        }
//    }

    //发布车源页面
    public function send_car_source()
    {
        //省份显示
        $pro_data['pid'] = 0;
        $province = M('districts')->where($pro_data)->select();
        //发布车源货物类型查询
        $goods = M('product_type')->select();
        $this->assign('goods',$goods);
        //车辆类型查询
        $car = M('relation_carinfo_type')->select();
        $this->assign('car',$car);
        //用户手机号查询
        $uid = $_SESSION['user_info']['uid'];
        $phoo['uid'] = $uid;
        $phone = M('users')->where($phoo)->find();
        $this->assign('phone',$phone);
//        NEWADD
//        核载吨数查询
        $car_undertake_weight = M('users')->where($phoo)->find();
        $this->assign('weight',$car_undertake_weight);
//        NEWADDEND
        $this->assign("province",$province);
        $temp['client_id'] = $uid;
        $this->display();
    }
    //车主订单详情页面
    public function order_car_car()
    {
        $id = $_GET['ids'];
        $data['so.id'] = $id;
        $order = M('orders')->table('su_orders so')->where($data)->field('so.id,so.ctime,su.name,su.headimgurl,su.role_id,su.phone_number,sm.coal_quantity,sm.area1,sm.area2,sm.detail_area1,sm.clients_id,sm.detail_area2,sm.loading_time')
            ->join('su_messages sm on sm.id = so.message_id')
            ->join('su_users su on su.uid = sm.clients_id')
            ->order('ctime desc')
            ->select();
        foreach($order as &$list){
            if($list['role_id']==0){
                $list['role_name']="个人车主";
            }else{
                $list['role_name']="个人货主";
            }
            if(!empty($list['area1'])){
                $list['area1_str']=$this->getAllAddress($list['area1']);
            }
            if(!empty($list['area2'])){
                $list['area2_str']=$this->getAllAddress($list['area2']);
            }
            if(empty($list['ctime'])){$list['ctime'] = '暂无时间';}
            else{$list['ctime']=date("Y-m-d",$list['ctime']);}
            if(empty($list['loading_time'])){$list['loading_time'] = '暂无时间';}
            else{$list['loading_time']=date("Y-m-d",$list['loading_time']);}
        }
        $uidd = $order[0]['clients_id'];
        //dump($order);
        //评论项查询
        $comment_info = M('orders_official_comment')->select();
        //平均评级的计算
        $Dao = M();
        $list_info = $Dao->query("select avg(comment_star) as avg from su_orders where id='$uidd'");
        $list_str = substr($list_info['0']['avg'],0,3);
        if(empty($list_str))
        {
            $list_str = 3;
        }
        $this->assign('list_str',$list_str);
        $this->assign('comment_info',$comment_info);
        $this->assign('list_str',$list_str);
        $this->assign('order',$order);
        $this->display();
    }
    //发布车源信息处理方法
    public function send_car_source_action()
    {
//        print_r($_POST);exit;
        $uid = $_SESSION['user_info']['uid'];
        $data['clients_id'] = $uid;
        $area1 = I('post.area1','','strip_tags');
        $area2 = I('post.area2','','strip_tags');
        $detail_area1 = I('post.detail_area1','','strip_tags');
        $datail_area2 = I('post.detail_area2','','strip_tags');
        $undertake_weight = I('post.undertake_weight','','strip_tags');
        $phone=I('post.phone','','strip_tags')?I('post.phone','','strip_tags'):"";
        $data['phone'] = I('post.phone','','strip_tags');
        $data['clients_id'] = $_SESSION['user_info']['uid'];
        $data['detail_area1'] = I('post.detail_area1','','strip_tags');
        $data['detail_area2'] = I('post.detail_area2','','strip_tags');
        $data['short_location'] = I('post.short_location','','strip_tags');
        $sendcar_carinfo_typeid = I('post.sendcar_carinfo_typeid','','strip_tags');
        $data['undertake_weight'] = I('post.undertake_weight','','strip_tags');
        $data['origin'] = 1;
        $data['area1'] = $area1;
        $data['area2'] = $area2;
        $data['sendcar_carinfo_typeid'] = I('post.sendcar_carinfo_typeid','','strip_tags');
//        $goods_type_id = I('post.product_type_id','','strip_tags');
//        $data['product_type_id'] = I('post.product_type_id','','strip_tags');
        $loading_time = I('post.loading_time','','strip_tags');
        $data['loading_time'] = strtotime(I('post.loading_time','','strip_tags'));
        $data['car_resource_descri'] = I('post.car_resource_descri','','strip_tags');
        $oldRecord=M("messages")->where($data)->find();
        if(!empty($oldRecord)){
            $returnArr['status']=0;
            $returnArr['msg']="发布信息重复。";
            echo jsonEcho($returnArr);exit;
        }
        $data['deline_time'] = $this->getDelineTime();
        $data['publish_time'] = time();
        $returnArr=array();
        if(empty($detail_area1)||empty($datail_area2)||empty($area1)||empty($area2)){
            $returnArr['status']=0;
            $returnArr['msg']="区域不能为空";
            echo jsonEcho($returnArr);exit;
        }
//        if(empty($loading_time)){
//            $returnArr['status']=0;
//            $returnArr['msg']="装车时间不能为空";
//            echo jsonEcho($returnArr);exit;
//        }
        //装车时间判断
        if(empty($data['loading_time'])){

        }elseif((time()-$data['loading_time']) > 3600*24){
            $returnArr['status']=0;
            $returnArr['msg']="装车时间不能低于现在时间";
            echo jsonEcho($returnArr);exit;
        }
//        if(empty($goods_type_id)){
//            $returnArr['status']=0;
//            $returnArr['msg']="货物类型不能为空。";
//            echo jsonEcho($returnArr);exit;
//        }
        if(empty($sendcar_carinfo_typeid)){
            $returnArr['status']=0;
            $returnArr['msg']="车辆类型不能为空";
            echo jsonEcho($returnArr);exit;
        }
//        NEWADD
        if(empty($undertake_weight)){
            $returnArr['status']=0;
            $returnArr['msg']="核载吨数不能为空";
            echo jsonEcho($returnArr);exit;
        }
//        NEWADDEND
        if(empty($phone)){
            $returnArr['status']=0;
            $returnArr['msg']="电话不能为空。";
            echo jsonEcho($returnArr);exit;
        }
        $res = M('messages')->add($data);
        if($res){
            $returnArr['status']=1;
            $returnArr['msg']="操作成功";
        }else{
            $returnArr['status']=0;
            $returnArr['msg']="操作失败";
        }
        echo jsonEcho($returnArr);
    }
}