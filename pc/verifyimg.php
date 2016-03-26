<?php
/** 
 * 验证图片
 */
header('Content-type:image/gif');

$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
$str_a = $chars{mt_rand(0, 26)};
$str_b = $chars{mt_rand(0, 26)};
$str_c = $chars{mt_rand(0, 26)};
$str_d = $chars{mt_rand(0, 26)};
$string = $str_a . $str_b . $str_c . $str_d;

// Gradient Background
$im_width = 60;
$im_height = 24;

$im = imagecreate($im_width, $im_height);

$color1 = imagecolorallocate($im, 76, 104, 161);
$color2 = imagecolorallocate($im, 52, 70, 110);

$color1 = imagecolorsforindex($im, $color1);
$color2 = imagecolorsforindex($im, $color2);

$steps = $im_height;

$r1 = ($color1['red'] - $color2['red']) / $steps;
$g1 = ($color1['green'] - $color2['green']) / $steps;
$b1 = ($color1['blue'] - $color2['blue']) / $steps;

$x1 = 0;
$y1 =& $i;
$x2 = $im_width;
$y2 =& $i;

for($i = 0; $i <= $steps; $i++) {
	$r2 = $color1['red'] - floor($i * $r1);
	$g2 = $color1['green'] - floor($i * $g1);
	$b2 = $color1['blue'] - floor($i * $b1);
	$color = imagecolorallocate($im, $r2, $g2, $b2);
	imageline($im, $x1, $y1, $x2, $y2, $color);
}

$font_color = imagecolorallocate($im, mt_rand(220, 250), mt_rand(220, 250), mt_rand(220, 250));

imagestring($im, mt_rand(3, 5), mt_rand(0, 5), mt_rand(0, 5), $str_a, $font_color);
imagestring($im, mt_rand(3, 5), mt_rand(15, 25), mt_rand(0, 5), $str_b, $font_color);
imagestring($im, mt_rand(3, 5), mt_rand(30, 40), mt_rand(0, 5), $str_c, $font_color);
imagestring($im, mt_rand(3, 5), mt_rand(45, 50), mt_rand(0, 5), $str_d, $font_color);

for($i = 1; $i < 2; $i++){
 imagestring($im, mt_rand(0, 5), mt_rand(-5, 63), mt_rand(-5, 23), ".", imageColorAllocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255)));
}

imagegif($im);

session_start();
$_SESSION['verifycode'] = strtolower($string);

header("Pragma:no-cache\r\n");
header("Cache-Control:no-cache\r\n");
header("Expires:0\r\n");

?>