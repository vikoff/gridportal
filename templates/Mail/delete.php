
<div style="text-align: center;">

	<div class="paragraph">

		Хотите удалить запись #<?= $this->instanceId; ?>		

		id: <?= $this->id; ?>, 
		Пользователь: <?= $this->uid; ?>, 
		Email: <?= $this->email; ?>, 
		Заголовок: <?= $this->title; ?>, 
		Текст: <?= $this->text; ?>, 
		Дата добавления: <?= $this->add_date; ?>, 
		Дата отправки: <?= $this->send_date; ?>, 
		
		безвозвратно?

	</div>
	
	<div class="paragraph">
		<form action="" method="post">
			<input type="hidden" name="id" value="<?= $this->instanceId; ?>" />
			<?= FORMCODE; ?>			
			<input class="button" type="submit" name="action[mail/delete]" value="Удалить" />
			<a class="button" href="<?= href('admin/content/mail'); ?>">Отмена</a>
		</form>
	</div>
	
</div>
