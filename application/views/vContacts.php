<span><label for="searchcontact">Search Contacts</label></span>
<div id="contactSearch">
	<select id="conSearchKey">
		<option value="name">Name</option>
		<option value="email">Email</option>
	</select>
	<input id="conSearchVal" style="padding: 1px;width:312px;display: inline;">
	<?php if(isset($programs)):?>
	<span style="font-weight: bold;padding: 2px;">Choose Program:</span>
	<select id="conProgram" style="width: 150px">
		<option value="">All Programs</option>
		<?php foreach ($programs as $program):?>
		<option value="<?php echo $program['pid']?>"><?php echo $program['program']?></option>
		<?php endforeach;?>
	</select>
	<select id="conStatus" style="width: 80px;">
		<option value="all">Status</option>
		<option value="Won">Won</option>
		<option value="Pending">Pending</option>
		<option value="Loss">Loss</option>
		<option value="Rejected">Rejected</option>
	</select>		
	<?php endif;?>
	<button class="button" id="contactGo">Go</button>
</div>
<span>My Contacts</span>
<div id="contactlist">
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
</div>

<span>To:</span>
<div id="contactEmailTo">

</div>

<div id="contactButton" align="right">
	<button class="button" id="cancel">Cancel</button>
	<button class="button" id="done">Done</button>
</div>
<script type="text/javascript">
$(document).ready(function(){

	var recieverid = new Array();
	var infoid = new Array();

	$("#contactlist .addTrigger").live("click",function(){
		if(recieverid.length < 10){
			$("#contactEmailTo").append($(this).clone());
			recieverid.push($(this).attr('id'));
			infoid.push($(this).attr('infoid'));
			$(this).remove();
		}else{
			//alert('Only 10 emails is allowed per send.');
			myMessageBox('Only 10 emails is allowed per send.','Error','red',false);
		}
	});
	$("#contactEmailTo .addTrigger").live("click",function(){
		$("#contactlist").append($(this).clone());
        var index= jQuery.inArray($(this).attr('id'), recieverid);
        recieverid.splice(index,1);
        var index= jQuery.inArray($(this).attr('infoid'), infoid);
        infoid.splice(index,1);	
		$(this).remove();
	});

	$( "#contactButton #cancel" ).click(function(){
		$('#dialog-contacts').dialog('close');
	});	

	$( "#contactButton #done" ).click(function(){
		$("#emailbox").val(recieverid);
		$('#dialog-contacts').dialog('close');
	});		

	$("#contactGo").click(function(){
		var fdata = {
				infoid:infoid,
				searchkey:$("#conSearchKey").val(),
				searchval:$("#conSearchVal").val(),
				conProgram:$("#conProgram").val(),
				constatus:$("#conStatus").val(),
				day : $("#day").val(),//if from dashboard
				program : $("#program").val(),//if from dashboard
				status : $("#status").val(),//if from dashboard
				lw : $("#lw").val(),//if from dashboard
				ajax:1
			};
		showMyLoader();
		$.ajax({
			url:'<?php echo (isset($fromDashboard)&&$fromDashboard==1)?site_url('dashboard/contactfilter'):site_url('main/contactfilter');?>',
			type:'POST',
			data:fdata,
			success:function(msg){
				$("#contactlist").html(msg);
				hideMyLoader();
			},
			error:function(){
				//alert('haha');
			}
		});
	});	

	$('.button').button();
});
</script>