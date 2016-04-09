<?php
require('../include/common.php');
require('../include/fun_admin.php');

// clear cache
if(date('Ymd') > systemConfig('clear_cache')){
	if($my_db->tableExist('log')) {
		$my_db->deleteRow('log', array('TO_DAYS(NOW()) - TO_DAYS(`date`) > ' . systemConfig('auto_clear_log')));
	}
	
	$dh = opendir('../' . $con_tmp_dir);
	while($item = readdir($dh)){
		preg_match('/^' . systemConfig('clear_cache') . '/', $item, $match);
		if($match && is_file('../' . $con_tmp_dir . $item)) unlink('../' . $con_tmp_dir . $item);
	}

	$my_db->saveRow('system', array('value' => date('Ymd')), array('varname' => 'clear_cache', 'tid' => 1));
}

$outcome = array();

$outcome['member'] = array();
$outcome['member']['count'] = $my_db->existRow('member');

require('templates/head.html');
require('templates/index.html');
require('templates/foot.html');
?>