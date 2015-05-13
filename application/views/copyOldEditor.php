<script type="text/javascript">
$(document).ready(function(){
	
	
	$("#copyEventForm #remark").change(function(){
		if($("#copyEventForm #remark").val()=="Opportunity"){
			$("#copyEventForm .otype").removeClass('hidden');
			$("#copyEventForm #opptype").change();	
		}else{
			$("#copyEventForm .otype").addClass('hidden');
			$("#copyEventForm #opptype").change();
		}
	});
	
	$("#copyEventForm #opptype").change(function(){
		if($("#copyEventForm #opptype").val()=="Pending" && $("#copyEventForm #remark").val()=="Opportunity"){
			$("#copyEventForm .chance").removeClass('hidden');	
		}else{
			$("#copyEventForm .chance").addClass('hidden');
		}
	});
		
			
});
</script>

<div id="" class="">
	<div class="contentEditor ui-widget-content" id="copyEventFormDiv">
		<form id="copyEventForm">
			<div class="editor-header" >
				New Event From Old Record
			</div>	
			<div style="padding:10px;" class="editor-content">
				<input type="hidden" id="infoid" value="<?php echo isset($record)?$record->id:""?>">
				<input type="hidden" id="userprogid" value="<?php echo isset($record)?$record->userprogid:""?>">
				<input type="hidden" id="newuserprogid" value="<?php echo my_session_value('userprogID')?my_session_value('userprogID'):""?>">	
				
				<div class="half halfleft" style="width: 200px;padding-right: 10px;">
					<label for="lastname">Contact Person<span class="floatright">Surname</span></label>
					<input type="text" style='width:190px' id='lastname' maxlength='50' value="<?php echo isset($record)?$record->lastname:""?>">
				</div>
				<div class="half" style="width: 200px;padding-right: 10px;">
					<label for="firstname">&nbsp;<span class="floatright">First Name</span></label>
					<input type="text" style="width:190px" id="firstname" maxlength="50" value="<?php echo isset($record)?$record->firstname:""?>">
				</div>
				<div class="half" style="width: 55px">
					<label for="mi">&nbsp;<span class="floatright">MI</span></label>
					<input type="text" style="width:47px" maxlength="2" id="mi" value="<?php echo isset($record)?$record->mi:""?>">
				</div>
				<div class="">
					<label for="companyname">Company Name </label>
					<input style="width:467px" id="companyname" type="text" value="<?php echo isset($record)?$record->companyName:""?>">
				</div>
				<div>
					<label for="position">Position </label>
					<input style="width:467px" id="position" type="text" maxlength="50" value="<?php echo isset($record)?$record->position:""?>">
				</div>
				<div class="half halfleft" style="width: 152px;padding-right: 10px;">
					<label for="telNo">Contacts<span class="floatright">Telephone</span></label>
					<input type="text" style="width:142px;" id="telNo" maxlength="50" value="<?php echo isset($record)?$record->telephone:""?>">
				</div>
				<div class="half" style="width: 152px;padding-right: 10px;">
					<label for="faxNo">&nbsp;<span class="floatright">Fax</span></label>
					<input type="text" style="width:142px" id="faxNo" maxlength="50" value="<?php echo isset($record)?$record->fax:""?>">
				</div>
				<div class="half" style="width: 152px;">
					<label for="mobileNo">&nbsp;<span class="floatright">Mobile</span></label>
					<input type="text" style="width:143px" id="mobileNo" maxlength="50" value="<?php echo isset($record)?$record->mobile:""?>">
				</div>
				<div>
					<label for="email">Email</label>
					<input style="width: 467px;" id="email" type="text" value="<?php echo isset($record)?$record->email:""?>">
				</div>
				<div class="half halfleft" style="width: 132px;">
					<label for="eventType">Communication</label>
					<select id="eventType" style="width: 140px;">
						<option value="Incoming Call">Incoming Call</option>
						<option value="Incoming Mail">Incoming Mail</option>
						<option value="Outgoing Call">Outgoing Call</option>
						<option value="Outgoing Mail">Outgoing Mail</option>
					</select>				
				</div>				
				<div class="half" style="width: 125px;">
					<label for="remark">Remark </label>
					<select id="remark" style="width: 115px;">
						<option></option>
						<option value="Opportunity">Opportunity</option>
						<option value="Rejected">Rejected</option>
						<!-- <option value="noted">Noted</option> -->
					</select>
				</div>
				<div class="half otype hidden" style="width: 140px;">
					<label for="opptype">Opportunity Type: </label>
					<select id="opptype" style="width: 130px;">
						<option value="Won">Won</option>
						<option value="Loss">Loss</option>
						<option value="Pending">Pending</option>
					</select>
				</div>
				<div class="half chance hidden" style="width: 55px;">
					<label for="cpercent">Chance</label>
					<input class="gradeField" type="text" maxlength="3" style="width:52px" id="cpercent">
				</div>
				<div class="clearer"></div>
				<div class="note">
					<label for="note">Note </label>
					<textarea style="width: 467px;height: 30px;" id="note"></textarea>
				</div>
				<div>
					<label for="refferal">Refferal</label>
					<input type="text" style="width:467px" id="refferal">
				</div>
				
				<div id="editorButtonDiv" align="right">
					<button type="submit" class="button button_img" id="csave"><img src="assets/images/icons/save_edit.png">Save</button>
				</div>		
																
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$('.button').button();
	$("#copyEventForm").bind("submit",function(){		
		if(!$("#copyEventForm").hasClass('clicked')){
			if($("#copyEventForm #remark").val()=="Opportunity"){
				var opptype = $("#copyEventForm #opptype").val();
			}else{
				var opptype = "";
			}
			var form_data = {
						eventType : $("#copyEventForm #eventType").val(),
						companyName : $("#copyEventForm #companyname").val(),
						lastname : $("#copyEventForm #lastname").val(),
						firstname : $("#copyEventForm #firstname").val(),
						mi : $("#copyEventForm #mi").val(),
						position : $("#copyEventForm #position").val(),
						telephone : $("#copyEventForm #telNo").val(),
						fax : $("#copyEventForm #faxNo").val(),
						mobile : $("#copyEventForm #mobileNo").val(),
						email : $("#copyEventForm #email").val(),
						remark : $("#copyEventForm #remark").val(),
						opportunityType : opptype,
						cPercent : $("#copyEventForm #cpercent").val(),
						refferal : $("#copyEventForm #refferal").val(),
						note : $("#copyEventForm #note").val(),
						dateID : $("#dateid").val(),
						infoID : $("#copyEventForm #infoid").val(),
						
						userprogid : $("#userprogid").val(),
						newuserprogid : $("#newuserprogid").val(),
						
						ajax: '1'
					};
			$.ajax({
				url: "<?php echo site_url('main/copyinfo'); ?>",
				type: 'POST',
				data: form_data,
				success: function(msg) {
						if(msg=='add'){
								//alert('A new alumni has been added.');
								$.fancybox.close();
								refreshmaincontent(form_data['newuserprogid'],form_data['dateID']);							
						}
						else if(msg.search("html")<0){
							myMessageBox(msg,'Error','red',false);
							$("#copyEventForm").removeClass('clicked');
						}
						else{
							window.location.reload();
						}
				},
				error:function(){
					//alert('haha');
				}
				});
		}//end if has class clicked
		$("#copyEventForm").addClass('clicked');
		return false;
	});

	
});
</script>