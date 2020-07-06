<?php


namespace App\Base;


use App\User\Roles;

class Helper
{
    public $role;

    public function __construct() {
        $this->role = new Roles();
    }

    public function dump($var) {
        echo "<pre>";
        var_dump($var);
        echo "</pre>";
    }

    public function slug($text) {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }
}