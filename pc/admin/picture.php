<?php
require('../include/common.php');
require('../include/fun_admin.php');
require('../include/cls_graphic.php');

$err = '';
$msg = array();

if ($_GET['type'] == 2) {
	$power_key = 'info';
	$picture_src = '../' . systemConfig('info_img_path') . $con_pic['pre']['info'];
}
else if ($_GET['type'] == 3) {
	$power_key = 'products';
	$picture_src = '../' . systemConfig('products_img_path') . $con_pic['pre']['products'];
}

if ($_GET['type']) {
	$res = $my_db->fetchOne($cms_cata_type[$_GET['type']]['db'], array('id' => $_GET['oid']));
	$res['fields'] = json_decode($res['fields'], true);
}

if ($_GET['action'] == 'upload'){
	preg_match('/\.(\w{2,6})$/', $_FILES[$_POST['fields']]['name'], $match);

	$file_name = time() . '.' . $match[1];
	move_uploaded_file($_FILES[$_POST['fields']]['tmp_name'], '../' . systemConfig('file_path') . $file_name);

	echo json_encode(array('filepath' => systemConfig('file_path') . $file_name, 'filename' => $file_name, 'error' => 0, 'msg' => ''));
	exit;

} else if($_GET['action'] == 'edt'){
	if($_GET['num']) $power_id = 2;
	else if($_POST['del'] == 'true') $power_id = 3;
	else $power_id = 1;

	if($_GET['num']){
		$picture_arr['src'] = $res['fields']['picture'][$_GET['num']];
	}

	if($_POST['del'] == 'true'){
		if(!adminPower($power_key, $power_id)) warning('权限不足');
		else {
			$done = 1;
			$imgarr = array();
			$fields = $res['fields'];

			foreach($_POST as $key => $value){
				$tmp = explode('chk_', $key);

				if ((int)$tmp[1] > 0) {
					$exist = true;

					array_push($imgarr, $res['fields']['picture'][$tmp[1]]);
					unset($fields['picture'][$tmp[1]]);
				}
			}

			$done &= $my_db->saveRow($cms_cata_type[$_GET['type']]['db'], array('fields' => json_encode($fields)), array('id' => $_GET['oid']));

			$getdata = $my_db->selectRow('*', 'language', array('connect' => $con_lang_current));
			while($result = mysql_fetch_array($getdata)){
				$done &= $my_db->saveRow($cms_cata_type[$_GET['type']]['db'], array('fields' => json_encode($fields)), array('id' => $_GET['oid']), $result['abbr']);
			}

			if($done && $exist){
				mysql_query("COMMIT");
				mysql_query("END");

				for($i = 0; $i < count($imgarr); $i++){
					if (strpos($imgarr[$i], 'video_') === 0) {
						if(file_exists($picture_src . $imgarr[$i])) unlink($picture_src . $imgarr[$i]);
					}
					else {
						$big_img = $picture_src . $con_pic['suf']['big'] . $imgarr[$i];
						$mid_img = $picture_src . $con_pic['suf']['mid'] . $imgarr[$i];
						$sml_img = $picture_src . $con_pic['suf']['sml'] . $imgarr[$i];

						if(file_exists($big_img)) unlink($big_img);
						if(file_exists($mid_img)) unlink($mid_img);
						if(file_exists($sml_img)) unlink($sml_img);
					}
				}

				$msg[0] = '相册删除成功';
				instructLog('相册删除', 'del');
			}
			else {
				mysql_query("ROLLBACK");
				mysql_query("END");

				if(!$exist){
					$msg[0] = '未选择要删除的项目';
					$msg[1] = 'warn';
				}
				else {
					$msg[0] = '相册删除失败';
					$msg[1] = 'fail';
				}
			}

			$href = $_SERVER['PHP_SELF'] . '?action=lst' . preg_replace('/action=[^&]*&/', '&', $_SERVER['QUERY_STRING']);
			header('Location: ' . $href . '&msg[]=' . urlencode($msg[0]) . '&msg[]=' . $msg[1]);
		}
	}
	else if($_POST['save'] == 'true'){
		if(!adminPower($power_key, $power_id)) warning('权限不足');

		$chk_post = new ChkRequest('sbt_');

		$format = 'image';
		$_POST['sbt_queue'] = min(max((int)$_POST['sbt_queue'], 0), $cms_max_num['queue']);

		if (strpos($_FILES['sbt_img']['type'], 'video/') === 0) {
			// 视频格式
			$format = 'video';
			$valid_type = array('x-flv');
			$type = strtr($_FILES['sbt_img']['type'], array('video/' => ''));

			if (!in_array($type, $valid_type)) {
				warning('视频格式不支持');
			}
			else {
				$_POST['sbt_src'] = 'video_' . $chk_post->createName($_FILES['sbt_img']['name']);
			}
		}
		else {
			// 图片格式
			$_POST['sbt_src'] = $chk_post->chkImage('img');
		}

		if ($picture_arr['src']) {
			preg_match('/(\.[\w]{3,4})$/', $_POST['sbt_src'], $match);

			if ($match[1]) {
				$_POST['sbt_src'] = preg_replace('/\.[\w]{3,4}$/', '', $picture_arr['src']) . $match[1];
				$picture_arr['src'] = $_POST['sbt_src'];
			} else {
				$_POST['sbt_src'] = $picture_arr['src'];
			}
		}

		if ($_POST['sbt_queue'] != $_GET['num'] && array_key_exists($_POST['sbt_queue'], $res['fields']['picture']))  {
			warning('序列号已存在');
		}

		if(!$err){
			$num = 1;

			if (isset($res['fields']['picture'])) {
				// 取出最大的鍵值
				$tmp1 = $res['fields']['picture'];
				krsort($tmp1);
				$num = key(array_slice($tmp1, 0, 1, true));
				$num ++;
			}
			else {
				$res['fields']['picture'] = array();
			}

			$done = 1;

			if (!$_GET['num'] || $_POST['sbt_queue'] != $_GET['num']) {
				if ($_POST['sbt_queue'] != $_GET['num']) {
					unset($res['fields']['picture'][$_GET['num']]);
					$num = $_POST['sbt_queue'];
				}

				$res['fields']['picture'][$num] = $_POST['sbt_src'];
				$done &= $my_db->saveRow($cms_cata_type[$_GET['type']]['db'], array('fields' => json_encode($res['fields'])), array('id' => $_GET['oid']));
				
				$getdata = $my_db->selectRow('*', 'language', array('connect' => $con_lang_current));
				while($result = mysql_fetch_array($getdata)){
					$done &= $my_db->saveRow($cms_cata_type[$_GET['type']]['db'], array('fields' => json_encode($res['fields'])), array('id' => $_GET['oid']), $result['abbr']);
				}
			}

			if($done){
				if($_FILES['sbt_img']['tmp_name']){

					if ($format == 'image') {
						$big_img = $picture_src . $con_pic['suf']['big'] . $_POST['sbt_src'];
						$mid_img = $picture_src . $con_pic['suf']['mid'] . $_POST['sbt_src'];
						$sml_img = $picture_src . $con_pic['suf']['sml'] . $_POST['sbt_src'];

						if(file_exists($big_img)) unlink($big_img);
						if(file_exists($mid_img)) unlink($mid_img);
						if(file_exists($sml_img)) unlink($sml_img);
						if(file_exists($_POST['tmp_img'])) unlink($_POST['tmp_img']);
						move_uploaded_file($_FILES['sbt_img']['tmp_name'], $big_img);

						$size = array('big' => systemConfig('img_max_size'), 'mid' => systemConfig('img_mid_size'), 'sml' => systemConfig('img_sml_size'));

						$imgop = new Graphic($big_img);

						$imgop->resizeImage($big_img, $size['big'], $size['big']);
						$imgop->resizeImage($mid_img, $size['mid'], $size['mid']);
						$imgop->resizeImage($sml_img, $size['sml'], $size['sml']);
					} 
					else if ($format == 'video') {
						move_uploaded_file($_FILES['sbt_img']['tmp_name'], $picture_src . $_POST['sbt_src']);
					}
				}

				instructLog('相册编辑', ($poser_id == 1 ? 'add' : 'edt'));
				$msg[0] = '相册编辑輯成功';

				if($_GET['num']){
					$href = $_SERVER['PHP_SELF'] . '?action=lst' . preg_replace('/action=[^&]+|&num=\d+/', '', $_SERVER['QUERY_STRING']);
					header('Location: ' . $href . '&msg[]=' . urlencode($msg[0]) . '&msg[]=' . $msg[1]);
				}
			}
			else {
				$msg[0] = '相册编辑失败';
				$msg[1] = 'fail';
			}
		}
	}

	require('templates/head.html');
	require('templates/picture.html');
}
else if($_GET['action'] == 'lst'){
	require('templates/head.html');

	if(!$_GET['page']) $_GET['page'] = 1;
	if(!$_GET['display']) $_GET['display'] = systemConfig('cms_display_qty');

	$q_url = '&display=' . $_GET['display'] . '&type=' . $_GET['type'] . '&oid=' . $_GET['oid'];

?>
<body id="adminbody1">
<form action="<?=$_SERVER['PHP_SELF'] . '?action=edt&page=' . $_GET['page'] . $q_url;?>" method="post">

	<div id="inner_form">
		<table width="99%" cellspacing="2">
			<tbody align="center">
				<tr bgcolor="#eaeaea" class="titletr">
					<td><input type="checkbox" name="chk_all" /> 全选</td>
					<td>缩略图</td>
					<td>操作</td>
				</tr>
				<?php
					$total = 0;
					if ($res['fields']['picture']) {
						foreach ($res['fields']['picture'] as $key => $value) {
							if($total < $_GET['display'] * $_GET['page'] && $total >= $_GET['display'] * ($_GET['page'] - 1)){

								$table = '<tr bgcolor="' . (($total % 2) ? '#ebeff1' : '#e8f2f8') . '">';
								$table .= '<td><input type="checkbox" name="chk_' . $key . '" /></td>';

								$src = '';
								if (strpos($value, 'video_') === 0) {
									$src = 'images/video-icon.png';
								}
								else {
									$src = $picture_src . $con_pic['suf']['sml'] . $value;
								}
								
								$table .= '<td><img src="' . $src . '" height="80" /></td>';
							
								$table .= '<td>';
								if(adminPower($cms_cata_type[$_GET['type']]['db'], 2)){
									$table .= '<a href="' . $_SERVER['PHP_SELF'] . '?action=edt&num=' . $key . '&page=' . $_GET['page'] . $q_url . '">详细</a>';
								}

								echo $table . '</td></tr>';
							}

							$total ++;
						}
					}
				?>
			</tbody>
		</table>
	</div>

	<div class="d_operate" id="d_operate">
		<div class="d_ico2">
			<a href="<?=$_SERVER['PHP_SELF'] .'?action=edt' . $q_url;?>" title="新增" class="t_new"></a>
			<a onclick="deleteSth()" href="javascript:void(0);" title="删除" class="t_del"></a>
			<a class="f_list" title="返回列表页" href="#"></a>

			<?php if ($_GET['page'] == 1) { ?>
			<a class="f_prev" title="上一页" href="#"></a>
			<?php } else { ?>
			<a class="t_prev" title="上一页" href="<?=$_SERVER['PHP_SELF'] . '?action=lst&page=' . ($_GET['page'] - 1) . $q_url;?>"></a>
			<?php } ?>

			<?php if($_GET['page'] < ceil($total / $_GET['display'])){ ?>
			<a class="t_next" title="下一页" href="<?=$_SERVER['PHP_SELF'] . '?action=lst&page=' . ($_GET['page'] + 1) . $q_url;?>"></a>';
			<?php } else { ?>
			<a class="f_next" title="下一页" href="#"></a>
			<?php } ?>

			<a class="t_page" title="跳转到第几页" href="javascript:void(0);" onclick="operateInput(this, <?=$_GET['page'];?>)"></a>
			<a class="t_show" title="每页显示数量" href="javascript:void(0);" onclick="operateInput(this, <?=$_GET['display'];?>)"></a>
			<a class="f_search" title="搜索" href="#"></a>
			<a class="t_fresh" title="刷新页面" href="javascript:void(0);" onclick="window.location.reload();"></a>
		</div>

		<div class="d_page">
			<?=$_GET['page'] . '/' . ceil($total / $_GET['display']) . ' 页 &nbsp; 共 ' . $total . ' 条记录';?>
		</div>
	</div>

</form>
<?php
}
?>
<script src="javascript/common.js" language="javascript"></script>
<script src="javascript/admin.js" language="javascript"></script>
</body>
</html>
