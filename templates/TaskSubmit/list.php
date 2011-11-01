
<?= $this->pagination; ?>

<? if($this->collection): ?>
	<table>
	<tr>
		<th>id</th>
		<th>Набор</th>
		<th>Порядковый номер</th>
		<th>Сатус</th>
		<th>Отправлена</th>
		<th>Завершена</th>
		<th>Получена</th>
		<th>Дата запуска</th>
		<th>Дата завершения</th>
		
		<th>опции</th>
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
		
		<td style="font-size: 11px;">
			<a href="<?= href('task-submit/view/'.$item['id']); ?>">Подробней</a>
		</td>
	</tr>
	<? endforeach; ?>	
	</table>
	
<? else: ?>
	<p>Сохраненных записей пока нет.</p>
<? endif; ?>

<?= $this->pagination; ?>