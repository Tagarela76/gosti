<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 02.10.13
 * Time: 6:26
 */

class PriceController extends Controller
{
    public function actionIndex()
    {
        $this->render('index');
    }
    public function accessRules()
    {
        return array(
            array('deny',
                'actions'=>array('index', 'edit'),
                'users'=>array('?'),
            ),
        );
    }

}