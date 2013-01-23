<?php
class Dkh_user extends MyAR
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
		return 'wp_users';
	}
	
	
	/**
	 * 获取大咖汇作者列表
	 */
	static public function getAuthorList() {
		$authors = self::model()->findAll();
		$authorList = CHtml::listData($authors, 'ID', 'user_login');
		return $authorList;
	}
}
