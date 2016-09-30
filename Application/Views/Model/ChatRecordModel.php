<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/9/27
 * Time: 22:32
 */

namespace Views\Model;


class ChatRecordModel
{
    protected $tableName = "chat_record";

    protected $fields=array( //辅助模型识别字段，不会影响查询，会影响增改
        "id",
        "self_wx",
        "client_name",
        "content",
        "isme",
        "type",
        "remark",
        "record_time",
        "status",
        "invalid_id",
        '_pk'=>"id",
    );

}