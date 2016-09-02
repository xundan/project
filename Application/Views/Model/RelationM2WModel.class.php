<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/8/23
 * Time: 15:49
 */

namespace Views\Model;
use Think\Model;

class RelationM2WModel extends Model
{
    protected $tableName = "relation_msg2wx";

    protected $fields=array( //辅助模型识别字段，不会影响查询，会影响增改
        "id",
        "msg_id",
        "wx",
        "invalid_id",
        "record_time",
        '_pk'=>"id",
    );
}