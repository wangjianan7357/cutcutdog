<?php
require('../include/common.php');
require('../include/fun_admin.php');

$err = '';
$msg = array();

if($_GET['action'] == "edt"){
	if($_GET['num']) $power_id = 2;
	else if($_POST['del'] == 'true') $power_id = 3;
	else $power_id = 1;

	if($_POST['del'] == 'true'){
		if(!adminPower('role', $power_id)) warning('權限不足');
		else delSelectedData('role', array('id' => $power_id));
	}
	else if($_POST['save'] == 'true'){
		if(!adminPower('role', $power_id)) warning('權限不足');

		$chk_post = new ChkRequest('sbt_');
		$chk_post->chkExist(array('name' => '角色'), array('name' => 'role'));
			
		if(!$err){
			$exist = false;
			$power = '';
			foreach($cms_admin_power as $key => $value){
				if (strpos($key, '{}') !== false) {
					$temp = array('');
					$getdata = $my_db->selectRow('*', $value[1], array($value[3]));
					while ($result = mysql_fetch_array($getdata)) {
						$temp[] = $result;
					}
					$value = $temp;
				}

				$number = '';
				for($i = 1; $i < count($value); $i++){
					if($_POST['sbt_' . $key . '_' . $i] == 'on'){
						$number .= 1;
						$exist = true;
					}
					else $number .= 0;
				}
				$power .= bindec(1 . $number) . ',';
			}

			if($exist) $submit = array('name' => $_POST['sbt_name'], 'power' => $power);
			else $submit = array('name' => $_POST['sbt_name']);

			if($my_db->saveRow('role', $submit, ($_GET['num'] ? array('id' => $_GET['num']) : ''))){
				instructLog($cms_admin_power['role'][$power_id] . $_POST['sbt_name'], ($_GET['num'] ? 'edt' : 'add'));
				$msg[0] = '提交成功';

				$href = $_SERVER['PHP_SELF'] . '?action=lst' . preg_replace('/action=[^&]+|&num=\d+/', '', $_SERVER['QUERY_STRING']);
				header('Location: ' . $href . '&msg[]=' . urlencode($msg[0]) . '&msg[]=' . $msg[1]);
			}
			else {
				$msg[0] = $cms_admin_power['role'][$power_id] . '提交失敗';
				$msg[1] = 'fail';
			}
		}
	}

	require('templates/head.html');

	$role_arr = $my_db->fetchOne('role', array('id' => $_GET['num']));

	// 生成权限选项表
	$i = 0;
	$code = '';
	$role_power = explode(',', $role_arr['power']);
	foreach($cms_admin_power as $key => $value){
		//if($key == 'role') continue;

		$tmp1 = '<tr data-action="hover">';
		$tmp1 .= '<td align="right"><b>' . $value[0] . '</b></td><td align="left">';

		$tmp2 = '';
		if (strpos($key, '{}') !== false) {
			$getdata = $my_db->selectRow('*', $value[1], array($value[3]), array('field' => 'id'));
			$j = 1;
			while ($result = mysql_fetch_array($getdata)) {
				$tmp2 .= '<div style="float:left; width:120px;"><input type="checkbox" id="role_' . $key . '_' . $j . '" name="sbt_' . $key . '_' . $j . '" ' . (($role_power[$i] && substr(decbin($role_power[$i]), $j, 1) == 1) ? 'checked="checked" ' : '') . '/> <label for="role_' . $key . '_' . $j . '">' . $result[$value[2]] . '</label></div>';
				$j ++;
			}

		} else {
			for($j = 1; $j < count($value); $j++){
				if (adminPower($key, $j)) {
					$tmp2 .= '<div style="float:left; width:120px;"><input type="checkbox" id="role_' . $key . '_' . $j . '" name="sbt_' . $key . '_' . $j . '" ' . (($role_power[$i] && substr(decbin($role_power[$i]), $j, 1) == 1) ? 'checked="checked" ' : '') . '/> <label for="role_' . $key . '_' . $j . '">' . $value[$j] . '</label></div>';
				}
			}
		}

		$code .= $tmp2 ? $tmp1 . $tmp2 . '</td></tr>' : '';
		$i++;
	}

} else {
	require('templates/head.html');
	$q_url = queryPart('id');

	// id != 1 超级管理员不显示出来
	$where = '`id` != 1';
	
	$code_table = tableFields(
		array(
			array('__all', 'edit'),
			'name' => array('角色', 'text'), 
			array('__edit', 'edit', array('power' => 'role', 'method' => array('quick' => 2, 'detail' => 2)))
		),
		array(
			'where' => $where,
			'table' => 'role',
		)
	);
}

require('templates/role.html');
require('templates/foot.html');