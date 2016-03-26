<?php
/////////////////////////////////////////
// 全站基本资料
/////////////////////////////////////////

if(1){
	$child_pre = 'xt_';
}


/* 设置数据库 */
if($_SERVER['SERVER_ADDR'] == "127.0.0.1" || $_SERVER['SERVER_ADDR'] == "::1"){
	$con_db_set = array('host' => 'localhost', 'user' => 'root', 'pass' => '');
	$con_db_name = 'xtclass';
}
else {
	$con_db_set = array('host' => 'localhost', 'user' => 'root', 'pass' => '');
	$con_db_name = 'xtclass';
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
	'arrange'   => $child_pre . 'arrange',
	'plan'      => $child_pre . 'plan',
	'teacher'   => $child_pre . 'teacher',
	'student'   => $child_pre . 'student',
	'purchase'  => $child_pre . 'purchase',
	'schedule'  => $child_pre . 'schedule',
	'system'    => $child_pre . 'system',
	'message'   => $child_pre . 'message',
	'language'  => $child_pre . 'language',
	'constant'  => $child_pre . 'constant',
	'log'       => $child_pre . 'log',
	'course'    => $child_pre . 'course',
	'evaluate'  => $child_pre . 'evaluate',
	'usage'     => $child_pre . 'usage',

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
	/*
	'role'       => array('系统角色', '添加角色', '编辑角色', '删除角色'),
	'admin'      => array('用户管理', '添加用户', '编辑用户', '删除用户'),
	'system'     => array('网站信息', '系统参数', '网站参数', '常用设置', '数据备份', '数据恢复', '网站日志'),
	'teacher'    => array('老师信息', '添加信息', '编辑信息', '删除信息'),
	'stuednt'    => array('学生信息', '添加信息', '编辑信息', '删除信息'),
	'module'     => array('院系班级', '添加院系班级', '编辑院系班级', '删除院系班级'),
	*/
	'course'     => array('实验课程', '添加课程', '编辑课程', '删除课程'),
	'plan'       => array('课程选择', '添加选课', '编辑选课', '删除选课'),
	
	'products'   => array('耗材内容', '添加耗材', '编辑耗材', '删除耗材'),
	'purchase'   => array('耗材申请', '添加申请', '审核申请', '删除申请', '确认申请'),
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
$cms_cata_type = array(1 => array('txt' => '院系', 'db' => 'catalog'), 2 => array('txt' => '班级', 'db' => 'catalog'));

/* 分类展示方式 */
$cms_cata_style = array(1 => '内容整页显示', 2 => '标题列表显示', 3 => '图片及描述排列显示', 4 => '图片及标题排列显示', 5 => '标题排列显示');

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

/* 资金动作 */
$cms_teacher_type = array(1 => '老师', 2 => '实验室准备员');

$cms_default_pass = '123456';

$cms_max_classes = 10;

$cms_product_status = array(0 => '待审批', 1 => '已审', 2 => '已购', 3 => '有库存', 4 => '自购');

?>