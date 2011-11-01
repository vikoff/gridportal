
<div class="options-row">
	<a href="<?= href('admin/content/task-submit/new'); ?>">Добавить запись</a>
</div>

<?= $this->pagination; ?>

<? if($this->collection): ?>
	<table class="std-grid tr-highlight">
	<tr>
		<th><?= $this->sorters['id']; ?></th>
		<th><?= $this->sorters['set_id']; ?></th>
		<th><?= $this->sorters['index']; ?></th>
		<th><?= $this->sorters['status']; ?></th>
		<th><?= $this->sorters['is_submitted']; ?></th>
		<th><?= $this->sorters['is_completed']; ?></th>
		<th><?= $this->sorters['is_fetched']; ?></th>
		<th><?= $this->sorters['start_date']; ?></th>
		<th><?= $this->sorters['finish_date']; ?></th>
		<th>Опции</th>
	</tr>
	<? foreach($this->collection as $item): ?>	
	<tr>
		<td><?= $item['id']; ?></td>
		<td><?= $item['set_id']; ?></td>
		<td><?= $item['index']; ?></td>
		<td><?= $item['status']; ?></td>
		<td><?= $item['is_submitted']; ?></td>
		<td><?= $item['is_completed']; ?></td>
		<td><?= $item['is_fetched']; ?></td>
		<td><?= $item['start_date']; ?></td>
		<td><?= $item['finish_date']; ?></td>
			
		<td class="center">
			<div class="tr-hover-visible options">
				<a href="<?= href('task-submit/view/'.$item['id']); ?>" title="Просмотреть"><img src="images/backend/icon-view.png" alt="Просмотреть" /></a>
				<a href="<?= href('admin/content/task-submit/edit/'.$item['id']); ?>" title="Редактировать"><img src="images/backend/icon-edit.png" alt="Редактировать" /></a>
				<a href="<?= href('admin/content/task-submit/delete/'.$item['id']); ?>" title="Удалить"><img src="images/backend/icon-delete.png" alt="Удалить" /></a>
			</div>
		</td>
	</tr>
	<? endforeach; ?>	
	</table>
<? else: ?>
	<p>Сохраненных записей пока нет.</p>
<? endif; ?>

<?= $this->pagination; ?>