<?php
class Dkh_term_taxonomy extends MyAR
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
		return 'wp_term_taxonomy';
	}
}
