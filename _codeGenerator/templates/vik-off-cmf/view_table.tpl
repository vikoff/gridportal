
<div>{a href=<?=$MODEL_NAME_LOW;?>/list text="Вернуться к списку"}</div>

<h2>
	Запись #{$instanceId}
</h2>

<table class="detailGrid">
<?
foreach($FIELDS_TITLES as $field => $title)
	echo
'<tr>
	<td class="title">'.$title.'</td>
	<td class="data">{$'.$field.'}</td>
</tr>
';
?>
</table>
