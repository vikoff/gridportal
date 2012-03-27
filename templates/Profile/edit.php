
<? if ($firstVisitText){ ?>
	<div style="padding: 1em;">
		<?= $firstVisitText ?>
	</div>
<? } ?>

<div id="profile-tabs" style="margin: 1em 2em;" class="tabs">
	<ul>
		<li><a href="#personal-data"><?= Lng::get('profile.private-data') ?></a></li>
		<li><a href="#check-voms"><?= Lng::get('profile.check-voms') ?></a></li>
		<li><a href="#temporary-cert"><?= Lng::get('profile.temparal-cert-voms') ?></a></li>
	</ul>
	<div class="cl"></div>
	
	<!-- tab 1 -->
	<div id="personal-data">

		<form id="regForm" name="regForm" action="<?= WWW_URI ?>#/personal-data" method="post">
			<?= FORMCODE ?>
			<input type="hidden" name="action" value="profile/edit" />
			
			<? foreach ($projectList as $p){ ?>
				<? if (!empty($userProjects[$p['id']])){ ?><input type="hidden" name="projects[]" value="<?= $p['id'] ?>" /><? } ?>
			<? } ?>
			
			<?= $this->profileEditError ?>
			
			<table class="std-grid narrow" style="text-align: left;">
				<col align="left" />
				<col align="left" />
				<tbody>
				<tr>
					<td colspan="2" style="text-align: center;">
						<h3 style="vertical-align:bottom;"> <img src="/images/icons/personal_data.gif" 
						alt="<?= Lng::get('profile.chenge-privat-data') ?>" title="<?= Lng::get('profile.chenge-privat-data') ?>"
						align="center" width=32 height=32 onmouseover="this.src='/images/icons/personal_data.a.gif'" 
						onmouseout="this.src='/images/icons/personal_data.gif'" /> 
						<?= Lng::get('profile.chenge-privat-data') ?>
						</h3>
					</td>
				</tr>
				<tr>
					<td class="left"><?= Lng::get('profile.you-dn') ?></td>
					<td><?= $dn ?></td>
				</tr>
				<tr>
					<td colspan="2" style="text-align: center;">
						<b><?= Lng::get('profile.contacts') ?></b>
					</td>
				</tr>
				<tr>
					<td class="left"><?= Lng::get('profile.e-mail') ?></td>
					<td><input type="text" name="email" value="<?= isset($this->profile['email']) ? $this->profile['email'] : '' ?>"></td>
				</tr>
				<tr>
					<td class="left"><?= Lng::get('profile.phon') ?></td>
					<td><input type="text" name="phone" value="<?= isset($this->profile['phone']) ? $this->profile['phone'] : '' ?>"></td>
				</tr>
				<tr>
					<td class="left"><?= Lng::get('profile.messager') ?></td>
					<td><input type="text" name="messager" value="<?= isset($this->profile['messager']) ? $this->profile['messager'] : '' ?>"></td>
				</tr>
				<tr>
					<td colspan="2" style="text-align: center;">
						<b><?= Lng::get('profile.different') ?></b>
					</td>
				</tr>
				<tr>
					<td class="left"><?= Lng::get('profile.task-type') ?><br /><?= Lng::get('profile.software') ?></td>
					<td>
						<? foreach ($this->softwareList as $s){ ?>
							<label>
								<input type="checkbox" name="software[]" value="<?= $s['id'] ?>" <? if (!empty($this->userSoftware[$s['id']])){ ?>checked="checked"<? } ?> />
								<?= $s['name'] ?>
							</label><br />
						<? } ?>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align: center;">
						<b><?= Lng::get('profile.notifies') ?></b>
					</td>
				</tr>
				<tr>
					<td class="left"><?= Lng::get('profile.notify-the-tasks') ?></td>
					<td>
						<? //$checked = !isset($this->profile['task_fetch_notify']) || !empty($this->profile['task_fetch_notify']); ?>
						<? $checked = !empty($this->profile['task_fetch_notify']); ?>
						<input type="checkbox" name="task_fetch_notify" value="1" <?= $checked ? 'checked="checked"' : ''; ?> /></td>
				</tr>
				<tr>
					<td align="center" colspan="2">
						<input type="submit" value="<?= Lng::get('save') ?>">
					</td>
				</tr>
				</tbody>
			</table>
			
		</form>
	</div>
	
	<!-- tab 2 -->
	<div id="check-voms">
	
		<?= $checkVomsMessage ?>
		<h3 style="vertical-align:bottom;"> <img src="/images/icons/check_voms.gif"
		alt="<?= Lng::get('profile.check-voms') ?>" title="<?= Lng::get('profile.check-voms') ?>"
		align="center" width=32 height=32 onmouseover="this.src='/images/icons/check_voms.a.gif'" 
		onmouseout="this.src='/images/icons/check_voms.gif'" /> <?= Lng::get('profile.check-voms') ?>
		</h3>
		
		<form action="<?= WWW_URI ?>#/check-voms" method="post">
			<input type="hidden" name="action" value="profile/check-voms" />
			<?= FORMCODE ?>
			
			<?= Lng::get('profile.enter-voms-for-check') ?>
			<div style="margin:0 auto 10px;width:auto;">
				<table align="center" style="text-align: left;">
					<? foreach ($vomsList as $v){ ?>
						<tr>
							<td><input id="cb-vo-<?= $v['id'] ?>" type="checkbox" name="voms[]" value="<?= $v['id'] ?>" /></td>
							<td><label for="cb-vo-<?= $v['id'] ?>"><?= $v['name'] ?></label></td>
							<td>
								<? if (!empty($userVoms[$v['id']])){ ?>
									<span style="font-size: 11px; color: green;"><?= Lng::get('profile.you-member-vo') ?></span>
								<? } else { ?>
									<span style="font-size: 11px;"><span style="color: red;"><?= Lng::get('profile.you-not-member-vo') ?></span> - <a href="https://<?= $v['url'] ?>?vo=<?= $v['name'] ?>&action=register" target="_blank"><?= Lng::get('profile.register-vo') ?></a></span>
								<? } ?>
							</td>
						</tr>
					<? } ?>
					<tr>
						<td colspan="4" style="text-align:right;font-size: 11px;">
							<a href="http://crimeaecogrid.reformal.ru/" target="_blank"><?= Lng::get('profile.user-vo-not-in-list') ?></a>
						</td>
					</tr>
					<tr>
						<td colspan="4" style="text-align:center;">
							<input type="submit" id="check_voms" value="<?= Lng::get('profile.check') ?>" />
						</td>
					</tr>
				</table>
			</div>
		</form>
		
		<?= $setDefaultVomsMessage ?>
		
		<h3><?= Lng::get('default-vo') ?></h3>
		
		<form action="<?= WWW_URI ?>#/check-voms" method="post">
			<input type="hidden" name="action" value="profile/save-default-voms" />
			<?= FORMCODE ?>
			
			<? foreach ($this->softwareList as $s){ ?>
				<input type="hidden" name="software[]" <? if (!empty($this->userSoftware[$s['id']])){ ?>value="<?= $s['id'] ?>"<? } ?> />
			<? } ?>
			<input type="hidden" name="email" value="<?= isset($this->profile['email']) ? $this->profile['email'] : '' ?>" />
			<input type="hidden" name="phone" value="<?= isset($this->profile['phone']) ? $this->profile['phone'] : '' ?>" />
			<input type="hidden" name="messager" value="<?= isset($this->profile['messager']) ? $this->profile['messager'] : '' ?>" />
			
			<?= Lng::get('enter-defaul-vo') ?>
			<div class="vo-list"><? //print_r($projectList); print_r($userProjects); die; ?>
				<? foreach ($projectList as $p){ ?>
				
					<? if ($p['inactive'])
						continue; ?>
						
				<? $proj = isset($userProjects[$p['id']]) ? $userProjects[$p['id']] : null;
					//foreach ($userProjects as $i => $proj){ ?>
				<div class="vo-item <? if (!empty($proj)){ ?>vo-item-selected<? } ?> <? if (!$projectList[$p['id']]['voms'] or count($projectList[$p['id']]['voms']) == 0){ ?>disabled<? } ?>">
					<input type="checkbox" name="projects[]" value="<?= $p['id'] ?>" <? if (!empty($proj)){ ?>checked="checked"<? } ?> />
					<?= $p['name'] ?>
					<select name="voms_projects[<?= $p['id'] ?>]" <? if (!count($projectList[$p['id']]['voms'])){ ?>disabled<? } ?>>
						<option value=""><?= Lng::get('enter-vo') ?></option>
						<? foreach ($projectList[$p['id']]['voms'] as $vid => $vtitle){ ?>
							<? if (isset($userVoms[$vid])){ ?>
							<option value="<?= $vid ?>" <? if (!empty($defaultVoms[$p['id']]) && $defaultVoms[$p['id']] == $vid){ ?>selected="selected"<? } ?>><?= $vtitle ?></option>
							<? } ?>
						<? } ?>
					</select>
				</div>
				<? } ?>
			</div>
			<input type="submit" value="<?= Lng::get('save') ?>" />
		</form>
	</div>
	
	<!-- tab 3 -->
	<div id="temporary-cert">
		
		<?= $checkSertMessage ?>
		
		<h3 style="vertical-align:bottom;"> <img src="/images/icons/temporary_certificate.gif"
		alt="<?= Lng::get('project-temporary-cert') ?>" title="<?= Lng::get('project-temporary-cert') ?>"
		align="center" width=32 height=32 onmouseover="this.src='/images/icons/temporary_certificate.a.gif'" 
		onmouseout="this.src='/images/icons/temporary_certificate.gif'" /> <?= Lng::get('project-temporary-cert') ?>
		</h3>
		
		<form action="<?= WWW_URI ?>#/temporary-cert" method="post">
			<input type="hidden" name="action" value="profile/check-cert" />
			<?= FORMCODE ?>
			
			<?= Lng::get('enter-parametrs-myproxy') ?>
			
			<p>
				<label>
					<input id="cert-manual-logon-inp" type="checkbox" name="manual-login" value="1" <? if ($myproxy_manual_login){ ?>checked="checked"<? } ?> />
					<?= Lng::get('profile.myproxy.not-register') ?>
				</label>
				<?= Page::getHelpIcon('profile.projects-help') ?>
			</p>
			<table id="cert-auto-login-box" align="center" style="margin: 1em auto; <? if ($myproxy_manual_login){ ?>display: none;<? } ?>">
				<col align="left" />
				<col align="left" />
				<tr>
					<td><?= Lng::get('login') ?></td>
					<td><input type="text" name="login" value="<?= $myproxy_login ?>" /></td>
				</tr>
				<tr>
					<td><?= Lng::get('password') ?></td>
					<td><input type="password" name="password" value="<?= $myproxy_password ?>" /></td>
				</tr>
				<tr>
					<td><?= Lng::get('server') ?></td>
					<td>
						<select id="myproxy-server" name="server">
							<option value=""><?= Lng::get('profile.myproxy.select-server') ?>...</option>
							<? foreach ($myproxyServersList as $item){ ?>
								<option <? if ($item['id'] == $myproxy_server_id){ ?>selected="selected"<? } ?> value="<?= $item['id'] ?>"><?= $item['name'] ?></option>
							<? } ?>
							<option value="custom"><?= Lng::get('profile.myproxy.enter-monual-server') ?></option>
						</select>
					</td>
				</tr>
				<tbody id="custom-server-block" style="display: none;">
					<tr><td><?= Lng::get('profile.myproxy.server-host') ?></td><td><input type="name" name="custom-server" value="" /></td></tr>
					<tr><td><?= Lng::get('profile.myproxy.server-port') ?></td><td><input type="name" name="custom-server-port" value="7512" /></td></tr>
				</tbody>
				<tr>
					<td><?= Lng::get('profile.myproxy.cert_ttl') ?></td>
					<td>
						<select name="lifetime">
							<option value="86400">1 <?= Lng::get('profile.myproxy.day') ?></option>
							<option value="604800">1 <?= Lng::get('profile.myproxy.week') ?></option>
							<option value="2592000">1 <?= Lng::get('profile.myproxy.month') ?></option>
							<option value="15552000">6 <?= Lng::get('profile.myproxy.6month') ?></option>
						</select>
					</td>
				</tr>
			</table>
			<input type="submit" value="<?= Lng::get('save') ?>" />
		</form>
	</div>
</div>

<div id="wait-popup" class="popup-container" style="display:none">
	<div class="popup">
		<div class="body-container">		
			<img src="images/load.gif" alt="<?= Lng::get('profile.check-voms') ?>" />
			<p><?= Lng::get('profile.check-voms') ?></p>
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
		
		// var_dump($['address'].value(), 'E=function');
		// alert($("#profile-tabs ul:first a[href='#" + data.value.substr(1) + "']").parent().index());
		$("#profile-tabs ul:first a").address();

		$['address'].externalChange(function(data){
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