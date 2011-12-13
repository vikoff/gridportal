
<h2><?= Lng::get('profile.projects') ?></h2>

<? foreach ($this->projectsList as $p): ?>
	<div class="project-list-item">
		<div class="project-list-item-info">
			<h3><?= $p['name']; ?></h3>
			<? if (isset($this->userAllowedProjects[ $p['id'] ])): ?>
				<a href="<?= href('task-set/new/'.$p['id']); ?>" class="button"><?= Lng::get('project.create-new') ?></a>
			<? else: ?>
				Вы не можете создавать задачи в этом проекте.
			<? endif; ?>
		</div>
		<div class="project-list-item-descr">
			<h3><?= Lng::get('project.description') ?></h3>
			<div>
				some descr
			</div>
		</div>
		<div class="cl"></div>
	</div>
<? endforeach; ?>