<?php

class Helper {

	static public function checkLogin() {
		return isset($_SESSION['isAutorize']) ? $_SESSION['isAutorize'] : false;
	}

	static function autorize(array $params) {
		if ($params['user'] != 'admin' ) return false;
		if ($params['psw']  != 'admin' ) return false;
		
		$_SESSION['isAutorize'] = true;
		return true;
	}
}