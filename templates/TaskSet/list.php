
<?= $this->pagination; ?>

<? if($this->collection): ?>
	<table class="std-grid">
	<tr>
		<th>id</th>
		<th>uid</th>
		<th>Проект</th>
		<th>Профиль</th>
		<th>Имя набора</th>
		<th>ready_to_start</th>
		<th>num_submits</th>
		<th>create_date</th>
		
		<th>опции</th>
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
		
		<td style="font-size: 11px;">
			<a href="<?= href('task-set/customize/'.$item['id']); ?>">запуск</a>
		</td>
	</tr>
	<? endforeach; ?>	
	</table>
	
<? else: ?>
	<p>Сохраненных записей пока нет.</p>
<? endif; ?>

<?= $this->pagination; ?>