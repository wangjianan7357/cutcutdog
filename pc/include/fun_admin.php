<?php
/**
 * CMS 所需的功能应用
 */

$admin_arr = array();

/* 登录时限 */
function loginTimeout(){
	global $my_db, $admin_arr;
	session_start();
	header('Cache-control: private');

	//if($_SERVER['SERVER_ADDR'] == '127.0.0.1' || $_SERVER['SERVER_ADDR'] == '::1') $cms_login_time = 10000000;

	if(!$_COOKIE['admin']['id']){
		echo '<script language="javascript">top.location.href = "login.php";</script>';
		exit();
	}
	else if (!$_SESSION['admin_id']) {
		$result = $my_db->fetchOne('admin', array('id' => $_COOKIE['admin']['id'], 'pass' => $_POST['pass']));

		if (!$result) {
			echo '<script language="javascript">top.location.href = "login.php";</script>';
			exit();
		}
	}
	
	$_SESSION['admin_id'] = $_COOKIE['admin']['id'];

	setcookie('admin[id]', $_COOKIE['admin']['id'], time() + systemConfig('cms_login_time') * 60);
	setcookie('admin[pass]', $_COOKIE['admin']['pass'], time() + systemConfig('cms_login_time') * 60);
}

/* 后台页面检测登录 */
loginTimeout();

/* 初始化语言信息 */
function langInit(){
	global $my_db;
	global $con_db_table;
	global $con_lang_current;
	global $cms_lang_connect;

	setcookie('cookie[lang]', $con_lang_current, time() + systemConfig('cms_login_time') * 60);

	$getdata = $my_db->selectRow('id, connect', 'language', array('abbr' => $con_lang_current, 'valid' => 1));
	$result = mysql_fetch_array($getdata);
	if($result) $cms_lang_connect = $result['connect'];
	else exit;
}

langInit();

/*
 * 生成列表的排序字段
 */
function tableFields($fields = array(), $table = array()){
	global $q_url;
	global $my_db;

	$condition = '';
	$table['prefix'] = isset($table['prefix']) ? $table['prefix'] : 'sbt';
	$table['where'] = isset($table['where']) ? $table['where'] : 1;

	if ($_REQUEST['searchcolumn']) {
		function getTarget($data) {
			global $my_db;

			$tmp = array();
			$target = array_shift($data);
			$field = explode('.', $target);

			if (!empty($data)) {
				$res = getTarget($data);
				if (!empty($res)) {
					$getdata = $my_db->selectRow('*', $field[0], array('`' . addslashes($field[1]) . '` IN (' . implode(',', $res) . ')'));
				} else {
					return $tmp;
				}
			} else {
				$getdata = $my_db->selectRow('*', $field[0], array('`' . addslashes($field[1]) . '` LIKE \'%' . addslashes(trim(urldecode($_REQUEST['searchwords']))) . '%\''));
			}

			while($result = mysql_fetch_array($getdata)){
				$tmp[] = $result[$field[2]];
			}
			return $tmp;
		}

		if (strpos($_REQUEST['searchcolumn'], '-')) {
			$relate = explode('-', $_REQUEST['searchcolumn']);
			$target = array_shift($relate);
			$res = getTarget($relate);
			if (!empty($res)) {
				$condition = ' AND `' . addslashes($target) . '` IN (' . implode(',', $res) . ')';
			} else {
				$condition = ' AND 0';
			}

		} else {
			$condition = $_REQUEST['searchcolumn'] ? ' AND `' . addslashes($_REQUEST['searchcolumn']) . '` LIKE \'%' . addslashes(trim(urldecode($_REQUEST['searchwords']))) . '%\'' : '';
		}

	}

	$getdata = $my_db->selectRow('count( id )', $table['table'], array($table['where'] . $condition));

	while($result = mysql_fetch_array($getdata)){
		$total = $result[0];
		break;
	}

	if (isset($table['search'])) {
		$code = '<div class="row">';

		if (isset($table['search'])) {
			// 搜索
			$code .= '<div class="fr"><select name="searchcolumn" class="bdr3 bd2 h24 vm minw150"><option value="">-- 請選擇 --</option>';

			$search = explode(',', $table['search']);
			foreach ($search as $value) {
				$tmp = explode('|', $value);
				$code .= '<option value="' . $tmp[0] . '">' . $tmp[1] . '</option>';
			}

			$code .= '</select> <input name="searchwords" value="" class="bdr3 bd2 h24 pdrl3 vm"> <input class="button1 vm" type="submit" value="搜 索" /></div>';
		}

		$code .= '</div><div class="row">';
		
	}

	$code = '<div class="row"><div class="col-sm-12"><table class="table table-bordered table-hover dataTable"><thead><tr>';

	foreach ($fields as $key => $value) {
		$value = (array)$value;

		if(strpos($value[0], '__') === 0) {
			// 双下划线保留给系统
			if ($value[0] == '__all') $code .= '<th style="white-space: nowrap;"><input type="checkbox" name="chk_all" /></th>';
			else if ($value[0] == '__edit') $code .= '<th id="i0" style="white-space: nowrap;">操作</th>';
		}
		else if (strpos($value[0], '_') === 0) {
			// 单下划线不提供排序
			$code .= '<th>' . substr($value[0], 1) . '</th>';
		}
        else if (strpos($key, '[') !== false) {
            $code .= '<th>' . $value[0] . '</th>';
        }
		else {
			$code .= '<th class="sorting' . ($_GET['field'] == $key ? '_' . $_GET['flag'] : '') . '"><a href="' . $q_url['134567'] . '&field=' . $key . '">' . $value[0] . '</a></th>';
		}
	}
	$code .= '</tr></thead><tbody>';

	$i = 0;
	$where = $table['where'] . $condition;
	$limit = $table['limit'] ? $table['limit'] : ($q_url['display'] * ($q_url['page'] - 1) . ' , ' . $q_url['display']);

	if($table['sql']) $get_data = $my_db->myQuery($table['sql']);
	else $get_data = $my_db->selectRow('*', $table['table'], array($where), array('field' => $q_url['field'], 'method' => $q_url['flag']), $limit);
	
	while($result = mysql_fetch_array($get_data)){
		$code .= '<tr>';

		foreach ($fields as $key => $value) {
			$key = preg_replace('/\[\d*\]$/', '', $key);
			
			if($value[1] == 'edit' || $key == 'cid') {
				$code .= '<td style="white-space: nowrap;">';
			} else if($key == 'path') {
				$code .= '<td style="word-break: break-all;">';
			} else {
				$code .= '<td>';
			}

			$value = (array)$value;
			if(!isset($value[1])) {
				$code .= htmlspecialchars(cutString($result[$key], 100));
			} 
			else if($value[1] == 'text') {
				if($_GET['num'] == $result['id']){
					$size = (int)min(max(strlen($result[$key]), 1), 35);

					if($value[2][0] == 'prefix') $code .= $value[2][1];
					$code .= '<input name="' . $table['prefix'] . '_' . $key . '" size="' . $size . '" value="' . htmlspecialchars($result[$key]) . '" />';
				} 
				else {
					$code .= ($value[2][0] == 'prefix' ? $value[2][1] : '') . $result[$key] . '</div>';
				}
			}
			else if($value[1] == 'select') {
				if(is_array($value[2][0])) {
					if($_GET['num'] == $result['id']){
						$code .= '<select name="' . $table['prefix'] . '_' . $key . '"><option value="">請選擇</option>';
						foreach($value[2][0] as $key1 => $value1) {
							$code .= '<option value="' . $key1 . '"' . ($result[$key] == $key1 ? ' selected="selected"' : '') . '>' . $value1 . '</option>';
						}
					}
					else {
						$code .= (isset($value[2][1]) ? $value[2][1][$result[$key]] : $value[2][0][$result[$key]]) . '</div>';
					}
				}
				else if(is_object($value[2][0])) {
					if($_GET['num'] == $result['id']){
						$code .= '<select name="' . $table['prefix'] . '_' . $key . '"><option value="">請選擇</option>';
						$code .= $value[2][0]->fun1($result[$key]);
					} else {
						$code .= $value[2][0]->fun2($result[$key]) . '</div>';
					}
				}
			}
			else if($value[1] == 'read') {
				if(is_array($value[2][0])) {
					$code .= $value[2][0][$result[$key]];
				}
				else if(is_object($value[2][0])) {
					$code .= $value[2][0]->fun1($result[$key], $result);
				}
				else {
					if($_GET['num'] == $result['id']){
						$size = (int)min(max(strlen($result[$key]), 1), 35);
						$code .= '<input readonly="readonly" name="' . $table['prefix'] . '_' . $key . '" size="' . $size . '" value="' . htmlspecialchars($result[$key]) . '" />';
					}
					else {
						$code .= $result[$key];
					}
				}
			}
			else if($value[1] == 'checkbox') {
				if($_GET['num'] == $result['id']){
					$code .= '<input type="hidden" name="' . $table['prefix'] . '_' . $key . '" value="0" /><input name="' . $table['prefix'] . '_' . $key . '" type="checkbox"' . ($result[$key] ? ' checked="checked"' : '') . ' />';
				}
				else {
					if($result[$key]) {
						$code .= isset($value[2]) ? $value[2][0] : '<span class="badge bg-green">&nbsp;&nbsp;</span>';
					}
					else {
						$code .= isset($value[2]) ? $value[2][1] : '<span class="badge bg-gray">&nbsp;&nbsp;</span>';
					}
				}
			}
			else if($value[1] == 'edit') {
				if($value[0] == '__all') {
					if(!isset($value[2])) {
						$code .= '<input type="checkbox" name="chk_' . $result['id'] . '" />';
					}
					else if(is_object($value[2][0])) {
						$code .= $value[2][0]->fun1($result);
					}
				}
				else if($value[0] == '__edit') {
					if(is_object($value[2][0])) {
						$code .= $value[2][0]->fun1($result);
						
					} else {
						$j = 0;
						$temp = '';
						foreach ($value[2]['method'] as $key1 => $value1) {
							$code .= $j ? '&nbsp;' : '';

							if($key1 == 'quick') {
								if($_GET['num'] == $result['id']){
									$code .= '<input type="submit" value="提交" /><input type="hidden" name="save" value="true" />';
								}
								else {
									$temp = '<a href="' . $_SERVER['PHP_SELF'] . '?action=lst&num=' . $result['id'] . $q_url['24567'] . '&flag=' . $_GET['flag'] . '#i' . $i . '" id="i' . ($i + 1) . '">简洁</a>';
									$code .= '<input type="hidden" id="quick_' . $result['id'] . '" value="' . $_SERVER['PHP_SELF'] . '?action=lst&num=' . $result['id'] . $q_url['24567'] . '&flag=' . $_GET['flag'] . '#i' . $i . '" />';
									$j --;
								}
							}
							else {
								// 当没有“详细”时，则显示“快捷”按钮
								$temp = '';
								$code .= '<a href="' . $_SERVER['PHP_SELF'] . '?action=edt&num=' . $result['id'] . $q_url['24567'] . '&flag=' . $_GET['flag'] . (isset($value[2]['query']) ? $value[2]['query'] : '') . '" id="i' . ($i + 1) . '">' . (isset($value[2]['text']) ? $value[2]['text'] : '編輯') . '</a>';
							}

							$j++;
						}
					}

					$code .= $temp;
				}
			}

			$code .= '</td>';
		}

		$code .= '</tr>';
		$i++;
	}

	$code .= '</tbody></table></div></div>';
	$code .= '<div class="row"><div class="col-sm-5">' . $q_url['page'] . '/' . ceil($total / $q_url['display']) . ' 頁 &nbsp; 共 ' . $total . ' 條記錄 </div><div class="col-sm-7"><div class="dataTables_paginate paging_simple_numbers"><ul class="pagination">';

	for ($i = 1; $i <= ceil($total / $q_url['display']); $i ++) {
		if ($q_url['page'] == $i) {
			$code .= '<li class="paginate_button active"><a href="#" aria-controls="example1" data-dt-idx="1" tabindex="0">' . $i . '</a></li>';
		} else {
			$code .= '<li class="paginate_button"><a href="' . $q_url['1256'] . '&flag=' . $_GET['flag'] . '&page=' . $i . '">' . $i . '</a></li>';
		}
	}

	$code .= '</ul></div></div></div>';

	return $code;
}

/* 
 * 查询管理所属的无权限数据ID
 */
function getManageDataId($range) {
	global $my_db;
	global $cms_admin_power;

	$disable = array();

	if (isset($cms_admin_power[$range])) {
		$i = 1;
		$target = $cms_admin_power[$range];
		$getdata = $my_db->selectRow('id', $target[1], array($target[3]), array('field' => 'id'));
		while ($result = mysql_fetch_array($getdata)) {
			if (!adminPower($range, $i)) {
				$disable[] = $result['id'];
			}
			$i ++;
		}
	}

	return $disable;
}

/* 
 * 循环生成分类选择
 * 需先根据需求初始化 catalog_all
 */
function selCatalog($parent = '', $i = 0){
	global $catalog_all;

	$dash = '';
	$selpage = array();
	$disable = getManageDataId('module{}');

	if(!$i) $selpage['cid']['^'] = '- 顯示全部 -';
	for($j = 0; $j < $i; $j++) $dash .= '-';

	foreach($catalog_all as $value){
		if (in_array($value['id'], $disable)) {
			continue;
		}

		if(substr_count($value['parent'], ',') == $i && $value['parent'] == $parent){
			$selpage['cid']['^' . $value['parent'] . $value['id']] = $dash . $value['name'];
			$selpage = array_merge_recursive($selpage, selCatalog($parent . $value['id'] . ',', $j + 1));
		}
	}
	return $selpage;
}

/* 
 * 循环生成分类用于添加时选择
 */
function catalogOption($type, $current = '', $catalog_num = 0, $reg = ''){
	global $my_db;
	global $cms_cata_type;

	if(!$current){
		$current = $_POST[$cms_cata_type[$type]['db'] . '_cid'];
		if(!$current && $_GET['cid']) $current = strtr($_GET['cid'], array('^' => '')) . ',';
	}

	$disable = getManageDataId('module{}');

	$opt_catalog = '';
	$where = array('parent REGEXP "^' . $reg . '$" AND `type` = ' . $type . ($catalog_num ? (' AND id != "' . $catalog_num . '"') : ''));
	$getdata = $my_db->selectRow('id, name, parent', 'catalog', $where, array('field' => 'id'));
	while($result = mysql_fetch_array($getdata)){
		if (in_array($result['id'], $disable)) {
			continue;
		}

		$level = strtr(preg_replace('/\d/', '', $result['parent']), array(',' => '-'));

		$skip = 0;
		if($catalog_num){
			// 不可选择其子类
			preg_match('/^[\d,]+,(' . $catalog_num . '),([\d,]*|$)/', $result['parent'], $match);
			$skip = $match[1];
		}

		if(!$skip) $opt_catalog .= '<option value="' . ($result['parent'] . $result['id'] . ',') . '"' . (($current == ($result['parent'] . $result['id'] . ',')) ? ' selected="selected"' : '') . '>' . $level . $result['name'] . '</option>';

		$opt_catalog .= catalogOption($type, $current, $catalog_num, ($reg . $result['id'] . ','));
	}
	return $opt_catalog;
}

/* 
 * 删除分类时，更新其子类及删除页面表记录
 */
function delCatalogInfo($target, $catalog_db, $lang = ''){
	global $my_db;
	
	$done = 1;

	// 更新分类父级信息
	$getdata = $my_db->selectRow('id, parent', 'catalog', array('`parent` REGEXP "(^|,)' . $target . ',"'), '', '', $lang);
	while($result = mysql_fetch_array($getdata)){
		$parent = preg_replace('/(^|,)' . $target . ',/', '\\1', $result['parent']);
		$done &= $my_db->saveRow('catalog', array('parent' => $parent), array('id' => $result['id']), $lang);
	}

	$getdata = $my_db->selectRow('id, cid', $catalog_db, array('`cid` REGEXP "(^|,)' . $target . ',"'), '', '', $lang);
	while($result = mysql_fetch_array($getdata)){
		$tmp = array();
		$tmp['cid'] = preg_replace('/(^|,)' . $target . ',/', '\\1', $result['cid']);

		$done &= $my_db->saveRow($catalog_db, $tmp, array('id' => $result['id']), $lang);
	}

	return $done;
}

/* 删除所选数据记录 */
function delSelectedData($table, $power=array(), $src = ''){
	global $my_db;
	global $con_pic;
	global $con_db_table;
	global $con_lang_current;
	global $cms_page_union;
	global $cms_admin_power;
	global $cms_cata_type;

	$done = 1;
	$exist = false;
	$catalog_db = '';
	$imgarr = array();
	$power['name'] = isset($power['name']) ? $power['name'] : $table;

	mysql_query('BEGIN');

	$page_union = isset($cms_page_union[$table]) ? true : false;

	foreach($_POST as $key => $value){
		$tmp = explode('chk_', $key);

		if ((int)$tmp[1] > 0) {
			$exist = true;

			if ($src) {
				$res = null;
				$getdata = $my_db->selectRow('*', $table, array('id' => $tmp[1]));
				$res = mysql_fetch_array($getdata);
				if($res['src']) array_push($imgarr, $res['src']);
			}

			if($table == 'catalog') {
				$getdata = $my_db->selectRow('type', $table, array('id' => $tmp[1]));
				$result = mysql_fetch_array($getdata);
				$catalog_db = $cms_cata_type[$result['type']]['db'];

				$done &= delCatalogInfo($tmp[1], $catalog_db, $con_lang_current);
			}
			else if ($table == 'member') {
				delDir('../' . systemConfig('member_img_path') . $tmp[1]);
			}

			$done &= $my_db->deleteRow($table, array('id' => $tmp[1]));
			if ($page_union) {
				$done &= $my_db->deleteRow('page', array('id' => ($tmp[1] + $cms_page_union[$table]['id'])));
			}

			// 默认有数据表名有两个字符后缀的是有多语信息
			if (preg_match('/(_[a-z]{2}$)/', $con_db_table[$table])) {
				// 删除参照语言资料
				$getdata = $my_db->selectRow('abbr', 'language', array('connect' => $con_lang_current));
				while($result_lan = mysql_fetch_array($getdata)){
					if($table == 'catalog') {
						$done &= delCatalogInfo($tmp[1], $catalog_db, $result_lan['abbr']);
					}

					$done &= $my_db->deleteRow($table, array('id' => $tmp[1]), $result_lan['abbr']);
					if ($page_union) {
						$done &= $my_db->deleteRow('page', array('id' => ($tmp[1] + $cms_page_union[$table]['id'])), $result_lan['abbr']);
					}
				}
			}
		}
	}

	if($done && $exist){
		mysql_query("COMMIT");
		mysql_query("END");

		for($i = 0; $i < count($imgarr); $i++){
			if ($table == 'product' || $table == 'info' || $table == 'service') {
				$big_img = $src . $con_pic['suf']['big'] . $imgarr[$i];
				$mid_img = $src . $con_pic['suf']['mid'] . $imgarr[$i];
				$sml_img = $src . $con_pic['suf']['sml'] . $imgarr[$i];

				if(file_exists($big_img)) unlink($big_img);
				if(file_exists($mid_img)) unlink($mid_img);
				if(file_exists($sml_img)) unlink($sml_img);
			}
			else {
				$org_img = $src . $imgarr[$i];
				if(file_exists($org_img) && is_file($org_img)) unlink($org_img);
			}
		}

		if (!empty($power)) {
			$msg[0] = $cms_admin_power[$power['name']][$power['id']] . '成功';
		} else {
			$msg[0] = '刪除成功';
		}

		$msg[1] = 'success';
		//instructLog($cms_admin_power[$power['name']][$power['id']], 'del');
	}
	else {
		mysql_query("ROLLBACK");
		mysql_query("END");

		if(!$exist){
			$msg[0] = '未選擇刪除記錄';
			$msg[1] = 'warn';
		}
		else {
			if (!empty($power)) {
				$msg[0] = $cms_admin_power[$power['name']][$power['id']] . '失敗';
			} else {
				$msg[0] = '刪除失敗';
			}
			$msg[1] = 'fail';
		}
	}

	$href = $_SERVER['PHP_SELF'] . '?action=lst' . preg_replace('/action=[^&]*&/', '&', $_SERVER['QUERY_STRING']);
	header('Location: ' . $href . '&msg[]=' . urlencode($msg[0]) . '&msg[]=' . $msg[1]);
}

/* 检测管理员权限 */
function adminPower($range = '', $ability = 'any', $id = ''){
	global $my_db;
	global $con_db_table;
	global $cms_admin_power;

	$userpower = null;
	if(!$id) $id = $_SESSION['admin_id'];

	if ($id == 1) {
		return true;
	}

	$getdata = $my_db->myQuery('SELECT `' . $con_db_table['role'] . '`.`power` FROM `' . $con_db_table['admin'] . '` JOIN `' . $con_db_table['role'] . '` ON `' . $con_db_table['admin'] . '`.`rid` = `' . $con_db_table['role'] . '`.`id` WHERE `' . $con_db_table['admin'] . '`.`id` = "' . $id . '"');
	while($getdata && $result = mysql_fetch_array($getdata)){
		$temp = explode(',', $result['power']);

		$i = 0;
		foreach($cms_admin_power as $key => $value){
			if($key == $range){
				$userpower = decbin($temp[$i]);
				break;
			}
			$i++;
		}
	}

	if($userpower != null){
		$ability_arr = array();

		if(is_string($ability)){
			if($ability == 'any'){
				for($i = 1; $i < count($cms_admin_power[$range]); $i++){
					array_push($ability_arr, $i);
				}
			}
			else $ability_arr = explode('|', $ability);
		}
		else $ability_arr[] = $ability;

		for($i = 0; $i < count($ability_arr); $i++){
			$num = (int)$ability_arr[$i];
			if($num && (substr($userpower, $num, 1) == 1)) return true;
		}
	}
	return false;
}

/* 设置路径查询字段 */
function queryPart($field = 'id', $flag = 'asc', $query = array(), $page = 1){
	$q_url = array();

	// $_SERVER['PHP_SELF'] && $_GET['action'] => 1
	// 'field_url' => 2
	// 'flag_url' => 3
	// 'page_url' => 4
	// 'display_url' => 5
	// 'search_url' => 6
	// 'cid' && 'type' => 7 && query
	
	if(!$_GET['field']) $q_url['field'] = $field;
	else $q_url['field'] = $_GET['field'];
	$q_url['field_url'] = '&field=' . $q_url['field'];
	
	if(!$_GET['flag']) $_GET['flag'] = $flag;
	if(strtoupper($_GET['flag']) == 'ASC'){
		$q_url['flag'] = 'ASC';
		$q_url['flag_url'] = '&flag=desc';
	}
	else {
		$q_url['flag'] = 'DESC';
		$q_url['flag_url'] = '&flag=asc';
	}

	if(!$_GET['page']) $q_url['page'] = $page;
	else $q_url['page'] = $_GET['page'];
	$q_url['page_url'] = '&page=' . $q_url['page'];

	$q_url['display'] = $_GET['display'];
	if(!$q_url['display']) $q_url['display'] = systemConfig('cms_display_qty');
	$q_url['display_url'] = '&display=' . $q_url['display'];

	$q_url['search_url'] = '';
	if($_REQUEST['searchcolumn'] && $_REQUEST['searchwords']){
		$q_url['search_url'] = '&searchcolumn=' . $_REQUEST['searchcolumn'] . '&searchwords=' . strtr($_REQUEST['searchwords'], array('\\' => ''));
	}

	$q_url['1'] = $_SERVER['PHP_SELF'] . '?action=' . $_GET['action'];
	$q_url['7'] = ($_GET['cid'] ? ('&cid=' . $_GET['cid']) : '') . ($_GET['type'] ? ('&type=' . $_GET['type']) : '');

	foreach ($query as $value) {
		$q_url['7'] .= '&' . $value . '=' . $_GET[$value];
	}

	$q_url['12'] = $q_url['1'] . $q_url['field_url'];
	$q_url['13'] = $q_url['1'] . $q_url['flag_url'];
	$q_url['45'] = $q_url['page_url'] . $q_url['display_url'];

	$q_url['1256'] = $q_url['12'] . $q_url['display_url'] . $q_url['search_url'];
	$q_url['245'] = $q_url['field_url'] . $q_url['45'];
	$q_url['2456'] = $q_url['245'] . $q_url['search_url'];
	
	$q_url['12456'] = $q_url['12'] . $q_url['45'] . $q_url['search_url'];
	$q_url['13456'] = $q_url['13'] . $q_url['45'] . $q_url['search_url'];

	$q_url['24567'] = $q_url['2456'] . $q_url['7'];

	$q_url['124567'] = $q_url['12456'] . $q_url['7'];
	$q_url['134567'] = $q_url['13456'] . $q_url['7'];

	return $q_url;
}

/* 初始化需提交的字段 */
function initSubmitColumns($table, $num = 0, $prefix = 'sbt'){
	global $my_db;
	global $cms_lang_connect;

	$columns = array();
	$getdata = $my_db->showColumns($table);
	while($result = mysql_fetch_array($getdata)){
		if(
			($result['Default'] == 'CURRENT_TIMESTAMP' && !$num) ||
			($result['Extra'] == 'auto_increment' && !$_POST[$prefix . '_' . $result['Field']]) ||
			(!isset($_POST[$prefix . '_' . $result['Field']]) && $result['Null'] == 'YES') ||
			($cms_lang_connect && $result['Field'] == 'path')
		) {
			continue;
		}

		if($num){
			if(isset($_POST[$prefix . '_' . $result['Field']])){
				$columns[] = $result['Field'];

			} else if ($result['Default'] == 'CURRENT_TIMESTAMP') {
				// 自动更新日期
				$_POST[$prefix . '_' . $result['Field']] = date('Y-m-d H:i:s');
				$columns[] = $result['Field'];

			} else {
				// 判断用于 json 格式的字段
				foreach($_POST as $key => $value){
					if(strpos($key, ($prefix . '_' . $result['Field'] . '_')) === 0){
						$columns[] = $result['Field'];
						break;
					}
				}
			}

		}
		else {
			$columns[] = $result['Field'];
			if(!isset($_POST[$prefix . '_' . $result['Field']]) && $result['Default'] !== '') {
				$_POST[$prefix . '_' . $result['Field']] = $result['Default'];
			}
		}
	}

	return $columns;
}

function submitByLanguage($submit){
	$refresh = systemConfig('connect_language_refresh');

	if(!$refresh) {
		return '';
	}
	else if($refresh == 1 && $_GET['num']) {
		foreach ($submit as $key => $value) {
			switch($key){
				case 'name':
				case 'desp':
				case 'info':
				case 'key1':
				case 'key2':
				case 'title':
				case 'description':
				//case 'fields':
				case 'keyword':
				case 'date':
					unset($submit[$key]);
				break;
			}
		}
	}

	return $submit;

}

function clearMso($match){
	// need modify
	//return $match[1] . preg_replace('/mso\-[^;\"]+;/', '', $match[2]) . '>';
	return $match[1] . preg_replace('/mso\-[^;\"]+;|mso\-[^;\"]+(\")/', '\\1', $match[2]) . '>';
}

// modify editor's information
function modEditorInfo($info, $method = 'save'){
	if($method == 'save'){
		// clear MS office's code
		$info = preg_replace('/<\/?\\?xml[^>]*>|<\/?\\w+:[^>]*>|<\/?col[^>]*>|<link [^>]*>|<meta [^>]*>|<!--(.*?)-->/is', '', $info);
		$info = preg_replace('/<(\\w[^>]*) lang=([^ |>]*)([^>]*)/is', '<\\1\\3', $info);
		$info = preg_replace('/<(\\w[^>]*) face=([^ |>]*)([^>]*)/is', '<\\1\\3', $info);
		$info = preg_replace('/<(\\w[^>]*) x:([^>]*)>/is', '<\\1>', $info);
		$info = preg_replace_callback('/(<[a-z\d\:!]+)([^>]+)>/',  'clearMso', $info);

		$info = preg_replace('/\s+/is', ' ', $info);

		// 清除上传图片时的子文件夹
		$info = preg_replace('/ href=\"\/[^\/]+\/uploads/is', ' href="/uploads', $info);
		$info = preg_replace('/ src=\"\/[^\/]+\/uploads/is', ' src="/uploads', $info);
	}

	return $info;
}


function instructLog($instruct, $operate = 'otr'){
	global $my_db;

	if($my_db->tableExist('log')){
		$detail = '';
		switch(gettype($instruct)){
			case 'string':
				$detail .= $instruct;
			break;
			case 'array':
				$getdata = $my_db->selectRow($instruct['field'], $instruct['name'], array('id' => $instruct['id']));
				$result = mysql_fetch_array($getdata);
				$detail .= $instruct['info'] . ' ' . $result[$instruct['field']];
			break;
		}

		$result = $my_db->fetchOne('admin', array('id' => $_SESSION['admin_id']));
		$my_db->saveRow('log', array('user' => $result['name'], 'operate' => $operate, 'detail' => $detail));
	}
}

/* 递归删除整个目录及里面的文件 */
function delDir($dir) {
	if(is_dir($dir)) {
		$dh = opendir($dir);
		while ($file = readdir($dh)) {
			if($file != '.' && $file != '..') {
				$fullpath = $dir . '/' . $file;

				if(!is_dir($fullpath)) {
					unlink($fullpath);
				} else {
					deldir($fullpath);
				}
			}
		}

		closedir($dh);
		rmdir($dir);
	}
}

?>