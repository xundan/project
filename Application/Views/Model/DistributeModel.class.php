<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/8/9
 * Time: 15:03
 */

namespace Views\Model;
use Think\Model;

class DistributeModel extends Model
{


    protected $tableName = "distribute_record";

    protected $fields=array( //辅助模型识别字段，不会影响查询，会影响增改
        "id",
        "message",
        "type",
        "remark",
        "record_time",
        '_pk'=>"id",
    );

}