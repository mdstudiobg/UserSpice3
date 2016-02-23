<?php
/*
UserSpice 3
by Dan Hoover at http://UserSpice.com

a modern version of
UserCake Version: 2.0.2
UserCake created by: Adam Davis
UserCake V2.0 designed by: Jonathan Cassels
*/
class Session {

	public static function exists($name){
		return (isset($_SESSION[$name])) ? true : false;
	}

	public static function put($name, $value){
		return $_SESSION[$name] = $value;
	}

	public static function delete($name){
		if (self::exists($name)) {
			unset($_SESSION[$name]);
		}
	}

	public static function get($name){
		return $_SESSION[$name];
	}

	public static function flash($name, $string = ''){
		if (self::exists($name)) {
			$session =  self::get($name);
			self::delete($name);
			return $session;
		} else{
			self::put($name, $string);
		}
	}

	public static function uagent_no_version(){
		$uagent = $_SERVER['HTTP_USER_AGENT'];
		$regx = '/\/[a-zA-Z0-9.]+/';
		$newString = preg_replace($regx,'',$uagent);
		return $newString;
	}

}
