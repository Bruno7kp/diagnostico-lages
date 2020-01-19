<?php


namespace App\Base;


class Model
{
    /**
     * @var Database|null
     */
    public $db = null;

    /**
     * @var \Exception|null
     */
    public $error = null;

    public function __construct()
    {
        $this->db = new Database();
    }
}
