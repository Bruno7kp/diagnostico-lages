<?php


namespace App\Base;


class Model
{
    /**
     * @var Database|null
     */
    protected $db = null;

    /**
     * @var \Exception|null
     */
    protected $error = null;

    public function __construct()
    {
        $this->db = new Database();
    }
}
