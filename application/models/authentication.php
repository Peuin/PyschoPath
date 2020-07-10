<?php

class Authentication extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function findUser($user)
    {
        $field = is_numeric($user) ? 'id' : 'username';
    }
}