<?php
/**
*
* 版权所有：恰维网络<qwadmin.qiawei.com>
* 作    者：寒川<hanchuan@qiawei.com>
* 日    期：2016-09-20
* 版    本：1.0.0
* 功能说明：文章控制器。
*
**/

namespace Qwadmin\Controller;
use Think\Controller;
class HomeController extends Controller {
	public function my_profile_update()
	{
		//当前登入人id
		$id = $_SESSION['user_info']['uid'] = 1;
		if ($_FILES['headimgurl']['name'])
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
				foreach ($info as $file)
				{
					$temp = '/Public/attached/' .$file['savepath'] . $file['savename'];
					$picPath_head[] = $temp;
				}
			}
		}
		if($_FILES['jiashi_card_pic']['name'])
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
				foreach ($info as $file)
				{
					$temp = '/Public/attached/' .$file['savepath'] . $file['savename'];
					$picPath_jiashi[] = $temp;
				}
			}
		}
		if($_FILES['xingshi_card_pic']['name'])
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
				foreach ($info as $file)
				{
					$temp = '/Public/attached/' .$file['savepath'] . $file['savename'];
					$picPath_xingshi[] = $temp;
				}
			}
		}
		if($_FILES['photo_front']['name'])
		{
			$upload = new \Think\Upload();
			$upload->exts = array('jpg', 'gif', 'png', 'jpeg');
			$upload->rootPath = './Public/attached/';
			$upload->maxSize = 8145728;
			$info = $upload->upload();
			if (!$info)
			{
				$this->error($upload->getError());
			} else
			{
				foreach ($info as $file)
				{
					$temp = '/Public/attached/' .$file['savepath'] . $file['savename'];
					$picPath_front[] = $temp;
				}
			}
		}
		if($_FILES['photo_after']['name'])
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
				foreach ($info as $file)
				{
					$temp = '/Public/attached/' .$file['savepath'] . $file['savename'];
					$picPath_after[] = $temp;
				}
			}
		}
		if($_FILES['photo_left']['name'])
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
				foreach ($info as $file)
				{
					$temp = '/Public/attached/' .$file['savepath'] . $file['savename'];
					$picPath_left[] = $temp;
				}
			}
		}
		if($_FILES['photo_right']['name'])
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
				foreach ($info as $file)
				{
					$temp = '/Public/attached/' .$file['savepath'] . $file['savename'];
					$picPath_right[] = $temp;
				}
			}
		}
		$data['name'] = I('post.name','','strip_tags');
		$data['sex'] = I('post.sex','','strip_tags');
		$data['birthday'] = I('post.birthday','','strip_tags');
		$data['birthday'] = I('post.birthday','','strip_tags');
		$data['id_card'] = I('post.id_card','','strip_tags');
		$data['jiashi_card'] = I('post.jiashi_card','','strip_tags');
		$data['headimgurl'] = $picPath_head;
		$data['jiashi_card_pic'] = $picPath_jiashi;
		$data['gooder_carinfo_type_id'] = I('post.gooder_carinfo_type_id','','strip_tags');
		$tesp['plate_number'] = I('post.type_id','','strip_tags');
		$tesp['xingshi_card_pic'] = $picPath_xingshi;
		$tesp['photo_front'] = $picPath_front;
		$tesp['photo_after'] = $picPath_after;
		$tesp['photo_left'] = $picPath_left;
		$tesp['photo_right'] = $picPath_right;
		$data['district_id'] = I('post.district_id','','strip_tags');
		$data['area_detail'] = I('post.area_detail','','strip_tags');
		$data['work_time'] = I('post.work_time','','strip_tags');
		$data['work_time_start'] = I('post.work_start_time','','strip_tags');
		$data['work_time_end'] = I('post.work_end_time','','strip_tags');
		$data['buy_car_time'] = I('post.buy_car_time','','strip_tags');
		$data['gooder_work_type_id'] = I('post.gooder_work_type_id','','strip_tags');
		$data['gooder_be_department'] = I('post.gooder_be_department','','strip_tags');
		$data['gooder_position'] = I('post.gooder_position','','strip_tags');
		$data['work_description'] = I('post.work_description','','strip_tags');
		//判断用户第一个手机号
		if(isset($_POST['phone1']))
		{
			$list_one['phone_number'] = I('post.phone1','','strip_tags');
			$phone_one = M('phone_number')->add($list_one);
			$list_phone_one = M('phone_number')->order('id desc')->find();
			$info_phone_one['client_id'] = $id;
			$info_phone_one['phone_id'] = $list_phone_one['id'];
			$info_phone_one['record_time'] = time();
			$res_phone_one = M('relation_phone_clients')->add($info_phone_one);
		}
		//判断用户第二个手机号
		if(isset($_POST['phone2']))
		{
			$list_two['phone_number'] = I('post.phone2','','strip_tags');
			$phone_two = M('phone_number')->add($list_two);
			$list_phone_two = M('phone_number')->order('id desc')->find();
			$info_phone_two['client_id'] = $id;
			$info_phone_two['phone_id'] = $list_phone_two['id'];
			$info_phone_two['record_time'] = time();
			$res_phone_one = M('relation_phone_clients')->add($info_phone_two);
		}
		//判断用户第二个手机号
		if(isset($_POST['phone2']))
		{
			$list_two['phone_number'] = I('post.phone2','','strip_tags');
			$phone_two = M('phone_number')->add($list_two);
			$list_phone_two = M('phone_number')->order('id desc')->find();
			$info_phone_two['client_id'] = $id;
			$info_phone_two['phone_id'] = $list_phone_two['id'];
			$info_phone_two['record_time'] = time();
			$res_phone_two = M('relation_phone_clients')->add($info_phone_two);
		}
		//判断用户第三个手机号
		if(isset($_POST['phone3']))
		{
			$list_three['phone_number'] = I('post.phone3','','strip_tags');
			$phone_three = M('phone_number')->add($list_three);
			$list_phone_three = M('phone_number')->order('id desc')->find();
			$info_phone_three['client_id'] = $id;
			$info_phone_three['phone_id'] = $list_phone_three['id'];
			$info_phone_three['record_time'] = time();
			$res_phone_three = M('relation_phone_clients')->add($info_phone_three);
		}

		$temp['uid'] = $id;
		$ast['name'] = I('post.name','','strip_tags');
		$ast['clients_id'] = $id;
		$ast['record_time'] = time();
		$company_info = M('client_company')->add($ast);
		$company_list = M('client_company')->order('id desc')->find();
		$data['company_id'] = $company_list['id'];
		$user_info = M('users')->where($temp)->save($data);
		$asp['client_id'] = $id;
		$tep['plate_number'] = I('post.plate_number','','strip_tags');
		$tep['buy_car_time'] = I('post.buy_car_time','','strip_tags');
		$car_info = M('relation_carinfo_client')->where($asp)->save($tep);
		if($car_info or $company_info){
			echo 123;
		}else{
			echo 456;
		}

	}
	//用户注册提交处理页面
	public function register_action()
	{
		$data['role_id'] = I('post.role_id','','strip_tags');
		$temp['phone_number'] =  I('post.role_id','','strip_tags');
		$list_phone = M('users')->add($data);

	}
	//发布车源信息提交处理页面
	public function send_carinfo_action()
	{
		$clients_id = $_SESSION['user_info']['uid'] = 1;
		$temp['area1'] = I('post.area1','','strip_tags');
		$temp['area2'] = I('post.area2','','strip_tags');
		$temp['send_id'] = $clients_id;
		$temp['loading_time'] = I('post.loading_time','','strip_tags');
		$temp['short_location'] = I('post.short_location','','strip_tags');
		$temp['goods_type_id'] = I('post.goods_type_id','','strip_tags');
		$temp['origin'] = 1;
		$temp['car_resource_descri'] = I('post.car_resource_descri','','strip_tags');
		$temp['sendcar__phone_id'] = I('post.sendcar__phone_id','','strip_tags');
		$temp['publish_time'] = time();
		$temp['sendcar_carinfo_typeid'] = I('post.sendcar_carinfo_typeid','','strip_tags');
		$temp['long'] = I('post.long','','strip_tags');
		$temp['width'] = I('post.width','','strip_tags');
		$temp['height'] = I('post.height','','strip_tags');
		$temp['carrying'] = I('post.carrying','','strip_tags');
		$res = M('messages')->add($temp);
		if($res){
			echo 123;
		}else{
			echo 456;
		}
	}
	//发布货源信息提交处理页面
	public function send_proinfo_action()
	{
		$clients_id = $_SESSION['user_info']['uid'] = 1;
		$temp['area1'] = I('post.area1','','strip_tags');
		$temp['area2'] = I('post.area2','','strip_tags');
		$temp['detail_area1'] = I('post.detail_area','','strip_tags');
		$temp['detail_area2'] = I('post.detail_area','','strip_tags');
		$temp['send_id'] = $clients_id;
		$temp['loading_time'] = I('post.loading_time','','strip_tags');
		$temp['path_price'] = I('post.path_price','','strip_tags');
		$temp['phone'] = I('post.phone','','strip_tags');
		$temp['save_phone'] = I('post.save_phone','','strip_tags');
		$temp['origin'] = 0;
		$data['descri'] = I('post.descri','','strip_tags');
		$data['granularity'] = I('post.granularity','','strip_tags');
		$data['rezhi'] = I('post.rezhi','','strip_tags');
		$data['remark'] = I('post.remark','','strip_tags');
		$temp['publish_time'] = time();
		$web_info = M('product')->add($data);
		$id = M('product')->order('id desc')->find();
		$temp['product_id'] = $id['id'];
		$res_add = M('messages')->add($temp);
		if($res_add or $web_info){
			echo 123;
		}else{
			echo 456;
		}
	}
	//发布求车信息提交处理页面
	public function send_forcar_action()
	{
		$data['area1'] = I('post.area1','','strip_tags');
		$data['area2'] = I('post.area2','','strip_tags');
		$data['detail_area1'] = I('post.detail_area1','','strip_tags');
		$data['detail_area2'] = I('post.detail_area2','','strip_tags');
		$data['coal_quantity'] = I('post.coal_quantity','','strip_tags');
		$data['path_price'] = I('post.path_price','','strip_tags');
		$data['loading_time'] = I('post.loading_time','','strip_tags');
		$data['loading_expense'] = I('post.loading_expense','','strip_tags');
		$data['unloading_expense'] = I('post.unloading_expense','','strip_tags');
		$data['phone'] = I('post.phone','','strip_tags');
		$data['save_phone'] = I('post.save_phone','','strip_tags');
		$data['origin'] = 3;
		$res = M('messages')->add($data);
		if($res){
			echo 123;
		}else{
			echo 456;
		}

	}
	//发布采购信息提交处理页面
	public function send_forpurchase_action()
	{
		$data['origin'] = 2;
		$data['product_type_id'] = I('post.product_type','','strip_tags');
		$data['product_origin'] = I('post.product_origin','','strip_tags');
		$data['rezhi_min'] = I('post.rezhi_min','','strip_tags');
		$data['purchasing_tonnage'] = I('post.purchasing_tonnage','','strip_tags');
		$data['product_min_price'] = I('post.product_min_price','','strip_tags');
		$data['product_max_price'] = I('post.product_max_price','','strip_tags');
		$data['pay_method'] = I('post.pay_method','','strip_tags');
		$res = M('messages')->add($data);
		if($res){
			echo 123;
		}else{
			echo 456;
		}
	}
	//发布供货信息提交处理页面
	public function send_releasesupply_action()
	{
		$temp['rezhi_min'] = I('post.rezhi_min','','strip_tags');
		$temp['total_water'] = I('post.total_water','','strip_tags');
		$temp['ash_content'] = I('post.ash_content','','strip_tags');
		$temp['sulfur'] = I('post.sulfur','','strip_tags');
		$temp['volatile_parts'] = I('post.volatile_parts','','strip_tags');
		$data['goods_validity_period'] = I('post.goods_validity_period','','strip_tags');
		$data['purchasing_tonnage'] = I('post.purchasing_tonnage','','strip_tags');
		$data['rezhi_max'] = I('post.rezhi_max','','strip_tags');
		$data['origin'] = 0;
		$asp['descri'] = I('post.descri','','strip_tags');
		$asp['supply_company'] = I('post.supply_company','','strip_tags');
		$asp['protype_id'] = I('post.protype_id','','strip_tags');
		$asp['pro_price'] = I('post.pro_price','','strip_tags');
		$asp['is_price_open'] = I('post.is_price_open','','strip_tags');
		$asp['remark'] = I('post.remark','','strip_tags');
		$max_detail = M('relation_messages_rezhi_max')->add($temp);
		$list_detail = M('relation_messages_rezhi_max')->order('id desc')->find();
		$data['rezhi_max_detail_id'] = $list_detail['id'];
		$product_detail = M('product')->add($asp);
		$list_pro = M('product')->order('id desc')->find();
		$data['product_id'] = $list_pro['id'];
		$res_mess = M('messages')->add($data);
		if($res_mess or $max_detail or $product_detail){
			echo 123;
		}else{
			echo 456;
		}
	}
	//个人中心设置密码页面
	public function password_action()
	{
		$temp['uid'] = $_SESSION['user_info']['uid']=1;
		$data['password'] = md5(I('post.password','','strip_tags'));
		$res = M('users')->where($temp)->save($data);
		if($res){
			echo 123;
		}else{
			echo 456;
		}
	}
	//个人中心设置密码页面
	/*public function password_action()
	{

	}*/

}