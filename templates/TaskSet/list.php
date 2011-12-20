
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
	<? $j = 0; foreach($this->collection as $i => $item): ?>	
	<tr class="<?= $j % 2 ? 'odd' : 'even' ?>">
		<td><a href="<?= href('task-set/view/'.$item['id']); ?>"><?= $item['name']; ?></a></td>
		<td><?= $item['project_name']; ?></td>
		<td><?= !empty($item['profile_name']) ? $item['profile_name'] : '-'; ?></td>
		<td onmouseover="showStatistics(this, <?= $item['num_submits']; ?>, <?= $item['num_finished']; ?>, <?= $item['num_processing']; ?>, <?= $item['num_errors']; ?>, <?= $item['num_undefined']; ?>);" onmouseout="hideStatistics(this);">
			<span style="font-size: 11px;" />всего: <?= $item['num_submits']; ?></span>
			<div class="task-progress">
				<? if ($item['num_errors']){ ?><div class="task-progress-item task-state-6" style="width:<?= $item['num_errors'] / $item['num_submits'] * 100 ?>%"></div><? } ?>
				<? if ($item['num_finished']){ ?><div class="task-progress-item task-state-4" style="width:<?= $item['num_finished'] / $item['num_submits'] * 100 ?>%"></div><? } ?>
				<? if ($item['num_processing']){ ?><div class="task-progress-item task-state-5" style="width:<?= $item['num_processing'] / $item['num_submits'] * 100 ?>%"></div><? } ?>
				<? if ($item['num_undefined']){ ?><div class="task-progress-item task-state-1" style="width:<?= $item['num_undefined'] / $item['num_submits'] * 100 ?>%"></div><? } ?>
				<div class="cl"></div>
			</div>
		</td>
		<td><?= $item['create_date_str']; ?></td>
		
		<td style="font-size: 11px;">
			<a href="<?= href('task-set/view/'.$item['id']); ?>">просмотр</a>
			<a href="<?= href('task-set/customize/'.$item['id']); ?>">запуск</a>
		</td>
	</tr>
	<? $j++; endforeach; ?>	
	</table>
	
<? else: ?>
	<p>Сохраненных записей пока нет.</p>
<? endif; ?>

<?= $this->pagination; ?>

<script type="text/javascript">
function showStatistics(elm, num_submits, num_finished, num_processing, num_errors, num_undefined){
	showPopup(elm, ''
		+'<table class="l" style="width:auto">'
			+'<tr><td>Всего: </td><td><b>'+num_submits+'</b></td></tr>'
			+'<tr><td>Завершено: </td><td><b>'+num_finished+'</b></td></tr>'
			+'<tr><td>В процессе: </td><td><b>'+num_processing+'</b></td></tr>'
			+'<tr><td>С ошибками: </td><td><b>'+num_errors+'</b></td></tr>'
			+'<tr><td>Неизвестно: </td><td><b>'+num_undefined+'</b></td></tr>'
		+'</table>');
}
function hideStatistics(elm){
	hidePopup(elm);
}
</script>