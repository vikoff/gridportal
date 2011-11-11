
<?= $this->pagination; ?>

<? if($this->collection): ?>
	<table class="std-grid">
	<tr>
		<th>id</th>
		<th>Проект</th>
		<th>Профиль</th>
		<th>Имя набора</th>
		<th>Количество запусков</th>
		<th>Дата создания</th>
		
		<th>опции</th>
	</tr>
	<? foreach($this->collection as $item): ?>	
	<tr>
		<td><?= $item['id']; ?></td>
		<td><?= $item['project_id']; ?></td>
		<td><?= $item['profile_id']; ?></td>
		<td><?= $item['name']; ?></td>
		<td><?= $item['num_submits']; ?></td>
		<td><?= $item['create_date_str']; ?></td>
		
		<td style="font-size: 11px;">
			<a href="<?= href('task-set/view/'.$item['id']); ?>">просмотр</a>
			<a href="<?= href('task-set/customize/'.$item['id']); ?>">запуск</a>
		</td>
	</tr>
	<? endforeach; ?>	
	</table>
	
<? else: ?>
	<p>Сохраненных записей пока нет.</p>
<? endif; ?>

<?= $this->pagination; ?>