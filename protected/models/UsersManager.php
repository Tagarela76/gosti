<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 01.10.13
 * Time: 12:15
 */

class UsersManager {
    public function __construct(){

    }

    public static function factory($driver = 'DB'){
        $class = 'Users'.$driver.'Manager';
        return new $class();
    }

    /**
     *
     * @param int $id
     * @return User
     */
    public function getUserById($id) {
    }


} 