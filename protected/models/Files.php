<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 01.10.13
 * Time: 12:21
 */

class Files extends CModel {
    public $id;
    public $folders_id;
    public $name;
    public $path;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'files';
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
            'name'=>'Name',
            'path'=>'Path'
        );
    }

    public function getName(){
        return $this->name;
    }

    public function getPath(){
        return $this->path;
    }

    public function getId(){
        return $this->id;
    }

    public function setName($newName){
        $this->name = $newName;
    }

    public function setPath($newPath){
        $this->path = $newPath;
    }

}