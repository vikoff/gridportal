<?=Lng::get('logged-block.greeting');?>, <b><?=$user_io;?></b>!<br /><br /><a href="<?=App::href('profile/edit');?>" class="button"><?=Lng::get('edit.profile');?></a><? if(USER_AUTH_PERMS == PERMS_ALIEN): ?>	<?=Lng::get('logged-block.not-in-vo'); ?> <a href="<?=App::href('page/join-vo');?>"><?=Lng::get('detail'); ?></a><? endif; ?>