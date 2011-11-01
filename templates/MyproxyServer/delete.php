
<div style="text-align: center;">

	<div class="paragraph">

		Хотите удалить запись #<?= $this->instanceId; ?>		

		id: <?= $this->id; ?>, 
		myproxy.name: <?= $this->name; ?>, 
		myproxy.url: <?= $this->url; ?>, 
		myproxy.port: <?= $this->port; ?>, 
		
		безвозвратно?

	</div>
	
	<div class="paragraph">
		<form action="" method="post">
			<input type="hidden" name="id" value="<?= $this->instanceId; ?>" />
			<?= FORMCODE; ?>			
			<input class="button" type="submit" name="action[myproxy-server/delete]" value="Удалить" />
			<a class="button" href="<?= href('admin/content/myproxy-server'); ?>">Отмена</a>
		</form>
	</div>
	
</div>
