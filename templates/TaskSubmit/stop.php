
<p>
<?= Lng::get('task-submit-stop-task') ?> <b><?= $this->name; ?></b> ?<br />
<span style="font-weight: bold;"><?= Lng::get('task-submit.stop-attention') ?></span> <?= Lng::get('task-submit.afte-stop-all-file-delete') ?>
</p>

<form action="<?= href('task-set/view/'.$this->set_id); ?>" method="post">
	<input type="hidden" name="id" value="<?= $this->instanceId; ?>" />
	<?= FORMCODE ?>
	
	<?= $this->myproxyLoginForm; ?>

	<input class="button" type="submit" name="action[task-submit/stop]" value="<?= Lng::get('task.stop') ?>" />
	<a class="button" href="<?= href('task-set/list'); ?>"><?= Lng::get('task.delete-5') ?></a>
</form>
