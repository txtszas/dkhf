<?php

/**
 * This is the model class for table "log".
 *
 * The followings are the available columns in table 'log':
 * @property integer $id
 * @property string $action
 * @property integer $do_ts
 * @property integer $about_id
 */
class Log extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Log the static model class
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
		return 'log';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('action, do_ts, about_id', 'required'),
			array('do_ts, about_id', 'numerical', 'integerOnly'=>true),
			array('action', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, action, do_ts, about_id', 'safe', 'on'=>'search'),
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
			'action' => 'Action',
			'do_ts' => 'Do Ts',
			'about_id' => 'About',
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
		$criteria->compare('action',$this->action,true);
		$criteria->compare('do_ts',$this->do_ts);
		$criteria->compare('about_id',$this->about_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	static public function todayPublish(){
		date_default_timezone_set('Asia/Shanghai');
		$today_ts = strtotime(date('Y-m-d',time()));
		return self::model()->count("action = 'publish' AND do_ts > :do_ts", array(':do_ts' => $today_ts));
	}
}