
<div>{a href="admin/users/list" text="Вернуться к списку"}</div>

<h2>
	Пользователь {$name} {$surname}
	{if !$active}<span class="simple-text red">заблокирован</span>{/if}
</h2>

<table class="std-grid">
<tr>
	<td class="title">email</td>
	<td class="data">{$profile.email}</td>
</tr>
<tr>
	<td class="title">Фамилия</td>
	<td class="data">{$surname}</td>
</tr>
<tr>
	<td class="title">Имя</td>
	<td class="data">{$name}</td>
</tr>
<tr>
	<td class="title">dn</td>
	<td class="data">{$dn}</td>
</tr>
<tr>
	<td class="title">dn_cn</td>
	<td class="data">{$dn_cn}</td>
</tr>
<tr>
	<td class="title">Права</td>
	<td class="data">
	{if $perms.allowEdit}
		<form class="inline" action="" method="post">
			{$formcode}
			<input type="hidden" name="instance-id" value="{$instanceId}" />
			<select name="level">{$perms.list}</select>
			<input type="submit" name="action[user/save-perms]" value="Сохранить" />
		</form>
	{else}
		{$perms.curTitle}
	{/if}
	</td>
</tr>
<tr>
	<td class="title">Дата регистрации</td>
	<td class="data">{$regdate}</td>
</tr>
<tr>
	<td>Действия</td>
	<td class="actions">
		<a class="button" href="{a href=admin/users/list}">Вернуться к списку</a>
		<a class="button" href="{a href=admin/users/delete/$instanceId}">Удалить пользователя</a>
		{if $active}
			<form class="inline" action="" method="post" onsubmit="return confirm('Заблокировать пользователя?');">
				{$formcode}
				<input type="hidden" name="instance-id" value="{$instanceId}" />
				<input class="button" type="submit" name="action[user/ban]" value="Заблокировать" />
			</form>
		{else}
			<form class="inline" action="" method="post" onsubmit="return confirm('Разблокировать пользователя?');">
				{$formcode}
				<input type="hidden" name="instance-id" value="{$instanceId}" />
				<input class="button" type="submit" name="action[user/unban]" value="Разблокировать" />
			</form>
		{/if}
	</td>
</tr>
</table>
