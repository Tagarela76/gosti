<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 01.10.13
 * Time: 12:15
 */

class FilesManager {
    public function __construct(){

    }

    public static function factory($driver = 'DB'){
        $class = 'Files'.$driver.'Manager';
        return new $class();
    }

    /**
     *
     * @param int $id
     * @return Files
     */
    public function getFilesById($id) {

    }

    /**
     *
     * @param int $folder
     * @return array
     */
    public function getFilesByFolder($folder) {
    }
} 