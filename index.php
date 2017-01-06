<?php
// 定义ThinkPHP框架路径
define('THINK_PATH', './TLib/');
define('APP_PATH', './12306');
// 加载框架入口文件 
require(THINK_PATH."/Desenz.php");
//实例化一个网站应用实例
App::run();
?>
