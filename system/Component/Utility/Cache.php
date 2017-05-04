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

	namespace BabiPHP\Component\Utility;

	class Cache
	{
		private $Dirname;
		public $Duration;
		private $Buffer;
		
		public function __construct($duration)
		{
			$this->Duration = $duration;
			$this->Dirname = TMP.'cache'.DS;
		}

		/**
		* Write
		* @param the $filename and the content to write
		*/
		public function Write($filename, $content)
		{
			if(!is_writable($this->Dirname))
			{
				throw new BPException("Your Cache directory is not writable", 9);
			}
			return file_put_contents($this->Dirname.$filename, $content);
		}

		/**
		* Read
		* @param the $filename to read
		*/
		public function Read($filename)
		{
			$file = $this->Dirname.$filename;
			if(!file_exists($file)) return false;
			$lifetime =(time() - filemtime($file)) / 60;
			if($lifetime > $this->Duration) return false;
			return file_get_contents($file);
		}

		/**
		* Delete
		* @param the $filename to delete
		* @return void
		*/
		public function Delete($filename)
		{
			$file = $this->Dirname.$filename;
			if(file_exists($file)) unlink($file);
		}

		/**
		* Clear
		*/
		public function Clear()
		{
			$files = glob($this->Dirname.'*');

			foreach ($files as $file)
			{
				unlink($file);
			}
		}

		/**
		* Inc
		* @param $file, $cachename
		* @return mixed
		*/
		public function Inc($file, $cachename = null)
		{
			if(!$cachename) $cachename = basename($file);
			if($content = $this->Read($cachename))
			{
				echo $content;
				return true;
			}
			ob_start();
			require $file;
			$content = ob_get_clean();
			$this->Write($cachename, $content);
			echo $content;
			return true;
		}

		/**
		* Start
		* @param $cachename
		* @return void
		*/
		public function Start($cachename)
		{
			if($content = $this->Read($cachename))
			{
				echo $content;
				$this->Buffer = false;
				return true;
			}
			ob_start();
			$this->Buffer = $cachename;
		}

		/**
		* End
		* @return mixed
		*/
		public function End()
		{
			if(!$this->Buffer) return false;
			$content = ob_get_clean();
			$this->Write($this->Buffer, $content);
			echo $content;
			return true;
		}

	}

?>
