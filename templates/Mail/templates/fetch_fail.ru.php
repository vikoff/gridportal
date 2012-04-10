<div>
	Задача <?= $this->jobid; ?> (имя в системе <?= $this->task_name; ?>)
	завершена с ошибкой <?= Lng::get()->getLngSnippet($this->lng, $this->task_status); ?><br />
	Ссылка на задачу: <a href="<?= $this->task_href; ?>"><?= $this->task_href; ?></a>
</div>