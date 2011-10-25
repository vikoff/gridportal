
<div><a href="<?= href('task-set/list'); ?> ">Вернуться к списку</a></div>

<h2>Запись #<?= $this->instanceId; ?></h2>

<table>
<tr>
	<td class="title">id</td>
	<td class="data"><?= $this->id; ?></td>
</tr>
<tr>
	<td class="title">uid</td>
	<td class="data"><?= $this->uid; ?></td>
</tr>
<tr>
	<td class="title">Проект</td>
	<td class="data"><?= $this->project_id; ?></td>
</tr>
<tr>
	<td class="title">Профиль</td>
	<td class="data"><?= $this->profile_id; ?></td>
</tr>
<tr>
	<td class="title">Имя набора</td>
	<td class="data"><?= $this->name; ?></td>
</tr>
<tr>
	<td class="title">ready_to_start</td>
	<td class="data"><?= $this->ready_to_start; ?></td>
</tr>
<tr>
	<td class="title">num_submits</td>
	<td class="data"><?= $this->num_submits; ?></td>
</tr>
<tr>
	<td class="title">create_date</td>
	<td class="data"><?= $this->create_date; ?></td>
</tr>
</table>
