<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Container{
	private static $data = [];

	public static function getBy($key){
		if(array_key_exists($key, static::$data)){
			return static::$data[$key];
		}
		return [];
	}
	public static function getSubItem($key,$subkey,$grandkey){
		$item = static::getBy($key);
		if(array_key_exists($subkey, $item)){
			$tmp = $item[$subkey];
			return array_key_exists($grandkey, $tmp)?$tmp[$grandkey]:"";
		}
		return [];
	}
	public static function setData($key,$callback){
		if(!array_key_exists($key, static::$data)){
			static::$data[$key] = $callback();
		}
	}
	public static function groupBy($array, $key) {
	    $return = array();
	    foreach($array as $val) {
	        $return[$val[$key]][] = $val;
	    }
	    return $return;
	}
}