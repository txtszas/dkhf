<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle=Yii::app()->name . ' - Login';
$this->breadcrumbs=array(
	'Login',
);
?>
<style>
.loginbox{
	width:250px;
	margin:0 auto;
	background:#fff;
	padding:30px;
	box-shadow: 0px 0px 3px #ccc;
}
body{
	background:#DEDED9;
}
</style>
<div class="loginbox">

<h2>大咖汇后台登陆</h2>


<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'login-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); ?>


	<div>
		<?php echo $form->labelEx($model,'username'); ?>
		<?php echo $form->textField($model,'username',array('class'=>'input-block-level')); ?>
		<?php echo $form->error($model,'username'); ?>
	</div>

	<div >
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password',array('class'=>'input-block-level')); ?>
		<?php echo $form->error($model,'password'); ?>
	</div>

	<div class="rememberMe">
		<?php echo $form->checkBox($model,'rememberMe'); ?>
		<?php echo $form->label($model,'rememberMe'); ?>
		<?php echo $form->error($model,'rememberMe'); ?>
	</div>

	<div >
		<input type="submit" value="登录" class="btn btn-block">
	</div>

<?php $this->endWidget(); ?>
</div>
