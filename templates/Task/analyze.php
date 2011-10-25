
<h2>Файлы задач</h2>

<? if(empty($this->fileTree['dirs']) && empty($this->fileTree['files'])): ?>
	У вас ничего нет. Перейдите в <a href="<?= href('task'); ?>">диспетчер задач</a>.
<? else: ?>

	<div class="task-file-manager">
		
		
		<table align="center" style="width: auto; text-align: left;" border>
			<? if(!$this->fileTree['isRootDir']): ?>
				<tr>
					<td>UP</td>
					<td><a href="<?= href('task/analyze?path='.$this->fileTree['relpath'].'..'); ?>">..</a></td>
				</tr>
			<? endif; ?>
			<? foreach($this->fileTree['dirs'] as $elm): ?>
				<tr>
					<td>DIR</td>
					<td><a href="<?= href('task/analyze?path='.$this->fileTree['relpath'].$elm['name']); ?>"><?= $elm['title']; ?></a></td>
					<td><a href="<?= href('task/analyze?act=download-dir&path='.$this->fileTree['relpath'].$elm['name']); ?>" class="button-small">скачать в архиве</a></td>
				</tr>
			<? endforeach; ?>

			<? foreach($this->fileTree['files'] as $elm): ?>
				<tr>
					<td>FILE</td>
					<td><?= $elm['title']; ?></td>
					<td><a href="" class="button-small">скачать</a></td>
				</tr>
			<? endforeach; ?>
		</table>
	</div>

<? endif; ?>
