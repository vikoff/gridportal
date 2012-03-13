<h1>
	<img src="/images/icons/analys.gif" alt="<?= Lng::get('task.analyze') ?>" title="<?= Lng::get('task.analyze') ?>" align="center" width=32 height=32 onmouseover="this.src='/images/icons/analys.a.gif'" onmouseout="this.src='/images/icons/analys.gif'" />
	<?= Lng::get('task.analyze') ?>
</h1>
<? if(count($this->fetchedTasks)): ?>
	<div class="c">
		<form action="<?= href('task-submit/analyze'); ?>" method="get" style="margin: 1em 0;">
			<?= Lng::get('task.analyze-task') ?>
			<select name="submit">
				<option value=""><?= Lng::get('task.analyze-select-the-task') ?></option>
			<? foreach($this->fetchedTasks as $t): ?>
				<option value="<?= $t['id']; ?>" <? if($t['id'] == $this->curSubmitId): ?>selected="selected"<? endif; ?>><?= $t['fullname']; ?></option>
			<? endforeach; ?>
			</select>
			<input type="submit" value="<?= Lng::get('task.analyze-display') ?>" />
		</form>

	<? if($this->curSubmitId): ?>
	
		<? if(empty($this->fileTree['dirs']) && empty($this->fileTree['files'])): ?>
			<?= Lng::get('task.analyze-in-task-files-are-missing') ?>
		<? else: ?>

			<h2><?= Lng::get('task.analyze-files-task') ?></h2>
			
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
							<td><a href="<?= href('task-submit/download-dir/'.$this->curSubmitId.'/archive?path='.$this->fileTree['relpath'].$elm); ?>" class="button-small"><?= Lng::get('task.analyze-download-in-archive') ?></a></td>
						</tr>
					<? endforeach; ?>

					<? foreach($this->fileTree['files'] as $elm): ?>
						<tr>
							<td>FILE</td>
							<td><?= $elm; ?></td>
							<td><a target="_blank" href="<?= href('task-submit/download-file/'.$this->curSubmitId.'?path='.$this->fileTree['relpath'].$elm); ?>" class="button-small"><?= Lng::get('task.analyze-download') ?></a></td>
							<td><a href="<?= href('task-submit/visualize/'.$this->curSubmitId.'?path='.$this->fileTree['relpath'].$elm); ?>" class="visualize">визуализация</a></td>
						</tr>
					<? endforeach; ?>
				</table>
			</div>
			
			<script type="text/javascript">
				$(function(){
					$('a.visualize').click(function(){
						url = this.href;
						var iframe = $('<iframe src="' + url + '" style="width: 800px; height: 500px;" />');
						$.modal($('<div />').append(iframe));
						return false;
					});
				});
			</script>
		<? endif; ?>

	<? else: ?>
	
		<?= Lng::get('task.analyze-select-a-task-to-view') ?>
		
	<? endif; ?>
	
	</div>
	
<? else: ?>
	
	<?= Lng::get('task.analyze-you-no-have-task-for-view') ?>
	
<? endif; ?>

