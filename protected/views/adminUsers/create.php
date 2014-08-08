<?php
/* @var $this AdminUsersController */
/* @var $model AdminUsers */

$this->breadcrumbs=array(
	'Admin Users'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List AdminUsers', 'url'=>array('index')),
	array('label'=>'Manage AdminUsers', 'url'=>array('admin')),
);
?>

<h1>Create AdminUsers</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>