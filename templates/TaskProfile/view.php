
<div><a href="<?= href('task-profile/list'); ?> ">Вернуться к списку</a></div>

<h2>Запись #<?= $this->instanceId; ?></h2>

<div class="paragraph">
	<h3>id</h3>
	<?= $this->id; ?>
</div>
<div class="paragraph">
	<h3>is_user_defined</h3>
	<?= $this->is_user_defined; ?>
</div>
<div class="paragraph">
	<h3>uid</h3>
	<?= $this->uid; ?>
</div>
<div class="paragraph">
	<h3>Имя профиля</h3>
	<?= $this->name; ?>
</div>
<div class="paragraph">
	<h3>Проект</h3>
	<?= $this->project_id; ?>
</div>
<div class="paragraph">
	<h3>create_date</h3>
	<?= $this->create_date; ?>
</div>
