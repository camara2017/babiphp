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
 * BabiPHP Debug Class.
 * 
 * Not edit this file
 *
 */

	namespace BabiPHP\Component\Misc;

	use BabiPHP\Component\Misc\Set;
	use BabiPHP\Component\Misc\Session;
	use BabiPHP\Component\Config\Config;

	class Debugbar
	{
        private static $Set;
        private static $session;
		private static $McTime;
		private static $Errors = 0;
		private static $UserRole = [];
		private static $messages = [];
		private static $exceptions = [];
		private static $queries = [];

        public static $activate = false;
		
		/**
		* constructor
		*/
		public function __construct()
		{
			self::Init();
		}

		/**
		 * Activate
		 * @param boolean $value
		 */
		public static function Activate($value)
		{
			if(is_bool($value)) {
				self::$activate = $value;
			} else {
				self::addError('The activation must be a boolean value');
			}
		}

		/**
		 * RenderHead
		 * Start debug module
		 */
		public static function RenderHead()
		{
			self::Init();
			self::$McTime = self::getTime();
		}

		/**
		 * Render
		 * @param object $request the current request
		 */
		public static function Render($request = null)
		{
			return self::buildRender($request);
		}

		/**
		 * setActivation
		 * @param string $environment
		 */
		public static function setActivation($environment)
		{
			if($environment == 'dev') {
				self::$activate = true;
			} elseif($environment == 'prod') {
				self::$activate = false;
			}
		}

		/**
		 * addMessage
		 * @param string $message
		 * @param string $label
		 */
		public static function addMessages($message, $label = 'info')
		{
			self::$messages[] = '<p class="_bp-debubar-message _message-'.$label.'">'.strip_tags($message).'</p>';
		}

		/**
		 * addInfo
		 * @param string $info
		 */
		public static function addInfo($info)
		{
			self::addMessages($info, 'info');
		}

		/**
		 * addWarning
		 * @param string $warning
		 */
		public static function addWarning($warning)
		{
			self::addMessages($warning, 'warning');
		}

		/**
		 * addError
		 * @param string $error
		 */
		public static function addError($error)
		{
			self::addMessages($error, 'error');
		}

		public static function addException($e)
		{
			self::$exceptions[] = $e;
		}

		public static function addQuery($q)
		{
			self::$queries[] = $q;
		}

		/**
		 * Init
		 */
		private static function Init()
		{
			self::$Set = new Set();
			self::$session = Session::GetInstance();

			$roles = array(
	        	'anonym' => 'ROLE_ANONYM',
	        	'user' => 'ROLE_USER',
	        	'admin' => 'ROLE_ADMIN',
	        	'super_admin' => 'ROLE_SUPER_ADMIN'
	        );
			self::$UserRole = array_flip($roles);
		}

		/**
		* GetTime
		*/
		private static function getTime()
		{
			list($usec, $sec) = explode(" ",microtime());
			return ((float)$usec + (float)$sec);
		}

		/**
		* GetAuth
		*/
		private static function getAuth()
		{
			$app_name = strtolower(Config::Get('name'));
			$app_name = str_replace(' ', '_', $app_name);

			$app_provider = $app_name.'_auth_providers';

			$_s = self::$session->get($app_provider);
			$_role = self::$UserRole[$_s['role']];

			$_u = '<span class="simptip-position-top simptip-movable simptip-smooth" data-tooltip="Logged as '.$_role.'">';
			$_u .= '<span class="_bp-bar-badge">'.$_role.'</span></span>';
			return $_u;;
		}

		private static function getMemoryUsage()
		{
			$size = memory_get_usage(true);
		    $unit = array('B','KB','MB','GB','TB','PB');
			$msg = '<span class="simptip-position-top simptip-movable simptip-smooth" data-tooltip="Memory usage">';
		    $msg .= @round($size/pow(1024,($i=floor(log($size,1024)))),2).$unit[$i];
			$msg .= '</span>';

		    return $msg;
		}

		/**
		* Render debug module
		* @param 
		* @return mixed
		*/
		private static function buildRender($req = null)
		{
			$ReqDuration = (round(self::getTime() - self::$McTime, 3) * 1000).'ms';

	        $icons = [];
			$assets = COMPONENT.'Ressources'.DS;
	        $icons_name = ['babiphp', 'cogs', 'clock', 'warning', 'user'];

	        foreach ($icons_name as $ico) {
	            $icons[$ico] = file_get_contents($assets.'icons'.DS.$ico.'.svg');
	        }

			$css = trim(file_get_contents($assets.'css'.DS.'simptip.min.css'));
			$css .= trim(file_get_contents($assets.'css'.DS.'debugbar.min.css'));

			$jscript = trim(file_get_contents($assets.'js'.DS.'debugbar.min.js'));

			$output = '<style type="text/css">' . "\n" . $css . "\n</style>";
			$output .= '<div class="_bp-debug-bar" id="bp-debug-bar">';
			$output .= '<div class="_item">BabiPHP <span class="simptip-position-top simptip-movable simptip-smooth" data-tooltip="BabiPHP">';
			$output .= '<span class="_bp-bar-badge _bp-bar-badge-info">'.BP_VERSION.' / PHP '.PHP_VERSION.'</span></span></div>';

			if(is_object($req))
			{
				$output .= ' <span class="simptip-position-top simptip-movable simptip-smooth" data-tooltip="Request method">';
				$output .= ' <span class="_bp-bar-badge">'.$_SERVER['REQUEST_METHOD'].'</span></span> ';
				$output .= ' <span class="simptip-position-top simptip-movable simptip-smooth" data-tooltip="Route">';
				$output .= ' <span class="_bp-bar-badge">'.$req->getController().' @ '.$req->getAction().'</span></span> On: ';
				$output .= ' <span class="simptip-position-top simptip-movable simptip-smooth" data-tooltip="Route: name">';
				$output .= ' <span class="_bp-bar-it">'.$req->getRouteName().'</span></span>';
			}
			
			$output .= '<div style="float: right;">';
			$output .= '<div class="_item first">'.$icons['user'].' '.self::getAuth().'</div>';
			$output .= '<div class="_item clickable">Messages <span class="_bp-bar-badge">'.count(self::$messages).'</span></div>';
			$output .= '<div class="_item clickable">Request</div>';
			$output .= '<div class="_item clickable">Exceptions <span class="_bp-bar-badge">'.count(self::$exceptions).'</span></div>';
			$output .= '<div class="_item clickable">Databases <span class="_bp-bar-badge">'.count(self::$queries).'</span></div>';
			$output .= '<div class="_item"> '.$icons['cogs'].' '.self::getMemoryUsage().'</div>';
			$output .= '<div class="_item">'.$icons['clock'].' <span class="simptip-position-left simptip-movable simptip-smooth" data-tooltip="Request Duration">'. $ReqDuration .'</span></div>';
			$output .= '<div class="_item last"><div class="_bp-toggle-btn" id="bp-toggle-btn"><span></span></div></div>';
			$output .= '</div></div>';
			$output .= '<div class="_bp-debug-content"></div>';
			$output .= '<script type="text/javascript">' . "\n" . $jscript . "\n</script>";

			return $output;
		}
	}

?>
