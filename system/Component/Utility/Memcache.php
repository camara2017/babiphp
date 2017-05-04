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
 * @package       system.libs
 * @since         BabiPHP v 0.1
 * @license       http://www.gnu.org/licenses/ GNU License
 */

/**
 * BabiPHP Memcache Class.
 * 
 * Not edit this file
 *
 */

	namespace BabiPHP\Component\Utility;

	use Memcache;

	class Memcache
	{
		private $_memcache = null;

		public function __construct()
		{
			$this->_memcache = new Memcache;
			$this->_connect();
		}
		
		private function _connect()
		{
			$config = parse_ini_file(__APPLICATION_DIR__ . 'configuration/core.configuration.ini', true);
			if (isset($config['memcache'])) {
				$i = 0;
				foreach($config['memcache']['server.host'] as $server) {
					$this->_memcache->addServer(
						$config['memcache']['server.host'][$i], $config['memcache']['server.port'][$i]
					);
					$i++;
				}
			} else {
				throw new Exception('no config available in ' . __APPLICATION_DIR__ . 'configuration/core.configuration.ini');
			}
		}

		public function get($key)
		{
			return $this->_memcache->get($key);
		}
		
		public function set($key, $data, $lifetime=0, $compressed=0)
		{
			return $this->_memcache->set($key, $data, $compressed, $lifetime);
		}
		
		public function delete($key)
		{
			return $this->_memcache->delete($key);
		}
		
		public function clear()
		{
			return $this->_memcache->flush();
		}
	}
?>