<div id="top-menu">
	
	<? foreach($this->_topMenuItems as $name => $data): ?>
		
		<a href="<?=App::href($data['href']);?>" <? if($this->_topMenuActiveItem == $name): ?> class="active" <? endif; ?> >
			<?=Lng::get($data['title']);?>
		</a>
		
	<? endforeach; ?>
	
</div>
