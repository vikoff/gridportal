
<div><a href="<?= href('task-set/statistics'); ?> ">Вернуться к списку</a></div>

<h2>Статистика задачи <?= $this->name; ?></h2>


<table class="std-grid narrow">
<tr>
	<td class="title">Проект</td>
	<td class="data"><?= $this->project_name; ?></td>
</tr>
<tr>
	<td class="title">Имя задачи</td>
	<td class="data"><?= $this->name; ?></td>
</tr>
<tr>
	<td class="title">Профиль</td>
	<td class="data"><?= $this->profile_name; ?></td>
</tr>
<tr>
	<td class="title">Количество запусков</td>
	<td class="data"><?= $this->num_submits; ?></td>
</tr>
<tr>
	<td class="title">Дата создания</td>
	<td class="data"><?= $this->create_date_str; ?></td>
</tr>
</table>


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
			<td class="l"><?= $s['fullname']; ?></td>
			<td class="l"><?= $s['jobid']; ?></td>
			<td class="task<?= $s['id'] ?>-status task-state-<?= (int)$s['status'] ?>"><?= Lng::get($s['status_str']); ?></td>
			<td><?= $s['start_date_str']; ?></td>
			<td><?= $s['finish_date_str']; ?></td>
			<!--td></td-->
		</tr>
	<? endforeach; ?>
	</table>
	
	<?= $this->submitPagination; ?>

<? else: ?>
	<p>Нет запущенных задач</p>
<? endif; ?>

<script type="text/javascript">
$(function(){
	
	// при каждом обновлении загружается новый экземпляр setTimeout
	// setTimeout(function(){
		// $.get(location.href, function(response){
			// $('#main').html(response);
		// });
	// }, 30000);
});

</script>
