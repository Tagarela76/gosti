<?php
/* @var $this AdminUsersController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Admin Users',
);

$this->menu=array(
	array('label'=>'Create AdminUsers', 'url'=>array('create')),
	array('label'=>'Manage AdminUsers', 'url'=>array('admin')),
);
?>

<h1>Admin Users</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
