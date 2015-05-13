<div id="userEditor" class="contentEditor" style="width: 316px;">
	<div class="editor-header" >
		Holiday Editor
	</div>
	<div class="editor-content ui-widget-content">
		<form id="editorForm" class="">
			<?php if(isset($holiday_id)):?>
				<input type="hidden" name="holiday_id" id="holiday_id" value="<?php echo $holiday_id?>">
			<?php endif;?>
			<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
				<div id="tabs-basic" class="ui-tabs-panel ui-widget-content ui-corner-bottom">
					<div>
						<label for="holiday_type">Type</label>
						<select class="holidayType" name="holiday_type" style="width: 167px;">
							<option value="regular" <?php echo isset($holiday_type) && $holiday_type == 'regular' ? 'selected="selected"' : ''?>>Regular</option>
							<option value="special" <?php echo isset($holiday_type) && $holiday_type == 'special' ? 'selected="selected"' : ''?>>Special</option>
						</select>
					</div>
					<div class="half" style="width: 88px;padding-right: 8px;">
						<label for="">Date<span class="floatright">month</span></label>
						<select name="month">
							<option value="" <?php echo isset($day) && $day == '' ? 'selected="selected"' : ''?>>Month</option>
							<?php foreach ($months as $k=>$v):?>
							<option value="<?php echo sprintf('%02d',$k)?>" <?php echo isset($month) && $month == sprintf('%02d',$k) ? 'selected="selected"' : ''?>><?php echo $v?></option>
							<?php endforeach;?>
						</select>
					</div>					
					<div class="half halfleft" style="width: 50px;padding-right: 8px">
						<label for="">&nbsp;<span class="floatright">day</span></label>
						<select name="day">
							<option value="" <?php echo isset($day) && $day == '' ? 'selected="selected"' : ''?>>Day</option>
							<?php for($i = 01;$i<=31;$i++): $i = sprintf('%02d',$i);?>
							<option value="<?php echo $i?>" <?php echo isset($day) && $day == $i ? 'selected="selected"' : ''?>><?php echo $i?></option>
							<?php endfor;?>
						</select>
					</div>
					<div class="half yearDiv <?php echo isset($holiday_type) && $holiday_type == 'special' ? '' : 'hidden'?>" style="width: 89px;">
						<label for="">&nbsp;<span class="floatright">year</span></label>
						<input style="width: 77px;" type="text" placeholder="YYYY" maxlength="4" value="<?php echo isset($year) ? $year : ''?>" name="year" id="year">
					</div>
					<div class="half" style="">
						<label for="description">Description</label>
						<input type="text" style="width: 250px;" value="<?php echo isset($description) ? $description : ''?>" name="description" id="description">
					</div>				   					   		
			   		<div class="clearer"></div>
			   </div>
			</div>
   
			<div id="editorButtonDiv">
				<button class="button">Save</button>
				<a class="button" id="cancel">Cancel</a>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$(".button").button();

	$('.holidayType').change(function(){
		if(this.value == 'special'){
			$('.yearDiv').removeClass('hidden');
		}else{
			$('.yearDiv').addClass('hidden');
		}
	});
	
	$("#editorForm").bind("submit",function(){
		if(!$(this).hasClass('submitted'))
		{
			$(this).addClass('submitted');
			var fdata = $(this).serialize();
			showMyLoader();
			$.ajax({
				url : '<?php echo site_url('schedule/save_holiday')?>',
				type : 'POST',
				data : fdata,
				success : function (msg){
					//alert(msg);
					//hideMyLoader();/*
						result = $.parseJSON(msg);
						if(result.status == "error"){
							myMessageBox(result.message,'Error','red',false);
							$("#editorForm").removeClass('submitted');
						}else{
							myConfirmBox('Success','Your actions has been successfully executed. Do you want to reload the page to see the changes?',
									function(){
										location.reload();
									},
								"Reload","Later");	
							$.fancybox.close();	
						}
					//*/
					}
			});
		}
		return false;
	});
	$("#cancel").bind("click",function(){$.fancybox.close();});
});
</script>
