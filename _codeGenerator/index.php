<?
session_start();

define('FS_ROOT', dirname(__FILE__).'/');

require_once('setup.php');
require('data/actions.php');

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
     "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<title>Кодогенератор</title>
	
	<link rel="stylesheet" href="data/style.css" type="text/css" />
</head>
<body>

<?

echo Messenger::get()->getAll();

?>

<script type="text/javascript">
	
	function ge(id){return document.getElementById(id);}
	
	function capitalize(string){return string.charAt(0).toUpperCase() + string.slice(1);}
	
	function tblNameEdit(){
		var model = capitalize(ge('tablename').value.toLowerCase());
		ge('modelclass').value = model;
		ge('controlclass').value = model + 'Controller';
	}
	
	document.body.onload = function(){
	
		ge('tablename').onkeyup = tblNameEdit;
	
		ge('modelclass').onkeyup = function(){
			ge('controlclass').value = this.value + 'Controller';
		}
		
	}
	
</script>

<p>
	<form align="right" action="" method="post" onsubmit="return confirm('Удалить все сохраненные данные?')">
		<input type="hidden" name="action" value="clearSession" />
		<input type="submit" name="clear-session" value="Очистить сессию" />
	</form>
</p>

<form method="post">
<input type="hidden" name="action" value="saveData" />

	<table>
	<tr>
		<td>Структура БД</td>
		<td>
			<?
			if(count(getVar($s['tableStruct'], array(), 'array'))){
				$fields = array();
				foreach($s['tableStruct'] as $f)
					$fields[] = $f['Field'].' [ '.$f['Type'].' ]';
				echo implode(',<br />', $fields);
			}else{
				echo "<a href=\"#\" onclick=\"window.open('tableStructure.php','validationRules','width=600,height=600,left=200,top=20'); return false;\">Получить</a>";
			}
			?>
		</td>
	</tr>
	<tr>
		<td>Таблица БД</td>
		<td>
			<input id="tablename" type="text" name="tablename" value="<?=getVar($s['tablename']);?>">
			<a href="#" onclick="tblNameEdit(); return false;">Раздать имена</a>
		</td>
	</tr><tr>
		<td>Класс модели</td>
		<td><input id="modelclass" type="text" name="modelclass" value="<?=getVar($s['modelclass']);?>"></td>
	</tr><tr>
		<td>Класс контроллера</td>
		<td><input id="controlclass" type="text" name="controlclass" value="<?=getVar($s['controlclass']);?>"></td>
	</tr><tr>
		<td>Общие правила<br />валидации<br /></td>
		<td>
			<textarea name="validatCommonRules" style="width: 900px; height: 75px;"><?
				if(getVar($s['strValidatCommonRules'])){
					echo $s['strValidatCommonRules'];
				}elseif(getVar($s['validatCommonRules'])){
					echo DbStructParser::getArrStr($s['validatCommonRules'], "            ");
				}
			?></textarea>
		</td>
	</tr><tr>
		<td>Индивидуальные<br />правила валидации<br /></td>
		<td>
			<textarea name="validatIndividRules" style="width: 900px; height: 150px;"><?
				if(getVar($s['strValidatIndividRules'])){
					echo $s['strValidatIndividRules'];
				}elseif(getVar($s['validatIndividRules'])){
					echo DbStructParser::getArrStr($s['validatIndividRules'], "            ");
				}
			?></textarea>
		</td>
	</tr><tr>
		<td>Заголовки полей<br /></td>
		<td>
			<textarea name="fieldsTitles" style="width: 900px; height: 150px;"><?
			
				if(getVar($s['strFieldsTitles'])){
					echo $s['strFieldsTitles'];
				}elseif(is_array($s['tableStruct']) && count($s['tableStruct'])){
					echo "array(\n";
					foreach($s['tableStruct'] as $field)
						echo "                '".$field['Field']."' => '".$field['Field']."',\n";
					echo "            )";
				}
				
			?></textarea>
		</td>
	</tr><tr>
		<td>Сортируемые поля</td>
		<td>
			<textarea name="sortableFields" style="width: 900px; height: 150px;"><?
			
				if(getVar($s['strSortableFields'])){
					echo $s['strSortableFields'];
				}elseif(is_array($s['tableStruct']) && count($s['tableStruct'])){
					foreach($s['tableStruct'] as $field)
						echo $field['Field']."\n";
				}
				
			?></textarea>
		</td>
	</tr><tr>
		<td>Шаблон</td>
		<td>
			<select name="template">
				<option value="">Выберите...</option>
				<?
				foreach(file('./templates/templates.txt') as $t){
					$t = trim($t);
					echo '<option value="'.$t.'" '.($s['template'] == $t ? 'selected="selected"' : '').'>'.$t.'</option>';
				}
				?>
			</select>
		</td>
	</tr><tr>
		<td></td>
		<td><input type="submit" name="step1save" value="Сохранить"></td>
	</tr>
	</table>
</form>

<br />
<br />

<form action="" method="post">
	
	<input type="hidden" name="action" value="generate" />

	<table border="1" style="font-size: 12px; margin: auto;">
	<tr>
		<td colspan="3" align="center">
			<div class="<?=strlen($s['template']) ? 'green' : 'red'; ?>">Шаблон</div>
		</td>
	</tr>
	<tr valign="top">
		<td>
			<b style="font-size: 16px;">Model</b>
			<div class="<?=strlen($s['modelclass']) ? 'green' : 'red'; ?>">Имя модели</div>
			<div class="<?=strlen($s['tablename']) ? 'green' : 'red'; ?>">Имя таблицы БД</div>
			<div class="<?=strlen($s['strValidatCommonRules']) && strlen($s['strValidatIndividRules']) ? 'green' : ''; ?>">Правила валидации</div>
		</td><td>
			<b style="font-size: 16px;">Controller</b><br />
			<div class="<?=strlen($s['controlclass']) > 10 ? 'green' : 'red'; ?>">Имя контроллера</div>
			<div class="<?=strlen($s['modelclass']) ? 'green' : 'red'; ?>">Имя модели</div>
		</td><td>
			<b style="font-size: 16px;">Templates</b><br />
			<div class="<?=strlen($s['strFieldsTitles']) ? 'green' : 'red'; ?>">Заголовки полей</div>
		</td>
	</tr>
	<tr valign="top">
		<td>      <!-- МОДЕЛЬ -->
			<? if(strlen($s['modelclass']) && 
				  strlen($s['template']) &&
				  strlen($s['tablename'])): ?>
				<p><input id="model-generate" type="checkbox" name="files[model]" value="1" <?=(getVar($s['files']['model']) ? 'checked="checked"' : '');?> /> <label for="model-generate">Сгенерировать</label></p>
			<? endif; ?>
		</td><td> <!-- КОНТРОЛЛЕР -->
			<? if(strlen($s['controlclass']) > 10 && 
				  strlen($s['template']) &&
				  strlen($s['modelclass'])): ?>
				<p><input id="control-generate" type="checkbox" name="files[controller]" value="1" <?=(getVar($s['files']['controller']) ? 'checked="checked"' : '');?> /> <label for="control-generate">Сгенерировать</label></p>
			<? endif; ?>
		</td><td> <!-- ШАБЛОНЫ -->
		
			<? if(strlen($s['strFieldsTitles']) &&
				  strlen($s['template'])): ?>
			<table style="font-size: 12px;">
				<tr><td>admin-list:</td><td><select name="files[tpl-admin-list]"><?=getHtmlTempateTypesList($s['files']['tpl-admin-list'], 'te');?></select></td></tr>
				<tr><td>list:</td><td><select name="files[tpl-list]"><?=getHtmlTempateTypesList(getVar($s['files']['tpl-list']));?></select></td></tr>
				<tr><td>view:</td><td><select name="files[tpl-view]"><?=getHtmlTempateTypesList(getVar($s['files']['tpl-view']));?></select></td></tr>
				<tr><td>edit:</td><td><select name="files[tpl-edit]"><?=getHtmlTempateTypesList(getVar($s['files']['tpl-edit']));?></select></td></tr>
				<tr><td>delete:</td><td><select name="files[tpl-delete]"><?=getHtmlTempateTypesList(getVar($s['files']['tpl-delete']), 'de');?></select></td></tr>
			</table>
			<? endif; ?>
			
		</td>
	</tr>
	
	<? if(getVar($s['template'])): ?>
	<tr>
		<td colspan="3" align="center">
			<input type="checkbox" id="clear-output-dir" name="clear-output-dir" value="1" <?=(getVar($s['clear-output-dir']) ? 'checked="checked"' : '');?> /> <label for="clear-output-dir">Очистить предыдущие</label><br />
			<input type="submit" name="" value="Сгенерировать" />
		</td>
	</tr>
	<? endif; ?>
	
	</table>
</form>

<br />
<br />

</body>
</html>

<?
// echo'<pre>'; print_r($s);
?>