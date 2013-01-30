<?php
class FilterController extends Controller {
	
	function actionIndex(){
		$filters = Filter::model()->findAll();
		$this->render('index', array('filters' => $filters));
	}
	
	function actionAdd(){
		$model = new Filter();
		$sources = Source::model()->findAll();
		
		if (isset($_POST['Filter'])){
			$model->attributes = $_POST['Filter'];
			$model->replace = $_POST['Filter']['replace'];
			if ($model->save()){
				$this->redirect($this->createUrl('Filter/index'));
			}
		}
		$this->render('add', array('model' => $model, 'sources' => $sources));
		
	}
	
	function actionEdit($id){
		$model = Filter::model()->findByPk($id);
		$sources = Source::model()->findAll();
		if (isset($_POST['Filter'])){
			$model->attributes = $_POST['Filter'];
			$model->replace = $_POST['Filter']['replace'];
			if ($model->save()){
				$this->redirect($this->createUrl('Filter/index'));
			}
		}
		$this->render('add', array('model' => $model, 'sources' => $sources));
		
	}
	
	function actionDel($id){
		$model = Filter::model()->findByPk($id);
		if ($model->delete()){
			$this->redirect($this->createUrl('Filter/index'));
		}
	}
	
}