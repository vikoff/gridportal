
<div style="text-align: center;">

	<div class="paragraph">

		Хотите удалить языковой фрагмент "<strong><?=$name;?></strong>" безвозвратно?

	</div>
	
	<div class="paragraph">
		<form action="" method="post">
			<input type="hidden" name="id" value="<?=$instanceId;?>" />
			<?=FORMCODE;?>
			
			<input class="button" type="submit" name="action[lng/delete]" value="Удалить" />
			<a class="button" href="<?=App::href('admin/root/lng/list');?>">Отмена</a>
		</form>
	</div>
	
</div>

