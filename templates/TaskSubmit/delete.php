<p>

	<?= Lng::get('task.delete-1'); ?> <b><?= $this->name; ?></b> <?= Lng::get('task.delete-3'); ?>?

	<form action="<?= href('task-set/view/'.$this->set_id); ?>" method="post">
		<input type="hidden" name="id" value="<?= $this->instanceId; ?>" />
		<?= FORMCODE; ?>
		
		<input class="button" type="submit" name="action[task-submit/delete]" value="<?= Lng::get('task.delete-4'); ?>" />
		<a class="button" href="<?= href('task-set/view/'.$this->set_id);?>"><?= Lng::get('task.delete-5'); ?></a>
	</form>

</p>