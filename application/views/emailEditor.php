<script type="text/javascript">
$(document).ready(function(){

	<?php if(!isset($result)):?>	
	$("#contactstrigger").bind("click",function(){
		$("#contactlist .addTrigger").die();
		$("#contactEmailTo .addTrigger").die();
		<?php if($fromDashboard==1):?>
		var url = '<?php echo site_url('dashboard/showcontacts')?>';
		var fdata = {
				day : $("#day").val(),
				program : $("#program").val(),
				status : $("#status").val(),
				lw : $("#lw").val(),
				ajax:1
				};
		<?php else:?>
		var url = '<?php echo site_url('main/showcontacts')?>';
		var fdata = {
				ajax:1
				};
		<?php endif;?>	
		myDialogBox(url,fdata,'contacts','Contacts',{width : '420'});	
	});	
	<?php endif;?>
	$('.emailButton').button();
		
});
</script>

<div id="userEditor" class="contentEditor" style="width: 900px;min-height: 400px;">
	<div id="emaileditorcontainer" class="">
		
		<table width="100%" border="0" cellpadding="2" cellspacing="0">
			<tbody>
				<tr  style="padding:5px;" >
					<td class="emaillabel" align="right" valign="top"><div style="padding-left: 40px;cursor: pointer;" id="contactstrigger" class="emailButton"><label style="cursor: pointer;" for="to">To:</label></div></td>
					<td style="width: 734px;padding-left: 4px;">
						<?php if(isset($result)):?><span style="font-size: 12px;font-weight: bold;color: #333;margin-left:2px;"><?php echo $result['firstname']." ".$result['lastname'];?></span>
						<?php else:?><textarea id="emailbox" style="min-width:705px;height: 16px;"></textarea>
						<?php endif;?>
					</td>
					<td><div id="loader"><span class="sendbotton emailButton" id="send">Send</span></div></td>
				</tr>
				<tr>
					<td class="emaillabel" valign="top" align="right"><div style="padding-left: 12px;" class="emailButton"><label for="subject">Subject:</label></div></td>
					<td style="padding-bottom:1px;" colspan="2"><input name="subject" value="" id="subject" type="text" style="width:775px;font-weight:bold;margin-left:2px;margin-top:1px;"/></td>
				</tr>
				<tr>
					<td colspan="3">
					<textarea name="emessage" id="message"></textarea>
					<script type="text/javascript">
						if(CKEDITOR.instances['message']){
						   delete CKEDITOR.instances['message'];
						}
						CKEDITOR.replace( 'message',{
							toolbar: [
								['Source','-','Preview'],
							    ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt'],
							    ['Undo','Redo','-','SelectAll'],
							    ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'],
							    ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
							    ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv'],
							    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
							    ['BidiLtr', 'BidiRtl'],
							    ['Link','Unlink','Anchor'],
							    ['Image','Table','HorizontalRule','Smiley','SpecialChar','PageBreak'],
							    ['Styles','Format','Font','FontSize'],
							    ['TextColor','BGColor'],
							    ['Maximize']
						    ],
					        uiColor : '#dbdbdb',
					        height: 300,
					        resize_enabled  : true,
					        resize_maxWidth: 848,
					        resize_minWidth: 848,
					        resize_minHeight: 350,
					        skin : 'kama',
					        disableNativeSpellChecker : false
					    });						
					</script>
					<script type="text/javascript">
						function sendClick(){
							var strarray = new Array();
							<?php if(!isset($result)):?>
								var str = $("#emailbox").val();
								strarray = str.split(',');
							<?php endif;?>
							if(strarray.length <= 10){
								sendemail();
							}else{
								myMessageBox('Only 10 emails is allowed per send. You have ' + strarray.length,'Error','red',false);
								
							}
						}
						function sendemail(){
							if(!$("#send").hasClass('clicked')){				
								var form_data = {
											subject:$("#subject").val(),
											message: CKEDITOR.instances.message.getData(),
											detailsid:$("#detailsid").val(),
											emailbox: $("#emailbox").val(),
											ajax: '1'
										};
								$("#loader .sendbotton").empty().html('<img style="width:23px;height:18px;" src="<?php echo base_url()?>assets/images/searchloader.gif" />');
								$.ajax({
									url: "<?php echo site_url('main/sendemail'); ?>",
									type: 'POST',
									data: form_data,
									success: function(msg) {
											if(msg=="invalidemail"){
												myMessageBox("Please make sure that all addresses are properly formed.",'Error','red',false);
												$("#loader").empty().html('<span class="sendbotton emailButton" id="send" onClick="sendClick()">Send</span>');
												$('.emailButton').button();
												$("#send").removeClass('clicked');
											}else{
												myMessageBox(msg,'Success','green',false);
												$('#dialog-emailer').dialog('close');
											}
										}
									});
							}//end of if clicked
							$("#send").addClass('clicked');
						}
						
						$(document).ready(function(){
							$("#send").bind("click",function(){
								sendClick();
							});
						});
					</script>
					</td>
				</tr>
			</tbody>
		</table>		
	</div>
</div>