
<!--div><a href="<?= href('task-set/list'); ?> "><?= Lng::get('upload_files.returne-to-list'); ?></a></div-->

<h2 style="vertical-align:bottom;">
	<img src="/images/icons/task_statistics.gif" alt="<?= Lng::get('task-view.task_statistics') ?>" title="<?= Lng::get('task-view.task_statistics') ?>" align="center" width=32 height=32 onmouseover="this.src='/images/icons/task_statistics.a.gif'" onmouseout="this.src='/images/icons/task_statistics.gif'" />
	<?= Lng::get('task-view.task_statistics') ?> <?= $this->name; ?>
</h2>


<table class="table-tiny">
<tr>
	<td class="title"><?= Lng::get('edit.project'); ?></td>
	<td class="data"><?= $this->project_name; ?></td>
</tr>
<tr>
	<td class="title"><?= Lng::get('tasklist.name'); ?></td>
	<td class="data"><?= $this->name; ?></td>
</tr>
<tr>
	<td class="title"><?= Lng::get('taskset.list.profile'); ?></td>
	<td class="data"><?= $this->profile_name; ?></td>
</tr>
<tr>
	<td class="title"><?= Lng::get('taskset.list.num-submits'); ?></td>
	<td class="data"><?= $this->num_submits; ?></td>
</tr>
<tr>
	<td class="title"><?= Lng::get('taskset.list.create-date'); ?></td>
	<td class="data"><?= $this->create_date_str; ?></td>
</tr>
</table>

<div style="margin: 2em 0 1em; text-align: center;">
	<a href="<?= href('task-set/submit/'.$this->instanceId); ?>" class="button"><?= Lng::get('upload_files.go-to-start'); ?></a>
	<a href="<?= href('task-set/customize/'.$this->instanceId); ?>" class="button"><?= Lng::get('tast-set-view.run'); ?></a>
	<a href="<?= href('task-set/delete/'.$this->instanceId); ?>" class="button"><?= Lng::get('task-set.delete'); ?></a>
	<a href="<?= href('task-set/list'); ?>" class="button"><?= Lng::get('upload_files.returne-to-list'); ?></a>
</div>

<?= $this->submitPagination; ?>

<? if ($this->submits): ?>
	<form id="grid-form" action="" method="get">
		<table style="margin: 1em auto 0;" class="std-grid c hl">
		<tr>
			<th><?= $this->submitSorters['name']; ?></th>
			<th><?= $this->submitSorters['jobid']; ?></th>
			<th><?= $this->submitSorters['status']; ?></th>
			<th><?= $this->submitSorters['start_date']; ?></th>
			<th><?= $this->submitSorters['finish_date']; ?></th>
			<th><?= Lng::get('tas-set-view.manage'); ?></th>
			<th><input type="checkbox" onchange="$('input.row-check').attr('checked', $(this).attr('checked') ? true : false)" /></th>
			
		</tr>
		<? foreach($this->submits as $i => $s): ?>
			<tr class="<?= $i % 2 ? 'odd' : 'even' ?>">
				<td class="l"><?= wordwrap($s['fullname'], 45, ' ', 1); ?></td>
				<td class="l"><?= $s['jobid']; ?></td>
				<td class="task<?= $s['id'] ?>-status task-state-<?= (int)$s['status'] ?>"><?= Lng::get($s['status_str']); ?></td>
				<td><?= $s['start_date_str']; ?></td>
				<td><?= $s['finish_date_str']; ?></td>
				<td>
					<? if($s['actions']['to_analyze']): ?>  <a href="<?= href('task-submit/analyze?submit='.$s['id']); ?>" class="button-small"><?= Lng::get('task.to-analyze'); ?></a> <? endif; ?>
					<? if($s['actions']['get_results']): ?> <a href="<?= href('task-submit/get-results/'.$s['id']); ?>" class="button-small"><?= Lng::get('task.get-result'); ?></a> <? endif; ?>
					<? if($s['actions']['stop']): ?>        <a href="<?= href('task-submit/stop/'.$s['id']); ?>" class="button-small"><?= Lng::get('task.stop'); ?></a>              <? endif; ?>
					<? if($s['actions']['delete']): ?>      <a href="<?= href('task-submit/delete?task[]='.$s['id']); ?>" class="button-small"><?= Lng::get('task.delete'); ?></a>          <? endif; ?>
				</td>
				<td>
					<input class="row-check" type="checkbox" name="task[]" value="<?= $s['id'] ?>" />
				</td>
			</tr>
		<? endforeach; ?>
		</table>
		<div style="text-align: right; margin-right: 10px; margin-top: 5px;">
			<?= Lng::get('tast-set-view.with selected'); ?>
			<input id="btn-del-all" type="button" class="button-small" value="<?= Lng::get('task.delete'); ?>" />
		</div>
	</form>
	
<?= $this->submitPagination; ?>

<? else: ?>
	<p><?= Lng::get('tast-set-view.no-running-tasks'); ?></p>
<? endif; ?>

<div style="margin: 1em 0; text-align: center;">
	<a href="<?= href('task-set/submit/'.$this->instanceId); ?>" class="button"><?= Lng::get('upload_files.go-to-start'); ?></a>
	<a href="<?= href('task-set/customize/'.$this->instanceId); ?>" class="button"><?= Lng::get('tast-set-view.run'); ?></a>
	<a href="<?= href('task-set/delete/'.$this->instanceId); ?>" class="button"><?= Lng::get('task-set.delete'); ?></a>
	<a href="<?= href('task-set/list'); ?>" class="button"><?= Lng::get('upload_files.returne-to-list'); ?></a>
</div>

<div class="refresh-indicator">
	<span></span>
	&nbsp;
	<a href="<?= href('task-set/view/'.$this->instanceId); ?>" onclick="refresh(0);return false"><img src="/images/refresh.png" alt="<?= Lng::get('TaskSet-view-update'); ?>" title="<?= Lng::get('TaskSet-view-update'); ?>" align="middle" /></a>
</div>

<script type="text/javascript">
$(function(){
	$('#btn-del-all').click(function(){
		$('#grid-form').attr('action', '<?= href('task-submit/delete'); ?>').submit();
	});
	
	autoUpdate(30, ".refresh-indicator :first");
});

</script>
