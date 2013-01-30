<?php
class SourceController extends Controller {
	function actionAdd($type){
		
		$model = new Source();
		
		if (isset($_POST['Source'])){
			$model->name = $_POST['Source']['name'];
			$model->list_url = $_POST['Source']['list_url'];
			$model->create_ts = time();
			$model->type = $_POST['Source']['type'];
			$model->lastpost_ts = 0;
			$model->is_page_fetch_by_normal = $_POST['Source']['is_page_fetch_by_normal'];
			$model->content_selector = $_POST['Source']['content_selector'];
			
			if ($model->save()){
				$this->redirect('/');
			}
			
			
		}
		
		
		
		
		switch ($type)
		{
			case Source::TYPE_RSS:
				$this->render('rss_form', array('model' => $model, 'type' => $type));
				break;
			case Source::TYPE_TIEBA:
				$this->render('tieba_form', array('model' => $model, 'type' => $type));
				break;
			case Source::TYPE_WEIBO:
				$this->render('weibo_form', array('model' => $model, 'type' => $type));
				break;
			case Source::TYPE_WEIBOTOP:
				$this->render('weibotop_form', array('model' => $model, 'type' => $type));
				break;
		}
	}
}