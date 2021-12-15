<?php
trait Macroable{
	protected static $macros = [];

	/**
	 * Register a custom macro.
	 *
	 * @param  string $name
	 * @param  object|callable  $macro
	 */
	public static function macro($name, $macro)
	{
	    static::$macros[$name] = $macro;
	}
	public static function hasMacro($name){
		return array_key_exists($name, static::$macros);
	}
}