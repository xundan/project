<?php
	/*
	*功能:PHP发送短信接口
	*修改日期:	2013-9-13
	*说明:http://www.gysoft.cn/smspost/send.aspx?username=用户账号&password=&mobile=号码&content=内容
	*/
  class  sms{
    
	private $uid;
	private $pwd;
	
	public function __construct($uid,$pwd)
	{
	  $this->uid=$uid;
	  $this->pwd=$pwd;
	}
  
   public function sendSMS($mobile,$content)
	{
		$http = 'http://www.gysoft.cn/smspost/send.aspx';
		$data = array
			(
			'username'=>$this->uid,			//用户账号
			'password'=>$this->pwd,	        //密码
			'mobile'=>$mobile,			//号码
			'content'=>$content      	//内容
			);
			
		$re= $this->postSMS($http,$data);			//POST方式提交
		$re=iconv('UTF-8', 'GB2312',$re);
		
		if( substr($re,0,2) == 'OK' )  //返回结果为OK1表示成功1条，OK2 表示成功二条，以此类推
		{
		    $result['msg']="发送成功!";
		    $result['data']=1;
		
			return $result;
		}
		else 
		{
		    $result['msg']="发送失败! 状态：".$re;   //返回错误的详细提示
		    $result['data']=0;
		
			return $result;			
		}
	}  
	public function postSMS($url,$data='')
	{
		$row = parse_url($url);
		$host = $row['host'];
		$port = $row['port'] ? $row['port']:80;
		$file = $row['path'];
		while (list($k,$v) = each($data)) 
		{
			$post .= rawurlencode($k)."=".rawurlencode($v)."&";	//转URL标准码
		}
		$post = substr( $post , 0 , -1 );
		$len = strlen($post);
		$fp = @fsockopen( $host ,$port, $errno, $errstr, 10);
		if (!$fp) {
			return "$errstr ($errno)\n";
		} else {
			$receive = '';
			$out = "POST $file HTTP/1.1\r\n";
			$out .= "Host: $host\r\n";
			$out .= "Content-type: application/x-www-form-urlencoded\r\n";
			$out .= "Connection: Close\r\n";
			$out .= "Content-Length: $len\r\n\r\n";
			$out .= $post;		
			fwrite($fp, $out);
			while (!feof($fp)) {
				$receive .= fgets($fp, 128);
			}
			fclose($fp);
			$receive = explode("\r\n\r\n",$receive);
			unset($receive[0]);
			return implode("",$receive);
		}
	}  
  }
?>