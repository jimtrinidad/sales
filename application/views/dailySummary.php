<div class="ui-widget-content list" id="tbdates">
<table>
	<tr>	
		<td colspan="2" style="border-bottom: 0;">
			<span>Total Number Actvities:</span><b><?php echo $total?></b>
			<span style="margin-left:20px;">New:</span><b><?php echo $new?></b>
			<span>Old:</span><b><?php echo $old?></b>
			<span>Follow Up:</span><b><?php echo $followup?></b>
		</td>
	</tr>
	<tr>
		<td style="border-bottom: 0;width:150px;">
			<span>Total Incoming Calls:</span><b><?php echo $IC?></b><br>
			<span>Total Incoming Mails:</span><b><?php echo $IM?></b>
		</td>
		<td style="border-bottom: 0;">
			<span>Total Outgoing Calls:</span><b><?php echo $OC?></b><br>
			<span>Total Outgoing Mails:</span><b><?php echo $OM?></b>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="border-bottom: 0;">
		<span>Rejected:</span><b><?php echo $rejected?></b>
		</td>
	</tr>	
	<tr>
		<td colspan="2" style="border-bottom: 0;">
		<span>Opportunity:</span><b><?php echo $opportunity?></b><br>
		<span style="margin-left:20px">Total Won:</span><b><?php echo $won?></b><br>
		<span style="margin-left:20px">Total Loss:</span><b><?php echo $loss?></b><br>
		<span style="margin-left:20px">Total Pending:</span><b><?php echo $note?></b><br>
		</td>
	</tr>
</table>
</div>