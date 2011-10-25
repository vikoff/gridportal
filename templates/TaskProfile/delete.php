
<div style="text-align: center;">

	<div class="paragraph">

		Хотите удалить запись #<?= $this->instanceId; ?>		

		id: <?= $this->id; ?>, 
		is_user_defined: <?= $this->is_user_defined; ?>, 
		uid: <?= $this->uid; ?>, 
		Имя профиля: <?= $this->name; ?>, 
		Проект: <?= $this->project_id; ?>, 
		create_date: <?= $this->create_date; ?>, 
		
		безвозвратно?

	</div>
	
	<div class="paragraph">
		<form action="" method="post">
			<input type="hidden" name="id" value="<?= $this->instanceId; ?>" />
			<?= FORMCODE; ?>			
			<input class="button" type="submit" name="action[task-profile/delete]" value="Удалить" />
			<a class="button" href="<?= href('admin/content/task-profile'); ?>">Отмена</a>
		</form>
	</div>
	
</div>
