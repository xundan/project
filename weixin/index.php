<?php

/**

 * JS_API支付

 * ====================================================

 * 在微信浏览器里面打开H5网页中执行JS调起支付。接口输入输出数据格式为JSON。

 * 成功调起支付需要三个步骤：

 * 步骤1：网页授权获取用户openid

 * 步骤2：使用统一支付接口，获取prepay_id

 * 步骤3：使用jsapi调起支付

*/ 

   session_start();   

   header("Content-type:text/html;charset=utf-8");

   $order_id=$_REQUEST['order_id'];

   if(!isset($_REQUEST['order_id'])){

	   // echo "<script>alert('参数错误！');window.location.href='http://".$_SERVER['HTTP_HOST']."'</script>";

	   echo "<script>alert('参数错误！');window.location.href='../'</script>";

	   exit;

   }

   

	include_once("./WxPayPubHelper/WxPayPubHelper.php");

	//使用jsapi接口

	$jsApi = new JsApi_pub();

	//=========步骤1：网页授权获取用户openid============

	//通过code获得openid

	if (!isset($_GET['code']))

	{

		//触发微信返回code码

		$url=urlencode(WxPayConf_pub::JS_API_CALL_URL."?order_id=".$order_id);

		$url = $jsApi->createOauthUrlForCode($url);

		Header("Location: $url"); 

	}else

	{

		//获取code码，以获取openid

	    $code = $_GET['code'];

		$jsApi->setCode($code);

		$openid = $jsApi->getOpenId();

	}



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

	

	//=========步骤2：使用统一支付接口，获取prepay_id============

	//使用统一支付接口

	$unifiedOrder = new UnifiedOrder_pub();

	$unifiedOrder->setParameter("openid","$openid");//商品描述

	$unifiedOrder->setParameter("body","有朋家宴订单");//商品描述

	$unifiedOrder->setParameter("out_trade_no",$order_id);//商户订单号 

	$unifiedOrder->setParameter("total_fee",getOrderTotal($order_id));//总金额

	$unifiedOrder->setParameter("notify_url",WxPayConf_pub::NOTIFY_URL);//通知地址 

	$unifiedOrder->setParameter("trade_type","JSAPI");//交易类型

	//非必填参数，商户可根据实际情况选填

	//$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号  

	//$unifiedOrder->setParameter("device_info","XXXX");//设备号 

	//$unifiedOrder->setParameter("attach","XXXX");//附加数据 

	//$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间

	//$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间 

	//$unifiedOrder->setParameter("goods_tag","XXXX");//商品标记 

	//$unifiedOrder->setParameter("openid","XXXX");//用户标识

	//$unifiedOrder->setParameter("product_id","XXXX");//商品ID



	$prepay_id = $unifiedOrder->getPrepayId();

		

	//=========步骤3：使用jsapi调起支付============

	$jsApi->setPrepayId($prepay_id);



	$jsApiParameters = $jsApi->getParameters();

	

	//获得订单总金额

	function getOrderTotal($order_id){

	  	global $db;

		$sql="select id,cost from fd_order_info where order_sn = ".$order_id." and openid = '".$_SESSION['openid']."' limit 0,1";

		$result=$db->get_one($sql);



		if(empty($result['id'])){

			  echo "<script>alert('该订单无效！');window.location.href='../'</script>";

			  exit;

		}else{
			$total=$result['front_money'];
		}

		return $total*100;

	}

?>



<html>

<head>

    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>

    <title>微信安全支付</title>

	<script type="text/javascript">

		//调用微信JS api 支付

		function jsApiCall()

		{

			WeixinJSBridge.invoke(

				'getBrandWCPayRequest',

				<?php echo $jsApiParameters; ?>,

				function(res){

					if(res.err_msg=='get_brand_wcpay_request:cancel'){

						window.location.href="../index.php"

					}else{

						window.location.href="../index.php"

					}

				}

			);

		}

		function callpay()

		{

			if (typeof WeixinJSBridge == "undefined"){

			    if( document.addEventListener ){

			        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);

			    }else if (document.attachEvent){

			        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 

			        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);

			    }

			}else{

			    jsApiCall();

			}

		}

	    window.onload=callpay();

	</script>

</head>

</html>