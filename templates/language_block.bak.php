
<div id="language-block">
	<? foreach($langs as $lang): ?>
		<a href="<?=App::getHrefLngReplaced($lang);?>"<?=$lang == $curLng ? ' class="active"' : '';?>><img src="images/lang/<?=$lang;?>.gif" alt="<?=$lang;?>" title="<?=$lang;?>" /></a>
	<? endforeach; ?>
</div>