<?php
class Comment_Fetch_Weibo extends Comment_Fetch{
	
	function fetch($weiboId, $pid){
		$this->pid = $pid;
		$comments = $this->getComments($weiboId);
		$commentData = array();
		$count = count($comments['comments']);
		$commentData = array();
		while ($count > 0 ) {
			$commentData[] = $this->getComment($comments['comments'][$count-1]);
			$count --;
		}
		$this->saveToDb($commentData);
		
	}
	
	function getComments($weiboId){
		$token = Token::getToken();
		$weiboO = new SaeTOAuthV2(Yii::app()->params['weibo_config']['akey'], Yii::app()->params['weibo_config']['skey']);
		$weiboC = new SaeTClientV2( Yii::app()->params['weibo_config']['akey'], Yii::app()->params['weibo_config']['skey'] , $token );
		return $weiboC->get_comments_by_sid($weiboId);
	}
	
	function getComment($comment){
		$origin_id = $comment['id'];
		$content = $comment['text'];
		$comment_parent = 0;
		$author = $comment['user']['screen_name'];
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
		
		
		$time = $comment['created_at'];
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