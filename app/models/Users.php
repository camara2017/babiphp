<?php

use BabiPHP\Component\Database\Table;

class Users
{
    private $table = null;

    public function __construct()
	{
        $this->table = new Table('users');
	}

    function getById($id, $fields = '*')
    {
        $bind[':id'] = $id;
        return $this->table->select($fields)->where('id = :id')->bind($bind)->findOne();
    }

    function getAll()
    {
        return $this->table->select()->limit(5)->find();
    }
}