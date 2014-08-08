<?php

/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 01.10.13
 * Time: 12:19
 */
class UsersDBManager extends UsersManager
{

    public function getUserById($id)
    {
        $db = Yii::app()->db;
        $users_table = Users::tableName();

        $sql = "SELECT * FROM {$$users_table} WHERE id=:ID LIMIT 1";
        $command = $db->createCommand($sql);
        $command->bindParam(":ID", $id);
        $row = $command->queryRow();

        $user = new Users;
        $user->id = $row['id'];
        $user->email = $row['email'];
        $user->pass = $row['pass'];
        $user->status = $row['status'];

        return $user;
    }

    public function saveUser(Users $user)
    {
        $db = Yii::app()->db;
        $users_table = Users::tableName();

        $sql = "REPLACE INTO " . $users_table . " (email, pass, status, expiration_date, downloads_number) ".
                "VALUES (:email, :pass, :status, :expiration_date, :downloads_number)";
        $command = $db->createCommand($sql);
        $command->bindParam(":email", $user->email, PDO::PARAM_STR);
        $command->bindParam(":pass", $user->pass, PDO::PARAM_STR);
        $command->bindParam(":status", $user->status, PDO::PARAM_STR);
        $command->bindParam(":expiration_date", $user->expirationDate, PDO::PARAM_STR);
        $command->bindParam(":downloads_number", $user->downloadsNumber, PDO::PARAM_INT);
        $inserted = $command->execute();

        if ($inserted) {
            return Yii::app()->db->getLastInsertID();
        } else {
            return $inserted;
        }
    }

    public function getUserByEmail($email)
    {
        $db = Yii::app()->db;
        $users_table = Users::tableName();

        $sql = "SELECT * FROM {$users_table} WHERE email=:MAIL LIMIT 1";
        $command = $db->createCommand($sql);
        $command->bindParam(":MAIL", $email);
        $row = $command->queryRow();
        if (empty($row)) {
            return false;
        }
        $user = new Users;
        $user->id = $row['id'];
        $user->email = $row['email'];
        $user->pass = $row['pass'];
        $user->status = $row['status'];
        $user->expirationDate = $row['expiration_date'];
        $user->downloadsNumber = $row['downloads_number'];

        return $user;
    }

    public function getkey($login, $pass)
    {
        $db = Yii::app()->db;
        $users_table = Users::tableName();
        $res = $db->createCommand()
                ->select('*')
                ->from($users_table)
                ->where('email = :email', array(':email' => $login))
                ->queryRow();

        if ($res) {
            if ($res['pass'] == $pass) {
                return array(
                    'key' => md5('12345' . date('Ymd') . $res['email']),
                    'downloadsNumber' => $res['downloads_number'],
                    'expirationDate' => $res['expiration_date'],
                );
            } else {
                return array('error' => 'wrong password!');
            }
        } else {
            return array('error' => 'user not found!');
        }
    }

    public function checkkey($userId, $key)
    {
        $db = Yii::app()->db;
        $users_table = Users::tableName();
        $res = $db->createCommand()
                ->select('*')
                ->from($users_table)
                ->where('email = :email', array(':email' => $userId))
                ->queryRow();
        if ($res) {
            if ($key == md5('12345' . date('Ymd') . $res['email'])) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getPass($email)
    {
        $db = Yii::app()->db;
        $users_table = Users::tableName();
        $res = $db->createCommand()
                ->select('*')
                ->from($users_table)
                ->where('email = :email', array(':email' => $email))
                ->queryRow();
        if ($res) {
            return $res['pass'];
        } else {
            return false;
        }
    }

}