<?php

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

		public function setConfiguration($data)
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
				Debugbar::addException('Error: '.$e->getMessage());
				exit('Unable to connect to database.');
			}
		}
	}