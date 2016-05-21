<?php
require('../include/common.php');
require('../include/fun_admin.php');

$err = '';
$msg = $_GET['msg'] ? $_GET['msg'] : array();

$catalog_type = (int)($_GET['type'] ? $_GET['type'] : ($_POST['sbt_type'] ? $_POST['sbt_type'] : 1));

if($_GET['action'] == 'edt'){
	if($_GET['num']) $power_id = 2;
	else if($_POST['del'] == 'true') $power_id = 3;
	else $power_id = 1;

	if($_GET['num']){
		$outcome = $my_db->fetchOne('catalog', array('id' => $_GET['num']));
	}
	else $outcome['valid'] = true;

	if($_POST['del'] == 'true'){
		if(!adminPower('catalog', $power_id)) warning('權限不足');
		else delSelectedData('catalog', array('id' => $power_id), '../' . systemConfig('catalog_img_path') . $con_pic['pre']['catalog']);
	}
	else if($_POST['save'] == 'true'){
		//if(!adminPower('catalog', $power_id)) warning('權限不足');

		if ($_GET['batch'] == 'true') {
			if ($_GET['num']) {
				foreach ($outcome as $key => $value) {
					if (!isset($_POST['sbt_' . $key]) && $key != 'src' && $key != 'fields') {
						$_POST['sbt_' . $key] = $value;
					}
				}
			}
			else exit;
		}

		$chk_post = new ChkRequest('sbt_');
		$chk_post->chkEmpty(array('name' => '名稱'));

		$_POST['sbt_name'] = trim($_POST['sbt_name']);
		$_POST['sbt_path'] = $chk_post->traFromName('name', array('name' => 'catalog', 'field' => 'path'), $outcome['path']);
		$_POST['sbt_src'] = $chk_post->chkImage('img');
		
		if ($outcome['src']) {
			preg_match('/(\.[\w]{3,4})$/', $_POST['sbt_src'], $match);
			$_POST['sbt_src'] = preg_replace('/\.[\w]{3,4}$/', '', $outcome['src']) . $match[1];
			$outcome['src'] = $_POST['sbt_src'];
		}

		if($_POST['sbt_src'] && !$cms_lang_connect && systemConfig('relate_image_name')) {
			preg_match('/(\.[\w]{3,4})$/', $_POST['sbt_src'], $match);
			$_POST['sbt_src'] = '-' . $_POST['sbt_path'] . strtolower($match[1]);
		}

		if(!$err){
			$submit_arr = initSubmitColumns('catalog', $_GET['num']);

			$_POST['sbt_id'] = $_GET['num'] ? $_GET['num'] : ($my_db->selectMax('catalog') + 1);
			$_POST['sbt_type'] = $catalog_type;
			$_POST['sbt_queue'] = min(max((int)$_POST['sbt_queue'], 0), $cms_max_num['queue']);
			$_POST['sbt_order'] = (int)$_POST['sbt_order'];
			$_POST['sbt_style'] = (int)$_POST['sbt_style'];
			$_POST['sbt_valid'] = $_POST['sbt_valid'] ? 1 : 0;
			$_POST['sbt_navi'] = $_POST['sbt_navi'] ? 1 : 0;
			$_POST['sbt_parent'] = $_POST['sbt_parent'] ? $_POST['sbt_parent'] : '';
			$_POST['sbt_fields'] = '';

			$parent_arr = explode(',', $_POST['sbt_parent']);
			switch($parent_arr[0]){
				case 1: $_POST['sbt_fields_use'] = 4; break;
			}

			function edtCatalogInfo($lang = ''){
				global $my_db;
				global $cms_cata_type;
				global $catalog_type;
				global $cms_page_union;
				
				$done = 1;
				$catalog_db = $cms_cata_type[$catalog_type]['db'];

				if(!$_GET['num']) return $done;

				// 更新其子类信息
				$getdata = $my_db->selectRow('id, parent', 'catalog', array('`parent` REGEXP "(^|,)' . $_GET['num'] . '," AND `type` = ' . $catalog_type), '', '', $lang);
				while($result = mysql_fetch_array($getdata)){
					$result['parent'] = preg_replace('/(^[\d,]*,|^)(' . $_GET['num'] . ',)/', $_POST['sbt_parent'] . '\\2', $result['parent']);
					$done &= $my_db->saveRow('catalog', array('parent' => $result['parent']), array('id' => $result['id']), $lang);
				}

				// 更新页面路径
				$done &= $my_db->saveRow('page', array('path' => $_POST['sbt_path'] . '-c'), array('id' => ($cms_page_union['catalog']['id'] + $_GET['num'])), $lang);

				$getdata = $my_db->selectRow('id, cid', $catalog_db, array('`cid` REGEXP "(^|,)' . $_GET['num'] . ',"'), '', '', $lang);
				while($result = mysql_fetch_array($getdata)){
					$tmp = array();
					$tmp['cid'] = $_POST['sbt_parent'] . preg_replace('/(^|[\d,]+,)(' . $_GET['num'] . ',)/', '\\2', $result['cid']);
					if($catalog_type == 1) $tmp['tid'] = 0;

					$done &= $my_db->saveRow($catalog_db, $tmp, array('id' => $result['id']), $lang);
				}

				return $done;
			}

			$submit = array();
			$submit_lan = array();
			for($i = 0; $i < count($submit_arr); $i++){
				if(!isset($_POST['sbt_' . $submit_arr[$i]])) continue;
				$submit += array($submit_arr[$i] => $_POST['sbt_' . $submit_arr[$i]]);
				if($submit_arr[$i] != 'name' || !$_GET['num']) $submit_lan += array($submit_arr[$i] => $_POST['sbt_' . $submit_arr[$i]]);
			}

			mysql_query('BEGIN');

			$done = 1;
			$done &= $my_db->saveRow('catalog', $submit, ($_GET['num'] ? array('id' => $_GET['num']) : ''));
			$done &= edtCatalogInfo($con_lang_current);

			$getdata = $my_db->selectRow('*', 'language', array('connect' => $con_lang_current));
			while($result = mysql_fetch_array($getdata)){
				$done &= $my_db->saveRow('catalog', $submit_lan, ($_GET['num'] ? array('id' => $_GET['num']) : ''), $result['abbr']);
				$done &= edtCatalogInfo($result['abbr']);
			}

			if($done){
				mysql_query("COMMIT");
				mysql_query("END");

				$catalog_src = '../' . systemConfig('catalog_img_path') . $con_pic['pre']['catalog'];

				if($_FILES['sbt_img']['tmp_name']){
					$imgpath = $catalog_src . $_POST['sbt_src'];
					if(file_exists($imgpath)) unlink($imgpath);
					if(file_exists($_POST['tmp_img'])) unlink($_POST['tmp_img']);

					move_uploaded_file($_FILES['sbt_img']['tmp_name'], $imgpath);
				}

				// 图片名称更新
				if($outcome['src'] && $_POST['sbt_src'] != $outcome['src']) {
					$img_path = $catalog_src;

					if (file_exists($img_path . $outcome['src'])) {
						copy($img_path . $outcome['src'], $img_path . $_POST['sbt_src']);
						unlink($img_path . $outcome['src']);
					}
				}

				instructLog($cms_admin_power['catalog'][$power_id] . $_POST['sbt_name'], ($poser_id == 1 ? 'add' : 'edt'));
				$msg[0] = $cms_admin_power['catalog'][$power_id] . '成功';
				$msg[1] = 'success';

				$href = $_SERVER['PHP_SELF'] . '?action=lst' . preg_replace('/action=[^&]+|&num=\d+/', '', $_SERVER['QUERY_STRING']);
				header('Location: ' . $href . '&msg[]=' . urlencode($msg[0]) . '&msg[]=' . $msg[1]);
			}
			else {
				mysql_query("ROLLBACK");
				mysql_query("END");
				$msg[0] = $cms_admin_power['catalog'][$power_id] . '失败';
				$msg[1] = 'danger';
			}
		}
	}

} else {
	$catalog_all = array();
	$getdata = $my_db->selectRow('*', 'catalog', array('type' => $catalog_type));
	while($result = mysql_fetch_array($getdata)) $catalog_all[$result['id']] = $result;

	$q_url = queryPart('date', 'desc');

	$cata_order = explode('|', systemConfig('info_order'));
	unset($cata_order[0]);

	$where = '`type` = ' . $catalog_type . ($_GET['cid'] ? ' AND `parent` REGEXP "' . $_GET['cid'] . '"' : '');
	
	class FieldFun {
		function __construct($namespace = 1){
			$this->namespace = $namespace;
		}

		function __call($method, $str) {
			switch ($this->namespace . '_' . $method) {
				case '1_fun1':
					global $catalog_type;
					return catalogOption($catalog_type, $str[0], $_GET['num']);
				case '1_fun2':
					global $catalog_all;
					return $str[0] ? $catalog_all[preg_replace('/(^[\d,]+,|^)(\d+),$/', '\\2', $str[0])]['name'] : '';
			}
		}
	}

	$code_table = tableFields(
		array(
			array('__all', 'edit'),
			'id' => 'ID', 
			'name' => array('名稱', 'text'), 
			'parent' => array('上級', 'select', array(new FieldFun())),
			'valid' => array('狀態', 'checkbox'),
			array('__edit', 'edit', array('power' => 'catalog', 'method' => array('quick' => 2, 'detail' => 2)))
		),
		array(
			'where' => $where,
			'table' => 'catalog',
		)
	);	
}

require('templates/head.html');
require('templates/catalog.html');
require('templates/foot.html');