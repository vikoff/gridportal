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
						<td><?= Lng::get('analyze.up') ?></td>
						<td><a href="<?= href('task-submit/analyze?submit='.$this->curSubmitId.'&path='.$this->fileTree['relpath'].'..'); ?>">..</a></td>
						<td><?= Lng::get('analyze.dir-size') ?></td>
						<td></td>
					</tr>
				<? endif; ?>
				<? foreach($this->fileTree['dirs'] as $elm): ?>
				<? 
					$res = array();
					exec('du -s -b ' . $this->fileTree['curpath'] . $elm, $res);
					$size = isset($res[0]) ? intval($res[0]) : 0;
				?>
					<tr>
						<td><?= Lng::get('analyze.dir') ?></td>
						<td><a href="<?= href('task-submit/analyze?submit='.$this->curSubmitId.'&path='.$this->fileTree['relpath'].$elm); ?>"><?= $elm; ?></a></td>
						<td><?= formatHumanReadableSize($size) ?></td>
						<td>
							<a class="visualize" href="<?= href('task-submit/download-dir/'.$this->curSubmitId.'/archive?path='.$this->fileTree['relpath'].$elm); ?>">
								<img src="images/icons-stat/winrar-ico.png" alt="<?= Lng::get('analyze.download-one-archive') ?>" title="<?= Lng::get('analyze.download-one-archive') ?>" />
							</a>
						</td>
					</tr>
				<? endforeach; ?>
				
				<? // echo '<pre>'; print_r($this->fileTree); die; ?>
				<? foreach($this->fileTree['files'] as $elm): ?>
					<tr>
						<td><?= Lng::get('analyze.dir-file') ?></td>
						<td><?= $elm['name']; ?></td>
						<td><?= formatHumanReadableSize($elm['size']) ?></td>
						<td>
							<? if (!$elm['empty']): ?>
								<a target="_blank" class="visualize" href="<?= href('task-submit/download-file/'.$this->curSubmitId.'?path='.$this->fileTree['relpath'].$elm['name']); ?>"
									><img src="images/icons-stat/download-ico.png" alt="<?= Lng::get('analyze.download-file') ?>" title="<?= Lng::get('analyze.download-file') ?>" 
								/></a>
								<a href="<?= href('task-submit/visualize/'.$this->curSubmitId.'?path='.$this->fileTree['relpath'].$elm['name']); ?>" class="visualize"><?
									 
									switch ($elm['visualizationType']) {
										case TaskVisualization::TYPE_TABLE:
											echo '<img src="images/icons-stat/txt-ico.gif" title="'.Lng::get('analyze.table').'" alt="'.Lng::get('analyze.table').'" />';
											break;
										case TaskVisualization::TYPE_CSV_CHART:
											echo '<img src="images/icons-stat/statistica-ico.png" title="'.Lng::get('analyze.chart').'" alt="'.Lng::get('analyze.chart').'" />';
											break;
										case TaskVisualization::TYPE_IMAGE:
											echo '<img src="images/icons-stat/pic-ico.png" title="'.Lng::get('analyze.image').'" alt="'.Lng::get('analyze.image').'" />';
											break;
										case TaskVisualization::TYPE_VIDEO:
											echo '<img src="images/icons-stat/avi-ico.png" title="'.Lng::get('analyze.video').'" alt="'.Lng::get('analyze.video').'" />';
											break;
										case TaskVisualization::TYPE_PDF:
											echo '<img src="images/icons-stat/pdf-ico.jpg" title="'.Lng::get('analyze.pdf').'" alt="'.Lng::get('analyze.pdf').'" />';
											break;
										default: echo 'визуализация';
									}
									
								?></a>
							<? else: ?>
								<?= Lng::get('task-submits.empty-file') ?>
							<? endif; ?>
						</td>
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

