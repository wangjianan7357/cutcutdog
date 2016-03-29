<?php
require('../include/common.php');
require('../include/fun_admin.php');

$err = '';
$msg = array();

if($_GET['action'] == 'edt'){
	if($_GET['num']) $power_id = 2;
	else if($_POST['del'] == 'true') $power_id = 3;
	else $power_id = 1;

	if($_GET['num']){
		$outcome = $my_db->fetchOne('catalog', array('id' => $_GET['num']));
	}
	else $outcome['valid'] = true;

	if($_POST['del'] == 'true'){
		if(!adminPower('member', $power_id)) warning('权限不足');
		else delSelectedData('member', array('id' => $power_id), $catalog_src);
	}
	else if($_POST['save'] == 'true'){
		if(!adminPower('member', $power_id)) warning('权限不足');

		$_POST['sbt_name'] = trim($_POST['sbt_name']);

		$chk_post = new ChkRequest('sbt_');
		$chk_post->chkEmpty(array('name' => '名称'));

		$_POST['sbt_src'] = $chk_post->chkImage('img');
		
		if ($outcome['src']) {
			preg_match('/(\.[\w]{3,4})$/', $_POST['sbt_src'], $match);
			$_POST['sbt_src'] = preg_replace('/\.[\w]{3,4}$/', '', $outcome['src']) . $match[1];
			$outcome['src'] = $_POST['sbt_src'];
		}

		if(!$err){
			$submit_arr = initSubmitColumns('member', $_GET['num']);

			$_POST['sbt_id'] = $_GET['num'] ? $_GET['num'] : ($my_db->selectMax('member') + 1);
			$_POST['sbt_type'] = $catalog_type;
			$_POST['sbt_queue'] = min(max((int)$_POST['sbt_queue'], 0), $cms_max_num['queue']);
			$_POST['sbt_valid'] = ($_POST['sbt_valid'] ? 1 : 0);


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

				if($_GET['num']){
					$href = $_SERVER['PHP_SELF'] . '?action=lst' . preg_replace('/action=[^&]+|&num=\d+/', '', $_SERVER['QUERY_STRING']);
					header('Location: ' . $href . '&msg[]=' . urlencode($msg[0]) . '&msg[]=' . $msg[1]);
				} else {
					$outcome['parent'] = $_POST['sbt_parent'];
				}
			}
			else {
				mysql_query("ROLLBACK");
				mysql_query("END");
				$msg[0] = $cms_admin_power['catalog'][$power_id] . '失败';
				$msg[1] = 'fail';
			}
		}
	}
}
else if($_GET['action'] == 'lst'){
	$catalog_all = array();
	$getdata = $my_db->selectRow('*', 'catalog', array('type' => $catalog_type));
	while($result = mysql_fetch_array($getdata)) $catalog_all[$result['id']] = $result;

	$q_url = queryPart('date', 'desc');

	$cata_order = explode('|', systemConfig('info_order'));
	unset($cata_order[0]);

	$where = '`parent` != "" AND `type` = ' . $catalog_type . ($_GET['cid'] ? ' AND `parent` REGEXP "' . $_GET['cid'] . '"' : '');
	
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
					return $str[0] ? $catalog_all[preg_replace('/(^[\d,]+,|^)(\d+),$/', '\\2', $str[0])]['name'] : '<font color="red">未分类</font>';
			}
		}
	}

	$code_table = tableFields(
		array(
			array('__all', 'edit'),
			'id' => 'ID', 
			'name' => array('名称', 'text'), 
			'parent' => array('上级', 'select', array(new FieldFun())),
			'valid' => array('有效', 'checkbox'),
			array('__edit', 'edit', array('power' => 'catalog', 'method' => array('quick' => 2, 'detail' => 2)))
		),
		array(
			'where' => $where,
			'table' => 'catalog',
			'operate' => array('edt', 'delete')
		)
	);	
}

require('templates/head.html');
require('templates/catalog.html');
require('templates/foot.html');