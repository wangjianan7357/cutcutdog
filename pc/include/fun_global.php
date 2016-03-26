<?php
/**
 * 设置通用函数
 * 在网站和 CMS 均使用
 */

 /* 记录错误信息 */
function warning($str){
    global $err;
    if(!$err) $err = $str;
}

/* 获取系统参数 */
function systemConfig($varname){
    global $my_db;

    $getdata = $my_db->selectRow('value, type', 'system', array('tid' => 1, 'varname' => $varname));
    $result = mysql_fetch_array($getdata);

    if($result['type'] == 'array'){
        preg_match('/^([^\[]+)\[/', $result['value'], $match);
        return $match[1];

    } else if($result['type'] == 'boolean'){
        return ($result['value'] === 'true' ? true : false);
    }
    else return $result['value'];
}

/* 定义获取浏览者 IP */
function getClientIP(){
    if($_SERVER['HTTP_CLIENT_IP'] && $_SERVER['HTTP_CLIENT_IP'] != 'unknown') return $_SERVER['HTTP_CLIENT_IP'];
    elseif($_SERVER['HTTP_X_FORWARDED_FOR'] && $_SERVER['HTTP_X_FORWARDED_FOR'] != 'unknown') return $_SERVER['HTTP_X_FORWARDED_FOR'];
    else return $_SERVER['REMOTE_ADDR'];
}

/* 检测文件夹 */
function checkFolder($dir){
    if(!is_dir($dir)) @mkdir($dir, 0777);

    if(is_dir($dir)){
        // 检测文件夹是否可写
        if($fp = @fopen($dir . '/test.test', 'w')){
            @fclose($fp);
            @unlink($dir . '/test.test');
            return true;
        } 
        else return false;
    }
}

/* json 编码，不处理中文字符 */
function json_encode_un($arr){
    $str = json_encode($arr);
    return $str;
    //$search = "#\\\u([0-9a-f]+)#ie";
    //$replace = "iconv('UCS-2', 'UTF-8', pack('H4', '\\1'))";

    //return preg_replace($search, $replace, $str);
}

/* 在符号处截断字符 */
function cutString($str, $cut = 0, $symbol = ' ', $points = true){
    if (!$cut || $cut > strlen($str)) return $str;

    if (!strpos($str, $symbol)) {
        preg_match_all("/./u", $str, $match);

        $words = '';
        for($i = 0, $j = 0; $i < count($match[0]); $i++) {
            $j = ord($match[0][$i]) > 127 ? $j + 2 : $j + 1;
            if($j > $cut - 1) {
                return $words . ($points ? '...' : '');
            }
            $words .= $match[0][$i];
        }
        return join('', $match[0]);
    }
    else {
        $pos = 0;
        while(1){
            $tmp = strpos($str, $symbol, $pos + 1);
            if($tmp > $cut ||  $pos > $tmp) {
                // 当离截取位置大于15字符时，不做分隔符判断
                if($cut - $pos > 15) {
                    preg_match_all("/./u", $str, $match);

                    for($i = 0, $string = ''; $i < count($match[0]); $i++){
                        $string .= $match[0][$i];

                        if(strlen($string) > $cut) break;
                        else $pos = strlen($string);
                    }
                }
                break;
            }
            else $pos = $tmp;
        }
        return substr($str, 0, $pos) . (($points && $pos < strlen($str)) ? '...' : '');
    }
}

function cutStringWithTag($str, $cut){
    $sep = false;
    $intag = false;
    $runend = false;
    $endtag = '';
    $string = '';
    $tags = array();
    
    preg_match_all("/./u", $str, $match);

    for($i = 0, $j = 0; $i < count($match[0]); $i++){
        if($j >= $cut) break;
        else $string .= $match[0][$i];

        $chr = $match[0][$i];

        if($intag) {
            if($chr == '>') {
                $intag = false;
                $sep = true;
            }
            else if($chr == ' ') {
                $sep = true;
            }
            else if ($chr == '/') {
                $runend = true;
            }

            if(!$sep) {
                if(!$runend) {
                    if(!$tags[$num]) $tags[$num] = '';
                    $tags[$num] = $tags[$num] . $chr;
                }
                else if ($chr != '/'){
                    $endtag .= $chr;
                }
            }
            else {
                if ($endtag && $endtag == $tags[count($tags) - 1]) {
                    unset($tags[count($tags) - 1]);
                }
                $endtag = '';
                $runend = false;
            }
            continue;
        }
        else if($chr == '<') {
            $intag = true;
            $sep = false;
            $num = count($tags);
            continue;
        }

        $j++;
    }

    for($i = count($tags) - 1; $i >= 0; $i--) {
        if($tags[$i] == 'br') continue;
        $string .= '</' . $tags[$i] . '>';
    }

    return $string;
}

?>