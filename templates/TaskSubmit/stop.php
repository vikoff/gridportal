
<p>
Остановить задачу <b><?= $this->name; ?></b>?<br />
<span style="font-weight: bold;">ВНИМАНИЕ!</span> После остановки все файлы задачи будут удалены.
</p>

<form action="<?= href('task-set/view/'.$this->set_id); ?>" method="post">
	<input type="hidden" name="id" value="<?= $this->instanceId; ?>" />
	<?= FORMCODE ?>
	
	<?= $this->myproxyLoginForm; ?>

	<input class="button" type="submit" name="action[task-submit/stop]" value="Остановить" />
	<a class="button" href="<?= href('task-set/list'); ?>">Отмена</a>
</form>
