<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body>
<?php
/**
 * Created by PhpStorm.
 * User: 呜呜呜
 * Date: 2016/9/27
 * Time: 16:09
 */
$conn = mysqli_connect('localhost:8080','root','');
if ($conn){
    echo '连接本地数据库成功.<br>极客学院视频中的函数musql_xx已经弃用<br>';
    echo'改为mysqli_xx';
}/*else{
    echo'失败';
}*/
?>
</body>
</html>