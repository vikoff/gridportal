
<form id="regForm" name="regForm" action="" method="post">
	{$formcode}
	<input type="hidden" name="action" value="profile/{$action}" />
	
	<table class='reg_box' border>
		<tbody>
		<tr>
			<td colspan='2'>
				<h3>
				{if $action == 'registration'}
					Регистрация нового пользователя
				{else}
					Изменение личных данных
				{/if}
				</h3>
				
				{if $userError}
					{$userError}
				{/if}
			</td>
		</tr>

	{if $action == 'registration'}

		<tr>
			<td class="left">E-mail:<span class="required">*</span></td>
			<td><input type="text" name="email" value="{$email}"></td>
		</tr>
		<tr>
			<td class="left">Пароль<span class="required">*</span><br />(не менее 5 символов):</td>
			<td><input type="password" name="password" value="{$password}"></td>
		</tr>
		<tr>
			<td class="left">Подтверждение пароля:<span class="required">*</span></td>
			<td><input type="password" name="password_confirm" value="{$password_confirm}"></td>
		</tr>
		
	{/if}

		<tr>
			<td class="left">Фамилия:</td>
			<td><input type="text" name="surname" value="{$surname}"></td>
		</tr>
		<tr>
			<td class="left">Имя:</td>
			<td><input type="text" name="name" value="{$name}"></td>
		</tr>
		<tr>
			<td class="left">Отчество:</td>
			<td><input type="text" name="patronymic" value="{$patronymic}"></td>
		</tr>
		<tr>
			<td class="left">Пол:</td>
			<td>
				<select name="sex">
					<option value="none" {if $sex == 'none'}selected="selected"{/if}> </option>
					<option value="man" {if $sex == 'man'}selected="selected"{/if}>Мужской</option>
					<option value="woman" {if $sex == 'woman'}selected="selected"{/if}>Женский</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="left">Дата рождения:</td>
			<td>
				Число <select name="birth_day"><option value="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>{$days_list}</select>
				Месяц <select name="birth_month"><option value="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>{$months_list}</select>
				Год <select name="birth_year"><option value="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option>{$years_list}</select>
			</td>
		</tr>
		<tr>
			<td class="left">Страна:</td>
			<td><select name="country"><option value="" class="grey">Выберите страну...</option>{$countries_list}</select></td>
		</tr>
		<tr>
			<td class="left">Город:</td>
			<td><input type="text" name="city" value="{$city}"></td>
		</tr>
		
	{if $action == 'registration'}
		<tr>
			<td class="left">Введите цифры с картинки:</td>
			<td>
				<div class="captchaBox">
					<img id="captcha" src="includes/captcha/captcha.php"/>
					<a href="#" onclick="captcha_reload(); return false;">Обновить</a>
				</div>
				<div class="captchaInput">
					<input type="text" name="captcha" value="" class="ignore" style="width: 100px;">
				</div>
			</td>
		</tr>
		
	{/if}

		<tr>
			<td align="center" colspan="2">
				<input type="submit" value="Сохранить">
			</td>
		</tr>
		</tbody>
	</table>
	
	<script type="text/javascript" src="js/jquery.validate.pack.js"></script>
	
	<script type="text/javascript">
		
		$(function(){
			
			$("#regForm").validate({
				ignore: ".ignore",
				{{$jsRules}}
			});
			
			{{if $action == 'registration'}}
			
			// EMAIL CHECK //
			
			$(document.regForm.email).rules("add", {
				remote: "ajax.php?r=profile/check_email",
				messages: {
					remote: 'Данные email-адрес уже используется, возможно Вам следует воспользатся функцией <a href="{{$WWW_PREFIX}}profile/forget-password">восстановления учетной записи</a>'
				}
			});
			
			{{/if}}
			
		});
		
	</script>
</form>

{if $action == 'edit'}

<form name="passwordForm" action="" method="POST">
	{$formcode}
	<input type="hidden" name="action" value="profile/set_new_password" />
	<table class='reg_box'>
		<tbody>
		<tr>
			<td colspan='2'>
				<h3>
					Изменение пароля
				</h3>
				{if $passwordError}
					{$passwordError}
				{/if}
			</td>
		</tr>
		<tr>
			<td class="left">Старый пароль:</td>
			<td><input type="password" name="oldPassword" value=""></td>
		</tr>
		<tr>
			<td class="left">Новый пароль:</td>
			<td><input type="password" name="newPassword" value=""></td>
		</tr>
		<tr>
			<td class="left">Подтверждение пароля:</td>
			<td><input type="password" name="newPasswordConfirm" value=""></td>
		</tr>
		<tr>
			<td class="left"></td>
			<td><input type="submit" name="" value="Изменить пароль"></td>
		</tr>
		</tbody>
	</table>
</form>
{/if}

