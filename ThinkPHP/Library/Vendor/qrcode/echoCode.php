<?php

  function EchoCode($data,$ECC="M",$size="6"){
      $PNG_TEMP_DIR = dirname(dirname(dirname(dirname(dirname(__FILE__))))).DIRECTORY_SEPARATOR.'qrcode'.DIRECTORY_SEPARATOR;
      $PNG_WEB_DIR = 'temp/';
      include "qrlib.php";
      if (!file_exists($PNG_TEMP_DIR))
          mkdir($PNG_TEMP_DIR);
      $filename = $PNG_TEMP_DIR.'test.png';
      $errorCorrectionLevel = $ECC;
      $matrixPointSize = $size;
      if (isset($data)){
          if (trim($data) == '')
          die('data cannot be empty! <a href="?">back</a>');
          $filename = $PNG_TEMP_DIR.'test'.md5($data.'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
          QRcode::png($data, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
          return basename($filename);
      } else {
          QRcode::png('暂无信息', $filename, $errorCorrectionLevel, $matrixPointSize, 2);
          return basename($filename);
      }
  }
// echo EchoCode("hahahahah");//返回生成的二维码的文件名