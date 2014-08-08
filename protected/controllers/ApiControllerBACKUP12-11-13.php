<?php
class ApiController extends Controller
{
    // Members
    /**
     * Key which has to be in HTTP USERNAME and PASSWORD headers
     */
    Const APPLICATION_ID = 'ASCCPE';

    /**
     * Default response format
     * either 'json' or 'xml'
     */
    private $format = 'json';

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array();
    }

    // Actions
    public function actionList()
    {
        $userId = Yii::app()->request->getParam('userId','');
        $key = Yii::app()->request->getParam('key','');
        if (!UsersManager::factory()->checkkey($userId,$key))
        {
            return;
        }
        $id = Yii::app()->request->getParam('id','1');
        $folders = FolderManager::factory()->getList($id);
        header('Content-type: application/json');
        echo CJSON::encode($folders);
        exit;
    }

    public function actionFile()
    {
        $userId = Yii::app()->request->getParam('userId','');
        $key = Yii::app()->request->getParam('key','');
        if (!UsersManager::factory()->checkkey($userId,$key))
        {
            return;
        }
        $name = Yii::app()->request->getParam('name','');
        $file = '';
        if ($name!='') {
            $file = FilesManager::factory()->getRealFileName($name);
        } else {
            $id = Yii::app()->request->getParam('id',0);

            if ($id!=0) {
                $file = FilesManager::factory()->getRealFileNameById($id);
            }
        }
        if ($file!='') {
            $this->file_force_download(dirname(Yii::app()->basePath).$file);
        }
    }

    public function actionFind()
    {
        $searchString = Yii::app()->request->getParam('name','');
        header('Content-type: application/json');
        $result = FilesManager::factory()->search($searchString);
        echo CJSON::encode($result);
        exit;
    }

    private function file_force_download($file) {
        $fname = $file;
        $file = mb_convert_encoding($file, 'Windows-1251', 'UTF-8');
        if (file_exists($file)) {
            // сбрасываем буфер вывода PHP, чтобы избежать переполнения памяти выделенной под скрипт
            // если этого не сделать файл будет читаться в память полностью!
            if (ob_get_level()) {
                ob_end_clean();
            }
            // заставляем браузер показать окно сохранения файла
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');

            header('Content-Disposition: attachment; filename*="utf-8\'ru-ru\''.str_replace(dirname($file).'/','',$fname).'"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            // читаем файл и отправляем его пользователю
            if ($fd = fopen($file, 'rb')) {
                while (!feof($fd)) {
                    print fread($fd, 1024);
                }
                fclose($fd);
            }
            exit;
        } else {
            echo 'no file - '.$file;
            exit;
        }
    }


    public function actionMail()
    {
        $userId = Yii::app()->request->getParam('userId','');
        $key = Yii::app()->request->getParam('key','');
        if (!UsersManager::factory()->checkkey($userId,$key))
        {
            return;
        }
        $from = Yii::app()->request->getParam('from','');
        $subj = Yii::app()->request->getParam('subj','');
        $mess = Yii::app()->request->getParam('mess','');
        Yii::app()->params['rootDir'];
        exit;
    }


    public function actionlogin()
    {
        $login = Yii::app()->request->getParam('login','');
        $pass = Yii::app()->request->getParam('pass','');
        header('Content-type: application/json');
        echo CJSON::encode(UsersManager::factory()->getkey($login,$pass));
        Yii::app()->end();
    }

    public function actionActivation()
    {
        $cod = Yii::app()->request->getParam('cod','');
        $email = Yii::app()->request->getParam('user','');
        $user = UsersManager::factory()->getUserByEmail($email);
        if (!empty($user))
        {
            $check = md5($user->id).md5($user->email);
            if ($check==$cod) {
                $user->status = 1;
                $user->save();
                $email = Yii::app()->email;
                $email->to = $user->email;
                $email->subject = 'Email verification';
                $email->viewVars = array();
                $email->view = 'you-are-verified';
                $email->send();
            }
        }
        Yii::app()->end();
    }

    public function actionForgot()
    {
        $mail = Yii::app()->request->getParam('email','');
        $pass = UsersManager::factory()->getPass($mail);
        if ($pass) {
            $email = Yii::app()->email;
            $email->to = $mail;
            $email->subject = 'forgotten password';
            $email->viewVars = array('pass'=>$pass);
            $email->view = 'forgotten-pass';
            $email->send();
            header('Content-type: application/json');
            echo CJSON::encode(array('mess'=>'password sended.'));
            Yii::app()->end();
        } else {
            header('Content-type: application/json');
            echo CJSON::encode(array('error'=>'user not found!'));
            Yii::app()->end();
        }

    }

    public function actionSignup()
    {
        $mail = Yii::app()->request->getParam('email','');
        $pass = Yii::app()->request->getParam('pass','');
        $validator = new CEmailValidator;
        if (empty($mail) or (!$validator->validateValue($mail))) {
            header('Content-type: application/json');
            echo CJSON::encode(array('error'=>'email validation error'));
            Yii::app()->end();
        }
        if (empty($pass)){
            header('Content-type: application/json');
            echo CJSON::encode(array('error'=>'no pass'));
            Yii::app()->end();
        }
        $is_exists = UsersManager::factory()->getUserByEmail($mail);
        if ($is_exists) {
            header('Content-type: application/json');
            echo CJSON::encode(array('error'=>'user exists'));
            Yii::app()->end();
        }
        $user = new Users();
        $user->email = $mail;
        $user->pass = $pass;
        $user->save();
        $activation = md5($user->id).md5($mail);
        $url = Yii::app()->createAbsoluteUrl('api/activation',array('cod'=>$activation,'user'=>$mail));
        $email = Yii::app()->email;
        $email->to = $mail;
        $email->subject = 'Email verification';
        $email->viewVars = array(
            'url' => $url,
        );
        $email->view = 'email-verification';
        $email->send();

        //echo $user->id.'||'.$url;
        header('Content-type: application/json');
        echo CJSON::encode(array('userId'=>$user->id));
        Yii::app()->end();
    }


}