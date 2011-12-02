
<p>
<?= Lng::get('xrls_edit.get-task'); ?> <b><?= $this->name; ?></b>?
</p>

<form action="" method="post">
	<input type="hidden" name="id" value="<?= $this->instanceId; ?>" />
	<?= FORMCODE ?>
	
	<?= $this->myproxyLoginForm; ?>
	
	<input class="button" type="submit" name="action[task-submit/get-results][task-submit/analyze?submit=<?= $this->id; ?>]" value="<?= Lng::get('xrls_edit.get'); ?>" />
	<a class="button" href="<?= href('task-set/view/'.$this->set_id); ?>"><?= Lng::get('xrls_edit.cancel'); ?></a>
</form>
