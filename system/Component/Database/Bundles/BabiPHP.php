<?php

	namespace Novelty\Bundles;

	use Novelty\ModelQuery;

	abstract class BabiPHP
	{
		private static $query;
		private static $table;

		public function __construct($table)
    	{
    		self::$query = new ModelQuery($table);
    		self::$table = $table;
    	}

    	public static function countQuery($param = 'count(*)')
	    {
	        self::$query->count($param);
	        return self::$query;
	    }

    	public static function selectQuery($param = '*')
	    {
	        self::$query->select($param);
	        return self::$query;
	    }

    	public static function insertQuery($param)
	    {
	        self::$query->insert($param);
	        return self::$query;
	    }

    	public static function updateQuery($param)
	    {
	        self::$query->update($param);
	        return self::$query;
	    }

	    public static function deleteQuery()
	    {
	    	self::$query->delete();
	        return self::$query;
	    }
	}

?>