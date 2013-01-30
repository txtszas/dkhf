<div class="container">
<h1>添加规则</h1>
<div class="form">
	<?php $form = $this->beginWidget('CActiveForm');?>
	<div class="clearfix">
		<label>选择源：</label>
		<div class="input">
			<?php echo CHtml::dropDownList('Filter[sid]', $model->sid, CHtml::listData($sources, 'id', 'name'))?>
			<?php echo $form->error($model, 'name', array('class' => 'help-inline'));?>
		</div>
	</div>
	
	<div class="clearfix">
		<label>选择规则：</label>
		<div class="input">
			<?php echo $form->textField($model, 'rule', array('class' => 'input-xlarge'));?>
			<?php echo $form->error($model, 'rule', array('class' => 'help-inline'));?>
		</div>
	</div>
	
	
	<div class="clearfix">
		<label>替换规则</label>
		<div class="input">
			<?php echo $form->textField($model, 'replace', array('class' => 'input-xlarge'));?>
			<?php echo $form->error($model, 'replace', array('class' => 'help-inline'));?>
		</div>
	</div>
	
	<div class="form-actions">
  		<button type="submit" class="btn btn-primary">保存</button>
  		<a href="<?php echo $this->createUrl('filter/index')?>" class="btn">返回</a>
	</div>
	<?php $this->endWidget();?>
</div>
</div>