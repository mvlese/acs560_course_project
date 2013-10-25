<?php

class Security {
	
	static function encryptPassword($password) {
		$salt = Security::_generateMD5Salt();
		$hashedPassword = crypt($password, $salt);
		return $hashedPassword;
	}
	
	static function isValid($password, $hashedPassword) {
		$rslt = false;
		$temp = crypt($password, $hashedPassword); 
		if ($temp == $hashedPassword) {
			$rslt = true;
		}
		return $rslt;
	}
	
	private static function _generateMD5Salt() {
		$length = 8;
		$str = '$1$';
	    $str .= substr(str_shuffle(implode(array_merge(range(0,9), range('A', 'Z'), range('a', 'z')))), 0, $length);
	    $str .= '$';
	    return $str;
	}
}	
?>