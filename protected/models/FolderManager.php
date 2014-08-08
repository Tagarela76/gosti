<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 01.10.13
 * Time: 12:15
 */

class FolderManager {
    public function __construct(){

    }

    public static function factory($driver = 'DB'){
        $class = 'Folder'.$driver.'Manager';
        return new $class();
    }

    /**
     *
     * @param int $id
     * @return Folder
     */
    public function getFolderById($id) {
    }

    /**
     *
     * @param int $parent
     * @return array
     */
    public function getFoldersTreeArray($parent) {
    }
} 