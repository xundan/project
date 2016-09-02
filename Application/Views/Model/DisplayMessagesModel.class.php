<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2016/8/12
 * Time: 13:58
 */

namespace Views\Model;


use Think\Model;

class DisplayMessagesModel extends Model
{
    protected $tableName = "messages";

    protected $fields=array( //辅助模型识别字段，不会影响查询，会影响增改
        "id",
        "title",
        "origin",
        "category",
        "publisher_rid",
        "publish_time",
        "level",
        "valid_time",
        "via_type",
        "times_number",
        "type",
        "content",
        "status",
        "remark",
        "record_time",
        "invalid_id",
        "handler",
        "recorder",
        "owner",
        "sender_wx",
        "sender",

        '_pk'=>"id",
    );
}