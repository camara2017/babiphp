<?php

    namespace Base;

    use \BabiPHP\Component\Database\ModelQuery as TableQuery;
	use \UsersQuery as ChildUsersQuery;

    abstract class UsersQuery extends TableQuery
    {
    	public function __construct()
    	{
    		parent::__construct('users');
    	}

    	public static function count($param = 'count(*)')
	    {
	        $query = new ChildUsersQuery();
	        $query->countQuery($param);
	        return $query;
	    }

    	public static function select($param = '*')
	    {
	        $query = new ChildUsersQuery();
	        $query->selectQuery($param);
	        return $query;
	    }

    	public static function insert($param)
	    {
	        $query = new ChildUsersQuery();
	        $query->insertQuery($param);
	        return $query;
	    }

    	public static function update($param)
	    {
	        $query = new ChildUsersQuery();
	        $query->updateQuery($param);
	        return $query;
	    }

	    public static function delete()
	    {
	    	$query = new ChildUsersQuery();
	        $query->deleteQuery();
	        return $query;
	    }
    }

?>