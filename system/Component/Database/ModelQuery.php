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

	use \BabiPHP\Component\Utility\Debugbar;
	use \BabiPHP\Component\Database\FetchMode\FetchXml;
	use \BabiPHP\Component\Database\Graphs\GraphResponse;
	use \PDO;
	use \PDOException;

	class ModelQuery
	{
		private $manager;
		private $returnType = 'object';
		private $returnStatu = false;
		private $error = null;
		private $results = null;
		private $table;
		private $table_prefix;

		protected $_fields;
		protected $_where;
		protected $_order;
		protected $_group;
		protected $_limit;
		protected $_sqlType;
		protected $_sql;
		protected $_bind;
		protected $_fetch = true;

		private static $_instance;

		function __construct($table)
		{
			$this->manager = ConnectionManager::getInstance();
			$this->table_prefix = $this->manager->getTablePrefix();
			$this->table = $this->table_prefix.$table;

			self::$_instance = $this;
		}

		/**
		 * getInstance
		 * @return class [The current instance of this class]
		 */
		public static function getInstance()
		{
			return self::$_instance;
		}

		public function countQuery($fields = 'count(*)')
		{
			if(is_array($fields)) {
				$fields = implode(', ', $fields);
			}

			$this->_fields = $fields;
			$this->_sqlType = 'count';
			$this->_fetch = false;

			return $this;
		}

		public function selectQuery($fields = '*')
		{
			if(is_array($fields)) {
				$fields = implode(', ', $fields);
			}

			$this->_fields = $fields;
			$this->_sqlType = 'select';

			return $this;
		}

		public function insertQuery($fields)
		{
			$this->_sqlData = $fields;
			$this->_sqlType = 'insert';
			return $this;
		}

		public function updateQuery($fields)
		{
			foreach ($fields as $column => $value) {
				$field_array[] = '`'.$column.'` = "'.$value.'"';
			}

			$this->_sqlData = implode(',', $field_array);
			$this->_sqlType = 'update';
			return $this;
		}

		public function deleteQuery()
		{
			$this->_sqlType = 'delete';
			return $this;
		}

		public function where($where = '')
		{
			$this->_where = $where;
			return $this;
		}

		public function orderBy($order)
		{
			$this->_order = $order;
			return $this;
		}

		public function groupBy($group)
		{
			$this->_group = $group;
			return $this;
		}

		public function limit($limit)
		{
			$this->_limit = $limit;
			return $this;
		}

		public function bind($bind = array())
		{
			$this->_bind = $this->cleanBind($bind);
			return ($this->_sqlType == 'delete' || $this->_sqlType == 'count') ? $this->save() : $this;
		}

		/**
		 * find
		 * @return mixed [the request result]
		 */
		public function find()
		{
			return $this->getResult(true);
		}

		/**
		 * findOne
		 * @return mixed [fetch one result]
		 */
		public function findOne()
		{
			return $this->getResult(false);
		}

		/**
		 * save
		 * @return mixed
		 */
		public function save()
		{
			return $this->getResult();
		}

		public function query(string $sql, $bind)
		{
			$this->_sql = $sql;
			$this->_bind = $this->cleanBind($bind);
			return $this->getResult();
		}

		/**
		 * getStatu "set the statu mode"
		 * @return current instance of this class
		 */
		public function getStatu()
		{
			$this->returnStatu = true;
			return $this;
		}

		/**
		 * run
		 * @param  string $sql
		 * @return mixed  The result or error
		 */
		private function run($sql = null)
		{
			$db = $this->manager->getConnection();
			$this->error = '';
			$this->_sql = (is_null($sql)) ? $this->buildQuery() : $sql;

			try
			{
				$pdostmt = $db->prepare($this->_sql);
				$request = ($this->_bind) ? $pdostmt->execute($this->_bind) : $pdostmt->execute();

				if($request)
				{
					if(preg_match("/^(" . implode("|", array("select", "describe", "pragma")) . ") /i", $this->_sql))
					{
						$pdostmt->setFetchMode(PDO::FETCH_OBJ);
						$this->results = ($this->_fetch) ? $pdostmt->fetchAll() : $pdostmt->fetch();
					}
					elseif(preg_match("/^(" . implode("|", array("insert")) . ") /i", $this->_sql)) {
						$this->results = $db->lastInsertId();
					}
					elseif(preg_match("/^(" . implode("|", array("delete", "update")) . ") /i", $this->_sql)) {
						$this->results = true;
					}
				}	
			}
			catch (PDOException $e)
			{
				$this->error = $e;
			}
		}

		/**
		 * filter
		 * @param  string $table
		 * @param  array $info
		 * @return array
		 */
		private function filter($table, $info)
		{
			$driver = $this->manager->getDriver();

			if($driver == 'sqlite')
			{
				$sql = "PRAGMA table_info('" . $table . "');";
				$key = "name";
			}
			elseif($driver == 'mysql')
			{
				$sql = "DESCRIBE " . $table . ";";
				$key = "Field";
			}
			else
			{	
				$sql = "SELECT column_name FROM information_schema.columns WHERE table_name = '" . $table . "';";
				$key = "column_name";
			}

			$this->run($sql);

			if($this->results !== false)
			{
				$fields = array();

				foreach($list as $record) {
					$fields[] = $record->$key;
				}

				return array_values(array_intersect($fields, array_keys($info)));
			}

			return array();
		}

		/**
		 * buildQuery
		 * @return string The sql request formated
		 */
		private function buildQuery()
		{
			if($this->_sqlType == 'count')
			{
				$sql = "SELECT ".$this->_fields;
				$sql .= " FROM ".$this->table;

				if($this->_where) {
					$sql .= " WHERE ".$this->_where;
				}
				if($this->_order) {
					$sql .= " ORDER BY ".$this->_order;
				}
				if($this->_limit) {
					$sql .= " LIMIT ".$this->_limit;
				}
			}
			elseif($this->_sqlType == 'select')
			{
				$sql = "SELECT ".$this->_fields;
				$sql .= " FROM ".$this->table;

				if($this->_where) {
					$sql .= " WHERE ".$this->_where;
				}
				if($this->_order) {
					$sql .= " ORDER BY ".$this->_order;
				}
				if($this->_limit) {
					$sql .= " LIMIT ".$this->_limit;
				}
			}
			elseif($this->_sqlType == 'insert')
			{
				$columns = array();
				$values = array();

				foreach ($this->_sqlData as $column => $value)
				{
					$columns[] = '`'.$column.'`';
					$values[] = '"'.$value.'"';
				}

				$sql = "INSERT INTO `".$this->table."` (".implode(',', $columns).")";
				$sql .= " VALUES (".implode(',', $values).")";
			}
			elseif($this->_sqlType == 'update')
			{
				$sql = "UPDATE ".$this->table." SET ";
				$sql .= $this->_sqlData;

				if($this->_where) {
					$sql .= " WHERE ".$this->_where;
				}
			}
			elseif($this->_sqlType == 'delete')
			{
				$sql = "DELETE FROM ".$this->table;

				if($this->_where) {
					$sql .= " WHERE ".$this->_where;
				}
			}

			return (isset($sql)) ? $sql.';' : null;
		}

		private function reset()
		{
			$this->error = null;
			$this->results = null;
			$this->returnType = 'object';
			$this->returnStatu = false;
			$this->_fields = '';
			$this->_where = '';
			$this->_order = '';
			$this->_limit = null;
			$this->_sqlType = null;
			$this->_sql = null;
			$this->_bind = null;
			$this->_fetch = true;
		}

		/**
		 * getResult
		 * @param $fetch
		 * @return mixed
		 */
		private function getResult($fetch = null)
		{
			if (is_bool($fetch)) {
				$this->_fetch = $fetch;
			}

			$this->run();

			if($this->returnStatu)
			{
				$data = $this->voidClass();
				$data->error = $this->voidClass(['message'=>null, 'line'=>null, 'file'=>null]);
				$data->request = $this->voidClass(['sql'=>$this->_sql, 'bind'=>$this->_bind, 'type'=>$this->_sqlType, 'fields'=>$this->_fields]);

				if(!is_null($this->results))
				{
					$data->success = true;
					$data->response = $this->fetchMode($this->results);
				}
				elseif(!is_null($this->error))
				{
					$data->success = false;
					$data->response = null;

					$data->error->message = $this->error->getMessage();
					$data->error->line = $this->error->getLine();
					$data->error->file = basename($this->error->getFile());
					
					Debugbar::addException($this->error);
				}

				$data = new GraphResponse($data);
			}
			else
			{
				if(!is_null($this->results)) {
					$data = $this->fetchMode($this->results);
				}
				elseif(!is_null($this->error)) {
					$data = $this->error;
				}
			}

			$this->reset();

			return $data;
		}

		public function toJson()
		{
			$this->returnType = 'json';
			return $this;
		}

		public function toArray()
		{
			$this->returnType = 'array';
			return $this;
		}

		public function toBoth()
		{
			$this->returnType = 'both';
			return $this;
		}

		public function toYaml()
		{
			$this->returnType = 'yaml';
			return $this;
		}

		public function toXml()
		{
			$this->returnType = 'xml';
			return $this;
		}

		public function toCsv()
		{
			$this->returnType = 'csv';
			return $this;
		}

		private function fetchMode($results)
		{
			if(!is_bool($results) && !is_numeric($results))
			{
				switch ($this->returnType)
				{
					case 'array':
						$results = $this->objectToArray($results);
						break;
					case 'both':
						$results = $this->objectToBoth($results);
						break;
					case 'json':
						$results = json_encode($results);
						break;
					case 'yaml':
						$results = yaml_emit($results);
						break;
					case 'xml':
						$xml = new FetchXml('my_node');
						$results = $xml->createNode($results);
						break;
					case 'csv':
						$results = $this->arrayToCsv($this->objectToArray($results), 'sql_result');
						break;
					default:
						$results = $results;
						break;
				}
			}

			return $results;
		}

		/**
		* cleanBind
		* @param binding
		*/
		private function cleanBind($bind)
		{
			if(!is_array($bind)) {
				$bind = (!empty($bind)) ? [$bind] : [];
			}

			return $bind;
		}

		/**
        * arrayToObject
        * @param $array
        * @return Object
        */
        private function arrayToObject($array)
        {
            if(is_array($array) && !empty($array))
            {
                $d = new \stdClass();

                foreach ($array as $k => $v)
                {
                    if(!empty($v) && is_array($v)) {
                    	$v = $this->arrayToObject($v);
                    }

                    $d->$k = $v;
                }

                return $d;
            }
        }

        /**
        * objectToArray
        * @param $object
        * @return Array
        */
        private function objectToArray($object)
        {
            if(is_object($object)) {
            	return get_object_vars($object);
            }
            elseif(is_array($object))
            {
            	$data = array();

            	foreach ($object as $key => $value) {
            		$data[] = get_object_vars($value);
            	}

            	return $data;
            }
        }

        /**
         * objectToBoth
         * @param  object $data
         * @return array
         */
        private function objectToBoth($object)
        {
        	if(count($object) > 1)
        	{
        		$new_array = array();

        		foreach ($object as $key => $value)
        		{
        			$value = $this->objectToArray($value);
        			$num = array_values($value);
        			$new_array[] = array_merge($value, $num);
        		}

        		$data = $new_array;
        	}
        	else
        	{
        		$data = $this->objectToArray($object);
        		$num = array_values($data);
        		$data = array_merge($data, $num);
        	}

        	return $data;
        }

        private function arrayToCsv($input_array, $output_file_name, $delimiter = ',')
		{
			ob_start();

		    $f = fopen('php://memory', 'w');

		    foreach ($input_array as $line)
		    {
		        fputcsv($f, $line, $delimiter);
		    }

		    fseek($f, 0);
		    header('Content-Type: application/csv');
		    header('Content-Disposition: attachement; filename="' . $output_file_name . '";');
		    fpassthru($f);

		    return ob_get_clean();
		}

		/**
		* create one void class
		*
		* @param array
		* @return object
		*/
		private function voidClass($array = [])
		{
			$class = new \stdClass;

			foreach ($array as $key => $value) {
				$class->$key = $value;
			}

			return $class;
		}

		/**
	     * Close connection
	     */
	    public function __destruct()
	    {
			$this->manager = null;
	        $this->table = '';
	        $this->table_prefix = '';
			self::$_instance = null;
	    }
	}

?>