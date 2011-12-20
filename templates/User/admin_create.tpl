
<form name="edit-form" id="edit-form" action="" method="post">
	{$formcode}
	
	<h3>Создание новой записи</h3>
	<table class="std-grid">
	<tr>
		<td>Логин</td>
		<td><input type="text" name="login" /></td>
	</tr>
	<tr>
		<td>Пароль</td>
		<td><input type="text" name="password" /></td>
	</tr>
	<tr>
		<td>email</td>
		<td><input type="text" name="email" /></td>
	</tr>
	<tr>
		<td>Фамилия</td>
		<td><input type="text" name="surname" /></td>
	</tr>
	<tr>
		<td>Имя</td>
		<td><input type="text" name="name" /></td>
	</tr>
	<tr>
		<td>dn</td>
		<td><input type="text" name="dn" /> <span style="cursor:pointer;border-bottom:#3763fb 1px dashed;color:#3763fb;" onclick="document.forms['edit-form'].dn.value='/DC=org/DC=ugrid/O=people/O=UGRID/CN='+document.forms['edit-form'].name.value+' '+document.forms['edit-form'].surname.value">test</span></td>
	</tr>
	<tr>
		<td>dn_cn</td>
		<td><input type="text" name="dn_cn" /> <span style="cursor:pointer;border-bottom:#3763fb 1px dashed;color:#3763fb;" onclick="document.forms['edit-form'].dn_cn.value=document.forms['edit-form'].name.value+' '+document.forms['edit-form'].surname.value">fill</span></td>
	</tr>
	<tr>
		<td>Права</td>
		<td><select name="level">{$perms}</select></td>
	</tr>
	<tr>
		<td>Активация</td>
		<td>
			<select name="active">
				<option value="0">активный</option>
				<option value="0">неактивный</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>Действия</td>
		<td class="actions">
		
			<input class="button" type="submit" name="action[user/create]" />
			<a class="button" href="{a href=admin/users}">отмена</a>
			
		</td>
	</tr>
	</table>
</form>

<script type="text/javascript">

$(function(){
	
	$("#next-action-select").val("{{$redirect}}");
	
	$("#edit-form").validate({{$validation}});

});

</script>
