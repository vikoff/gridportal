<?
if (empty($this->formData)) {
	Lng::get('tast-set-file-constructor-no-data-to-edit');
	return;
}
// echo '<pre align="left">'; print_r($this->formData); die;

// уникальный ключ для каждого параметра
$keyIndex = -1;

?>

<table class="std-grid narrow">
<? foreach($this->formData as $index => $item): ?>

	<? if (!isset($item['args']) || !is_array($item['args'])) continue; ?>
	
	<tr><th colspan="3"><?= $item['name']; ?></th></tr>

	<? foreach ($item['args'] as $name => $row): ?>
		<? $keyIndex++; ?>
		<tr style="vertical-align: top !important;">
		<td><?= $name; ?></td>
		<td>
			<input type="hidden" name="keys[<?= $index; ?>][<?= $name ?>]" value="<?= $keyIndex; ?>" />
			
			<? if ($row['allow_multiple']): ?>
			
				<table class="multiplier" id="multiplier-<?= $keyIndex; ?>"></table>
				
				<script type="text/javascript">
				$(function(){
					<? if (is_array($row['value'])): ?>
					
						<? foreach($row['value'] as $r): ?>
						
							Multiplier.add(
								<?= $keyIndex; ?>,
								<? if (is_array($r)): ?>
									{
										from: "<?= htmlspecialchars($r['from']) ?>",
										to: "<?= htmlspecialchars($r['to']) ?>",
										step: "<?= htmlspecialchars($r['step']) ?>"
									}
								<? else: ?>
									'<?= htmlspecialchars($r); ?>'
								<? endif; ?>
							);
							
						<? endforeach; ?>
						
					<? else: ?>
					
						Multiplier.add(<?= $keyIndex; ?>, '<?= htmlspecialchars($row['value']); ?>');
						
					<? endif; ?>
				});
				</script>
				
			<? else: ?>
				<input type="text" name="items[<?= $keyIndex; ?>][value]" value="<?= htmlspecialchars($row['value']); ?>" />
			<? endif; ?>
		</td>
		<td style="vertical-align: bottom;">
			<? if ($row['allow_multiple']): ?>
				<input onclick="Multiplier.add(<?= $keyIndex; ?>, '');" type="button" value="+" title="<?= Lng::get('tast-set-file-constructor-no-add-new-value'); ?>" />
			<? endif; ?>
		</td>
		</tr>
	<? endforeach; ?>
	
<? endforeach; ?>
</table>
