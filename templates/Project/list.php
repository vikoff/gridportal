<h1 style="vertical-align:bottom;">
	<img src="/images/icons/projects.gif" alt="<?= Lng::get('profile.projects') ?>" title="<?= Lng::get('profile.projects') ?>" align="center" width=32 height=32 onmouseover="this.src='/images/icons/projects.a.gif'" onmouseout="this.src='/images/icons/projects.gif'" />
	<?= Lng::get('profile.projects') ?>
</h1>

<? foreach ($this->projectsList as $p): ?>
	<div class="project-list-item">
		<div class="project-list-item-info">
			<h3><?= Lng::get($p['name_key']); ?></h3>
			<? if (isset($this->userAllowedProjects[ $p['id'] ])): ?>
				<a href="<?= href('task-set/new/'.$p['id']); ?>" class="button"><?= Lng::get('project.create-new') ?></a>
			<? else: ?>
			<?=	Lng::get('project.do-not-create') ?>
			<? endif; ?>
		</div>
		<div class="project-list-item-descr">
			<h3><?= Lng::get('project.description') ?></h3>
			<div>
				    <?= Lng::get($p['text_key']); ?> 
			<!--    <?= Page::getHelpIcon('profile.projects') ?> -->
			</div>
		</div>
		<div class="cl"></div>
	</div>
<? endforeach; ?>