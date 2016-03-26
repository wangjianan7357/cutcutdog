<?php
require('../include/common.php');
require('../include/fun_admin.php');

if(!isset($_REQUEST['new'])) {
	exit;
}

$attrdefine = new AttrDefine();
$attrdefine->getTagCatalog($_REQUEST['new']);

/*
if($_REQUEST['old'] && !$attrdefine->compareFields($_REQUEST['old'])) {
	echo 0;
	exit;
}
*/

$fields = $attrdefine->createFields($_REQUEST['id']);

$i = 0;
$code = '';

foreach ($fields as $value) {
	$i++;

	if($i % 2 == 1) {
		$code .= '<tr>';
	}

	$code .= '<td width="20%" align="right">' . $value['text'] . '</td><td width="30%" align="left"' . $rowspan . '>' . $value['form'] . '</td>';

	if($i % 2 == 0) {
		$code .= '</tr>';
	}
	else if ($i == count($fields)) {
		$code .= '<td colspan="2">&nbsp;</td>';
	}
}

echo $code ? $code : -1;