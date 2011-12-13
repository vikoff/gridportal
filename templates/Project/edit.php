
<ul id="submit-box-floating"></ul>

<h2><?= $this->pageTitle; ?></h2>

<form id="edit-form" action="" method="post">
	<?= FORMCODE; ?>
	<input type="hidden" name="id" value="<?= $this->instanceId; ?>" />
	
	<? $selectedTab = 0; ?>
	
	<div id="tabs">
		<ul style="">
			<? foreach(Lng::$allowedLngs as $l): ?>
				<li><a href="#tab-lng-<?= $l; ?>"><?= Lng::$lngTitles[$l]; ?></a></li>
			<? endforeach; ?>
		</ul>
		
		<? foreach(Lng::$allowedLngs as $index => $l): ?>
			
			<? if($l == $this->curLng)
					$selectedTab = $index; ?>
					
			<div id="tab-lng-<?= $l; ?>">
				<div class="paragraph">
					<label class="title">Название <span class="required">*</span></label>
					<input type="text" name="lng[<?= $l; ?>][name]" value="<?= !empty($this->lng) ? $this->lng[$l]['name'] : ''; ?>" style="width: 300px;" />
				</div>
				
				<div class="paragraph">
					<label class="title">Описание</label>
					<textarea class="wysiwyg" style="width: 98%; height: 400px;" name="lng[<?= $l; ?>][text]"><?= !empty($this->lng) ? $this->lng[$l]['text'] : ''; ?></textarea>
				</div>
			</div>
		<? endforeach; ?>
	</div>
	
	<div class="paragraph">
		<fieldset title="Виртуальные организации">
			<legend style="font-weight: bold;">Виртуальные организации</legend>
			<? foreach ($this->vomsList as $v): ?>
				<input id="checkbox-voms-<?= $v['id']; ?>" type="checkbox" name="voms[]" value="<?= $v['id']; ?>" <? if (isset($this->allowedVoms[ $v['id'] ])): ?>checked="checked"<? endif; ?>>
				<label for="checkbox-voms-<?= $v['id']; ?>"><?= $v['name']; ?></label><br />
			<? endforeach; ?>
		</fieldset>
	</div>
	
	<div class="paragraph" id="submit-box">
		<input id="submit-save" class="button" type="submit" name="action[project/save][admin/content/project/list]" value="Сохранить" title="Созхранить изменения и вернуться к списку" />
		<input id="submit-apply" class="button" type="submit" name="action[project/save][admin/content/project/edit/<?= $this->instanceId ? $this->instanceId : '(%id%)'; ?>]" value="Применить" title="Сохранить изменения и продолжить редактирование" />
		<a id="submit-cancel" class="button" href="{a href=admin/content/project/list}" title="Отменить все изменения и вернуться к списку">отмена</a>
		<? if ($this->instanceId): ?>
			<a id="submit-delete" class="button" href="{a href=admin/content/project/delete/$instanceId}" title="Удалить запись">удалить</a>
			<a id="submit-copy" class="button" href="{a href=admin/content/project/copy/$instanceId}" title="Сделать копию записи">копировать</a>
		<? endif; ?>
	</div>
</form>

<script type="text/javascript" src="includes/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">

$(function(){
	
	$('#tabs').tabs({selected: <?= $selectedTab; ?>});
	
	tinyMCE.init($.extend(getDefaultTinyMceSettings('<?= WWW_ROOT; ?>'), {
		mode : 'specific_textareas',
		editor_selector : 'wysiwyg'
	}));
	
	enableFloatingSubmits();
});

</script>
