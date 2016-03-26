<?php
/**
 * MatchHtml : operate HTML Dom like JavaScript
 * Version: 1.0 beta (2013-2-22)
 * Author: WJN 
 */

class MatchHtml {
	protected $filepath = '';
	protected $pagecode = '';
	protected $tempcode = '';
	protected $tempposi = 0;
	protected $element = array();
	protected $elePosition = array();
	protected $createEle = array();
	protected static $showError = true;
	protected static $singleTag = array('!DOCTYPE', 'area', 'base', 'basefont', 'br', 'embed', 'frame', 'hr', 'img', 'input', 'link', 'meta', 'param');
	protected static $validAttribute = array('align', 'alt', 'border', 'class', 'color', 'height', 'href', 'id', 'name', 'rel', 'src', 'style', 'target', 'type', 'value', 'width', 'data-placeholder');

	function __construct($html = '', $showError = false){
		$this->pagecode = $html;
		self::$showError = $showError;
	}

	function __destruct(){
		$this->clearHtml();
		$this->tempposi = null;
		$this->element = null;
		$this->elePosition = null;
	}

	function __get($property){
		switch($property) {
			case 'length': return $this->length();
			case 'showHtml': return $this->showHtml();
			case 'outerText': return $this->_outerText();
			case 'innerText': return $this->_innerText();
			case 'outerHtml': return $this->_outerHtml();
			case 'innerHtml': return $this->_innerHtml();
			default : return $this->getAttribute($property);
		}
	}

	function __set($property, $value){
		switch($property) {
			case 'innerHtml': 
				return $this->setInnerHtml($value);
			case 'outerHtml': 
				return $this->setOuterHtml($value);
			default : 
				for($i = 0; $i < count(self::$validAttribute); $i++){
					if($property == self::$validAttribute[$i]){
						return $this->setAttribute($property, $value);
					}
				}
				return self::errorInfo('delimit', $property);
		}
	}

	function __call($method, $value){
		$num = (isset($value[0])) ? $value[0] : 0;
		$ele = (isset($value[2])) ? $value[2] : 'get';
		switch($method){
			case 'innerHtml':
				if(isset($value[1])) return $this->setInnerHtml($value[1], $num, $ele);
				else return $this->_innerHtml($num);
			break;
			case 'innerText':
				return $this->_innerText($num);
			break;
			case 'outerHtml':
				if(isset($value[1])) return $this->setOuterHtml($value[1], $num, $ele);
				else return $this->_outerHtml($num);
			break;
			case 'outerText':
				return $this->_outerText($num);
			break;
			default : 
				if(isset($value[1])){
					for($i = 0; $i < count(self::$validAttribute); $i++){
						if($method == self::$validAttribute[$i]){
							return $this->setAttribute($method, $value[1], $num, $ele);
						}
					}
				}
				else if($this->hasAttribute($method, $num)){
					return $this->getAttribute($method, $num);
				}
				return self::errorInfo('delimit', $method);
		}
  }

	protected function htmlInTag($tag, $html, $remain = false){
		/* can't support Chinese
		$html = '<' . $tag . '>' . $html;
		preg_match('/<(' . $tag . ')(| [^<>]*)>(((?R)|.*?)*)<\/\\1>/is', $html, $match);
		if($remain) $this->tempcode = substr($html, strlen($match[0]) - strlen($html));
		return $match[3];
		*/
			
		$tagamt = 0;
		$tagend = 0;

		$tagarr = explode('<' . $tag, $html);
		$tagarrLen = count($tagarr);

		// calculate the end tag match to current tag
		for($t = 0; $t < $tagarrLen; $t++){
			$tagamt += count(explode('</' . $tag, $tagarr[$t])) - 2;
			if($tagamt >= 0){
				$tagend = $t;
				break;
			}
		}
		$aimHtml = '';
		$partHtml = explode('</' . $tag, $html);
		for($p = 0; $p <= $tagend; $p++){
			if($p > 0) $aimHtml .= '</' . $tag;
			$aimHtml .= $partHtml[$p];
		}
		if($remain){
			$this->tempcode = '';
			for($p = $tagend + 1; $p < count($partHtml); $p++){
				$this->tempcode .= '</' . $tag . $partHtml[$p];
			}
		}
		return $aimHtml;
	}

	protected function invalidStr($str, $valid = ''){
		preg_match('/[^a-z\d\-_' . $valid . ']/i', trim($str), $match);
		if($match[0]) $this->errorInfo('invalid', $str, $match[0]);
		return $match;
	}

	protected function checkFrom($from = -1){
		$from = (int)$from;
		if($from >= 0){
			if(!$this->element[$from]) return $this->errorInfo('empty', $from, 'Get');

			preg_match("/^<([a-z\d\:!]+)([ \/]?>| [^>]+>)([\w\W]*$)/is", $this->element[$from], $match);
			$this->tempposi = $this->elePosition[$from] + strlen($match[1] . $match[2]) + 1;
			$this->tempcode = $match[3];
		}
		else {
			$this->tempcode = $this->pagecode;
			$this->tempposi = 0;
		}
		$this->clearGetElement();

		return true;
	}

	protected function checkSingle($tag){
		$allSingle = count(self::$singleTag);
		for($i = 0; $i < $allSingle; $i++){
			if(self::$singleTag[$i] == strtolower($tag)){
				return true;
			}
		}
		return false;
	}

	protected function checkAttrbiute($name){
		$allAttribute = count(self::$validAttribute);
		for($i = 0; $i < $allAttribute; $i++){
			if(self::$validAttribute[$i] == strtolower($name)){
				return true;
			}
		}
		return false;
	}

	protected function errorInfo($error, $char = '', $errval = ''){
		if(!self::$showError) return null;
		$warnStr = '<br /><b>Warning: ';
		switch($error){
			case 'empty':
				$warnStr .= $errval . ' element[' . $char . '] is empty.';
			break;
			case 'invalid':
				$warnStr .= 'String \'' . $char . '\' contain invalid character ' . addslashes($errval) . '.';
			break;
			case 'delimit':
				$warnStr .= 'Property \'' . $char . '\' isn\'t delimited.';
			break;
			case 'existed':
				$warnStr .= '\'' . $char . '\' is existed in ' . $errval . '.';
			break;
			case 'readfile':
				$warnStr .= 'File read failed.';
			break;
			case 'savefile':
				$warnStr .= 'Page code save failed.';
			break;
		}
		echo $warnStr . '</b><br />';
		return null;
	}

	////////////////////// html's operation //////////////////////

	private function _innerHtml($num = 0){
		if($this->element[$num] != null){
			return preg_replace('/^<([a-z\d\:!]+)[^>]*>|<\/[^>]*>$/is', '', $this->element[$num]);
		}
		else return null;
	}

	private function _innerText($num = 0){
		if($this->element[$num] != null){
			$tempHtml = preg_replace('/^<([a-z\d\:!]+)[^>]*>|<\/[^>]*>$/is', '', $this->element[$num]);
			return htmlspecialchars($tempHtml);
		}
		else return null;
	}

	private function _outerHtml($num = 0){
		if($this->element[$num] != null){
			return $this->element[$num];
		}
		else return null;
	}

	private function _outerText($num = 0){
		if($this->element[$num] != null){
			return htmlspecialchars($this->element[$num]);
		}
		else return null;
	}

	public function length($ele = 'get'){
		if($ele == "get") return count($this->element);
		else if($ele == "create") return count($this->createEle);
	}

	////////////////////// page code's operation //////////////////////

	public function readHtml($filepath){
		if(file_exists($filepath)){
			$this->filepath = $filepath;
			$this->pagecode = file_get_contents($filepath);
		}
		else {
			return $this->errorInfo('readfile');
		}
	}

	public function saveHtml(){
		if(file_exists($this->filepath) && file_put_contents($this->filepath, $this->pagecode)){
			return true;
		}
		else {
			return $this->errorInfo('savefile');
		}
	}
	
	public function appendHtml($html){
		$this->pagecode .= $html;
		return $this;
	}

	public function showHtml(){
		return $this->pagecode;
	}

	public function clearHtml(){
		$this->pagecode = '';
		$this->tempcode = '';
		return null;
	}

	public static function clearDistrub($disturb = '', $pagecode){
		$distrubArr = explode(' ', strtolower($disturb));
		for($d = 0; $d < count($distrubArr); $d++){
			switch($distrubArr[$d]){
				case "comments":
					$pagecode = preg_replace("/<!--(.*?)-->/is", '', $pagecode);
				break;
				case "style":
					$pagecode = preg_replace("/<style[^>]*>(.*?)<\/style>/is", '', $pagecode);
				break;
				case "script":
					$pagecode = preg_replace("/<script[^>]*>(.*?)<\/script>/is", '', $pagecode);
				break;
			}
		}
		return $pagecode;
	}

	public static function addAttribute($attribute){
		if(gettype($attribute) == 'array'){
			$selfClass = new MatchHtml();
			array_walk_recursive($attribute, array($selfClass, 'addAttribute'));
		}
		else {
			preg_match('/[\'\"\(\)\s<>]/i', $attribute, $match);
			if($match[0]){
				return self::errorInfo('invalid', $attribute, $match[0]);
			}
			else {
				for($i = 0; $i < count(self::$validAttribute); $i++){
					if(self::$validAttribute[$i] == $attribute){
						self::errorInfo('existed', $attribute, 'attribute array');
						return false;
					}
				}
				array_push(self::$validAttribute, $attribute);
				return true;
			}
		}
	}

	public static function addSingle($tag){
		if(gettype($tag) == 'array'){
			$selfClass = new MatchHtml();
			array_walk_recursive($tag, array($selfClass, 'addSingle'));
		}
		else {
			preg_match('/[\'\"\(\)\s<>]/i', $tag, $match);
			if($match[0]){
				return self::errorInfo('invalid', $tag, $match[0]);
			}
			else if(self::checkSingle($tag)){
				return self::errorInfo('existed', $tag, 'single tag array');
			}
			else {
				array_push(self::$singleTag, $tag);
				return true;
			}
		}
	}

	////////////////////// new element's operation //////////////////////
	
	public function createElement($tag, $num = -1){
		if($this->invalidStr($tag)) return null;

		if((gettype($num) != 'integer') || ($num < 0)){
			$num = count($this->createEle);
		}

		if($this->checkSingle($tag)) $this->createEle[$num] = "<" . $tag . " />";
		else $this->createEle[$num] = "<" . $tag . "></" . $tag . ">";
		return $this;
	}

	public function copyElement($source, $num = -1){
		if((gettype($num) != 'integer') || ($num < 0)){
			$num = count($this->createEle);
		}

		if($this->element[$source]){
			$this->createEle[$num] = $this->element[$source];
			return true;
		}
		else return false;
	}

	public function appendElement($parent = 0, $num = 0){
		preg_match("/^<([a-z\d\:!]+)([ \/]?>| [^>]+>)/is", $this->element[$parent], $match);
		if(!$this->checkSingle($match[1]) && $this->createEle[$num]){
			$tempposi = $this->elePosition[$parent];
			$elelen = strlen($this->element[$parent]);
			$this->element[$parent] = preg_replace("/(<\/[^>]*>$)/is", $this->createEle[$num] . '\\1', $this->element[$parent]);

			$newlen = strlen($this->element[$num]) - $elelen;
			$allele = count($this->element);
			for($i = $parent + 1; $i < $allele; $i++){
				$this->elePosition[$i] += $newlen;
			}

			$this->pagecode = substr($this->pagecode, 0, $tempposi) . $this->element[$parent] . substr($this->pagecode, $tempposi + $elelen, strlen($this->pagecode));
			return true;
		}
		else return false;
	}

	public function followElement($sibling = -1, $num = 0){
		$before = false;
		if($sibling < 0){
			$sibling = 0;
			$before = true;
		}
		if(($this->createEle[$num]) && ($this->element[$sibling])){
			$tempposi = $this->elePosition[$sibling];
			$elelen = strlen($this->element[$sibling]);
			$leftcode = substr($this->pagecode, 0, $tempposi);
			$rightcode = substr($this->pagecode, - $tempposi - $elelen);

			if(!$before) $this->pagecode = $leftcode . $this->element[$sibling] . $this->createEle[$num] . $rightcode;
			else $this->pagecode = $leftcode . $this->createEle[$num] . $this->element[$sibling] . $rightcode;
			return true;
		}
		else return false;
	}

	public function clearGetElement(){
		$this->element = array();
		$this->elePosition = array();
	}

	public function clearCreateElement(){
		$this->createEle = array();
	}

	////////////////////// get element's operation //////////////////////

	public function getElementsByAttribute($attr, $from = -1){
		$found = 0;
		if(isset($attr['tagname'])){
			$this->getElementsByTagName($attr['tagname'], $from);
			unset($attr['tagname']);
			$found++;
		}
		else if(!$this->checkFrom($from)){
			return null;
		}

		foreach($attr as $key => $value){
			preg_match('/[\'\"<>()]/i', $key . $value, $match);
			if($match[0]){
				return $this->errorInfo('invalid', $key . ' => ' . $value, $match[0]);
			}

			if($found){
				$allele = count($this->element);
				for($i = 0; $i < $allele; $i++){
					preg_match("/<([a-z\d\:!]+)([^>]*?{$key}=)([\'\"]?)({$value}\\3)([ \/]?>| [^>]+>)/is", $this->element[$i], $match);
					if(empty($match)){
						unset($this->element[$i]);
						unset($this->elePosition[$i]);
					}
				}
			}
			else {
				$attcnt = preg_match_all("/<([a-z\d\:!]+)([^>]*?{$key}=)([\'\"]?){$value}\\3([ \/]?>| [^>]+>)/is", $this->tempcode, $match);
				$attcnt = count($match[0]);
				
				for($i = 0; ; $i++){
					preg_match("/(^[\w\W]*?)<([a-z\d\:!]+)([^>]*?{$key}=)([\'\"]?){$value}\\4([ \/]?>| [^>]+>)([\w\W]*$)/is", $this->tempcode, $match);
					$tag = $match[2];
					$single = $this->checkSingle($tag);
					$startTag = '<' . $tag . $match[3] . $match[4] . $value . $match[4] . $match[5];
					if($single) $this->element[$i] = $startTag;
					else $this->element[$i] = $startTag . $this->htmlInTag($tag, $match[6]) . '</' . $tag . '>';

					$this->elePosition[$i] += strlen($match[1]);
					if($i < ($attcnt - 1)){
						$this->elePosition[$i + 1] = $this->elePosition[$i] + strlen($startTag);
						$this->tempcode = $match[6];
					}
					else break;
				}
				$found++;
			}
		}

		$temparr = $this->element;
		$this->element = array();
		foreach($temparr as $value){
			array_push($this->element, $value);
		}

		$temparr = $this->elePosition;
		$this->elePosition = array();
		foreach($temparr as $value){
			array_push($this->elePosition, $value);
		}

		return $this;
	}

	public function getElementsByClassName($className, $from = -1){
		if($this->invalidStr($className, ' ') || !$this->checkFrom($from)) return null;

		$attcnt = preg_match_all("/<([a-z\d\:!]+)([^>]*?class=)([\'\"]?)(\s*|[^\'\"]+\s+){$className}(\s*|[^\'\"]+\s+)\\3([ \/]?>| [^>]+>)/is", $this->tempcode, $match);
		$attcnt = count($match[0]);
		
		for($i = 0; ; $i++){
			preg_match("/(^[\w\W]*?)<([a-z\d\:!]+)([^>]*?class=)([\'\"]?)(\s*|[^\'\"]+\s+){$className}(\s*|[^\'\"]+\s+)\\4([ \/]?>| [^>]+>)([\w\W]*$)/is", $this->tempcode, $match);
			$tag = $match[2];
			$single = $this->checkSingle($tag);
			$startTag = '<' . $tag . $match[3] . $match[4] . $match[5] . $className . $match[6] . $match[4] . $match[7];
			if($single) $this->element[$i] = $startTag;
			else $this->element[$i] = $startTag . $this->htmlInTag($tag, $match[8]) . '</' . $tag . '>';

			$this->elePosition[$i] += strlen($match[1]);
			if($i < ($attcnt - 1)){
				$this->elePosition[$i + 1] = $this->elePosition[$i] + strlen($startTag);
				$this->tempcode = $match[8];
			}
			else break;
		}
		return $this;
	}

	public function getElementsByTagName($tag, $from = -1, $num = -1){
		if($this->invalidStr($tag) || !$this->checkFrom($from)) return null;
		$single = $this->checkSingle($tag);
		$num = (int)$num;
		
		if($num >= 0){
			$this->tempcode = preg_replace("/(<{$tag})([ \/]?>| [^>]+>)/ie", "str_pad('', strlen(stripcslashes('\\1\\2')), 0)", $this->tempcode, $num);

			preg_match("/(^[\w\W]*?)<{$tag}([ \/]?>| [^>]+>)([\w\W]*$)/is", $this->tempcode, $match);
			if($match[2]){
				if($single) $this->element[0] = '<' . $tag . $match[2];
				else if($match[3]) $this->element[0] = '<' . $tag . $match[2] . $this->htmlInTag($tag, $match[3]) . '</' . $tag . '>';
				$this->elePosition[0] += strlen($match[1]);
			}
		}
		else {
			$codelen = strlen($this->tempcode);
			preg_match_all("/<{$tag}([ \/]?>| [^>]+>)/is", $this->tempcode, $match);
			
			for($i = 0; $i < count($match[0]); $i++){
				$this->elePosition[$i] = strpos($this->tempcode, $match[0][$i], (!$i ? 0 : $this->elePosition[$i - 1] + 1));

				if($single) $this->element[$i] = $match[0][$i];
				else $this->element[$i] = $match[0][$i] . $this->htmlInTag($tag, substr($this->tempcode, $this->elePosition[$i] + strlen($match[0][$i]), $codelen)) . '</' . $tag . '>';
			}
		}

		return $this;
	}

	public function getElementById($id, $from = -1){
		if($this->invalidStr($id) || !$this->checkFrom($from)) return null;

		preg_match("/(^[\w\W]*?)<([a-z\d\:!]+)(| [^>]*?)( id=)([\'\"]?)({$id}\\5)(\/?| [^>]*)>([\w\W]*$)/is", $this->tempcode, $match);
		if($match[2]){
			$tag = $match[2];
			$single = $this->checkSingle($tag);
			$startTag = '<' . $tag . $match[3] . $match[4] . $match[5] . $match[6] . $match[7] . '>';
			$this->elePosition[0] = strlen($match[1]) + $this->tempposi;

			if($single) $this->element[0] = $startTag;
			else $this->element[0] = $startTag . $this->htmlInTag($tag, $match[8]) . '</' . $tag . '>';
		}
		return $this;
	}

	public function getParentByElement($num = 0){
		if($this->elePosition[$num]){
			$leftcode = substr($this->pagecode, 0, $this->elePosition[$num]);
			$rightcode = substr($this->pagecode, $this->elePosition[$num], strlen($this->pagecode));

			$this->clearGetElement();
			
			$utmost = false;
			for($t = 0, $tagend = 1; ; ){
				preg_match("/(^[\w\W]*)<([a-z\d\:!]+)([ \/]?>| [^>]+>)([\w\W]*$)/is", $leftcode, $match);
				if(empty($match)){
					$utmost = true;
					break;
				}

				preg_match_all("/<\/[a-z\d\:!]+>/is", $match[4], $matchend);
				$tagend += count($matchend[0]);
				$rightcode = $match[4] . $rightcode;
				
				if($t < $tagend - 1){
					$t++;
					$rightcode = '<' . $match[2] . $match[3] . $rightcode;
					$leftcode = preg_replace("/(^[\w\W]*)<([a-z\d\:!]+)([ \/]?>| [^>]+>)([\w\W]*$)/is", "\\1", $leftcode);
				}
				else break;
			}
			
			if(!$utmost){
				$this->element[0] = '<' . $match[2] . $match[3] . $this->htmlInTag($match[2], $rightcode) . '</' . $match[2] . '>';
				$this->elePosition[0] = strlen($match[1]);
			}

			return $this;
		}
		return null;
	}

	public function getSiblingByElement($num = 0, $sibling = 1){
		if($this->element[$num]){
			$tempcode = $this->element[$num];
			$tempposi = $this->elePosition[$num];
			$elelen = strlen($this->element[$num]);
			$leftcode = substr($this->pagecode, 0, $tempposi);
			$rightcode = substr($this->pagecode, $tempposi + $elelen, strlen($this->pagecode));

			if($sibling != 0) $this->clearGetElement();
			else return null;

			if($sibling > 0){
				for($i = 1; ; ){
					preg_match("/(^[\w\W]*?)<([a-z\d\:!]+)([ \/]?>| [^>]+>)([\w\W]*$)/is", $rightcode, $match);

					if($match[2]){
						$tag = $match[2];
						$single = $this->checkSingle($tag);
						$startTag = '<' . $tag . $match[3];

						if($i == $sibling){
							if($single) $this->element[0] = $startTag;
							else {
								$innerHtml =  $this->htmlInTag($tag, $match[4]);
								$this->element[0] = $startTag . $innerHtml . '</' . $tag . '>';
							}
							$this->elePosition[0] = strlen($match[1]) + $tempposi + $elelen;
							break;
						}
						else {
							$i++;
							$tempposi += strlen($startTag);
							if(!$single){
								$tempposi += strlen($innerHtml . '</' . $tag . '>');
								$this->htmlInTag($tag, $match[4], true);
								$rightcode = $this->tempcode;
							}
							else $rightcode = $match[4];
						}
					}
					else return null;
				}
			}
			else if($sibling < 0){
				$tempcode = '';
				for($i = -1; $i > $sibling - 1; $i--){
					for($t = 1, $tagend = 0; ; $t++){
						preg_match("/(^[\w\W]*)<([a-z\d\:!]+)([ \/]?>| [^>]+>)([\w\W]*$)/is", $leftcode, $match);
						if($match[2]){
							if($this->checkSingle($match[2])) $tagend += 1;
							
							// count all end tag in match[4]
							preg_match_all("/<\/[a-z\d\:!]+>/is", $match[4], $matchend);
							$tagend += count($matchend[0]);

							// collect match code
							$tempcode = '<' . $match[2] . $match[3] . $match[4] . $tempcode;
						}
						else return null;
						
						// cancel the previous match code
						$leftcode = preg_replace("/(^[\w\W]*)<([a-z\d\:!]+)([ \/]?>| [^>]+>)([\w\W]*$)/is", "\\1", $leftcode);

						// if start tags are equal to end tags, these codes have a complete HTML markup
						if($t == $tagend) break;
						// if start tags are more than end tags, the code pointer locate on the parent node
						else if($t > $tagend) return null;
					}

					if($i == $sibling){
						preg_match("/^<([a-z\d\:!]+)([ \/]?>| [^>]+>)([\w\W]*$)/is", $tempcode, $match);
						$single = $this->checkSingle($match[1]);
						$startTag = '<' . $match[1] . $match[2];

						if($single) $this->element[0] = $startTag;
						else $this->element[0] = $startTag . $this->htmlInTag($match[1], $match[3]) . '</' . $match[1] . '>';
						$this->elePosition[0] = strlen($leftcode);
						break;
					}
				}
			}
			return $this;
		}
		return null;
	}

	public function getChildrenByElement($num = 0){
		if($this->element[$num]){
			$this->checkFrom($num);
			$this->elePosition[0] = $this->tempposi;

			for($i = 0; ; $i++){
				preg_match("/(^[\w\W]*?)<([a-z\d\:!]+)([ \/]?>| [^>]+>)([\w\W]*$)/is", $this->tempcode, $match);
				if($match[3]){
					$tag = $match[2];
					$single = $this->checkSingle($tag);
					$startTag = '<' . $tag . $match[3];

					if($single){
						$this->element[$i] = $startTag;
						$this->tempcode = $match[4];
					}
					else $this->element[$i] = $startTag . $this->htmlInTag($tag, $match[4], true) . '</' . $tag . '>';

					$this->elePosition[$i] += strlen($match[1]);
				}

				preg_match("/<([a-z\d\:!]+)([ \/]?>| [^>]+>)/is", $this->tempcode, $match);
				if($match[1]){
					$this->elePosition[$i + 1] = strlen($this->element[$i]) - (3 + strlen($tag)) + $this->elePosition[$i];
				}
				else break;
			}
			return $this;
		}
		return null;
	}

	public function getTextElement($from = -1){
		if(!$this->checkFrom($from)) return null;
		$this->elePosition[0] = $this->tempposi;

		for($i = 0; ; $i++){
			while(1){
				preg_match("/^(\s*<)(\/?)([a-z\d\:!]+)([ \/]?>| [^>]+>)([\w\W]*$)/is", $this->tempcode, $match);
				if($match[3] == null) break;
				//else if(!$match[2] && ((strtolower($match[3]) == "script") || (strtolower($match[3]) == "style"))){
				else if(!$match[2] && strtolower($match[3]) == "style"){
					$this->elePosition[$i] += strlen($match[1] . $match[3] . $match[4] . $this->htmlInTag($match[3], $match[5], true));
				}
				else {
					$this->elePosition[$i] += strlen($match[1] . $match[2] . $match[3] . $match[4]);
					$this->tempcode = $match[5];
				}
			}

			preg_match("/([\w\W]+?)(<\/?[a-z\d\:!]+)([\w\W]*$)/is", $this->tempcode, $match);
			if($match[1] != ''){
				$this->element[$i] = $match[1];
			}
			else {
				if($this->tempcode != ''){
					// record the last text out of the html tag
					$this->element[$i] = $this->tempcode;
				}
				else {
					unset($this->elePosition[$i]);
				}
				break;
			}
			
			// prepare for next element
			$this->elePosition[$i + 1] = $this->elePosition[$i] + strlen($this->element[$i]);
			$this->tempcode = $match[2] . $match[3];
		}
		return $this;
	}

	////////////////////// attribute's operation //////////////////////

	public function hasAttribute($name, $num = 0, $ele = "get"){
		$name = preg_replace('/\-/', '\-', $name);
		
		$pattern = "/^<[a-z\d\:!]+[^>]*?({$name})=([\'\"]?)([^>]*?)\\2([ \/]?>| [^>]+>)/is";
		if($ele == "get") preg_match($pattern, $this->element[$num], $match);
		else if($ele == "create") preg_match($pattern, $this->createEle[$num], $match);

		if($match[1]) return true;
		else return false;
	}

	public function getAttribute($name, $num = 0, $ele = "get"){
		$match = array();
		if($name == "tagname"){
			$pattern = "/^<([a-z\d\:!]+)([ \/]?>| [^>]+>)/is";
			if($ele == "get") preg_match($pattern, $this->element[$num], $match);
			else if($ele == "create") preg_match($pattern, $this->createEle[$num], $match);
			return $match[1];
		}
		else {
			$name = preg_replace('/\-/', '\-', $name);

			$pattern = "/^<[a-z\d\:!]+[^>]*?{$name}=([\'\"]?)([^>]*?)\\1([ \/]?>| [^>]+>)/is";
			if($ele == "get") preg_match($pattern, $this->element[$num], $match);
			else if($ele == "create") preg_match($pattern, $this->createEle[$num], $match);
			return $match[2];
		}
	}

	public function setAttribute($name, $value, $num = 0, $ele = "get"){
		if(!$this->checkAttrbiute($name) || $this->invalidStr($value, ' #$%:=')) return null;

		$name = preg_replace('/\-/', '\-', $name);
		if($ele == "get"){
			if($this->element[$num]){
				$tempposi = $this->elePosition[$num];
				$elelen = strlen($this->element[$num]);

				if($this->hasAttribute($name, $num, $ele)){
					$this->element[$num] = preg_replace("/(^<[a-z\d\:!]+[^>]*?{$name}=)([\'\"]?)([^>]*?)\\2([ \/]?>| [^>]+>)/is", '\\1"' . $value . '"\\4', $this->element[$num]);
				}
				else $this->element[$num] = preg_replace("/(^<[a-z\d\:!]+)([ \/]?>| [^>]+>)/is", "\\1 $name=\"$value\"\\2", $this->element[$num]);

				$newlen = strlen($this->element[$num]) - $elelen;
				$allele = count($this->element);
				for($i = $num + 1; $i < $allele; $i++){
					$this->elePosition[$i] += $newlen;
				}

				$this->pagecode = substr($this->pagecode, 0, $tempposi) . $this->element[$num] . substr($this->pagecode, $tempposi + $elelen, strlen($this->pagecode));
				return true;
			}
			else {
				return $this->errorInfo('empty', $num, 'Get');
			}
		}
		else if($ele == "create"){
			if($this->createEle[$num]){
				if($this->hasAttribute($name, $num, $ele)){
					$this->createEle[$num] = preg_replace("/(^<[a-z\d\:!]+[^>]*?{$name}=)([\'\"]?)([^>]*?)\\2([ \/]?>| [^>]+>)/is", '\\1"' . $value . '"\\4', $this->createEle[$num]);
				}
				else $this->createEle[$num] = preg_replace("/(^<[a-z\d\:!]+)([ \/]?>| [^>]+>)/is", "\\1 $name='$value'\\2", $this->createEle[$num]);
				return true;
			}
			else {
				return $this->errorInfo('empty', $num, 'Create');
			}
		}
		return false;
	}

	public function removeAttribute($name, $num = 0, $ele = "get"){
		$name = preg_replace('/\-/', '\-', $name);

		if($ele == "get"){
			if($this->element[$num]){
				if($this->hasAttribute($name, $num, $ele)){
					$tempposi = $this->elePosition[$num];
					$elelen = strlen($this->element[$num]);

					$this->element[$num] = preg_replace("/(^<[a-z\d\:!]+[^>]*?){$name}=([\'\"]?)([^>]*?)\\2([ \/]?>| [^>]+>)/is", '\\1\\4', $this->element[$num]);
					$this->element[$num] = preg_replace("/(^<[a-z\d\:!]+[^>]*?) >/is", '\\1>', $this->element[$num]);
					$newlen = strlen($this->element[$num]) - $elelen;
					$allele = count($this->element);
					for($i = $num + 1; $i < $allele; $i++){
						$this->elePosition[$i] += $newlen;
					}

					$this->pagecode = substr($this->pagecode, 0, $tempposi) . $this->element[$num] . substr($this->pagecode, $tempposi + $elelen, strlen($this->pagecode));
					return true;
				}
			}
			else {
				return $this->errorInfo('empty', $num, 'Get');
			}
		}
		else if($ele == "create"){
			if($this->createEle[$num]){
				if($this->hasAttribute($name, $num, $ele)){
					$this->createEle[$num] = preg_replace("/(^<[a-z\d\:!]+[^>]*?){$name}=([\'\"]?)([^>]*?)\\2([ \/]?>| [^>]+>)/is", '\\1\\4', $this->createEle[$num]);
					return true;
				}
			}
			else {
				return $this->errorInfo('empty', $num, 'Create');
			}
		}
		return false;
	}

	public function setInnerHtml($value, $num = 0, $ele = "get"){
		if($ele == "get"){
			if($this->element[$num]){
				preg_match("/^<([a-z\d\:!]+)([ \/]?>| [^>]+>)/is", $this->element[$num], $match);
				
				if($this->checkSingle($match[1])) return false;
				else {
					$elelen = strlen($this->element[$num]);

					if($match[1]) $this->element[$num] = "<" . $match[1] . $match[2] . $value . "</" . $match[1] . ">";
					else $this->element[$num] = $value;

					$newlen = strlen($this->element[$num]) - $elelen;
					$allele = count($this->element);
					for($i = $num + 1; $i < $allele; $i++){
						$this->elePosition[$i] += $newlen;
					}

					$tempposi = $this->elePosition[$num];
					$this->pagecode = substr($this->pagecode, 0, $tempposi) . $this->element[$num] . substr($this->pagecode, $tempposi + $elelen, strlen($this->pagecode));
					return $this;
				}
			}
			else {
				return $this->errorInfo('empty', $num, 'Get');
			}
		}
		else if($ele == "create"){
			if($this->createEle[$num]){
				preg_match("/^<([a-z\d\:!]+)([ \/]?>| [^>]+>)/is", $this->createEle[$num], $match);
				
				if($this->checkSingle($match[1])) return false;
				else {
					$this->createEle[$num] = "<" . $match[1] . $match[2] . $value . "</" . $match[1] . ">";
					return $this;
				}
			}
			else {
				return $this->errorInfo('empty', $num, 'Create');
			}
		}
		return false;
	}

	public function setOuterHtml($value, $num = 0, $ele = "get"){
		if($ele == "get"){
			if($this->element[$num]){
				preg_match("/^<([a-z\d\:!]+)([ \/]?>| [^>]+>)/is", $this->element[$num], $match);
				
				if($this->checkSingle($match[1])) return false;
				else {
					$tempposi = $this->elePosition[$num];
					$elelen = strlen($this->element[$num]);
					$this->element[$num] = $value;

					$newlen = strlen($this->element[$num]) - $elelen;
					$allele = count($this->element);
					for($i = $num + 1; $i < $allele; $i++){
						$this->elePosition[$i] += $newlen;
					}

					$this->pagecode = substr($this->pagecode, 0, $tempposi) . $this->element[$num] . substr($this->pagecode, $tempposi + $elelen, strlen($this->pagecode));
					return true;
				}
			}
			else {
				return $this->errorInfo('empty', $num, 'Get');
			}
		}
		else if($ele == "create"){
			if($this->createEle[$num]){
				preg_match("/^<([a-z\d\:!]+)([ \/]?>| [^>]+>)/is", $this->createEle[$num], $match);
				
				if($this->checkSingle($match[1])) return false;
				else {
					$this->createEle[$num] = $value;
					return true;
				}
			}
			else {
				return $this->errorInfo('empty', $num, 'Create');
			}
		}
		return false;
	}

	public function removeElement($num = 0, $ele = "get"){
		if($ele == "get"){
			if($this->element[$num]){
				$tempposi = $this->elePosition[$num];
				$this->pagecode = substr($this->pagecode, 0, $tempposi) . substr($this->pagecode, $tempposi + strlen($this->element[$num]), strlen($this->pagecode));
				$this->element[$num] = null;
				$this->elePosition[$num] = null;
				return true;
			}
			else {
				return $this->errorInfo('empty', $num, 'Get');
			}
		}
		else if($ele == "create"){
			if($this->createEle[$num]){
				$this->createEle[$num] = null;
			}
			else {
				return $this->errorInfo('empty', $num, 'Create');
			}
		}
		return false;
	}

	////////////////////// debug's operation //////////////////////

	public function showPosition($num = 0){
		return $this->elePosition[$num];
	}
}

?>
