<?php

/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/7/18
 * Time: 9:56
 */
namespace Home\Controller\Index;
use Think\Controller;
class index extends Controller
{
    /**
     * 将action绑定到类，在config中将ACTION_BIND_CLASS打开
     * 如此一来，在浏览器中输入localhost/cjkzy_v1时，会执行下面的run()
     */
    public function run(){
        echo "bind action";
    }
}