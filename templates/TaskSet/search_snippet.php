<script type="text/javascript">
function searchToggle(){
	var small = $('#searchbox-short').slideToggle();
	var full = $('#searchbox-full').slideToggle();
	var input = $('#is-full-view').slideToggle();
	
	// показать полный вид
	if (full.hasClass('hidden')) {
		small.slideUp();
		full.removeClass('hidden').slideDown();
		input.removeAttr('disabled');
	}
	// показать краткий вид
	else {
		small.slideDown();
		full.addClass('hidden').slideUp();
		input.attr('disabled', 'disabled');
	}
}
</script>
<?
$isFull = !empty($_GET['full-view']);
$shortStyle = $isFull ? 'display: none;' : '';
$shortClass = $isFull ? 'hidden' : '';
$fullStyle = $isFull ? '' : 'display: none;';
$fullClass = $isFull ? '' : 'hidden';
?>
<div style="text-align:right; ">
	<form name="filter" action="">
		<div id="searchbox-short" style="<?= $shortStyle; ?>" class="<?= $shortClass ?>">
			<input type="text" name="search" value="<?= getVar($_GET['search']) ?>" placeholder="<?= Lng::get('task-set.search-by-statistics') ?>" />
			<input type="submit" value="<?= Lng::get('task-set.search') ?>" />
			<div style="margin-right: 80px;">
				<a href="#" onclick="searchToggle(); return false;"><?= Lng::get('top-menu.advanced-search') ?></a>
			</div>
		</div>
		<div id="searchbox-full" style="<?= $fullStyle; ?>" class="<?= $fullClass ?>">
			<input id="is-full-view" type="hidden" name="full-view" value="1" />
			<div style="margin-right: 0px;">
				<a href="#" onclick="searchToggle(); return false;"><?= Lng::get('top-menu.short-search') ?></a>
			</div>
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
	</form>
		
</div>
<div style="clear: both;"></div>
