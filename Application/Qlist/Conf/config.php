<?php
return array(
    //'配置项'=>'配置值'
//    'DEFAULT_V_LAYER'=>'Template', // 将默认视图存放目录View改为Template（自定义）
//    'TMPL_TEMPLATE_SUFFIX'=>'.xcl', // 更改模板最终显示的后缀
//    'TMPL_FILE_DEPR'=>'_', // 视图模板文件夹下的连接符替换，内部也需要改
//    'VIEW_PATH'=>'./Public/Views/' ,
    // 上面四项修改后自己内如路径也要如同显示一样，或者说上面几个选项是为了配合内部改动而存在的。

//    'TMPL_L_DELIM'=>'{{',
//    'TMPL_R_DELIM'=>'}}',   // 如果要打印出{}符号可以考虑这样写



//    'DEFAULT_THEME'=>'default',
//    'TMPL_DETECT_THEME'=>true,    //
//    'THEME_LIST'=>'default,xcl',  //多主题情形下这两项必须要配置
//    'TMPL_PARSE_STRING'=>
//        array(
//            '__pub__'=>'__PUBLIC__', // __PUBLIC__本身也是替换文本，它属于默认规则
//        ),


    'DB_TYPE'               =>  'mysql',     // 数据库类型
    'DB_HOST'               =>  'localhost', // 服务器地址
    'DB_NAME'               =>  'db',          // 数据库名
    'DB_USER'               =>  'root',      // 用户名
    'DB_PWD'                =>  '',          // 密码
    'DB_PORT'               =>  '3306',        // 端口
    'DB_PREFIX'             =>  '',    // 数据库表前缀
    'DB_PARAMS'          	=>  array(), // 数据库连接参数
    'DB_DEBUG'  			=>  TRUE, // 数据库调试模式 开启后可以记录SQL日志
    'DB_FIELDS_CACHE'       =>  true,        // 启用字段缓存
    'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8
    'DB_DEPLOY_TYPE'        =>  0, // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'DB_RW_SEPARATE'        =>  false,       // 数据库读写是否分离 主从式有效
    'DB_MASTER_NUM'         =>  1, // 读写分离后 主服务器数量
    'DB_SLAVE_NO'           =>  '', // 指定从服务器序号
);