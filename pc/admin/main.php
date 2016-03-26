<?php
require('../include/common.php');
require('../include/fun_admin.php');

if($_GET['action'] == 'info'){
	$project = array('admin' => '您的角色', 'language' => '多国语言', 'member' => '会员注册', 'catalog' => '频道分类', 'info' => '资讯信息', 'products' => '产品信息');

	$i = 0;
	$tablecode = '';
	foreach($project as $key => $value){
		$haspower = true;
		$tempcode = '<tr bgcolor="' . (($i % 2) ? '#e8f2f8' : '#ebeff1') . '">';
		$tempcode .= '<td width="16%" align="center" style="border:2px solid white;"><b>' . $value . '</b></td>';
		$tempcode .= '<td width="84%" align="left" style="padding:0;"><table width="100%" cellspacing="0" class="white_table"><tbody>';

		switch($key){
			case 'admin':
				$getdata = $my_db->myQuery('SELECT `' . $con_db_table['role'] . '`.`name` FROM `' . $con_db_table['admin'] . '` JOIN `' . $con_db_table['role'] . '` ON `' . $con_db_table['admin'] . '`.`rid` = `' . $con_db_table['role'] . '`.`id` WHERE `' . $con_db_table['admin'] . '`.`id` = "' . $_SESSION['admin_id'] . '"');
				while($getdata && $result = mysql_fetch_array($getdata)){
					$tempcode .= '<tr><td><b>' . $result['name'] . '</b></td></tr>';
					break;
				}
			break;
			case 'language':
				if($haspower = adminPower($key, '1|2|3|4')){
					$tempcode .= '<tr><td>默认语言: ' . $con_lang_arr[$con_lang_default] . '</td></tr>';
					$tempcode .= '<tr><td>已加语言: ';
					$getdata = $my_db->selectRow('*', 'language');
					while($result = mysql_fetch_array($getdata)){
						$tempcode .= $con_lang_arr[$result['abbr']] . ($result['valid'] ? '（<font color="green">有效</font>）' : '（<font color="red">无效</font>）') . '；';
					}
					$tempcode .= '</td></tr>';
				}
			break;
			case 'member':
				if($haspower = adminPower($key, '1|2|3')){
					$total = 0;
					$valid = 0;
					$getdata = $my_db->selectRow('*', $key);
					while($result = mysql_fetch_array($getdata)){
						$total++;
						if($result['check']) $valid++;
					}

					$tempcode .= '<tr><td width="25%">会员数量：' . $total . '</td>';
					$tempcode .= '<td width="75%">审核会员: ' . $valid . '</td></tr>';
				}
			break;
			case 'order':
				if($haspower = adminPower($key, '1|2|3')){
					$total = 0;
					$status = array();
					$getdata = $my_db->selectRow('*', $key);
					while($result = mysql_fetch_array($getdata)){
						$total++;
						if(!isset($status[$result['status']])) {
							$status[$result['status']] = 0;
						}
						$status[$result['status']]++;
					}

					$tempcode .= '<tr><td width="25%">订单总数：' . $total . '</td>';
					$tempcode .= '<td width="25%">未完成: ' . $status[0] . '</td>';
					$tempcode .= '<td width="25%">已提交: ' . $status[1] . '</td>';
					$tempcode .= '<td width="25%">已支付: ' . $status[2] . '</td></tr>';
				}
			break;
			case 'comment':
				if($haspower = adminPower($key, '1|2|3')){
					$total = 0;
					$new = 0;
					$getdata = $my_db->selectRow('*', $key);
					while($result = mysql_fetch_array($getdata)){
						$total++;
						if(!$result['read']) $new++;
					}

					$tempcode .= '<tr><td width="25%">评论总数：' . $total . '</td>';
					$tempcode .= '<td width="75%">未查看: ' . $new . '</td></tr>';
				}
			break;
			case 'catalog':
				if($haspower = adminPower($key, '1|2|3')){
					foreach($cms_cata_type as $type => $value){
						$tempcode .= '<tr><td width="25%">' . $value['txt'] . ': ';
						$getdata = $my_db->selectRow('count(id)', 'catalog', array('type' => $type));
						$result = mysql_fetch_array($getdata);
						$tempcode .= $result[0];

						$getdata = $my_db->selectRow('count(id)', 'catalog', array('type' => $type, 'valid' => 1));
						$result = mysql_fetch_array($getdata);
						$tempcode .= '</td><td width="75%">有效分类: ' . $result[0];

						$tempcode .= '</td></tr>';
					}
				}
			break;
			case 'products':
				if($haspower = adminPower($key, '1|2|3')){
					$total = 0;
					$valid = 0;

					$getdata = $my_db->selectRow('*', $key);
					while($result = mysql_fetch_array($getdata)){
						$total++;
						if($result['valid']) $valid++;
					}

					$tempcode .= '<tr><td width="25%">产品总数：' . $total . '</td>';
					$tempcode .= '<td width="75%">有效产品: ' . $valid . '</td></tr>';
				}
			break;
			case 'info':
				if($haspower = adminPower($key, '1|2|3')){
					$total = 0;
					$valid = 0;

					$getdata = $my_db->selectRow('*', $key);
					while($result = mysql_fetch_array($getdata)){
						$total++;
						if($result['valid']) $valid++;
					}

					$tempcode .= '<tr><td width="25%">资讯总数：' . $total . '</td>';
					$tempcode .= '<td width="75%">有效资讯: ' . $valid . '</td></tr>';
				}
			break;
		}

		if($haspower){
			$tablecode .= $tempcode . '</tbody></table></td></tr>';
			$i++;
		}
	}

	require('templates/head.html');
?>
<body id="adminbody1">
<div id="inner_form">
	<table width="99%" cellspacing="0" class="no_sel_tr">
		<tbody>
			<?=$tablecode;?>
		</tbody>
	</table>
</div>

<?php
	setOperate(array());
}
else if($_GET['action'] == 'help'){
	require('templates/head.html');
?>
<body id="adminbody1">
<div id="inner_form">

	<table width="99%" cellspacing="2">
		<tbody style="line-height:22px;">
			<tr><td align="center" class="title1">系统帮助</td></tr>
	
			<?php if(adminPower('page', 2)){ ?>
			<tr bgcolor="#e5e5e5"><td align="left"><b>&lt;<a href="javascript:userHelp('h_template');">文字模板</a>&gt;</b> （用 assign 标签）</td></tr>
			<tr bgcolor="#f4f4f4">
				<td align="left" id="h_template" style="display:none;">
					<table cellspacing="2" width="100%">
						<tr bgcolor="#ebeff1">
							<td align="left">1. 调用常量：&lt;assign from="constant"&gt;（常量名）&lt;/assign&gt; 各常量名可参考文本框旁边的灰色英文单词<br />
								&nbsp;&nbsp;&nbsp;&nbsp;示例：&lt;assign from="constant"&gt;CO_COMPANYNAME&lt;/assign&gt;（调用公司名称）
							</td>
						</tr>
						<tr bgcolor="#e8f2f8">
							<td align="left">
								2. 调用变量：&lt;assign from="various"&gt;（变量名）&lt;/assign&gt; 各变量名需带美元符号（$）<br />
								&nbsp;&nbsp;&nbsp;&nbsp;示例：&lt;assign from="various"&gt;$ctname&lt;/assign&gt;（在分类页时调用当前页面对应的分类）<br />
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								&lt;assign from="various"&gt;$catalog_list[1000]['name']&lt;/assign&gt;（调用分类数组序号为1000的分类名称）
							</td>
						</tr>
						<tr bgcolor="#ebeff1">
							<td align="left">
								3. 克隆模板：&lt;assign from="clone"&gt;（模板名）&lt;/assign&gt; 各模板名可参考文本框旁边的灰色英文单词<br />
								&nbsp;&nbsp;&nbsp;&nbsp;参数：page 主页面；cpage 子页面；<br />
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								被克隆的模板里面，如果包含克隆标签将不再进行转换<br />
								&nbsp;&nbsp;&nbsp;&nbsp;示例：&lt;assign from="clone" page="index"&gt;P_TITLE&lt;/assign&gt;（克隆 index 页的页面标题模板）<br />
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								&lt;assign from="clone" page="catalog" cpage="sort1"&gt;P_KEY1&lt;/assign&gt;（克隆 index 页的页面标题模板）
							</td>
						</tr>
						<tr bgcolor="#e8f2f8">
							<td align="left">
								4. 调用分类：&lt;assign from="catalog"&gt;（变量名）&lt;/assign&gt;<br />
								&nbsp;&nbsp;&nbsp;&nbsp;参数：level 调用分类的层级，不设则调用全部；target 替换目标，标签内的词将被替换为分类名称；<br />
								&nbsp;&nbsp;&nbsp;&nbsp;示例：&lt;assign from="catalog" target="test"&gt;test, &lt;/assign&gt;（循环调用全部分类名称，并用 , 间隔）<br />
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								&lt;assign from="catalog" level="1" target="lvtest"&gt;&lt;strong&gt;lvtest&lt;/strong&gt;&lt;/assign&gt;（调用第一层的分类名称，并用 strong 标注）
							</td>
						</tr>
						<tr bgcolor="#ebeff1">
							<td align="left">
								5. 词组选择：&lt;assign from="selection"&gt;（词组）&lt;/assign&gt;<br />
								&nbsp;&nbsp;&nbsp;&nbsp;参数：depart 定义词组的区分符号，默认为竖线 | ；<br />
								&nbsp;&nbsp;&nbsp;&nbsp;示例：&lt;assign from="selection"&gt;test1|test2&lt;/assign&gt;（随机出现 test1 或 test2）<br />
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								&lt;assign from="selection" depart="_sep_"&gt;first_sep_second&lt;/assign&gt;（随机出现 first 或 second）
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>

<?=setOperate(array());?>
<?php
}
?>
<script src="javascript/admin.js" language="javascript"></script>
</body>
</html>