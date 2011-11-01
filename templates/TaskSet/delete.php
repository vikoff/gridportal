
<div style="text-align: center;">

	<div class="paragraph">

		Хотите удалить задачу <b><?= $this->name; ?></b> (#<?= $this->instanceId; ?>) безвозвратно?

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
