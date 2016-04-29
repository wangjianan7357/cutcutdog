<?php
require('../include/common.php');
require('../include/fun_admin.php');

$err = '';
$msg = $_GET['msg'] ? $_GET['msg'] : array();

if($_GET['action'] == 'set'){
	if($_POST['save'] == 'true'){
		if(!adminPower('system', $_GET['type'])) warning('權限不足');

		$data = array();
		$getdata = $my_db->selectRow('*', 'system', array('tid' => $_GET['type'], 'lang' => ($_GET['type'] == 2 ? ($_GET['lang'] ? $_GET['lang'] : $con_lang_default) : '')));
		while($result = mysql_fetch_array($getdata)){
			$value = '';
			if($result['type'] == 'hidden'){
				continue;
			}
			else if($result['type'] == 'boolean'){
				$value = $_POST['sbt_' . $result['varname']] == 'on' ? 'true' : 'false';
			}
			else if($result['type'] == 'integer'){
				if($value < 0) warning($result['info'] . '不得小于 0');
				else $value = (int)$_POST['sbt_' . $result['varname']];
			}
			else if($result['type'] == 'array'){
				$value = preg_replace('/^[^\[]*\[/', $_POST['sbt_' . $result['varname']] . '[', $result['value']);
			}
			else $value = $_POST['sbt_' . $result['varname']];

			switch($result['varname']){
				case 'receive_email':
					$email_arr = explode(';', $value);
					foreach ($email_arr as $email) {
						preg_match('/^[a-z0-9]+([._\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*){2,63}\.[a-z0-9]+$/i', $email, $match);
						if(!$match){
							warning($result['info'] . '格式有误');
							break;
						}
					}
				break;
			}

			if(!$err) array_push($data, array('varname' => $result['varname'], 'value' => $value));
		}

		if(!$err){
			mysql_query('BEGIN');

			$done = 1;
			for($i = 0; $i < count($data); $i++){
				$done &= $my_db->saveRow('system', array('value' => $data[$i]['value']), array('varname' => $data[$i]['varname'], 'tid' => $_GET['type'], 'lang' => ($_GET['type'] == 2 ? ($_GET['lang'] ? $_GET['lang'] : $con_lang_default) : '')));
			}

			if($done){
				mysql_query("COMMIT");
				mysql_query("END");

				instructLog(($_GET['type'] == 1 ? '系统' : '网站') . '信息修改', 'edt');
				$msg[0] = ($_GET['type'] == 1 ? '系统' : '网站') . '信息修改成功';
				$msg[1] = 'success';
			}
			else {
				mysql_query("ROLLBACK");
				mysql_query("END");
				$msg[0] = ($_GET['type'] == 1 ? '系统' : '网站') . '信息修改失败';
				$msg[1] = 'fail';
			}
		}
	}

	$i = 0;
	$code = '';
	$getdata = $my_db->selectRow('*', 'system', array('tid' => $_GET['type'], 'lang' => ($_GET['type'] == 2 ? ($_GET['lang'] ? $_GET['lang'] : $con_lang_default) : '')));
	while($result = mysql_fetch_array($getdata)){
		if($result['type'] == 'hidden') continue;

		$code .= '<div class="form-group"><label for="sbt_' . $result['varname'] . '">' . $result['info'] . ($_GET['type'] == 2 ? ' &nbsp; <span style="color: gray; font-weight: normal;">CO_' . strtoupper($result['varname']) . '</span>' : '') . '</label>';

		if($result['type'] == 'boolean') {
			$code .= '<input type="checkbox" name="sbt_' . $result['varname'] . '"' . ($result['value'] == 'true' ? ' checked="checked"' : '') . '/>';
		}
		else if($result['type'] == 'array'){
			$code .= '<select class="form-control" name="sbt_' . $result['varname'] . '" id="sbt_' . $result['varname'] . '">';
			preg_match('/(^[^\[]+)\[([^\]]+)\]$/', $result['value'], $match);
			$selpro = explode('|', $match[2]);
			for($j = 0; $j < count($selpro); $j++){
				$project = explode(':', $selpro[$j]);
				$code .= '<option value="' . $project[0] . '"' . ($match[1] == $project[0] ? ' selected="selected"' : '') . '>' . $project[1] . '</option>';
			}
			$code .= '</select>';
		}
		else if($result['type'] == 'date') {
			$code .= '<input type="text" class="form-control" name="sbt_' . $result['varname'] . '" value="' . $result['value'] . '" id="sbt_' . $result['varname'] . '" onfocus="WdatePicker({dateFmt:\'yyyy-MM-dd\'})" />';
		}
		else $code .= '<input type="text" class="form-control" name="sbt_' . $result['varname'] . '" value="' . $result['value'] . '" id="sbt_' . $result['varname'] . '" />';

		// 添加功能按钮
		if ($result['varname'] == 'page_cache_time') {
			$code .= ' <input type="button" onclick="clearPageCache()" value="立即清除" />';
		}

		$code .= '</div>';
		$i++;
	}

	require('templates/head.html');
	require('templates/system.html');
}
else if($_GET['action'] == 'lst'){
	require('templates/head.html');
	$q_url = queryPart('date', 'desc');

?>
<body id="adminbody1">
<form action="<?=$q_url['12456'] . '&flag=' . $_GET['flag'];?>" method="post">
<?php
	$where = '';
	$operate = array('add' => '添加', 'edt' => '编辑', 'del' => '删除', 'vfy' => '审核', 'otr' => '其他');
		
	echo tableFields(
		array(
			'user' => '用户', 
			'operate' => array('操作', 'read', array($operate)), 
			'detail' => '内容', 
			'date' => '日期'
		),
		array(
			'where' => $where,
			'table' => 'log'
		)
	);

	setOperate(array('name' => 'log', 'where' => $where), array('power' => 'system', 'search' => 'user|用户,detail|内容,date|日期'));
?>
</form>
<?php
}

require('templates/foot.html');
