<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/8/16
 * Time: 10:14
 */

namespace Views\Model;
use Think\Model;

class LabelModel extends Model
{
    protected $tableName = "labels";

    protected $fields=array( //辅助模型识别字段，不会影响查询，会影响增改
        "id",
        "label_name",
        "remark",
        "type",
        "invalid_id",
        '_pk'=>"id",
    );
}