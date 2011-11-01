
<form id="edit-form" action="" method="post">
	<?= FORMCODE ?>
	<input type="hidden" name="project_id" value="<?= $this->projectId; ?>" />
	
	<h2><?= Lng::get('edit.addNewTask'); ?></h2>
	
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
		<td>Тип</td>
		<td style="text-align: left;">
			<label><input type="radio" name="type" value="single" /> Одиночная задача</label><br />
			<label><input type="radio" name="type" value="batch" /> Пакетная задача</label>
		</td>
	</tr>
	
	<tr>
		<td>Файлы запуска задачи</td>
		<td style="text-align: left;">
			
			<label><input type="checkbox" name="useDefaultFiles" value="1" /> Использовать предустановленный пакет файлов запуска и моделей</label>
		</td>
	</tr>
	</table>
	
	<div class="paragraph">
		<input class="button" type="submit" name="action[task/create][]" value="<?= Lng::get('save'); ?>" />
		<a class="button" href="<?= href('task/list'); ?>"><?= Lng::get('task.delete-5'); ?></a>
	</div>
</form>

<script type="text/javascript">

$(function(){

});

</script>
