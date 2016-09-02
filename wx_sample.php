<?php
/**
  * wechat php test
  */
//define your token
define("TOKEN", "weixin");
$wechatObj = new wechatCallbackapiTest(); 

if($_GET["echostr"]){
    $wechatObj->valid();
}else{
    $wechatObj->responseMsg();
}

class wechatCallbackapiTest
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr)){
                /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
                   the best way is to check the validity of xml by yourself */
                libxml_disable_entity_loader(true);
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
//                $Event = $postObj->Event;
//                $EventKey=$postObj->EventKey;
                $MsgType = $postObj->MsgType;
                $time = time();
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
//                if($Event=="CLICK" and $EventKey=="V1001_TODAY_MUSIC"){
//                    $textTpl = "<xml>
//                            <ToUserName><![CDATA[%s]]></ToUserName>
//                            <FromUserName><![CDATA[%s]]></FromUserName>
//                            <CreateTime>%s</CreateTime>
//                            <MsgType><![CDATA[news]]></MsgType>
//                            <ArticleCount>1</ArticleCount>
//                            <Articles>
//                            <item>
//                            <Title><![CDATA[测试图文]]></Title>
//                            <Description><![CDATA[测试发送图文消息，点击跳转百度]]></Description>
//                            <PicUrl><![CDATA[https://ss0.bdstatic.com/5aV1bjqh_Q23odCf/static/superman/img/logo/bd_logo1_31bdc765.png]]></PicUrl>
//                            <Url><![CDATA[https://www.baidu.com/]]></Url>
//                            </item>
//                            </Articles>
//                            </xml>";
//                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time);
//                    echo $resultStr;
//                }
//                if($Event=="CLICK" and $EventKey=="V1001_GOOD"){
//                   $msgType = "text";
//                    $contentStr = "谢谢你的支持！";
//                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
//                    echo $resultStr;
//                }
                if($MsgType=="image"){

                    $msgType = "text";
                    $contentStr = "老司机~";
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                    echo $resultStr;
                }
//                if($Event=="subscribe"){
//                    if (substr($EventKey, 0, 8)=="qrscene_"){
//                        $msgType = "text";
//                        $contentStr = "欢迎通过二维码关注我们！";
//                        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
//                        echo $resultStr;
//                    }
//                    $msgType = "text";
//                    $contentStr = "欢迎关注全栈开发！";
//                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
//                    echo $resultStr;
//                }
//                if($Event=="SCAN"){
//                    $msgType = "text";
//                    $contentStr = "你扫了二维码";
//                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
//                    echo $resultStr;
//                }
				if(!empty( $keyword ))
                {
                    if($keyword=="百度"){
                         $textTpl = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[news]]></MsgType>
                            <ArticleCount>1</ArticleCount>
                            <Articles>
                            <item>
                            <Title><![CDATA[测试图文]]></Title> 
                            <Description><![CDATA[测试发送图文消息，点击跳转百度]]></Description>
                            <PicUrl><![CDATA[https://ss0.bdstatic.com/5aV1bjqh_Q23odCf/static/superman/img/logo/bd_logo1_31bdc765.png]]></PicUrl>
                            <Url><![CDATA[https://www.baidu.com/]]></Url>
                            </item>
                            </Articles>
                            </xml>";
                        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time);
                        echo $resultStr;
                    }

                     
                    $firstTwoKeyWord = cubstr($keyword,0,6);
//                    $firstTwoKeyWord = mb_substr($keyword, 0,2,"utf8");
                    if($firstTwoKeyWord=="超级"||$firstTwoKeyWord=="超矿"||$firstTwoKeyWord=="讯单"||$firstTwoKeyWord=="主页"||$firstTwoKeyWord=="你好"){
                         $textTpl = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[news]]></MsgType>
                            <ArticleCount>1</ArticleCount>
                            <Articles>
                            <item>
                            <Title><![CDATA[超级矿资源]]></Title>
                            <Description><![CDATA[超级矿资源信息平台]]></Description>
                            <PicUrl><![CDATA[http://www.kuaimei56.com/copyright2.png]]></PicUrl>
                            <Url><![CDATA[http://www.kuaimei56.com/]]></Url>
                            </item>
                            </Articles>
                            </xml>";
                        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time);
                        echo $resultStr;
                    }

                    if ($firstTwoKeyWord=="天气"){
                        $city = cubstr($keyword, 6,21);
                        //TODO:传参给getWeather.php

                        $msgType = "text";
                        $contentStr = getWeather($city);
                        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                        echo $resultStr;
                    }

                    if ($firstTwoKeyWord=="拉取"){
                        $wx = cubstr($keyword, 6,21);
                        $msgType = "text";
                        $contentStr = getMessage($wx);
                        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                        echo $resultStr;
                    }

                    $msgType = "text";
                    $contentStr = "你发送了".$keyword;
//                    $contentStr = "测试".$firstTwoKeyWord;
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                    echo $resultStr;
                }else{
                	echo "Input something...";
                }

        }else {
        	echo "";
        	exit;
        }
    }
		
	private function checkSignature()
	{
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }
        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}

function getWeather($city){

    $urlCityName=urlencode($city);
    $sendUrl="http://v.juhe.cn/weather/index?format=2&cityname=".$urlCityName."&key=cb78c4298bbb832580d7f156a4ddee97";
    $sendarr=(array)json_decode(getOutput($sendUrl));
    $result = (array)$sendarr['result'];
    $sk = (array)$result['sk'];
    $temp = " 当前温度:".$sk['temp'];
    $wind = " 当前风力:".$sk['wind_direction'].$sk['wind_strength'];
    $time = " 当前时间:".$sk['time'];
    $today = (array)$result['today'];
    $temperature = " 今日温度:".$today['temperature'];
    $weather = " 今日天气:".$today['weather'];
    $date_y = " 日期:".$today['date_y'];
    $dressing_advice=" 穿衣指数：".$today['dressing_advice'];
    $res = "".$city.$date_y."\n".$temperature.$weather."\n"."-".$time.$temp.$wind."\n".$dressing_advice;
    return $res;
}

function getMessage($wx){
    $sendUrl="http://www.kuaimei56.com/index.php/Views/PullMessages/pull?wx=".$wx;
    $send_arr=(array)json_decode(getOutput($sendUrl));
    $result = $send_arr['result'];
    if ($result=='OK'){
        $msg = $send_arr['message'];
        if ($msg){
            return $msg;
        }else{
            return $send_arr['reason'];
        }
    }
    return "微信公众平台服务错误，请通知开发人员！";
}


function getOutput($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

function cubstr($string, $beginIndex, $length){
    if(strlen($string) < $length){
        return substr($string, $beginIndex);
    }

    $char = ord($string[$beginIndex + $length - 1]);
    if($char >= 224 && $char <= 239){
        $str = substr($string, $beginIndex, $length - 1);
        return $str;
    }

    $char = ord($string[$beginIndex + $length - 2]);
    if($char >= 224 && $char <= 239){
        $str = substr($string, $beginIndex, $length - 2);
        return $str;
    }

    return substr($string, $beginIndex, $length);
}

?>