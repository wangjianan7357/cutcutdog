<?php
require('../include/common.php');
require('../include/cls_graphic.php');
require('../include/fun_admin.php');

header("Content-type: text/html; charset=utf-8"); 

/*
 * 上传视频格式
 */
if (strpos($_FILES['sbt_img']['type'], 'video/') === 0) {
	// 不做处理
	exit;
}

$err = '';
$newwidth = 400;
$newheight = 200;

$chk_post = new ChkRequest();

/*
 * 生成临时图片
 * 格式 date('YmdHis') . rand(100, 999) . strtolower($match[1]);
 */
if($d = $chk_post->chkImage($_POST['x_img'])){
	preg_match('/(.[\w]+)$/is', $_FILES[$_POST['x_img']]['name'], $match);
	$tmp_img = '../' . $con_tmp_dir . date('YmdHis') . rand(100, 999) . strtolower($match[1]);

	if(file_exists($tmp_img)) unlink($tmp_img);

	move_uploaded_file($_FILES[$_POST['x_img']]['tmp_name'], $tmp_img);

	$graphic = new Graphic($tmp_img);
	$width = $graphic->getWidth();
	$height = $graphic->getHeight();

	$width_ratio = 1;
	$height_ratio = 1;
	if(!$_POST['img_w']) $_POST['img_w'] = $newwidth;
	if(!$_POST['img_h']) $_POST['img_h'] = $newheight;

	$width_ratio = $_POST['img_w'] / $width;
	$height_ratio = $_POST['img_h'] / $height;

	if($width_ratio < $height_ratio) $ratio = $width_ratio;
	else $ratio = $height_ratio;

	$newwidth = $width * $ratio;
	$newheight = $height * $ratio;
}
?>

<script type="text/javascript">
var err = "<?=$err;?>";

if(err) alert(err);
else if(<?=$_POST['x_img'] ? 1 : 0;?>){
	var showimg = window.parent.document.getElementById("showimg");
	showimg.setAttribute("src", "<?=$tmp_img;?>");
	//showimg.setAttribute("width", <?=$newwidth;?>);
	showimg.setAttribute("height", <?=$newheight;?>);

	if(window.parent.document.getElementById("tmpimg")){
		window.parent.document.getElementById("tmpimg").value = "<?=$tmp_img;?>";
	}
	else {
		var hiddenField = document.createElement("input");
		hiddenField.setAttribute("type", "hidden");
		hiddenField.setAttribute("name", "tmp_img");
		hiddenField.setAttribute("value", "<?=$tmp_img;?>");
		hiddenField.setAttribute("id", "tmpimg");
	}

	try {
		showimg.parentNode.appendChild(hiddenField);
	}
	catch (e){}
}
</script>