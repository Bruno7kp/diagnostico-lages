<?php


namespace App\User;


use App\User\UserModel;

class Roles
{
    const ADMIN = "admin";

    const MOD = "mod";

    const DATA = "data";

    const GUEST = "guest";

    public static function isAdmin(UserModel $user) {
        return $user->role === Roles::ADMIN;
    }

    public static function isMod(UserModel $user) {
        return $user->role === Roles::MOD;
    }

    public static function isData(UserModel $user) {
        return $user->role === Roles::DATA;
    }

    public static function isGuest(UserModel $user) {
        return $user->role === Roles::GUEST;
    }

    public static function isDataOrUp(UserModel $user) {
        return Roles::isData($user) || Roles::isMod($user) || Roles::isAdmin($user);
    }

    public static function isModOrUp(UserModel $user) {
        return Roles::isMod($user) || Roles::isAdmin($user);
    }
}
