<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 01.10.13
 * Time: 12:19
 */

class FolderDBManager extends FolderManager{
    public function getFolderById($id) {
        $db = Yii::app()->db;
        $folder_table = Folder::tableName();

        $sql = "SELECT * FROM {$folder_table} WHERE id=:ID LIMIT 1";
        $command=$db->createCommand($sql);
        $command->bindParam(":ID", $id);
        $row = $command->queryRow();

        $folder = new Folder;
        $folder->id = $row['id'];
        $folder->parent_id = $row['parent_id'];
        $folder->name = $row['name'];

        return $folder;
    }


    public function getFoldersTreeArray($parent = 1) {
        $db = Yii::app()->db;
        $folder_table = Folder::tableName();
        $sql = "SELECT * FROM {$folder_table} WHERE parent_id=:PID AND id > 1 ORDER BY name";
        $command=$db->createCommand($sql);
        $command->bindParam(":PID", $parent);
        $rows = $command->queryAll();
        if (is_array($rows)) {
            $max_i = count($rows);
            for ($i=0;$i<$max_i;$i++) {
                $items = $this->getFoldersTreeArray($rows[$i]['id']);
                if (!empty($items)) {
                    $rows[$i]['items'] = $items;
                }
            }
        }
        return $rows;
    }

    public function addFolder($data)
    {
        $db = Yii::app()->db;
        $folder_table = Folder::tableName();
        $command = $db->createCommand()
            ->insert($folder_table,array(
                'parent_id' => $data['parentid'],
                'name' =>$data['name'],
            ));
    }

    public function editFolder($data)
    {
        $db = Yii::app()->db;
        $folder_table = Folder::tableName();
        $command = $db->createCommand()
            ->update($folder_table,array(
                'name' =>$data['name'],
            ),'id=:id',array(':id'=>$data['id']));
    }

    public function delFolderById($id)
    {
        $db = Yii::app()->db;
        $folder_table = Folder::tableName();
        $command = $db->createCommand()
            ->delete($folder_table, 'id=:id', array(':id'=>$id));
    }

    public function getList($id)
    {
        $folder_table = Folder::tableName();
        $files_table = Files::tableName();
        $db = Yii::app()->db;

        $sql = "SELECT id, name, 'folder' as type FROM {$folder_table} fold WHERE parent_id=:PID AND id > 1
        UNION
        SELECT id, name, 'file' as type FROM {$files_table} file WHERE folders_id=:PID ORDER BY name";
        $command=$db->createCommand($sql);
        $command->bindParam(":PID", $id);
        $rows = $command->queryAll();
        return $rows;
    }
    
    /**
     * 
     * get folder by name
     * 
     * @param string $name
     * 
     * @return boolean|Folder
     */
    public function getFolderByName($name)
    {
        $folder_table = Folder::tableName();
        $db = Yii::app()->db;
        
        $sql = "SELECT * FROM {$folder_table} WHERE name=:NAME";
        $command = $db->createCommand($sql);
        $command->bindParam(":NAME", $name);
        $rows = $command->queryAll();
        if(empty($rows)){
            return false;
        }
        
        return $rows[0];
    }
}