<?php
class Factory_Comment_Fetch {
	static $fetchShortName = array(
				'Jiandan',
				'U148',
				'Weibo'
			);
	static public function getFetchByShortName($shortName){
		if (in_array($shortName, self::$fetchShortName)) {
			$fetchName = 'Comment_Fetch_'.$shortName;
			$fetch = new $fetchName;
			return $fetch;
		}else{
			return false;
		}
		
	}
}