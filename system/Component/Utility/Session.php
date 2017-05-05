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
 * @author 		  Lambirou <lambirou225@gmail.com>
 * @link          http://babiphp.org BabiPHP Project
 * @package       system.component.utility
 * @since         BabiPHP v 0.3
 * @license       http://www.gnu.org/licenses/ GNU License
 */

/**
 * BabiPHP Session Class.
 * 
 * Not edit this file
 *
 */

	namespace BabiPHP\Component\Utility;
	
	use \BabiPHP\Component\Config\Config;

	class Session
	{
		protected $flash_name = 'default';

		protected $flash_template = array();

		protected $current_flash_template;

		protected $flash_slug = array('type', 'icon', 'message');

		private static $_instance;

		/**
		 * Constructor
		 */
		public function __construct()
		{
			$this->Start();
			$this->setFlashName(Config::Get('name'));
			$this->flash_template = Config::Get('flash_template');
			$this->current_flash_template = $this->flash_template['default'];
		}

		/**
		* GetInstance
		*/
		public static function getInstance()
		{
			if (is_null(self::$_instance))
				self::$_instance = new Session();

			return self::$_instance;
		}

		/**
		* Start
		*/
		public function Start()
		{
			if(!isset($_SESSION)) {
				session_start();
			}
		}

		/**
		* Set
		*/
		public function Set($key, $value, $persist = true)
		{
			if($persist !== true) {
				return false;
			}

			$_SESSION[$key] = $value;
		}

		/**
		* Get
		* @param $key session index
		* @return array or boolean (if the index is not exist)
		*/
		public function Get($key = null)
		{
			if($key) {
				return (isset($_SESSION[$key])) ? $_SESSION[$key] : false;
			} else {
				return $_SESSION;
			}
		}

		/**
		* Check
		*/
		public function Check($s, $_child = null)
		{
			if($_child) {
				return isset($_SESSION[$s][$_child]);
			}

			return isset($_SESSION[$s]);
		}

		/**
		* Delete
		* @param $key (string) index session at delete
		* @return boolean
		*/
		public function Delete($key)
		{
			if(isset($_SESSION[$key]))
			{
				unset($_SESSION[$key]);
				return true;
			}
			else return false;
		}

		/**
		* DeleteAll
		* Destroy all session
		*/
		public function DeleteAll()
		{
			unset($_SESSION);
			session_destroy();
		}

		/**
		* setFlash
		* @param array $flash
		*/
		public function setFlash($flash)
		{
			$this->flash_slug = array();
			
			foreach ($flash as $key => $value)
			{
				$this->flash_slug[] = $key;
			}
			
			$this->Set($this->flash_name, $flash);
		}

		/**
		* setFlash
		* @param $message, $type
		*/
		public function setSimpleFlash($icon, $message, $type = 'info')
		{
			$data = array(
				'message' => $message,
				'type' => $type,
				'icon' => $icon
			);

			$this->Set($this->flash_name, $data);
		}

		/**
		* checkFlash
		*/
		public function checkFlash()
		{
			return $this->Check($this->flash_name);
		}

		/**
		* Flash
		* @return flash message
		*/
		public function Flash()
		{
			$flash = $this->Get($this->flash_name);
			$template = $this->current_flash_template;
			$slugs = $this->flash_slug;

			if(!empty($flash))
			{
				foreach ($slugs as $key => $slug)
				{
					$template = str_replace('{{'.$slug.'}}', $flash[$slug], $template);
				}

				$this->Delete($this->flash_name);

				return $template;
			}
		}

		/**
		 * addFlashTemplate
		 * @param string $tpl
		 */
		public function addFlashTemplate($name, $tpl)
		{
			$this->flash_template[$name] = $tpl;
		}

		/**
		 * setFlashTemplate
		 * @param string $tpl
		 */
		public function setFlashTemplate($name)
		{
			$this->current_flash_template = $this->flash_template[$name];
		}

		/**
		 * setFlashName
		 * @param string $app_name
		 */
		private function setFlashName($app_name)
		{
			$app_name = strtolower($app_name);
			$app_name = str_replace(' ', '_', $app_name);

			$this->flash_name = $app_name.'_flash';
		}
	}

?>