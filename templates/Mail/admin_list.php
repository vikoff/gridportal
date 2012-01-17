
<div class="options-row">
	<a href="<?= href('admin/content/mail/new'); ?>">Добавить запись</a>
</div>

<?= $this->pagination; ?>

<? if($this->collection): ?>
	<table class="std-grid tr-highlight">
	<tr>
		<th><?= $this->sorters['id']; ?></th>
		<th><?= $this->sorters['uid']; ?></th>
		<th><?= $this->sorters['email']; ?></th>
		<th><?= $this->sorters['title']; ?></th>
		<th><?= $this->sorters['text']; ?></th>
		<th><?= $this->sorters['add_date']; ?></th>
		<th><?= $this->sorters['send_date']; ?></th>
		<th>Опции</th>
	</tr>
	<? foreach($this->collection as $item): ?>	
	<tr>
		<td><?= $item['id']; ?></td>
		<td><?= $item['uid']; ?></td>
		<td><?= $item['email']; ?></td>
		<td><?= $item['title']; ?></td>
		<td><?= $item['text']; ?></td>
		<td><?= $item['add_date']; ?></td>
		<td><?= $item['send_date']; ?></td>
			
		<td class="center">
			<div class="tr-hover-visible options">
				<a href="<?= href('mail/view/'.$item['id']); ?>" title="Просмотреть"><img src="images/backend/icon-view.png" alt="Просмотреть" /></a>
				<a href="<?= href('admin/content/mail/edit/'.$item['id']); ?>" title="Редактировать"><img src="images/backend/icon-edit.png" alt="Редактировать" /></a>
				<a href="<?= href('admin/content/mail/delete/'.$item['id']); ?>" title="Удалить"><img src="images/backend/icon-delete.png" alt="Удалить" /></a>
			</div>
		</td>
	</tr>
	<? endforeach; ?>	
	</table>
<? else: ?>
	<p>Сохраненных записей пока нет.</p>
<? endif; ?>

<?= $this->pagination; ?>