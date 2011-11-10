
<h2><?= $this->pageTitle; ?></h2>

<div>
	<a href="">Название</a>
	<a href="">Редактирование</a>
	<a href="">Запуск</a>
</div>

<form id="edit-form" action="" method="post">
	<?= FORMCODE ?>
	<input type="hidden" name="project_id" value="<?= $this->projectId; ?>" />
	
	<table style="margin: auto; text-align: left;" border>
	<tr>
		<td><?= Lng::get('edit.project'); ?></td>
		<td><b><?= $this->projectName; ?></b></td>
	</tr>
	
	<tr>
		<td><?= Lng::get('edit.name'); ?></td>
		<td><input type="text" name="name" value="<?= $this->name; ?>" /></td>
	</tr>
	
	<tr>
		<td><?= Lng::get('edit.profile'); ?></td>
		<td>
			<select name="profile_id">
				<option value="">-</option>
				<? foreach($this->profileList as $p): ?>
					<option value="<?= $p['id']; ?>"><?= $p['name']; ?></option>
				<? endforeach; ?>
			</select>
		</td>
	</tr>
	
	</table>
	
	<div class="paragraph">
		<input class="button" type="submit" name="action[task-set/create][task-set/customize]" value="<?= Lng::get('save'); ?>" />
		<a class="button" href="<?= href('task-set'); ?>"><?= Lng::get('task.delete-5'); ?></a>
	</div>
</form>

<script type="text/javascript">

$(function(){

});

</script>
