
<table>
<? foreach ($this->rows as $row): ?>
	<tr>
		<td><?= $row[0]; ?></td>
		<td><?= $row[1]; ?></td>
	</tr>
<? endforeach; ?>
</table>