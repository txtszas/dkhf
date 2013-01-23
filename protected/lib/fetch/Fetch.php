<?php 
/**
 * 抓取器
 */
class Fetch
{
	public $tiebaMaxPageNum = 2;
	/*
	 * 抓取所有来源
	 */
	function fetchAll()
	{
		set_time_limit(0);
		$sources = $this->getAllSource();
		foreach ($sources as $s){
			//Yii::beginProfile('fetch sources');
			$pageList = $this->getPageList( $s );
			//Yii::endProfile('fetch sources');
			
			if ($pageList && count($pageList) > 0 ) {
				//图片本地化
				Yii::beginProfile('img local');
				$this->imageLocalization($pageList);
				Yii::endProfile('img local');
				
				Yii::beginProfile('save to db');
				$this->saveToDb($pageList);
				$s->lastpost_ts = time();
				$s->save();
				Yii::endProfile('save to db');
			}
			
		}
		return true;
	}

	function fetchTop(){
		set_time_limit(0);
		$sources = $this->getTopSource();
		foreach ($sources as $s){
			//Yii::beginProfile('fetch sources');
			$pageList = $this->getPageList( $s );
			//Yii::endProfile('fetch sources');
			
			if ($pageList && count($pageList) > 0 ) {
				//图片本地化
				Yii::beginProfile('img local');
				$this->imageLocalization($pageList);
				Yii::endProfile('img local');
				
				Yii::beginProfile('save to db');
				$this->saveToDb($pageList);
				$s->lastpost_ts = time();
				$s->save();
				Yii::endProfile('save to db');
			}
			
		}
		return true;
	}

	/*
	 * 获取所有来源
	 */
	
	function getAllSource(){
		return Source::model()->findAll();
	}

	function getTopSource(){
		return Source::model()->findAll('type = 4');
	}
	
	/**
	 * 根据类型获取内容
	 */
	function getPageList( $source ){
		switch ( $source->type ){
			case Source::TYPE_RSS :
				return $this->getRssList($source);
			case Source::TYPE_WEIBO :
				return $this->getWeiboList($source);
			case Source::TYPE_TIEBA:
				return $this->getTiebaList($source);
			case Source::TYPE_WEIBOTOP:
				return $this->getWeiboTopList($source);
		}
	}
	

	function getRssList($source){
		//获取订阅
		Yii::beginProfile('get rss');
		$feed = $this->getRssFeed($source->list_url);
		Yii::endProfile('get rss');
		$pageList = array();
		//遍历每个订阅项目
		foreach ($feed->get_items() as $item) {
			//获取标题
			$title = html_entity_decode ( $item->get_title (), null, 'UTF-8' );
			//检查是否已经存在此标题
			if (Page::checkTitle($title)){
				$postdate = strtotime( $item->get_date() );
				//获取页面内容
				$content = $this->getPageContent($source->is_page_fetch_by_normal, $item, $source);
				$pageList [] = array (
						'title' => $title,
						'sid' => $source->id,
						'link' => $item->get_link (),
						'postdate' => $postdate ? $postdate : time(),
						'content' => $content
				);
			}
		}
		return $pageList;
	}
	
	function getRssFeed($url){
		$feed = new SimplePie();
		$feed->set_feed_url($url);
		$feed->set_cache_location(Yii::app()->basePath.'/rss_cache');
		$feed->strip_htmltags(false);
		$feed->init();
		return $feed;
	}
	

	function getPages($pageList){
		if (count($pageList) > 0 ){
			foreach ($pageList as $page) {
				$this->getPage($page['link']);
			}
		}else{
			return false;
		}
	}

	function getPage($url, $source) {
		$snoopy = new Snoopy();
		Yii::beginProfile('fetch page');
		$snoopy->fetch($url);
		Yii::endProfile('fetch page');
		$content = $this->parsePage($snoopy->results, $source);
		return $content;
	}
	
	function parsePage($html, $source){
		$this->writerFile($html);
		$doc = phpQuery::newDocumentHTML($html);
		$content = $doc->find($source->content_selector)->html();
		$this->writerFile($content);
		return $content;
	}
	
	function writerFile($str){
		$file = fopen(Yii::app()->basePath."/test","w");
		echo fwrite($file,$str);
		fclose($file);
	}
	
	
	
	function saveToDb($pageList){
		foreach ($pageList as $page){
			$model = new Page();
			$model->attributes = $page;
			$model->save();
		}
	}
	
	/**
	 * 获取页面内容
	 */
	
	function getPageContent($isNormal,$item,$source){
		if ($isNormal) {
			$content = $this->getPage ( $item->get_link (), $source );
		} else {
			$content = $item->get_content ();
		}
		return html_entity_decode ( $content, null, 'UTF-8' );;
	}
	
	/**
	 * 获取微博内容
	 */
	function getWeiboList($source) {
		$token = Token::getToken();
		$weiboDate = $this->getWeiboDate($token, $source->list_url);
		$pageList = array();
		if ($weiboDate) {
			$pageList = $this->getWeiboPageList($weiboDate, $source);
		}
		return $pageList;
	}
	
	function getWeiboPageList($weiboDate, $source) {
		$pageList = array();
		foreach ($weiboDate as $weibo){
			if (isset($weibo['text'])) {


			$content= $weibo['text'];
				
				$postDate = strtotime($weibo['created_at']);
				$title = $weibo['user']['screen_name'] . ':' . $this->utf8Substr($content, 0, 20);
				if (isset($weibo['bmiddle_pic'])){
					$content .= '<p><img src="' . $weibo['bmiddle_pic'] . '"></p>';
		
				}
				$pageList [] = array (
						'title' => $title,
						'sid' => $source->id,
						'link' => '1',
						'postdate' => $postDate ? $postDate : time(),
						'content' => $content
				);
			}
		}
		return $pageList;
	}
	
	
	function getWeiboDate($token, $name){
		$weiboO = new SaeTOAuthV2(Yii::app()->params['weibo_config']['akey'], Yii::app()->params['weibo_config']['skey']);
		$weiboC = new SaeTClientV2( Yii::app()->params['weibo_config']['akey'], Yii::app()->params['weibo_config']['skey'] , $token );
		$weibos = $weiboC->user_timeline_by_name($name,1,20,0,0,1,0);
		if (isset($weibos['statuses'])) {
			return $weibos['statuses'];
		}else{
			return false;
		}
	}
	
	function utf8Substr($str, $from, $len)
	{
		return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$from.'}'.
				'((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$len.'}).*#s',
				'$1',$str);
	}
	
	
	function getTiebaList($source){
		//Yii::beginProfile('fetch tiebalist');
		$pageList = $this->getThreadList($source);
		//Yii::endProfile('fetch tiebalist');
		$pageList = $this->getThreadContent($pageList);
		return $pageList;	
	}
	
	function getThreadList($source){
		$pageList = array();
		$doc = $this->getDom($source->list_url,'gbk');
		//捕获帖子list
		$list = $doc->find('#thread_list li.j_thread_list');
		foreach ($list as $tiezi) {
			$thread = pq($tiezi);
			$attr = $thread->attr('data-field');	
			$attrD = json_decode($attr,true);
			$replayNum = $attrD['reply_num'];
			$isTop = $attrD['is_top'];
			$tid = $attrD['id'];
			$title= trim($thread->find('.threadlist_title')->text());
			//只抓取指定回复数和非置顶帖子且不存在此贴
			if ($replayNum > 200 && $isTop == 0 && Page::checkTitle($title)) {
				$pageList[] = array(
								'title' => $title,
								'sid' => $source->id,
								'link' => 'http://tieba.baidu.com/p/'.$tid
				);
			}
		}
		return $pageList;
	}
	function getThreadContent($pageList) {
		foreach ($pageList as $k => $thread) {
			Yii::beginProfile('fetch tiebacontent');
			$contentAndPostdate = $this->getTiebaContent($thread['link']);
			Yii::endProfile('fetch tiebacontent');
			if ($contentAndPostdate['content'] == FALSE){
				$pageList[$k]['content'] = null;
			}else{
				$pageList[$k]['content'] = $contentAndPostdate['content'];
			}
			$pageList[$k]['postdate'] = $contentAndPostdate['postdate'];
		}
		return $pageList;
	}
	/**
	 * 获取贴吧某一帖子内容和时间
	 * @param string $url
	 * @return boolean
	 */
	
	function getTiebaContent($url) {
		$url = $url . '?see_lz=1';
		$doc = $this->getDom($url, 'gbk');
		$title = $doc->find('title')->text();
		//判读页面是否存在(可能被和谐了)
		if ($title != '百度贴吧'){
			//获取页码
			$pageNum = $doc->find('.thread_theme_3 .l_reply_num .red')->eq(0)->html();
			//检查页面是否超出限定	
			$pageNum = $pageNum > $this->tiebaMaxPageNum ? $this->tiebaMaxPageNum : $pageNum;
			
			$dateInfo = json_decode($doc->find('.l_post')->eq(0)->attr('data-field'), true);
			$postDate = strtotime($dateInfo['content']['date']);
			//遍历所有页
			$content = '';
			for ($page = 1; $page <= $pageNum; $page++) {
				Yii::beginProfile('fetch tiebaPageContent');
				$content .= $this->getTiebaPageContent($url . '&pn=' . $page);
				Yii::endProfile('fetch tiebaPageContent');
			}
			return array('content' => $content, 'postdate' => $postDate);
			
		}else{
			return false;
		}
	}
	/*
	 * 获取贴吧单页内容
	 */
	function getTiebaPageContent($url){
		$doc = $this->getDom($url, 'gbk');
		$floors = $doc->find('.l_post');
		$content = '';
		foreach ($floors as $floor){
			Yii::beginProfile('fetch tiebaPageFloor');
			$floorDom = pq($floor);
			$content .= $floorDom->find('.d_post_content')->html();
			$content .= '<br>';
			Yii::endProfile('fetch tiebaPageFloor');
		}
		return $content;
	}
	
	function getDom($url, $coding = 'UTF-8'){
		$snoopy = new Snoopy();
		$snoopy->fetch($url);
		$result = $snoopy->results;
		if ($coding != 'UTF-8') {
			//转码
			$result = preg_replace('/<meta .*?charset.*?>/', '<meta http-equiv="Content-Type" content="text/html;charset=utf-8">', $result);
			$result = mb_convert_encoding( $result, 'UTF-8', $coding );
		}

		//创建dom对象
		$doc = phpQuery::newDocumentHTML($result);
		return $doc;
	}
	
	
	function getWeiboTopList($source) {
		//抓取页面
		$doc = $this->getDom($source->list_url);
		//获取mid
		$mids = $doc->find('.WB_feed')->attr('action-data');
		preg_match_all('/\d+/', $mids, $matches);
		$mids = $matches[0];
		$weiboDate = $this->getWeiboDateByMids($mids);
 		$pageList = $this->getWeiboPageList($weiboDate, $source);
		return $pageList;
	}
	
	function getWeiboDateByMids($mids){
		$data = array();
		$token = Token::getToken();
		$weiboO = new SaeTOAuthV2(Yii::app()->params['weibo_config']['akey'], Yii::app()->params['weibo_config']['skey']);
		$weiboC = new SaeTClientV2( Yii::app()->params['weibo_config']['akey'], Yii::app()->params['weibo_config']['skey'] , $token );
		foreach ($mids as $mid) {
			$data[] = $weiboC->show_status($mid);
		}
		return $data;
	}
	
	
	function imageLocalization( &$pageList ){
		foreach ($pageList as $k => $page){
			$pageList[$k]['content'] = $this->saveImage( $pageList[$k]['content'] );
		}
	}
	
	
	
	/*
	 * 图片存储到本地，过滤内容
	 */
	
	function saveImage( $content ) {
		$doc = phpQuery::newDocumentHTML($content);
		$imgs = $doc->find('img');
		$imgsArr = array();
		foreach (phpQuery::pq( $imgs ) as $img ) {
			$url = phpQuery::pq( $img )->attr('src');
			$localUrl = $this->add( $url, $imgsArr);
			phpQuery::pq( $img )->attr('src', $localUrl);
		}
		if( count($imgsArr) ){		
			Downloader::curlDownloadAll($imgsArr);
		}
		return $doc->html();
		
	}
	protected function add( $url, &$imgs ){
		preg_match('#\.[a-zA-Z]*$#', $url, $match);
		if( isset( $match[0] ) ){
			$fileType = $match[0];
		}else{
			$fileType = 'jpg';
		}
		$rPath = '/attachment/Mon_'.date('ym').'/'.md5( $url ).$fileType;
		$path = dirname(Yii::app()->BasePath) .$rPath;
		$imgs[$url] = $path;
		return Yii::app()->params['imgPrefix'].$rPath;
	}
	
	
	
	
	
	
	
}

?>