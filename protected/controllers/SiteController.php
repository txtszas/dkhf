<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
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

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		$sources = Source::model()->findAll();
		
		$allUnreadNum = Page::unReadNum();
		
		$unRead = Config::model()->findByPk('unread');

		$authorList = Dkh_user::getAuthorList();
		
		$this->render('index', array(
								'sources' 		=> $sources,
								'unReadNum' 	=> $allUnreadNum,
								'isShowUnread' 	=> $unRead->value,
								'authorList'	=> $authorList
							)
					);
	}
	
	public function actionUpdate() {
		$weiboO = new SaeTOAuthV2(Yii::app()->params['weibo_config']['akey'], Yii::app()->params['weibo_config']['skey']);
		$code_url = $weiboO->getAuthorizeURL( Yii::app()->params['weibo_config']['callback_url'], NULL, NULL, 'mobile');
		echo '<a href="' . $code_url . '">刷新access</a>';
	}
	
	public function actionCallBack($code)
	{
		$token = $this->getToken($code);
		
		//获取本地存储的token
		$local_token = Token::model()->find('id = 1');

		//获取过期信息
		$weiboO = new SaeTOAuthV2(Yii::app()->params['weibo_config']['akey'], Yii::app()->params['weibo_config']['skey']);
		$token_info = $weiboO->oAuthRequest('https://api.weibo.com/oauth2/get_token_info', 'POST', array('access_token' => $token['access_token']));
		$token_info = json_decode($token_info, true);
		//更新token 的信息
		$local_token->access_token = $token['access_token'];
		$local_token->create_at = $token_info['create_at'];
		$local_token->expire_in = $token_info['expire_in'];
		$local_token->save();
		
		$expiretime = $token_info['create_at'] + $token_info['expire_in'];
		var_dump(date('Y-m-d H:i:s',$expiretime));
	}
	
	public function actionFetch()
	{
		$fetch = new Fetch();
		$fetch->fetchAll();
	}

	public function actionFetchTop(){
		$fetch = new Fetch();
		$fetch->fetchTop();
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$name='=?UTF-8?B?'.base64_encode($model->name).'?=';
				$subject='=?UTF-8?B?'.base64_encode($model->subject).'?=';
				$headers="From: $name <{$model->email}>\r\n".
					"Reply-To: {$model->email}\r\n".
					"MIME-Version: 1.0\r\n".
					"Content-type: text/plain; charset=UTF-8";

				mail(Yii::app()->params['adminEmail'],$subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
	
	public function getToken($code)
	{
		$weiboO = new SaeTOAuthV2(Yii::app()->params['weibo_config']['akey'], Yii::app()->params['weibo_config']['skey']);
		$keys = array();
		$keys['code'] = $code;
		$keys['redirect_uri'] =  Yii::app()->params['weibo_config']['callback_url'];
		$token = $weiboO->getAccessToken( 'code', $keys ) ;
// 		$session = new CHttpSession;
// 		$session->open();
// 		$session['weibo_token'] = $token;
		return $token;
	}
	
	public function actionTest(){
		$filter = Filter::getFilterBySid(1);
		$content = 'tsdfaf123123';
		echo $filter->filterContent($content);
	}
	public function actionClear(){
		Page::clear3day();
	}
}