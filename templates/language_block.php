<? foreach($langs as $lang): ?>
	<a href="<?=App::getHrefLngReplaced($lang);?>"<?=$lang == $curLng ? ' class="active"' : '';?>><img src="/images/spacer.gif" class="flag flag-<?=$lang;?>" alt="<?=$lang;?>" title="<?=$lang;?>" /></a>
<? endforeach; ?>