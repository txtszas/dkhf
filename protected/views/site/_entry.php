<div class="entry entry-<?php echo $k ?>"
	id="page-<?php echo $page->id; ?>" data-id="<?php echo $page->id ?>">
	<div class="collapsed">
		<label class="checkbox"> <input type="checkbox">
		</label>
		<div class="entry-date"><?php echo date('Y-m-d H:i:s',$page->postdate)?></div>
		<div class="entry-main">
			<a class="entry-original" target="_blank"
				href="<?php echo $page->link ?>">原</a> <span
				class="entry-source-title"><?php echo $page->source->name?></span>
			<div class="entry-secondary">
				<h2 class="entry-title"><?php echo $page->title ?></h2>
			</div>
		</div>
	</div>
</div>