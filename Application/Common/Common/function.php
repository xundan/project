<?php
/**
*
* 版权所有：恰维网络<qwadmin.qiawei.com>
* 作    者：寒川<hanchuan@qiawei.com>
* 日    期：2015-09-17
* 版    本：1.0.0
* 功能说明：模块公共文件。
*
**/


function UpImage($callBack="image",$width=100,$height=100,$image=""){
    echo '<iframe scrolling="no" frameborder="0" border="0" onload="this.height=this.contentWindow.document.body.scrollHeight;this.width=this.contentWindow.document.body.scrollWidth;" width='.$width.' height="'.$height.'"  src="'.U('Upload/uploadpic').'?Width='.$width.'&Height='.$height.'&BackCall='.$callBack.'&Img='.$image.'"></iframe>
         <input type="hidden" name="'.$callBack.'" id="'.$callBack.'">';
}
function BatchImage($callBack="image",$height=300,$image=""){
    echo '<iframe scrolling="no" frameborder="0" border="0" onload="this.height=this.contentWindow.document.body.scrollHeight;this.width=this.contentWindow.document.body.scrollWidth;" src="'.U('Upload/batchpic').'?BackCall='.$callBack.'&Img='.$image.'"></iframe>
		<input type="hidden" name="'.$callBack.'" id="'.$callBack.'">';
}


/*
 * 函数：网站配置获取函数
 * @param  string $k      可选，配置名称
 * @return array          用户数据
*/
function setting($k=''){
	if($k==''){
        $setting =M('setting')->field('k,v')->select();
		foreach($setting as $k=>$v){
			$config[$v['k']] = $v['v'];
		}
		return $config;
	}else{
		$model = M('setting');
		$result=$model->where("k='{$k}'")->find(); 
		return $result['v'];
	}
}

/**
 * 函数：格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '') {
	$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
	for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
	return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 函数：加密
 * @param string            密码
 * @return string           加密后的密码
 */
function password($password){
	/*
	*后续整强有力的加密函数
	*/
	return md5('Q'.$password.'W');

}
/*
 * 发送请求
 * @param $method String 值为"get"或"post"
 * @param $method String/array 发送的数据
 */
function sendMessage($method,$url,$message=""){
	vendor("curlClass");
	$curl=new Curl();
    if($method=="get"){
		if(empty($message)){
			return $curl->get($url);
		}else{
			return $curl->get($url."?".$message);
		}
	}
	if($method=="post"){
		$curl->post($url,$message);
	}
}
function EasemObj(){
	vendor("Emchat.Easemob#class");
	$options['client_id']='YXA6agCz4CPmEea-vWeG6al_iA';
	$options['client_secret']='YXA6tDqTTgzzrMu8nGdN1anVv-LyGI0';
	$options['org_name']='lisy';
	$options['app_name']='xinchating';
	$Easemob=new \Easemob($options);
	return $Easemob;
}
/*
 * 获取ip
 */
function get_server_ip(){
	if(!empty($_SERVER['SERVER_ADDR']))
		return $_SERVER['SERVER_ADDR'];
	return gethostbyname($_SERVER['HOSTNAME']);
}
/*
 * json数据转array
 */
function jsonArr($data){
     return json_decode($data,true);
}
/*
 * json 中文输出 php-version>=5.4
 */
function jsonEcho($data){
    return json_encode($data);
}

/*
 * 产生随机码
 */
function createCode($id){
    $randStr = str_shuffle('1234567890AJIEKSJSOJWlasdjfoiasdf');
    $rand = substr($randStr,0,6);
    return $id.time().$rand;
}
/*
 *加密 encrypt($subInfo['password'],C("secretkey"))
 */
function encrypt($data, $key)
{
    $key    =   md5($key);
    $x      =   0;
    $len    =   strlen($data);
    $l      =   strlen($key);
    $char="";
    for ($i = 0; $i < $len; $i++)
    {
        if ($x == $l)
        {
            $x = 0;
        }
        $char .= $key{$x};
        $x++;
    }
    $str="";
    for ($i = 0; $i < $len; $i++)
    {
        $str .= chr(ord($data{$i}) + (ord($char{$i})) % 256);
    }
    return base64_encode($str);
}
/*
 *解密 decrypt($subInfo['password'],C("secretkey"))
 */
function decrypt($data, $key)
{
    $key = md5($key);
    $x = 0;
    $data = base64_decode($data);
    $len = strlen($data);
    $l = strlen($key);
    $char="";
    for ($i = 0; $i < $len; $i++)
    {
        if ($x == $l)
        {
            $x = 0;
        }
        $char .= substr($key, $x, 1);
        $x++;
    }
    $str="";
    for ($i = 0; $i < $len; $i++)
    {
        if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1)))
        {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        }
        else
        {
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return $str;
}
/*
 * 获取图片信息
 */
function getImageInfo($image){
    $imageInfo = getimagesize($image);
    if ($imageInfo !== FALSE) {
        $imageType = strtolower(substr(image_type_to_extension($imageInfo[2]), 1));
        $imageSize = filesize($image);
        $info = array(
            "width" => $imageInfo[0],
            "height" => $imageInfo[1],
            "type" => $imageType,
            "size" => $imageSize,
            "mime" => $imageInfo['mime']
        );
        return $info;
    } else {
        return FALSE;
    }
}
/*
 * 生成加密验证,密钥newapp
 */
function createSign($subInfo){
    ksort($subInfo);
    $signStr="newapp";
    //var_dump($subInfo);
    foreach($subInfo as $key=>$val){
//        if($val===0||$val==="0"){
//            $signStr=$signStr.$key.$val;
//        }else{
//            if (!empty($val)) {
//                $signStr = $signStr . $key . $val;
//            }
//        }
        if(null!=$val){
            $signStr=$signStr.$key.$val;
        }
    }
    echo $signStr."<br>";
    $signStr=md5($signStr);
    return $signStr;
}
/*
 * 判断文件数组是否至少有一个值
 */
function is_empty($arr=array()){
    foreach($arr as $val){
        if(!empty($val['tmp_name'])){
            return true;
            break;
        }
    }
    return false;
}
/*
 * 产生指定数据库中指定表内不重复字符
 * @param String table 表名称
 * @param String field 字段名称
 */
function creatRandCode($table,$field){
    $randStr = str_shuffle('1234567890AJIEKSJSOJWlasdjfoiasdf');
    $rand = substr($randStr,0,6);
    $allModel=M($table);
    while($rand){
        $rs=$allModel->where(array($field=>$rand))->find();
        if(!empty($rs)){
            $rand = substr($randStr,0,6);
        }else{
            $finalData=$rand;
            $rand="";
        }
    }
    return $finalData;
}