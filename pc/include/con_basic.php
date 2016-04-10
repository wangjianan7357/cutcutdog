<?php
/////////////////////////////////////////
// 全站基本资料
/////////////////////////////////////////

if(1){
	$child_pre = 'cut_';
}


/* 设置数据库 */
if($_SERVER['SERVER_ADDR'] == "127.0.0.1" || $_SERVER['SERVER_ADDR'] == "::1"){
	$con_db_set = array('host' => 'localhost', 'user' => 'root', 'pass' => '');
	$con_db_name = 'cutcutdog';
}
else {
	$con_db_set = array('host' => 'localhost', 'user' => 'iphones1_web1', 'pass' => 'wang025971');
	$con_db_name = 'iphones1_web1';
}

/* 设置语言 */
$con_lang_default = 'cn';

/* 设置邮件服务器 */
$con_mail_set = array('host' => '', 'name' => '', 'pass' => '');

/* 设置数据备份文件夹 */
$con_db_bakup = 'backup/' . $child_pre;

/* 设置上传临时文件夹 */
$con_tmp_dir = 'uploads/tmp/';

/* 设置缩略图前后缀 */
$con_pic = array(
	'suf' => array(
		'big' => 'b',
		'mid' => 'm',
		'sml' => 's'
	),
	'pre' => array(
		'catalog' => 'ct',
		'info' => 'im',
		'product' => 'pr',
		'member' => 'me'
	)
);

$con_lang_current = $_GET['lang'] ? $_GET['lang'] : ($_COOKIE['cookie']['lang'] ? $_COOKIE['cookie']['lang'] : $con_lang_default);

/* 设置数据表 */
// 数据表名须大于两个字符，避免和语言缩写冲突
$con_db_table = array(
	'role'      => $child_pre . 'role',
	'admin'     => $child_pre . 'admin',
	'member'   	=> $child_pre . 'member',
	'message'   => $child_pre . 'message',
	'likes'   	=> $child_pre . 'likes',
	'language'  => $child_pre . 'language',
	'constant'  => $child_pre . 'constant',
	'service'   => $child_pre . 'service',
	'log'       => $child_pre . 'log',
	'system'    => $child_pre . 'system',
	'verify'    => $child_pre . 'verify',
	'order'     => $child_pre . 'order',

	// 多国语言
	'page'      => $child_pre . 'page_' . $con_lang_current,
	'catalog'   => $child_pre . 'catalog_' . $con_lang_current,
	'info'      => $child_pre . 'info_' . $con_lang_current,
	'product'   => $child_pre . 'product_' . $con_lang_current,
	'tag'       => $child_pre . 'tag_' . $con_lang_current
);

/////////////////////////////////////////
// CMS 基本资料
/////////////////////////////////////////

/** 
 * 管理员权限（以二进制判断权限，以十进制记录进数据库）
 * 超级管理员才能添加管理角色
 * 管理角色可被赋予管理注册会员的权限
 * 数组说明：
 * 管理范围 => array(管理标题, 管理内容1, 管理内容2, ...)
 * 管理所属[] => array(管理标题, 数据表/需要有id字段, 字段, 条件, ...)
 * 以下信息会影响日志记录及页面标题
 */
$cms_admin_power = array(
	'system'     => array('网站信息', '系统参数', '网站参数', '常用设置', '数据备份', '数据恢复', '网站日志'),
	'role'       => array('角色管理', '添加角色', '编辑角色', '删除角色'),
	'admin'      => array('用户管理', '添加管理', '编辑管理', '删除管理'),
	'catalog'    => array('頻道分類', '添加分類', '编辑分類', '删除分類'),
	'info'   	 => array('資訊內容', '添加資訊', '编辑資訊', '删除資訊'),
	'product'    => array('產品內容', '添加產品', '编辑產品', '删除產品'),
	'service'    => array('服務項目', '添加服務', '编辑服務', '删除服務'),
	'message'    => array('互動留言', '查看留言', '回复留言', '删除留言'),
	'member'   	 => array('會員用戶', '添加用戶', '编辑用戶', '删除用戶'),
);


/* 语言参照 */
$cms_lang_connect = '';

/* 最大数值 */
$cms_max_num = array('queue' => 10000000, 'power' => 1000);

/**
 * 分类模块
 * type: 0 留给其他
 */
$cms_cata_type = array(
	1 => array('txt' => '圖片廣告', 'db' => 'info', 'ico' => 'fa-picture-o'), 
	3 => array('txt' => '資料庫', 'db' => 'info', 'ico' => 'fa-briefcase'), 
	4 => array('txt' => '討論區', 'db' => 'info', 'ico' => 'fa-quote-left'), 
	7 => array('txt' => '產品', 'db' => 'product', 'ico' => 'fa-shopping-cart')
);

/* 分类展示方式 */
$cms_cata_style = array(1 => '内容整页显示', 2 => '标题列表显示');

/* 会员类型 */
$cms_member_type = array(1 => '會員', 10 => '美容師');

/* 交互模块 */
$cms_msg_type = array(1 => '在線評論', 2 => '在線預約');

$cms_page_union = array(
	'catalog' => array('id' => 1000, 'fix' => 'c'), 
	'tag' => array('id' => 3000, 'fix' => 't'), 
	'product' => array('id' => 5000, 'fix' => 'p'),
	'info' => array('id' => 7000, 'fix' => 'i'), 
);

$cms_page_set = array(
	array('priority' => 1, 'path' => 'index', 'type' => 1),
	array('priority' => 0.2, 'path' => 'sitemap', 'type' => 1),
	array('priority' => 0.5, 'path' => 'info', 'type' => 2),
	array('priority' => 0.5, 'path' => 'product', 'type' => 2),
	array('priority' => 0.5, 'path' => 'tag', 'type' => 2),
	array('priority' => 0.7, 'path' => 'catalog', 'type' => 2),
);

?>