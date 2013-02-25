<?php
class Comment_Fetch_U148 extends Comment_Fetch{
	function fetch($url, $pid)
	{
		$this->pid = $pid;
		$dom = $this->getDom($url);
		$comments = $dom->find('#floors ul');
		$commentData = array();
		foreach ($comments as $comment){
			$commentData[] = $this->getComment(pq($comment));
		}
		$this->saveToDb($commentData);
	}
	
	function getComment($comment){
		
		//获取评论id
		$comment_id = $comment->attr('id');
		preg_match('/\d+/', $comment_id, $match);
		$origin_id = $match[0];
		
		//获取评论内容
		$content = $comment->find('#review_contents_'.$origin_id)->html();
		//过滤内容引用部分
		$content = preg_replace('/<blockquote>.*?<\/blockquote>/', '', $content);
		
		//抓取作者
		$author =  $comment->find('.uinfo a')->text();
		//获取对应的马甲
		if (in_array($author, $this->commenters)){
			$commenter = $this->hasUsers[$author];
		}else{
			$majia = Majia::randomOne();
			//如果马甲已经用过，重新随机
			while (in_array($majia->majia, $this->hasUsers) || $majia->majia == ''){
				$majia = Majia::randomOne();
			}
			$this->hasUsers[$author] = $majia->majia;
			$this->commenters[$majia->majia] = $author;
			$commenter = $majia->majia;
		}
		
		//获取时间
		$time = $comment->find('.uinfo span')->text();
		preg_match('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}/', $time, $match);
		$create_ts = strtotime($match[0]);
		
		//回复信息
		$comment_parent = 0;
		return array(
				'origin_id' => $origin_id,
				'pid'		=> $this->pid,
				'author'	=> $commenter,
				'content'	=> $content,
				'parent_id'	=> $comment_parent,
				'create_ts' => $create_ts
		);		
	}
}