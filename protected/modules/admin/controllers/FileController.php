<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 02.10.13
 * Time: 9:28
 */

class FileController extends Controller
{
    public function actionSave()
    {
        //echo '<pre>'.$_FILES.'</pre>';
        $files = CUploadedFile::getInstancesByName('files');
        foreach ($files as $file) {
            $data = array(
                'folders_id' => Yii::app()->request->getParam('id',0),
                'name' => $file->getName(),
                'path' => '/upload/'.$file->getName(),
            );
            $folders = FilesManager::factory()->saveFiledata($data);
            $file->saveAs(dirname(Yii::app()->basePath) . '/upload/'.iconv( "UTF-8","windows-1251", $file->getName()));
        //echo  Yii::app()->basePath . '/../upload/'.$file->getName();
        }
        $this->redirect(Yii::app()->baseUrl.'/admin');
    }
} 