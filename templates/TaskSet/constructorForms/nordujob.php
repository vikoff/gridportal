<?
if (empty($this->formData)) {
	Lng::get('tast-set-file-constructor-no-data-to-edit');
	return;
}
?>

<? foreach($this->formData as $i => $row): ?>
	<input type="hidden" name="items[<?= $row['row']; ?>][pre_text]" value="<?= htmlspecialchars($row['pre_text']); ?>" />
	<input type="hidden" name="items[<?= $row['row']; ?>][post_text]" value="<?= htmlspecialchars($row['post_text']); ?>" />
	<input type="hidden" name="items[<?= $row['row']; ?>][field]" value="<?= htmlspecialchars($row['field']); ?>" />
	<div class="nordujob-file-param">
		<div class="nordujob-file-param-title"><?= $row['field']; ?></div>
		<div class="nordujob-file-param-values" data-row-no="<?= $i ?>" data-last-index="<?= is_array($row['value']) ? count($row['value']) - 1 : 0 ?>">
			<? if ($row['allow_multiple']){ ?>
				<? if (is_array($row['value'])){ ?>
					<? foreach($row['value'] as $r){ ?>
						<div class="nordujob-file-param-value" data-index="<?= $i ?>">
							<div class="nordujob-file-param-value-range">
								<div>
									<span>от:
									</span><input type="text" name="items[<?= $row['row']; ?>][value][<?= $i ?>][from]" value="<?= htmlspecialchars($r['from']); ?>" class="nordujob-file-param-value-from"
									/><span>до:
									</span><input type="text" name="items[<?= $row['row']; ?>][value][<?= $i ?>][to]" value="<?= htmlspecialchars($r['to']); ?>" class="nordujob-file-param-value-to"
									/><span>шаг:
									</span><input type="text" name="items[<?= $row['row']; ?>][value][<?= $i ?>][step]" value="<?= htmlspecialchars($r['step']); ?>" class="nordujob-file-param-value-step"
									/><!-- "заворот кишок" © // dont push -->
								</div>
							</div>
							<div class="nordujob-file-param-value-options">
								<? if ($row['allow_multiple']){ ?><span class="nordujob-file-param-range" title="<?= Lng::get('range') ?>">  </span><? } ?><span class="nordujob-file-param-delete" title="<?= Lng::get('delete') ?>">  </span>
							</div>
							<div class="cl"></div>
						</div>
					<? } ?>
				<? } else { ?>
					<div class="nordujob-file-param-value" data-index="0">
						<div class="nordujob-file-param-value-single">
							<input type="text" name="items[<?= $row['row']; ?>][value][0][single]" value="<?= htmlspecialchars($row['value']); ?>" />
						</div>
						<div class="nordujob-file-param-value-options">
							<? if ($row['allow_multiple']){ ?><span class="nordujob-file-param-range" title="<?= Lng::get('range') ?>">  </span><? } ?><span class="nordujob-file-param-delete" title="<?= Lng::get('delete') ?>">  </span>
						</div>
						<div class="cl"></div>
					</div>
				<? } ?>
			<? } else { ?>
				<div class="nordujob-file-param-value" data-index="0">
					<div class="nordujob-file-param-value-single">
						<input type="text" name="items[<?= $row['row']; ?>][value]" value="<?= htmlspecialchars($row['value']); ?>" />
					</div>
					<div class="nordujob-file-param-value-options">
						<? if ($row['allow_multiple']){ ?><span class="nordujob-file-param-range" title="<?= Lng::get('range') ?>">  </span><? } ?><span class="nordujob-file-param-delete" title="<?= Lng::get('delete') ?>">  </span>
					</div>
					<div class="cl"></div>
				</div>
			<? } ?>
			<? if ($row['allow_multiple']){ ?><div class="nordujob-file-param-add" title="<?= Lng::get('add') ?>"></div><? } ?>
		</div>
		<div class="cl"></div>
	</div>
<? endforeach; ?>

<script type="text/javascript">
$(function(){
	$('.nordujob-file-param-range').live('click', function(){
		var $value = $($(this).parent().parent().children()[0]);
		var $valuesContainer = $value.parent().parent();
		var value = $('input', $(this).parent().parent()).val();
		var rowNo = $valuesContainer.attr('data-row-no');
		var index = $(this).parent().parent().attr('data-index');
		if ($value.hasClass('nordujob-file-arg-value-single')){
			$value
				.removeClass('nordujob-file-arg-value-single')
				.addClass('nordujob-file-arg-value-range')
				.html('<div>'
					+ '<span>от: </span><input type="text" name="items[' + rowNo + '][value][' + index + '][from]" value="' + value + '" class="nordujob-file-arg-value-from" />'
					+ '<span>до: </span><input type="text" name="items[' + rowNo + '][value][' + index + '][to]" value="' + value + '" class="nordujob-file-arg-value-to" />'
					+ '<span>шаг: </span><input type="text" name="items[' + rowNo + '][value][' + index + '][step]" value="1" class="nordujob-file-arg-value-step" />'
					+ '</div>'
				);
		}
		else {
			$value
				.addClass('nordujob-file-arg-value-single')
				.removeClass('nordujob-file-arg-value-range')
				.html('<input type="text" name="items[' + rowNo + '][value][' + index + '][single]" value="' + value + '" />');
		}
	});
	$('.nordujob-file-param-delete').live('click', function(){
		var $value = $(this).parent().parent();
		$value.slideUp(function(){
			$value.remove();
		});
	});
	$('.nordujob-file-param-add').click(function(){
		var $valuesContainer = $(this).parent();
		var rowNo = $valuesContainer.attr('data-row-no');
		var index = $valuesContainer.attr('data-last-index');
		$valuesContainer.attr('data-last-index', ++index);
		$('<div class="nordujob-file-param-value" data-index="' + index + '"><div class="nordujob-file-param-value-single"><input type="text" name="items[' + rowNo + '][value][' + index + '][single]" value="" /></div><div class="nordujob-file-param-value-options"><span class="nordujob-file-param-range" title="<?= Lng::get('range') ?>">  </span><span class="nordujob-file-param-delete" title="<?= Lng::get('delete') ?>">  </span>	</div><div class="cl"></div></div>')
			.css({ display: 'none' })
			.appendTo($(this).parent())
			.slideDown();
	});
	$('#bottom-right').html('<?= Lng::get('fds-construct-form.file-type-nordujob') ?><!--&nbsp;&nbsp;&nbsp;Размер файла: &nbsp;&nbsp;&nbsp;Вариантов: &nbsp;&nbsp;&nbsp;Общий размер: -->');
});
</script>
