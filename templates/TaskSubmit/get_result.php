
<p>
<?= Lng::get('xrls_edit.get-task'); ?> <b><?= $this->name; ?></b>?
</p>

<form action="" method="post">
	<input type="hidden" name="id" value="<?= $this->instanceId; ?>" />
	<?= FORMCODE ?>
	
	<? if($this->showMyproxyLogin): ?>
		<table class="std-grid narrow">
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
	
	<input class="button" type="submit" name="action[task-submit/get-results][task-submit/analyze?submit=<?= $this->id; ?>]" value="<?= Lng::get('xrls_edit.get'); ?>" />
	<a class="button" href="<?= href('task-set/view/'.$this->set_id); ?>"><?= Lng::get('xrls_edit.cancel'); ?></a>
</form>
