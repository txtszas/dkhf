<h1>添加贴吧源</h1>
<div class="form">
	<?php $form = $this->beginWidget('CActiveForm');?>
	<div class="clearfix">
		<label>源名称：</label>
		<div class="input">
			<?php echo $form->textField($model, 'name');?>
			<?php echo $form->error($model, 'name', array('class' => 'help-inline'));?>
		</div>
	</div>
	
	<div class="clearfix">
		<label>贴吧地址：</label>
		<div class="input">
			<?php echo $form->textField($model, 'list_url', array('class' => 'input-xlarge'));?>
			<?php echo $form->error($model, 'list_url', array('class' => 'help-inline'));?>
		</div>
	</div>
	<input type="hidden" name="Source[is_page_fetch_by_normal]" value="0" />
	<input type="hidden" name="Source[content_selector]" value="0" />
	<input type="hidden" name="Source[type]" value="<?php echo $type ?>" />
	<div class="form-actions">
  		<button type="submit" class="btn btn-primary">添加</button>
  		<button type="button" class="btn">取消</button>
	</div>
	<?php $this->endWidget();?>
</div>