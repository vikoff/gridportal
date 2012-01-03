
<div class="options-row">
	<a href="<?= href('admin/content/task-set/new'); ?>"><?= Lng::get('admin_list-add-record'); ?></a>
</div>

<?= $this->pagination; ?>

<? if($this->collection): ?>
	<table class="std-grid tr-highlight">
	<tr>
		<th><?= $this->sorters['id']; ?></th>
		<th><?= $this->sorters['uid']; ?></th>
		<th><?= $this->sorters['project_id']; ?></th>
		<th><?= $this->sorters['profile_id']; ?></th>
		<th><?= $this->sorters['name']; ?></th>
		<?/*<th><?= $this->sorters['ready_to_start']; ?></th>*/?>
		<th><?= $this->sorters['num_submits']; ?></th>
		<th><?= $this->sorters['create_date']; ?></th>
		<th><?= Lng::get('admin_list-options');?></th>
	</tr>
	<? foreach($this->collection as $item): ?>	
	<tr>
		<td><?= $item['id']; ?></td>
		<td><?= $item['uid']; ?></td>
		<td><?= $item['project_id']; ?></td>
		<td><?= $item['profile_id']; ?></td>
		<td><?= $item['name']; ?></td>
		<?/*<td><?= $item['ready_to_start']; ?></td>*/?>
		<td><?= $item['num_submits']; ?></td>
		<td><?= $item['create_date']; ?></td>
			
		<td class="center">
			<div class="tr-hover-visible options">
				<a href="<?= href('task-set/view/'.$item['id']); ?>" title="<?= Lng::get('admin_list-view'); ?>"><img src="images/backend/icon-view.png" alt="<?= Lng::get('admin_list-view'); ?>/></a>
				<a href="<?= href('admin/content/task-set/edit/'.$item['id']); ?>" title="<?= Lng::get('admin_list-edit'); ?>"><img src="images/backend/icon-edit.png" alt="<?= Lng::get('admin_list-edit'); ?>" /></a>
				<a href="<?= href('admin/content/task-set/delete/'.$item['id']); ?>" title="<?= Lng::get('task.delete'); ?>"><img src="images/backend/icon-delete.png" alt="<?= Lng::get('task.delete'); ?>"/></a>
			</div>
		</td>
	</tr>
	<? endforeach; ?>	
	</table>
<? else: ?>
	<p><?= Lng::get('admin_list-not-save-record'); ?></p>
<? endif; ?>

<?= $this->pagination; ?>