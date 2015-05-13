<script type="text/javascript">
$(document).ready(function(){

	var ptemp_panel = false;
	$(".ptempimgdiv").bind("click",function(){
		if(!ptemp_panel){
			$("#ptemp_panel").animate({"bottom": "0px"}, "fast");
			ptemp_panel = true;
		}else{
			$("#ptemp_panel").animate({"bottom": "-320px"}, "fast");
			ptemp_panel = false;
		}
	});	
		
	$('#addprogram_panel #close').bind('click',function(e){
		$('#addprogram_panel').fadeOut(500);
		addprogram=false;
	});
	$("#addprogram_panel").hide();
	$("#addprogram_panel").draggable({handle: '.header'});
	var addprogram = false;
	$('#addprogram').click(function(e){	
		if(!addprogram)
		{
			addprogram=true;
			var fdata = {ajax:'1'};
			$.ajax({
				url:"<?php echo site_url('administrator/addprogram')?>",
				data:fdata,
				type:'POST',
				success:function(content){
					$("#addprogram_panel").html(content);
				}
			});
			$("#addprogram_panel").fadeIn('fast');
		}
		else
		{
			addprogram=false;
			$("#addprogram_panel").fadeOut(500);
		}		
	});
	var row = null;
	var editprogram = false;
	$('.editprogram').click(function(e){
		if(row)
		{
			if(editprogram && $(this).attr('id')==row){
				$("#addprogram_panel").fadeOut("fast");
				editprogram = false;
				addprogram=false;
			}else if(editprogram){
				$("#program_panel div").empty();
				$("#addprogram_panel").fadeIn("fast");
				getprogram($(this).attr('id'));
				editprogram = true;
			}else{
				$("#addprogram_panel").fadeIn("fast");
				getprogram($(this).attr('id'));
				editprogram = true;				
			}
		}
		else
		{
			if(!editprogram){
				$("#addprogram_panel").fadeIn("fast");
				getprogram($(this).attr('id'));
				editprogram = true;
			}
		}
		row = $(this).attr('id');			
	});			
	
});//end domument.ready
function getprogram(id){
	var fdata = {
			id : id,
			ajax:'1'
			};
	$("#program_panel div").empty();
	$.ajax({
		url:"<?php echo site_url('administrator/editprogram')?>",
		data:fdata,
		type:'POST',
		success:function(content){
			$("#addprogram_panel").html(content);
		}
	});
}
</script>
<div id="addprogram_panel">
</div>

<div style="padding:10px;min-height: 330px;">
<?php if($progPrivilege):?><button id="addprogram"><img src="assets/images/icons/add_program.png">New Program</button><?php endif;?>
	<div class="list">
		<table>
			<tr>
			<?php $i=1;
				foreach ($programs as $value):
					if($progPrivilege || $value['isActive']==1):
			?>			
				<td style="border-bottom: 0">
					<img <?php if($progStatus):?>id="<?php echo $value['id']?>" class="programstatus"<?php endif;?> style="float:left" src="<?php echo base_url()?>assets/photos/logo/<?php echo $value['logo']?>" width="120px" height="100px">
					<div style="float: left; padding: 5px;width: 220px;">
					<span <?php if($progStatus):?>id="<?php echo $value['id']?>" class="programstatus"<?php endif;?>>Name: <b><?php echo $value['title']?></b></span><br>
					<span>Batch: <b><?php echo $value['batch']?></b></span><span style="margin-left:10px;">Target: <b><?php echo $value['target']?></b></span><br>
					<?php if($value['details']!=""):?><span>Details: </span><b><?php echo $value['details']?></b><br><?php endif;?>
					<span>Start Date: <b><?php echo date("F j, Y",strtotime($value['dateStart']))?></b></span><br>
					<span>End Date: <b><?php echo date("F j, Y",strtotime($value['dateEnd']))?></b></span><br>
					<span>Date Created: <b><?php echo date("F j, Y",strtotime($value['dateCreated']))?></b></span><br>
					<span>Status: <?php echo ($value['isActive']=='1')?"<b style='color:red'>Active</b>":"<b style='color:orange'>Inactive</b>"?></span><br>
					<?php if($progPrivilege):?><span id="<?php echo $value['id']?>" class="editprogram">Edit</span>	<?php endif;?>
					</div>
				</td>
			<?php
					echo ($i%3==0)?"</tr><tr>":""; 
					$i++;
				endif;
			endforeach;
			?>					
			</tr>
			<tr>
				<td colspan="3" style="border-bottom: 0;">
				<?php echo $this->pagination->create_links()?>
				</td>
			</tr>
		</table>
	</div>
</div>
<?php if($progPrivilege):?>
<div id="ptempholder">
	<div id="ptemp_panel">
		<div class="ptempimgdiv" align="center"><!-- THIS IS THE HANDLE -->
			<img style="height: 30px;margin-left:20px;" alt="" src="<?php echo base_url()?>/assets/images/ptemplabel.jpg">
		</div>	
		<div style="padding:10px;" class="ptemplist">
			<div class="noborderlist">
				<table>
					<tr>
					<?php $i=1;
					foreach ($ptemplate as $value):
					?>
						<td style="padding:2px;border-bottom: 0;" align="center">
							<img src="<?php echo base_url()?>assets/photos/logo/<?php echo $value['logo']?>" width="80px" height="60px">
							<div>
							<span>Name: <b><?php echo $value['title']?></b></span><br>
							<a id="<?php echo $value['id']?>" class="editmini" rel="#mini" href="<?php echo site_url('administrator/editprogramtemplate')."/".$value['id']?>">Edit</a>	
							</div>
						</td>	
					<?php
					echo ($i%4==0)?"</tr><tr>":""; 
					$i++;
					endforeach;
					?>					
					</tr>						
				</table>
			</div>			
		</div>
		<div align="left" style="margin-left: 12px;"><a href="<?php echo site_url('administrator/addprogramtemplate')?>" id="addprogram" class="editmini" rel="#mini"><button><img src="assets/images/icons/add_program.png">New Program Template</button></a></div>
	</div>
</div>
<?php endif;?>
