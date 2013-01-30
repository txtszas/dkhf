<h1>添加订阅源</h1>
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
		<label>rss订阅地址：</label>
		<div class="input">
			<?php echo $form->textField($model, 'list_url', array('class' => 'input-xlarge'));?>
			<?php echo $form->error($model, 'list_url', array('class' => 'help-inline'));?>
		</div>
	</div>
	
	<div class="clearfix">
		<label class="checkbox"><?php echo $form->checkBox($model,'is_page_fetch_by_normal'); ?>是否需要抓取内容页来获取内容（一般不需要）</label>
	</div>
	
	<div class="clearfix">
		<label>内容选择类(如果上面勾取了，这个必填)</label>
		<div class="input">
			<?php echo $form->textField($model, 'content_selector');?>
		</div>
	</div>
	
	<input type="hidden" name="Source[type]" value="<?php echo $type ?>" />
	<div class="form-actions">
  		<button type="submit" class="btn btn-primary">添加</button>
  		<button type="button" class="btn">取消</button>
	</div>
	<?php $this->endWidget();?>
</div>