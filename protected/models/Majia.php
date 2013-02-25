<?php

/**
 * This is the model class for table "majia".
 *
 * The followings are the available columns in table 'majia':
 * @property integer $id
 * @property integer $uid
 * @property string $name
 * @property string $majia
 * @property integer $majia_id
 */
class Majia extends CActiveRecord
{
	public $count = 1254;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Majia the static model class
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
		return 'majia';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, majia', 'required'),
			array('uid, majia_id', 'numerical', 'integerOnly'=>true),
			array('name, majia', 'length', 'max'=>100),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, uid, name, majia, majia_id', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'uid' => 'Uid',
			'name' => 'Name',
			'majia' => 'Majia',
			'majia_id' => 'Majia',
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
		$criteria->compare('uid',$this->uid);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('majia',$this->majia,true);
		$criteria->compare('majia_id',$this->majia_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	static function randomOne()
	{
		$limit = rand(1, 1254);
		$majia = self::model()->findAllBySql('select * from majia where 1 =1 limit '.$limit.',1');
		return $majia[0];
	}
}