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

<h2>{lng snippet='xrls_edit.taskset'}</h2>

<form action="" method="post">
	{$formcode}
	<input type="hidden" name="id" value="{$id}" />
	
	<table style="text-align: left; margin: auto;">
	{if $showMyproxyLogin}
		<tr>
			<td>{lng snippet='xrls_edit.server'}</td>
			<td>
				<select name="server">
					{foreach from=$myproxyServersList item='item'}
						<option value="{$item.id}">{$item.name}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td>{lng snippet='xrls_edit.username'}</td>
			<td><input type="text" name="user[name]" value="" /></td>
		</tr>
		<tr>
			<td>{lng snippet='xrls_edit.password'}</td>
			<td><input type="password" name="user[pass]" value="" /></td>
		</tr>
		<tr>
			<td>{lng snippet='xrls_edit.max-time'}</td>
			<td><select name="lifetime"><option value="2850">2850</option><option value="1200">1200</option></select></td>
		</tr>
	{else}
		<tr style="display: none;"><td><input type="hidden" name="myproxy-autologin" value="1" /></td></tr>
	{/if}
	
	</table>
	
	<br /><br /><br />
	<table style="text-align: left; margin: auto;">
	{foreach from=$gridjobfile item=row}
		{if $row.type == 'config'}
			<tr><td>{$row.title}</td><td><input type="text" name="xrsl[{$row.name}]" value="{$row.value_escaped}" style="width: 400px;" /></td></tr>
		{/if}
	{/foreach}
	</table>
	
	<div class="paragraph">
		<input class="button" type="submit" name="action[task/run]" value="{lng snippet='xrls_edit.start-task'}" />
		
		<a class="button" href="{a href=task/list}">{lng snippet='xrls_edit.go-to-task-list'}</a>
	</div>
    
    <div id="wait-popup" class="popup-container" style="display:none">
	<div class="popup">
		<div class="body-container">		
			<img src="images/load.gif" alt="{lng snippet='xrls_edit.starting-task'}" />
			<p>{lng snippet='xrls_edit.starting-task'}</p>
		</div>
	</div>
</div>
</form>