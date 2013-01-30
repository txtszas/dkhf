<?php
class Dkh_wti_like_post extends MyAR
{
	public $dbname = 'dkhdb';
	/**
	 * Returns the static model of the specified AR class.
	 * 
	 * @return Attendance the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model ( $className );
	}
	
	/**
	 *
	 * @return string the associated database table name
	 */
	public function tableName() {
		return 'wp_wti_like_post';
	}
	
	
	
	static public function addPostDing($dingNum, $caiNum, $postId){
		$ding = new Dkh_wti_like_post();
		$ding->post_id = $postId;
		$ding->value = $dingNum;
		$ding->date_time = date('Y-m-d H:s:i',time());
		$ding->ip = '127.0.0.1';
		$ding->save();
		
		$cai = new Dkh_wti_like_post();
		$cai->post_id = $postId;
		$cai->value = $caiNum * (-1);
		$cai->date_time = date('Y-m-d H:s:i',time());
		$cai->ip = '127.0.0.1';
		$cai->save();
	}
}
