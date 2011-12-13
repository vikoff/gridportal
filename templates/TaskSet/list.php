
<?= $this->pagination; ?>

<? if($this->collection): ?>
	<table class="std-grid">
	<tr>
		<th><?= $this->sorters['name']; ?></th>
		<th><?= $this->sorters['project_id']; ?></th>
		<th><?= $this->sorters['profile_id']; ?></th>
		<th><?= $this->sorters['num_submits']; ?></th>
		<th><?= $this->sorters['create_date']; ?></th>
		
		<th><?= Lng::get('options'); ?></th>
	</tr>
	<? foreach($this->collection as $i => $item): ?>	
	<tr class="<?= $i % 2 ? 'odd' : 'even' ?>">
		<td><a href="<?= href('task-set/view/'.$item['id']); ?>"><?= $item['name']; ?></a></td>
		<td><?= $item['project_id']; ?></td>
		<td><?= $item['profile_id']; ?></td>
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