<?php


namespace App\Base;


use App\User\Roles;

class Helper
{
    public $role;

    public function __construct() {
        $this->role = new Roles();
    }

    public function has_any_period($indicator) {
        foreach ($indicator->periods as $v) {
            if (!empty(trim($v->value))) {
                return true;
            }
        }
        return false;
    }

    public function has_period($indicators, $period) {
        foreach ($indicators as $indicator) {
            foreach ($indicator->periods as $v) {
                if ($v->indicator_period === $period && !empty(trim($v->value))) {
                    return true;
                }
            }
        }
        return false;
    }

    public function dump($var) {
        echo "<pre>";
        var_dump($var);
        echo "</pre>";
    }

    function url(){
        return sprintf(
            "%s://%s",
            isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
            $_SERVER['SERVER_NAME']
        );
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