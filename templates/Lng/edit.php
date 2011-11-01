
<form id="edit-form" action="" method="post">
	<?=FORMCODE;?>
	<input type="hidden" name="id" value="<?=$instanceId;?>" />
	
	<h2><?=$pageTitle;?></h2>
	
	<div class="paragraph">
		<label class="title">Ключ</label>
		<div class="description">По ключу осуществляется вызов тектового фрагмента<br />Например: top-menu.main</div>
		<input type="text" name="name" value="<?=getVar($name);?>" style="width: 300px;" />
	</div>
	
	<div class="paragraph">
		<label class="title">Описание</label>
		<div class="description">для того, чтобы напомнить себе, что это за фрагмент</div>
		<textarea name="description" style="width: 300px; height: 3em;"><?=getVar($description);?></textarea>
	</div>
	
	<div class="paragraph">
		<label class="title">Текст для каждого из языков</label>
		<table>
		<? foreach($lngs as $l): ?>
			<tr>
				<td><?=$l;?>:</td>
				<td><input type="text" name="text[<?=$l;?>]" value="<?=getVar($$l);?>" style="width: 500px;" /></td>
			</tr>
		<? endforeach; ?>
		</table>
	</div>
	
	<div class="paragraph">
		<input class="button" type="submit" name="action[lng/save][admin/root/lng/list]" value="Сохранить" />
		<? if($instanceId): ?><input class="button" type="submit" name="action[lng/save][admin/root/lng/copy/<?=$instanceId;?>]" value="Копировать" /><? endif; ?>
		<a class="button" href="<?=App::href('admin/root/lng/delete/'.$instanceId);?>">Удалить</a>
		<a class="button" href="<?=App::href('admin/root/lng/list');?>">Отмена</a>
	</div>
</form>

<script type="text/javascript">

$(function(){

});

</script>
