<?php


$data='{
           "touser":"oQoIuwPcoG_fwLMDiJGWdZ4RmGmI",
           "template_id":"qSYtIJbPyojkEs0RkgI_91MgwWXX7XJwXVCkg7H1mR4",
           "url":"http://www.baidu.com/",
           "topcolor":"#FF0000",            
           "data":{
                   "first": {
                       "value":"你好，你收到了新消息！（测试）",
                       "color":"#173177"
                   },
                   "orderMoneySum":{
                       "value":"42.0元",
                       "color":"#173177"
                   },
                   "orderProductName": {
                       "value":"烫烫烫锟斤拷",
                       "color":"#173177"
                   },
                   "Remark":{
                       "value":"我们是超级矿资源！微信号：cjkzy005",
                       "color":"#173177"
                   }
           }
       }';
$sendUrl="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".getToken();
$sendarr=(array)json_decode(postOutput($data,$sendUrl));
// echo $shortarr['short_url'];
print_r($sendarr);

function getToken(){
	/*获取Token*/
	$appid="wxd946ab56ca4d238a";
	$secret="04409ec97b1c1d2013367ad771e86669";
	$url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$secret;
	$output = getOutput($url);
	$token=(array)json_decode($output);
	return $token['access_token'];
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

function postOutput($data, $url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	// curl_setopt($ch, CURLOPT_USERAGENT, "");
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$tmpInfo = curl_exec($ch);
	if (curl_errno($ch)){
		return curl_errno($ch);
	}
	curl_close($ch);
	return $tmpInfo;
}
?>