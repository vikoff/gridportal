
<!--div><a href="<?= href('task-set/statistics'); ?> "><?= Lng::get('upload_files.returne-to-list'); ?></a></div-->

<h2 style="vertical-align:bottom;">
	<img src="/images/icons/task_statistics.gif" alt="<?= Lng::get('task-view.task_statistics') ?>" title="<?= Lng::get('task-view.task_statistics') ?>" align="center" width=32 height=32 onmouseover="this.src='/images/icons/task_statistics.a.gif'" onmouseout="this.src='/images/icons/task_statistics.gif'" />
	<?= Lng::get('task-view.task_statistics') ?> <?= $this->name; ?>
</h2>

<table class="table-tiny">
<tr>
	<td><?= Lng::get('edit.project'); ?></td>
	<td><?= $this->project_name; ?></td>
</tr>
<tr>
	<td><?= Lng::get('tasklist.name'); ?></td>
	<td><?= $this->name; ?></td>
</tr>
<tr>
	<td><?= Lng::get('taskset.list.profile'); ?></td>
	<td><?= $this->profile_name; ?></td>
</tr>
<tr>
	<td><?= Lng::get('taskset.list.num-submits'); ?></td>
	<td><?= $this->num_submits; ?></td>
</tr>
<tr>
	<td><?= Lng::get('taskset.list.create-date'); ?></td>
	<td><?= $this->create_date_str; ?></td>
</tr>
</table>


<div style="margin: 2em 0 1em; text-align: center;">
	<a href="<?= href('task-set/list'); ?>" class="button"><?= Lng::get('upload_files.returne-to-list'); ?></a>
</div>

<? if ($this->submits): ?>

	<?= $this->submitPagination; ?>
	
	<table style="margin: 1em auto 0;" class="std-grid c">
	<tr>
		<th><?= $this->submitSorters['name']; ?></th>
		<th><?= $this->submitSorters['jobid']; ?></th>
		<th><?= $this->submitSorters['status']; ?></th>
		<th><?= $this->submitSorters['start_date']; ?></th>
		<th><?= $this->submitSorters['finish_date']; ?></th>
		<!--th>опции</th-->
		
	</tr>
	<? foreach($this->submits as $i => $s): ?>
		<tr class="<?= $i % 2 ? 'odd' : 'even' ?>">
			<td class="l"><?= wordwrap($s['fullname'], 45, ' ', 1); ?></td>
			<td class="l"><?= $s['jobid']; ?></td>
			<td class="task<?= $s['id'] ?>-status task-state-<?= (int)$s['status'] ?>"><?= Lng::get($s['status_str']); ?></td>
			<td><?= $s['start_date_str']; ?></td>
			<td><?= $s['finish_date_str']; ?></td>
			<!--td>
<div style="background:rgb(241, 72, 72);height:16px;border:solid 1px #000;border-radius:3px;color:#000;padding-top:1px;">Ошибка</div></td-->
		</tr>
	<? endforeach; ?>
	</table>
	
	<?= $this->submitPagination; ?>
	
	<div style="margin: 1em 0; text-align: center;">
		<a href="<?= href('task-set/list'); ?>" class="button"><?= Lng::get('upload_files.returne-to-list'); ?></a>
	</div>

<? else: ?>
	<p><?= Lng::get('tast-set-view.no-running-tasks'); ?></p>
<? endif; ?>

<div class="refresh-indicator">
	<span></span>
	&nbsp;
	<a href="<?= href('task-set/statistics/'.$this->instanceId); ?>" onclick="refresh(0);return false"><img src="/images/refresh.png" alt="<?= Lng::get('TaskSet-view-update'); ?>" title="<?= Lng::get('TaskSet-view-update'); ?>" align="middle" /></a>
</div>

<script type="text/javascript">
$(function(){
	autoUpdate(30, ".refresh-indicator :first");
});

</script>
