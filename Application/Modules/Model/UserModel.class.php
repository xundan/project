<?php

/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/7/27
 * Time: 23:12
 */
namespace Modules\Model;
use Think\Model;

/**
 * 默认对应数据表 User
 * 设置成对应数据表 clients
 * Class UserModel
 * @package Modules\Model
 */
class UserModel extends Model
{
//    protected $tablePrefix = "db_";
    protected $tableName = "clients";
//    protected $trueTableName = "db_Clients"; //可以保证表名的大小写（linux对此敏感）

    protected $fields=array( //辅助模型识别字段，不会影响查询，会影响增改
        "id",
    "name",
    "wx_id",
    "wx_name",
    "phone",
    "linephone",
    "qq",
    "address",
    "email",
    "record_time",
    "source",
    "distric_id",
    "group_id",
    "role_id",
    "product_id",
    "company",
    "position",
    "recorder",
    "invalid_id",
        '_pk'=>"id",
//        '_type'=>array(
//            'id'=>'int',
//            ...
//        ),
    );

}