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
 * @package       system.component.security
 * @since         BabiPHP v 0.7.9
 * @license       http://www.gnu.org/licenses/ GNU License
 */

/**
 * BabiPHP Authorization Class.
 * 
 * Not edit this file
 *
 */

	namespace BabiPHP\Component\Security;
	
	class Authorize
	{
		private $_roles = array();
		private $_access = array();
		const ISNULL = 'anonym';
		
		public function __construct()
		{
			
		}

		public function addRole($role, $extends = null)
		{
			if($role == self::ISNULL)
			{
				return false;
			}
			if(!is_null($extends))
			{
				if(!is_array($extends))
				{
					$extends = array($extends);
				}

				foreach($extends as $extend)
				{
					if(!array_key_exists($extend, $this->_roles))
					{
						return false;
					}
				}
			}
			if(!array_key_exists($role, $this->_roles))
			{
				$this->_roles[$role] = $extends;
				return true;
			}
			return false;
		}
			
		public function addAccess($resource, $role = null, $action)
		{
			$array = isset($this->_access[$role]) ? $this->_access[$role] : null;

			if($role === null) $role = self::ISNULL;
			if(!is_array($action)) $action = array($action);
			if(count($array) == 0 || !array_key_exists($resource, $array))
			{
				$this->_access[$role][$resource] = $action;
				return true;
			}

			return false;
		}
		
		private function searchParents($role, $resource, $action)
		{
			foreach($this->_roles[$role] as $parent) {
				if(isset($this->_access[$parent][$resource]) && is_array($this->_access[$parent][$resource]) && in_array($action, $this->_access[$parent][$resource])) {
					return true;
				}
				if(!is_null($this->_roles[$parent])) {
					return $this->searchParents($parent, $resource, $action);
				}	
			}
			return false;
		}
		
		public function isAllowed($role, $resource, $action)
		{
			if(is_null($role)) {
				$role = self::ISNULL;
			}
			if(array_key_exists($role, $this->_roles) || $role == self::ISNULL)
			{
				if(isset($this->_access[$role]) && is_array($this->_access[$role]) && array_key_exists($resource, $this->_access[$role]))
				{
					if(in_array($action, $this->_access[$role][$resource]))
					{
						return true;
					}
				}
				if(is_array($this->_access[self::ISNULL]) && array_key_exists($resource, $this->_access[self::ISNULL]))
				{
					if(in_array($action, $this->_access[self::ISNULL][$resource]))
					{
						return true;
					}
				}
				if(isset($this->_roles[$role]))
				{
					return $this->searchParents($role, $resource, $action);
				}
			}
			return false;
		}
		
		
		public function save($filename = 'authorization')
		{
			$serialize = serialize($this);
			$fp = fopen($filename, "w");
			fwrite($fp, $serialize);
			fclose($fp);
		}
		
		public function getRoles()
		{
			return $this->_roles;
		}
		
		public function getAccesses()
		{
			return $this->_access;
		}
	}
?>