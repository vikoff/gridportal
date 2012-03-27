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
			<span class="fds-file-param-spoiler" spoiler="<?= $index; ?>">  </span>
			<span class="fds-file-param-title"><?= Lng::get('fds.' . $item['name']) == 'fds.' . $item['name'] ? $item['name'] : Lng::get('fds.' . $item['name']); ?></span>
		</div>

	<? foreach ($item['args'] as $name => $row){ ?>
		<? $keyIndex++; ?>
		<input type="hidden" name="keys[<?= $index; ?>][<?= $name ?>]" value="<?= $keyIndex; ?>" />
		<div class="fds-file-args fds-file-spoiler-<?= $index; ?>">
			<div class="fds-file-arg">
				<div class="fds-file-arg-title"><?= Lng::get('fds.' . $name) == 'fds.' . $name ? $name : Lng::get('fds.' . $name); ?></div>
				<div class="fds-file-arg-values">
					<div class="fds-file-arg-value">
						<? if ($row['allow_multiple']){ ?>
							<? if (is_array($row['value'])){ ?>
								<? foreach($row['value'] as $r){ ?>
									<? if (is_array($r)){ ?>
										<div class="fds-file-arg-value-range">
											<div>
												<span>от:
												</span><input type="text" name="items[<?= $keyIndex; ?>][value][<?= $index ?>][from]" value="<?= htmlspecialchars($r['from']); ?>" class="fds-file-arg-value-from"
												/><span>до:
												</span><input type="text" name="items[<?= $keyIndex; ?>][value][<?= $index ?>][to]" value="<?= htmlspecialchars($r['to']); ?>" class="fds-file-arg-value-to"
												/><span>шаг:
												</span><input type="text" name="items[<?= $keyIndex; ?>][value][<?= $index ?>][step]" value="<?= htmlspecialchars($r['step']); ?>" class="fds-file-arg-value-step"
												/><!-- "заворот кишок" © // dont push -->
											</div>
										</div>
									<? } else { ?>
										<div class="fds-file-arg-value-single">
											<input type="text" name="items[<?= $keyIndex; ?>][value][<?= $index ?>][single]" value="<?= htmlspecialchars($r); ?>" />
										</div>
									<? } ?>
								<? } ?>
							<? } else { ?>
								<div class="fds-file-arg-value-single">
									<input type="text" name="items[<?= $keyIndex; ?>][value][<?= $index ?>][single]" value="<?= htmlspecialchars($row['value']); ?>" />
								</div>
							<? } ?>
						<? } else { ?>
							<div class="fds-file-arg-value-single">
								<input type="text" name="items[<?= $keyIndex; ?>][value]" value="<?= htmlspecialchars($row['value']); ?>" />
							</div>
						<? } ?>
						<div class="fds-file-arg-value-options">
							<? if ($row['allow_multiple']){ ?><span class="fds-file-param-range">  </span><? } ?><span class="fds-file-param-delete">  </span>
						</div>
						<div class="cl"></div>
					</div>
					<? if ($row['allow_multiple']){ ?><div class="fds-file-arg-add"></div><? } ?>
				</div>
				<div class="cl"></div>
			</div>
		</div>
	<? } ?>
	</div>
<? } ?>
