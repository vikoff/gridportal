<?

class ProfileController extends Controller{
	
	const DEFAULT_VIEW = 1;
	const TPL_PATH = 'Profile/';
	
	// методы, отображаемые по умолчанию
	// переопределены в _getDisplayDefaultIdentifier()
	
	// права на выполнение методов контроллера
	public $permissions = array(
		'display_registration' 		=> PERMS_UNREG,
		'display_forget_password'	=> PERMS_UNREG,
		'display_greeting' 			=> PERMS_UNREG,
		'display_home' 				=> PERMS_UNREG,
		'display_edit' 				=> PERMS_UNREG,

		'action_login' 				=> PERMS_UNREG,
		'action_logout' 			=> PERMS_UNREG,
		'action_registration' 		=> PERMS_UNREG,
		'action_edit'		 		=> PERMS_REG,
		'action_set_new_password'	=> PERMS_REG,
		'action_check_voms'			=> PERMS_REG,
		'action_save_default_voms'	=> PERMS_REG,
		'action_check_cert'			=> PERMS_REG,
		'admin_action_delete' 		=> PERMS_ADMIN,
		
		'ajax_ping'		 			=> PERMS_UNREG,
		'ajax_check_email' 			=> PERMS_UNREG,
		'ajax_save_user_stat'		=> PERMS_UNREG,
	);
	
	
	// ПЕРЕОПРЕДЕЛЕНИЕ МЕТОДА ИЗ CONTROLLER.CORE.PHP
	protected function _getDisplayDefaultIdentifier(){
		
		if(!CurUser::get()->isLogged())
			App::redirect(App::href('profile/registration'));
			
		return 'home';
	}
	
	/////////////////////
	////// DISPLAY //////
	/////////////////////
	
	// DISPLAY REGISTRATION
	public function display_registration($params = array()){
			
		$user = CurUser::get();
		
		if($user->isLogged())
			App::redirectHref('profile');
		
		$variables = array_merge($_POST, array(
			'action' => 'registration',
			'userError' => Messenger::get()->getAll(),
			'jsRules' => $user->getValidator()->getJsRules(),
			'years_list' => YDate::getYearsList(getVar($_POST['birth_year'])),
			'months_list' => YDate::getMonthsList(getVar($_POST['birth_month'])),
			'days_list' => YDate::getDaysList(getVar($_POST['birth_day'])),
			'countries_list' => Common::getCountriesList(getVar($_POST['country'])),
		));
		
		FrontendViewer::get()
			->setTitle('Регистрация')
			->setContentSmarty(self::TPL_PATH.'registration.tpl', $variables)
			->render();
	}
	
	// DISPLAY EDIT
	public function display_edit($params = array()){
		
		if(!CurUser::get()->isLogged())
			App::redirectHref('');
			
		$user = CurUser::get();
		
		$firstVisitText = '';
		if(getVar($params[0]) == 'first'){
			$text = Page::load(4)->getAllFieldsPrepared();
			$firstVisitText = $text['body'];
		}
		
		$vomsList = VomsCollection::load()->getAll();
		$projectList = ProjectCollection::load()->getAll();
		$projectVoms = ProjectCollection::load()->getProjectsVoms();
		
		// добавление к проектам соответствующих виртуальных организаций
		foreach($projectList as &$p){
			$p['voms'] = array();
			if(!empty($projectVoms[$p['id']]))
				foreach($projectVoms[$p['id']] as $v)
					$p['voms'][$v] = $vomsList[$v]['name'];
		}
		
		$variables = array_merge($user->getAllFieldsPrepared(), array(
			'action' => 'edit',
			'firstVisitText' => $firstVisitText,
			'profileEditError' => Messenger::get()->ns('profile-edit')->getAll(),
			'checkVomsMessage' => Messenger::get()->ns('check-voms')->getAll(),
			'setDefaultVomsMessage' => Messenger::get()->ns('set-default-voms')->getAll(),
			'checkSertMessage' => Messenger::get()->ns('check-cert')->getAll(),
			'projectList' => $projectList,
			'userProjects' => $user->getAllowedProjects(),
			'vomsList' => $vomsList,
			'userVoms' => $user->getAllowedVoms(),
			'defaultVoms' => $user->getDefaultVoms(),
			'softwareList' => SoftwareCollection::load()->getAll(),
			'userSoftware' => $user->getAllowedSoftware(),
			'myproxyServersList' => MyproxyServerCollection::load()->getAll(),
		));
		
		// echo '<pre>'; print_r($user->getDefaultVoms()) ;die;
		FrontendViewer::get()
			->setTitle('Редактирование личных данных')
			->setContentSmarty(self::TPL_PATH.'edit.tpl', $variables)
			->render();
	}
	
	// DISPLAY GREETING
	public function display_greeting($params = array()){
		
		if(!CurUser::get()->isLogged())
			App::redirectHref('');
	
		$variables = array();
		
		FrontendViewer::get()
			->setTitle('Добро пожаловать на сайт!')
			->setContentSmarty(self::TPL_PATH.'greeting.tpl', $variables)
			->render();
	}
	
	// DISPLAY STATISTICS
	public function display_home($params = array()){
		
		if(!CurUser::get()->isLogged())
			App::redirectHref('');
	
		$variables = array(
			'user_io' => CurUser::get()->getName('io')
		);
		
		FrontendViewer::get()
			->setTitle('Личный кабинет')
			->setContentSmarty(self::TPL_PATH.'home.tpl', $variables)
			->render();
	}
	
	// DISPLAY FORGET PASSWORD
	public function display_forget_password($params = array()){
		
		if(CurUser::get()->isLogged())
			App::redirectHref('profile/home');
	
		$variables = array();
		
		FrontendViewer::get()
			->setTitle('Восстановление пароля')
			->setContentSmarty(self::TPL_PATH.'forget_password.tpl', $variables)
			->render();
	}
	
	
	////////////////////
	////// ACTION //////
	////////////////////
	
	// ACTION LOGIN
	public function action_login($params = array()){
		
		try{
			CurUser::get()->login(getVar($_POST['email']), getVar($_POST['pass']), getVar($_POST['remember'], false, 'bool'));
			App::reload();
		}catch(Exception $e){
			Messenger::get()->ns('login')->addError($e->getMessage());
		}
	}
	
	// ACTION LOGOUT
	public function action_logout($params = array()){
		
		CurUser::logout();
		App::reload();
	}
	
	// ACTION REGISTRATION
	public function action_registration($params = array()){
		
		$user = CurUser::get();
		
		if($user->Save($_POST)){
			$user->login($user->getField('email'), $_POST['password']);
			App::redirectHref('profile/greeting');
			return TRUE;
		}else{
			Messenger::get()->addError('При регистрации возникли ошибки:', $user->getError());
			return FALSE;
		}
	}
	
	// ACTION EDIT PROFILE
	public function action_edit($params = array()){
		
		$user = CurUser::get();
		if($user->saveProfile($_POST)){
			Messenger::get()->ns('profile-edit')->addSuccess('Личные данные сохранены');
			return TRUE;
		}else{
			Messenger::get()->ns('profile-edit')->addError('Не удалось сохранить данные:<div style="margin-left: 10px; font-size: 13px;">'.$user->getError().'</div>');
			return FALSE;
		}
	}
	
	public function action_check_voms($params = array()){
		
		$user = CurUser::get();
		$num = $user->checkVoms(getVar($_POST['voms'], array(), 'array'));
		Messenger::get()->ns('check-voms')->addSuccess('Вы состоите в '.$num.' виртуальных организаций из выбранных.');
		return TRUE;
	}
	
	public function action_save_default_voms($params = array()){
		
		$user = CurUser::get();
		$num = $user->setDefaultVoms(getVar($_POST['projects'], array(), 'array'));
		
		Messenger::get()->ns('set-default-voms')->addSuccess('Виртуальные организации выбраны.');
		return TRUE;
	}
	
	public function action_check_cert($params = array()){
		
		$user = CurUser::get();
		
		// ручная авторизация
		if(!empty($_POST['manual-login']) && !$user->getField('myproxy_manual_login')){
			$user->setManualMyproxyLogon(TRUE);
			Messenger::get()->ns('check-cert')->addInfo('Выбран ручной способ авторизации');
			return TRUE;
		}
		
		// автоматическая авторизация
		if(empty($_POST['manual-login']) && $user->getField('myproxy_manual_login')){
			$user->setManualMyproxyLogon(FALSE);
			Messenger::get()->ns('check-cert')->addInfo('Выбран автоматический способ авторизации');
		}
		
		if($user->getField('myproxy_manual_login'))
			return TRUE;
		
		
		try{
			$connector = MyproxyConnector::createByConnectForm($_POST);
			if($user->checkCert($connector)){
				Messenger::get()->ns('check-cert')->addSuccess('Авторизационные данные сохранены');
				return TRUE;
			}else{
				Messenger::get()->ns('check-cert')->addError($user->getError());
				return FALSE;
			}
		}catch(Exception $e){
			Messenger::get()->addError(Lng::get('task.warnings'), $e->getMessage());
			return FALSE;
		}
	}
	
	// ACTION SET NEW PASSWORD
	public static function action_set_new_password($params = array()){
	
		$user = CurUser::get();
		
		$oldPassword = getVar($_POST['oldPassword']);
		$newPassword = getVar($_POST['newPassword']);
		$newPasswordConfirm = getVar($_POST['newPasswordConfirm']);
		$messenger = Messenger::get()->ns('password-change');
		
		if(!strlen($oldPassword) || !strlen($newPassword) || !strlen($newPasswordConfirm)){
			$messenger->addError('Заполните все поля');
			return FALSE;
		}
		
		$messenger = Messenger::get()->ns('password-change');
		
		if($user->setNewPassword($oldPassword, $newPassword, $newPasswordConfirm)){
			$messenger->addSuccess('Пароль обновлен.');
			return TRUE;
		}else{
			$messenger->addError('Не удалось обновить пароль:', $user->getError());
			return FALSE;
		}
	}

	
	//////////////////
	////// AJAX //////
	//////////////////

	// AJAX PING
	public function ajax_ping($params = array()){
		
		echo 'ok';
	}
	
	// AJAX CHECK EMAIL
	public static function ajax_check_email($params = array()){
		
		$email = isset($_GET['email']) ? $_GET['email'] : '';
		echo (preg_match(Common::getRegExp('email'), $email) && !User::isEmailInUse($email)) ? 'true' : 'false';
	}

	// AJAX SAVE USER STATISTICS
	public static function ajax_save_user_stat($params = array()){
		
		if(
			preg_match('/^[\w ]{0,20}$/', getVar($_POST['browser_name'])) &&
			preg_match('/^[\d\.]{0,10}$/', getVar($_POST['browser_version']))
		){
			UserStatistics::get()->saveClientSideStatistics(
				$_POST['browser_name'],
				$_POST['browser_version'],
				getVar($_POST['screen_width'], 0, 'int'),
				getVar($_POST['screen_height'], 0, 'int')
			);
			echo 'ok';
		}else{
			echo 'Недопустимые символы в имени или версии браузера';
		}
	}


}

?>