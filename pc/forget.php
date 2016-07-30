<?php
$con_posi = 'member';

session_start();

require_once('include/fun_web.php');
require_once('include/common.php');
require_once('include/initial.php');

$err = '';

if ($_POST['reset'] == 'true') {
    $verify = $my_db->fetchOne('verify', array('sign = "' . addslashes($_GET['token']) . '" AND type = 1 AND time > ' . (time() - 1800)));
    if (!$verify || md5($verify['email']) != $_GET['m']) {
        $err = '該鏈接驗證已失效，請重新提交！';
    }

    $chk_post = new ChkRequest('sbt_');
    $chk_post->chkPassword(array('password' => '密碼'), array('confirm' => '確認密碼'));

    if(!$err){
        $my_db->saveRow('member', array('pass' => md5($_POST['sbt_password'])), array('email' => $verify['email']));
        $my_db->deleteRow('verify', array('id' => $verify['id']));

        $msg = '密碼修改成功！';
    }

} else if ($_GET['token']) {
    $getdata = $my_db->fetchOne('verify', array('sign = "' . addslashes($_GET['token']) . '" AND type = 1 AND time > ' . (time() - 1800)));
    if (!$getdata) {
        $err = '該鏈接驗證已失效，請重新提交！';
    }
}

?>
<!DOCTYPE html>
<html lang="zh-CN" class="root61">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="css/member.css" type="text/css" />
<?php
$script = '';
if($err) echo '<script language="javascript">alert("' . $err . '"); history.go(-1); </script>';
if($msg) echo '<script language="javascript">alert("' . $msg . '"); </script>';
if($href) echo '<script language="javascript">document.location.href = "' . $href . '"; </script>';
?>
</head>
<body>
<div class="regist_wrap">	        	
    <div class="regist_tab">
        <ul class="clearfix">
            <li><i class="r_mail"></i><strong><span>找回密碼</span></strong></li>
        </ul>
        <p class="cur_tab cur"><em></em></p>
    </div>

    <form class="regist_form mobile_register_form" action="forget.php?token=<?= strip_tags($_GET['token']); ?>&m=<?= strip_tags($_GET['m']); ?>" method="post">
        <input type="hidden" value="true" name="reset">

        <ul class="clearfix">
            <li>
                <div class="form_item">
                    <label>新密碼：</label>
                    <input type="password" name="sbt_password" class="ipt ipt_phone" placeholder="">
                </div>
            </li>
            <li>
                <div class="form_item">
                    <label>確認密碼：</label>
                    <input type="password" name="sbt_confirm" class="ipt ipt_phone" placeholder="">
                </div>
            </li>
            <li class="regist_btn">
                <br />
                <button type="button" onclick="javascript:this.form.submit();">提交</button>
            </li>
        </ul>
	</form>
</div>

</body>
</html>