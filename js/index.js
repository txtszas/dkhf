//列表内容展开js

!function($){
	"use strict";
	
	var ViewDail = function (sid) {
		this.sid = sid
		this.entryTitleDom = $('.entry .entry-secondary')
		this.checkbox = $('.entry .checkbox-input')
		this.markChosenReaded = $('#markChosenReaded')
		this.quickPost = $('#post-to li a')
		this.editor = new UE.ui.Editor()
		this.checkIds = []
		this.listen()
	}
	
	ViewDail.prototype = {
			
		constructor: ViewDail
		
		, listen:function(){
			this.entryTitleDom
				.unbind()
			this.checkbox
				.unbind()
			this.markChosenReaded
				.unbind()
			this.quickPost
				.unbind()
				
				
			this.entryTitleDom
				.on('click',	$.proxy(this.click, this))
			this.checkbox
				.on('change',	$.proxy(this.check, this))
			this.markChosenReaded
				.on('click',	$.proxy(this.markAllRead, this))
			this.quickPost
				.on('click',	$.proxy(this.postAll, this))
				
		}
		, click: function(e){
			this.entry = $(e.currentTarget).parent().parent().parent();
			this.id = this.entry.attr('data-id');
			//判读点击对象是否已展开
			if (this.entry.hasClass('current-entry')) {
				//点击到已展开
				this.close()
			}else{
				this.closeAll()
				//点击到未展开
				this.expand()
				
				
			}
		}
		, check: function(e){
			this.entry = $(e.currentTarget).parent().parent().parent();
			this.id = this.entry.attr('data-id');
			var key = this.isInCheckId(this.checkIds,this.id);
			if (key !== false){
				this.checkIds.splice(key,1)
			}else{
				this.checkIds.push(this.id)
			}
		}
		, markAllRead: function(e){
			if (this.checkIds.length > 0) {
				var that = this
				$.get('/Api/AllMarkReaded?idList='+JSON.stringify(this.checkIds),function(result){
					if (result.indexOf('success') !== -1){
						for (var key in that.checkIds){
							$('#page-'+that.checkIds[key]).addClass('readed')
						}
					}
				});
			}
		}
		, postAll: function(e){
			if (this.checkIds.length > 0){
				var term_id = $(e.currentTarget).attr('data-id')
				var that = this
				$.get('/Api/PostAll',{idList:JSON.stringify(this.checkIds),term_id:term_id},function(result){
					if (result.indexOf('success') !== -1){
						for (var key in that.checkIds){
							$('#page-'+that.checkIds[key]).addClass('readed')
						}
					}
				});
			}
		}
		//检查是否存在
		, isInCheckId: function(idList,id){
			if (idList.length > 0 ) {
				for (var key in idList){
					if (idList[key] == id) {
						return key
					}
				}
				return false
			}else{
				return false
			}
		}
		, test: function() {
			console.log(333);
		}
		, expand: function(){
			this.entry.addClass('expanded');
			this.entry.addClass('current-entry');
			//获取内容
			var that = this
			$.get('/Api/page?id='+this.id, function(result){
				  that.addPage(result);
			});
			//标记为已读
			$.get('/Api/MarkReaded?id='+this.id);
			if (!this.entry.hasClass('readed')){
				this.entry.addClass('readed');
				//左侧边栏未读数减1
				$('.source-'+this.sid+' em').text(function(index,value){
					return value - 1;
				})
				$('.item em').text(function(index,value){
					return value - 1;
				})
				
			}
			this.sweetTo(this.id)
			
		}
		, sweetTo:function(id){
			var scrollTop = $('#viewer-entries-container').scrollTop()
			var top = $('#page-'+id).offset().top
			$('#viewer-entries-container').scrollTop(top - 85 + scrollTop)
		}


		, close:  function(){
			this.entry.find('.entry-action').remove();
			this.entry.find('.entry-container').remove();
			this.entry.removeClass('expanded current-entry');
		}
		, closeAll: function(){
			$('.entry-action').remove();
			$('.entry-container').remove();
			$('.expanded').removeClass('expanded')
			$('.current-entry').removeClass('current-entry')
		}
		, addPage: function(result){
			var editDom
			this.page = $.parseJSON(result)
			var commentDom
			commentDom = '<ul>'
				
			for ( var key in this.page.comments){
				var comment = this.page.comments[key]
				commentDom += '<li><b>'+comment.author+':</b><span class="commit-content">'+comment.content+'</span></li>'
			}
			commentDom +='</ul>'
			this.entry.append('<div class="entry-action"><a href="javascript:;" class="edit-page"><i class="icon-edit"></i>编辑</a></div>\
						<div class="entry-container">\
							<div class="entry-main">\
								<h2 class="entry-title"><a class="entry-title-link" target="_blank" href="'+this.page.link+'">'+this.page.title+'<div class="entry-title-go-to"></div></a></h2>\
								<div class="entry-body">'+ this.page.content +'</div>\
							</div>\
							<div class="entry-commit">\
							'+ commentDom  +'\
							</div>\
						</div>\
						<div class="entry-action"><a href="javascript:;" class="edit-page"><i class="icon-edit"></i>编辑</a></div>')
			//编辑监听
			//console.log(this.page)
			editDom = this.entry.find('.edit-page')
			editDom.on('click', $.proxy(this.edit, this))
			//editDom.on('edit', $.proxy(this.click, this))
			
		}

		, edit:  function (e){
			var entryContainerDom
			var that = this
			entryContainerDom = this.entry.find('.entry-container')

			entryContainerDom.empty()
			this.entry.find('.entry-action').html('<a href="javascript:;" class="edit-close"><i class="icon-remove"></i>关闭</a>')
			this.entry.find('.entry-action .edit-close').on('click', $.proxy(this.close, this))
			
			var commentDom
			commentDom = '<ul>'
				
			for ( var key in this.page.comments){
				var comment = this.page.comments[key]
				commentDom += '<li id="comment-'+comment.id+'"><b>'+comment.author+':</b><span class="commit-content" data-id="'+comment.id+'">'+comment.content+'</span><a href="javascript:;" class="remove-comment" data-id="'+comment.id+'"><i class="icon-remove"></i>删除</a></li>'
			}
			commentDom +='</ul>'
				
			entryContainerDom.append('<form action="/api/QuickPublishPage" method="post" class="form-page">\
							<input type="hidden" name ="pageid" value="'+ this.page.id +'"/>\
						<div>\
							<input name="title" type="text" value="'+this.page.title+'" class="input-title input"/>\
						</div>\
						<div>\
							<textarea class="text-content" name="content" id="myEditor"></textarea>\
						</div>\
						<span>大咖汇板块:</span>\
						<select name="term_id">\
			  				<option value="3">新谈资</option>\
			  				<option value="4">好段子</option>\
			  				<option value="5">热视频</option>\
			  				<option value="24">深阅读</option>\
						</select>\
						<span>发布者:</span>\
						'+$('.author-list-select').html() + '\
						<span>顶：</span><input type="text" name="ding" value="0" class="ding">\
						<span>踩：</span><input type="text" name="cai" value="0" class="ding">\
						'+commentDom+'\
						<div class="form-actions">\
						  <button type="submit" class="btn btn-primary">发布</button>\
						  <button type="button" class="btn">取消</button>\
						</div>\
					</form>'
				)
				this.editor.options.initialFrameWidth = $(window).width() - 350
				this.editor.render("myEditor");
				this.editor.setContent(this.page.content);


				//评论可编辑
				$('.commit-content').editable('api/EditComment',{
					 indicator : '保存中',
					 name 	: 'comment-content',
					 onblur	: 'submit',
					 style  : 'display: inline',
					 submitdata:function( value ){
						 return {comment_id:$(this).attr('data-id')}
					 }
				})
				//评论可删除
				$('.remove-comment').click(function(){
					var comment_id = $(this).attr('data-id')
					console.log(comment_id)
					$.get('/Api/DelComment?id='+comment_id, function(result){
						  $('#comment-'+comment_id).slideUp()
					});
				})
				//this.editor.setHeight(400);
			//表单ajax提交
			$('.form-page').ajaxForm(function(data){
				var result
				result = $.parseJSON(data)
				if (result.status == 'success' ) {
					that.close()
					that.removeDom()
					that.message('发布成功!<a href="http://dakahui.com/?p='+result.pid+'" target="_blank">查看链接</a>')
				}
				
			});
			this.sweetTo(this.id)

		}
		, message: function(message){
			$('.message-area-container').append('<div class="alert fade in">\
		            <button type="button" class="close" data-dismiss="alert">×</button>\
		            '+message+'\
		            </div>')
		}
		, removeDom: function(){
			this.entry.hide();
		}
		
			
	}
	
	$.fn.viewdail = function (sid){
		return  new ViewDail(sid);
	}
	
}(window.jQuery)

//监听锚链接变化

!function($){
	$(window).hashchange( function(){
		hash = location.hash
		$('#getMore').remove();//去除加载更多
		var pageNum = 0
		if (location.pathname == '/' && (location.hash == '' || location.hash == '#all-source' ) && location.search == '' ) {
			sid = 'all'
			$.get('/Api/pages?sid=all', function(result){
				  renderView(result,sid);
			});
			
			//加载更多实现代码
			$('#entries').after('<button class="btn btn-block" id="getMore">加载更多</button>')
			$('#getMore').click(function(){
				pageNum ++
				var button = $('#getMore')
				button.addClass('disabled')
				button.text('加载中....')
				$.get('/Api/pages?sid=' + sid + '&pageNum='+pageNum, function(result){
					renderMoreView(result,sid)
					button.text('加载更多')
					button.removeClass('disabled')
				})
				
			})
			

			if (!$('.item').hasClass('selected')){
				$('#source-list li').removeClass('current')
				$('.item').addClass('selected')
			}
			
		}else{
			sid = hash.match(/\d+/g)
			$('.item').removeClass('selected')
			$('#source-list li').removeClass('current')
			$('.source-'+sid).addClass('current')
			$.get('/Api/pages?sid=' + sid, function(result){
				  renderView(result,sid);
			});
		}
	})
	$(window).hashchange();
	
	//窗口调整
	
	$(window).resize(function(){
		resizeHeight()
	})
	
	function resizeHeight(){
		var leftSideHeight = $(window).height() - 96
		var contentHeight = $(window).height() - 96
		$('#scrollable-sections-holder').css('height',leftSideHeight)
		$('#viewer-entries-container').css('height',contentHeight)
	}
	
	resizeHeight()
	
	
	
	function renderView(result, sid){
		 data = $.parseJSON(result)
		 pages = data.pages
		 sourceName = data.sourceName;
		 $('#chrome-title').html(sourceName)
		 $('#entries').html('');
		 for(var key in pages) { 
			 dom = getEntrydom(pages[key], key, sid);
			 $('#entries').append(dom)
		 }
		 
		 
		 $('1').viewdail(sid)
	}
	
	
	function renderMoreView(result,sid){
		 data = $.parseJSON(result)
		 pages = data.pages
		 for(var key in pages) { 
			 dom = getEntrydom(pages[key], key, sid);
			 $('#entries').append(dom)
		 }
		 $('1').viewdail(sid);
	}
	
	function getEntrydom(page, k, sid){
		readStyle = '';
		//标记已读
		if (page.isReaded == 1) {
			readStyle = 'readed'
		}
		dom = '<div class="entry entry-' + k + ' '+ readStyle +'" id="page-' + page.id + '" data-id="' + page.id + '">\
				<div class="collapsed">\
				<label class="checkbox"><input type="checkbox" class="checkbox-input"></label>\
			<div class="entry-date">'+ page.postdate +'</div>\
			<div class="entry-main">\
				<a class="entry-original" target="_blank" href="' + page.link +'">原</a>'
		noTitleClass= 'no-source-title'
		if (sid == 'all') {
			dom += '<span class="entry-source-title">' + page.sourceName + '</span>'
			noTitleClass = ''
		}
		dom +=	'<div class="entry-secondary '+noTitleClass+'">\
					<h2 class="entry-title">' + page.title + '</h2>\
				</div>\
			</div>\
		</div>\
	</div>'
		return dom
	}
	
	
	$('.allread-button,.unread-button').click(function(){
		if (!$(this).hasClass('active')){
			$.get('/Api/ChangeUnread')
			setTimeout(function(){
				$(window).hashchange();
			},200)
			
		}
	})
	

	
}(window.jQuery)



