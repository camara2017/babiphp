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
* @since         BabiPHP v 0.1
* @license       http://www.gnu.org/licenses/ GNU License
*
*
* Not edit this file
*/

namespace BabiPHP\Core;

use BabiPHP\Component\Config\Config;
use BabiPHP\Component\Routing\Router;
use BabiPHP\Component\Misc\Debugbar;
use BabiPHP\Component\Database\ConnectionManager;
use BabiPHP\Component\Error\Run as ErrorRun;
use BabiPHP\Component\Error\Handler\PrettyPageHandler as ErrorPageHandler;

class Application
{
	/**
	 * BabiPHP Version
	 * @const string
	 */
	const VERSION = '0.8.10-dev';

	/**
	 * App Instance
	 * @var array
	 */
	protected static $apps = [];

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var array
	 */
	private static $_autoloaders = ['core_autoload', 'model_autoload', 'symfony_autoload'];

	/**
	 * @var BabiPHP\Core\Dispatcher
	 */
	private $dispatcher;

	/**
	 * PSR-0 Symfony_autoload
	 */
	public static function symfony_autoload($class_name)
	{
		$namespace = str_replace('\\', DS, ltrim($class_name, '\\'));
		$file_name = BASEPATH.'Component'.DS.'Symfony'.substr($namespace, 17).EXT;

		if(file_exists($file_name)) {
			require $file_name;
		}
	}

	/**
	 * PSR-0 model_autoload
	 */
	public static function model_autoload($class_name)
	{
		$file_name = APPPATH.'models'.DS. str_replace('\\', DS, ltrim($class_name, '\\')).EXT;

		if(file_exists($file_name)) {
			require $file_name;
		}
	}

	/**
	 * PSR-0 core_autoload
	 */
	public static function core_autoload($class_name)
	{
		$class_name = ltrim($class_name, '\\BabiPHP\\');
		$file_name = BASEPATH.str_replace('\\', DS, $class_name).EXT;

		if(file_exists($file_name)) {
			require $file_name;
		}
	}

	/**
	 * Register PSR-0 Autoload
	 */
	public static function registerAutoloader()
	{
		foreach (static::$_autoloaders as $k => $autoloader) {
			spl_autoload_register(__NAMESPACE__.'\\Application::'.$autoloader);
		}
	}

	/**
	 * Constructor
	 */
	public function __construct()
	{
		// Configurate handling errors
		$error_handler = new ErrorRun;
		$error_handler->pushHandler(new ErrorPageHandler);
		$error_handler->register();

		// Remove magic quotes
		$this->removeMagicQuotes();

		// Framework version
		define('BP_VERSION', self::VERSION);

		// Path to the temporary files directory
		if (!defined('CONFIG')) {
			define('CONFIG', APPPATH.'config'.DS);
		}

		// Path to the vendors directory
		if (!defined('VENDORS')) {
			define('VENDORS', ROOT.DS.'vendor'.DS);
		}

		// Path to the component directory
		if (!defined('COMPONENT')) {
			define('COMPONENT', BASEPATH.'Component'.DS);
		}

		// Configuration of application
		require_once CONFIG.'Config'.EXT;
		require_once CONFIG.'Security'.EXT;
		require_once CONFIG.'Database'.EXT;

		// Definition des constantes
		if (!defined('VIEW_EXT')) {
			define('VIEW_EXT', Config::get('view_ext'));
		}

		if (!defined('APP_TEMPLATE')) {
			define('APP_TEMPLATE', Config::get('template'));
		}

		if (!defined('APP_BASE_URL')) {
			$scheme = (isset($_SERVER['HTTPS'])) ? 'https' : 'http';
			$base_url = $scheme.'://'.trim(Config::get('base_url'), '/');

			define('APP_BASE_URL', $base_url);
		}

		if (!defined('APP_HOME_CONTROLLER')) {
			define('APP_HOME_CONTROLLER', Config::get('index_controller'));
		}

		// webroot path
		if (defined('WWW_ROOT') && !defined('WEBROOT')) {
			define('WEBROOT', APP_BASE_URL.WWW_ROOT);
		}

		// Functions
		require_once COMPONENT.'Function'.DS.'Functions'.EXT;
		require_once COMPONENT.'Function'.DS.'FunctionHelper'.EXT;
		require_once APPPATH.'helpers'.DS.'Functions'.EXT;

		// Routing
		Router::setBasePath(APP_BASE_URL);
		require_once CONFIG.'Routes'.EXT;

		// Activation du module de debogage
		Debugbar::setActivation(Config::get('environment'));

		// Make default if first instance
		if (is_null(static::getInstance())) {
			$this->setName('default');
		}

		// Definition des configurations de base pour la connexion à la base de donnée
		$manager = ConnectionManager::getInstance();
		$manager->addMultiConfiguration(Config::get('databases'));
		$manager->setCurrentConfigName(Config::get('default_database'));

		// instanciate the dispatcher
		$this->dispatcher = new Dispatcher();
	}

	/**
	 * Permet de récuperer l'instance de Application
	 * 
	 * @param  string $name
	 * @return \BabiPHP\Core\Application|null
	 */
	public static function getInstance($name = 'default')
	{
		return isset(static::$apps[$name]) ? static::$apps[$name] : null;
	}

	/**
	 * Permet de définir le nom de l'application
	 * 
	 * @param string $name
	 * @return void
	 */
	public function setName(string $name)
	{
		$this->name = $name;
		static::$apps[$name] = $this;
	}

	/**
	 * Permet de récuperer le nom de l'application
	 * 
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Permet de lancer l'application
	 *
	 * @return void
	 */
	public function run()
	{
		// Vendor Autoload
		if (Config::get('composer_autoload') === true) {
			require_once VENDORS.'autoload'.EXT;
		}

		// Initialize the Debug bar
		if(Debugbar::$activate) {
			Debugbar::RenderHead();
		}

		// Inclusion du controller principal de l'application
		$app_controller = APPPATH.'controllers'.DS.'AppController'.EXT;

		if(file_exists($app_controller)) {
			require_once $app_controller;
		}

		// Inclusion du controller principal de l'application
		$hook_controller = APPPATH.'config'.DS.'HookControllers'.EXT;

		if(file_exists($hook_controller)) {
			require_once $hook_controller;
		}

		// Dispatch
		$this->dispatcher->dispatch();

		// current request
		$request = Dispatcher::getRequest();

		// Desable auto-render and debugbar
		if($request->isAjax()) {
			Debugbar::Activate(false);
		}

		// Render the Debug bar
		if(Debugbar::$activate) {
			echo Debugbar::Render($request);
		}
	}

	/**
	 * Désactive automatiquement les magic_quotes
	 *
	 * @return void
	 */
	private function removeMagicQuotes()
	{
		ini_set('magic_quotes_runtime', 0);

		if (1 == get_magic_quotes_gpc())
		{
			function remove_magic_quotes_gpc(&$value)
			{
				$value = stripslashes($value);
			}

			array_walk_recursive($_GET, 'remove_magic_quotes_gpc');
			array_walk_recursive($_POST, 'remove_magic_quotes_gpc');
			array_walk_recursive($_COOKIE, 'remove_magic_quotes_gpc');
		}
	}
}