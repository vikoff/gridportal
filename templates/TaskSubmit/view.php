
<div><a href="<?= href('task-submit/list'); ?> ">Вернуться к списку</a></div>

<h2>Запись #<?= $this->instanceId; ?></h2>

<table>
<tr>
	<td class="title">id</td>
	<td class="data"><?= $this->id; ?></td>
</tr>
<tr>
	<td class="title">Набор</td>
	<td class="data"><?= $this->set_id; ?></td>
</tr>
<tr>
	<td class="title">Порядковый номер</td>
	<td class="data"><?= $this->index; ?></td>
</tr>
<tr>
	<td class="title">Сатус</td>
	<td class="data"><?= $this->status; ?></td>
</tr>
<tr>
	<td class="title">Отправлена</td>
	<td class="data"><?= $this->is_submitted; ?></td>
</tr>
<tr>
	<td class="title">Завершена</td>
	<td class="data"><?= $this->is_completed; ?></td>
</tr>
<tr>
	<td class="title">Получена</td>
	<td class="data"><?= $this->is_fetched; ?></td>
</tr>
<tr>
	<td class="title">Дата запуска</td>
	<td class="data"><?= $this->start_date; ?></td>
</tr>
<tr>
	<td class="title">Дата завершения</td>
	<td class="data"><?= $this->finish_date; ?></td>
</tr>
</table>
