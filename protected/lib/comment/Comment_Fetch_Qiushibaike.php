<?php
class Comment_Fetch_Qiushibaike extends Comment_Fetch{
	function fetch($url, $pid)
	{
		$this->pid = $pid;
		Yii::beginProfile('getDom');
		$dom = $this->getDom($url);
		Yii::endProfile('getDom');
		$comments = $dom->find('.comments .comment-block');
		$commentData = array();
		$commentNum = count($comments);
		$s = 0;
		foreach ($comments as $comment){
			$s ++;
			if ($s > 50) 
				break;
			$commentData[] = $this->getComment(pq($comment));
		}

 		$this->saveToDb($commentData);
	}
	
	function getComment($comment){
		$comment_id = $comment->attr('id');
		$match = array();
		preg_match('/\d+/', $comment_id, $match);
		$origin_id = $match[0];
		
		$content = $comment->find('.replay .body')->html();
		
		$author =  $comment->find('.replay .userlogin')->text();
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
		
		$create_ts = 0;
		
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