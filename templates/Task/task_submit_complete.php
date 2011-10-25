
<strong>лог выполненения</strong>
<div class="task-submit-log">
	<?=$this->log;?>
</div>

<div class="paragraph" style="text-align: center;">
	<a href="<?=App::href('task/xrsl-edit/'.$id);?>" class="button"><?=Lng::get('task.go-to-current-task');?></a>
	<a href="<?=App::href('task/list');?>" class="button"><?=Lng::get('task.go-to-list');?></a> <!-- Вернуться к списку задач Вернуться к текущей задаче -->
</div>
