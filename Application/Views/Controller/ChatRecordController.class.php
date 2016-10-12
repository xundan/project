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

    //　记录一条历史消息
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

    // 拉取还未发送出去的消息
    Public function unsent_record()
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


                    $fetch = $this->fetch_unsent_record($self_wx);
                    if ($fetch) {
                        $data['result_code'] = "201";
                        $data['reason'] = "获取数据成功";
                        $data['error_code'] = 0;
                        $data['message_id'] = $fetch["id"];
                        $data['name']=$fetch["client_name"];
                        $data['word']=$fetch["content"];
                        $data['result'] = "success";
//                        $data['result'] = utf8_encode(json_encode($fetch));
                        Log::record("The response data: $data", Log::NOTICE);
                    } elseif($fetch===null) {
                        $data = $this->noMoreDataResponse($data);
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

    // 按id重置信息的状态，用以标记已经发出的信息
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
                        $data = $this->noMoreDataResponse($data);
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

    // 按self_wx与client_name重置type，用以标记已完成的转人工
    // 测试json:{"self_wx":"test","client_name":"test2","type":"plain"}
    Public function set_type()
    {
        $data = $this->defaultResponse();
        switch ($this->_method) {
            case 'get': // get请求处理代码
                $this->defaultGetAction();
                break;
            case 'post': // post请求处理代码
                $object2 = $this->decodeJSONFromBody();

                if ($object2) {
                    $self_wx= $object2['self_wx'];
                    $client_name= $object2['client_name'];
                    $r_type = $object2['type'];

                    $update = $this->update_type($self_wx, $client_name, $r_type);
                    if ($update) {
                        $data['result_code'] = "201";
                        $data['reason'] = "更新数据成功";
                        $data['error_code'] = 0;
                        $data['message_id'] = $update;
                        $data['result'] = "update status to ".$r_type;
                    } elseif($update===0) {
                        $data = $this->noMoreDataResponse($data);
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

    // 获取某微信号对某个客户的聊天记录
    Public function distinct_record()
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

                    $fetch = $this->fetch_distinct_record($self_wx, $client_name);
                    if ($fetch) {
                        $record_list = "";
                        foreach ($fetch as $a_record){
                            if ($a_record){
                                if ($a_record["isme"]){
                                    $record_list .= "我:";
                                }else{
                                    $record_list .= $a_record["client_name"].":";
                                }
                                $record_list .= $a_record["content"]."<br>";
                            }
                        }
                        $data['result_code'] = "201";
                        $data['reason'] = "获取数据成功";
                        $data['error_code'] = 0;
                        $data['message_id'] = count($fetch);
                        $data['result'] = $record_list;
                    } elseif($fetch===null) {
                        $data = $this->noMoreDataResponse($data);
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

    // 取出所有的待转人工列表
    // 测试json:{"wx_list":["test","test2"]}
    Public function all_distinct_record()
    {
        $data = $this->defaultResponse();
        switch ($this->_method) {
            case 'get': // get请求处理代码
                $this->defaultGetAction();
                break;
            case 'post': // post请求处理代码
                $object2 = $this->decodeJSONFromBody();
                if ($object2) {
                    $wx_list = $object2['wx_list'];
                    if ($wx_list){
                        $wx_in = $this->assemble_wx_in_sql($wx_list);
                        $fetch = $this->fetch_all_distinct_record($wx_in);

                        if ($fetch) {
                            $data['result_code'] = "201";
                            $data['reason'] = "获取数据成功";
                            $data['error_code'] = 0;
                            $data['message_id'] = count($fetch);
                            $data['result'] = $fetch;
                        } elseif($fetch===null) {
                            $data = $this->noMoreDataResponse($data);
                        } else{
                            $data = $this->dbErrorResponse($data);
                        }
                    }else{
                        $data = $this->noMoreDataResponse($data);
                    }

                } else {
                    $data = $this->internalErrorResponse($data);
                }

                $this->response($data, 'json');
                break;
        }
    }

    private function fetch_all_distinct_record($wx_in)
    {
        $fetch = D('ChatRecord')->field('content',true)->where("isme=0 AND type='plain' AND invalid_id=0 AND self_wx IN "
            .$wx_in)->group('client_name')->order('record_time asc')->select();
        return $fetch;
    }

    private function fetch_distinct_record($self_wx, $client_name)
    {
        $whereAttr = array(
            'self_wx' => $self_wx,
            'client_name'=> $client_name,
            'invalid_id' => 0,
        );
        $find = D('ChatRecord')->where($whereAttr)->order('record_time asc')->select();
        return $find;
    }

    private function fetch_unsent_record($self_wx)
    {
        $whereAttr = array(
            'self_wx' => $self_wx,
            'isme' => 1,
            'status' =>0,
            'invalid_id' => 0,
        );
        $find = D('ChatRecord')->where($whereAttr)->order('record_time asc')->find();
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

    private function update_type($self_wx, $client_name, $r_type)
    {
        $attribute = array(
            'self_wx' => $self_wx,
            'client_name' => $client_name,
        );
        $data['type']=$r_type;
        $insert = D('ChatRecord')->where($attribute)->save($data);
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

    /**
     * @param $wx_list
     * @return string
     */
    private function assemble_wx_in_sql($wx_list)
    {
        $wx_in = "(";
        foreach ($wx_list as $wx) {
            $wx_in .= "'$wx',";
        }
        $wx_in .= "'suffix')";
        return $wx_in;
    }

    /**
     * @param $data
     * @return mixed
     */
    private function noMoreDataResponse($data)
    {
        $data['result_code'] = "202";
        $data['reason'] = "没有更多数据了";
        $data['error_code'] = 0;
        $data['message_id'] = 0;
        $data['result'] = "";
        return $data;
    }
}