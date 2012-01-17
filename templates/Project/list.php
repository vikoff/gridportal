<h1 style="vertical-align:bottom;">
	<img src="/images/icons/projects.gif" alt="<?= Lng::get('profile.projects') ?>" title="<?= Lng::get('profile.projects') ?>" align="center" width=32 height=32 onmouseover="this.src='/images/icons/projects.a.gif'" onmouseout="this.src='/images/icons/projects.gif'" />
	<?= Lng::get('profile.projects') ?>
</h1>

<table class="project-list">
<? foreach ($this->projectsList as $p): ?>
	<tr class="project-list-item">
		<td class="project-list-item-info">
			<h3><?= Lng::get($p['name_key']); ?></h3>
			<? if (!$p['inactive'] && isset($this->userAllowedProjects[ $p['id'] ])): ?>
				<a href="<?= href('task-set/new/'.$p['id']); ?>" class="button"><?= Lng::get('project.create-new') ?></a>
			<? else: ?>
			<?= Lng::get('project.do-not-create') ?><br /><br />
			<a href="<?= href('profile/edit#/check-voms') ?>"><?= Lng::get('project.check-voms-auth') ?></a>
			<? endif; ?>
		</td>
		<td class="project-list-item-descr">
			<h3><?= Lng::get('project.description') ?></h3>
			<div>
				    <?= Lng::get($p['text_key']); ?> 
			<!--    <?= Page::getHelpIcon('profile.projects') ?> -->
			</div>
		</td>
	</tr>
<? endforeach; ?>
</table>