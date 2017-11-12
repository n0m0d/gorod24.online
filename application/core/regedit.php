<?php

class Registry{
	static $vars = array();
   
	static function isRegistered($vname) {
        if (isset(Registry::$vars[$vname])) {
            return true;
        }
        return false;
    }
	
	static function set($key, $var) {
		Registry::$vars[$key] = $var;
		return true;
	}

	static function get($key) {
		if (isset(Registry::$vars[$key]) == false) {
			return null;
		}
		return Registry::$vars[$key];
	}

	static function remove($var) {
		unset(Registry::$vars[$key]);
	}
		
	static function offsetExists($offset) {
        return isset(Registry::$vars[$offset]);
	}

	static function offsetGet($offset) {
		return Registry::get($offset);
	}

	static function offsetSet($offset, $value) {
		Registry::set($offset, $value);
	}

	static function offsetUnset($offset) {
		unset(Registry::$vars[$offset]);
	}
}
