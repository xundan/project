<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/8/16
 * Time: 10:58
 */


namespace Views\Model;
use Think\Model;

class RelationLabelModel extends Model
{
    protected $tableName = "relation_label";

    protected $fields=array( //辅助模型识别字段，不会影响查询，会影响增改
        "id",
        "object_rid",
        "label_name",
        "invalid_id",
        "record_time",
        '_pk'=>"id",
    );
}