
<div style="text-align: center;">

	<div class="paragraph">

		<?= Lng::get('task-set-delete.want-to-delete-a-tasktask') ?> <b><?= $this->name; ?></b> 
		<!--(#<?= $this->instanceId; ?>)-->
		<?= Lng::get('task-set-delete.never-to-return') ?>

	</div>
	
	<div class="paragraph">
		<form action="" method="post">
			<input type="hidden" name="id" value="<?= $this->instanceId; ?>" />
			<?= FORMCODE; ?>			
			<input class="button" type="submit" name="action[task-set/delete]" value="<?= Lng::get('task-set.delete') ?>" />
			<a class="button" href="<?= href('admin/content/task-set'); ?>"><?= Lng::get('task.delete-5') ?></a>
		</form>
	</div>
	
</div>
