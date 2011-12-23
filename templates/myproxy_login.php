
<? if($this->showMyproxyLogin): ?>
	<table class="std-grid narrow myproxy-login">
	<tr>
		<td><?= Lng::get('xrls_edit.server'); ?></td>
		<td>
			<select name="server">
				<? foreach($this->myproxyServersList as $item): ?>
					<option value="<?= $item['id']; ?>"><?= $item['name']; ?></option>
				<? endforeach; ?>
				<option value="custom">ввести сервер вручную</option>
			</select>
		</td>
	</tr>
	<tbody id="custom-server-block" style="display: none;">
		<tr><td>сервер</td><td><input type="name" name="custom-server" value="" /></td></tr>
		<tr><td>порт</td><td><input type="name" name="custom-server-port" value="7512" /></td></tr>
	</tbody>
	<tr>
		<td><?= Lng::get('xrls_edit.username'); ?></td>
		<td><input type="text" name="login" value="" /></td>
	</tr>
	<tr>
		<td><?= Lng::get('xrls_edit.password'); ?></td>
		<td><input type="password" name="password" value="" /></td>
	</tr>
	<tr>
		<td><?= Lng::get('xrls_edit.max-time'); ?></td>
		<td><select name="lifetime"><option value="2850">2850</option><option value="1200">1200</option></select></td>
	</tr>
	</table>
	<script type="text/javascript">
	$(function(){
		$('select[name="server"]').change(function(){
			$('#custom-server-block')[$(this).val() == 'custom' ? 'show' : 'hide']()
		}).change();
	});
	</script>
<? else: ?>
	<input type="hidden" name="myproxy-autologin" value="1" />
<? endif; ?>
