<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/9/27
 * Time: 22:46
 */

namespace Views\Controller;

use Think\Controller\RestController;
use Think\Log;


class ChatRecordController extends RestController
{
    protected $allowMethod = array('get', 'post', 'put'); // REST允许的请求类型列表
    protected $allowType = array('json'); // REST允许请求的资源类型列表


    public function index()
    {
//        $this->createRaw("test");
        echo "ChatRecord api works";
    }

    Public function record()
    {
        $data = array(
            "result_code" => "105",
            "reason" => "应用未审核超时，请提交认证",
            "result" => null,
            "message_id" => null,
            "error_code" => 10005,
        );
        switch ($this->_method) {
            case 'get': // get请求处理代码
                if ($this->_type == 'html') {
                    echo 'html';
                } elseif ($this->_type == 'xml') {
                    echo 'xml';
                }
                echo '<br>restful url is correct.';
                break;
            case 'put': // put请求处理代码
//                if ($this->_type == 'json'){
//
//                }
                break;
            case 'post': // post请求处理代码

                $result1 = $GLOBALS['HTTP_RAW_POST_DATA'];
                $object2 = json_decode($result1, true);

                if ($object2) {
                    $self_wx = $object2['self_wx'];
                    $client_name = $object2['client_name'];
                    $content = $object2['content'];
                    $isme = $object2['isme'];
                    $type = $object2['type'];
                    $remark = $object2['remark'];

                    $insert = $this->createChatRecord($self_wx, $client_name,
                        $content, $isme, $type,$remark);
                    if ($insert) {
                        $data['result_code'] = "201";
                        $data['reason'] = "新建或修改数据成功";
                        $data['error_code'] = "0";
                        $data['message_id'] = $client_name;
                        $data['result'] = $remark;
                    } else {
                        // TODO 插入失败，通知开发人员
                        $data['result_code'] = "106";
                        $data['reason'] = "数据库操作错误";
                        $data['error_code'] = "10006";
                        $data['message_id'] = $client_name;
                        $data['result'] = $remark;
                    }
                } else {
                    //TODO 数据为空，网络错误，客户端错误，通知开发人员
                    $data['result_code'] = "500";
                    $data['reason'] = "内部错误";
                    $data['message_id'] = null;
                    $data['error_code'] = "10500";
                    $data['result'] = null;
                }

                $this->response($data, 'json');
                break;
        }
    }

    private function createChatRecord($self_wx, $client_name, $content, $isme, $type, $remark)
    {

        //解决短时间内重复调用问题
//        $duplicate_data = D('Raw')->where("rid = '$title'")->find();
//        if ($duplicate_data) {
//            return $duplicate_data;
//        }

        $rawAttribute = array(
            'self_wx' => $self_wx,
            'client_name' => $client_name,
            'content' => $content,
            'isme' => $isme,
            'type' => $type,
            'remark' => $remark,
            'status' => 0,
            'invalid_id' => 0,
        );
        $insert = D('ChatRecord')->add($rawAttribute);
        return $insert;
    }


}