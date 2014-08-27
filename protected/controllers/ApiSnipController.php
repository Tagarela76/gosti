<?php

class ApiSnipController extends Controller
{

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array();
    }

    /**
     * get Folder By Folder Name
     */
    public function actionGetFolderByFolderName()
    {
        $folderName = Yii::app()->request->getParam('folderName', '');
        $folder = FolderManager::factory()->getFolderByName($folderName);
        if($folder){
            echo CJSON::encode($folder);
        }else{
            echo CJSON::encode('not found');
        }
    }
    
    
    public function actionGetFoldersStructureByFolderId()
    {
        $folderId = Yii::app()->request->getParam('folderId', '');
        $folders = FolderManager::factory()->getList($folderId);
        header('Content-type: application/json');
        echo CJSON::encode($folders);
    }
    
    public function actionGetFolderTreeArray()
    {
        $folderId = Yii::app()->request->getParam('folderId', '');
        $folders = FolderManager::factory()->getFoldersTreeArray($folderId);
        header('Content-type: application/json');
        echo CJSON::encode($folders);
    }
    
    public function actionGetFileListByFolderId()
    {
        $folderId = Yii::app()->request->getParam('folderId', '');
        $ext = Yii::app()->request->getParam('ext', null);
        $fileList = FilesManager::factory()->getFilesByFolderAndExt($folderId, $ext);
        echo CJSON::encode($fileList);
        Yii::app()->end();
    }
}
