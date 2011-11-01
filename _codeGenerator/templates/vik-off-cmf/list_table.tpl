
{$pagination}

{if $collection}
	<table class="dataGrid">
	<tr>
<? foreach($FIELDS_TITLES as $field => $title)
		echo "\t\t".'<th>'.$title.'</th>'."\r\n"; ?>
		<th>опции</th>
	</tr>
	{foreach from=$collection item='item'}
	<tr>
<? foreach($FIELDS_TITLES as $field => $title)
		echo "\t\t".'<td>{$item.'.$field.'}</td>'."\r\n"; ?>
		<td style="font-size: 11px;">
			{a href=<?=$MODEL_NAME_LOW;?>/view/`$item.id` text="Подробней"}
		</td>
	</tr>
	{/foreach}
	</table>
{else}
	<p>Сохраненных записей пока нет.</p>
{/if}

{$pagination}
