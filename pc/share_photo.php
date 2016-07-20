<?php
require_once('include/fun_web.php');
require_once('include/common.php');

$cur_data = $my_db->fetchOne('info', array('id' => $_GET['id']));
$info_img_path = systemConfig('info_img_path');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>

<img src="<?= $info_img_path . $con_pic['pre']['info'] . $con_pic['suf']['mid'] . $cur_data['src']; ?>" />

</body>
</html>