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
		'links' => 'lk',
		'info' => 'im',
		'products' => 'pr',
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

	// 多国语言
	'page'      => $child_pre . 'page_' . $con_lang_current,
	'catalog'   => $child_pre . 'catalog_' . $con_lang_current,
	'links'     => $child_pre . 'links_' . $con_lang_current,
	'info'      => $child_pre . 'info_' . $con_lang_current,
	'products'  => $child_pre . 'products_' . $con_lang_current,
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
	'role'       => array('系统角色', '添加角色', '编辑角色', '删除角色'),
	'admin'      => array('用户管理', '添加用户', '编辑用户', '删除用户'),
	'system'     => array('网站信息', '系统参数', '网站参数', '常用设置', '数据备份', '数据恢复', '网站日志'),
	'module'     => array('院系班级', '添加院系班级', '编辑院系班级', '删除院系班级'),
	'message'    => array('留言信息', '查看留言', '回复留言', '删除留言'),
);


/* 语言参照 */
$cms_lang_connect = '';

/* 最大数值 */
$cms_max_num = array('queue' => 10000000, 'power' => 1000);

/**
 * 分类模块
 * type: 0 留给其他
 */
$cms_cata_type = array(1 => array('txt' => '資料庫', 'db' => 'info'), 2 => array('txt' => '討論區', 'db' => 'info'), 3 =>array('txt' => '相片區', 'db' => 'info'));

/* 分类展示方式 */
$cms_cata_style = array(1 => '内容整页显示', 2 => '标题列表显示');

$cms_member_type = array(1 => '會員', 10 => '美容師');

/* 交互模块 */
$cms_msg_type = array(1 => '留言信息', 2 => '通知公告');

$cms_page_union = array(
	'catalog' => array('id' => 1000, 'fix' => 'c'), 
	'tag' => array('id' => 2000, 'fix' => 't'), 
	'info' => array('id' => 3000, 'fix' => 'i'), 
	'products' => array('id' => 4000, 'fix' => 'p')
);

$cms_page_set = array(
	array('priority' => 1, 'path' => 'index', 'type' => 1),
	array('priority' => 0.2, 'path' => 'sitemap', 'type' => 1),
	array('priority' => 0.5, 'path' => 'info', 'type' => 2),
	array('priority' => 0.5, 'path' => 'tag', 'type' => 2),
	array('priority' => 0.7, 'path' => 'catalog', 'type' => 2),
);

?>