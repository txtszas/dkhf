<?php

/**
 * This is the model class for table "page".
 *
 * The followings are the available columns in table 'page':
 * @property integer $id
 * @property integer $sid
 * @property string $title
 * @property string $content
 * @property string $link
 * @property integer $postdate
 */
class Page extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Page the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'page';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sid, title, content, link, postdate', 'required'),
			array('title, content', 'unique'),
			array('sid, postdate', 'numerical', 'integerOnly'=>true),
			array('title, link', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, sid, title, content, link, postdate', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'source' => array(self::BELONGS_TO, 'Source', 'sid'),
			'comments' => array(self::HAS_MANY, 'Comment', 'pid')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'sid' => 'Sid',
			'title' => 'Title',
			'content' => 'Content',
			'link' => 'Link',
			'postdate' => 'Postdate',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('sid',$this->sid);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('link',$this->link,true);
		$criteria->compare('postdate',$this->postdate);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/*
	 * 检查重复title
	 */
	public static function checkTitle($title){
		Yii::beginProfile('checkTitle');
		$result = self::model()->find('title =:title', array(':title' => $title));
		if ($result == NULL) {
			return true;
		}else{
			return false;
		}
		Yii::endProfile('checkTitle');
	}
	
	public static function checkContent($content){
		Yii::beginProfile('checkContent');
		$result = self::model()->find('content =:content', array(':content' => $content));
		if ($result == NULL) {
			return true;
		}else{
			return false;
		}
		Yii::endProfile('checkContent');
	}
	/**
	 * 根据主键获取对象
	 */
	public static function getPageByid($id){
		return self::model()->find('id=:id', array(':id' => $id));
	}
	/**
	 * 总未读数
	 */
	public static function unReadNum(){
		return self::model()->count('is_read = 0');
	}
	public function markReaded(){
		$this->is_read = 1;
		$this->save();
	}
	
	static public function clear3day(){
		$time = time() - 2 * 3600 * 24; 
		$pages = self::model()->findAll('postdate < '.$time);
		echo count($pages);
		foreach ($pages as $page){
			foreach ($page->comments as $comment){
				$comment->delete();
			}
			$page->delete();
		}
	}
	
	static public function todayFetchNum(){
		date_default_timezone_set('Asia/Shanghai');
		$today_ts = strtotime(date('Y-m-d',time()));
		return self::model()->count('fetch_ts > '.$today_ts);
	}
}