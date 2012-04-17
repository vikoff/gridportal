<div>
	Task <?= $this->jobid; ?> (system name <?= $this->task_name; ?>)
	completed with error <?= Lng::get()->getLngSnippet($this->lng, $this->task_status); ?><br />
	Link to the task: <a href="<?= $this->task_href; ?>"><?= $this->task_href; ?></a>
</div>