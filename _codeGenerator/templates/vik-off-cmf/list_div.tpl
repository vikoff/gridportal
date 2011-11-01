{$pagination}

{if $collection}
	{foreach from=$collection item='item'}
	<p>
<? foreach($FIELDS_TITLES as $field => $title)
	echo "\t\t".'<h3>'.$title.'</h3>'."\r\n"
		."\t\t".'{$item.'.$field.'}'."\r\n"; ?>
		<div>{a href=<?=$MODEL_NAME_LOW;?>/view/`$item.id` text="Подробней"}</div>
	</p>
	{/foreach}
{else}
	<p>Сохраненных записей пока нет.</p>
{/if}

{$pagination}

