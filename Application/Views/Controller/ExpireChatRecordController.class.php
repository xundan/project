<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/9/27
 * Time: 22:35
 */

namespace Views\Controller;

use Think\Controller\RestController;

class ExpireChatRecordController extends RestController
{

    public function index()
    {
        echo "Chat-records expiration works.";
        $whatever = $this->selectExpired();
        $count = count($whatever);

        if ($whatever) {
            for ($i=0;$i<$count;$i++){
                $what = $whatever[$i];
                echo "<br/>Should be expired $i: ".$what['self_wx'].", "
                    .$what['client_name'].", ". $what['record_time'];
            }
        }else{
            echo "<br/>Nothing to be expired.";
        }
//        echo "<br/>".substr("【求车】a",0,12);
//        2016-08-26 16:23:28
    }

    public function all()
    {
        $ChatRecord = D('ChatRecord');

        $oldChatRecord = $this->selectExpired();
        $count = count($oldChatRecord);
        for ($i=0;$i<$count;$i++){
            $oldRecord = $oldChatRecord[$i];
            $ChatRecord->delete($oldRecord);
        }
//        $record = array(
//            'message' => 'Expire_M2W:' . $count,
//            'type' => 'Expire_M2W',
//            'remark' => '' . $count,
//        );
//        D('Distribute')->add($record);
    }

    /**
     * @return array
     */
    private function selectExpired()
    {
        $expireDays = 2;
        $ChatRecord = D('ChatRecord');
        $expireLine = date("Y-m-d H:i:s", time() - $expireDays * 86400);
        $oldChatRecord = $ChatRecord->where("record_time<'$expireLine'")->select();
        return $oldChatRecord;
    }
}