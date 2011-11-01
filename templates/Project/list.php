
<h1>Проекты</h1>

<? foreach ($this->projectsList as $p): ?>
	<h2><?= $p['name']; ?></h2>
	<div class="paragraph">
		<? if (isset($this->userAllowedProjects[ $p['id'] ])): ?>
			<a href="<?= href('task-set/new/'.$p['id']); ?>" class="button">Создать задачу</a>
		<? else: ?>
			Вы не можете создавать задачи в этом проекте.
		<? endif; ?>
	</div>
<? endforeach; ?>