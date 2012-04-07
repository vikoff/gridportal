<h1 style="vertical-align:bottom;">
	<img src="/images/icons/statistics.gif" alt="<?= Lng::get('top-menu.results') ?>" title="<?= Lng::get('top-menu.results') ?>" align="center" width=32 height=32 onmouseover="this.src='/images/icons/statistics.a.gif'" onmouseout="this.src='/images/icons/statistics.gif'" />
	<?= Lng::get('top-menu.results') ?>
</h1>

<div style="text-align:right; ">
	<form name="filter" action="">
		<div id="searchbox-short">
			<input type="text" name="search" value="<?= getVar($_GET['search']) ?>" placeholder="<?= Lng::get('task-set.search-by-statistics') ?>" />
			<input type="submit" value="<?= Lng::get('task-set.search') ?>" />
			<div style="margin-right: 80px;"><a href="#" onclick="$('#searchbox-short,#searchbox-full').slideToggle(); return false;"><?= Lng::get('top-menu.advanced-search') ?></a></div>
		</div>
		<div style="display: none;" id="searchbox-full">
		<div style="margin-right: 0px;"><a href="#" onclick="$('#searchbox-short,#searchbox-full').slideToggle(); return false;"><?= Lng::get('top-menu.short-search') ?></a></div>
			<table class="table-tiny" style="margin: 0;" align="right">
			<tr>	
				<td><?= Lng::get('user') ?>:</td>
				<td><input type="text" name="filter[username]" value="<?= getVar($_GET['filter']['username']) ?>" /></td>
			</tr>
			<tr>
				<td><?= Lng::get('tasklist.name') ?>:</td>
				<td><input type="text" name="filter[taskname]" value="<?= getVar($_GET['filter']['taskname']) ?>" /></td>
			</tr>
			<tr>
				<td><?= Lng::get('taskset.list.profile') ?>:</td>
				<td><input type="text" name="filter[profile]" value="<?= getVar($_GET['filter']['profile']) ?>" /></td>
			</tr>
			<tr>
				<td><?= Lng::get('taskset.list.num-submits') ?>:</td>
				<td>
					<?= Lng::get('taskset.list.from') ?><input type="text" name="filter[num_submits][from]" value="<?= getVar($_GET['filter']['num_submits']['from']) ?>" size=5 />
					<?= Lng::get('taskset.list.to') ?><input type="text" name="filter[num_submits][to]" value="<?= getVar($_GET['filter']['num_submits']['to']) ?>" size=5 />
				</td>
			</tr>
			<tr>
				<td><?= Lng::get('taskset.list.create-date') ?>:</td>
				<td>
					<input type="hidden" name="filter[date][from]" value="<?= getVar($_GET['filter']['date']['from']) ?>" />
					<input type="hidden" name="filter[date][to]" value="<?= getVar($_GET['filter']['date']['to']) ?>" />
					<?= Lng::get('taskset.list.from') ?><input type="text" name="filter[dateVisual][from]" value="" style="width: 50px;" />
					<?= Lng::get('taskset.list.to') ?><input type="text" name="filter[dateVisual][to]" value="" style="width: 50px;" />
				</td>
			</tr>
			<tr>
				<td></td>
				<td>
					<input type="submit" value="<?= Lng::get('task-set.filter') ?>" />
				</td>
			</tr>
			</table>
		</div>
		<!--div style="display: none;" id="searchbox-full">
			<div style="margin-right: 0px;"><a href="#" onclick="$('#searchbox-short,#searchbox-full').slideToggle(); return false;">краткий поиск</a></div>
			Пользователь:
			<input type="text" name="filter[username]" value="<?= getVar($_GET['filter']['username']) ?>" />
			<br />
			Имя задачи:
			<input type="text" name="filter[taskname]" value="<?= getVar($_GET['filter']['taskname']) ?>" />
			<br />
			Профиль:
			<input type="text" name="filter[profile]" value="<?= getVar($_GET['filter']['profile']) ?>" />
			<br />
			Кол-во запусков:
			от <input type="text" name="filter[num_submits][from]" value="<?= getVar($_GET['filter']['num_submits']['from']) ?>" size=5 />
			до <input type="text" name="filter[num_submits][to]" value="<?= getVar($_GET['filter']['num_submits']['to']) ?>" size=5 />
			<br />
			Дата создания:
			<input type="hidden" name="filter[date][from]" value="<?= getVar($_GET['filter']['date']['from']) ?>" />
			<input type="hidden" name="filter[date][to]" value="<?= getVar($_GET['filter']['date']['to']) ?>" />
			от <input type="text" name="filter[dateVisual][from]" value="" size=10 />
			до <input type="text" name="filter[dateVisual][to]" value="" size=10 />
			<br />
			<input type="submit" value="<?= Lng::get('task-set.filter') ?>" />
		</div-->
	</form>
		
</div>
<div style="clear: both;"></div>

<?= $this->pagination; ?>

<? if($this->collection): ?>
	<table class="std-grid">
	<tr>
		<th><?= $this->sorters['uid']; ?></th>
		<th><?= $this->sorters['name']; ?></th>
		<th><?= $this->sorters['project_id']; ?></th>
		<th><?= $this->sorters['profile_id']; ?></th>
		<th><?= $this->sorters['num_submits']; ?></th>
		<th><?= $this->sorters['create_date']; ?></th>
		
		<th><?= Lng::get('options'); ?></th>
	</tr>
	<? foreach($this->collection as $i => $item): ?>	
	<tr class="<?= $i % 2 ? 'odd' : 'even' ?>">
		<td><?= $item['user_name']; ?></td>
		<td><a href="<?= href('task-set/statistics/'.$item['id']); ?>"><?= $item['name']; ?></a></td>
		<td><?= $item['project_name']; ?></td>
		<td><?= !empty($item['profile_name']) ? $item['profile_name'] : '-'; ?></td>
		<td onmouseover="showStatistics(this, <?= $item['num_submits']; ?>, <?= $item['num_finished']; ?>, <?= $item['num_processing']; ?>, <?= $item['num_errors']; ?>, <?= $item['num_undefined']; ?>);" onmouseout="hideStatistics(this);">
			<span style="font-size: 11px;"><?= Lng::get('TaskSet-list-total'); ?> <?= $item['num_submits']; ?></span>
			<div class="task-progress">
				<? if ($item['num_errors']){ ?><div class="task-progress-item task-state-6" style="width:<?= $item['num_errors'] / $item['num_submits'] * 100 ?>%"></div><? } ?>
				<? if ($item['num_finished']){ ?><div class="task-progress-item task-state-4" style="width:<?= $item['num_finished'] / $item['num_submits'] * 100 ?>%"></div><? } ?>
				<? if ($item['num_processing']){ ?><div class="task-progress-item task-state-5" style="width:<?= $item['num_processing'] / $item['num_submits'] * 100 ?>%"></div><? } ?>
				<? if ($item['num_undefined']){ ?><div class="task-progress-item task-state-1" style="width:<?= $item['num_undefined'] / $item['num_submits'] * 100 ?>%"></div><? } ?>
				<div class="cl"></div>
			</div>
		</td>
		<td><?= $item['create_date_str']; ?></td>
		
		<td style="font-size: 11px;">
			<a href="<?= href('task-set/statistics/'.$item['id']); ?>"><?= Lng::get('TaskSet-list-view'); ?></a>
		</td>
	</tr>
	<? endforeach; ?>	
	</table>
	
<? else: ?>
	<p><?= Lng::get('tast-set-view.no-running-tasks'); ?></p>
<? endif; ?>

<?= $this->pagination; ?>

<div class="refresh-indicator">
	<span></span>
	&nbsp;
	<a href="<?= href('task-set/statistics'); ?>" onclick="refresh(0);return false"><img src="/images/refresh.png" alt="<?= Lng::get('TaskSet-view-update'); ?>" title="<?= Lng::get('TaskSet-view-update'); ?>" align="middle" /></a>
</div>

<script type="text/javascript">
autoUpdate(30, ".refresh-indicator :first");
function showStatistics(elm, num_submits, num_finished, num_processing, num_errors, num_undefined){
	showPopup(elm, ''
		+'<table class="l" style="width:auto">'
			+'<tr><td><?= Lng::get('TaskSet-list-total'); ?> </td><td><b>'+num_submits+'</b></td></tr>'
			+'<tr><td><?= Lng::get('TaskSet-list-complite'); ?> </td><td><b>'+num_finished+'</b></td></tr>'
			+'<tr><td><?= Lng::get('TaskSet-list-in-progres'); ?> </td><td><b>'+num_processing+'</b></td></tr>'
			+'<tr><td><?= Lng::get('TaskSet-list-with-erors'); ?> </td><td><b>'+num_errors+'</b></td></tr>'
			+'<tr><td><?= Lng::get('TaskSet-list-unknown'); ?> </td><td><b>'+num_undefined+'</b></td></tr>'
		+'</table>');
}
function hideStatistics(elm){
	hidePopup(elm);
}
$(document.forms[0]["filter[dateVisual][from]"]).datepicker({
	altField: $(document.forms["filter"]["filter[date][from]"]),
	altFormat: '@',
	dateFormat: 'dd.mm.yy',
	defaultDate: $.datepicker.parseDate("@", document.forms["filter"]["filter[dateVisual][from]"].value)
});
$(document.forms[0]["filter[dateVisual][from]"]).datepicker("setDate", $.datepicker.parseDate("@", document.forms["filter"]["filter[date][from]"].value));

$(document.forms[0]["filter[dateVisual][to]"]).datepicker({
	altField: $(document.forms["filter"]["filter[date][to]"]),
	altFormat: '@',
	dateFormat: 'dd.mm.yy',
	defaultDate: $.datepicker.parseDate("@", document.forms["filter"]["filter[dateVisual][to]"].value)
});
$(document.forms[0]["filter[dateVisual][to]"]).datepicker("setDate", $.datepicker.parseDate("@", document.forms["filter"]["filter[date][to]"].value));
</script>