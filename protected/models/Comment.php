<?php

/**
 * This is the model class for table "comment".
 *
 * The followings are the available columns in table 'comment':
 * @property integer $id
 * @property integer $origin_id
 * @property integer $pid
 * @property string $author
 * @property string $content
 * @property integer $parent_id
 * @property integer $create_ts
 */
class Comment extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Comment the static model class
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
		return 'comment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('origin_id, pid, author, content, create_ts', 'required'),
			array('origin_id, pid, parent_id, create_ts', 'numerical', 'integerOnly'=>true),
			array('author', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, origin_id, pid, author, content, parent_id, create_ts', 'safe', 'on'=>'search'),
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
			'origin_id' => 'Origin',
			'pid' => 'Pid',
			'author' => 'Author',
			'content' => 'Content',
			'parent_id' => 'Parent',
			'create_ts' => 'Create Ts',
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
		$criteria->compare('origin_id',$this->origin_id);
		$criteria->compare('pid',$this->pid);
		$criteria->compare('author',$this->author,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('parent_id',$this->parent_id);
		$criteria->compare('create_ts',$this->create_ts);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}