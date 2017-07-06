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
 * @package       system.component.database
 * @since         BabiPHP v 0.8.8
 * @license       http://www.gnu.org/licenses/ GNU License
 *
 * 
 * Not edit this file
 *
 */

	namespace BabiPHP\Component\Database;

	use \PDO;
	use \PDOException;
	use BabiPHP\Component\Config\Config;
	use BabiPHP\Component\Misc\Debugbar;
	use BabiPHP\Component\Http\Response;

	/**
	* ConnectionManager
	*/
	class ConnectionManager
	{
		/**
		 * Liste des configurations pour se connecter à une base de donnée
		 *
		 * @var array
		 */
		private $configs = [];

		/**
		 * Nom de la configuration courante
		 *
		 * @var string
		 */
		private $current_config_name = '';

		private $driver = 'mysql';

		private $host = 'localhost';

		private $port = '3306';

		private $db;

		private $user;

		private $passwd;

		/**
		 * Encodage de la base de donnée
		 *
		 * @var string
		 */
		private $charset = 'utf8';

		/**
		 * Persistence de la base de donnée
		 *
		 * @var boolean
		 */
		private $persistent = true;

		/**
		 * Prefix des tables de la base de donnée
		 *
		 * @var string
		 */
		private $prefix = '';

		/**
		* The current connection
		*
		* @var: \PDO
		*/
		private $connection;

		/**
		 * Instance de la class (Singleton)
		 *
		 * @var ConnectionManager
		 */
		private static $_instance;

		/**
		 * Permet de récuperer l'instance la class
		 *
		 * @return ConnectionManager
		 */
		public static function getInstance()
		{
			if (is_null(self::$_instance)) {
				self::$_instance = new ConnectionManager();
			}

			return self::$_instance;
		}

		/**
		 * Permet de définir le nom de la configuration courante
		 *
		 * @param string $name
		 * @return void
		 */
		public function setCurrentConfigName(string $name)
		{
			$this->current_config_name = $name;
		}

		/**
		 * Permet d'ajouter une nouvelle configuration de connection
		 *
		 * @param string $name
		 * @param Array $data
		 * @return void
		 */
		public function addConfiguration(string $name, Array $data)
		{
			$this->configs[$name] = $data;
		}

		/**
		 * Permet d'ajouter plusieurs configurations de connection
		 *
		 * @param Array $config
		 * @return void
		 */
		public function addMultiConfiguration(array $configs)
		{
			foreach ($configs as $name => $data) {
				$this->addConfiguration($name, $data);
			}
		}

		/**
		 * Permet de récuperer une configuration
		 *
		 * @param string $name
		 * @return array
		 */
		public function getConfiguration(string $name = null)
		{
			if (is_null($name)) {
				$config = $this->configs[$this->current_config_name];
			} else {
				$config = $this->configs[$name];
			}

			return $config;
		}

		/**
		 *
		 * @return string
		 */
		public function getTablePrefix()
		{
			return $this->configs[$this->current_config_name]['prefix'];
		}

		/**
		 * Permet de récuperer le driver de la configuration courante
		 *
		 * @return string
		 */
		public function getDriver()
		{
			return $this->configs[$this->current_config_name]['driver'];
		}

		/**
		 * Permet de récuperer le connection courante
		 *
		 * @return ConnectionManager
		 */
		public function getConnection()
		{
			return ($this->connection instanceof PDO) ? $this->connection : $this->connect();
		}

		/**
		 * Permet de définir une configuration pour se connecter à une base de donnée
		 *
		 * @param string $name
		 * @param Array $data
		 * @return void
		 */
		private function setCurrentConfig(string $name)
		{
			if (isset($this->configs[$name]))
			{
				if (isset($this->configs[$name]['driver'])) {
					$this->charset = $this->configs[$name]['driver'];
				}

				if (isset($this->configs[$name]['host'])) {
					$this->charset = $this->configs[$name]['host'];
				}

				if (isset($this->configs[$name]['port'])) {
					$this->charset = $this->configs[$name]['port'];
				}

				$this->db = $this->configs[$name]['name'];
				$this->user = $this->configs[$name]['user'];
				$this->passwd = $this->configs[$name]['pass'];
				$this->prefix = $this->configs[$name]['prefix'];

				if (isset($this->configs[$name]['charset'])) {
					$this->charset = $this->configs[$name]['charset'];
				}

				if (isset($this->configs[$name]['persistent'])) {
					$this->persistent = $this->configs[$name]['persistent'];
				}

				$this->setCurrentConfigName($name);
			} else {
				throw new DatabaseException('Database configuration with name '.$name.' not set');
			}
		}

		/**
		 * Permet de créer la connection à la base de donnée
		 *
		 * @return \PDO
		 */
		private function connect()
		{
			$this->setCurrentConfig($this->current_config_name);

			$options = array(
				PDO::ATTR_PERSISTENT => $this->persistent,
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
			);
			$dsn = $this->driver.':host='.$this->host.';port='.$this->port.';dbname='.$this->db.';charset='.$this->charset;

			try {
				$this->connection = new PDO($dsn, $this->user, $this->passwd, $options);
				return $this->connection;
			} catch (PDOException $e) {
				Debugbar::addException($e->getMessage());

				if (Config::get('display_error_details')) {
					throw new DatabaseException('Unable to connect to database.');
				} else {
					$title = 'BabiPHP Application Error';
					$html = '<p>Unable to connect to database.</p>';

					$output = sprintf(
						"<html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8'>" .
						"<title>%s</title><style>body{margin:0;padding:30px;font:12px/1.5 Helvetica,Arial,Verdana," .
						"sans-serif;}h1{margin:0;font-size:48px;font-weight:normal;line-height:48px;}strong{" .
						"display:inline-block;width:65px;}</style></head><body><h1>%s</h1>%s</body></html>",
						$title,
						$title,
						$html
					);

					$response = new Response();
					$response->header('HTTP/1.0 500 Internal Server Error');
            		$response->header('Content-type', 'text/html');

					echo $output;
					exit;
				}
			}
		}
	}