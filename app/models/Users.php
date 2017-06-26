<?php

use BabiPHP\Component\Database\Table;

class Users
{
    private $db = null;

    public function __construct()
	{
		$this->db = new Table('users');
	}

    function getById($id, $fields = '*')
    {
        $bind[':id'] = $id;
        return $this->db->select($fields)->where('id = :id')->bind($bind)->findOne();
    }
}