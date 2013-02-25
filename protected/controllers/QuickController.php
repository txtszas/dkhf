<?php
class QuickController extends Controller{
	public function actions()
	{
		return array(
				// captcha action renders the CAPTCHA image displayed on the contact page
				'captcha'=>array(
						'class'=>'CCaptchaAction',
						'backColor'=>0xFFFFFF,
				),
				// page action renders "static" pages stored under 'protected/views/site/pages'
				// They can be accessed via: index.php?r=site/page&view=FileName
				'page'=>array(
						'class'=>'CViewAction',
				),
		);
	}

	public function actionFetch(){
		
		if (isset($_POST['url'])) {
			$url = $_POST['url'];
			//检查url是否为空
			if ($url == ''){
				$msg = 'url不能为空';
				$this->render('index', array('msg' => $msg));
				return true;
			}
			
			//检查url是否符合标准
			if (!$this->checkUrl($url)){
				$msg = 'url格式错误';
				$this->render('index', array('msg' => $msg));
				return true;
				
			}
			
			$fetch = new Fetch();
			$page = $fetch->fetchByUrl($url);
		}
		$authorList = Dkh_user::getAuthorList();
		$this->render('index',array(
								'page' => $page,
								'authorList' => $authorList			
					));
		
	}
	

	
	public function checkUrl($url){
		preg_match('/^http:\/\//', $url, $match);
		if (count($match) > 0) {
			return true;
		}else{
			return false;
		}
		
	}
}