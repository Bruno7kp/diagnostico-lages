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

    /**
     * @return \Exception|null
     */
    public function getError() {
        return $this->error;
    }

    /**
     * @return string
     */
    public function getErrorMessage() {
        if ($this->error instanceof \Exception) {
            return $this->error->getMessage();
        }
        return "";
    }
}
