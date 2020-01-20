<?php


namespace App\User;


class Auth
{
    public function startSession(UserModel $user) {
        $_SESSION["user_id"] = $user->id;
    }

    public function endSession() {
        $_SESSION["user_id"] = null;
    }

    public function getCurrentUser() {
        $user = new UserModel();
        if (array_key_exists("user_id", $_SESSION) && $_SESSION["user_id"] !== null) {
            $user = $user->getById($_SESSION["user_id"]);
        }
        return $user;
    }
}
