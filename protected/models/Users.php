<?php

/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 01.10.13
 * Time: 12:21
 */
class Users extends CModel
{

    public $id;
    public $email;
    public $pass;
    public $status;
    
    /**
     *
     * exdpiration Date
     * 
     * @var string 
     */
    public $expirationDate;
    
    /**
     *
     * number of free downloads
     * 
     * @var int
     */
    public $downloadsNumber = 3;

    const DOWNLOADS_NUMBER = 3;
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'users';
    }

    public function rules()
    {
        return array(
            array('email, pass', 'required'),
            array('email, pass', 'length', 'max' => 255),
        );
    }

    public function attributeNames()
    {
        return array(
            'id' => 'Id',
            'email' => 'Mail',
            'pass' => 'Password'
        );
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName($newEmail)
    {
        $this->email = $newEmail;
    }

    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;
    }

    public function getDownloadsNumber()
    {
        return $this->downloadsNumber;
    }

    public function setDownloadsNumber($downloadsNumber)
    {
        $this->downloadsNumber = $downloadsNumber;
    }

    public function isActive()
    {
        return ($this->status == 1);
    }

    public function save()
    {
        $result = UsersManager::factory()->saveUser($this);
        if ($result) {
            $this->id = $result;
            return true;
        } else {
            return false;
        }
    }

}