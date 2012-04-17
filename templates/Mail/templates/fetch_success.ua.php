<div>
	Завдання <?= $this->jobid; ?> (ім'я в системі <?= $this->task_name; ?>)
	виконана зі статусом <?= Lng::get()->getLngSnippet($this->lng, $this->task_status); ?><br />
	Ссылка на задачу: <a href="<?= $this->task_href; ?>"><?= $this->task_href; ?></a>
</div>