<?php

/* 忽略的网站项目 */
$ignore_project = array(
	//'table' => array('dt_province' => 1, 'dt_city' => 1, 'dt_area' => 1),
);

/* 网站基本信息 */
$power = '';
foreach($cms_admin_power as $key => $value){
	$temp = '';
	for($i = 1; $i < count($value); $i++){
		if($ignore_project['power'][$key] == $i || $ignore_project['power'][$key] == 'all') $temp .= 0;
		else $temp .= 1;
	}
	// 让前面的 0 不被省掉
	$power .= bindec(1 . $temp) . ',';
}

$ini_role = array('name' => '超级管理员', 'power' => $power);

$ini_admin = array('name' => 'admin', 'pass' => md5(substr($child_pre, 0, 2)), 'rid' => 1, 'realname' => 'admin');

$ini_language = array(
	array('id' => 1, 'abbr' => $con_lang_default, 'connect' => '', 'valid' => 1),
);

$ini_system = array(
	1 => array(
		array('varname' => 'clear_cache', 'info' => '系统清理时间', 'type' => 'hidden', 'value' => date('Ymd')),
		array('varname' => 'fullsize_display_dialog', 'info' => '全屏显示对话框', 'type' => 'hidden', 'value' => 'true'),
		array('varname' => 'page_cache_time', 'info' => '页面缓存有效时间，0 为不缓存(秒)', 'type' => 'hidden', 'value' => 0),
		array('varname' => 'info_order', 'info' => '信息排序方式', 'type' => 'hidden', 'value' => '|按ID升序|按ID降序|按序列号升序|按序列号降序|按发布日期升序|按发布日期降序|按浏览量升序|按浏览量降序'),
		array('varname' => 'receive_email', 'info' => '收信箱(用;区分)', 'type' => 'string', 'value' => 'wangjianan7357@163.com'),
		array('varname' => 'img_format', 'info' => '可用的图片格式(用,区分)', 'type' => 'hidden', 'value' => 'jpeg,pjpeg,gif,png'),
		array('varname' => 'img_max_bytes', 'info' => '缩略图最大上传量(kb)', 'type' => 'integer', 'value' => 1500),
		array('varname' => 'img_max_size', 'info' => '图片最大尺寸(px)', 'type' => 'integer', 'value' => 800),
		array('varname' => 'img_mid_size', 'info' => '中图尺寸(px)', 'type' => 'integer', 'value' => 350),
		array('varname' => 'img_sml_size', 'info' => '小图尺寸(px)', 'type' => 'integer', 'value' => 145),
		array('varname' => 'info_list_qty', 'info' => '信息列表显示数量', 'type' => 'integer', 'value' => 20),
		array('varname' => 'products_list_qty', 'info' => '产品列表显示数量', 'type' => 'integer', 'value' => 20),
		array('varname' => 'member_list_qty', 'info' => '会员列表显示数量', 'type' => 'integer', 'value' => 15),
		array('varname' => 'file_path', 'info' => '文件存放路径', 'type' => 'hidden', 'value' => 'uploads/files/'),
		array('varname' => 'textbook_path', 'info' => '教材存放路径', 'type' => 'hidden', 'value' => 'uploads/textbook/'),
		array('varname' => 'cms_display_qty', 'info' => '后台列表显示数量', 'type' => 'integer', 'value' => 20),
		array('varname' => 'auto_refresh_path', 'info' => '自动更新路径', 'type' => 'hidden', 'value' => 'false'),
		array('varname' => 'index_tag_path', 'info' => '索引标签路径', 'type' => 'hidden', 'value' => 'false'),
		array('varname' => 'relate_image_name', 'info' => '缩略图名称关联路径', 'type' => 'hidden', 'value' => 'true'),
		array('varname' => 'path_max_length', 'info' => '路径参数最大长度', 'type' => 'hidden', 'value' => '25'),
		array('varname' => 'mail_setting', 'info' => '邮箱设置(SMTP,account,password)', 'type' => 'hidden', 'value' => ''),
		array('varname' => 'auto_clear_log', 'info' => '自动清理过往的日志(天)', 'type' => 'hidden', 'value' => 20),
		array('varname' => 'cms_login_time', 'info' => '登录超时退出(分钟)', 'type' => 'integer', 'value' => '60'),
	),

	2 => array(
		array('varname' => 'webname', 'info' => '网站名称', 'type' => 'string', 'lang' => $con_lang_default, 'value' => ''),
		array('varname' => 'person', 'info' => '联系人', 'type' => 'string', 'lang' => $con_lang_default),
		array('varname' => 'job', 'info' => '职位', 'type' => 'string', 'lang' => $con_lang_default),
		array('varname' => 'tel1', 'info' => '电话1', 'type' => 'string', 'lang' => $con_lang_default, 'value' => ''),
		array('varname' => 'tel2', 'info' => '电话2', 'type' => 'string', 'lang' => $con_lang_default, 'value' => ''),
		array('varname' => 'mob1', 'info' => '移动电话1', 'type' => 'string', 'lang' => $con_lang_default, 'value' => ''),
		array('varname' => 'mob2', 'info' => '移动电话2', 'type' => 'string', 'lang' => $con_lang_default),
		array('varname' => 'fax', 'info' => '传真', 'type' => 'string', 'lang' => $con_lang_default, 'value' => ''),
		array('varname' => 'qq1', 'info' => 'QQ1', 'type' => 'integer', 'lang' => $con_lang_default),
		array('varname' => 'qq2', 'info' => 'QQ2', 'type' => 'integer', 'lang' => $con_lang_default),
		array('varname' => 'msn', 'info' => 'MSN', 'type' => 'string', 'lang' => $con_lang_default),
		array('varname' => 'skype', 'info' => 'SKYPE', 'type' => 'string', 'lang' => $con_lang_default),
		array('varname' => 'email', 'info' => 'Email', 'type' => 'string', 'lang' => $con_lang_default),
		array('varname' => 'web1', 'info' => '网址1', 'type' => 'string', 'lang' => $con_lang_default),
		array('varname' => 'address1', 'info' => '地址1', 'type' => 'string', 'lang' => $con_lang_default, 'value' => ''),
		array('varname' => 'address2', 'info' => '地址2', 'type' => 'string', 'lang' => $con_lang_default),
		array('varname' => 'country', 'info' => '国家', 'type' => 'string', 'lang' => $con_lang_default),
		array('varname' => 'zipcode', 'info' => '邮编', 'type' => 'string', 'lang' => $con_lang_default, 'value' => ''),
		array('varname' => 'copyright', 'info' => '版权信息', 'type' => 'string', 'lang' => $con_lang_default)
	)
);

$ini_constvar = array (
	array(
		'various' => 'ABOUT_US',
		'entxt' => 'About Us'
	),
	array(
		'various' => 'ADDRESS',
		'entxt' => 'Address'
	),
	array(
		'various' => 'ADD_FAVOR',
		'entxt' => 'Add favor'
	),
	array(
		'various' => 'ADD_TO_CART',
		'entxt' => 'Add to Cart',
	),
	array(
		'various' => 'BIG',
		'entxt' => 'Big',
	),
	array(
		'various' => 'BRAND',
		'entxt' => 'Brand',
	),
	array(
		'various' => 'BROWSE',
		'entxt' => 'Browse',
	),
	array(
		'various' => 'CART_IS_EMPTY',
		'entxt' => 'Cart is empty',
	),
	array(
		'various' => 'CATEGORIES',
		'entxt' => 'Categories',
	),
	array(
		'various' => 'CHECK_OUT',
		'entxt' => 'Check out',
	),
	array(
		'various' => 'CHINA',
		'entxt' => 'China',
	),
	array(
		'various' => 'COMPANYNAME',
		'entxt' => 'Company name',
	),
	array(
		'various' => 'CODE',
		'entxt' => 'Code',
	),
	array(
		'various' => 'CONFIRM_PASSWORD',
		'entxt' => 'Confirm password',
	),
	array(
		'various' => 'CONTACT_US',
		'entxt' => 'Contact Us',
	),
	array(
		'various' => 'COPYRIGHT',
		'entxt' => 'Copyright',
	),
	array(
		'various' => 'CREATE_ACCOUNT',
		'entxt' => 'Create account',
	),
	array(
		'various' => 'CUSTOMER_SERVICE',
		'entxt' => 'Customer Service',
	),
	array(
		'various' => 'DATE',
		'entxt' => 'Date',
	),
	array(
		'various' => 'DESCRIPTION',
		'entxt' => 'Description',
	),
	array(
		'various' => 'EMPTY',
		'entxt' => 'Empty',
	),
	array(
		'various' => 'E_MAIL',
		'entxt' => 'Email',
	),
	array(
		'various' => 'E_MAIL_INVALID',
		'entxt' => 'Email is invalid',
	),
	array(
		'various' => 'FAX',
		'entxt' => 'Fax',
	),
	array(
		'various' => 'FIRST_NAME',
		'entxt' => 'First Name',
	),
	array(
		'various' => 'FONT',
		'entxt' => 'Font',
	),
	array(
		'various' => 'GENDER',
		'entxt' => 'Gender',
	),
	array(
		'various' => 'HOT_PRODUCTS',
		'entxt' => 'Hot products',
	),
	array(
		'various' => 'INDEX',
		'entxt' => 'Home',
	),
	array(
		'various' => 'INVALID',
		'entxt' => 'Invalid',
	),
	array(
		'various' => 'LANGUAGE',
		'entxt' => 'Language',
	),
	array(
		'various' => 'LAST_NAME',
		'entxt' => 'Last Name',
	),
	array(
		'various' => 'LOGOUT',
		'entxt' => 'Logout',
	),
	array(
		'various' => 'MANUFACTURER',
		'entxt' => 'Manufacturer',
	),
	array(
		'various' => 'MEMBER_CENTER',
		'entxt' => 'Member Center',
	),
	array(
		'various' => 'MESSAGE',
		'entxt' => 'Message',
	),
	array(
		'various' => 'MIDDLE',
		'entxt' => 'Middle',
	),
	array(
		'various' => 'MISS',
		'entxt' => 'Miss.',
	),
	array(
		'various' => 'MOBILE',
		'entxt' => 'Mobile',
	),
	array(
		'various' => 'MODEL',
		'entxt' => 'Model',
	),
	array(
		'various' => 'MORE',
		'entxt' => 'More',
	),
	array(
		'various' => 'MR',
		'entxt' => 'Mr.',
	),
	array(
		'various' => 'MY_ACCOUNT',
		'entxt' => 'My account',
	),
	array(
		'various' => 'MY_CART',
		'entxt' => 'My Cart',
	),
	array(
		'various' => 'MY_ORDER',
		'entxt' => 'My Order',
	),
	array(
		'various' => 'MY_PROFILE',
		'entxt' => 'My Profile',
	),
	array(
		'various' => 'NAME',
		'entxt' => 'Name',
	),
	array(
		'various' => 'NEWS',
		'entxt' => 'News',
	),
	array(
		'various' => 'NEXT',
		'entxt' => 'Next',
	),
	array(
		'various' => 'NEW_PASSWORD',
		'entxt' => 'New Password',
	),
	array(
		'various' => 'NO_SUCH_PRODUCT',
		'entxt' => 'No such product!',
	),
	array(
		'various' => 'NUMBER',
		'entxt' => 'Number',
	),
	array(
		'various' => 'OLD_PASSWORD',
		'entxt' => 'Old Password',
	),
	array(
		'various' => 'ORDER_NO',
		'entxt' => 'Order No.',
	),
	array(
		'various' => 'OUR_PRODUCTS',
		'entxt' => 'Our Products',
	),
	array(
		'various' => 'PASSWORD',
		'entxt' => 'Password',
	),

	array(
		'various' => 'PASSWORD_INCORRECT',
		'entxt' => 'Password incorrect',
	),
	array(
		'various' => 'PRICE',
		'entxt' => 'Price',
	),
	array(
		'various' => 'PRODUCT',
		'entxt' => 'Product',
	),
	array(
		'various' => 'PLEASE_ENTER',
		'entxt' => 'Please enter',
	),
	array(
		'various' => 'PLEASE_LOGIN',
		'entxt' => 'Please login!',
	),
	array(
		'various' => 'PLEASE_SELECT',
		'entxt' => 'Please select',
	),
	array(
		'various' => 'PREVIOUS',
		'entxt' => 'Previous',
	),
	array(
		'various' => 'PRODUCTS',
		'entxt' => 'Products',
	),
	array(
		'various' => 'PRODUCTS_CATALOG',
		'entxt' => 'Products Catalog',
	),
	array(
		'various' => 'PRODUCTS_CENTER',
		'entxt' => 'Products Center',
	),
	array(
		'various' => 'PRODUCTS_DESCRIPTION',
		'entxt' => 'Products Description',
	),
	array(
		'various' => 'PRODUCTS_SEARCH',
		'entxt' => 'Products Search',
	),
	array(
		'various' => 'READ_MORE',
		'entxt' => 'Read more',
	),
	array(
		'various' => 'REGISTER_NEW',
		'entxt' => 'Register for a new account',
	),
	array(
		'various' => 'REQUEST_FAILED',
		'entxt' => 'Request Failed',
	),
	array(
		'various' => 'REQUIRED',
		'entxt' => 'Required',
	),
	array(
		'various' => 'SIGN_IN',
		'entxt' => 'Sign In',
	),
	array(
		'various' => 'SIMILAR_PRODUCTS',
		'entxt' => 'Similar products',
	),
	array(
		'various' => 'SITEMAP',
		'entxt' => 'Sitemap',
	),
	array(
		'various' => 'SMALL',
		'entxt' => 'Small',
	),
	array(
		'various' => 'SEARCH',
		'entxt' => 'Search',
	),
	array(
		'various' => 'STATUS',
		'entxt' => 'Status',
	),
	array(
		'various' => 'SUBJECT',
		'entxt' => 'Subject',
	),
	array(
		'various' => 'SUBMIT',
		'entxt' => 'Submit',
	),
	array(
		'various' => 'SUBMIT_FAIL',
		'entxt' => 'Submit fail',
	),
	array(
		'various' => 'SUBMIT_SUCCESS',
		'entxt' => 'Submit success',
	),
	array(
		'various' => 'TEL',
		'entxt' => 'Tel',
	),
	array(
		'various' => 'TOTAL',
		'entxt' => 'Total',
	),
	array(
		'various' => 'VERIFY_CODE',
		'entxt' => 'Verify Code',
	),
	array(
		'various' => 'VIDEO',
		'entxt' => 'Video',
	),
	array(
		'various' => 'YOU_ARE_HERE',
		'entxt' => 'You are here',
	),
);


$ini_catalog = array(

);
          
// 开始导入数据

foreach($con_db_table as $key => $value){
	if($ignore_project['table'][$key]) continue;
	
	$my_db->createTable($key);
}

$my_db->saveRow('role', $ini_role);

$my_db->saveRow('admin', $ini_admin);

$my_db->alterTable('constant', array($con_lang_default . 'txt' => 'TEXT'), 'add');

foreach($ini_constvar as $value){
	//$my_db->saveRow('constant', $value);	
}

foreach($ini_language as $key => $value){
	$my_db->saveRow('language', $value);
}

foreach($ini_system as $key => $value){
	for($i = 0; $i < count($ini_system[$key]); $i++){
		if(!isset($ini_system[$key][$i]['value'])) $ini_system[$key][$i]['value'] = '';
		$temp = $ini_system[$key][$i] + array('tid' => $key);
		$my_db->saveRow('system', $temp);
	}
}


$i = 0;
for($j = 0; $j < count($ini_catalog); $j++){
	$i++;
	$my_db->saveRow('catalog', array('name' => $ini_catalog[$j]['name'], 'path' => $ini_catalog[$j]['path'], 'valid' => 1, 'parent' => $ini_catalog[$j]['parent'], 'src' => $ini_catalog[$j]['src'], 'style' => (int)$ini_catalog[$j]['style'], 'queue' => (int)$ini_catalog[$j]['queue'], 'type' => $ini_catalog[$j]['type'], 'navi' => (int)$ini_catalog[$j]['navi'], 'fields' => $ini_catalog[$j]['fields']));
}

$i = 0;
for($j = 0; $j < count($ini_tag); $j++){
	$i++;
	$my_db->saveRow('tag', $ini_tag[$j]);
}

foreach($cms_page_set as $key => $value){
	$value['title'] = '';
	$value['keyword'] = '';
	$value['description'] = '';
	$value['desp'] = '';
	$value['info'] = '';
	$value['fields'] = '';

	$my_db->saveRow('page', $value);
}

$msg = '数据库创建完成。';

?>