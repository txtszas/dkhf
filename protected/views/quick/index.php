<script type="text/javascript" src="/ueditor/editor_config_fetch.js"></script>
<script type="text/javascript" src="/ueditor/editor_all.js"></script>
<style>
.container{
	margin:10px auto;
}
</style>
<div class="quick-add">
	<div class="navbar" style="margin-bottom: 0">
       	<div class="navbar-inner">
       		<a class="brand" href="/">管理平台</a>
            <form class="navbar-form pull-left" action="<?php echo $this->createUrl('Quick/Fetch')?>" method="post">
               <input type="text" class="span4" name="url">
               <button type="submit" class="btn">快速发布</button>
            </form>
        </div>
    </div>
</div>
<?php if (isset($msg)) {
?>
<div class="message">
	<?php echo $msg?>
</div>
<?php 	
}else{
?>
<div class="container">
	<form action="<?php echo $this->createUrl('api/publish');?>" method="post" >
	<div>
		<input type="text" class="input-block-level" value="<?php echo $page['title']?>" name="title">
	</div>
	<div>
		<textarea name="content" id="editor">
<?php
echo $page['content'];
?>
		</textarea>
	</div>
	<span>版块：</span>
	<select name="term_id">
		<option value="3">新谈资</option>
		<option value="4">好段子</option>
		<option value="5">热视频</option>
		<option value="24">深阅读</option>
	</select>
	
	<span>作者：</span>
	<select name="author_id">
<?php foreach ($authorList as $k => $v){?>
		<option value="<?php echo $k ?>"><?php echo $v?></option>
<?php }?>
	</select>
	<span>顶：</span>
	<input type="text" name="ding" value="0" class="ding">
	<span>踩：</span>
	<input type="text" name="cai" value="0" class="ding">
	<div class="form-actions">
  		<button type="submit" class="btn btn-primary">发布</button>
	</div>
	
	</form>
</div>	
<script type="text/javascript">
UE.getEditor('editor')
var editor = new UE.ui.Editor();
</script>

<?php 
}
?>


