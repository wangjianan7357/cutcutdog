<?php
require('../include/common.php');
require('../include/fun_admin.php');

$err = '';
$msg = array();

if($_GET['action'] == 'edt'){
	if($_POST['backup'] == 'true') {
		if(!adminPower('system', 2)) warning('權限不足');

		if(!$err){
			$sql = '';
			//$file_sep_part = 'sql_p_' . date('YmdHi', time()) . '_';
			$file_all_name = 'sql_a_' . date('YmdHi', time()) . '.bak';

			$getstatus = $my_db->tableStatus($con_db_name);

			while($result = mysql_fetch_array($getstatus)){
				if(!preg_match('/_v_[a-z]{2}/', $result['Name'])){
					$sql .= $my_db->buildSqlSyntax('create', $result['Name']);
					$sql .= $my_db->buildSqlSyntax('insert', $result['Name']);
				}
			}

			$my_db->backupSql($sql, $con_db_bakup . '/' . $file_all_name, $_POST['backup_position']);
			instructLog('备份数据 ' . $file_all_name, 'otr');

			$msg[0] = '全部数据表备份完成';

			// 备份到本地的方式要在此做结束，否则会将下面的 HTML 代码也写进备份文件
			//if($_POST['backup_position'] == 'local') exit;
		}
	}
	else if($_POST['resume'] == "true"){
		set_time_limit(400);
		if(!adminPower('system', 3)) warning('權限不足');
		
		if($_POST['resume_position'] == 'server'){
			$file = $con_db_bakup . '/sql_a_' . $_POST['resume_s_file'] . '.bak';
			if(!file_exists($file)) warning('备份文件不存在');
		}
		else if($_POST['resume_position'] == 'local'){
			switch($_FILES['resume_l_file']['error']){
				case 1:
				case 2: warning('上传的文件大于服务器限定值'); break;
				case 3: warning('未能从本地完整上传文件'); break;
				case 4: warning('从本地上传文件失败'); break;
			}

			if(!is_uploaded_file($_FILES['resume_l_file']['tmp_name'])) warning('本地备份文件上传失败');
			else $file = $_FILES['resume_l_file']['tmp_name'];
		}

		if(!$err){
			mysql_query('BEGIN');

			function importSql($filename){
				global $my_db;
				$done = 1;
				$sql_arr = file($filename);
				foreach($sql_arr as $sql){
					str_replace("\r", '', $sql);
					str_replace("\n", '', $sql);
					$sql = trim($sql);

					if(!empty($sql)) $done &= $my_db->myQuery($sql);
				}
				return $done;
			}

			$done = 1;
			$done &= importSql($file);

			if($done){
				mysql_query("COMMIT");
				mysql_query("END");
				instructLog('导入数据 ' . $file, 'otr');
				$msg[0] = '备份文件导入数据库成功';
			}
			else {
				mysql_query("ROLLBACK");
				mysql_query("END");
				$msg[0] = '备份文件导入数据库失败';
				$msg[1] = 'fail';
			}
		}
	}

	$recent = 0;
	$opt_code = '';
	$recent_code = '';

	if(checkFolder($con_db_bakup)) {
		$handle = opendir($con_db_bakup);
		while($file = readdir($handle)){
			preg_match('/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})\.bak$/', $file, $match);
			if($match[0]) $opt_code .= '<option value="' . (float)$match[0] . '">' . $match[1] . '年' . (int)$match[2] . '月' . (int)$match[3] . '日 ' . $match[4] . ':' . $match[5] . '</option>';

			if((float)$match[0] > $recent){
				$recent = (float)$match[0];
				$recent_code = $match[1] . '年' . (int)$match[2] . '月' . (int)$match[3] . '日 ' . $match[4] . ':' . $match[5];
			}
		}
		closedir($handle);
	}

	require('templates/head.html');
	require('templates/backup.html');
}
?>
<script src="javascript/common.js" language="javascript"></script>
<script src="javascript/admin.js" language="javascript"></script>
</body>
</html>
