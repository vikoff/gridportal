
<? if(count($this->fetchedTasks)): ?>
	<form action="<?= href('task-submit/analyze'); ?>" method="get" style="margin: 1em 0;">
		Задача
		<select name="submit">
			<option value="">Выберите задачу...</option>
		<? foreach($this->fetchedTasks as $t): ?>
			<option value="<?= $t['id']; ?>" <? if($t['id'] == $this->curSubmitId): ?>selected="selected"<? endif; ?>><?= $t['fullname'].'_rc'.$t['index']; ?></option>
		<? endforeach; ?>
		</select>
		<input type="submit" value="Показать" />
	</form>

	<? if($this->curSubmitId): ?>
		<? if(empty($this->fileTree['dirs']) && empty($this->fileTree['files'])): ?>
			У вас ничего нет. Перейдите в <a href="<?= href('task-set/list'); ?>">диспетчер задач</a>.
		<? else: ?>

			<h2>Файлы задач</h2>
			
			<div class="task-file-manager">
				
				<table align="center" style="width: auto; text-align: left;" border>
					<? if(!$this->fileTree['isRootDir']): ?>
						<tr>
							<td>UP</td>
							<td><a href="<?= href('task-submit/analyze?submit='.$this->curSubmitId.'&path='.$this->fileTree['relpath'].'..'); ?>">..</a></td>
							<td></td>
						</tr>
					<? endif; ?>
					<? foreach($this->fileTree['dirs'] as $elm): ?>
						<tr>
							<td>DIR</td>
							<td><a href="<?= href('task-submit/analyze?submit='.$this->curSubmitId.'&path='.$this->fileTree['relpath'].$elm); ?>"><?= $elm; ?></a></td>
							<td><a href="<?= href('task-submit/download-dir/'.$this->curSubmitId.'/archive?path='.$this->fileTree['relpath'].$elm); ?>" class="button-small">скачать в архиве</a></td>
						</tr>
					<? endforeach; ?>

					<? foreach($this->fileTree['files'] as $elm): ?>
						<tr>
							<td>FILE</td>
							<td><?= $elm; ?></td>
							<td><a target="_blank" href="<?= href('task-submit/download-file/'.$this->curSubmitId.'?path='.$this->fileTree['relpath'].$elm); ?>" class="button-small">скачать</a></td>
						</tr>
					<? endforeach; ?>
				</table>
			</div>

		<? endif; ?>

	<? else: ?>
	
		Выберите задачу для просмотра.
		
	<? endif; ?>

<? else: ?>
	
	У вас нет задач для просмотра.
	
<? endif; ?>
