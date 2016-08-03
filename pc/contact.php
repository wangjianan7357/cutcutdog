<?php

$cur_page = 'catalog';
$_GET['path'] = 'gongsijiejian';

require('include/fun_web.php');
require('include/common.php');
require('include/initial.php');

require('head.php');
?>

<div class="title1">
    <div class="txt">聯繫我們</div>
</div>

<div>
<?= $cur_data['desp']; ?>
</div>

<?php require('foot.php'); ?>