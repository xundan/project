<?php

/*网页授权获取用户id*/
$code=$_GET['code'];//微信服务器返回的code
$state=$_GET['state'];//用户带进来的参数
echo "".$state."<br/>";
// 获取用户的access_token及openid，access_token有效期7200s，失效后可以用refresh_token调微信api刷新
$getTokenUrl="https://api.weixin.qq.com/sns/oauth2/access_token?appid=wxd946ab56ca4d238a&secret=04409ec97b1c1d2013367ad771e86669&code=".$code."&grant_type=authorization_code";
// echo getOutput($getTokenUrl);
$openIDArr= json_decode(getOutput($getTokenUrl),true);
print_r($openIDArr);
$userToken = $openIDArr['access_token'];
$openID=$openIDArr['openid'];
// scope为snsapi_userinfo模式时，拉取用户更多信息
$userUrl="https://api.weixin.qq.com/sns/userinfo?access_token=".$userToken."&openid=".openID."&lang=zh_CN";
echo "<br/>";
$userInfo = getOutput($userUrl);
$userInfoArr = json_decode($userInfo, true);
print_r($userInfoArr);

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

?>