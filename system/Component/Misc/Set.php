<?php

/**
 * BabiPHP : The Simple and Fast Development Framework (http://babiphp.org)
 * Copyright (c) BabiPHP. (http://babiphp.org)
 *
 * Licensed under The GNU General Public License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) BabiPHP. (http://babiphp.org)
 * @link          http://babiphp.org BabiPHP Project
 * @package       system.component.utility
 * @since         BabiPHP v 0.3
 * @license       http://www.gnu.org/licenses/ GNU License
 */



/**
 * BabiPHP Set Class.
 * 
 * Not edit this file
 *
 */


namespace BabiPHP\Component\Misc;


use \BabiPHP\Component\Error\BpException;

use \BabiPHP\Component\Config\Config;

use \BabiPHP\Component\Config\Security;


class Set
{
	
	protected $data = array();
	
	
	public function __construct()
	{
		
		
	}
	
	
	
	/**
        * ArrayToClass
        * Deprecated
        * @param array of value
        */
	
	public function ArrayToClass($array)
	{
		
		if(is_array($array) && !empty($array))
		{
			
			$d = new \stdClass();
			
			
			foreach ($array as $k => $v)
			{
				
				if(!empty($v) && is_array($v))
				{
					
					$v = $this->ArrayToClass($v);
					
				}
				
				
				$d->$k = $v;
				
			}
			
			
			return $d;
			
		}
		
	}
	
	
	
	/**
        * ArrayToObject
        * @param $array
        * @return Object
        */
	
	public function ArrayToObject($array)
	{
		
		if(is_array($array) && !empty($array))
		{
			
			$d = new \stdClass();
			
			
			foreach ($array as $k => $v)
			{
				
				if(!empty($v) && is_array($v))
				{
					
					$v = $this->ArrayToObject($v);
					
				}
				
				
				$d->$k = $v;
				
			}
			
			
			return $d;
			
		}
		
	}
	
	
	
	/**
        * ObjectToArray
        * @param $object
        * @return Array
        */
	
	public function ObjectToArray($object)
	{
		
		if(is_object($object))
		{
			
			return get_object_vars($object);
			
		}
		
	}
	
	
	
	/**
        * ParseDateTime
        * @param the date and the time to parse to datetime
        * @return The datetime generated
        */
	
	public function ParseDateTime($d, $t)
	{
		
		sscanf($d, "%2s-%2s-%4s", $j, $m, $a);
		
		
		if(date('H') <= 9) sscanf('0'.$t, "%2s:%2s:%2s", $h, $mi, $s);
		
		else sscanf($t, "%2s:%2s:%2s", $h, $mi, $s);
		
		
		return $a.'-'.$m.'-'.$j.' '.$h.':'.$mi.':'.$s;
		
	}
	
	
	function Get_1L($s) {
		
		if (strlen($s) > 1)
		{
			
			$s = strip_tags($s);
			
			$s = str_replace("\n"," ",$s);
			
			$s = str_replace("\r"," ",$s);
			
			$s = str_replace(" "," ",$s);
			
			$s = str_replace("  "," ",$s);
			
			$s = substr($s, 0, 1);
			
		}
		
		return $s;
		
	}
	
	
	
	/**
        * ParseDate
        * @param the datetime to parse to date
        * @return The date generated
        */
	
	public function ParseDate($d){
		
		sscanf($d, "%4s-%2s-%2s %2s:%2s:%2s", $a, $m, $j, $h, $mi, $s);
		
		return $j.'-'.$m.'-'.$a;
		
	}
	
	
	
	/**
        * ParseTime
        * @param the datetime to parse to time
        * @return The time generated
        */
	
	public function ParseTime($t){
		
		sscanf($t, "%4s-%2s-%2s %2s:%2s:%2s", $a, $m, $j, $h, $mi, $s);
		
		return $h.':'.$mi.':'.$s;
		
	}
	
	
	
	/**
        * Hash encode a string
        * @param   string
        * @return  string
        */
	
	public function Hash($data)
	{
		
		$hash_key = Security::Get('Security_salt');
		
		$algo = Security::Get('Password_encoder');
		
		return hash_hmac($algo, $data, $hash_key);
		
	}
	
	
	
	/**
        * Cripth
        * @param $pass
        * @return string
        */
	
	public function Cripth($pass, $salt = false)
	{
		
		if ($salt === false)
		{
			
			$salt = $this->_salt(22);
			
		}
		
		return crypt($pass, $salt);
		
	}
	
	
	
	/**
        * Encrypt
        * @param   $pure_string
        * @param   $encryption_key
        * @return  string encrypted
        */
	
	public function Encrypt($pure_string, $encryption_key)
	{
		
		$iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
		
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		
		$encrypted_string = mcrypt_encrypt(MCRYPT_BLOWFISH, $encryption_key, utf8_encode($pure_string), MCRYPT_MODE_ECB, $iv);
		
		return $encrypted_string;
		
	}
	
	
	
	/**
        * Decrypt
        * @param   $encrypted_string
        * @param   $encryption_key
        * @return  string decrypted
        */
	
	public function Decrypt($encrypted_string, $encryption_key)
	{
		
		$iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
		
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		
		$decrypted_string = mcrypt_decrypt(MCRYPT_BLOWFISH, $encryption_key, $encrypted_string, MCRYPT_MODE_ECB, $iv);
		
		return $decrypted_string;
		
	}
	
	
	
	/**
         * _salt 
         * @param  integer $length [description]
         * @return string          [description]
         */
	
	protected function _salt($length = 22)
	{
		
		$salt = str_replace(
		array('+', '='),
		'.',
		base64_encode(sha1(uniqid(Config::Get('Security_salt'), true), true))
		);
		
		return substr($salt, 0, $length);
		
	}
	
	
	
	/**
        * Finder
        * @param $file at find
        */
	
	public function Finder($file, $type, $data=null)
	{
		
		if(!in_array(strtolower($type), array('helper', 'lib')))
		{
			
			throw new BpException('"'.$type.'" n\'est pas un type pris en charge.', 6);
			
		}
		
		
		if($type == 'helper')
		{
			
			$helper = ucfirst(strtolower($file));
			
			$file = $helper.EXT;
			
			$path1 = APPPATH.'Helpers'.DS;
			
			$path2 = COMPONENT.'Helper'.DS;
			
			
			if(file_exists($path1.$file))
			require $path1.$file;
			
			elseif(file_exists($path2.$file))
			require $path2.$file;
			
			else {
				
				throw new BpException('Helper '.$file.' file not found.', 4);
				
			}
			
			
			$helperClass = '\\BabiPHP\Component\Helper\\'.$helper;
			
			return new $helperClass($data);
			
		}
		
		if($type == 'lib')
		{
			
			$lib = ucfirst(strtolower($file));
			
			$file = $lib.'.class'.EXT;
			
			$path1 = APPPATH.'libs'.DS;
			
			$path2 = BASEPATH.'libs'.DS;
			
			
			if(file_exists($path1.$file)) return require $path1.$file;
			
			elseif(file_exists($path2.$file)) return require $path2.$file;
			
			else {
				
				throw new BpException('Library '.$file.' file not found.', 4);
				
			}
			
			
			$libClass = 'BabiPHP\Library\\'.$lib;
			
			return new $libClass();
			
		}
		
	}
	
	
	
	/**
         * [normalizeKey description]
         * @param $key 
         * @return $key 
         */
	
	protected function normalizeKey($key)
	{
		
		return $key;
		
	}
	
	
	
	/**
         * Set data key to value
         * @param string $key   The data key
         * @param mixed  $value The data value
         */
	
	public function set($key, $value)
	{
		
		$this->data[$this->normalizeKey($key)] = $value;
		
	}
	
	
	
	/**
         * Get data value with key
         * @param  string $key     The data key
         * @param  mixed  $default The value to return if data key does not exist
         * @return mixed           The data value, or the default value
         */
	
	public function get($key, $default = null)
	{
		
		if ($this->has($key)) {
			
			$isInvokable = is_object($this->data[$this->normalizeKey($key)]) && method_exists($this->data[$this->normalizeKey($key)], '__invoke');
			
			
			return $isInvokable ? $this->data[$this->normalizeKey($key)]($this) : $this->data[$this->normalizeKey($key)];
			
		}
		
		
		return $default;
		
	}
	
	
	
	/**
         * Add data to set
         * @param array $items Key-value array of data to append to this set
         */
	
	public function replace($items)
	{
		
		foreach ($items as $key => $value) {
			
			$this->set($key, $value);
			// 			Ensure keys are normalized
		}
		
	}
	
	
	public function all()
	{
		
		return $this->data;
		
	}
	
	
	
	/**
         * Fetch set data keys
         * @return array This set's key-value data array keys
         */
	
	public function keys()
	{
		
		return array_keys($this->data);
		
	}
	
	
	
	/**
         * Does this set contain a key?
         * @param  string  $key The data key
         * @return boolean
         */
	
	public function has($key)
	{
		
		return array_key_exists($this->normalizeKey($key), $this->data);
		
	}
	
	
	
	/**
         * Remove value with key from this set
         * @param  string $key The data key
         */
	
	public function remove($key)
	{
		
		unset($this->data[$this->normalizeKey($key)]);
		
	}
	
	
	
	/**
         * Property Overloading
         */
	
	
	public function __get($key)
	{
		
		return $this->get($key);
		
	}
	
	
	public function __set($key, $value)
	{
		
		$this->set($key, $value);
		
	}
	
	
	public function __isset($key)
	{
		
		return $this->has($key);
		
	}
	
	
	public function __unset($key)
	{
		
		return $this->remove($key);
		
	}
	
	
	
	/**
         * Clear all values
         */
	
	public function clear()
	{
		
		$this->data = array();
		
	}
	
	
	
	/**
         * Array Access
         */
	
	
	public function offsetExists($offset)
	{
		
		return $this->has($offset);
		
	}
	
	
	public function offsetGet($offset)
	{
		
		return $this->get($offset);
		
	}
	
	
	public function offsetSet($offset, $value)
	{
		
		$this->set($offset, $value);
		
	}
	
	
	public function offsetUnset($offset)
	{
		
		$this->remove($offset);
		
	}
	
	
	
	/**
         * Countable
         */
	
	public function count()
	{
		
		return count($this->data);
		
	}
	
	
	
	/**
         * Ensure a value or object will remain globally unique
         * @param  string  $key   The value or object name
         * @param  Closure        The closure that defines the object
         * @return mixed
         */
	
	public function Singleton($key, $value)
	{
		
		$this->set($key, function ($c) use ($value) {
			
			static $object;
			
			
			if (null === $object) {
				
				$object = $value($c);
				
			}
			
			
			return $object;
			
		}
		);
		
	}
	
	
	
	/**
         * getArrayKeys
         * @param array $array
         */
	
	public function getArrayKeys($array)
	{
		
		foreach ($array as $k => $v)
		{
			
			if(is_array($v))
			$k = array_keys($v);
			
			
			$d[] = $k;
			
		}
		
		
		return $d;
		
	}
	
}


?>