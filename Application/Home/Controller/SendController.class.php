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
class SendController extends ComController {
    //发布求车页面
    public function send_forcar()
    {
        $uid = $_SESSION['user_info']['uid'];
        //省份显示
        $pro_data['pid'] = 0;
        $province = M('districts')->where($pro_data)->select();
        //煤炭分类
        $productTypes=M('Product_type')->select();
        $this->assign('province',$province);
        $this->assign('productTypes',$productTypes);
        //煤的品质查询
        $proQuality = M('product_descri')->select();
        $this->assign('proQuality',$proQuality);
        //煤的粒度查询
        $proGranularity = M('product_granularity')->select();
        $this->assign('proGranularity',$proGranularity);
        //当前登录人手机号
        $phone = M('users')->where("uid='$uid'")->find();
        $this->assign('phone',$phone);
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
       // dump($_POST);
//        echo json_encode($_POST);exit;
//        echo time();exit;
        $area1=I('post.area1','','strip_tags')?I('post.area1','','strip_tags'):"";
        $area2=I('post.area2','','strip_tags')?I('post.area2','','strip_tags'):"";
        $phone=I('post.phone','','strip_tags')?I('post.phone','','strip_tags'):"";
        $save_phone=I('post.save_phone','','strip_tags')?I('post.save_phone','','strip_tags'):"";
        $origin=I('post.origin','','strip_tags')?I('post.origin','','strip_tags'):"";
        $data['clients_id'] = $_SESSION['user_info']['uid'];
        $data['detail_area1'] = I('post.detail_area1','','strip_tags');
        $data['detail_area2'] = I('post.detail_area2','','strip_tags');
//        NEWADD
//        $data['granularity_id'] = I('post.granularity_id','','strip_tags');
//        NEWADDEND
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
        //装车时间判断
        if((time()-$data['loading_time']) > 3600*24){
            $returnArr['status']=0;
            $returnArr['msg']="装车时间不能低于现在时间";
            echo jsonEcho($returnArr);exit;
        }
        if(empty($phone)){
            $returnArr['status']=0;
            $returnArr['msg']="发货人电话不能为空。";
            echo jsonEcho($returnArr);exit;
        }
        if(empty($save_phone)){
            $returnArr['status']=0;
            $returnArr['msg']="收货人电话不能为空。";
            echo jsonEcho($returnArr);exit;
        }
        $data['origin'] = I('post.origin','','strip_tags');
        $data['area1'] = I('post.area1','','strip_tags');
        $data['area2'] = I('post.area2','','strip_tags');
        $data['phone'] = I('post.phone','','strip_tags');
        $data['save_phone'] = I('post.save_phone','','strip_tags');
        $data['coal_quantity'] = I('post.coal_quantity','','strip_tags');
        $product['granularity_id'] = I('post.granularity_id','','strip_tags');
        $rs=M('product')->add($product);
        if($rs){
            $data['product_id'] = $rs;
            $res = M('messages')->add($data);
            if($res){
                $returnArr['status']=1;
                $returnArr['msg']="操作成功";
                echo jsonEcho($returnArr);
            }else{
                $returnArr['status']=0;
                $returnArr['msg']="操作失败";
                echo jsonEcho($returnArr);
                M("product")->delete($rs);
            }
        }else{
            $returnArr['status']=0;
            $returnArr['msg']="新增记录失败。";
            echo jsonEcho($returnArr);exit;
        }
    }
    //发布货源信息提交处理页面
    public function send_proinfo_action()
    {
        $subInfo=I("post.","",'strip_tags');
//        dump($subInfo);exit;
//        dump($_POST['granularity_id']);exit;
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
        $data['descri_id']=$subInfo['descri_id'];//煤炭品质
        //NEWADD
        $data['granularity_id']=$subInfo['granularity_id'];//煤炭粒度
        //NEWADDEND
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
            $ins['product_id'] = $rs;
            $ins['area1'] = $subInfo['area1'];//产地
            $ins['purchasing_tonnage'] = $subInfo['purchasing_tonnage'];
            $ins['clients_id'] = $_SESSION['user_info']['uid'];
            $ins['remark'] = $subInfo['remark'];
            $ins['rezhi_max'] = $subInfo['rezhi_max'];//高位热值
            $ins['rezhi_min'] = $subInfo['rezhi_min'];//低位热值
            $ins['valid_time'] = $this->getDelineTime();//有效期
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
            $ins['product_type_id']=$subInfo['protype_id'];//煤炭种类
            $ins['caigou_area']=$subInfo['place_origin_id'];//产地
            $ins['area1']=$subInfo['place_origin_id'];//产地存到地区字段方便排序
            $ins['rezhi_min']=$subInfo['rezhi_min'];
            $ins['purchasing_tonnage']=$subInfo['purchasing_tonnage'];
            $ins['clients_id']=$_SESSION['user_info']['uid'];
            $ins['remark']=$subInfo['remark'];
            $ins['product_min_price']=$subInfo['product_min_price'];
            $ins['product_max_price']=$subInfo['product_max_price'];
            $ins['deline_time'] = $this->getDelineTime();
            $ins['publish_time'] = time();
            $ins['origin'] = $subInfo['origin'];
            $product['protype_id'] = $subInfo['protype_id'];
            $product['descri_id'] = $subInfo['descri_id'];
            $product['granularity_id'] = $subInfo['granularity_id'];
//            dump($product['protype_id']);exit;
            $rs=M('product')->add($product);
            if($rs){
                $ins['product_id'] = $rs;
                $res_add = M('messages')->add($ins);//新增到信息表
                if($res_add){
                    $returnArr['status']=1;
                    $returnArr['msg']="发布成功。";
                    echo jsonEcho($returnArr);
                }else{
                    $returnArr['status']=0;
                    $returnArr['msg']="发布失败。";
                    echo jsonEcho($returnArr);
                }
            }else{
                $returnArr['status']=0;
                $returnArr['msg']="新增记录失败。";
                echo jsonEcho($returnArr);
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
        $user_info = $_SESSION['user_info'];
		if($user_info['role_id'] == 0){
			$user_info['role_name'] = '车主';
		}else{
			$user_info['role_name'] = '货主';
		}
        $asp['uid'] = $user_info['uid'];
        $xianshi = M('users')->where($asp)->find();
        $this->assign('xianshi',$xianshi);
        $this->assign('user_info',$user_info);
        $this->display();
    }
    //我的评级页面
    public function my_rate()
    {
        $user_info = $_SESSION['user_info'];
        $uid = $user_info['uid'];
        $data['uid'] = $uid;
        $list = M('users')->where($data)->field('headimgurl')->select();
        foreach($list as &$lists)
        {
            $list = M()->query("select avg(so.comment_star) as avg from su_orders so join su_messages sm on so.message_id = sm.id where sm.clients_id='$uid' ");
            $list_str = substr($list['0']['avg'],0,3);
            $lists['avg'] = $list_str;
            $ress = M()->query("select count(so.id) as five from su_orders so join su_messages sm on so.message_id = sm.id where sm.clients_id = '$uid' and so.five_str = '1'");
            $lists['five'] = $ress[0]['five'];
            $count = M()->query("select count(so.id) as count from su_orders so join su_messages sm on so.message_id = sm.id where sm.clients_id = '$uid'");
            $lists['count'] = $count[0]['count'];
            $comment_men = M()->query("select count(so.id) as count from su_orders so join su_messages sm on so.message_id = sm.id where sm.clients_id = '$uid' and so.official_comment_id!='' or comment_star!='' or comment!=''");
            $lists['comment_men'] = $comment_men[0]['count'];
        }
        //评论循环显示
        $comment = M('orders_official_comment')->select();
        $this->assign('comment',$comment);
        $this->assign('list',$lists);
        $this->display();
    }
    //我的推荐页面
    public function my_recommend()
    {
        $this->display();
    }
    /*
     * 推荐好友二维码页面
     */
    public function qrcode(){
        $uid=$_SESSION['user_info']['uid'];
        $userModel=M("Users");
        $userInfo=$userModel->find($uid);
        if(empty($userInfo)){
            $this->error("用户不存在。");
        }
        if(empty($userInfo["qrcode"])){
            //邀请码
            if(empty($userInfo['invitation_code'])){
                $userInfo['invitation_code']=creatRandCode("Users","invitation_code");
                $userModel->save(array("uid"=>$uid,'invitation_code'=>$userInfo['invitation_code']));
            }
            //二维码
            vendor("qrcode.echoCode");
            $userInfo['qrcode']=echoCode(C("URL")."?invitation_code=".$userInfo['invitation_code']);
            $userModel->save(array("uid"=>$uid,'qrcode'=>$userInfo['qrcode']));
        }
        $this->assign("qrcode",$userInfo['qrcode']);
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
        //省份显示
        $pro_data['pid'] = 0;
        $province = M('districts')->where($pro_data)->select();
        $this->assign('province',$province);
        //行业类型查询
        $work_type = M('gooder_work_type')->select();
        $this->assign('work_type',$work_type);
        //邀请人查询
        $uid = $_SESSION['user_info']['uid'];
        $temp['uid'] = $uid;
        $invite = M('users')->table('su_users su')->where($temp)->field('scc.name as name2,su.gooder_work_type_id,su.phone_number,su.invitation_code,su.birthday,su.name,su.district_id,su.area_detail,su.sex,su.headimgurl,su.work_time,su.work_description,su.gooder_position,su.gooder_be_department')
            ->join('su_client_company scc on scc.id = su.company_id',left)
            ->find();
//        dump($invite);exit;
        $asp['clients_id'] = $invite['invitation_code'];
        $invite_people = M('users')->where($asp)->find();
        $name = $invite_people['name'];
        $this->assign('name',$name);

        //头像单独显示
        $this->assign('invite',$invite);
        $this->display();
    }
    //登陆退出方法
    public function quit_do(){
        unset($_SESSION['user_info']);
        if(empty($_SESSION['user_info']))
        {
            $this->success('退出成功',U('login_yzm'));
        }
        else
        {
            $this->error('退出失败',U('#'));
        }
    }
    //车主资料页面
    public function car_data()
    {
		//车辆类型查询
		$car_type = M('relation_carinfo_type')->select();
		$this->assign('car_type',$car_type);
        //省份显示
        $pro_data['pid'] = 0;
        $province = M('districts')->where($pro_data)->select();
        $this->assign('province',$province);

        //邀请人查询
        $uid = $_SESSION['user_info']['uid'];
        $temp['su.uid'] = $uid;
        $invite =  M('users')->table('su_users su')->where($temp)->field('su.car_id,srcc.plate_number,srcc.photo_front,srcc.photo_after,srcc.photo_left,srcc.photo_right,su.id_card,su.jiashi_card,su.gooder_work_type_id,su.invitation_code,su.birthday,su.name,su.district_id,su.area_detail,su.sex,su.headimgurl,su.work_time,su.work_description,su.gooder_position,su.gooder_be_department,su.phone_number,su.car_undertake_weight')
            ->join('su_relation_carinfo_client srcc on srcc.id = su.car_id')
            ->find();
        $carInfo=M('relation_carinfo_client')->where(array("id"=>$invite['car_id']))->find();
        $this->assign('carInfo',$carInfo);
        $asp['clients_id'] = $invite['invitation_code'];
        $invite_people = M('users')->where($asp)->find();
        $name = $invite_people['name'];
        $this->assign('name',$name);

        //头像单独显示
        $this->assign('invite',$invite);
        $this->display();
    }
    //车主基本资料提交处理页面
    public function car_data_do()
    {
        $uid = $_SESSION['user_info']['uid'];
        if (is_empty($_FILES))
        {
            $upload = new \Think\Upload();
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg');
            $upload->rootPath = './Public/attached/';
            $upload->maxSize = 8145728;
            $info = $upload->upload();
            if (!$info)
            {
                $this->error($upload->getError());
            }
            else
            {
                foreach ($info as $key=>$file)
                {
                    if($key=="jiashi_card_pic"){//驾驶
                       $temp = '/Public/attached/' .$file['savepath'] . $file['savename'];
                       $subInfo['jiashi_card_pic']= $temp;
                    }
                    if($key=="headimgurl"){//头像
                       $temp = '/Public/attached/' .$file['savepath'] . $file['savename'];
                       $subInfo['headimgurl']= $temp;
                    }
                    if($key=="xingshi_card_pic"){//行驶
                       $temp = '/Public/attached/' .$file['savepath'] . $file['savename'];
                       $car['xingshi_card_pic']= $temp;
                    }
                    if($key=="photo_front"){//行车照前
                        $temp = '/Public/attached/' .$file['savepath'] . $file['savename'];
                        $car['photo_front']= $temp;
                    }
                    if($key=="photo_after"){//行车照后
                        $temp = '/Public/attached/' .$file['savepath'] . $file['savename'];
                        $car['photo_after']= $temp;
                    }
                    if($key=="photo_left"){//行车照左
                        $temp = '/Public/attached/' .$file['savepath'] . $file['savename'];
                        $car['photo_left']= $temp;
                    }
                    if($key=="photo_right"){//行车照右
                        $temp = '/Public/attached/' .$file['savepath'] . $file['savename'];
                        $car['photo_right']= $temp;
                    }
                }
            }
        }
        $aspp['uid'] = $uid;
        $xianshi = M('users')->where($aspp)->find();
        $carr['id'] = $xianshi['car_id'];
        $car_info = M('relation_carinfo_client')->where($carr)->find();
        if($_POST['name']=="")
        {
           $subInfo['name'] =  $xianshi['name'];
        }
        else
        {
            $subInfo['name'] = I('post.name','','strip_tags');
        }
        $subInfo['district_id'] = I('post.dizhi','','strip_tags');
        if($_POST['sex']=="")
        {
            $subInfo['sex'] =  $xianshi['sex'];
        }
        else
        {
            $subInfo['sex'] = I('post.sex','','strip_tags');
        }
        if($_POST['birthday']=="")
        {
            $subInfo['birthday'] =  $xianshi['birthday'];
        }
        else
        {
            $subInfo['birthday'] = strtotime(I('post.birthday','','strip_tags'));
        }
        if($_POST['id_card']=="")
        {
            $subInfo['id_card'] =  $xianshi['id_card'];
        }
        else
        {
            $subInfo['id_card'] = I('post.id_card','','strip_tags');
        }
        if($_POST['jiashi_card']=="")
        {
            $subInfo['jiashi_card'] =  $xianshi['jiashi_card'];
        }
        else
        {
            $subInfo['jiashi_card'] = I('post.jiashi_card','','strip_tags');
        }
        if($_POST['type_id']=="")
        {
            $car['type_id'] =  $car_info['type_id'];
        }
        else
        {
            $car['type_id'] = I('post.type_id','','strip_tags');
        }
        if($_POST['plate_number']=="")
        {
            $car['plate_number'] =  $car_info['plate_number'];
        }
        else
        {
            $car['plate_number'] = I('post.plate_number','','strip_tags');
        }
//        NEWADD
        if($_POST['car_undertake_weight']=="")
        {
            $car['car_undertake_weight'] = $car_info['car_undertake_weight'];
            $subInfo['car_undertake_weight'] = $xianshi['car_undertake_weight'];
        }
        else
        {
            $car['car_undertake_weight'] = I('post.car_undertake_weight','','strip_tags');
            $subInfo['car_undertake_weight'] = I('post.car_undertake_weight','','strip_tags');
        }
//        NEWADDEND
        if(!empty($_POST['phone1']))
        {
            $phone1['phone_number'] = I('post.phone1','','strip_tags');
            $phone_one = M('relation_phone_number')->add($phone1);
            $phone_one_res = M('relation_phone_number')->order('id desc')->find();
            $phoness_one['client_id'] = $uid;
            $phoness_one['phone_id'] = $phone_one_res['id'];
            $phoness_one['record_time'] = time();
            $phones_one = M('relation_phone_clients')->add($phoness_one);
        }
        if(!empty($_POST['phone2']))
        {
            $phone2['phone_number'] = I('post.phone2','','strip_tags');
            $phone_two = M('relation_phone_number')->add($phone2);
            $phone_two_res = M('relation_phone_number')->order('id desc')->find();
            $phoness_two['client_id'] = $uid;
            $phoness_two['phone_id'] = $phone_two_res['id'];
            $phoness_two['record_time'] = time();
            $phones_two = M('relation_phone_clients')->add($phoness_two);
        }
        if(!empty($_POST['phone3']))
        {
            $phone3['phone_number'] = I('post.phone1','','strip_tags');
            $phone_three = M('relation_phone_number')->add($phone3);
            $phone_three_res = M('relation_phone_number')->order('id desc')->find();
            $phoness_three['client_id'] = $uid;
            $phoness_three['phone_id'] = $phone_three_res['id'];
            $phoness_three['record_time'] = time();
            $phones_three = M('relation_phone_clients')->add($phoness_three);
        }
        if(!empty($_POST['phone4']))
        {
            $phone4['phone_number'] = I('post.phone4','','strip_tags');
            $phone_four = M('relation_phone_number')->add($phone4);
            $phone_four_res = M('relation_phone_number')->order('id desc')->find();
            $phoness_four['client_id'] = $uid;
            $phoness_four['phone_id'] = $phone_four_res['id'];
            $phoness_four['record_time'] = time();
            $phones_four = M('relation_phone_clients')->add($phoness_four);
        }
		$car_add = M('relation_carinfo_client')->add($car);
		if($car_add)
		{
			$car_id = M('relation_carinfo_client')->order('id desc')->find();
            $subInfo['car_id'] = $car_id['id'];
            $temp_user['uid'] = $uid;
			$user_add = M('users')->where($temp_user)->save($subInfo);
//            dump($subInfo);exit;
			if($user_add)
			{
				$this->success('资料修改成功',U('vip_center'));
			}
			else
			{
				$this->error('资料修改失败',U('vip_center'));
			}
		}
		
    }
    //车主从业资料提交处理页面
    public function car_data_do_do()
    {
        $uid = $_SESSION['user_info']['uid'];
        $aspp['uid'] = $uid;
        $xianshi = M('users')->where($aspp)->find();
        if(empty($_POST['work_time']))
        {
            $data['work_time'] = $xianshi['work_time'];
        }
        else
        {
            $data['work_time'] = I('post.work_time','','strip_tags');
        }
        if(empty($_POST['work_time_start']))
        {
            $data['work_time_start'] = $xianshi['work_time_start'];
        }
        else
        {
            $data['work_time_start'] = strtotime(I('post.work_time_start','','strip_tags'));
        }
        if(empty($_POST['work_time_end']))
        {
            $data['work_time_end'] = $xianshi['work_time_end'];
        }
        else
        {
            $data['work_time_end'] = strtotime(I('post.work_time_end','','strip_tags'));
        }
        if(empty($_POST['buy_car_time']))
        {
            $data['buy_car_time'] = $xianshi['buy_car_time'];
        }
        else
        {
            $data['buy_car_time'] = strtotime(I('post.buy_car_time','','strip_tags'));
        }
        if(empty($_POST['work_description']))
        {
            $data['work_description'] = $xianshi['work_description'];
        }
        else
        {
            $data['work_description'] = strtotime(I('post.work_description','','strip_tags'));
        }
        $work_info = M('users')->where($aspp)->save($data);
        if($work_info)
        {
            $this->success('资料修改成功',U('vip_center'));
        }
        else
        {
            $this->success('资料修改失败',U('vip_center'));
        }
    }
    //货主基本资料处理页面
    public function owner_data_do()
    {
//         dump($_POST);exit;
        $uid = $_SESSION['user_info']['uid'];
        $aspp['uid'] = $uid;
        $xianshi = M('users')->where($aspp)->find();
//        dump($xianshi);exit;
        if (is_empty($_FILES))
        {
            $upload = new \Think\Upload();
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg');
            $upload->rootPath = './Public/attached/';
            $upload->maxSize = 8145728;
            $info = $upload->upload();
            if (!$info)
            {
                $this->error($upload->getError());
            }
            else
            {
                foreach ($info as $key=>$file)
                {
                    if($key=="headimgurl"){//驾驶
                        $temp = '/Public/attached/' .$file['savepath'] . $file['savename'];
                        $data['headimgurl']= $temp;
                    }
                }
            }
        }
        if(!empty($_POST['phone1']))
        {
            $phone1['phone_number'] = I('post.phone1','','strip_tags');
            $phone_one = M('relation_phone_number')->add($phone1);
            $phone_one_res = M('relation_phone_number')->order('id desc')->find();
            $phoness_one['client_id'] = $uid;
            $phoness_one['phone_id'] = $phone_one_res['id'];
            $phoness_one['record_time'] = time();
            $phones_one = M('relation_phone_clients')->add($phoness_one);
        }
        if(!empty($_POST['phone2']))
        {
            $phone2['phone_number'] = I('post.phone2','','strip_tags');
            $phone_two = M('relation_phone_number')->add($phone2);
            $phone_two_res = M('relation_phone_number')->order('id desc')->find();
            $phoness_two['client_id'] = $uid;
            $phoness_two['phone_id'] = $phone_two_res['id'];
            $phoness_two['record_time'] = time();
            $phones_two = M('relation_phone_clients')->add($phoness_two);
        }
        if(!empty($_POST['phone3']))
        {
            $phone3['phone_number'] = I('post.phone1','','strip_tags');
            $phone_three = M('relation_phone_number')->add($phone3);
            $phone_three_res = M('relation_phone_number')->order('id desc')->find();
            $phoness_three['client_id'] = $uid;
            $phoness_three['phone_id'] = $phone_three_res['id'];
            $phoness_three['record_time'] = time();
            $phones_three = M('relation_phone_clients')->add($phoness_three);
        }
        if(!empty($_POST['phone4']))
        {
            $phone4['phone_number'] = I('post.phone4','','strip_tags');
            $phone_four = M('relation_phone_number')->add($phone4);
            $phone_four_res = M('relation_phone_number')->order('id desc')->find();
            $phoness_four['client_id'] = $uid;
            $phoness_four['phone_id'] = $phone_four_res['id'];
            $phoness_four['record_time'] = time();
            $phones_four = M('relation_phone_clients')->add($phoness_four);
        }
        if(empty($_POST['sex']))
        {
            $data['sex'] = $xianshi['sex'];
        }
        else
        {
            $data['sex'] = I('post.sex','','strip_tags');
        }
        if(empty($_POST['dizhi']))
        {
            $data['district_id'] = $xianshi['district_id'];
        }
        else
        {
            $data['district_id'] = I('post.dizhi','','strip_tags');
        }
        $data['district_id'] = I('post.district_id','','strip_tags');
        if(empty($_POST['name']))
        {
            $data['name'] = $xianshi['name'];
        }
        else
        {
            $data['name'] = I('post.name','','strip_tags');
        }
        if(empty($_POST['area_detail']))
        {
            $data['area_detail'] = $xianshi['area_detail'];
        }
        else
        {
            $data['area_detail'] = I('post.area_detail','','strip_tags');
        }
        if($_POST['birthday']=="")
        {
            $data['birthday'] =  $xianshi['birthday'];
        }
        else
        {
            $data['birthday'] = strtotime(I('post.birthday','','strip_tags'));
        }
        $data['record_time'] = time();
        $temp_user['uid'] = $uid;
        $user_add = M('users')->where($temp_user)->save($data);
//        dump($user_add);exit;
        if($user_add)
        {
            $this->success('资料修改成功',U('vip_center'));
        }
        else
        {
            $this->error('资料修改失败',U('vip_center'));
        }
    }
    //货主从业资料处理页面
    public function owner_data_do_do()
    {
//         dump($_POST);exit;
        $uid = $_SESSION['user_info']['uid'];
        $aspp['uid'] = $uid;
        $xianshi = M('users')->where($aspp)->find();
        if(empty($_POST['work_time']))
        {
            $data['work_time'] = $xianshi['work_time'];
        }
        else
        {
            $data['work_time'] = I('post.work_time','','strip_tags');
        }
        if(empty($_POST['work_time_start']))
        {
            $data['work_time_start'] = $xianshi['work_time_start'];
        }
        else
        {
            $data['work_time_start'] = strtotime(I('post.work_time_start','','strip_tags'));
        }
        if(empty($_POST['work_time_end']))
        {
            $data['work_time_end'] = $xianshi['work_time_end'];
        }
        else
        {
            $data['work_time_end'] = strtotime(I('post.work_time_end','','strip_tags'));
        }
        if(empty($_POST['gooder_work_type_id']))
        {
            $data['gooder_work_type_id'] = $xianshi['gooder_work_type_id'];
        }
        else
        {
            $data['gooder_work_type_id'] = I('post.gooder_work_type_id','','strip_tags');
        }
        if(empty($_POST['gooder_be_department']))
        {
            $data['gooder_be_department'] = $xianshi['gooder_be_department'];
        }
        else
        {
            $data['gooder_be_department'] = I('post.gooder_be_department','','strip_tags');
        }
        if(empty($_POST['gooder_position']))
        {
            $data['gooder_position'] = $xianshi['gooder_position'];
        }
        else
        {
            $data['gooder_position'] = I('post.gooder_position','','strip_tags');
        }
        if(empty($_POST['work_description']))
        {
            $data['work_description'] = $xianshi['work_description'];
        }
        else
        {
            $data['work_description'] = I('post.work_description','','strip_tags');
        }
        if(empty($_POST['company']))
        {

            $data['company_id'] = $xianshi['company_id'];
        }
        else
        {
            $company = I('post.plate_number','','strip_tags');
            $web['name'] = $company;
            $company_info = M('client_company')->where($web)->find();
            if(!empty($company_info))
            {
                $data['company_id'] = $company_info['id'];
            }
            else
            {
                $tep['name'] = I('post.company','','strip_tags');
                $tep['record_time'] = time();
                $res = M('client_company')->add($tep);
                $find_com = M('client_company')->order('id desc')->find();
                $data['company_id'] = $find_com['id'];
            }
        }
        $work_sdf['uid'] = $uid;
        $finally_res = M('users')->where($work_sdf)->save($data);
//        dump($finally_res);exit;
        if($finally_res)
        {
            $this->success('资料修改成功',U('vip_center'));
        }
        else
        {
            $this->error('资料修改失败',U('vip_center'));
        }
    }

}