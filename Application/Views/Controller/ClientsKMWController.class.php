<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/8/3
 * Time: 10:34
 */

namespace Views\Controller;
use Think\Controller\RestController;

class ClientsKMWController extends RestController
{

    function index()
    {


        $url = 'http://app.trade.51kuaimei.com/asyn/SetUserInfoByCreate';

        $post_data = array();
        $post_data['client'] = 'api';
        $post_data['UDID'] = '123456987654321';
        $post_data['UID'] = "00000000000000000000000000000000";
        $post_data['timestamp'] = time();
        $post_data['app'] = "buyer"; //buyer=>是买家 seller =>是卖家 supplier =>是供应商

        $post_data['mobile'] = '12345698765432'; //手机号
        $post_data['password'] = '12345698765432'; //密码
        $post_data['invite_code'] = '8810';


        $data = $this->getParameter($post_data);
        $o = "";
        foreach ($data as $k => $v) {
            $o .= "$k=" . urlencode($v) . "&";
        }
        echo "<br>this is o:";
        echo "<br>".$o;
        $post_data = substr($o, 0, -1); // 消掉字符串末尾的'&'
        echo "<br>this is post_data:";
        echo "<br>".$post_data;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $data = curl_exec($ch);
        curl_close($ch);

        //$data = json_decode($data,true);
        echo "<h1/>";
        echo "THIS IS RESPONSE:<BR>";
        print_r($data);
    }


    function getParameter($param)
    {
        $token = "6f27b3e5b70d10";
        $_retval = array();
        foreach ($param as $key => $val) {
            $_retval[$key] = $val;
        }

        $tmpArr = array($token, $param['timestamp'], $param['UID'], $param['UDID'], $param['client'], $param['app']);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);

        $tmpStr = implode($tmpArr);
        $tmpStr = md5($tmpStr);
        $_retval['signature'] = $tmpStr;
        echo "<br>this is tmpStr:";
        echo "<br>".$tmpStr;
        return $_retval;
    }

}