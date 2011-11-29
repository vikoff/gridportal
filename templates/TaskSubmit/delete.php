<p>
	
<? if(!empty($this->collection)): ?>

	<form action="<?= href('task-set/view/'.$this->setId); ?>" method="post">
		<?= FORMCODE; ?>
		
		<?= Lng::get('task.delete-1'); ?> <b><?= $this->name; ?></b> <?= Lng::get('task.delete-3'); ?>?
	
		<table style="text-align: left; margin: 1em auto;">
		<? foreach($this->collection as $item): ?>
			<tr><td>
				<?= $item['fullname']; ?>
				<input type="hidden" name="task[]" value="<?= $item['id']; ?>" />
			</td></tr>
		<? endforeach; ?>
		</table>
		
		<?= $this->myproxyLoginForm; ?>
		
		
		<input class="button" type="submit" name="action[task-submit/delete]" value="<?= Lng::get('task.delete-4'); ?>" />
		<a class="button" href="<?= href('task-set/view/'.$this->setId);?>"><?= Lng::get('task.delete-5'); ?></a>
	</form>
	
<? else: ?>
	Файлы не выбраны.
<? endif; ?>

</p>