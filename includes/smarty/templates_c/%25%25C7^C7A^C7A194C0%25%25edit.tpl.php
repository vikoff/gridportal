<?php /* Smarty version 2.6.26, created on 2011-08-24 20:23:52
         compiled from Profile/edit.tpl */ ?>

<div id="profile-tabs" style="margin: 1em 2em;">
	<ul>
		<li><a href="#profile-tabs-1">Личные данные</a></li>
		<li><a href="#profile-tabs-2">Проверка VOMS</a></li>
	</ul>
	
	<!-- tab 1 -->
	<div id="profile-tabs-1">

		<form id="regForm" name="regForm" action="" method="post">
			<?php echo (isset($this->_tpl_vars['formcode']) ? $this->_tpl_vars['formcode'] : ''); ?>

			<input type="hidden" name="action" value="profile/edit" />
			
			<table class="std-grid narrow">
				<col align="left" />
				<col align="left" />
				<tbody>
				<tr>
					<td colspan='2'>
						<h3>Изменение личных данных</h3>
						<?php echo (isset($this->_tpl_vars['userError']) ? $this->_tpl_vars['userError'] : ''); ?>

					</td>
				</tr>
				<tr>
					<td class="left">Фамилия:</td>
					<td><input type="text" name="surname" value="<?php echo (isset($this->_tpl_vars['surname']) ? $this->_tpl_vars['surname'] : ''); ?>
"></td>
				</tr>
				<tr>
					<td class="left">Имя:</td>
					<td><input type="text" name="name" value="<?php echo (isset($this->_tpl_vars['name']) ? $this->_tpl_vars['name'] : ''); ?>
"></td>
				</tr>
				<tr>
					<td class="left">Отчество:</td>
					<td><input type="text" name="patronymic" value="<?php echo (isset($this->_tpl_vars['patronymic']) ? $this->_tpl_vars['patronymic'] : ''); ?>
"></td>
				</tr>
				<tr>
					<td class="left">Пол:</td>
					<td>
						<select name="sex">
							<option value="none" <?php if ((isset($this->_tpl_vars['sex']) ? $this->_tpl_vars['sex'] : '') == 'none'): ?>selected="selected"<?php endif; ?>> </option>
							<option value="man" <?php if ((isset($this->_tpl_vars['sex']) ? $this->_tpl_vars['sex'] : '') == 'man'): ?>selected="selected"<?php endif; ?>>Мужской</option>
							<option value="woman" <?php if ((isset($this->_tpl_vars['sex']) ? $this->_tpl_vars['sex'] : '') == 'woman'): ?>selected="selected"<?php endif; ?>>Женский</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="left">Дата рождения:</td>
					<td>
						Число <select name="birth_day"><option value="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option><?php echo (isset($this->_tpl_vars['days_list']) ? $this->_tpl_vars['days_list'] : ''); ?>
</select>
						Месяц <select name="birth_month"><option value="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option><?php echo (isset($this->_tpl_vars['months_list']) ? $this->_tpl_vars['months_list'] : ''); ?>
</select>
						Год <select name="birth_year"><option value="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</option><?php echo (isset($this->_tpl_vars['years_list']) ? $this->_tpl_vars['years_list'] : ''); ?>
</select>
					</td>
				</tr>
				<tr>
					<td class="left">Страна:</td>
					<td><select name="country"><option value="" class="grey">Выберите страну...</option><?php echo (isset($this->_tpl_vars['countries_list']) ? $this->_tpl_vars['countries_list'] : ''); ?>
</select></td>
				</tr>
				<tr>
					<td class="left">Город:</td>
					<td><input type="text" name="city" value="<?php echo (isset($this->_tpl_vars['city']) ? $this->_tpl_vars['city'] : ''); ?>
"></td>
				</tr>

				<tr>
					<td align="center" colspan="2">
						<input type="submit" value="Сохранить">
					</td>
				</tr>
				</tbody>
			</table>
			
		</form>
	</div>
	
	<!-- tab 2 -->
	<div id="profile-tabs-2">
		<h3>Проверка принадлежности к ВО</h3>
		<p>
			Выберите виртуальные организации для проверки:
			<div style="">
				<input type="checkbox" name="" value="1" />
				<label> crimea-grid </label>
				<span style="font-size: 11px; color: red;">(вы не состоите в этой организации)</span>
				<br />
				<input type="checkbox" name="" value="1" />
				<label> ukraine-vo</label>
				<span style="font-size: 11px; color: green;">(вы состоите в этой организации)</span>
				<br />
			</div>
			<input type="submit" value="проверить" />
		</p>
		<h3>Выбор ВО по умолчанию</h3>
		Выберите ВО, используемую по умолчанию: 
		<select>
			<option> a</option>
			<option> b</option>
			<option> c</option>
			<option> d</option>
		</select>
		<input type="submit" value="сохранить" />
	</div>
</div>

<script type="text/javascript"><?php echo '
	
	$(function(){
		$( "#profile-tabs" ).tabs({ fx: {opacity: \'toggle\', duration: \'slow\' } });			
	});
	
'; ?>
</script>