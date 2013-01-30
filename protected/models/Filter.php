<?php

/**
 * This is the model class for table "filter".
 *
 * The followings are the available columns in table 'filter':
 * @property integer $id
 * @property integer $sid
 * @property string $rule
 * @property strin $replace
 */
class Filter extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Filter the static model class
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
		return 'filter';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sid, rule', 'required'),
			array('sid', 'numerical', 'integerOnly'=>true),
			array('rule', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, sid, rule, replace', 'safe', 'on'=>'search'),
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
			'rule' => 'Rule',
			'replace' => 'Replace'
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */

	
	public function filterContent($content){
		return preg_replace('/' . $this->rule . '/', $this->replace, $content);
	}
	
	static public function getFilterBySid($sid){
		return self::model()->findAll('sid = :sid', array(':sid' => $sid));
	}
	
	static public function filterRule($content, $sid){
		$filters = self::getFilterBySid($sid);
		if (count($filters) > 0 ){
			foreach ($filters as $filter) {
				$content = $filter->filterContent($content);
			}
		}
		return $content;
	}
}