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
 * @package       system.component.utility.cache
 * @since         BabiPHP v 0.8.8
 * @license       http://www.gnu.org/licenses/ GNU License
 */

/**
 * 
 * Not edit this file
 *
 */

	namespace BabiPHP\Component\Utility\Cache\Adapter;

	use BabiPHP\Component\Exception\BpException;
	use \Memcache;

	class MemcacheAdapter
	{
		private $_memcache = null;

		public function __construct()
		{
			$this->_memcache = new Memcache;
			$this->_connect();
		}
		
		private function _connect()
		{
			$config = parse_ini_file(CONFIG . 'memcache.config.ini', true);

			if (isset($config['memcache'])) {
				$i = 0;
				foreach($config['memcache']['server.host'] as $server) {
					$this->_memcache->addServer(
						$config['memcache']['server.host'][$i], $config['memcache']['server.port'][$i]
					);
					$i++;
				}
			} else {
				throw new BpException('No Memcache config available');
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