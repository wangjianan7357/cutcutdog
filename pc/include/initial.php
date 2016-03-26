<?php

require('cls_cart.php');

/***************** define basic information *****************/

basicConstant();

$cur_data = array();
$cur_module = array();
$cur_catalog = array();

// catalog information
$info_list = array();
$products_list = array();
$links_list = array();
$catalog_list = array();

function resultFormat ($result) {
	if(isset($result['fields'])) {
		$result['fields'] = json_decode($result['fields'], true);
	}
	if (isset($result['price'])) {
		$result['price'] = number_format($result['price'], 2, '.', ',');
	}
	if (isset($result['sale'])) {
		$result['sale'] = number_format($result['sale'], 2, '.', ',');
	}

	return $result;
}


$getdata = $my_db->selectRow('*', 'catalog', array('valid = 1'), array('`queue` DESC, `date` ASC'));
while($result = mysql_fetch_array($getdata)){
	$catalog_list[0][$result['id']] = $result;
	$catalog_list[$result['type']][$result['id']] = $result;

	if($cur_page == 'catalog' && $_GET['path'] && $_GET['path'] == $result['path']) {
		$cur_catalog = $result;
	}

	$order = array();
	switch($result['order']){
		case 1: $order = array('field' => 'id', 'method' => 'ASC'); break;
		case 2: $order = array('field' => 'id', 'method' => 'DESC'); break;
		case 3: $order = array('field' => 'queue', 'method' => 'ASC'); break;
		case 4: $order = array('field' => 'queue', 'method' => 'DESC'); break;
		case 5: $order = array('field' => 'date', 'method' => 'ASC'); break;
		case 6: $order = array('field' => 'date', 'method' => 'DESC'); break;
		case 7: $order = array('field' => 'visitor', 'method' => 'ASC'); break;
		case 8: $order = array('field' => 'visitor', 'method' => 'DESC'); break;
		//default: $order = array('field' => 'date', 'method' => 'DESC'); break;
	}

	if ($order) {
		// $result['type'] 小于 4 为基础数据库，必需存在
		if($result['type'] < 4 || $my_db->tableExist($cms_cata_type[$result['type']]['db'])){
			$getdata1 = $my_db->selectRow('*', $cms_cata_type[$result['type']]['db'], array('`valid` = 1 AND `cid` REGEXP "(^|,)' . $result['id'] . ',$"'), $order);
			while ($result1 = mysql_fetch_array($getdata1)){
				$result1 = resultFormat($result1);
				eval('$' . $cms_cata_type[$result['type']]['db'] . '_list[$result1[\'id\']] = $result1;');
			}
		}
	}
}


foreach ($cms_cata_type as $value) {
	$check_page = false;

	$getdata = $my_db->selectRow('*', $value['db'], array('valid' => 1), array('`cid` ASC, `queue` DESC, `date` DESC'));
	while ($result = mysql_fetch_array($getdata)) {
		$result = resultFormat($result);
		eval('$' . $value['db'] . '_list[$result[\'id\']] = $result;');

		// 访问详细页面时
		if ($cur_page == $value['db'] && $_GET['path']) {
			$check_page = true;

			if ($_GET['path'] == $result['path']) {
				$cur_data = $result;
				$temp = explode(',', $result['cid']);
				$cur_catalog = $catalog_list[0][$temp[count($temp) - 2]];
			}

		} else if(!$cur_data && strpos($result['cid'], ($cur_catalog['parent'] . $cur_catalog['id'] . ',')) === 0) {
			// 当前分类的首个内容。!$cur_data 表示只做一次赋值
			$cur_data = $result;
		}
	}

	if ($check_page && !$cur_data) {
		header('HTTP/1.0 404 Not Found');
		exit;
	}
}


if ($cur_catalog) {
	$modules = explode(',', $cur_catalog['parent']);
	foreach ($modules as $value) {
		if ($value) {
			$cur_module[] = $catalog_list[0][$value];
		}
	}

	$cur_module[] = $cur_catalog;
	
} else if ($cur_page == 'catalog' && $_GET['path']){
	header('HTTP/1.0 404 Not Found');
	exit;
}

// recommend products
$products_recom = array();

$getdata = $my_db->selectRow('*', 'products', array('recom > 0 AND valid = 1'), array('field' => 'recom', 'method' => 'DESC'));
while($result = mysql_fetch_array($getdata)){
	if (isset($result['price'])) {
		$result['price'] = number_format($result['price'], 2, '.', ',');
	}
	if (isset($result['sale'])) {
		$result['sale'] = number_format($result['sale'], 2, '.', ',');
	}

	$products_recom[$result['id']] = $result;
}


$language_list = array();
$getdata = $my_db->selectRow('*', 'language', array('valid' => 1));
while($result = mysql_fetch_array($getdata)) $language_list[$result['id']] = $result;

/***************** define advanced information *****************/
$suffix = '';
$cur_id = array('catalog' => 0, 'info' => 0, 'products' => 0, 'tag' => 0);

switch($cur_page){
	case 'catalog': $suffix = '-c'; break;
	case 'info': $suffix = '-i'; break;
	case 'products': $suffix = '-p'; break;
	case 'tag': $suffix = '-t'; break;
}

$tmp['key1'] = '';

if($suffix){
	$getdata = $my_db->selectRow('id, name', $cur_page, array('path' => $_GET['path']));
	$result = mysql_fetch_array($getdata);
	$cur_id[$cur_page] = $result['id'];

	$tmp['key1'] = $result['name'];
}

$arr = array('key1', 'key2', 'title', 'keyword', 'description', 'desp', 'info');

$where = array('`path` = "' . $cur_page . '"' . ($suffix ? (' OR `path` = "' . $_GET['path'] . $suffix . '"') : ''));
$getdata = $my_db->selectRow('*', 'page', $where, array('field' => 'id', 'method' => 'ASC'));
while($result = mysql_fetch_array($getdata)){
	for($i = 0; $i < count($arr); $i++){
		if($result[$arr[$i]]) $tmp[$arr[$i]] = $result[$arr[$i]];
	}
}

for($i = 0; $i < count($arr); $i++){
	define('P_' . strtoupper($arr[$i]), tr_code($tmp[$arr[$i]]));
}

?>