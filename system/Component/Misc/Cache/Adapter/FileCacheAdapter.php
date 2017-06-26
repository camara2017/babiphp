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
 * @since         BabiPHP v 0.7
 * @license       http://www.gnu.org/licenses/ GNU License
 */

/**
 * BabiPHP Cache Class.
 * 
 * Not edit this file
 *
 */

	namespace BabiPHP\Component\Misc\Cache\Adapter;

	use BabiPHP\Component\Exception\BpException;

	class FileCacheAdapter
	{
		private $cache_path;
		private $duration;
		private $buffer;

		/**
		 * List of cached files
		 *
		 * @var array
		 */
		private $cached_files = [];

		/**
		 * Cache list filename
		 *
		 * @var string
		 */
		private $cached_list = 'FileCacheAdapterList.json';
		
		public function __construct($duration)
		{
			$this->duration = $duration;
			$this->cache_path = APPPATH.'cache'.DS;
			$this->cached_files = $this->getCachedFileList();
		}

		private function getCachedFileList ()
		{
			if (file_exists($this->cache_path.$this->cached_list)) {
				$list = json_decode(file_get_contents($this->cache_path.$this->cached_list));
			} else {
				$list = [];
			}

			return $list;
		}

		private function saveCachedFileList(Array $list)
		{
			return file_put_contents($this->cache_path.$this->cached_list, json_encode($list));
		}

		/**
		* set
		* @param the $filename and the content to set
		*/
		public function set($filename, $content, $lifetime = 0)
		{
			if(!is_writable($this->cache_path)) {
				throw new BPException("Your cache directory is not writable", 9);
			}

			$this->cached_files[] = ['name'=>$filename, 'duration'=>$lifetime];

			if ($this->saveCachedFileList($this->cached_files)) {
				return file_put_contents($this->cache_path.$filename, $content);
			} else {
				throw new BPException('Cached file list c\'nt be saved.');
			}

		}

		/**
		* get
		* @param the $filename to get
		*/
		public function get($filename)
		{
			$file = $this->cache_path.$filename;

			if(file_exists($file)) {
				$duration = 0;
				
				foreach ($this->cached_files as $elm) {
					if ($elm['name'] == $filename) {
						$duration = $elm['duration'];
						break;
					}
				}

				$lifetime = (time() - filemtime($file)) / 60;

				if($lifetime <= $duration) {
					return file_get_contents($file);
				}
			}

			return false;
		}

		/**
		* delete
		* @param the $filename to delete
		* @return void
		*/
		public function delete($filename)
		{
			$file = $this->cache_path.$filename;
			
			if(file_exists($file)) {
				unlink($file);
			}
		}

		/**
		 * clear
		 *
		 * @return void
		 */
		public function clear()
		{
			$files = glob($this->cache_path.'*');

			foreach ($files as $file) {
				unlink($file);
			}
		}

	}

?>
