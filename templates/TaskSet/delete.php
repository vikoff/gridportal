
<div style="text-align: center;">

	<div class="paragraph">

		Хотите удалить запись #<?= $this->instanceId; ?>		

		id: <?= $this->id; ?>, 
		uid: <?= $this->uid; ?>, 
		Проект: <?= $this->project_id; ?>, 
		Профиль: <?= $this->profile_id; ?>, 
		Имя набора: <?= $this->name; ?>, 
		ready_to_start: <?= $this->ready_to_start; ?>, 
		num_submits: <?= $this->num_submits; ?>, 
		create_date: <?= $this->create_date; ?>, 
		
		безвозвратно?

	</div>
	
	<div class="paragraph">
		<form action="" method="post">
			<input type="hidden" name="id" value="<?= $this->instanceId; ?>" />
			<?= FORMCODE; ?>			
			<input class="button" type="submit" name="action[task-set/delete]" value="Удалить" />
			<a class="button" href="<?= href('admin/content/task-set'); ?>">Отмена</a>
		</form>
	</div>
	
</div>
