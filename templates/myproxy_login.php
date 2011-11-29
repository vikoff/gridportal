
<? if($this->showMyproxyLogin): ?>
	<table class="std-grid narrow myproxy-login">
	<tr>
		<td><?= Lng::get('xrls_edit.server'); ?></td>
		<td>
			<select name="server">
				<? foreach($this->myproxyServersList as $item): ?>
					<option value="<?= $item['id']; ?>"><?= $item['name']; ?></option>
				<? endforeach; ?>
			</select>
		</td>
	</tr>
	<tr>
		<td><?= Lng::get('xrls_edit.username'); ?></td>
		<td><input type="text" name="user[name]" value="" /></td>
	</tr>
	<tr>
		<td><?= Lng::get('xrls_edit.password'); ?></td>
		<td><input type="password" name="user[pass]" value="" /></td>
	</tr>
	<tr>
		<td><?= Lng::get('xrls_edit.max-time'); ?></td>
		<td><select name="lifetime"><option value="2850">2850</option><option value="1200">1200</option></select></td>
	</tr>
	</table>
<? else: ?>
	<input type="hidden" name="myproxy-autologin" value="1" />
<? endif; ?>
