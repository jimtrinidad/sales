<div id="editevent_panel" class="hidden">
	<div class="contentEditor ui-widget-content" id="eventEditFormDiv">
		<form id="editEventForm">
			<div class="editor-header" >
				Edit Event
			</div>
			<div style="padding:10px;" class="editor-content">
				<input type="hidden" id="detailsid" value="<?php echo $result['did']?>">	
				<input type="hidden" id="infoid" value="<?php echo $result['infoid']?>">	
				<div class="half halfleft" style="width: 200px;padding-right: 10px;">
					<label for="lastname">Contact Person<span class="floatright">Surname</span></label>
					<input type="text" style='width:190px' id='lastname' maxlength='50' value="<?php echo $result['lastname']?>">
				</div>
				<div class="half" style="width: 200px;padding-right: 10px;">
					<label for="firstname">&nbsp;<span class="floatright">First Name</span></label>
					<input type="text" style="width:190px" id="firstname" maxlength="50" value="<?php echo $result['firstname']?>">
				</div>
				<div class="half" style="width: 55px">
					<label for="mi">&nbsp;<span class="floatright">MI</span></label>
					<input type="text" style="width:47px" maxlength="2" id="mi" value="<?php echo $result['mi']?>">
				</div>
				<div class="">
					<label for="companyname">Company Name </label>
					<input style="width:467px" id="companyname" type="text" value="<?php echo $result['companyName']?>">
				</div>
				<div>
					<label for="position">Position </label>
					<input style="width:467px" id="position" type="text" maxlength="50" value="<?php echo $result['position']?>">
				</div>
				<div class="half halfleft" style="width: 152px;padding-right: 10px;">
					<label for="telNo">Contacts<span class="floatright">Telephone</span></label>
					<input type="text" style="width:142px;" id="telNo" maxlength="50" value="<?php echo $result['telephone']?>">
				</div>
				<div class="half" style="width: 152px;padding-right: 10px;">
					<label for="faxNo">&nbsp;<span class="floatright">Fax</span></label>
					<input type="text" style="width:142px" id="faxNo" maxlength="50" value="<?php echo $result['fax']?>">
				</div>
				<div class="half" style="width: 152px;">
					<label for="mobileNo">&nbsp;<span class="floatright">Mobile</span></label>
					<input type="text" style="width:143px" id="mobileNo" maxlength="50" value="<?php echo $result['mobile']?>">
				</div>
				<div>
					<label for="email">Email</label>
					<input style="width: 467px;" id="email" type="text" value="<?php echo $result['email']?>">
				</div>
				<div class="half halfleft" style="width: 132px;">
					<label for="eventType">Communication</label>
					<select id="eventType" style="width: 140px;">
						<option value="Incoming Call" <?php echo ($result['eventType']=="Incoming Call")?"selected='selected'":""?>>Incoming Call</option>
						<option value="Incoming Mail" <?php echo ($result['eventType']=="Incoming Mail")?"selected='selected'":""?>>Incoming Mail</option>
						<option value="Outgoing Call" <?php echo ($result['eventType']=="Outgoing Call")?"selected='selected'":""?>>Outgoing Call</option>
						<option value="Outgoing Mail" <?php echo ($result['eventType']=="Outgoing Mail")?"selected='selected'":""?>>Outgoing Mail</option>
					</select>				
				</div>				
				<div class="half" style="width: 125px;">
					<label for="remark">Remark </label>
					<select id="remark" style="width: 115px;">
						<option></option>
						<option value="Opportunity" <?php echo ($result['remark']=="Opportunity")?"selected='selected'":""?>>Opportunity</option>
						<option value="Rejected" <?php echo ($result['remark']=="Rejected")?"selected='selected'":""?>>Rejected</option>
						<!-- <option value="noted">Noted</option> -->
					</select>
				</div>
				<div class="half otype <?php echo ($result['remark']=="Opportunity")?"":"hidden"?>"" style="width: 140px;">
					<label for="opptype">Opportunity Type </label>
					<select id="opptype" style="width: 130px;">
						<option value="Won" <?php echo ($result['opportunityType']=="Won")?"selected='selected'":""?>>Won</option>
						<option value="Loss" <?php echo ($result['opportunityType']=="Loss")?"selected='selected'":""?>>Loss</option>
						<option value="Pending" <?php echo ($result['opportunityType']=="Pending")?"selected='selected'":""?>>Pending</option>
					</select>
				</div>
				<div class="half chance <?php echo ($result['opportunityType']=="Pending")?"":"hidden"?>"" style="width: 55px;">
					<label for="cpercent">Chance</label>
					<input class="gradeField" type="text" maxlength="3" style="width:52px" id="cpercent" value="<?php echo $result['cPercent']!=0?$result['cPercent']:""?>">
				</div>
				<div class="clearer"></div>
				<div class="note">
					<label for="note">Note </label>
					<textarea style="width: 467px;height: 30px;" id="note"><?php echo $result['note']?></textarea>
				</div>
				<div>
					<label for="refferal">Refferal</label>
					<input type="text" style="width:467px" id="refferal">
				</div>
							
				<div id="editorButtonDiv" align="right">
					<button class="button button_img" id="editsave" type="submit"><img src="assets/images/icons/save_edit.png">Save</button>
					<button class="button button_img" id="editcancel" type="button"><img src="assets/images/icons/cancel.png">Cancel</button>
				</div>										
			</div>
		</form>
<script type="text/javascript">
$(document).ready(function(){
	$('.button').button();
	$("#editEventForm #remark").change(function(){
		if($("#editEventForm #remark").val()=="Opportunity"){
			$("#editEventForm .otype").removeClass('hidden');
			$("#editEventForm #opptype").change();	
		}else{
			$("#editEventForm .otype").addClass('hidden');
			$("#editEventForm #opptype").change();
		}
	});
	
	$("#editEventForm #opptype").change(function(){
		if($("#editEventForm #opptype").val()=="Pending" && $("#editEventForm #remark").val()=="Opportunity"){
			$("#editEventForm .chance").removeClass('hidden');	
		}else{
			$("#editEventForm .chance").addClass('hidden');
		}
	});
	

	$("#editEventForm").bind("submit",function(){		
		if(!$("#editEventForm").hasClass('clicked')){
			if($("#editEventForm #remark").val()=="Opportunity"){
				var opptype = $("#editEventForm #opptype").val();
			}else{
				var opptype = "";
			}
			var form_data = {
						eventType : $("#editEventForm #eventType").val(),
						companyName : $("#editEventForm #companyname").val(),
						lastname : $("#editEventForm #lastname").val(),
						firstname : $("#editEventForm #firstname").val(),
						mi : $("#editEventForm #mi").val(),
						position : $("#editEventForm #position").val(),
						telephone : $("#editEventForm #telNo").val(),
						fax : $("#editEventForm #faxNo").val(),
						mobile : $("#editEventForm #mobileNo").val(),
						email : $("#editEventForm #email").val(),
						remark : $("#editEventForm #remark").val(),
						opportunityType : opptype,
						cPercent : $("#editEventForm #cpercent").val(),
						note : $("#editEventForm #note").val(),
						refferal : $("#editEventForm #refferal").val(),
						dID : $("#editEventForm #detailsid").val(),
						infoID : $("#editEventForm #infoid").val(),
						ajax: '1'
					};
			$.ajax({
				url: "<?php echo site_url('main/editevent'); ?>",
				type: 'POST',
				data: form_data,
				success: function(msg) {
						if(msg=='edit'){
							var fdata = {
									id:$("#detailsid").val(),
									ajax:'1'
									};
							$.ajax({
								url : '<?php echo site_url('main/getDetails')?>',
								data : fdata,
								type : 'POST',
								success : function(emsg){
									refreshmaincontent("<?php echo my_session_value('userprogID') ?>","<?php echo my_session_value('dateID') ?>",<?php echo my_session_value('showalldays')?"'".my_session_value('showalldays')."'":"''"?>);
									$("#moredetailspanel").html(emsg);
									$.fancybox.close();
								}
							});						
						}
						else if(msg.search("html")<0){
							myMessageBox(msg,'Error','red',false);
							$("#editEventForm").removeClass('clicked');
						}
						else{
							window.location.reload();
						}
				},
				error:function(){
					$("#editEventForm").removeClass('clicked');
				}
				});
		}//end if has class clicked
		$("#editEventForm").addClass('clicked');
		return false;
	});

	$("#editcancel").click(function(){
		$.fancybox.close();
	});
			
});
</script>
	</div>		
</div>

<div class="ui-widget-content list">
<table width="100%"  style="border: 0;">
	<tr>
		<td style="border-bottom:0;">
			<span>Mode of Communication:</span><b><?php echo $result['eventType']?></b><br>
			<span>Date / Time:</span><b><?php echo date("F j, Y / g:i:s A",strtotime($result['time']))?></b><br>
			<span>Remark:</span><b><?php echo $result['remark']?></b><br>
			<?php if($result['remark']=="Opportunity"):?><span>Status:</span><b><?php echo $result['opportunityType']?></b><?php endif;?>
			<?php if($result['opportunityType']=="Pending" && $result['cPercent']!=""):?><span>Chance:</span><b><?php echo $result['cPercent']?>%</b><br><?php elseif($result['remark']=="Opportunity"):echo "<br>"; endif;?>
			<?php if($result['note']!=""):?><span>Note:</span><b><?php echo $result['note']?></b><br><?php endif;?>			
			<span>Contact Person:</span><b><?php echo $result['lastname'].", ".$result['firstname'];echo $result['mi']!=""?" ".$result['mi'].".":"" ?></b><br>
			<span>Company Name:</span><b><?php echo $result['companyName']?></b><br>
			<span>Position:</span><b><?php echo $result['position']?></b><br>
			<span>Telephone:</span><b><?php echo $result['telephone']?></b><br>
			<span>Fax:</span><b><?php echo $result['fax']?></b><br>
			<span>Mobile:</span><b><?php echo $result['mobile']?></b><br>
			<span>Email:</span><b><?php echo $result['email']?></b><br>
			<span>Refferal:</span><b><?php echo $result['refferal']?></b><br>
		</td>
	</tr>
	<tr>
		<td class="" style="border-bottom:0;">
			<?php if(userPrivilege('canSendM')==1):?>
				<?php if(!empty($result['email'])):?>
				<button class="button button_img sendemail" id="<?php echo $result['did']?>"><img src="assets/images/icons/send_email_user.png">Send Email</button>
				<?php endif;?>
			<?php endif;?>
			<button class="button button_img" id="history"><img src="assets/images/icons/history.png">History</button>
			<button class="button button_img" id="editevent" href="#eventEditFormDiv"><img src="assets/images/icons/edit.png">Edit</button>
			<button class="button button_img" id="edelete"><img src="assets/images/icons/delete.png">Delete</button>
			<button class="button button_img" id="ehide"><img src="assets/images/icons/cancel.png">Hide</button>
		</td>
	</tr>
</table>
</div>


<script type="text/javascript">
$(document).ready(function(){

	$("#editevent").fancybox({
		'overlayOpacity'	: .4,
		'padding'			: 0,
		'centerOnScroll'	: true,
		'autoScale'			: true,
		'autoDimensions'	: true,
		'hideOnOverlayClick': false,
		'onComplete'		:	function(){
			if($('#eventEditFormDiv .editor-header:visible').length != 0){
				$("#fancybox-outer").css('backgroundColor','transparent').draggable({handle : '#eventEditFormDiv .editor-header'});
			}
			$.fancybox.center();
			hideMyLoader();
		}
	});	
	
	$('.button').button();

	/*
	$('#editcancel').bind('click',function(e){
		$('#editevent_panel').toggle('fast');
		editevent=false;
	});

	$("#editevent_panel").hide();
	//$("#editevent_panel").draggable({handle: '.header'});
	var editevent = false;
	$('#editevent').click(function(e){	
		if(!editevent)
		{
			editevent=true;
			$("#editevent_panel").toggle('fast');
		}
		else
		{
			editevent=false;
			$("#editevent_panel").toggle(500);
		}		
	});	

	*/

	$("#ehide").click(function(){
		$("#moredetailspanel").animate({"right": "-520px"}, "fast");
		$("#history_panel").animate({"left": "-430px"}, "fast");

		//kapag naka dialog box lang to
		//$('#dialog-<?php echo $result['did']?>-details').dialog('close');
	});

	$("#edelete").click(function(){
		myConfirmBox('Confirm','Are you sure you want to remove this record?',
				function(){
					var fdata = {
							did : $("#detailsid").val(),
							infoid : $("#infoid").val(),
							ajax : '1'
							};
					$.ajax({
						url : '<?php echo site_url('main/deleteEvent')?>',
						data : fdata,
						type : 'POST',
						success : function(){
							refreshmaincontent("<?php echo my_session_value('userprogID') ?>","<?php echo my_session_value('dateID') ?>");
						}
					});
				},
			"Yes","No");
	});


	$(".sendemail").bind("click",function(){
		var fdata = {
				detailsid : $(this).attr('id'),
				ajax : 1
				};
			myDialogBox('<?php echo site_url('main/emaileditor')?>',fdata,'emailer','Email Editor',{width : 'auto'});
	    return false;
	});		

	var side_panel = false;
	$("#history").bind("click",function(){
		if(!side_panel){
			$("#history_panel").animate({"left": "0px"}, "fast");
			side_panel = true;
		}else{
			$("#history_panel").animate({"left": "-430px"}, "fast");
			side_panel = false;
		}
	});	

});
</script>