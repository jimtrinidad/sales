<script src="<?php echo base_url()?>assets/js/jquery.ui.widget.js"></script>
<script src="<?php echo base_url()?>assets/js/jquery.iframe-transport.js"></script>
<script src="<?php echo base_url()?>assets/js/jquery.fileupload.js"></script>
<link type="text/css" href="<?php echo base_url()?>assets/css/jquery.fileupload-ui.css" rel="stylesheet" />
<link type="text/css" href="<?php echo base_url()?>assets/css/bootstrap.min.css" rel="stylesheet" />
<link type="text/css" href="<?php echo base_url()?>assets/css/jquery.fileupload.css" rel="stylesheet" />
<script type="text/javascript">

$(document).ready(function(){

	/*
	$('#addevent_panel #close').bind('click',function(e){
		$('#addevent_panel').toggle(500);
		addevent=false;
	});

	
	$("#addevent_panel").hide();
	$("#addevent_panel").draggable({handle: '.header'});
	var addevent = false;
	$('#addevent').click(function(e){	
		if(!addevent)
		{
			addevent=true;
			$("#addevent_panel").toggle('fast');
		}
		else
		{
			addevent=false;
			$("#addevent_panel").toggle(500);
		}		
	});	
	*/
	
	$("#addevent").fancybox({
		'overlayOpacity'	: .4,
		'padding'			: 0,
		'centerOnScroll'	: true,
		'autoScale'			: true,
		'autoDimensions'	: true,
		'hideOnOverlayClick': false,
		'onComplete'		:	function(){
			if($('#eventFormDiv .editor-header:visible').length != 0){
				$("#fancybox-outer").css('backgroundColor','transparent').draggable({handle : '#eventFormDiv .editor-header'});
			}
			$.fancybox.center();
			hideMyLoader();
		}
	});	

	$("#open_upload_form").fancybox({
		'overlayOpacity'	: .4,
		'padding'			: 0,
		'centerOnScroll'	: true,
		'autoScale'			: true,
		'autoDimensions'	: true,
		'hideOnOverlayClick': false,
		onStart 			: function () {
			$('#uploadMessage').text('Upload excel file to process.').removeClass('text-danger');
			$('#uploadedXLSContent').html('');
		},
		'onComplete'		:	function(){
			$.fancybox.center();
			hideMyLoader();
		}
	});	



	$(".tableList th").each(function(){			 
		$(this).addClass("ui-state-default");			 
	});
	
	$(".tableList td").each(function(){
		$(this).addClass("ui-widget-content");
	});
	

	$('.tableList tr td:not(:last-child),.tableList tr th:not(:last-child)').css('border-right', '0');
	$('.tableList tr td').css('border-top', '0');	

	$(".tableList tr").hover(
			function(){
				$(this).children("td").addClass("ui-state-hover");
			},
			function(){
				$(this).children("td").removeClass("ui-state-hover");
			}
		);	

	$("#resultpages #pagination span,#resultpages #pagination strong").addClass('button');
	$('.button').button();
	$("#resultpages #pagination strong").addClass('ui-state-disabled');


	$('#fileupload').fileupload({
		url: './index.php/main/uploadxls/',
		dataType: 'json',
		done: function (e, data) {
			var response 	= data.result;
			if (response.status) {
				console.log(response.data);
			} else {
				$('#uploadMessage').text(response.message).addClass('text-danger');
				$('#progress .progress-bar').css('width', '0%');
			}
			hideMyLoader();
		},
		progressall: function (e, data) {
			showMyLoader();
			var progress = parseInt(data.loaded / data.total * 100, 10);
			$('#progress .progress-bar').css('width', progress + '%');
		}
	})
	.prop('disabled', !$.support.fileInput)
	.parent().addClass($.support.fileInput ? undefined : 'disabled');
			
});
</script>

<div id="historyholder">
	<div id="history_panel">
		
	</div>
</div>
<div id="eventdetailsholder" ><!-- CONTENT BOX FOR EVENT DETAILS  -->
	<div id="moredetailspanel">
	</div>
</div>

<div id="upload_form" class="hidden">
	<div class="contentEditor ui-widget-content" id="uploadFormDiv" style="width: 1200px;height: 600px;">
		<div class="editor-header" style="cursor: default;">
			Upload Excel Leads
		</div>
		<div class="uploadFormBody" style="padding: 10px;">
			<div style="padding: 10px;">
				<span class="btn btn-success fileinput-button">
					<i class="glyphicon glyphicon-plus"></i>
					<span>Upload</span>
				<!-- The file input field used as target for the file upload widget -->
					<input id="fileupload" type="file" name="xmlfile">
				</span>
				<span id='uploadMessage'>Upload excel file to process.</span>
				<br>
				<!-- The global progress bar -->
				<div id="progress" class="progress" style="margin-top:10px;width:240px;">
					<div class="progress-bar progress-bar-success"></div>
				</div>
			</div>
			<div id="uploadedXLSContent"></div>
		</div>
	</div>
</div>

<div id="addevent_panel" class="hidden">
	<div class="contentEditor ui-widget-content" id="eventFormDiv">
		<form id="addEventForm">
			<div class="editor-header" >
				New Event
			</div>
			<div style="padding:10px;" class="editor-content">
				<div class="half halfleft" style="width: 200px;padding-right: 10px;">
					<label for="lastname">Contact Person<span class="floatright">Surname</span></label>
					<input type="text" style='width:190px' id='lastname' maxlength='50' value="<?php echo isset($lastname)?$lastname:""?>">
				</div>
				<div class="half" style="width: 200px;padding-right: 10px;">
					<label for="firstname">&nbsp;<span class="floatright">First Name</span></label>
					<input type="text" style="width:190px" id="firstname" maxlength="50" value="<?php echo isset($firstname)?$firstname:""?>">
				</div>
				<div class="half" style="width: 55px">
					<label for="mi">&nbsp;<span class="floatright">MI</span></label>
					<input type="text" style="width:47px" maxlength="2" id="mi" value="<?php echo isset($mi)?$mi:""?>">
				</div>
				<div class="">
					<label for="companyname">Company Name </label>
					<input style="width:467px" id="companyname" type="text">
				</div>
				<div>
					<label for="position">Position </label>
					<input style="width:467px" id="position" type="text" maxlength="50">
				</div>
				<div class="half halfleft" style="width: 152px;padding-right: 10px;">
					<label for="telNo">Contacts<span class="floatright">Telephone</span></label>
					<input type="text" style="width:142px;" id="telNo" maxlength="50" value="<?php echo isset($workNo)?$workNo:""?>">
				</div>
				<div class="half" style="width: 152px;padding-right: 10px;">
					<label for="faxNo">&nbsp;<span class="floatright">Fax</span></label>
					<input type="text" style="width:142px" id="faxNo" maxlength="50" value="<?php echo isset($faxNo)?$faxNo:""?>">
				</div>
				<div class="half" style="width: 152px;">
					<label for="mobileNo">&nbsp;<span class="floatright">Mobile</span></label>
					<input type="text" style="width:143px" id="mobileNo" maxlength="50" value="<?php echo isset($mobileNo)?$mobileNo:""?>">
				</div>
				<div>
					<label for="email">Email</label>
					<input style="width: 467px;" id="email" type="text">
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
					<button type="submit" class="button button_img" id="save"><img src="assets/images/icons/save_edit.png">Save</button>
				</div>													
			</div>
		</form>
	<script type="text/javascript">
	$(document).ready(function(){
	
		$("#addEventForm #remark").change(function(){
			if($("#addEventForm #remark").val()=="Opportunity"){
				$("#addEventForm .otype").removeClass('hidden');
				$("#addEventForm #opptype").change();	
			}else{
				$("#addEventForm .otype").addClass('hidden');
				$("#addEventForm #opptype").change();
			}
		});
		
	
		$("#addEventForm #opptype").change(function(){
			if($("#addEventForm #opptype").val()=="Pending" && $("#addEventForm #remark").val()=="Opportunity"){
				$("#addEventForm .chance").removeClass('hidden');	
			}else{
				$("#addEventForm .chance").addClass('hidden');
			}
		});	
		
		$("#addEventForm").bind("submit",function(){		
			if(!$("#addEventForm").hasClass('clicked')){
				if($("#addEventForm #remark").val()=="Opportunity"){
					var opptype = $("#addEventForm #opptype").val();
				}else{
					var opptype = "";
				}
				var form_data = {
							eventType : $("#addEventForm #eventType").val(),
							companyName : $("#addEventForm #companyname").val(),
							lastname : $("#addEventForm #lastname").val(),
							firstname : $("#addEventForm #firstname").val(),
							mi : $("#addEventForm #mi").val(),
							position : $("#addEventForm #position").val(),
							telephone : $("#addEventForm #telNo").val(),
							fax : $("#addEventForm #faxNo").val(),
							mobile : $("#addEventForm #mobileNo").val(),
							email : $("#addEventForm #email").val(),
							remark : $("#addEventForm #remark").val(),
							opportunityType : opptype,
							cPercent : $("#addEventForm #cpercent").val(),
							note : $("#addEventForm #note").val(),
							refferal : $("#addEventForm #refferal").val(),
							dateID : $("#dateid").val(),
							userprogid : $("#detailsupid").val(),
							ajax: '1'
						};
				$.ajax({
					url: "<?php echo site_url('main/addevent'); ?>",
					type: 'POST',
					data: form_data,
					success: function(msg) {
							if(msg=='add'){
									//alert('A new alumni has been added.');
									//$('#addevent_panel').fadeOut('fast');
									$.fancybox.close();
									refreshmaincontent(form_data['userprogid'],form_data['dateID'],<?php echo my_session_value('showalldays')?"'".my_session_value('showalldays')."'":"''"?>);							
							}
							else if(msg.search("html")<0){
								myMessageBox(msg,'Error','red',false);
								$("#addEventForm").removeClass('clicked');
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
			$("#addEventForm").addClass('clicked');
			return false;//to stop reloading on submit
		});
	
		$("#listSearchForm").bind("submit",function(){
			var fdata = {
					searchkeyD : $("#searchkeyD").val(),
					searchvalD : $("#searchvalD").val(),
					etypeD : $("#etypeD").val(),
					remarkD : $("#remarkD").val(),
					statusD : $("#opptypeD").val(),
					upid : $("#detailsupid").val(),
					dateid : $("#detailsdateid").val(),
					showalldays : $("#showalldays").val(),
					ajax : '1'
					};
			//$("#recordDetails").empty().html("<img style='margin:50px 0 50px 490px	;' src='<?php echo base_url() ?>assets/images/bigloader.gif'>");
			showMyLoader();
			$.ajax({
				url : '<?php echo site_url('main/ajaxprogram')?>',
				type : 'POST',
				data : fdata,
				success : function(msg){
					$("#recordDetails").html(msg);
					hideMyLoader();
				},
				error : function(){
				}
			});
			return false;
		});		
				
	});
	</script>
	</div>	
</div>
<div style="width: 100%;">
<input type="hidden" id="dateid" value="<?php echo $date->id?>">
<img src="<?php echo base_url()?>assets/photos/logo/<?php echo $program->logo?>" style="float:left;width: 60px;height: 50px;border: 0;padding:0px 5px;">
	<span>Program : </span><b><?php echo $program->title." ".$program->batch?></b><br>
	<?php if($showalldays==""):?><span>Date : </span><b><?php echo date("l, F j, Y",strtotime($date->date))?></b><br>
			<a class="button button_img" id="addevent" href="#eventFormDiv"><img src="assets/images/icons/contact_new.png">New Event</a>
			<a class="button button_img" id="open_upload_form" href="#uploadFormDiv"><img src="assets/images/icons/report_excel.png">Upload</a>
	<?php else:?><span>Date : </span><b><?php echo date("F j, Y",strtotime($program->dateStart))?> - <?php echo date("F j, Y",strtotime($program->dateEnd))?></b><br>
	<?php endif;?>
</div>
<div class="clearer"></div>
<div class="list ui-widget-content" style="border: 0;margin-top: 3px;padding-top: 3px;">
<form id="listSearchForm">
	<span>
		<select id="searchkeyD" style="width: 110px;">
			<option value="lastname">Lastname</option>
			<option value="firstname">Firstname</option>
			<option value="companyName">Company</option>
		</select>
		<input type="text" id="searchvalD" style="width: 150px;">
		<label for="etype">&nbsp;Mode of Comm: </label><select id="etypeD" style="max-width:140px;">
											<option value="">Any</option>
											<?php foreach ($events as $k=>$v):?>
											<option value="<?php echo $k?>"><?php echo $v?></option>
											<?php endforeach;?>	
										</select>													
		<label for="remark">Remark: </label><select id="remarkD">
											<option value="">Any</option>
											<option value="Opportunity">Opportunity</option>
											<option value="Rejected">Rejected</option>
										</select>
		<label for="opptype">Status: </label><select id="opptypeD">
											<option value="">Any</option>
											<option value="Won">Won</option>
											<option value="Loss">Loss</option>
											<option value="Pending">Pending</option>
										</select>					
		<button class="trigger button button_img" type="submit"><img src="assets/images/icons/find.png">Search</button>	
	</span>
</form>
	<div id="recordDetails">
		<table cellspacing="0" cellpadding="0" border="0" class="tableList">
			<thead>				
				<tr>
				<th width="30px">#</th><th><?php echo $showalldays==""?"Time":"Date/Time";?></th><th>Mode of Comm.</th><th>Contact Person</th><th>Company Name</th>
				<th>Telephone</th><th>Remark</th><th>Status</th><th></th>
				</tr>
			</thead>
			<tbody id="tb">
			<?php $i=1+$counter?>
			<?php foreach ($results as $value):?>
				<tr id="trover" rowid="<?php echo $value['did']?>" class="trdetails" <?php echo $value['latest']==0?"style='background:#dadadb'":""?>>
					<td style="font-size:11px;">
					<input type="hidden" value="<?php echo $value['did']?>"><?php echo $i;?> )</td>
					<td><?php echo date("M j, Y/g:i A",strtotime($value['time']));//$showalldays==""?date("g:i A",strtotime($value['time'])):date("M j/g:i A",strtotime($value['time'])) ?></td>
					<td><?php echo $value['eventType'] ?></td>					
					<td class="name"><?php echo $value['lastname'].", ".$value['firstname'];echo $value['mi']!=""?" ".$value['mi'].".":"" ?></td>
					<td><?php echo $value['companyName'] ?></td>
					<td><?php echo $value['telephone'] ?></td>
					<td><?php echo $value['remark'] ?></td>
					<td><?php echo $value['opportunityType'] ?></td>
						<td align="right" class="opt">
							<?php if(userPrivilege('canSendM')==1):?>
								<?php if(!empty($value['email'])):?>
								<a class="sendemail" id="<?php echo $value['did']?>" title="Send email"><img src="assets/images/icons/send_email_user.png"></a> &nbsp;
								<?php endif;?>
							<?php endif;?>
						</td>					
				</tr>
				<?php $i++; ?>
			<?php endforeach;?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="5">
						<div id="resultpages"><?php echo $this->ajaxpagination->create_links()?></div>
					</th>
					<?php echo "<th style='text-align:right;border-left: 0;font-size: 9px;' colspan='4'>$msg</th>";?>
				</tr>
			</tfoot>			
		</table>
	</div>
	<input type="hidden" id="userid" value="<?php echo my_session_value('uid')?>">
	<input type="hidden" value="<?php echo $program->programTempID?>" id="programTempID">
	<input type="hidden" id="detailsupid" value="<?php echo $upid?>">
	<input type="hidden" id="detailsdateid" value="<?php echo $dateid?>">
	<input type="hidden" id="showalldays" value="<?php echo $showalldays?>">
</div>
<script type="text/javascript">
function getDetails(id){
	var fdata = {
			id:id,
			ajax:'1'
			};
	$("#moredetailspanel").empty().html("<div class='infoloader ui-widget-content'><img  src='<?php echo base_url() ?>assets/images/bigloader.gif'></div>");
	//showMyLoader();
	$.ajax({
		url : '<?php echo site_url('main/getDetails')?>',
		data : fdata,
		type : 'POST',
		success : function(msg){
			$("#moredetailspanel").html(msg);
			//hideMyLoader();
		},
		error : function(){
			//alert(id);
		}
	});
	$("#history_panel").empty();
	//showMyLoader();
	$.ajax({
		url : '<?php echo site_url('main/getHistory')?>',
		data : fdata,
		type : 'POST',
		success : function(msg){
			$("#history_panel").html(msg);
			//hideMyLoader();
		},
		error : function(){
			//alert(id);
		}
	});	
}
$(document).ready(function(){
	
	$("#resultpages #pagination span").click(function(){
		showMyLoader();
		$("#moredetailspanel").animate({"right": "-520px"}, "fast");
		$('#editevent_panel').hide();
		var fdata = {
				searchkeyD : $("#searchkeyD").val(),
				searchvalD : $("#searchvalD").val(),
				etypeD : $("#etypeD").val(),
				remarkD : $("#remarkD").val(),
				statusD : $("#opptypeD").val(),
				showalldays : $("#showalldays").val(),
					upid : $("#detailsupid").val(),
					dateid : $("#detailsdateid").val()
				};
		$.ajax({
			url : $(this).attr("href"),
			data : fdata,
			type : 'POST',
			success : function(res){
				$("#recordDetails").html(res);
				hideMyLoader();
			}
		});
	});	

	/*
	$(".trdetails").bind("click",function(){
		var id = $(this).attr('rowid');
		var fdata = {
				id: id,
				ajax:'1'
				};		
		myDialogBox('<?php echo site_url('main/getDetails')?>',fdata,id + '-details',$(this).find('.name').text(),{width : 'auto'});
	});

	*/
	$(".trdetails").bind("click",function(){
		$('#editevent_panel #close').bind('click',function(e){
			$('#editevent_panel').fadeOut('fast');
			editevent=false;
		});
	});		
	
	var row = null;
	var moredetailspanel = false;
	$(".trdetails td:not(.opt)").bind("click",function(){
		var rowid = $(this).parent(".trdetails").attr('rowid');
		$('#editevent_panel').hide();
		if(row){
			if(moredetailspanel && rowid==row){
				$("#history_panel").animate({"left": "-430px"}, "fast");
				$("#moredetailspanel").animate({"right": "-520px"}, "fast");
				moredetailspanel = false;
			}else if(moredetailspanel){
				getDetails(rowid);
				$("#history_panel").animate({"left": "-430px"}, "fast");
				$("#moredetailspanel").animate({"right": "-520px"}, "fast");
				$("#moredetailspanel").animate({"right": "0px"}, "fast");
				moredetailspanel = true;
			}else{
				getDetails(rowid);
				$("#copyevent_panel").fadeOut('fast');
				$("#right_panel").animate({"right": "-600px"}, "fast");
				$("#moredetailspanel").animate({"right": "0px"}, "fast");				
				moredetailspanel = true;				
			}
		}
		else
		{
			if(!moredetailspanel){
				getDetails(rowid);
				$("#copyevent_panel").fadeOut('fast');
				$("#right_panel").animate({"right": "-600px"}, "fast");
				$("#moredetailspanel").animate({"right": "0px"}, "fast");				
				moredetailspanel = true;
			}
		}
		row = rowid;		
	});	
	//*/

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