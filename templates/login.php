<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
     "http://www.w3.org/TR/html4/strict.dtd"><html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<title><?=$this->_getHtmlTitle();?></title>
	<base href="<?=$this->_getHtmlBaseHref();?>" />
	<link rel="stylesheet" href="css/common.css" type="text/css" />
	<link rel="stylesheet" href="css/backend.css" type="text/css" />
</head>
<body>

<div id="login-screen">
	
	<div class="explain">Пожалуйста, авторизуйтесь.</div>
	
	<?=$errorMessage;?>
	
	<form action="" method="post">
		<?=FORMCODE;?>
		
		<table>
		<tr><td>Логин</td><td><input type="text" name="email" value="" style="width: 100%;" /></td></tr>
		<tr><td>Пароль</td><td><input type="password" name="pass" value="" style="width: 100%;" /></td></tr>
		<tr>
			<td></td>
			<td>
				<input type="checkbox" name="remember" id="rememberme-checkbox" value="1" />
				<label for="rememberme-checkbox">Запомнить меня</label>
				<div style="float: right;"><input type="submit" class="submit" name="action[profile/login]" value="Войти"></div>
			</td>
		</tr>
		</table>
	</form>
	
</div>

</body>
</html>