<?php


namespace App\Base;


use App\User\Roles;

class Helper
{
    public $role;

    public function __construct() {
        $this->role = new Roles();
    }
}