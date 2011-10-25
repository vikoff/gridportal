
<div class="options-row">
	<a href="<?= href('admin/content/myproxy-server/new'); ?>">Добавить запись</a>
</div>

<?= $this->pagination; ?>

<? if($this->collection): ?>
	<table class="std-grid tr-highlight">
	<tr>
		<th><?= $this->sorters['id']; ?></th>
		<th><?= $this->sorters['name']; ?></th>
		<th><?= $this->sorters['url']; ?></th>
		<th><?= $this->sorters['port']; ?></th>
		<th>Опции</th>
	</tr>
	<? foreach($this->collection as $item): ?>	
	<tr>
		<td><?= $item['id']; ?></td>
		<td><?= Lng::get($item['name']); ?></td>
		<td><?= Lng::get($item['url']); ?></td>
		<td><?= Lng::get($item['port']); ?></td>
			
		<td class="center">
			<div class="tr-hover-visible options">
				<a href="<?= href('myproxy-server/view/'.$item['id']); ?>" title="Просмотреть"><img src="images/backend/icon-view.png" alt="Просмотреть" /></a>
				<a href="<?= href('admin/content/myproxy-server/edit/'.$item['id']); ?>" title="Редактировать"><img src="images/backend/icon-edit.png" alt="Редактировать" /></a>
				<a href="<?= href('admin/content/myproxy-server/delete/'.$item['id']); ?>" title="Удалить"><img src="images/backend/icon-delete.png" alt="Удалить" /></a>
			</div>
		</td>
	</tr>
	<? endforeach; ?>	
	</table>
<? else: ?>
	<p>Сохраненных записей пока нет.</p>
<? endif; ?>

<?= $this->pagination; ?>