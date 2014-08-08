<?php
/* @var $this AdminUsersController */
/* @var $model AdminUsers */

$this->breadcrumbs=array(
	'Admin Users'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List AdminUsers', 'url'=>array('index')),
	array('label'=>'Create AdminUsers', 'url'=>array('create')),
	array('label'=>'View AdminUsers', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage AdminUsers', 'url'=>array('admin')),
);
?>

<h1>Update AdminUsers <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>