<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 02.10.13
 * Time: 8:03
 */

class FilesDBManager extends FilesManager
{
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
        $db = Yii::app()->db;
        $files_table = Files::tableName();
        $sql = "SELECT * FROM {$files_table} WHERE folders_id=:PID";
        $command=$db->createCommand($sql);
        $command->bindParam(":PID", $folder);
        $rows = $command->queryAll();
        return $rows;
    }

    public function getRealFileName($fname)
    {
        $db = Yii::app()->db;
        $files_table = Files::tableName();
        $sql = "SELECT path FROM {$files_table} WHERE name=:FNAME";
        $command=$db->createCommand($sql);
        $command->bindParam(":FNAME", $fname);
        $rows = $command->queryScalar();
        return $rows;
    }

    public function getRealFileNameById($id)
    {
        $db = Yii::app()->db;
        $files_table = Files::tableName();
        $sql = "SELECT path FROM {$files_table} WHERE id=:ID";
        $command=$db->createCommand($sql);
        $command->bindParam(":ID", $id);
        $rows = $command->queryScalar();
        return $rows;
    }

    public function saveFiledata($data)
    {
        $db = Yii::app()->db;
        $files_table = Files::tableName();
        $sql="REPLACE INTO ".$files_table."(folders_id, name, path) VALUES(:folders_id, :name, :path)";
        $command=$db->createCommand($sql);
        $command->bindParam(":folders_id",$data['folders_id'],PDO::PARAM_STR);
        $command->bindParam(":name",$data['name'],PDO::PARAM_STR);
        $command->bindParam(":path",$data['path'],PDO::PARAM_STR);
        $command->execute();
    }

    public function delFileById($id)
    {
        $db = Yii::app()->db;
        $files_table = Files::tableName();
        $command = $db->createCommand()
                ->delete($files_table, 'id=:id', array(':id'=>$id));
    }

    public function search($string) {
        $db = Yii::app()->db;
        $files_table = Files::tableName();
        $sql = "SELECT * FROM {$files_table} WHERE name LIKE :NAME";
        $command=$db->createCommand($sql);
        $command->bindParam(":NAME", '%'.$string.'%');
        $rows = $command->queryAll();
        return $rows;
    }
} 