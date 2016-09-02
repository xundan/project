<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2016/8/17
 * Time: 17:44
 */

namespace Views\Model;
use Think\Model;

class StaffsLoginModel extends Model
{
    protected $tableName = "staffs";
    protected $fields=array( //辅助模型识别字段，不会影响查询，会影响增改
        "id",
        "name",
        "username",
        "password",
        "email",
        "type",
        "authority",
        "employee_id",
        "remark",
        "invalid_id",

        '_pk'=>"id",
    );
}