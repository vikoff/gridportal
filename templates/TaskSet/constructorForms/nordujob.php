<?
if (empty($this->formData)) {
	Lng::get('tast-set-file-constructor-no-data-to-edit');
	return;
}
?>

<table class="std-grid narrow">
<? foreach($this->formData as $row): ?>
<tr style="vertical-align: top !important;">
	<td><?= $row['field']; ?></td>
	<td>
		<input type="hidden" name="items[<?= $row['row']; ?>][pre_text]" value="<?= htmlspecialchars($row['pre_text']); ?>" />
		<input type="hidden" name="items[<?= $row['row']; ?>][post_text]" value="<?= htmlspecialchars($row['post_text']); ?>" />
		<input type="hidden" name="items[<?= $row['row']; ?>][field]" value="<?= htmlspecialchars($row['field']); ?>" />
		
		<? if ($row['allow_multiple']): ?>
		
			<table class="multiplier" id="multiplier-<?= $row['row']; ?>"></table>
			
			<script type="text/javascript">
			$(function(){
				<? if (is_array($row['value'])): ?>
				
					<? foreach($row['value'] as $r): ?>
					
						Multiplier.add(
							<?= $row['row']; ?>,
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
				
					Multiplier.add(<?= $row['row']; ?>, '<?= htmlspecialchars($row['value']); ?>');
					
				<? endif; ?>
			});
			</script>
			
		<? else: ?>
			<input type="text" name="items[<?= $row['row']; ?>][value]" value="<?= htmlspecialchars($row['value']); ?>" />
		<? endif; ?>
	</td>
	<td style="vertical-align: bottom;">
		<? if ($row['allow_multiple']): ?>
			<input onclick="Multiplier.add(<?= $row['row']; ?>, '');" type="button" value="+" title="<?= Lng::get('tast-set-file-constructor-no-add-new-value'); ?>" />
		<? endif; ?>
	</td>
</tr>
<? endforeach; ?>
</table>
