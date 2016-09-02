<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/7/18
 * Time: 10:47
 */
function getTestData(){
    $data = array();
    for ($i=0; $i<10; $i++){
        $data[$i]['name']='user-'.$i;
        $data[$i]['age']=rand(18,45);
    }
    return $data;
}