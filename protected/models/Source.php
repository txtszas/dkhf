<?php

/**
 * This is the model class for table "source".
 *
 * The followings are the available columns in table 'source':
 * @property integer $id
 * @property string $name
 * @property string $list_url
 * @property integer $create_ts
 * @property integer $type
 * @property integer $lastpost_ts
 */
class Source extends CActiveRecord
{
	
	const TYPE_DEFUALT 	= 0;
	const TYPE_RSS 		= 1;
	const TYPE_WEIBO 	= 2;
	const TYPE_TIEBA 	= 3;
	const TYPE_WEIBOTOP = 4;
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Source the static model class
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
		return 'source';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, list_url, create_ts, type', 'required'),
			array('id, create_ts, type', 'numerical', 'integerOnly'=>true),
			array('name, list_url', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, list_url, create_ts, type', 'safe', 'on'=>'search'),
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
			'name' => 'Name',
			'list_url' => 'List Url',
			'create_ts' => 'Create Ts',
			'type' => 'Type',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('list_url',$this->list_url,true);
		$criteria->compare('create_ts',$this->create_ts);
		$criteria->compare('type',$this->type);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	public function unReadNum(){
		return Page::model()->count('sid = :sid AND is_read = 0', array(':sid' => $this->id));
	}
	
}