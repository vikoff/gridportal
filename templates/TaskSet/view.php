
<div><a href="<?= href('task-set/list'); ?> ">Вернуться к списку</a></div>

<h2>Статистика задачи <?= $this->name; ?></h2>


<table class="std-grid narrow">
<tr>
	<td class="title">Проект</td>
	<td class="data"><?= $this->project_id; ?></td>
</tr>
<tr>
	<td class="title">Имя задачи</td>
	<td class="data"><?= $this->name; ?></td>
</tr>
<tr>
	<td class="title">Пользователь</td>
	<td class="data"><?= $this->uid; ?></td>
</tr>
<tr>
	<td class="title">Профиль</td>
	<td class="data"><?= $this->profile_id; ?></td>
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

<table style="margin: 1em auto;" class="std-grid">
<tr>
	<th>Порядковый номер</th>
	<th>JobID</th>
	<th>Сатус</th>
	<th>Дата запуска</th>
	<th>Дата завершения</th>
	<th>Управление</th>
	
</tr>
<? foreach($this->submits as $s): ?>
	<tr>
		<td><?= $s['index']; ?></td>
		<td><?= $s['jobid']; ?></td>
		<td><?= Lng::get($s['status_str']); ?></td>
		<td><?= $s['start_date_str']; ?></td>
		<td><?= $s['finish_date_str']; ?></td>
		<td>
			<? if($s['actions']['to_analyze']): ?>  <a href="<?= href('task-submit/analyze?submit='.$s['id']); ?>" class="button-small"><?= Lng::get('task.to-analyze'); ?></a> <? endif; ?>
			<? if($s['actions']['get_results']): ?> <a href="<?= href('task-submit/get-results/'.$s['id']); ?>" class="button-small"><?= Lng::get('task.get-result'); ?></a> <? endif; ?>
			<? if($s['actions']['stop']): ?>        <a href="<?= href('task-submit/stop/'.$s['id']); ?>" class="button-small"><?= Lng::get('task.stop'); ?></a>              <? endif; ?>
			<? if($s['actions']['delete']): ?>      <a href="<?= href('task-submit/delete/'.$s['id']); ?>" class="button-small"><?= Lng::get('task.delete'); ?></a>          <? endif; ?>
		</td>
	</tr>
<? endforeach; ?>
</table>

<div style="margin: 1em 0; text-align: center;">
	<a href="<?= href('task-set/customize/'.$this->instanceId); ?>" class="button">Запустить</a>
	<a href="<?= href('task-set/delete/'.$this->instanceId); ?>" class="button">Удалить</a>
	<a href="<?= href('task-set/list'); ?>" class="button">Вернуться к списку</a>
</div>
