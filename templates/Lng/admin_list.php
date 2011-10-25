
<div class="options-row">
	<a href="<?=App::href('admin/root/lng/new');?>">Добавить запись</a>
</div>

<? if(!empty($collection)): ?>
	
	<table class="std-grid tr-highlight">
		<tr>
			<th>Ключ</th>
			<th>Описание</th>
			<th>Текст</th>
			<th>Опции</th>
		</tr>
		<? foreach($collection as $item): ?>
			<tr>
				<td><a href="<?=App::href('admin/root/lng/edit/'.$item['id']);?>"><?=$item['name'];?></a></td>
				<td><?=$item['description'];?></td>
				<td>
					<? foreach($lngs as $l): ?>
						<div><strong><?=$l;?>:</strong> <?=!empty($item[$l]) ? $item[$l] : '<span class="red">не назначено</span>';?></div>
					<? endforeach; ?>
				</td>
				<td class="center" style="width: 90px;">
					<div class="tr-hover-visible options">
						<a href="<?=App::href('admin/root/lng/edit/'.$item['id']);?>" class="item" title="Редактировать"><img src="images/backend/icon-edit.png" alt="Редактировать" /></a>
						<a href="<?=App::href('admin/root/lng/copy/'.$item['id']);?>" class="item" title="Копировать"><img src="images/backend/icon-copy.png" alt="Копировать" /></a>
						<a href="<?=App::href('admin/root/lng/delete/'.$item['id']);?>" class="item" title="Удалить"><img src="images/backend/icon-delete.png" alt="Удалить" /></a>
					</div>
				</td>
			</tr>
		<? endforeach; ?>
	</table>
	
<? else: ?>

	<div class="paragraph">
		Языковые фрагменты еще не созданы.
	</div>
	
<? endif; ?>