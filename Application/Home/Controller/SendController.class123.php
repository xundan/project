<?php
namespace Home\Controller;
use Think\Controller;
header("Content-type: text/html; charset=utf-8"); 
class SendController extends ComController {
    //发布求车页面
    public function send_forcar()
    {
        //省份显示
        $pro_data['pid'] = 0;
        $province = M('districts')->where($pro_data)->select();
        //煤炭分类
        $productTypes=M('Product_type')->select();
        $this->assign('province',$province);
        $this->assign('productTypes',$productTypes);
        $this->display();
    }
    //省市联动ajax查询市
    public function ajax_sheng($pro_id)
    {
        $data['pid'] = $pro_id;
        $shi = M('districts')->where($data)->select();
        if($shi)
        {
            echo json_encode($shi);
        }else{
            echo $shi = 0;
        }
    }
    //发布求车信息提交处理页面
    public function send_forcar_action()
    {
        $area1=I('post.area1','','strip_tags')?I('post.area1','','strip_tags'):"";
        $area2=I('post.area2','','strip_tags')?I('post.area2','','strip_tags'):"";
        $phone=I('post.phone','','strip_tags')?I('post.phone','','strip_tags'):"";
        $save_phone=I('post.save_phone','','strip_tags')?I('post.save_phone','','strip_tags'):"";
        $origin=I('post.origin','','strip_tags')?I('post.origin','','strip_tags'):"";
        $data['clients_id'] = $_SESSION['user_info']['uid'];
        $data['detail_area1'] = I('post.detail_area1','','strip_tags');
        $data['detail_area2'] = I('post.detail_area2','','strip_tags');
        $data['coal_quantity'] = I('post.coal_quantity','','strip_tags');
        $data['path_price'] = I('post.path_price','','strip_tags');
        $data['loading_time'] = strtotime(I('post.loading_time','','strip_tags'));
        $data['unloading_expense'] = strtotime(I('post.unloading_expense','','strip_tags'));
        $data['loading_expense'] = I('post.loading_expense','','strip_tags');
        $data['deline_time'] = $this->getDelineTime();
        $data['publish_time'] = time();
        $returnArr=array();
        if(empty($area1)||empty($area2)){
            $returnArr['status']=0;
            $returnArr['msg']="区域不能为空";
            echo jsonEcho($returnArr);exit;
        }
        if(empty($origin)){
            $returnArr['status']=0;
            $returnArr['msg']="参数不完整";
            echo jsonEcho($returnArr);exit;
        }
        if(empty($phone)){
            $returnArr['status']=0;
            $returnArr['msg']="发货人电话为能为空。";
            echo jsonEcho($returnArr);exit;
        }
        if(empty($save_phone)){
            $returnArr['status']=0;
            $returnArr['msg']="收货人电话为能为空。";
            echo jsonEcho($returnArr);exit;
        }
        $data['origin'] = I('post.origin','','strip_tags');
        $data['area1'] = I('post.area1','','strip_tags');
        $data['area2'] = I('post.area2','','strip_tags');
        $data['phone'] = I('post.phone','','strip_tags');
        $data['save_phone'] = I('post.save_phone','','strip_tags');
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
    //发布货源信息提交处理页面
    public function send_proinfo_action()
    {
        $subInfo=I("post.","",'strip_tags');
        $returnArr=array();
        if(empty($subInfo['area1'])||empty($subInfo['area2'])){
            $returnArr['status']=0;
            $returnArr['msg']="产地或交割地为空。";
            echo jsonEcho($returnArr);exit;
        }
        if(empty($subInfo['price'])){
            $returnArr['status']=0;
            $returnArr['msg']="价格不能为空。";
            echo jsonEcho($returnArr);exit;
        }
        $data['protype_id']=$subInfo['product_type_id'];//煤炭种类
        $data['descri']=$subInfo['type_str_new'];//煤炭品质
        $data['supply_company']=$subInfo['company_supply'];//供货公司
        $data['place_origin_id']=$subInfo['area1'];//产地
        $data['place_delivery_id']=$subInfo['area2'];//交割地
        $data['pro_price']=$subInfo['price'];//产品价格
        $data['is_price_open']=$subInfo['is_open'];//是否公开
        $data['volatile_parts']=$subInfo['volatile_parts'];//挥发分
        $data['sulfur']=$subInfo['sulfur'];//硫分
        $data['ash_content']=$subInfo['ash_content'];//灰分
        $data['total_water']=$subInfo['total_water'];//全水分
        $rs=M("Product")->add($data);//新增到产品表
        if($rs){
            $ins['product_id']=$rs;
            $ins['purchasing_tonnage']=$subInfo['purchasing_tonnage'];
            $ins['clients_id']=$_SESSION['user_info']['uid'];
            $ins['remark']=$subInfo['remark'];
            $ins['rezhi_max']=$subInfo['rezhi_max'];//高位热值
            $ins['rezhi_min']=$subInfo['rezhi_min'];//低位热值
            $ins['valid_time']=strtotime($subInfo['goods_validity_period']);//有效期
            $ins['deline_time'] = $this->getDelineTime();
            $ins['publish_time'] = time();
            $ins['origin'] = $subInfo['origin'];
            $res_add = M('messages')->add($ins);//新增到信息表
            if($res_add){
                $returnArr['status']=1;
                $returnArr['msg']="发布成功。";
                echo jsonEcho($returnArr);
            }else{
                $returnArr['status']=0;
                $returnArr['msg']="发布失败。";
                echo jsonEcho($returnArr);
                M("Product")->delete($rs);
            }
        }else{
            $returnArr['status']=0;
            $returnArr['msg']="新增记录失败。";
            echo jsonEcho($returnArr);exit;
        }
    }
    //发布采购提交处理页面
    public function send_buy_action()
    {
        $subInfo=I("post.","","strip_tags");
        $data['protype_id']=$subInfo['protype_id'];//煤炭种类
        $data['place_origin_id']=$subInfo['place_origin_id'];//产地
        $rs=M("Product")->add($data);//新增到产品表
        if($rs){
            $ins['product_id']=$rs;
            $ins['rezhi_min']=$subInfo['rezhi_min'];
            $ins['purchasing_tonnage']=$subInfo['purchasing_tonnage'];
            $ins['clients_id']=$_SESSION['user_info']['uid'];
            $ins['remark']=$subInfo['remark'];
            $ins['product_min_price']=$subInfo['product_min_price'];
            $ins['product_max_price']=$subInfo['product_max_price'];
            $ins['deline_time'] = $this->getDelineTime();
            $ins['publish_time'] = time();
            $ins['origin'] = $subInfo['origin'];
            $res_add = M('messages')->add($ins);//新增到信息表
            if($res_add){
                $returnArr['status']=1;
                $returnArr['msg']="发布成功。";
                echo jsonEcho($returnArr);
            }else{
                $returnArr['status']=0;
                $returnArr['msg']="发布失败。";
                echo jsonEcho($returnArr);
                M("Product")->delete($rs);
            }
        }else{
            $returnArr['status']=0;
            $returnArr['msg']="新增记录失败。";
            echo jsonEcho($returnArr);exit;
        }
    }
    //设置密码页面
    public function set_password()
    {
        if($_GET['yes']==yes)
        {
            $res_return = 0;
        }else{
            $res_return = 1;
        }
        $this->assign('res_return',$res_return);
        $this->display();
    }
    //设置密码提交处理页面
    public function set_password_action()
    {
        $data['password'] = I('post.password','','strip_tags');
        $data['password'] = md5($data['password']);
        $id = $_SESSION['user_info']['uid'];
        $asp['uid'] = $id;
        $res = M('users')->where($asp)->save($data);
        //echo M()->getlastsql();
        if($res)
        {
            echo "<script>location.href='".__CONTROLLER__."/set_password/yes/yes'</script>";
        }
        else
        {
            echo "<script>location.href='".__CONTROLLER__."/set_password'</script>";
        }
    }
    //个人中心页面
    public function vip_center()
    {
        $role_id = $_SESSION['user_info']['role_id'];
        $this->assign('role_id',$role_id);
        $user_info = $_SESSION['user_info'];
        if($role_id==0)
        {
            $user_info['role_name']='车主';
        }
        else
        {
            $user_info['role_name']='货主';
        }
        $this->assign('user_info',$user_info);
        $this->display();
    }
    //我的推荐页面
    public function my_recommend()
    {
        $this->display();
    }
    //我的推荐ajax实现效果
    public function ajax_recommend()
    {
        $uid = $_SESSION['user_info']['uid'];
        $amp['uid'] = $uid;
        $code_action = M('users')->where($amp)->find();
        if(!empty($code_action))
        {
            echo $res = 2;
        }
        else
        {
            $randStr = str_shuffle('1234567890');
            $str = substr($randStr,0,6);
            $data['invitation_code'] = $str;
            //邀请码不重复
            $asp['invitation_code'] = $str;
            $code = M('users')->where($asp)->find();
            while(!empty($code))
            {
                $str = substr($randStr,0,6);
            }
            $temp['uid'] = $_SESSION['user_info']['uid'];
            $code_res = M('users')->where($temp)->save($data);
            if($code_res)
            {
                echo $res = 0;
            }
            else
            {
                echo $res = 1;
            }
        }

    }
    //我的地址页面
    public function my_area()
    {
        $this->display();
    }
    //我的地址ajax保存地区
    public function ajax_myarea_do($X,$Y)
    {
       echo  123;
    }
    //货主资料页面
    public function owner_data()
    {
        $this->display();
    }
}