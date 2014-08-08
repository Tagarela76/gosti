<?php

class DefaultController extends Controller
{
	public function actionIndex()
	{
        Yii::app()->clientScript->registerCssFile('css/kendo.common.min.css');
        Yii::app()->clientScript->registerCssFile('css/kendo.bootstrap.min.css');
        Yii::app()->clientScript->registerCorescript('jquery');
        Yii::app()->clientScript->registerScriptFile('js/kendo.web.min.js');
		$this->render('index');
	}

    public function actiongetfiles()
    {
        $folders = array(
        );
        $FolderId = Yii::app()->request->getParam('id',0);
        $folders = FilesManager::factory()->getFilesByFolder($FolderId);
        echo CJSON::encode($folders);
        Yii::app()->end();
    }

    public function actiondelfiles()
    {
        $id = Yii::app()->request->getParam('id',false);
        if (!$id) {
            exit;
        }
        $ids = explode('__',$id);
        foreach ($ids as $val) {
            FilesManager::factory()->delFileById($val);
        }
        exit;
        /*
        $folders = array(
        );
        $FolderId = Yii::app()->request->getParam('id',0);
        $folders = FilesManager::factory()->getFilesByFolder($FolderId);
        echo CJSON::encode($folders);
        Yii::app()->end();
        */
    }

    public function actiondelfolder()
    {
        $id = Yii::app()->request->getParam('id',false);
        if (!$id) {
            exit;
        }
        FolderManager::factory()->delFolderById($id);
        exit;
    }

    public function actionGetTree()
    {
        $arr = FolderManager::factory()->getFoldersTreeArray();
        echo CJSON::encode($arr);
        exit;
    }

    public function actionAddFolder()
    {
        $data = array(
            'parentid'=>Yii::app()->request->getParam('parentid',1),
            'name'=>Yii::app()->request->getParam('FolderName','noname'),
        );
        $arr = FolderManager::factory()->addFolder($data);
    }

    public function actionEditFolder()
    {
        $data = array(
            'id'=>Yii::app()->request->getParam('id',0),
            'name'=>Yii::app()->request->getParam('FolderName','noname'),
        );
        $arr = FolderManager::factory()->editFolder($data);
    }

    public function accessRules()
    {
        return array(
            array('allow', // позволим аутентифицированным пользователям выполнять любые действия
                'users'=>array('@'),
            ),
            array('deny',  // остальным запретим всё
                'users'=>array('*'),
            ),
        );
    }
}