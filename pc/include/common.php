<?php
/**
 * 定义常用变量及参数
 */

/* 初始化基本变量 */
require('con_basic.php');

/* 定义时间区域 */
date_default_timezone_set('Asia/Shanghai');

/* 重定向域名到 www */
if($_SERVER['SERVER_ADDR'] != "127.0.0.1" && $_SERVER['SERVER_ADDR'] != "::1" && !preg_match('/^www./', $_SERVER['HTTP_HOST']) && substr_count($_SERVER['HTTP_HOST'], '.') == 1) {
	header('HTTP/1.1 301 Moved Permanently');
	header('Location: http://www.' . $_SERVER['HTTP_HOST'] . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : ''));
} 

/* 设置通用函数 */
require('fun_global.php');

/* 初始化数据库类 */
require('cls_mydata.php');
$my_db = new MyData($con_db_set, $con_db_name);

/* 使用页面缓存 */
if(function_exists('pageCache')) pageCache('read');

/* 关闭魔术引号 */
if (get_magic_quotes_gpc()) {
	function stripslashes_deep($value){
	    $value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
	    return $value;
	}

	$_POST = array_map('stripslashes_deep', $_POST);
	$_GET = array_map('stripslashes_deep', $_GET);
	$_COOKIE = array_map('stripslashes_deep', $_COOKIE);
	$_REQUEST = array_map('stripslashes_deep', $_REQUEST);
}

/* 定义当前页面 */
preg_match('/\/([^\/]+)\.php/i', $_SERVER['PHP_SELF'], $match);
if(empty($cur_page)) $cur_page = $match[1];

$cur_uri = preg_replace('/^.*\/([^\/]*)$/', '\1', $_SERVER['HTTP_X_REWRITE_URL'] ? $_SERVER['HTTP_X_REWRITE_URL'] : $_SERVER['REQUEST_URI']);
$cur_uri = $cur_uri ? $cur_uri : 'index.html';

/* 定义国家名称及其缩写 */
require('con_country.php');
$con_country_arr = $country_abbr_cn;

/* 定义语言及其缩写 */
require('con_language.php');
$con_lang_arr = $con_language_cn;

require('cls_matchhtml.php');

require('cls_chkrequest.php');

require('cls_attrdefine.php');

session_start();

?>