<?php
class Comment_Fetch_Jiandan extends Comment_Fetch{
		
	function fetch($url, $pid)
	{
		$this->pid = $pid;
		$dom = $this->getDom($url);
		$comments = $dom->find('.commentlist li');
		$commentData = array();
		foreach ($comments as $comment){
			$commentData[] = $this->getComment(pq($comment));
		}
		$this->saveToDb($commentData);
	}
		
	
	function getComment($comment){
		
		//获取在原站评论id
		$comment_id = $comment->attr('id');
		preg_match('/\d+/', $comment_id, $match);
		$origin_id = $match[0];
		
		//获取评论内容
		$content = $comment->find('p')->html();
		
		//判读是否是回复
		if (preg_match('/#comment-(\d+)/', $content, $match)){
			$comment_parent = $match[1];
			$content = preg_replace('/@<a href="#comment-\d+" rel="nofollow">.*?<\/a>:/', '', $content);
		}else{
			$comment_parent = 0;
		}
		
		$author =  $comment->find('b')->eq(0)->text();
		
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
		
		$time = $comment->find('.time')->text();
		$time = str_replace('@', '', $time);
		$create_ts = strtotime($time);
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