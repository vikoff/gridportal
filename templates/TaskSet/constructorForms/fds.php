<?
if (empty($this->formData)) {
	Lng::get('tast-set-file-constructor-no-data-to-edit');
	return;
}
// echo '<pre align="left">'; print_r($this->formData); die;

// уникальный ключ для каждого параметра
$keyIndex = -1;

?>

<? foreach($this->formData as $index => $item){ ?>

	<? if (!isset($item['args']) || !is_array($item['args'])) continue; ?>
	<div class="fds-file-param">
		<div class="fds-file-param-head">
			<div class="fds-file-param-spoiler" spoiler="<?= $index; ?>"></div>
			<div class="fds-file-param-title"><?= Lng::get('fds.' . $item['name']) == 'fds.' . $item['name'] ? $item['name'] : Lng::get('fds.' . $item['name']); ?></div>
			<div class="cl"></div>
		</div>

	<? foreach ($item['args'] as $name => $row){ ?>
		<? $keyIndex++; ?>
		<input type="hidden" name="keys[<?= $index; ?>][<?= $name ?>]" value="<?= $keyIndex; ?>" />
		<div class="fds-file-args fds-file-spoiler-<?= $index; ?>">
			<div class="fds-file-arg">
				<div class="fds-file-arg-title"><?= Lng::get('fds.' . $name) == 'fds.' . $name ? $name : Lng::get('fds.' . $name); ?></div>
				<div class="fds-file-arg-values" data-key-index="<?= $keyIndex; ?>" data-last-index="<?= is_array($row['value']) ? count($row['value']) - 1 : 0 ?>">
					<? if ($row['allow_multiple']){ ?>
						<? if (is_array($row['value'])){ ?>
							<? $i = 0; ?>
							<? foreach($row['value'] as $r){ ?>
								<div class="fds-file-arg-value" data-index="<?= $i ?>">
									<? if (is_array($r)){ ?>
										<div class="fds-file-arg-value-range">
											<div>
												<span>от:
												</span><input type="text" name="items[<?= $keyIndex; ?>][value][<?= $i ?>][from]" value="<?= htmlspecialchars($r['from']); ?>" class="fds-file-arg-value-from"
												/><span>до:
												</span><input type="text" name="items[<?= $keyIndex; ?>][value][<?= $i ?>][to]" value="<?= htmlspecialchars($r['to']); ?>" class="fds-file-arg-value-to"
												/><span>шаг:
												</span><input type="text" name="items[<?= $keyIndex; ?>][value][<?= $i ?>][step]" value="<?= htmlspecialchars($r['step']); ?>" class="fds-file-arg-value-step"
												/><!-- "заворот кишок" © // dont push -->
											</div>
										</div>
									<? } else { ?>
										<div class="fds-file-arg-value-single">
											<input type="text" name="items[<?= $keyIndex; ?>][value][<?= $i ?>][single]" value="<?= htmlspecialchars($r); ?>" />
										</div>
									<? } ?>
									<div class="fds-file-arg-value-options">
										<? if ($row['allow_multiple']){ ?><span class="fds-file-param-range" title="<?= Lng::get('range') ?>">  </span><? } ?><span class="fds-file-param-delete" title="<?= Lng::get('delete') ?>">  </span>
									</div>
									<div class="cl"></div>
								</div>
								<? $i++ ?>
							<? } ?>
						<? } else { ?>
							<div class="fds-file-arg-value" data-index="0">
								<div class="fds-file-arg-value-single">
									<input type="text" name="items[<?= $keyIndex; ?>][value][0][single]" value="<?= htmlspecialchars($row['value']); ?>" />
								</div>
								<div class="fds-file-arg-value-options">
									<? if ($row['allow_multiple']){ ?><span class="fds-file-param-range" title="<?= Lng::get('range') ?>">  </span><? } ?><span class="fds-file-param-delete" title="<?= Lng::get('delete') ?>">  </span>
								</div>
								<div class="cl"></div>
							</div>
							<? } ?>
						<? } else { ?>
							<div class="fds-file-arg-value" data-index="0">
								<div class="fds-file-arg-value-single">
									<input type="text" name="items[<?= $keyIndex; ?>][value]" value="<?= htmlspecialchars($row['value']); ?>" />
								</div>
								<div class="fds-file-arg-value-options">
									<? if ($row['allow_multiple']){ ?><span class="fds-file-param-range" title="<?= Lng::get('range') ?>">  </span><? } ?><span class="fds-file-param-delete" title="<?= Lng::get('delete') ?>">  </span>
								</div>
								<div class="cl"></div>
							</div>
						<? } ?>
					<? if ($row['allow_multiple']){ ?><div class="fds-file-arg-add" title="<?= Lng::get('add') ?>"></div><? } ?>
				</div>
				<div class="cl"></div>
			</div>
		</div>
	<? } ?>
	</div>
<? } ?>
<script type="text/javascript">
$(function(){
	$('.fds-file-param-spoiler').click(function(){
		$(this).toggleClass('fds-file-param-spoiler-hidden');
		$('.fds-file-spoiler-' + $(this).attr('spoiler')).slideToggle();
	});
	$('.fds-file-param-range').live('click', function(){
		var $value = $($(this).parent().parent().children()[0]);
		var $valuesContainer = $value.parent().parent();
		var value = $('input', $(this).parent().parent()).val();
		var keyIndex = $valuesContainer.attr('data-key-index');
		var index = $(this).parent().parent().attr('data-index');
		if ($value.hasClass('fds-file-arg-value-single')){
			$value
				.removeClass('fds-file-arg-value-single')
				.addClass('fds-file-arg-value-range')
				.html('<div>'
					+ '<span>от: </span><input type="text" name="items[' + keyIndex + '][value][' + index + '][from]" value="' + value + '" class="fds-file-arg-value-from" />'
					+ '<span>до: </span><input type="text" name="items[' + keyIndex + '][value][' + index + '][to]" value="' + value + '" class="fds-file-arg-value-to" />'
					+ '<span>шаг: </span><input type="text" name="items[' + keyIndex + '][value][' + index + '][step]" value="1" class="fds-file-arg-value-step" />'
					+ '</div>'
				);
		}
		else {
			$value
				.addClass('fds-file-arg-value-single')
				.removeClass('fds-file-arg-value-range')
				.html('<input type="text" name="items[' + keyIndex + '][value][' + index + '][single]" value="' + value + '" />');
		}
	});
	$('.fds-file-param-delete').live('click', function(){
		var $value = $(this).parent().parent();
		$value.slideUp(function(){
			$value.remove();
		});
	});
	$('.fds-file-arg-add').click(function(){
		var $valuesContainer = $(this).parent();
		var keyIndex = $valuesContainer.attr('data-key-index');
		var index = $valuesContainer.attr('data-last-index');
		$valuesContainer.attr('data-last-index', ++index);
		$('<div class="fds-file-arg-value" data-index="' + index + '"><div class="fds-file-arg-value-single"><input type="text" name="items[' + keyIndex + '][value][' + index + '][single]" value="" /></div><div class="fds-file-arg-value-options"><span class="fds-file-param-range" title="<?= Lng::get('range') ?>">  </span><span class="fds-file-param-delete" title="<?= Lng::get('delete') ?>">  </span>	</div><div class="cl"></div></div>')
			.css({ display: 'none' })
			.appendTo($(this).parent())
			.slideDown();
	});
	$('#bottom-right').html(''
		+ '<?= Lng::get('fds-construct-form.file-type-fds') ?>'
		+ '&nbsp;&nbsp;&nbsp;Размер файла: <?= $this->file_size; ?>'
		+ '&nbsp;|&nbsp;&nbsp;Всего вариантов: <?= $this->num_submits; ?>'
		+ '&nbsp;|&nbsp;&nbsp;Вариантов в файле: <?= $this->num_variants_in_file; ?>'
		// + '&nbsp;&nbsp;&nbsp;Общий размер:'
	);
});
</script>