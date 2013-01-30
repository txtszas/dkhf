<?php
define('H_R', dirname(Yii::app()->BasePath));

require_once( H_R . '/wp/wp-load.php' );
require_once( H_R . '/wp/wp-admin/includes/image.php');
class ApiController extends Controller
{
	function actionPage($id){
		$page = Page::model()->find('id =:id', array(':id' => $id));
		echo json_encode($page->attributes);
	}
	
	function actionPages($sid){
		if ($sid == 'all') {
			$pages = $this->getAllPages();
			$data = $this->packData($pages);
			echo json_encode(array('pages' => $data, 'sourceName' => '所有源'));
		}else{
			$source = Source::model()->find('id =:id', array(':id' => $sid));
			$pages = $this->getPages($sid);
			$data = $this->packData($pages);
			echo json_encode(array('pages' => $data, 'sourceName' => $source->name, 'lastupdate' => $source->lastpost_ts));
		}
	}
	
	
	function packData($pages) {
		$data = array();
		foreach ($pages as $page) {
			$data[] = array(
						'id'	=> $page->id,
						'title' => $page->title,
						'link'  => $page->link,
						'sid'	=> $page->sid,
						'isReaded' => $page->is_read,
						'sourceName' => $page->source->name,
						'postdate' =>  date('Y-m-d H:i:s', $page->postdate)
					);
		}
		return $data;
	}
	function actionMarkReaded($id){
		$page = Page::getPageByid($id);
		$page->markReaded();
	}
	
	function actionAllMarkReaded($idList){
		$pageIdList = json_decode(stripslashes($idList), true);
		foreach ($pageIdList as $v){
			$page = Page::getPageByid($v);
			$page->markReaded();
		}
		echo 'success';
	}
	
	function actionPostAll($idList,$term_id){
		$pageIdList = json_decode(stripslashes($idList), true);
		foreach ($pageIdList as $v){
			$page = Page::getPageByid($v);
			$author_id = 1;
			$this->publishPage($page, $term_id, $author_id);
		}
		echo 'success';
	}
	
	function getPages($sid){
		$unRead = Config::model()->findByPk('unread');
		if ($unRead->value == 1) {
			return $pages = Page::model()->findAll('sid = :sid AND is_read = 0 AND is_publish = 0 ORDER BY postdate desc LIMIT 100', array('sid' => $sid));
		}else{
			return $pages = Page::model()->findAll('sid = :sid  ORDER BY postdate desc LIMIT 100', array('sid' => $sid));
		}
	}
	
	function getAllPages(){
		$unRead = Config::model()->findByPk('unread');
		if ($unRead->value == 1) {
			return $pages = Page::model()->findAll('is_read = 0 AND is_publish = 0 ORDER BY postdate desc LIMIT 500');
		}else{
			return $pages = Page::model()->findAll('is_publish = 0 ORDER BY postdate desc LIMIT 500');
		}
	}
	
	function actionChangeUnread(){
		$unRead = Config::model()->findByPk('unread');
		$unRead->value = $unRead->value ? 0 : 1;
		$unRead->save();
	}
	
	function actionQuickPublishPage(){
		$pageId = $_POST['pageid'];
		$title = $_POST['title'];
		$content = stripslashes($_POST['content']);
		$term_id = $_POST['term_id'];
		$author_id = $_POST['author_id'];
		$dingNum = $_POST['ding'];
		$caiNum = $_POST['cai'];
		$page = $this->updatePage($pageId, $title, $content);

		if ($page != false) {
			$postId = $this->publishPage($page, $term_id, $author_id);
			if ($postId > 0){
				$result = array('status' => 'success','pid' => $postId);
				Dkh_wti_like_post::addPostDing($dingNum, $caiNum, $postId);
				echo json_encode($result);
				return true;
			}else{
				echo 'fail';
				return false;
			}
		}
		
		
	}
	
	
	function updatePage($id,$title,$content){
		$page = Page::model()->findByPk($id);
		$page->title = $title;
		$page->content = $content;
		if ($page->save()){
			return $page;
		}else{
			return false;
		}
	}
	
	function publishPage($page, $term_id, $author_id){
		define('H_R', dirname(Yii::app()->BasePath));
		require_once( H_R . '/wp/wp-load.php' );
		//存储到大咖汇
		$post = new Dkh_post();
		$post->post_author =$author_id;
		$post->post_date = date('Y-m-d H:i:s',time());
		$post->post_date_gmt = gmdate('Y-m-d H:i:s', time());
		$post->save();
		$post->post_content = $this->post_save_images( $page->content,$post->ID);
		$post->post_title = $page->title;
		$post->post_excerpt = '';
		$post->post_status = 'publish';
		$post->comment_status = 'open';
		$post->post_name = htmlentities($page->title);
		$post->post_modified = date('Y-m-d H:i:s',time());
		$post->post_modified_gmt = gmdate('Y-m-d H:i:s', time());
		$post->post_parent = 0;
		$post->post_type = 'post';
		if ($post->save()) {
			
			$termRelationship = new Dkh_term_relationship();
			$termRelationship->object_id = $post->ID;
			$termRelationship->term_taxonomy_id = $term_id;
			$termRelationship->save();

			//把文章状态改为已发布
			$page->is_publish = 1;
			$page->save();
			
			//记录日志
			
			$log = new Log();
			$log->action = 'publish';
			$log->do_ts = time();
			$log->about_id = $post->ID;
			$log->save();
			
			return $post->ID;
		}else{
			return false;
		}
		
	}
	
	function actionTest(){
		$url = 'http://dapenti.org/blog/rssfortugua.asp';
		$feed = new SimplePie();
		$feed->set_feed_url($url);
		$feed->set_cache_location(Yii::app()->basePath.'/rss_cache');
		$feed->strip_htmltags(false);
		$feed->init();
		echo count($feed->get_items());
	}
	

	
	function post_save_images($content, $post_id){
		set_time_limit(240);
		$preg = preg_match_all ( '/<img.*?src="(.*?)"/', stripslashes ( $content ), $matches );
		if ($preg) {
			foreach ( $matches [1] as $image_url ) {
				if (empty ( $image_url ))
					continue;
				$pos = strpos ( $image_url, get_bloginfo ( 'url' ) );
				if ($pos === false) {
					$res = $this->save_images ( $image_url, $post_id );
					$replace = $res ['url'];
					$content = str_replace ( $image_url, $replace, $content );
				}
			}
		}
		return $content;
	}
	
	//save exterior images
	function save_images($image_url,$post_id){
		$file=file_get_contents($image_url);
		$filename=basename($image_url);
		$res=wp_upload_bits($filename,'',$file);
		$this->insert_attachment($res['file'],$post_id);
		return $res;
	}
	
	//insert attachment
	function insert_attachment($file,$id){
		$dirs=wp_upload_dir();
		$filetype=wp_check_filetype($file);
		$attachment=array(
				'guid'=>$dirs['baseurl'].'/'._wp_relative_upload_path($file),
				'post_mime_type'=>$filetype['type'],
				'post_title'=>preg_replace('/\.[^.]+$/','',basename($file)),
				'post_content'=>'',
				'post_status'=>'inherit'
		);
		$attach_id=wp_insert_attachment($attachment,$file,$id);
		$attach_data=wp_generate_attachment_metadata($attach_id,$file);
		wp_update_attachment_metadata($attach_id,$attach_data);
		
		return $attach_id;
	}
	function writerFile($str){
		$file = fopen(Yii::app()->basePath."/test","w");
		echo fwrite($file,$str);
		fclose($file);
	}
	
	
}