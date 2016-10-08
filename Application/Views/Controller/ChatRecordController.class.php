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
        echo "ChatRecord api works";
    }

    Public function record()
    {
        $data = $this->defaultResponse();
        switch ($this->_method) {
            case 'get': // get请求处理代码
                $this->defaultGetAction();
                break;
            case 'post': // post请求处理代码
                $object2 = $this->decodeJSONFromBody();

                if ($object2) {
                    $self_wx = $object2['self_wx'];
                    $client_name = $object2['client_name'];
                    $content = $object2['content'];
                    $isme = $object2['isme'];
                    $type = $object2['type'];
                    $remark = $object2['remark'];

                    $insert = $this->createChatRecord($self_wx, $client_name,
                        $content, $isme, $type, $remark);
                    if ($insert) {
                        $data['result_code'] = "201";
                        $data['reason'] = "新建或修改数据成功";
                        $data['error_code'] = "0";
                        $data['message_id'] = $client_name;
                        $data['result'] = $remark;
                    } else {
                        $data = $this->dbErrorResponse($data);
                    }
                } else {
                    $data = $this->internalErrorResponse($data);
                }

                $this->response($data, 'json');
                break;
        }
    }

    Public function unsent_record()
    {
        mb_internal_encoding('utf-8');
        $data = $this->defaultResponse();
        switch ($this->_method) {
            case 'get': // get请求处理代码
                $this->defaultGetAction();
                break;
            case 'post': // post请求处理代码
                $object2 = $this->decodeJSONFromBody();

                if ($object2) {
                    $self_wx = $object2['self_wx'];


                    $fetch = $this->fetch_unsent_record($self_wx);
                    if ($fetch) {
                        $data['result_code'] = utf8_encode("201");
                        $data['reason'] = utf8_encode("获取数据成功");
                        $data['error_code'] = 0;
                        $data['message_id'] = utf8_encode($fetch["id"]);
                        $data['name']=utf8_encode($fetch["client_name"]);
                        $data['word']=utf8_encode($fetch["content"]);
                        $data['result'] = utf8_encode(json_encode($fetch));
                    } elseif($fetch===null) {
                        $data['result_code'] = "202";
                        $data['reason'] = "没有更多数据了";
                        $data['error_code'] = 0;
                        $data['message_id'] = "";
                        $data['result'] = "";
                    } else{
                        $data = $this->dbErrorResponse($data);
                    }
                } else {
                    $data = $this->internalErrorResponse($data);
                }

                $this->response($data, 'json');
                break;
        }
    }


    Public function status()
    {
        $data = $this->defaultResponse();
        switch ($this->_method) {
            case 'get': // get请求处理代码
                $this->defaultGetAction();
                break;
            case 'post': // post请求处理代码
                $object2 = $this->decodeJSONFromBody();

                if ($object2) {
                    $id = $object2['id'];
                    $status = $object2['status'];

                    $update = $this->update_status($id, $status);
                    if ($update) {
                        $data['result_code'] = "201";
                        $data['reason'] = "更新数据成功";
                        $data['error_code'] = 0;
                        $data['message_id'] = $id;
                        $data['result'] = "update status to ".$status;
                    } elseif($update===0) {
                        $data['result_code'] = "202";
                        $data['reason'] = "没有可以更新的数据了";
                        $data['error_code'] = 0;
                        $data['message_id'] = $id;
                        $data['result'] = "there is no id = ".$id;
                    } else{
                        $data = $this->dbErrorResponse($data);
                    }
                } else {
                    $data = $this->internalErrorResponse($data);

                }

                $this->response($data, 'json');
                break;
        }
    }


    private function fetch_unsent_record($self_wx)
    {
        $whereAttr = array(
            'self_wx' => $self_wx,
            'isme' => 1,
            'status' =>0,
            'invalid_id' => 0,
        );
        $find = D('ChatRecord')->where($whereAttr)->find();
        return $find;
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

    private function update_status($id, $status)
    {
        $data['status']=$status;
        $insert = D('ChatRecord')->where('id=%d',$id)->save($data);
        return $insert;
    }

    /**
     * @param $data
     * @return mixed
     */
    private function dbErrorResponse($data)
    {
        // TODO 数据库操作失败，通知开发人员
        $data['result_code'] = "106";
        $data['reason'] = "数据库操作错误";
        $data['error_code'] = 10006;
        $data['message_id'] = "";
        $data['result'] = "";
        return $data;
    }

    /**
     * @param $data
     * @return mixed
     */
    private function internalErrorResponse($data)
    {
        //TODO 数据为空，网络错误，客户端错误，通知开发人员
        $data['result_code'] = "500";
        $data['reason'] = "内部错误";
        $data['message_id'] = null;
        $data['error_code'] = 10500;
        $data['result'] = null;
        return $data;
    }

    /**
     * @return array
     */
    private function defaultResponse()
    {
        $data = array(
            "result_code" => "105",
            "reason" => "应用未审核超时，请提交认证",
            "result" => null,
            "message_id" => null,
            "error_code" => 10005,
        );
        return $data;
    }

    private function defaultGetAction()
    {
        if ($this->_type == 'html') {
            echo 'html';
        } elseif ($this->_type == 'xml') {
            echo 'xml';
        }
        echo '<br>restful url is correct.';
    }

    /**
     * @return mixed
     */
    private function decodeJSONFromBody()
    {
        $result1 = $GLOBALS['HTTP_RAW_POST_DATA'];
        $object2 = json_decode($result1, true);
        return $object2;
    }
}