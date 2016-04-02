<?php
require('../include/common.php');
require('../include/fun_admin.php');

$err = '';
$msg = array();

$_GET['type'] = $_GET['type'] ? intval($_GET['type']) : 1;

$department = array();
$getdata = $my_db->selectRow('id, name', 'catalog', array('`type` = 1 AND `parent` = 0'), array('valid' => 1));
while ($res = mysql_fetch_array($getdata)) {
    $department[$res['id']] = $res['name'];
}

$module_src = '../' . systemConfig('catalog_img_path') . $con_pic['pre']['catalog'];

if($_GET['action'] == 'edt'){
	if($_GET['num']) $power_id = 2;
	else if($_POST['del'] == 'true') $power_id = 3;
	else $power_id = 1;

	if($_GET['num']){
		$outcome = $my_db->fetchOne('catalog', array('id' => $_GET['num']));
	}
	else $outcome['valid'] = true;

	if($_POST['del'] == 'true'){
		if(!adminPower('module', $power_id)) warning('權限不足');
		else delSelectedData('catalog', array('id' => $power_id, 'name' => 'module'));
	}
	else if($_POST['save'] == 'true'){
		if(!adminPower('module', $power_id)) warning('權限不足');

		$_POST['sbt_name'] = trim($_POST['sbt_name']);

		$chk_post = new ChkRequest('sbt_');
		$chk_post->chkEmpty(array('name' => '名称'));

		$_POST['sbt_src'] = $outcome['src'] ? $outcome['src'] : $chk_post->chkImage('img');
		$_POST['sbt_path'] = $chk_post->traFromName('name', array('name' => 'catalog', 'field' => 'path'), $outcome['path']);
		
		if(!$err){
			if($_GET['num']) unset($_POST['sbt_type']);

			$submit_arr = initSubmitColumns('catalog', $_GET['num']);

			$_POST['sbt_id'] = $_GET['num'] ? $_GET['num'] : ($my_db->selectMax('catalog') + 1);
			$_POST['sbt_parent'] = (int)$_POST['sbt_parent'];
			$_POST['sbt_queue'] = min(max((int)$_POST['sbt_queue'], 0), $cms_max_num['queue']);
			$_POST['sbt_order'] = (int)$_POST['sbt_order'];
			$_POST['sbt_style'] = (int)$_POST['sbt_style'];
			$_POST['sbt_valid'] = ($_POST['sbt_valid'] ? 1 : 0);
			$_POST['sbt_navi'] = ($_POST['sbt_navi'] ? 1 : 0);

			$submit = array();
			for($i = 0; $i < count($submit_arr); $i++){
				$submit += array($submit_arr[$i] => $_POST['sbt_' . $submit_arr[$i]]);
			}

			mysql_query('BEGIN');

			$done = 1;
			$done &= $my_db->saveRow('catalog', $submit, ($_GET['num'] ? array('id' => $_GET['num']) : ''));

			if($submit = submitByLanguage($submit)) {
				$getdata = $my_db->selectRow('*', 'language', array('connect' => $con_lang_current));
				while($result = mysql_fetch_array($getdata)){
					$done &= $my_db->saveRow('catalog', $submit, ($_GET['num'] ? array('id' => $_GET['num']) : ''), $result['abbr']);
				}
			}

			if($done){
				mysql_query("COMMIT");
				mysql_query("END");

				if($_FILES['sbt_img']['tmp_name']){
					$imgpath = $module_src . $_POST['sbt_src'];
					if(file_exists($imgpath)) unlink($imgpath);
					if(file_exists($_POST['tmp_img'])) unlink($_POST['tmp_img']);

					move_uploaded_file($_FILES['catalog_img']['tmp_name'], $imgpath);
				}

				instructLog($cms_admin_power['module'][$power_id] . $_POST['sbt_name'], ($poser_id == 1 ? 'add' : 'edt'));
				$msg[0] = '提交成功';

				$href = $_SERVER['PHP_SELF'] . '?action=lst' . preg_replace('/action=[^&]+|&num=\d+/', '', $_SERVER['QUERY_STRING']);
				header('Location: ' . $href . '&msg[]=' . urlencode($msg[0]) . '&msg[]=' . $msg[1]);
			}
			else {
				mysql_query("ROLLBACK");
				mysql_query("END");
				$msg[0] = '提交失敗';
				$msg[1] = 'fail';
			}
		}
	}

	require('templates/head.html');
}
else if($_GET['action'] == 'lst'){
	require('templates/head.html');

	$q_url = queryPart('date', 'desc');

	$where = '`type` = ' . $_GET['type'];

	$fields = array(
		array('__all', 'edit'),
		'id' => '序号', 
		'parent' => array('系', 'read', array($department)),
		'name' => array('班级', 'text'), 
		'valid' => array('有效', 'checkbox'),
		array('__edit', 'edit', array('power' => 'module', 'method' => array('quick' => 2, 'detail' => 2)))
	);

	if ($_GET['type'] == 1) {
		unset($fields['parent']);
		$fields['name'][0] = '系名称';
	}
	
	$code_table = tableFields($fields,
		array(
			'where' => $where,
			'table' => 'catalog',
			'operate' => array('edt', 'delete')
		)
	);
}

require('templates/module.html');
require('templates/foot.html');