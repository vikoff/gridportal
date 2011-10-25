
<div class="options-row">
	<a href="<?= href('admin/content/task-set/new'); ?>">Добавить запись</a>
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
		<th><?= $this->sorters['ready_to_start']; ?></th>
		<th><?= $this->sorters['num_submits']; ?></th>
		<th><?= $this->sorters['create_date']; ?></th>
		<th>Опции</th>
	</tr>
	<? foreach($this->collection as $item): ?>	
	<tr>
		<td><?= $item['id']; ?></td>
		<td><?= $item['uid']; ?></td>
		<td><?= $item['project_id']; ?></td>
		<td><?= $item['profile_id']; ?></td>
		<td><?= $item['name']; ?></td>
		<td><?= $item['ready_to_start']; ?></td>
		<td><?= $item['num_submits']; ?></td>
		<td><?= $item['create_date']; ?></td>
			
		<td class="center">
			<div class="tr-hover-visible options">
				<a href="<?= href('task-set/view/'.$item['id']); ?>" title="Просмотреть"><img src="images/backend/icon-view.png" alt="Просмотреть" /></a>
				<a href="<?= href('admin/content/task-set/edit/'.$item['id']); ?>" title="Редактировать"><img src="images/backend/icon-edit.png" alt="Редактировать" /></a>
				<a href="<?= href('admin/content/task-set/delete/'.$item['id']); ?>" title="Удалить"><img src="images/backend/icon-delete.png" alt="Удалить" /></a>
			</div>
		</td>
	</tr>
	<? endforeach; ?>	
	</table>
<? else: ?>
	<p>Сохраненных записей пока нет.</p>
<? endif; ?>

<?= $this->pagination; ?>