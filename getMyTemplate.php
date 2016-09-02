<?php


$sendUrl="https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token=".getToken();
$sendarr=(array)json_decode(getOutput($sendUrl));
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

?>