<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 01.10.13
 * Time: 12:21
 */

class Folder extends CModel {
    public $id;
    public $name;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'folders';
    }

    public function rules() {
        return array(
            array('name', 'required'),
            array('name', 'length', 'max'=>1024),
        );
    }

    public function attributeNames()
    {
        return array(
            'id'=>'Id',
            'name'=>'Name'
        );
    }

    public function getName(){
        return $this->name;
    }

    public function getId(){
        return $this->id;
    }

    public function setName($newName){
        $this->name = $newName;
    }

}