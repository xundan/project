<?php
/**
 * crontab调用从生消息表转移数据至消息表
 * User: CLEVO
 * Date: 2016/8/9
 * Time: 14:33
 */

namespace Views\Controller;
use Think\Controller\RestController;

class DistributeController extends RestController
{
    public function index(){

    }

    public function create($recorder){
//        $record=array(
//            'message'=>$content,
//            'type'=>'plain',
//            'remark'=>'0',
//        );
//        D('Distribute')->add($record);

        $Raw = D('Raw');
        $Message=D('Message');

//        $map['remark'] = '0';
        $map['status'] = 0;
//        $map['type'] = 'plain';// 把查询条件传入查询方法
        $transferring=$Raw->where($map)->select();
        $count=count($transferring);

//        var_dump($transferring);

        $insert_trans=array(
            "title"=>null,
            "origin"=>null,
            "category"=>null,
            "publisher_rid"=>null,
            "publish_time"=>null,
            "level"=>1,
            "valid_time"=>1,
            "via_type"=>2,
            "times_number"=>1,
            "type"=>null,
            "content"=>null,
            "status"=>0,
            "remark"=>null,
            "invalid_id"=>0,
            "handler"=>null,
            "recorder"=>$recorder,
            "owner"=>null,
            "sender_wx"=>null,
            "sender"=>null,
            );
        $update_trans=array(
            "id"=>-1,
            "status"=>10,
        );
        for ($i=0;$i<$count;$i++){
            $trans1=$transferring[$i];
            // 根据id更新生消息表状态
            $update_trans["id"]=$trans1["id"];
            $check=$Raw->save($update_trans);

            if ($check==false){
                //TODO 写日志，通知开发
            }
            // 生成新的消息表数据
            $insert_trans["title"]=$trans1["rid"];
            $insert_trans["content"]=$trans1["content"];
            $insert_trans["sender"]=$trans1["sender"];
            $insert_trans["type"]=$trans1["type"];
            $insert_trans["owner"]=$trans1["owner"];
            $insert_trans["sender_wx"]=$trans1["sender_wx"];
            $insert_trans["remark"]=$trans1['remark'];

            $content=$trans1['content'];
            $mode = '/([0-9]{11})|(\+86[0-9]{11})/'; //正则，必须写在反斜杠里面
            preg_match($mode,$content,$match);
            $origin="未知";
            if($match){
                $origin=$match[0];
            }
            $insert_trans['origin']=$origin;
//            var_dump($insert_trans);
            $check=$Message->add($insert_trans);
            if ($check==false){
                //TODO 写日志，通知开发
            }
        }
        $record=array(
            'message'=>'transfer_'.$count,
            'type'=>'plain',
            'remark'=>''.$count,
        );
        D('Distribute')->add($record);
    }


}