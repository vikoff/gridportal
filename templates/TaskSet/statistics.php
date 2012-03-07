<h1 style="vertical-align:bottom;">
	<img src="/images/icons/statistics.gif" alt="<?= Lng::get('top-menu.results') ?>" title="<?= Lng::get('top-menu.results') ?>" align="center" width=32 height=32 onmouseover="this.src='/images/icons/statistics.a.gif'" onmouseout="this.src='/images/icons/statistics.gif'" />
	<?= Lng::get('top-menu.results') ?>
</h1>
<?= $this->pagination; ?>

<? if($this->collection): ?>
	<table class="std-grid">
	<tr>
		<th><?= $this->sorters['uid']; ?></th>
		<th><?= $this->sorters['name']; ?></th>
		<th><?= $this->sorters['project_id']; ?></th>
		<th><?= $this->sorters['profile_id']; ?></th>
		<th><?= $this->sorters['num_submits']; ?></th>
		<th><?= $this->sorters['create_date']; ?></th>
		
		<th><?= Lng::get('options'); ?></th>
	</tr>
	<? foreach($this->collection as $i => $item): ?>	
	<tr class="<?= $i % 2 ? 'odd' : 'even' ?>">
		<td><?= $item['user_name']; ?></td>
		<td><a href="<?= href('task-set/statistics/'.$item['id']); ?>"><?= $item['name']; ?></a></td>
		<td><?= $item['project_name']; ?></td>
		<td><?= !empty($item['profile_name']) ? $item['profile_name'] : '-'; ?></td>
		<td onmouseover="showStatistics(this, <?= $item['num_submits']; ?>, <?= $item['num_finished']; ?>, <?= $item['num_processing']; ?>, <?= $item['num_errors']; ?>, <?= $item['num_undefined']; ?>);" onmouseout="hideStatistics(this);">
			<span style="font-size: 11px;"><?= Lng::get('TaskSet-list-total'); ?> <?= $item['num_submits']; ?></span>
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
			<a href="<?= href('task-set/statistics/'.$item['id']); ?>"><?= Lng::get('TaskSet-list-view'); ?></a>
		</td>
	</tr>
	<? endforeach; ?>	
	</table>
	
<? else: ?>
	<p><?= Lng::get('tast-set-view.no-running-tasks'); ?></p>
<? endif; ?>

<?= $this->pagination; ?>

<div class="refresh-indicator">
	<span></span>
	&nbsp;
	<a href="<?= href('task-set/statistics'); ?>" onclick="refresh(0);return false"><img src="/images/refresh.png" alt="<?= Lng::get('TaskSet-view-update'); ?>" title="<?= Lng::get('TaskSet-view-update'); ?>" align="middle" /></a>
</div>

<script type="text/javascript">
autoUpdate(30, ".refresh-indicator :first");
function showStatistics(elm, num_submits, num_finished, num_processing, num_errors, num_undefined){
	showPopup(elm, ''
		+'<table class="l" style="width:auto">'
			+'<tr><td><?= Lng::get('TaskSet-list-total'); ?> </td><td><b>'+num_submits+'</b></td></tr>'
			+'<tr><td><?= Lng::get('TaskSet-list-complite'); ?> </td><td><b>'+num_finished+'</b></td></tr>'
			+'<tr><td><?= Lng::get('TaskSet-list-in-progres'); ?> </td><td><b>'+num_processing+'</b></td></tr>'
			+'<tr><td><?= Lng::get('TaskSet-list-with-erors'); ?> </td><td><b>'+num_errors+'</b></td></tr>'
			+'<tr><td><?= Lng::get('TaskSet-list-unknown'); ?> </td><td><b>'+num_undefined+'</b></td></tr>'
		+'</table>');
}
function hideStatistics(elm){
	hidePopup(elm);
}
</script>