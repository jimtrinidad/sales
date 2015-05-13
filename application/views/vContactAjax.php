	<?php foreach ($contacts as $contact):?>
	<div style="display: block;" infoid=<?php echo $contact['infoid']?> id=" <?php echo $contact['email']?>" class="addTrigger">
		<table cellpadding="0" cellspacing="0" style="margin:3px;">
			<tr>
				<td width="20px"></td>
				<td><?php echo $contact['name']." &lt;".$contact['email']."&gt;"?></td>
			</tr>
		</table>
	</div>
	<?php endforeach;?>