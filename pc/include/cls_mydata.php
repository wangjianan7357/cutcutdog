<?php
/**
 * 数据库类
 */

class MyData {
	private $sql;
	private $db_name;

	function __construct($db_set, $db_name){
		$this->db_name = $db_name;
		mysql_connect($db_set['host'], $db_set['user'], $db_set['pass']);
		mysql_select_db($this->db_name);
	}
	
	public function myQuery($sql = null){
		global $con_db_bakup;

		mysql_query("set names 'utf8'");
		mysql_query("set character_set_client=utf8");
		mysql_query("set character_set_results=utf8");

		$this->sql = $sql ? $sql : $this->sql;

		if($result = mysql_query($this->sql)) return $result;
		else {
			$err = 'MySQL Error No.: ' . mysql_errno() . ', ' . mysql_error();
			if(($_SERVER['SERVER_ADDR'] == '127.0.0.1') || ($_SERVER['SERVER_ADDR'] == '::1')){
				echo $err . '<br />';
				echo $this->sql . '<br />';
			} 
			else {
				if($handle = fopen($con_db_bakup . '/log.txt', 'a')){
					fwrite($handle, "\n\n" . date("Y-m-d H:i:s") . ": \n" . $this->sql . "\n" . $err);
					fclose($handle);
				}
			}
			return false;
		}
	}

	public function getLastSql() {
		return $this->sql;
	}

	public function setTableName($table, $lang = null){
		// $child_pre 给数据表加前缀
		global $child_pre;
		global $con_db_table;
		
		preg_match('/(_[a-z]{2}$)/', $table, $match);
		if(!$match[1]) $table = $con_db_table[($child_pre ? preg_replace('/^' . $child_pre . '/', '', $table) : $table)];

		if($lang) $table = preg_replace('/_[a-z]{2}$/', '_' . $lang, $table);

		return $table;
	}
	
	public function selectRow($field, $table, $where = array(), $order = array(), $limit = null, $lang = null){
		$sql = 'SELECT ' . $field . ' FROM `' . $this->setTableName($table, $lang) . '`';

		if($where){
			// eg: array('value' => 'test', 'value = "test"')
			$sql .= ' WHERE 1';
			foreach($where as $key => $value){
				if(gettype($key) == 'string') $sql .= ' AND `' . $key . '` = "' . addslashes($value) . '"';
				// * 以下方式未作引号检测，以适应类似 name="name"，输入时要先做检测 *
				else if(gettype($key) == 'integer' && $value) $sql .= ' AND ' . $value;
			}
		}

		if($order){
			// eg: array('field' => 'order_field', 'method' => 'ASC')
			if($order['field']) $sql .=  ' ORDER BY `' . addslashes($order['field']) . '` ' . ($order['method'] ? addslashes($order['method']) : 'ASC');
			// eg: array('length(`field`), `field`')
			else if($order[0]) $sql .= ' ORDER BY ' . addslashes($order[0]);
		}
		
		$this->sql = $sql . ($limit ? ' LIMIT ' . addslashes($limit) : '') . ';';
		return $this->myQuery();
	}

	public function selectMax($table, $field = 'id', $where = array()){
		$result = array();
		$getdata = $this->selectRow($field, $table, $where, array('field' => $field, 'method' => 'DESC'), 1);
		$result = mysql_fetch_array($getdata);
		return $result[$field];
	}

	public function existRow($table, $where = array()){
		$result = array();
		$getdata = $this->selectRow('COUNT(*)', $table, $where);
		$result = mysql_fetch_array($getdata);
		return $result[0];
	}

	public function fetchOne($table, $where = array(), $order = array(), $lang = null){
		if (empty($order)) {
			$order = array('field' => 'id', 'method' => 'DESC');
		}

		$result = array();
		$getdata = $this->selectRow('*', $table, $where, $order, 1, $lang);
		return mysql_fetch_array($getdata);
	}

	public function saveRow($table, $save = array(), $where = array(), $lang = null){
		$set = '';
		foreach($save as $key => $value){
			$set .= ($set ? ', ' : '') . '`' . $key . '` = "' . addslashes($value) . '"';
		}
		$sql = '`' . $this->db_name . '`.`' . $this->setTableName($table, $lang) . '` SET ' . $set;

		if(empty($where)){
			$sql = 'INSERT INTO ' . $sql;
		}
		else {
			$row = '';
			foreach($where as $key => $value){
				$row .= ($row ? ' AND ' : '') . '`' . $key . '` = "' . addslashes($value) . '"';
			}
			$sql = 'UPDATE ' . $sql . ' WHERE ' . $row;
		}

		$this->sql = $sql . ';';
		return $this->myQuery();
	}

	public function deleteRow($table, $where = array(), $lang = null){
		$sql = '';

		if (count($where) == 1 && $where[0]) {
			$sql = $where[0];
		}
		else {
			foreach($where as $key => $value){
				$sql .= ($sql ? ' AND ' : '') . '`' . $key . '` = "' . addslashes($value) . '"';
			}
		}
		
		$this->sql = 'DELETE FROM `' . $this->db_name . '`.`' . $this->setTableName($table, $lang) . '` WHERE ' . $sql . ';';
		return $this->myQuery();
	}

	public function createTable($table, $lang = null, $engine = 'InnoDB'){
		$tablefields = require('con_tablefields.php');

		$this->dropTable($table, $lang);
		$sql = 'CREATE TABLE `' . $this->db_name . '`.`' . $this->setTableName($table, $lang) . '` (' . $tablefields[$table] . ') ENGINE = ' . $engine . ' CHARSET=utf8';

		$this->sql = $sql;
		return $this->myQuery();
	}

	public function tableExist($table){
		$exist = false;
		$table = $this->setTableName($table, $lang);
		$result = mysql_query('SHOW TABLES');         
		while($getdata = mysql_fetch_row($result)){
			if($getdata[0] == $table){
				$exist = true;
				break;
			}
		}
		return $exist;
	}

	public function alterTable($table, $field = array(), $method, $property = ''){
		$sql = 'ALTER TABLE `' . $this->setTableName($table, $lang) . '` ';
		switch(strtolower($method)){
			case 'order':
				$comma = '';
				$sql .= 'ORDER BY ';
				foreach($field as $value){
					$sql .= $comma . '`' . $value . '` ';
					$comma = ', ';
				}
				$sql .= $property;
			break;
			case 'add':
				$sql .= 'ADD ';
				foreach($field as $key => $value){
					$sql .= '`' . $key . '` ' . $value;
					break;
				}
				$sql .= ' CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;';
			break;
			case 'drop':
				$sql .= 'DROP COLUMN ';
				foreach($field as $value){
					$sql .= '`' . $value . '`;';
					break;
				}
			break;
		}
		$this->sql = $sql;
		return $this->myQuery();
	}

	public function tableStatus($table){
		$this->sql = 'SHOW TABLE STATUS FROM `' . $table . '`;';
		return $this->myQuery();
	}

	public function showColumns($table){
		$this->sql = 'SHOW COLUMNS FROM ' . $this->setTableName($table, $lang) . ';';
		return $this->myQuery();
	}

	public function showCreateTable($table){
		$this->sql = 'SHOW CREATE TABLE ' . $table . ';';
		return $this->myQuery();
	}

	public function truncateTable($table){
		$this->sql = 'TRUNCATE TABLE `' . $table . '`;';
		return $this->myQuery();
	}

	public function dropTable($table, $lang = null){
		$this->sql = 'DROP TABLE IF EXISTS ' . $this->setTableName($table, $lang) . ';';
		return $this->myQuery();
	}

	public function dropView($view, $lang){
		$this->sql = 'DROP VIEW IF EXISTS ' . $this->setTableName($view, $lang) . ';';
		return $this->myQuery();
	}

	public function buildSqlSyntax($method = 'create', $table){
		if($method == 'create'){
			$sql = "\n" . 'DROP TABLE IF EXISTS ' . $table . ";\n";
			
			$getdata = $this->showCreateTable($table);
			while($result = mysql_fetch_array($getdata)){
				$sql .= preg_replace('/\n/', '', $result['Create Table']) . ";\n";
			}
			return $sql;
		}
		else if($method == 'insert'){
			$sql = '';
			$getdata = $this->selectRow('*', $table);
			$fieldnum = mysql_num_fields($getdata);
			while($result = mysql_fetch_array($getdata)){
				$sql .= 'INSERT INTO ' . $table . ' VALUES(';
				for($i = 0; $i < $fieldnum; $i++) $sql .= ($i ? ', ' : '') . '"' . mysql_real_escape_string($result[$i]) . '"';
				$sql .= ')' . ";\n";
			}
			
			return $sql;
		}
	}

	public function backupSql($sql, $filename, $position = 'server'){
		if($position == 'local'){
			ob_end_clean();
			header('Content-Encoding: none');
			header('Content-Type: ' . (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') ? 'application/octetstream' : 'application/octet-stream'));
			header('Content-Disposition: ' . (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') ? 'inline; ' : 'attachment; ') . 'filename=' . preg_replace('/^[\w\W]*?\/([\w\.-]*$)/i', '\\1', $filename));
			header('Content-Length: ' . strlen($sql));
			header('Pragma: no-cache');
			header('Expires: 0');
			echo $sql;

			$e = ob_get_contents();
			ob_end_clean();
		}
		else if($position == 'server'){
			$fp = fopen($filename, 'w+');
			fwrite($fp, $sql);
			fclose($fp);
		}
	}
	
}

?>