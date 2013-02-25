<?php
class Comment_Fetch{
	public $pid;
	protected  $commenters = array();
	protected $hasUsers = array();
	
	function getDom($url)
	{
		Yii::beginProfile('fetch page');
		$snoopy = new Snoopy();
		$snoopy->fetch($url);
		Yii::endProfile('fetch page');
		$result = $snoopy->results;
		$dom = phpQuery::newDocumentHTML($result,'utf8');
		return $dom;
	}
	
	function writerFile($str){
		$file = fopen(Yii::app()->basePath."/test","w");
		echo fwrite($file,$str);
		fclose($file);
	}
	
	function saveToDb($commentData){
		foreach ($commentData as $comment){
			$model = new Comment();
			$model->attributes = $comment;
			if ($model->save()){
			};
		}
	}
	
	function test(){
		echo 'hellow';
	}
}