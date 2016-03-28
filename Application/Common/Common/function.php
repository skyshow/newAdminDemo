<?php

/**
 * 实现字符串编码格式实现转换
 * @param string $fContents
 * @param string $from
 * @param string $to
 * @return string
 */
function auto_charset($fContents,$from='',$to=''){
	if(empty($from)) $from = C('TEMPLATE_CHARSET');
	if(empty($to)) $to = C('OUTPUT_CHARSET');
		$from = strtoupper($from)=='UTF8'? 'utf-8':$from;
		$to = strtoupper($to)=='UTF8'? 'utf-8':$to;
		//如果编码相同或者非字符串标量则不转换
		if( strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents)) ){
			return $fContents;
		}
		if(is_string($fContents) ) {
			if(function_exists('mb_convert_encoding')){
				return mb_convert_encoding ($fContents, $to, $from);
			}elseif(function_exists('iconv')){
				return iconv($from,$to,$fContents);
			}else{
				//halt(L('_NO_AUTO_CHARSET_'));
				return $fContents;
			}
		}
		elseif(is_array($fContents)){
			foreach ( $fContents as $key => $val ) {
				$_key = auto_charset($key,$from,$to);
				$fContents[$_key] = auto_charset($val,$from,$to);
				if($key != $_key ) {
					unset($fContents[$key]);
				}
			}
			return $fContents;
		}
		elseif(is_object($fContents)) {
			$vars = get_object_vars($fContents);
			foreach($vars as $key=>$val) {
				$fContents->$key = auto_charset($val,$from,$to);
			}
			return $fContents;
		}
		else{
		//halt('系统不支持对'.gettype($fContents).'类型的编码转换！');
		return $fContents;
	}
}

/**
 * 把字符转换由utf8转换成gb2312编码
 * @param string $content
 */
function string_utf8_to_gb2312($content){
	return auto_charset($content,'UTF-8','gb2312');
}

/**
 * 把字符转换由utf8转换成gb2312编码
 * @param string $content
 */
function string_gb2312_to_utf8($content){
	return auto_charset($content,'gb2312','UTF-8');
}

/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 */
function check_verify($code, $id = 1){
	$verify = new \Think\Verify();
	return $verify->check($code, $id);
}


function UC($url='',$vars='',$suffix=true,$domain=false) {
	return '/index.php'.U($url,$vars,$suffix,$domain);
}

function utf8_strcut($str, $start, $length=null) {
	preg_match_all('/./us', $str, $match);
	$chars = is_null($length)? array_slice($match[0], $start ) : array_slice($match[0], $start, $length);

	unset($str);

	return implode('', $chars);
}