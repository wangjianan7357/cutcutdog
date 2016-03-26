<?php
/**
 * 自定义属性
 */

class AttrDefine {
	public $fields;
	public $prefix;
	public $table;

	public static $define = array(
		1 => array(
			'text' => '单行文本域',
			'catalog' => true,
		),
		2 => array(
			'text' => '多行文本域',
			'catalog' => true,
		),
		5 => array(
			'text' => '单选标签组',
			'path' => true,
			'catalog' => true,
		),
		6 => array(
			'text' => '复选标签组',
			'path' => true,
			'catalog' => true,
		),
		7 => array(
			'text' => '选项',
			'path' => true,
		),
		8 => array(
			'text' => '文件上传',
			'catalog' => true,
		),
	);

	function __construct($prefix = 'sbt_fields_') {
		$this->prefix = $prefix;
	}

	public static function needPath($id) {
		if (self::$define[$id]['path']) {
			return true;
		} else {
			return false;
		}
	}

	public static function getText() {
		$tmp = array();
		foreach (self::$define as $key => $value) {
			$tmp[$key] = $value['text'];
		}
		return $tmp;
	}

	// 获取分类对象字段
	public function getTagCatalog($cid) {
		global $my_db;
		global $cms_cata_type;

		$catalog = explode(',', $cid);

		$module = $my_db->fetchOne('catalog', array('id' => $catalog[0]));
		$this->table = $cms_cata_type[$module['type']]['db'];

		$tnp = '';
		$where = '0 = 1';
		for ($i = 0; $i < count($catalog) - 1; $i++) {
			$where .= ' OR `cid` = "' . $catalog[$i] . '"';
		}

		$fields = array();
		$getdata = $my_db->selectRow('*', 'tag', array($where), array('LENGTH(`cid`) DESC, `queue` DESC'));
		while($result = mysql_fetch_array($getdata)) {
			$fields['cid'] = $result['cid'];
			$fields['define'][] = $result;
		}

		$this->fields = $fields;
		return $this->fields;
	}

	// 判断是否和之前的分类对象字段相同
	public function compareFields($cid) {
		$savefields = $this->fields;
		$compare = $this->getTagCatalog($cid);
		$this->fields = $savefields;

		if($compare['cid'] == $savefields['cid']) {
			return 0;
		} else {
			return 1;
		}
	}


	public function createFields($id = '') {
		global $my_db;

		$res = array();
		$fields = array();

		if ($id) {
			$tmp = array();
			$tmp = $my_db->fetchOne($this->table, array('id' => $id));
			$fields = json_decode($tmp['fields'], true);
		}

		$tmp = array();

		if ($this->fields) {
			foreach ($this->fields['define'] as $key => $value) {
				$tmp['text'] = $value['name'];

				switch ($value['define']) {
					case 1:
						$tmp['form'] = '<input name="' . $this->prefix . $value['mark'] . '" style="width: 95%;" value="' . $fields[$value['mark']] . '" />';
					break;
					case 2:
						$tmp['form'] = '<textarea name="' . $this->prefix . $value['mark'] . '" style="width: 95%;">' . $fields[$value['mark']] . '</textarea>';
					break;

					case 5:
						// 设置默认选项以避免全不选时提交无效
						$tmp['form'] = '<input name="' . $this->prefix . $value['mark'] . '[_]" type="hidden" value="">';

						$getdata = $my_db->selectRow('*', 'tag', array('tid' => $value['id']));
						while($result = mysql_fetch_array($getdata)) {
							$tmp['form'] .= '<input name="' . $this->prefix . $value['mark'] . '[' . $result['mark'] . ']" type="checkbox" value="1" ' . ($fields[$value['mark']][$result['mark']] ? 'checked="checked"' : '') . '> ' . $result['name'] . ' &nbsp; ';
						}
					break;
					case 6:
						$tmp['form'] = '<input name="' . $this->prefix . $value['mark'] . '[_]" type="hidden" value="">';
						$getdata = $my_db->selectRow('*', 'tag', array('tid' => $value['id']));
						while($result = mysql_fetch_array($getdata)) {
							$tmp['form'] .= '<input name="' . $this->prefix . $value['mark'] . '[' . $result['mark'] . ']" type="radio" value="1"> ' . $result['name'] . ' &nbsp; ';
						}
					case 8:
						$tmp['form'] = '<input type="file" name="upload_' . $value['mark'] . '" value="" data-action="upload_file" data-target="' . $this->prefix . $value['mark'] . '" id="upload_' . $value['mark'] . '"><input type="hidden" name="' . $this->prefix . $value['mark'] . '" value="' . $fields[$value['mark']] . '" id="' . $this->prefix . $value['mark'] . '">';

						if ($fields[$value['mark']]) {
							$tmp['form'] .= ' <a href="../' . $fields[$value['mark']] . '" target="_blank">下载</a> &nbsp;';
							$tmp['form'] .= ' <a href="javascript:;" data-action="delete_upload" data-target="' . $this->prefix . $value['mark'] . '">删除</a>';
						}

					break;
					
				}
				
				$res[] = $tmp;

			}
		}

		return $res;
	}

}

?>