<?php

/**

 * 通用通知接口demo

 * ====================================================

 * 支付完成后，微信会把相关支付和用户信息发送到商户设定的通知URL，

 * 商户接收回调信息后，根据需要设定相应的处理流程。

 * 

 * 这里举例使用log文件形式记录回调信息。

*/

	include_once("./log_.php");

	include_once("./WxPayPubHelper/WxPayPubHelper.php");
    include_once("./sms.php");
    //$sms=new sms('xuchao750760','857706');



    //使用通用通知接口

	$notify = new Notify_pub();



	//存储微信的回调

	$xml = $GLOBALS['HTTP_RAW_POST_DATA'];	

	$notify->saveData($xml);

	

	//验证签名，并回应微信。

	//对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，

	//微信会通过一定的策略（如30分钟共8次）定期重新发起通知，

	//尽可能提高通知的成功率，但微信不保证通知最终能成功。

	if($notify->checkSign() == FALSE){

		$notify->setReturnParameter("return_code","FAIL");//返回状态码

		$notify->setReturnParameter("return_msg","签名失败");//返回信息

	}else{

		$notify->setReturnParameter("return_code","SUCCESS");//设置返回码

	}

	$returnXml = $notify->returnXml();

	echo $returnXml;

	

	//==商户根据实际情况设置相应的处理流程，此处仅作举例=======

	

	//以log文件形式记录回调信息

	$log_ = new Log_();

	$log_name="./notify_url.log";//log文件路径

	$log_->log_result($log_name,"【接收到的notify通知】:\n".$xml."\n");



	if($notify->checkSign() == TRUE)

	{

		if ($notify->data["return_code"] == "FAIL") {

			//此处应该更新一下订单状态，商户自行增删操作

			$log_->log_result($log_name,"【通信出错】:\n".$xml."\n");

		}

		elseif($notify->data["result_code"] == "FAIL"){

			//此处应该更新一下订单状态，商户自行增删操作

			$log_->log_result($log_name,"【业务出错】:\n".$xml."\n");

		}

		else{

			//此处应该更新一下订单状态，商户自行增删操作

			$log_->log_result($log_name,"【支付成功】:\n".$xml."\n");

			

			include_once("./Db.php");

			$db_config["hostname"] = "localhost"; //服务器地址

			$db_config["username"] = "ypjy"; //数据库用户名

			$db_config["password"] = "yp123456789"; //数据库密码

			$db_config["database"] = "youpeng_db"; //数据库名称

			$db_config["charset"] = "utf8";//数据库编码

			$db_config["pconnect"] = 1;//开启持久连接

			$db_config["log"] = 1;//开启日志

			$db_config["logfilepath"] = './';//开启日志	

			$db=new DB($db_config);		

			

			//更新订单状态

			$order_id=$notify->data["out_trade_no"];

			$res=$db->update("fd_order_info",array("status"=>2),"order_sn=".$order_id);
			//发送短信给客户和商家
			$sms=new \SMS('xuchao750760','857706');//上线后正式修改
			$sql="select * from fd_order_info where order_sn='".$order_id."'";
			$result=$db->get_one($sql);
			if(!empty($result)){
				if($result['dinnertype']=="1"){
					$dinnerName="午餐";
				}
				if($result['dinnertype']=="2"){
					$dinnerName="晚餐";
				}
				/*
                 * 发短信给客户
                 * */
				if($dinnerName=="午餐"){
					$clientMessage="感谢您在我店订餐，您选择的是".$dinnerName.",就餐日期是".$result['dinnertime'].",为提供更好的服务建议您在12：00-14:00之间前来。";
				}
				if($dinnerName=="晚餐"){
					$clientMessage="感谢您在我店订餐，您选择的是".$dinnerName.",就餐日期是".$result['dinnertime'].",为提供更好的服务建议您在18：00-20:00之间前来。";
				}
				$clientMessage=iconv('UTF-8', 'gb2312',$clientMessage);
				$sms->sendSMS($result['tel'],$clientMessage);
				/*
               * 发短信给商家
               * */
				$businessSql="select phone from fd_phone";
				$phones=$db->get_all($businessSql);
				$room=$db->get_one("select name from fd_room where id=".$result['room_id']);
				foreach($phones as $phone){
					$businessContents="客户'".$result['linkman']."'预订了".$result['dinnertime'].",".$room['name']."的".$dinnerName.",手机号为".$result['tel']."。更多详细信息，请在网站后台查看。";
					$businessContents=iconv('UTF-8', 'gb2312',$businessContents);
					$sms->sendSMS($phone['phone'],$businessContents);
				}
			}
			if($res){

				$log_->log_result($log_name,"【支付成功】:支付成功啦啦啦！\n");

			}else{

				$log_->log_result($log_name,"【支付失败】:支付失败了！\n");

			}
		}

		

		//商户自行增加处理流程,

		//例如：更新订单状态

		//例如：数据库操作

		//例如：推送支付完成信息

	}

?>