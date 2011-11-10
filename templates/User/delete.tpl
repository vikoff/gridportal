
<p>
	Вы уверены, что хотите удалить пользователя {$name} {$surname}
</p>

<p>
<form action='{a href="admin/users/list"}' method="post">
	<input type="hidden" name="id" value="{$instanceId}" />
	{$formcode}
	
	<input class="button" type="submit" name="action[user/delete]" value="Удалить безвозвратно" />
	<input class="button" type="submit" name="cancel" value="Отмена" />
</form>
</p>
