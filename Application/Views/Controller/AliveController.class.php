<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/10/3
 * Time: 12:20
 */

namespace Views\Controller;

use Think\Controller\RestController;
use Think\Log;

class AliveController extends RestController
{

    protected $allowMethod = array('get', 'post', 'put'); // REST允许的请求类型列表
    protected $allowType = array('json'); // REST允许请求的资源类型列表


    public function index()
    {
//        $this->createRaw("test");
        echo "Alive api works";
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
                    $name = $object2['name'];
                    $alive = $object2['alive'];

                    $insert = $this->createAliveRecord($name,$alive);
                    if ($insert) {
                        $data['result_code'] = "201";
                        $data['reason'] = "新建或修改数据成功";
                        $data['error_code'] = "0";
                        $data['message_id'] = $name;
                        $data['result'] = $alive;
                    } else {
                        // TODO 插入失败，通知开发人员
                        $data['result_code'] = "106";
                        $data['reason'] = "数据库操作错误";
                        $data['error_code'] = "10006";
                        $data['message_id'] = $name;
                        $data['result'] = $alive;
                    }
                } else {
                    //TODO 数据为空，网络错误，客户端错误，通知开发人员
                    $data['result_code'] = "500";
                    $data['reason'] = "内部错误";
                    $data['message_id'] = null;
                    $data['error_code'] = "10500";
                    $data['result'] = null;
//                    Log::record("sth wrong", Log::WARN);
                }

                $this->response($data, 'json');
                break;
        }
    }

    public function show($name){
        $data = array(
            "result_code" => "105",
            "reason" => "超时，请检查调用",
            "result" => "-1",
            "message" => null,
            "error_code" => 10005,
        );
        switch ($this->_method) {
            case 'get': // get请求处理代码
                $msg = $this->get_alive($name);
                if ($msg) {
                    if ($msg['alive']==1){
                        $data['message'] = $msg['record_time'];
                        $data['result_code'] = "201";
                        $data['reason'] = $name." is alive.";
                        $data['result'] = "1";
                        $data['error_code'] = 0;
                    }else{
                        $data['message'] = $msg['record_time'];
                        $data['result_code'] = "201";
                        $data['reason'] = $name." is dead.";
                        $data['result'] = "0";
                        $data['error_code'] = 0;
                    }
                } elseif($msg===null) { // wx为空
                    $data['message'] = "";
                    $data['result_code'] = "500";
                    $data['reason'] = "参数错误，没有该服务的记录";
                    $data['result'] = "-1";
                    $data['error_code'] = 10500;
                } elseif($msg===false) { // 查询出错
                    $data['message'] = "";
                    $data['result_code'] = "501";
                    $data['reason'] = "查询出错";
                    $data['result'] = "-1";
                    $data['error_code'] = 10404;
                } else { // 其他错误
                    $data['message'] = "";
                    $data['result_code'] = "501";
                    $data['reason'] = "内部错误";
                    $data['result'] = "-1";
                    $data['error_code'] = 10501;
                }
                $this->response($data, 'json');
                break;
        }
    }

    private function createAliveRecord($name,$alive)
    {

        $rawAttribute = array(
            'name' => $name,
            'alive' => $alive,
        );
        $insert = M('alive')->add($rawAttribute);
        return $insert;
    }

    /**
     * @param $wx
     * @return string
     */
    private function get_alive($name)
    {
        //此处不能瞎echo，否则GLOBAL解析json会出错
        if (!$name) return null;
        $record = M("alive")->where("name='%s'",$name)->order('record_time desc')->find();

        return $record;
    }


}