<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/8/3
 * Time: 10:23
 */

namespace Views\Model;
use Think\Model;


class ClientsKMWModel extends Model
{

    protected $tableName = "clients_for_kmw";

    protected $fields=array( //辅助模型识别字段，不会影响查询，会影响增改
        "id",
        "name",
        "phone",
        "pswd",
        "type",
        "remark",
        "resign_time",
        "source",
        "channel",
        '_pk'=>"id",
    );

}