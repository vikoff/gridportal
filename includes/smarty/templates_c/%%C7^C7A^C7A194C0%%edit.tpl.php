<?php /* Smarty version 2.6.26, created on 2012-01-15 20:13:55
         compiled from Profile/edit.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'lng', 'Profile/edit.tpl', 10, false),)), $this); ?>

<?php if ((isset($this->_tpl_vars['firstVisitText']) ? $this->_tpl_vars['firstVisitText'] : '')): ?>
	<div style="padding: 1em;">
		<?php echo (isset($this->_tpl_vars['firstVisitText']) ? $this->_tpl_vars['firstVisitText'] : ''); ?>

	</div>
<?php endif; ?>

<div id="profile-tabs" style="margin: 1em 2em;" class="tabs">
	<ul>
		<li><a href="#personal-data"><?php echo SmartyPlugins::function_lng(array('snippet' => 'profile.private-data'), $this);?>
</a></li>
		<li><a href="#check-voms"><?php echo SmartyPlugins::function_lng(array('snippet' => 'profile.check-voms'), $this);?>
</a></li>
		<li><a href="#temporary-cert"><?php echo SmartyPlugins::function_lng(array('snippet' => 'profile.temparal-cert-voms'), $this);?>
</a></li>
	</ul>
	<div class="cl"></div>
	
	<!-- tab 1 -->
	<div id="personal-data">

		<form id="regForm" name="regForm" action="<?php echo (isset($this->_tpl_vars['WWW_URI']) ? $this->_tpl_vars['WWW_URI'] : ''); ?>
#/personal-data" method="post">
			<?php echo (isset($this->_tpl_vars['formcode']) ? $this->_tpl_vars['formcode'] : ''); ?>

			<input type="hidden" name="action" value="profile/edit" />
			
			<?php echo (isset($this->_tpl_vars['profileEditError']) ? $this->_tpl_vars['profileEditError'] : ''); ?>

			
			<table class="std-grid narrow" style="text-align: left;">
				<col align="left" />
				<col align="left" />
				<tbody>
				<tr>
					<td colspan="2" style="text-align: center;">
						<h3 style="vertical-align:bottom;"> <img src="/images/icons/personal_data.gif" 
						alt="<?php echo SmartyPlugins::function_lng(array('snippet' => 'profile.chenge-privat-data'), $this);?>
" title="<?php echo SmartyPlugins::function_lng(array('snippet' => 'profile.chenge-privat-data'), $this);?>
"
						align="center" width=32 height=32 onmouseover="this.src='/images/icons/personal_data.a.gif'" 
						onmouseout="this.src='/images/icons/personal_data.gif'" /> 
						<?php echo SmartyPlugins::function_lng(array('snippet' => 'profile.chenge-privat-data'), $this);?>

						</h3>
					</td>
				</tr>
				<tr>
					<td class="left"><?php echo SmartyPlugins::function_lng(array('snippet' => 'profile.you-dn'), $this);?>
</td>
					<td><?php echo (isset($this->_tpl_vars['dn']) ? $this->_tpl_vars['dn'] : ''); ?>
</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align: center;">
						<b><?php echo SmartyPlugins::function_lng(array('snippet' => 'profile.contacts'), $this);?>
</b>
					</td>
				</tr>
				<tr>
					<td class="left"><?php echo SmartyPlugins::function_lng(array('snippet' => 'profile.e-mail'), $this);?>
</td>
					<td><input type="text" name="email" value="<?php echo (isset($this->_tpl_vars['profile']['email']) ? $this->_tpl_vars['profile']['email'] : ''); ?>
"></td>
				</tr>
				<tr>
					<td class="left"><?php echo SmartyPlugins::function_lng(array('snippet' => 'profile.phon'), $this);?>
</td>
					<td><input type="text" name="phone" value="<?php echo (isset($this->_tpl_vars['profile']['phone']) ? $this->_tpl_vars['profile']['phone'] : ''); ?>
"></td>
				</tr>
				<tr>
					<td class="left"><?php echo SmartyPlugins::function_lng(array('snippet' => 'profile.messager'), $this);?>
</td>
					<td><input type="text" name="messager" value="<?php echo (isset($this->_tpl_vars['profile']['messager']) ? $this->_tpl_vars['profile']['messager'] : ''); ?>
"></td>
				</tr>
				<tr>
					<td colspan="2" style="text-align: center;">
						<b><?php echo SmartyPlugins::function_lng(array('snippet' => 'profile.different'), $this);?>
</b>
					</td>
				</tr>
				<tr>
					<td class="left"><?php echo SmartyPlugins::function_lng(array('snippet' => 'profile.task-type'), $this);?>
<br /><?php echo SmartyPlugins::function_lng(array('snippet' => 'profile.software'), $this);?>
</td>
					<td>
						<?php $_from = (isset($this->_tpl_vars['softwareList']) ? $this->_tpl_vars['softwareList'] : ''); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['s']):
?>
							<label>
								<input type="checkbox" name="software[]" value="<?php echo (isset($this->_tpl_vars['s']['id']) ? $this->_tpl_vars['s']['id'] : ''); ?>
" <?php if ((isset($this->_tpl_vars['userSoftware'][(isset($this->_tpl_vars['s']['id']) ? $this->_tpl_vars['s']['id'] : '')]) ? $this->_tpl_vars['userSoftware'][(isset($this->_tpl_vars['s']['id']) ? $this->_tpl_vars['s']['id'] : '')] : '')): ?>checked="checked"<?php endif; ?> />
								<?php echo (isset($this->_tpl_vars['s']['name']) ? $this->_tpl_vars['s']['name'] : ''); ?>

							</label><br />
						<?php endforeach; endif; unset($_from); ?>
					</td>
				</tr>
				<tr>
					<td align="center" colspan="2">
						<input type="submit" value="<?php echo SmartyPlugins::function_lng(array('snippet' => 'save'), $this);?>
">
					</td>
				</tr>
				</tbody>
			</table>
			
		</form>
	</div>
	
	<!-- tab 2 -->
	<div id="check-voms">
	
		<?php echo (isset($this->_tpl_vars['checkVomsMessage']) ? $this->_tpl_vars['checkVomsMessage'] : ''); ?>

		<h3 style="vertical-align:bottom;"> <img src="/images/icons/check_voms.gif"
		alt="<?php echo SmartyPlugins::function_lng(array('snippet' => 'profile.check-voms'), $this);?>
" title="<?php echo SmartyPlugins::function_lng(array('snippet' => 'profile.check-voms'), $this);?>
"
		align="center" width=32 height=32 onmouseover="this.src='/images/icons/check_voms.a.gif'" 
		onmouseout="this.src='/images/icons/check_voms.gif'" /> <?php echo SmartyPlugins::function_lng(array('snippet' => 'profile.check-voms'), $this);?>

		</h3>
		
		<form action="<?php echo (isset($this->_tpl_vars['WWW_URI']) ? $this->_tpl_vars['WWW_URI'] : ''); ?>
#/check-voms" method="post">
			<input type="hidden" name="action" value="profile/check-voms" />
			<?php echo (isset($this->_tpl_vars['formcode']) ? $this->_tpl_vars['formcode'] : ''); ?>

			
			<?php echo SmartyPlugins::function_lng(array('snippet' => 'profile.enter-voms-for-check'), $this);?>

			<table align="center" style="text-align: left;">
				<?php $_from = (isset($this->_tpl_vars['vomsList']) ? $this->_tpl_vars['vomsList'] : ''); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['v']):
?>
					<tr>
						<td><input id="cb-vo-<?php echo (isset($this->_tpl_vars['v']['id']) ? $this->_tpl_vars['v']['id'] : ''); ?>
" type="checkbox" name="voms[]" value="<?php echo (isset($this->_tpl_vars['v']['id']) ? $this->_tpl_vars['v']['id'] : ''); ?>
" /></td>
						<td><label for="cb-vo-<?php echo (isset($this->_tpl_vars['v']['id']) ? $this->_tpl_vars['v']['id'] : ''); ?>
"><?php echo (isset($this->_tpl_vars['v']['name']) ? $this->_tpl_vars['v']['name'] : ''); ?>
</label></td>
						<td>
							<?php if ((isset($this->_tpl_vars['userVoms'][(isset($this->_tpl_vars['v']['id']) ? $this->_tpl_vars['v']['id'] : '')]) ? $this->_tpl_vars['userVoms'][(isset($this->_tpl_vars['v']['id']) ? $this->_tpl_vars['v']['id'] : '')] : '')): ?>
								<span style="font-size: 11px; color: green;"><?php echo SmartyPlugins::function_lng(array('snippet' => 'profile.you-member-vo'), $this);?>
</span>
							<?php else: ?>
								<span style="font-size: 11px;"><span style="color: red;"><?php echo SmartyPlugins::function_lng(array('snippet' => 'profile.you-not-member-vo'), $this);?>
</span> - <a href="https://<?php echo (isset($this->_tpl_vars['v']['url']) ? $this->_tpl_vars['v']['url'] : ''); ?>
?vo=<?php echo (isset($this->_tpl_vars['v']['name']) ? $this->_tpl_vars['v']['name'] : ''); ?>
&action=register" target="_blank"><?php echo SmartyPlugins::function_lng(array('snippet' => 'profile.register-vo'), $this);?>
</a></span>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; endif; unset($_from); ?>
			</table>
			<input type="submit" id="check_voms" value="<?php echo SmartyPlugins::function_lng(array('snippet' => 'profile.check'), $this);?>
" />
		</form>
		
		<?php echo (isset($this->_tpl_vars['setDefaultVomsMessage']) ? $this->_tpl_vars['setDefaultVomsMessage'] : ''); ?>

		
		<h3><?php echo SmartyPlugins::function_lng(array('snippet' => 'default-vo'), $this);?>
</h3>
		
		<form action="<?php echo (isset($this->_tpl_vars['WWW_URI']) ? $this->_tpl_vars['WWW_URI'] : ''); ?>
#/check-voms" method="post">
			<input type="hidden" name="action" value="profile/save-default-voms" />
			<?php echo (isset($this->_tpl_vars['formcode']) ? $this->_tpl_vars['formcode'] : ''); ?>

			<?php echo SmartyPlugins::function_lng(array('snippet' => 'enter-defaul-vo'), $this);?>

			<div class="vo-list">
				<?php $_from = (isset($this->_tpl_vars['projectList']) ? $this->_tpl_vars['projectList'] : ''); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['p']):
?>ppppppppppp[<?php echo (isset($this->_tpl_vars['p']['id']) ? $this->_tpl_vars['p']['id'] : ''); ?>
]ppppppppppp<?php endforeach; endif; unset($_from); ?>
				<?php $_from = (isset($this->_tpl_vars['userProjects']) ? $this->_tpl_vars['userProjects'] : ''); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['proj']):
?>
				<div class="vo-item <?php if ((isset($this->_tpl_vars['userProjects'][(isset($this->_tpl_vars['p']['id']) ? $this->_tpl_vars['p']['id'] : '')]) ? $this->_tpl_vars['userProjects'][(isset($this->_tpl_vars['p']['id']) ? $this->_tpl_vars['p']['id'] : '')] : '')): ?>vo-item-selected<?php endif; ?> <?php if (! (isset($this->_tpl_vars['projectList'][(isset($this->_tpl_vars['proj']['id']) ? $this->_tpl_vars['proj']['id'] : '')]['voms']) ? $this->_tpl_vars['projectList'][(isset($this->_tpl_vars['proj']['id']) ? $this->_tpl_vars['proj']['id'] : '')]['voms'] : '') || count ( (isset($this->_tpl_vars['projectList'][(isset($this->_tpl_vars['proj']['id']) ? $this->_tpl_vars['proj']['id'] : '')]['voms']) ? $this->_tpl_vars['projectList'][(isset($this->_tpl_vars['proj']['id']) ? $this->_tpl_vars['proj']['id'] : '')]['voms'] : '') ) == 0): ?>disabled<?php endif; ?>">
					<input type="checkbox" name="projects[]" value="<?php echo (isset($this->_tpl_vars['p']['id']) ? $this->_tpl_vars['p']['id'] : ''); ?>
" <?php if ((isset($this->_tpl_vars['userProjects'][(isset($this->_tpl_vars['p']['id']) ? $this->_tpl_vars['p']['id'] : '')]) ? $this->_tpl_vars['userProjects'][(isset($this->_tpl_vars['p']['id']) ? $this->_tpl_vars['p']['id'] : '')] : '')): ?>checked="checked"<?php endif; ?> />
					<?php echo (isset($this->_tpl_vars['proj']['name']) ? $this->_tpl_vars['proj']['name'] : ''); ?>

					<?php if ((isset($this->_tpl_vars['projectList'][(isset($this->_tpl_vars['proj']['id']) ? $this->_tpl_vars['proj']['id'] : '')]['voms']) ? $this->_tpl_vars['projectList'][(isset($this->_tpl_vars['proj']['id']) ? $this->_tpl_vars['proj']['id'] : '')]['voms'] : '')): ?>
						<select name="projects[<?php echo (isset($this->_tpl_vars['proj']['id']) ? $this->_tpl_vars['proj']['id'] : ''); ?>
]">
							<option value=""><?php echo SmartyPlugins::function_lng(array('snippet' => 'enter-vo'), $this);?>
</option>
							<?php $_from = (isset($this->_tpl_vars['projectList'][(isset($this->_tpl_vars['proj']['id']) ? $this->_tpl_vars['proj']['id'] : '')]['voms']) ? $this->_tpl_vars['projectList'][(isset($this->_tpl_vars['proj']['id']) ? $this->_tpl_vars['proj']['id'] : '')]['voms'] : ''); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['vid'] => $this->_tpl_vars['vtitle']):
?>
								<?php if ((isset($this->_tpl_vars['userVoms'][$this->_tpl_vars['vid']]) ? $this->_tpl_vars['userVoms'][$this->_tpl_vars['vid']] : '')): ?>
								<option value="<?php echo (isset($this->_tpl_vars['vid']) ? $this->_tpl_vars['vid'] : ''); ?>
" <?php if ((isset($this->_tpl_vars['defaultVoms'][(isset($this->_tpl_vars['proj']['id']) ? $this->_tpl_vars['proj']['id'] : '')]) ? $this->_tpl_vars['defaultVoms'][(isset($this->_tpl_vars['proj']['id']) ? $this->_tpl_vars['proj']['id'] : '')] : '') == (isset($this->_tpl_vars['vid']) ? $this->_tpl_vars['vid'] : '')): ?>selected="selected"<?php endif; ?>><?php echo (isset($this->_tpl_vars['vtitle']) ? $this->_tpl_vars['vtitle'] : ''); ?>
</option>
								<?php endif; ?>
							<?php endforeach; endif; unset($_from); ?>
						</select>
					<?php endif; ?>
				</div>
				<?php endforeach; endif; unset($_from); ?>
			</div>
			<input type="submit" value="<?php echo SmartyPlugins::function_lng(array('snippet' => 'save'), $this);?>
" />
		</form>
	</div>
	
	<!-- tab 3 -->
	<div id="temporary-cert">
		
		<?php echo (isset($this->_tpl_vars['checkSertMessage']) ? $this->_tpl_vars['checkSertMessage'] : ''); ?>

		
		<h3 style="vertical-align:bottom;"> <img src="/images/icons/temporary_certificate.gif"
		alt="<?php echo SmartyPlugins::function_lng(array('snippet' => 'project-temporary-cert'), $this);?>
" title="<?php echo SmartyPlugins::function_lng(array('snippet' => 'project-temporary-cert'), $this);?>
"
		align="center" width=32 height=32 onmouseover="this.src='/images/icons/temporary_certificate.a.gif'" 
		onmouseout="this.src='/images/icons/temporary_certificate.gif'" /> <?php echo SmartyPlugins::function_lng(array('snippet' => 'project-temporary-cert'), $this);?>

		</h3>
		
		<form action="<?php echo (isset($this->_tpl_vars['WWW_URI']) ? $this->_tpl_vars['WWW_URI'] : ''); ?>
#/temporary-cert" method="post">
			<input type="hidden" name="action" value="profile/check-cert" />
			<?php echo (isset($this->_tpl_vars['formcode']) ? $this->_tpl_vars['formcode'] : ''); ?>

			
			<?php echo SmartyPlugins::function_lng(array('snippet' => 'enter-parametrs-myproxy'), $this);?>

			
			<p>
				<label>
					<input id="cert-manual-logon-inp" type="checkbox" name="manual-login" value="1" <?php if ((isset($this->_tpl_vars['myproxy_manual_login']) ? $this->_tpl_vars['myproxy_manual_login'] : '')): ?>checked="checked"<?php endif; ?> />
					<?php echo SmartyPlugins::function_lng(array('snippet' => 'profile.myproxy.not-register'), $this);?>

				</label>
				<?php  echo Page::getHelpIcon('profile.projects-help')  ?>
			</p>
			<table id="cert-auto-login-box" align="center" style="margin: 1em auto; <?php if ((isset($this->_tpl_vars['myproxy_manual_login']) ? $this->_tpl_vars['myproxy_manual_login'] : '')): ?>display: none;<?php endif; ?>">
				<col align="left" />
				<col align="left" />
				<tr>
					<td><?php echo SmartyPlugins::function_lng(array('snippet' => 'login'), $this);?>
</td>
					<td><input type="text" name="login" value="<?php echo (isset($this->_tpl_vars['myproxy_login']) ? $this->_tpl_vars['myproxy_login'] : ''); ?>
" /></td>
				</tr>
				<tr>
					<td><?php echo SmartyPlugins::function_lng(array('snippet' => 'password'), $this);?>
</td>
					<td><input type="password" name="password" value="<?php echo (isset($this->_tpl_vars['myproxy_password']) ? $this->_tpl_vars['myproxy_password'] : ''); ?>
" /></td>
				</tr>
				<tr>
					<td><?php echo SmartyPlugins::function_lng(array('snippet' => 'server'), $this);?>
</td>
					<td>
						<select id="myproxy-server" name="server">
							<option value=""><?php echo SmartyPlugins::function_lng(array('snippet' => 'profile.myproxy.select-server'), $this);?>
...</option>
							<?php $_from = (isset($this->_tpl_vars['myproxyServersList']) ? $this->_tpl_vars['myproxyServersList'] : ''); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
								<option <?php if ((isset($this->_tpl_vars['item']['id']) ? $this->_tpl_vars['item']['id'] : '') == (isset($this->_tpl_vars['myproxy_server_id']) ? $this->_tpl_vars['myproxy_server_id'] : '')): ?>selected="selected"<?php endif; ?> value="<?php echo (isset($this->_tpl_vars['item']['id']) ? $this->_tpl_vars['item']['id'] : ''); ?>
"><?php echo (isset($this->_tpl_vars['item']['name']) ? $this->_tpl_vars['item']['name'] : ''); ?>
</option>
							<?php endforeach; endif; unset($_from); ?>
							<option value="custom"><?php echo SmartyPlugins::function_lng(array('snippet' => 'profile.myproxy.enter-monual-server'), $this);?>
</option>
						</select>
					</td>
				</tr>
				<tbody id="custom-server-block" style="display: none;">
					<tr><td><?php echo SmartyPlugins::function_lng(array('snippet' => 'profile.myproxy.server-host'), $this);?>
</td><td><input type="name" name="custom-server" value="" /></td></tr>
					<tr><td><?php echo SmartyPlugins::function_lng(array('snippet' => 'profile.myproxy.server-port'), $this);?>
</td><td><input type="name" name="custom-server-port" value="7512" /></td></tr>
				</tbody>
				<tr>
					<td><?php echo SmartyPlugins::function_lng(array('snippet' => 'profile.myproxy.cert_ttl'), $this);?>
</td>
					<td>
						<select name="lifetime">
							<option value="86400">1 <?php echo SmartyPlugins::function_lng(array('snippet' => 'profile.myproxy.day'), $this);?>
</option>
							<option value="604800">1 <?php echo SmartyPlugins::function_lng(array('snippet' => 'profile.myproxy.week'), $this);?>
</option>
							<option value="2592000">1 <?php echo SmartyPlugins::function_lng(array('snippet' => 'profile.myproxy.month'), $this);?>
</option>
							<option value="15552000">6 <?php echo SmartyPlugins::function_lng(array('snippet' => 'profile.myproxy.6month'), $this);?>
</option>
						</select>
					</td>
				</tr>
			</table>
			<input type="submit" value="<?php echo SmartyPlugins::function_lng(array('snippet' => 'save'), $this);?>
" />
		</form>
	</div>
</div>

<div id="wait-popup" class="popup-container" style="display:none">
	<div class="popup">
		<div class="body-container">		
			<img src="images/load.gif" alt="<?php echo SmartyPlugins::function_lng(array('snippet' => 'profile.check-voms'), $this);?>
" />
			<p><?php echo SmartyPlugins::function_lng(array('snippet' => 'profile.check-voms'), $this);?>
</p>
		</div>
	</div>
</div>


<script type="text/javascript"><?php echo '
	
	$(function(){
		
		/*$( "#profile-tabs" ).tabs({
			// selected: data.value.substr(1),
			//fx: {opacity: \'toggle\', duration: \'slow\' }
		});	*/
		tabs("#profile-tabs");
		
		$(\'#myproxy-server\').change(function(){
			$(\'#custom-server-block\')[$(this).val() == \'custom\' ? \'show\' : \'hide\']()
		}).change();
		
		// var_dump($.address.value(), \'E=function\');
		// alert($("#profile-tabs ul:first a[href=\'#" + data.value.substr(1) + "\']").parent().index());
		$("#profile-tabs ul:first a").address();

		$.address.externalChange(function(data){
			//$( "#profile-tabs" ).tabs(\'select\', data.value.substr(1));
			tabs("#profile-tabs");
		});
		
		function certManualLogonCheck(){
			$(\'#cert-auto-login-box\')[$(\'#cert-manual-logon-inp\').attr(\'checked\') ? \'fadeOut\' : \'fadeIn\']();
		}
		
		$(\'#cert-manual-logon-inp\').change(certManualLogonCheck);
		certManualLogonCheck();

});

'; ?>
</script>

<script type="text/javascript" language="javascript"><?php echo '

$(document).ready(function() {

	jQuery.fn.center = function () {
		this.css("position","fixed");
		this.css("top", ( $(window).height() - this.height()) / 2 + "px");
		this.css("left", ( $(window).width() - this.width()) / 2 + "px");
		return this;
	}


    $(window).resize(function() {
        $(".popup-container").css({width: $(this).width() + \'px\', height: $(this).height() + \'px\'});
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
			$(this).contents(\'input[type=checkbox]\').removeProp("checked");
		}
		else {
			$(this).addClass("vo-item-selected");
			$(this).contents(\'input[type=checkbox]\').prop("checked", "checked");
		}
	});


});
'; ?>
</script>