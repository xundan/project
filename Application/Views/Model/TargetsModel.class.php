<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/8/21
 * Time: 23:22
 */

namespace Views\Model;
use Think\Model;
class TargetsModel extends Model
{
    protected $tableName = "Target";

    protected $fields=array( //辅助模型识别字段，不会影响查询，会影响增改
        "resourceid",
        "name",
        "mdate",
        "starttime",
        "endtime",
        "description",
        "mvalue",
        "isagenda",
        "minterval",
        "mmaxvalue",
        "todayvalue",
        "yesterdayvalue",
        "isdone",
        '_pk'=>"resourceid",
    );
}