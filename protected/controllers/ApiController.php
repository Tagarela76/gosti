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
        $userId = Yii::app()->request->getParam('userId', '');
        $key = Yii::app()->request->getParam('key', '');

        // echo $userId;
        // echo $key;
        if (!UsersManager::factory()->checkkey($userId, $key)) {
            return;
        }
        $id = Yii::app()->request->getParam('id', '1');
        $folders = FolderManager::factory()->getList($id);

        header('Content-type: application/json');
        echo CJSON::encode($folders);
        exit;
    }

    public function actionFile()
    {
        $userId = Yii::app()->request->getParam('userId', '');
        $key = Yii::app()->request->getParam('key', '');
        if (!UsersManager::factory()->checkkey($userId, $key)) {
            //return;
        }
        $name = Yii::app()->request->getParam('name', '');
        $file = '';
        if ($name != '') {
            $file = FilesManager::factory()->getRealFileName($name);
        } else {
            $id = Yii::app()->request->getParam('id', 0);

            if ($id != 0) {
                $file = FilesManager::factory()->getRealFileNameById($id);
            }
        }
        if ($file != '') {
            $this->file_force_download(dirname(Yii::app()->basePath) . $file, $userId);
        }
    }

    public function actionFind()
    {
        $userId = Yii::app()->request->getParam('userId', '');
        $key = Yii::app()->request->getParam('key', '');
        if (!UsersManager::factory()->checkkey($userId, $key)) {
            return;
        }
        $searchString = Yii::app()->request->getParam('name', '');
        header('Content-type: application/json');
        $result = FilesManager::factory()->search($searchString);
        echo CJSON::encode($result);
        exit;
    }

    private function file_force_download($file, $userId = null)
    {
        $fname = $file;
        $response = array(
            'error' => false,
            'errorMsg' => ''
        );

        //check for user id
        if (is_null($userId)) {
            echo 'user with id ' . $userId . ' not found';
            die();
        }
        $user = UsersManager::factory()->getUserByEmail($userId);
        //check user download count and expiration date for grtting if user can download file
        //Get today's date
        $timeToday = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        //get user expiration date
        $expirationDate = $user->expirationDate;
        //get user free downloads number
        $downloadsNumber = $user->downloadsNumber;

        if ($timeToday > $expirationDate) {
            if ($downloadsNumber <= 0) {
                //user can't download file
                $response['error'] = true;
                $response['errorMsg'] = 'License and download limit is over. You can\'t download the file.';
                $response['downloadsNumber'] = $downloadsNumber;
            } else {
                //decrease user downloads Number
                $downloadsNumber--;
                $user->downloadsNumber = $downloadsNumber;
                $result = $user->save();
                if ($result) {
                    $response['downloadsNumber'] = $downloadsNumber;
                } else {
                    $response['error'] = true;
                    $response['errorMsg'] = 'Update Error number of downloads';
                }
            }
        }
        //return responce if error
        if ($response['error']) {
            header("HTTP/1.0 500");
            echo 'limit is over';
            Yii::app()->end();
            die();
        }

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

            header('Content-Disposition: attachment; filename*="utf-8\'ru-ru\'' . str_replace(dirname($file) . '/', '', $fname) . '"');
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
        } else {
            echo 'no file - ' . $file;
            exit;
        }
    }

    public function actionMail()
    {
        $userId = Yii::app()->request->getParam('userId', '');
        $key = Yii::app()->request->getParam('key', '');
        if (!UsersManager::factory()->checkkey($userId, $key)) {
            return;
        }
        $from = Yii::app()->request->getParam('from', '');
        $subj = Yii::app()->request->getParam('subj', '');
        $mess = Yii::app()->request->getParam('mess', '');
        Yii::app()->params['rootDir'];
        exit;
    }

    public function actionlogin()
    {
        $login = Yii::app()->request->getParam('login', '');
        $pass = Yii::app()->request->getParam('pass', '');
        header('Content-type: application/json');

        echo CJSON::encode(UsersManager::factory()->getkey($login, $pass));
        Yii::app()->end();
    }

    public function actionActivation()
    {
        $cod = Yii::app()->request->getParam('cod', '');
        $email = Yii::app()->request->getParam('user', '');
        $user = UsersManager::factory()->getUserByEmail($email);

        if (!empty($user)) {
            $check = md5($user->id) . md5($user->email);
            if ($check == $cod) {
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
        $mail = Yii::app()->request->getParam('email', '');
        $pass = UsersManager::factory()->getPass($mail);
        if ($pass) {
            $email = Yii::app()->email;
            $email->to = $mail;
            $email->subject = 'forgotten password';
            $email->viewVars = array('pass' => $pass);
            $email->view = 'forgotten-pass';
            $email->send();
            header('Content-type: application/json');
            echo CJSON::encode(array('mess' => 'password sended.'));
            Yii::app()->end();
        } else {
            header('Content-type: application/json');
            echo CJSON::encode(array('error' => 'user not found!'));
            Yii::app()->end();
        }
    }

    /**
     * signup user
     */
    public function actionSignup()
    {
        $mail = Yii::app()->request->getParam('email', '');
        $pass = Yii::app()->request->getParam('pass', '');
        $validator = new CEmailValidator;
        if (empty($mail) or (!$validator->validateValue($mail))) {
            header('Content-type: application/json');
            echo CJSON::encode(array('error' => 'email validation error'));
            Yii::app()->end();
        }
        if (empty($pass)) {
            header('Content-type: application/json');
            echo CJSON::encode(array('error' => 'no pass'));
            Yii::app()->end();
        }
        $is_exists = UsersManager::factory()->getUserByEmail($mail);
        if ($is_exists) {
            header('Content-type: application/json');
            echo CJSON::encode(array('error' => 'user exists'));
            Yii::app()->end();
        }
        //create expiration date
        //get current date
        $expDate = mktime(0, 0, 0, date("m"), date("d") - 1, date("Y"));

        $user = new Users();
        $user->email = $mail;
        $user->pass = $pass;
        $user->expirationDate = $expDate;
        $user->downloadsNumber = Users::DOWNLOADS_NUMBER;
        $user->save();
        $activation = md5($user->id) . md5($mail);
        $url = Yii::app()->createAbsoluteUrl('api/activation', array('cod' => $activation, 'user' => $mail));
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
        echo CJSON::encode(
                array(
                    'userId' => $user->id,
                    'downloads_number' => $user->downloadsNumber
        ));
        Yii::app()->end();
    }

    public function actionAddExpirationDate()
    {
        $userEmail = Yii::app()->request->getParam('userId', '');
        $key = Yii::app()->request->getParam('key', '');
        $expirationDays = Yii::app()->request->getParam('expirationDays', '');
        //check user by key $userId = mail;
        if (!UsersManager::factory()->checkkey($userEmail, $key)) {
            return;
        }
        $user = UsersManager::factory()->getUserByEmail($userEmail);
        $expirationDate = $user->expirationDate;
        $timeToday = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        if ($timeToday > $expirationDate) {
            //get new experation date from today
            $newExpirationDate = mktime(0, 0, 0, date("m"), date("d") + $expirationDays, date("Y"));
        } else {
            $newExpirationDate = strtotime('+' . $expirationDays . ' day', $expirationDate);
        }

        $user->expirationDate = $newExpirationDate;
        $result = $user->save();
        //Check the user has successfully saved
        $response = array();
        if ($result) {
            $response['error'] = false;
            $response['newExpirationDate'] = $user->expirationDate;
        } else {
            $response['error'] = true;
            $response['errorMsg'] = 'Error saving license. The license is not saved. Please try again later';
        }
        header('Content-type: application/json');
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    //create new methods for paid iso version

    /**
     * get folder list
     * 
     */
    public function actionListPaid()
    {
        $id = Yii::app()->request->getParam('id', '1');
        $folders = FolderManager::factory()->getList($id);
        header('Content-type: application/json');
        echo CJSON::encode($folders);
        exit;
    }

    /**
     * find file
     */
    public function actionFindPaid()
    {
        $searchString = Yii::app()->request->getParam('name', '');
        header('Content-type: application/json');
        $result = FilesManager::factory()->search($searchString);
        echo CJSON::encode($result);
        exit;
    }

    /**
     * download file
     * 
     * @return json
     */
    public function actionFilePaid()
    {
        $name = Yii::app()->request->getParam('name', '');
        $file = '';
        if ($name != '') {
            $file = FilesManager::factory()->getRealFileName($name);
        } else {
            $id = Yii::app()->request->getParam('id', 0);

            if ($id != 0) {
                $file = FilesManager::factory()->getRealFileNameById($id);
            }
        }
        if ($file != '') {
            $this->file_force_download_paid(dirname(Yii::app()->basePath) . $file);
        }
    }
    //load file download
    /**
     * 
     * @param type $file
     * @param type $userId
     */
    private function file_force_download_paid($file)
    {
        $fname = $file;
        $response = array(
            'error' => false,
            'errorMsg' => ''
        );

        $file = mb_convert_encoding($file, 'Windows-1251', 'UTF-8');
        if (file_exists($file)) {
            if (ob_get_level()) {
                ob_end_clean();
            }
            // save window show
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');

            header('Content-Disposition: attachment; filename*="utf-8\'ru-ru\'' . str_replace(dirname($file) . '/', '', $fname) . '"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            // read file and send it to user
            if ($fd = fopen($file, 'rb')) {
                while (!feof($fd)) {
                    print fread($fd, 1024);
                }
                fclose($fd);
            }
        } else {
            echo 'no file - ' . $file;
            exit;
        }
    }

}
