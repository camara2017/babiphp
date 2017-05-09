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
	use \BabiPHP\Component\Utility\Debugbar;

	/**
	* ConnectionManager
	*/
	class ConnectionManager
	{
		private $config;
		private $driver;
		private $host;
		private $port;
		private $db;
		private $user;
		private $passwd;
		private $charset = 'utf8';
		private $persistent = true;
		private $prefix;

		/**
		* The current connection
		* @type: \PDO
		*/
		private $connection;

		private static $_instance;

		public static function getInstance()
		{
			if (is_null(static::$_instance)) {
				static::$_instance = new ConnectionManager();
			}

			return static::$_instance;
		}

		public function setConfiguration(Array $data)
		{
			$this->config = $data;

			$this->driver = $data['driver'];
			$this->host = $data['host'];
			$this->port = $data['port'];
	        $this->db = $data['name'];
			$this->user = $data['user'];
	        $this->passwd = $data['pass'];
	        $this->port = $data['port'];
	        $this->charset = $data['charset'];
	        $this->persistent = $data['persistent'];
	        $this->prefix = $data['prefix'];
		}

		public function getConfiguration()
		{
			return $this->config;
		}

		/**
		 *
		 * @return string
		 */
		public function getTablePrefix()
		{
			return $this->prefix;
		}

		
		public function getDriver()
		{
			return $this->driver;
		}

		public function getConnection()
		{
			return ($this->connection instanceof PDO) ? $this->connection : $this->connect();
		}

		private function connect()
		{
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
				throw new DatabaseException('Unable to connect to database.');
			}
		}
	}