<h1>设置</h1><a href="/"> <<返回首页</a>
<ul class="nav nav-tabs">
  <li class="active">
    <a href="#">内容过滤规则设置</a>
  </li>
</ul>
<a href="<?php echo $this->createUrl('Filter/Add')?>" class="btn">添加规则</a>
<table class="table table-striped">
	<thead>
		<tr>
			<th>来源</th>
			<th>规则</th>
			<th>替换</th>
			<th>操作</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($filters as $f) {?>
		<tr>
			<th><?php echo $f->source->name?></th>
			<th><?php echo $f->rule?></th>
			<th><?php echo $f->replace?></th>
			<th><a href="<?php echo $this->createUrl('Filter/edit', array('id' => $f->id))?>"><i class="icon-edit"></i>编辑</a>
			<a href="<?php echo $this->createUrl('Filter/del', array('id' => $f->id)) ?>"><i class="icon-remove"></i>删除</a></th>
		</tr>
	<?php }?>
	</tbody>
</table>
