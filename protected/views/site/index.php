<script type="text/javascript" src="ueditor/editor_config.js"></script>
<script type="text/javascript" src="ueditor/editor_all.js"></script>
<div class="message-area-container" id="loading-area-container">
</div>
<div id="nav">
	<div id="logo-section">
		<a href="#">管理平台</a>
	</div>
	
	<div id="add-source-section">
		<div class="btn-group" id="add-source-button">
			<a class="btn btn-success dropdown-toggle" data-toggle="dropdown" href="#" >
		    	添加源
		    	<span class="caret"></span>
		  	</a>
		  	<ul class="dropdown-menu">
		  		<li><a href="<?php echo $this->createUrl('Source/add', array('type' => Source::TYPE_RSS)) ?>">添加订阅源</a></li>
		  		<li><a href="<?php echo $this->createUrl('Source/add', array('type' => Source::TYPE_WEIBO)) ?>">添加微博源</a></li>
		  		<li><a href="<?php echo $this->createUrl('Source/add', array('type' => Source::TYPE_TIEBA)) ?>">添加贴吧源</a></li>
		  		<li><a href="<?php echo $this->createUrl('Source/add', array('type' => Source::TYPE_WEIBOTOP)) ?>">添加热门微博源</a></li>
		  	</ul>
		</div>
	</div>

	<div id="scrollable-sections-holder">
		<div class="item selected">
			<a href="#all-source">所有源
			<?php 
			if ($unReadNum > 0){ ?>
				<span class="unread-num">(<em><?php echo $unReadNum?></em>)</span>					
			<?php }?>	
			</a>
		</div>
	

	<ul id="source-list">
<?php foreach ($sources as $source) {?>
		<li class="source-<?php echo $source->id?>">
			<a href="#s-<?php echo $source->id?>"><?php echo $source->name?>
		<?php $unReadNum = $source->unReadNum();
			if ($unReadNum > 0){		
		?>
			<span class="unread-num">(<em><?php echo $unReadNum?></em>)</span>
		<?php }?>
			</a>
		</li>
<?php }?>
	</ul>

	</div>


</div>

<div id="chrome">
	<div id="chrome-viewer-container">
		<div id="viewer-header-container">
			<div id="viewer-header">
				<div id="viewer-top-controls-container">

					<button class="btn"><i class="icon-repeat"></i></button>

					<div class="btn-group" data-toggle="buttons-radio">
<?php 
$unReadStyle = '';
$allReadStyle = '';
if ($isShowUnread) {
	$unReadStyle = 'active';
}else{
	$allReadStyle = 'active';
}
?>
					  	<button type="button" class="btn allread-button <?php echo $allReadStyle?>">全部</button>
					  	<button type="button" class="btn unread-button <?php echo $unReadStyle?>" >未读</button>
					</div>
	
					
					<div class="btn-group">
					  	<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
					    	操作
					    	<span class="caret"></span>
					  	</a>
					  	<ul class="dropdown-menu">
					  		<li><a href="javascript:;" id="markChosenReaded">已读</a></li>
					  		<li class="dropdown-submenu">
				   				<a tabindex="-1" href="#">发布到</a>
				    			<ul class="dropdown-menu" id="post-to">
				    				<li><a href="javascript:;" data-id="3">新谈资</a></li>
				    				<li><a href="javascript:;" data-id="4">好段子</a></li>
				    				<li><a href="javascript:;" data-id="5">热视频</a></li>
				    				<li><a href="javascript:;" data-id="24">深阅读</a></li>
				    			</ul>
				  			</li>
					  	</ul>
					</div>
					<div class="btn-group">
					  	<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
					    	<i class="icon-cog"></i>设置
					    	<span class="caret"></span>
					  	</a>
					  	<ul class="dropdown-menu">
					  		<li><a href="<?php echo $this->createUrl('Filter/index')?>" >内容过滤设置</a></li>
					  	</ul>
					</div>
				</div>
				<div class="status">
					今日抓取：<?php echo Page::todayFetchNum()?>
					今日已发布：<?php echo Log::todayPublish()?>
				</div>
			</div>

		</div>

		<div id="viewer-container">
			<div id="viewer-entries-container" style="height:503px;">
				<div id="title-and-status-holder">
					<span id="chrome-title">所有源</span>
				</div>

				<div id="entries" class="list">
						加载中..
				</div>
			</div>
		</div>
	</div>
</div>
<div class="author-list-select" style="display: none">
	<select name="author_id">
<?php foreach ($authorList as $k => $v){?>
	<option value="<?php echo $k ?>"><?php echo $v?></option>
<?php }?>
	</select>
</div>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/index.js?0005"></script>
