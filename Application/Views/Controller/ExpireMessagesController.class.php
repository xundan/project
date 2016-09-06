<?php
/**
 * 删除过期未拉取的微信号与小消息的关系
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/9/3
 * Time: 9:47
 */

namespace Views\Controller;

use Think\Controller\RestController;

class ExpireMessagesController extends RestController
{

    public function index()
    {
        echo "Message expiration works.";
        $whatever = $this->selectExpired();
        $count = count($whatever);

        if ($whatever) {
            for ($i=0;$i<$count;$i++){
                $what = $whatever[$i];
                echo "<br/>Should be expired $i: ".$what['msg_id'].", "
                    .$what['wx'].", ". $what['record_time'];
            }
        }else{
            echo "<br/>Nothing to be expired.";
        }
//        echo "<br/>".substr("【求车】a",0,12);
//        2016-08-26 16:23:28
    }

    public function all()
    {
        $M2W = D('RelationM2W');

        $oldM2W = $this->selectExpired();
        $count = count($oldM2W);
        for ($i=0;$i<$count;$i++){
            $oldRelation = $oldM2W[$i];
            $oldRelation['invalid_id']=99;
            $M2W->save($oldRelation);
        }
        $record = array(
            'message' => 'Expire_M2W:' . $count,
            'type' => 'Expire_M2W',
            'remark' => '' . $count,
        );
        D('Distribute')->add($record);


    }

    /**
     * @return array
     */
    private function selectExpired()
    {
        $expireDays = 2;
        $M2W = D('RelationM2W');
        $expireLine = date("Y-m-d H:i:s", time() - $expireDays * 86400);
        $oldM2W = $M2W->where("invalid_id=0 AND record_time<'$expireLine'")->select();
        return $oldM2W;
    }
}