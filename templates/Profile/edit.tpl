
{if $firstVisitText}
	<div style="padding: 1em;">
		{$firstVisitText}
	</div>
{/if}

<div id="profile-tabs" style="margin: 1em 2em;" class="tabs">
	<ul>
		<li><a href="#personal-data">{lng snippet='profile.private-data'}</a></li>
		<li><a href="#check-voms">{lng snippet='profile.check-voms'}</a></li>
		<li><a href="#temporary-cert">{lng snippet='profile.temparal-cert-voms'}</a></li>
	</ul>
	<div class="cl"></div>
	
	<!-- tab 1 -->
	<div id="personal-data">

		<form id="regForm" name="regForm" action="{$WWW_URI}#/personal-data" method="post">
			{$formcode}
			<input type="hidden" name="action" value="profile/edit" />
			
			{$profileEditError}
			
			<table class="std-grid narrow" style="text-align: left;">
				<col align="left" />
				<col align="left" />
				<tbody>
				<tr>
					<td colspan="2" style="text-align: center;">
						<h3 style="vertical-align:bottom;"> <img src="/images/icons/personal_data.gif" 
						alt="{lng snippet='profile.chenge-privat-data'}" title="{lng snippet='profile.chenge-privat-data'}"
						align="center" width=32 height=32 onmouseover="this.src='/images/icons/personal_data.a.gif'" 
						onmouseout="this.src='/images/icons/personal_data.gif'" /> 
						{lng snippet='profile.chenge-privat-data'}
						</h3>
					</td>
				</tr>
				<tr>
					<td class="left">{lng snippet='profile.you-dn'}</td>
					<td>{$dn}</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align: center;">
						<b>{lng snippet='profile.contacts'}</b>
					</td>
				</tr>
				<tr>
					<td class="left">{lng snippet='profile.e-mail'}</td>
					<td><input type="text" name="email" value="{$profile.email}"></td>
				</tr>
				<tr>
					<td class="left">{lng snippet='profile.phon'}</td>
					<td><input type="text" name="phone" value="{$profile.phone}"></td>
				</tr>
				<tr>
					<td class="left">{lng snippet='profile.messager'}</td>
					<td><input type="text" name="messager" value="{$profile.messager}"></td>
				</tr>
				<tr>
					<td colspan="2" style="text-align: center;">
						<b>{lng snippet='profile.different'}</b>
					</td>
				</tr>
				<tr>
					<td class="left">{lng snippet='profile.task-type'}<br />{lng snippet='profile.software'}</td>
					<td>
						{foreach from=$softwareList item='s'}
							<label>
								<input type="checkbox" name="software[]" value="{$s.id}" {if $userSoftware[$s.id]}checked="checked"{/if} />
								{$s.name}
							</label><br />
						{/foreach}
					</td>
				</tr>
				<tr>
					<td align="center" colspan="2">
						<input type="submit" value="{lng snippet='save'}">
					</td>
				</tr>
				</tbody>
			</table>
			
		</form>
	</div>
	
	<!-- tab 2 -->
	<div id="check-voms">
	
		{$checkVomsMessage}
		<h3 style="vertical-align:bottom;"> <img src="/images/icons/check_voms.gif"
		alt="{lng snippet='profile.check-voms'}" title="{lng snippet='profile.check-voms'}"
		align="center" width=32 height=32 onmouseover="this.src='/images/icons/check_voms.a.gif'" 
		onmouseout="this.src='/images/icons/check_voms.gif'" /> {lng snippet='profile.check-voms'}
		</h3>
		
		<form action="{$WWW_URI}#/check-voms" method="post">
			<input type="hidden" name="action" value="profile/check-voms" />
			{$formcode}
			
			{lng snippet='profile.enter-voms-for-check'}
			<table align="center" style="text-align: left;">
				{foreach from=$vomsList item='v'}
					<tr>
						<td><input id="cb-vo-{$v.id}" type="checkbox" name="voms[]" value="{$v.id}" /></td>
						<td><label for="cb-vo-{$v.id}">{$v.name}</label></td>
						<td>
							{if $userVoms[$v.id]}
								<span style="font-size: 11px; color: green;">{lng snippet='profile.you-member-vo'}</span>
							{else}
								<span style="font-size: 11px;"><span style="color: red;">{lng snippet='profile.you-not-member-vo'}</span> - <a href="https://{$v.url}?vo={$v.name}&action=register" target="_blank">{lng snippet='profile.register-vo'}</a></span>
							{/if}
						</td>
					</tr>
				{/foreach}
			</table>
			<input type="submit" id="check_voms" value="{lng snippet='profile.check'}" />
		</form>
		
		{$setDefaultVomsMessage}
		
		<h3>{lng snippet='default-vo'}</h3>
		
		<form action="{$WWW_URI}#/check-voms" method="post">
			<input type="hidden" name="action" value="profile/save-default-voms" />
			{$formcode}
			{lng snippet='enter-defaul-vo'}
			<div class="vo-list">
				{foreach from=$projectList item='p'}ppppppppppp[{$p.id}]ppppppppppp{/foreach}
				{foreach from=$userProjects item='proj'}
				<div class="vo-item {if $userProjects[$p.id]}vo-item-selected{/if} {if not $projectList[$proj.id].voms or count($projectList[$proj.id].voms) == 0}disabled{/if}">
					<input type="checkbox" name="projects[]" value="{$p.id}" {if $userProjects[$p.id]}checked="checked"{/if} />
					{$proj.name}
					{if $projectList[$proj.id].voms}
						<select name="projects[{$proj.id}]">
							<option value="">{lng snippet='enter-vo'}</option>
							{foreach from=$projectList[$proj.id].voms item='vtitle' key='vid'}
								{if $userVoms[$vid]}
								<option value="{$vid}" {if $defaultVoms[$proj.id] == $vid}selected="selected"{/if}>{$vtitle}</option>
								{/if}
							{/foreach}
						</select>
					{/if}
				</div>
				{/foreach}
			</div>
			<input type="submit" value="{lng snippet='save'}" />
		</form>
	</div>
	
	<!-- tab 3 -->
	<div id="temporary-cert">
		
		{$checkSertMessage}
		
		<h3 style="vertical-align:bottom;"> <img src="/images/icons/temporary_certificate.gif"
		alt="{lng snippet='project-temporary-cert'}" title="{lng snippet='project-temporary-cert'}"
		align="center" width=32 height=32 onmouseover="this.src='/images/icons/temporary_certificate.a.gif'" 
		onmouseout="this.src='/images/icons/temporary_certificate.gif'" /> {lng snippet='project-temporary-cert'}
		</h3>
		
		<form action="{$WWW_URI}#/temporary-cert" method="post">
			<input type="hidden" name="action" value="profile/check-cert" />
			{$formcode}
			
			{lng snippet='enter-parametrs-myproxy'}
			
			<p>
				<label>
					<input id="cert-manual-logon-inp" type="checkbox" name="manual-login" value="1" {if $myproxy_manual_login}checked="checked"{/if} />
					{lng snippet='profile.myproxy.not-register'}
				</label>
				{php} echo Page::getHelpIcon('profile.projects-help') {/php}
			</p>
			<table id="cert-auto-login-box" align="center" style="margin: 1em auto; {if $myproxy_manual_login}display: none;{/if}">
				<col align="left" />
				<col align="left" />
				<tr>
					<td>{lng snippet='login'}</td>
					<td><input type="text" name="login" value="{$myproxy_login}" /></td>
				</tr>
				<tr>
					<td>{lng snippet='password'}</td>
					<td><input type="password" name="password" value="{$myproxy_password}" /></td>
				</tr>
				<tr>
					<td>{lng snippet='server'}</td>
					<td>
						<select id="myproxy-server" name="server">
							<option value="">{lng snippet='profile.myproxy.select-server'}...</option>
							{foreach from=$myproxyServersList item='item'}
								<option {if $item.id == $myproxy_server_id}selected="selected"{/if} value="{$item.id}">{$item.name}</option>
							{/foreach}
							<option value="custom">{lng snippet='profile.myproxy.enter-monual-server'}</option>
						</select>
					</td>
				</tr>
				<tbody id="custom-server-block" style="display: none;">
					<tr><td>{lng snippet='profile.myproxy.server-host'}</td><td><input type="name" name="custom-server" value="" /></td></tr>
					<tr><td>{lng snippet='profile.myproxy.server-port'}</td><td><input type="name" name="custom-server-port" value="7512" /></td></tr>
				</tbody>
				<tr>
					<td>{lng snippet='profile.myproxy.cert_ttl'}</td>
					<td>
						<select name="lifetime">
							<option value="86400">1 {lng snippet='profile.myproxy.day'}</option>
							<option value="604800">1 {lng snippet='profile.myproxy.week'}</option>
							<option value="2592000">1 {lng snippet='profile.myproxy.month'}</option>
							<option value="15552000">6 {lng snippet='profile.myproxy.6month'}</option>
						</select>
					</td>
				</tr>
			</table>
			<input type="submit" value="{lng snippet='save'}" />
		</form>
	</div>
</div>

<div id="wait-popup" class="popup-container" style="display:none">
	<div class="popup">
		<div class="body-container">		
			<img src="images/load.gif" alt="{lng snippet='profile.check-voms'}" />
			<p>{lng snippet='profile.check-voms'}</p>
		</div>
	</div>
</div>


<script type="text/javascript">
	
	$(function(){
		
		/*$( "#profile-tabs" ).tabs({
			// selected: data.value.substr(1),
			//fx: {opacity: 'toggle', duration: 'slow' }
		});	*/
		tabs("#profile-tabs");
		
		$('#myproxy-server').change(function(){
			$('#custom-server-block')[$(this).val() == 'custom' ? 'show' : 'hide']()
		}).change();
		
		// var_dump($.address.value(), 'E=function');
		// alert($("#profile-tabs ul:first a[href='#" + data.value.substr(1) + "']").parent().index());
		$("#profile-tabs ul:first a").address();

		$.address.externalChange(function(data){
			//$( "#profile-tabs" ).tabs('select', data.value.substr(1));
			tabs("#profile-tabs");
		});
		
		function certManualLogonCheck(){
			$('#cert-auto-login-box')[$('#cert-manual-logon-inp').attr('checked') ? 'fadeOut' : 'fadeIn']();
		}
		
		$('#cert-manual-logon-inp').change(certManualLogonCheck);
		certManualLogonCheck();

});

</script>

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

    $("input[id=check_voms]").click(function() {
		$(window).resize();
		$(".popup").center();
		$(".popup .body-container").center();
		$("#wait-popup").show();
    });
	
	$(".vo-item").click(function(e){
		if ($(e.target).is(".vo-item select, .vo-item select *")) return;
		if ($(this).hasClass("vo-item-selected")){
			$(this).removeClass("vo-item-selected");
			$(this).contents('input[type=checkbox]').removeProp("checked");
		}
		else {
			$(this).addClass("vo-item-selected");
			$(this).contents('input[type=checkbox]').prop("checked", "checked");
		}
	});


});
</script>