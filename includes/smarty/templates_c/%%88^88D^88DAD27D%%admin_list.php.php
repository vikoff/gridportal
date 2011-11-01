<?php /* Smarty version 2.6.26, created on 2011-09-23 21:03:45
         compiled from MyproxyServer/admin_list.php */ ?>

<div class="options-row">
	<a href="<?php echo '<?='; ?>
 href('admin/content/myproxy-server/new'); <?php echo '?>'; ?>
">Добавить запись</a>
</div>

<?php echo '<?='; ?>
 $this->pagination; <?php echo '?>'; ?>


<?php echo '<?'; ?>
 if($this->collection): <?php echo '?>'; ?>

	<table class="std-grid tr-highlight">
	<tr>
		<th><?php echo '<?='; ?>
 $this->sorters['id']; <?php echo '?>'; ?>
</th>
		<th><?php echo '<?='; ?>
 $this->sorters['name']; <?php echo '?>'; ?>
</th>
		<th><?php echo '<?='; ?>
 $this->sorters['url']; <?php echo '?>'; ?>
</th>
		<th>Опции</th>
	</tr>
	<?php echo '<?'; ?>
 foreach($this->collection as $item): <?php echo '?>'; ?>
	
	<tr>
		<td><?php echo '<?='; ?>
 $item['id']; <?php echo '?>'; ?>
</td>
		<td><?php echo '<?='; ?>
 $item['name']; <?php echo '?>'; ?>
</td>
		<td><?php echo '<?='; ?>
 $item['url']; <?php echo '?>'; ?>
</td>
			
		<td class="center">
			<div class="tr-hover-visible options">
				<a href="<?php echo '<?='; ?>
 href('myproxy-server/view/'.$item['id']); <?php echo '?>'; ?>
" title="Просмотреть"><img src="images/backend/icon-view.png" alt="Просмотреть" /></a>
				<a href="<?php echo '<?='; ?>
 href('admin/content/myproxy-server/edit/'.$item['id']); <?php echo '?>'; ?>
" title="Редактировать"><img src="images/backend/icon-edit.png" alt="Редактировать" /></a>
				<a href="<?php echo '<?='; ?>
 href('admin/content/myproxy-server/delete/'.$item['id']); <?php echo '?>'; ?>
" title="Удалить"><img src="images/backend/icon-delete.png" alt="Удалить" /></a>
			</div>
		</td>
	</tr>
	<?php echo '<?'; ?>
 endforeach; <?php echo '?>'; ?>
	
	</table>
<?php echo '<?'; ?>
 else: <?php echo '?>'; ?>

	<p>Сохраненных записей пока нет.</p>
<?php echo '<?'; ?>
 endif; <?php echo '?>'; ?>


<?php echo '<?='; ?>
 $this->pagination; <?php echo '?>'; ?>