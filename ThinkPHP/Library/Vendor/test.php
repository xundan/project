<?php
function sendCode($phone,$code)
{
    include "TopSdk.php";
    $c = new TopClient;
    $c->appkey = '23425802';
    $c->secretKey = 'bfc7dd30439f8421c066cc9cdf40555a';
    $req = new AlibabaAliqinFcSmsNumSendRequest;
    $req->setExtend("123456");
    $req->setSmsType("normal");
    $req->setSmsFreeSignName('迅单网络');
    $req->setSmsParam("{\"code\":$code,\"product\":\"超级矿资源\"}");
    $req->setRecNum("$phone");
    $req->setSmsTemplateCode("SMS_12951206");
    $resp = $c->execute($req);
}
?>