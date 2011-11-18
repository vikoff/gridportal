<script type="text/javascript" language="javascript">

$(document).ready(function() {

	jQuery.fn.center = function () {
		this.css("position","fixed");
		this.css("top", ( $(window).height() - this.height()) / 2 + "px");
		this.css("left", ( $(window).width() - this.width()) / 2 + "px");
		return this;
	}


    $(window).resize(function() {
        $(".popup-container").css({width: $(this).width() + 'px', height: $(this).height() + 'px'});
    });

    $("input[type=submit]").click(function() {
		$(window).resize();
		$(".popup").center();
		$(".popup .body-container").center();
		$("#wait-popup").show();
    });


});
</script>

<h2><?= Lng::get('xrls_edit.taskset'); ?></h2>

<form action="" method="post">
	<?= FORMCODE; ?>
	<input type="hidden" name="id" value="<?= $this->id; ?>" />
	
	<table style="text-align: left; margin: 1em auto;">
	<? if ($showMyproxyLogin): ?>
		<tr>
			<td><?= Lng::get('xrls_edit.server'); ?></td>
			<td>
				<select name="server">
					<? foreach($this->myproxyServersList as $item): ?>
						<option value="<?= $item['id']; ?>"><?= $item['name']; ?></option>
					<? endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td><?= Lng::get('xrls_edit.username'); ?></td>
			<td><input type="text" name="user[name]" value="" /></td>
		</tr>
		<tr>
			<td><?= Lng::get('xrls_edit.password'); ?></td>
			<td><input type="password" name="user[pass]" value="" /></td>
		</tr>
		<tr>
			<td><?= Lng::get('xrls_edit.max-time'); ?></td>
			<td><select name="lifetime"><option value="2850">2850</option><option value="1200">1200</option></select></td>
		</tr>
	<? else: ?>
		<tr style="display: none;"><td><input type="hidden" name="myproxy-autologin" value="1" /></td></tr>
	<? endif; ?>
	</table>
	
	<table style="margin: 1em auto;">
	<tr>
		<td>Предпочитаемый сервер<br />(оставить пустым, если не надо)</td>
		<td><input type="text" name="prefer-server" value="" /></td>
	</tr>
	</table>
	
	<div class="paragraph">
		<? if ($this->numSubmits > 1): ?>
			Будет запущено <?= $this->numSubmits; ?> задач.
		<? else: ?>
			Будет запущена одна задача.
		<? endif; ?>
	</div>
	<div class="paragraph">
		<input class="button" type="submit" name="action[task-set/submit]" value="<?= Lng::get('xrls_edit.start-task'); ?>" />
		<a class="button" href="<?= href('task-set/customize/'.$id); ?>"><?= Lng::get('xrls_edit.manage-files'); ?></a>
		<a class="button" href="<?= href('task-set/list'); ?>"><?= Lng::get('xrls_edit.go-to-task-list'); ?></a>
	</div>
    
    <div id="wait-popup" class="popup-container" style="display:none">
	<div class="popup">
		<div class="body-container">		
			<img src="images/load.gif" alt="<?= Lng::get('xrls_edit.starting-task'); ?>" />
			<p><?= Lng::get('xrls_edit.starting-task'); ?></p>
		</div>
	</div>
</div>
</form>