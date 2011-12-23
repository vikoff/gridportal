
<ul id="submit-box-floating"></ul>

<h2><?= $this->pageTitle; ?></h2>

<form id="edit-form" action="" method="post">
	<?= FORMCODE; ?>
	<input type="hidden" name="id" value="<?= $this->instanceId; ?>" />
	<input type="hidden" id="redirect-input" name="redirect" value="" />
	
	<? if($this->instanceId): ?>
		<div class="info-block">
		
			<h3>[PAGE INFO]</h3>
			
			<? if($this->type == Page::TYPE_FULL): ?>
				<p>
					<label class="title">URL текущей страницы</label>
					<?= App::href('page/'.$this->alias); ?>
				</p>
				<p>
					<label class="title">Вставка ссылки в php-код</label>
					<code>&lt;a href="&lt;?= App::href('page/<?= $this->alias; ?>'); ?&gt;"&gt;<?= $this->title[$this->curLng]; ?>&lt;/a&gt;</code>
				</p>
				<p>
					<label class="title">Вставка ссылки в smarty-код</label>
					<code>&lt;a href="{a href='page/<?= $this->alias; ?>'}"&gt;<?= $this->title[$this->curLng]; ?>&lt;/a&gt;</code>
				</p>
				<p>
					<label class="title">Вставка ссылки в html-код страницы контента</label>
					<code>&lt;a href="{href('page/<?= $this->alias; ?>')}"&gt;<?= $this->title[$this->curLng]; ?>&lt;/a&gt;</code>
				</p>
			<? else: ?>
				<p>
					<label class="title">Получение страницы из php-кода</label>
					<span class="description-inline">более производительный, но менее наглядный способ:</span><br />
					<code>Page::load(<?= $this->id; ?>)->getAllFieldsPrepared();</code>
					<br />
					<span class="description-inline">чуть менее производительный, но более наглядный способ:</span><br />
					<code>Page::loadByAlias('<?= $this->alias; ?>')->getAllFieldsPrepared();</code>
					<br />
					<br />
					Оба метода возвращают ассоциативный массив с ключами:<br />
					<code><?= implode(', ', $instanceFields); ?></code>
				</p>
			<? endif; ?>
		</div>
	<? endif; ?>
	
	
	<p>
		<label class="title">Тип страницы</label>
		
		<label>
			<input type="radio" name="type" value="<?= Page::TYPE_FULL ?>"
				<? if($this->type == Page::TYPE_FULL || empty($this->type)): ?>checked="checked"<? endif; ?> />
			<?= Page::getPageTypeTitle(Page::TYPE_FULL); ?>
			</label>
		<span class="description-inline"> - такая страница отображается как основной контент.</span>
		<br />
		<label>
		<input type="radio" name="type" value="<?= Page::TYPE_CHUNK ?>"
				<? if($this->type == Page::TYPE_CHUNK): ?>checked="checked"<? endif; ?> />
			<?= Page::getPageTypeTitle(Page::TYPE_CHUNK); ?>
		</label>
		<span class="description-inline">
		- такая страница не отображается как основной контент, но может быть выведена
		как текстовый фрагмент в произвольном месте.
		</span>
		<br />
	</p>
	
	<p>
		<label class="title">Псевдоним</label>
		<span class="description">
			уникальный идентификатор страницы [a-z, 0-9].<br />
			Если не заполнен, система автоматически создаст псевдоним,<br />
			соответствующий id страницы.
		</span>
		<input type="text" name="alias" value="<?= $this->alias; ?>" style="width: 300px;" />
	</p>
	
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
				<p>
					<label class="title">Заголовок <span class="required">*</span></label>
					<input type="text" name="title[<?= $l; ?>]" value="<?= !empty($this->title) ? $this->title[$l] : ''; ?>" style="width: 300px;" />
				</p>
				
				<p>
					<label class="title">Текст</label>
					<textarea class="wysiwyg" style="width: 98%; height: 400px;" name="body[<?= $l; ?>]"><?= !empty($this->body) ? $this->body[$l] : ''; ?></textarea>
				</p>
				
				<p>
					<label class="title">meta description</label>
					<textarea style="width: 300px; height: 60px;" name="meta_description[<?= $l; ?>]"><?= !empty($this->meta_description) ? $this->meta_description[$l] : ''; ?></textarea>
				</p>
				
				<p>
					<label class="title">meta keywords</label>
					<textarea style="width: 300px; height: 60px;" name="meta_keywords[<?= $l; ?>]"><?= !empty($this->meta_keywords) ? $this->meta_keywords[$l] : ''; ?></textarea>
				</p>
			</div>
		<? endforeach; ?>
	</div>
	
	<p>
		<label>
			<input type="checkbox" name="published" value="1" <? if($this->published): ?>checked="checked"<? endif; ?> />
			Опубликовать
		</label>
	</p>
	
	<? if(CurUser::get()->hasPerm(PERMS_SUPERADMIN)): ?>
	<p>
		<label>
			<input type="checkbox" name="locked" value="1" <? if($this->locked): ?>checked="checked"<? endif; ?> />
			Запретить удаление
		</label>
	</p>
	<? endif; ?>
	
	<div class="paragraph" id="submit-box">
		<input id="submit-save" class="button" type="submit" name="action[page/save][admin/content/page/list]" value="Сохранить" title="Созхранить изменения и вернуться к списку" />
		<? if($this->instanceId): ?>
		<input id="submit-apply" class="button" type="submit" name="action[page/save]" value="Применить" title="Сохранить изменения и продолжить редактирование" />
		<? endif; ?>
		<a id="submit-cancel" class="button" href="<?= App::href('admin/content/page/list'); ?>" title="Отменить все изменения и вернуться к списку">отмена</a>
		<? if($this->instanceId && !$locked): ?>
		<a id="submit-delete" class="button" href="<?= App::href('admin/content/page/delete/'.$this->instanceId); ?>" title="Удалить запись">удалить</a>
		<? endif; ?>
		<? if($this->instanceId): ?>
		<a id="submit-copy" class="button" href="<?= App::href('admin/content/page/copy/'.$this->instanceId); ?>" title="Сделать копию записи">копировать</a>
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
