<?php
$con_posi = 'member';

session_start();
setcookie('cookie[memberid]', false, time() - 1000, '/');
setcookie('cookie[pass]', false, time() - 1000, '/');
setcookie('cookie[name]', false, time() - 1000, '/');

$wanturl = '';
if ($_COOKIE['cookie']['wanturl']) {
    $wanturl = $_COOKIE['cookie']['wanturl'];
}
unset($_COOKIE);

if ($wanturl) {
    $_COOKIE['cookie']['wanturl'] = $wanturl;
}

require('include/fun_web.php');
require('include/common.php');
require('include/initial.php');

if($_POST['login'] == 'true'){
    if($_POST['sbt_account'] && $_POST['sbt_pass']){

        $member_arr = null;
        $getdata = $my_db->selectRow('*', 'member', array('pass' => md5($_POST['sbt_pass'])));

        while ($result = mysql_fetch_array($getdata)) {
            if ($result['name'] == $_POST['sbt_account'] || $result['email'] == $_POST['sbt_account'] || $result['phone'] == $_POST['sbt_account']) {
                $member_arr = $result;
                break;
            }
        }

        if($member_arr){
            setcookie('cookie[memberid]', $member_arr['id'], time() + systemConfig('cms_login_time') * 60, '/');
            setcookie('cookie[pass]', $member_arr['pass'], time() + systemConfig('cms_login_time') * 60, '/');
            setcookie('cookie[name]', $member_arr['name'], time() + systemConfig('cms_login_time') * 60, '/');

            if($_COOKIE['cookie']['wanturl']){
                header('Location:' . $_COOKIE['cookie']['wanturl']);
                setcookie('cookie[wanturl]', '', time() - 1000, '/');
            }
            else header('Location: cart.html');
        }
        else $err = '賬戶或密碼有誤';
    }
    else $err = '賬戶或密碼有誤';
}

require('head.php');
?>

<div class="title1">
    <div class="txt">登錄</div>
</div>

<div class="mgt10">
    <form method="post" action="login.php">
        <input type="hidden" name="login" value="true">

        <div class="form_item">
            <label>賬號</label>
            <input type="text" class="input_txt" name="sbt_account">
        </div>
        <div class="form_item">
            <label>密碼</label>
            <input type="password" class="input_txt" name="sbt_pass">
        </div>
        <div class="form_item xc">
            <input type="submit" class="btn_login" value="登录">
        </div>
    </form>
</div>

<?php require('foot.php'); ?>