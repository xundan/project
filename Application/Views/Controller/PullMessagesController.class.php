<?php
/**
 * 微信后台调用处理小消息
 * User: CLEVO
 * Date: 2016/8/24
 * Time: 17:29
 */

namespace Views\Controller;

use Think\Controller\RestController;

class PullMessagesController extends RestController
{
    protected $allowMethod = array('get', 'post', 'put'); // REST允许的请求类型列表
    protected $allowType = array('json'); // REST允许请求的资源类型列表

    public function index()
    {
        echo "pull available";
    }

    Public function pull($wx)
    {
        $data = array(
            "result_code" => "105",
            "reason" => "超时，请检查调用",
            "result" => null,
            "message" => null,
            "error_code" => 10005,
        );
        switch ($this->_method) {
            case 'get': // get请求处理代码
                $msg = $this->get_message($wx);
                if ($msg) {
                    $data['message'] = "【" . $msg['category'] . "】" . $msg['content'];
                    $data['result_code'] = "201";
                    $data['reason'] = "获取成功";
                    $data['result'] = "OK";
                    $data['error_code'] = 0;
                } elseif($msg===0) { // 关系表有数据，但是信息表没有该id
                    $data['message'] = "";
                    $data['result_code'] = "505";
                    $data['reason'] = "找不到该消息，请检查数据库";
                    $data['result'] = "FAIL";
                    $data['error_code'] = 10405;
                } elseif($msg===null) { // wx为空
                    $data['message'] = "";
                    $data['result_code'] = "500";
                    $data['reason'] = "参数错误";
                    $data['result'] = "FAIL";
                    $data['error_code'] = 10500;
                } elseif($msg===false) { // 没有消息了
                    $data['message'] = "";
                    $data['result_code'] = "404";
                    $data['reason'] = "没有更多的消息了";
                    $data['result'] = "OK";
                    $data['error_code'] = 10404;
                } else { // 其他错误
                    $data['message'] = "";
                    $data['result_code'] = "501";
                    $data['reason'] = "内部错误";
                    $data['result'] = "FAIL";
                    $data['error_code'] = 10501;
                }
                $this->response($data, 'json');
                break;
        }
    }

    // 有些不标准，get方法修改msg2wx关系表状态，以后应改为post方法
    Public function used($wx, $msg_id)
    {
        $data = array(
            "result_code" => "105",
            "reason" => "应用未审核超时，请提交认证",
            "result" => null,
            "message" => null,
            "error_code" => 10005,
        );
        switch ($this->_method) {
            case 'get':
                $update = $this->set_used($wx, $msg_id);
                if ($update) {
                    $data['result_code'] = "201";
                    $data['reason'] = "修改数据成功";
                    $data['error_code'] = "0";
                    $data['result'] = 'OK';
                    $data['message'] = "wx:" . $wx . ",msg_id:" . $msg_id;
                } elseif ($update === false) { //更新出错
                    $data['message'] = "";
                    $data['result_code'] = "501";
                    $data['reason'] = "内部错误";
                    $data['result'] = "FAIL";
                    $data['error_code'] = 10501;
                } else { // 更新行数为0
                    $data['message'] = "";
                    $data['result_code'] = "404";
                    $data['reason'] = "没有可更新的数据";
                    $data['result'] = "OK";
                    $data['error_code'] = 10404;
                }
                $this->response($data, 'json');
                break;
        }
    }


    /**
     * @param $wx
     * @return string
     */
    private function get_message($wx)
    {
        //不能瞎echo，否则GLOBAL解析json会出错
//        echo "start ";
        if (!$wx) return null;
        $relation = D("RelationM2W")->where("invalid_id=0 AND wx='" . $wx . "'")->find();

        if ($relation) {
//            echo "here is $relation";
            $msg_id = $relation['msg_id'];
//            echo "<br/>here is $msg_id";
            $msg = D('message')->find($msg_id);
//            echo "<br/> that's $msg";
            if ($msg) {
                $this->set_used($wx, $msg_id);
            }
            return $msg;

        }
        return false;
    }

    /**
     * @param $wx
     * @param $msg_id
     * @return bool
     */
    private function set_used($wx, $msg_id)
    {
        $update_data['invalid_id'] = 100;
        $update = D('RelationM2W')->where("invalid_id=0 AND msg_id=$msg_id AND wx='$wx'")
            ->save($update_data);
        // TODO 此处应该更新小消息表中的publish_time
        return $update;
    }


}