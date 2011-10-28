
<strong>лог выполненения</strong>
<div class="task-submit-log">
	<?=$this->log;?>
</div>

<? if($this->numSubmits == 1): ?>
	Задача успешно запущена
<? else: ?>
	Одна задача из пакета (<?= $this->numSubmits ?> задач) успешно запущена.
<? endif; ?>

<div class="paragraph" style="text-align: center;">
	<a href="<?= href('task-set/view/'.$this->id);?>" class="button"><?=Lng::get('task.go-to-current-task');?></a>
	<a href="<?= href('task-set/list');?>" class="button"><?=Lng::get('task.go-to-list');?></a> <!-- Вернуться к списку задач Вернуться к текущей задаче -->
</div>
