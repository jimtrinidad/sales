<script type="text/javascript">
$(document).ready(function(){	
	$(".delete").click(function(){
		var e = $(this).attr("id");
		var msg = $(this).attr("msg");
		var header = $(this).attr("header");
		var loc = $(this).attr("loc");
		myConfirmBox(header,msg,
				function(){
					$.ajax({
						url: loc + '/' + e,
						type: 'GET',
						success : function(msg){
							//alert(msg)
								location.reload(true);
							}
					});
				});				
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

	$('.editUser').bind("click",function(){
		var e = $(this).attr("id");
		var fdata = {
				ajax : 1
				};
		myDialogBox('<?php echo site_url('user/edit')?>/' + e,fdata,'edit_user','Edit User',{width : '600'});
	    return false;
	});			

	$('.button').button();
	
	$('.changePassword').bind("click",function(){
		var e = $(this).attr("id");
		var fdata = {
				refid : e,
				ajax : 1
				};
		ajaxCallBoxOpen('<?php echo site_url('user/password')?>',fdata);
	    return false;
	});		
		
});
function openwall(){
	var window_dimensions = "toolbars=no,menubar=no,location=no,scrollbars=no,resizable=yes,status=no";  
		window.open("<?php echo site_url('user/wall')."/".$id?>","_blank",  window_dimensions);     
	return true;
}
</script>
<div class="ui-widget-content">
	<div class="ui-widget-header widget-title">
		<?php echo $lastname.", ".$firstname?> 
		<span style="float:right;"><a style="color:#ffffff" href="<?php echo site_url('user/manage')?>">Back</a></span>
	</div>
	<div class="sidebar-content widget-content" id="">
		<span>Last login:<b> <?php if($lastloggin!='0000-00-00 00:00:00'):echo date("l, F j, Y - g:i:s a",strtotime($lastloggin));else:echo "N/A";endif;?></b></span><br>
		<span>Date added:<b> <?php echo date("F d, Y",strtotime($dateadded))?></b></span><br>
		<span>Email:<b> <?php echo $email?></b></span><br>
		<span><a href="javascript:void()" id="<?php echo $id?>" onClick="openwall()">Wall</a></span><br>
		<span><a class="delete" msg="Reset login attempts" header="Reset" loc="<?php echo site_url('user/resetLogin')?>" id="<?php echo $id?>">Reset Login Attempts</a></span><br>
		<span><a class="changePassword" id="<?php echo $id?>">Change Password</a></span><br>
		<span><a id="<?php echo $id?>" class="editUser">Edit</a></span><br><br>
	</div>
</div>
<div class="ui-widget-content">
	<div class="ui-widget-header widget-title">Activities</div>
	<div class="list sidebar-content widget-content">
		<table cellspacing="0" cellpadding="0" border="0" class="tableList">
			<thead>
				<tr>
				<th width="30px">#</th><th>Actions</th><th>IP Addess</th><th>Date Time</th>
				<th><span style="float:right;"><a header="Delete All" msg="Are you sure you want to remove all the logs?" loc="<?php echo site_url('user/deletealltrails')?>" class="button button_img delete" id="<?php echo $id?>"><img src="assets/images/icons/delete.png">Delete All</a></span></th>
				</tr>
			</thead>
			<tbody id="tb">
			<?php $i=1+$counter;?>
			<?php foreach ($results->result_array() as $value):?>
				<tr id="trover">
					<td style="font-size:11px;">
					<?php echo $i;?> )</td>
					<td><?php echo $value['action']?></td>
					<td><?php echo $value['ipaddress']?></td>
					<td width="200px"><?php echo date("F j, Y - g:i:s a",strtotime($value['datetime']))?></td>
					<td align="center" class="opt">
						<a class="delete" header="Delete"  msg="Are you sure you want to remove this log?" loc="<?php echo site_url('user/deletetrail')?>" id="<?php echo $value['id']?>"><img src="assets/images/icons/delete.png"></a>
					</td>
				</tr>
				<?php $i++; ?>
				<?php endforeach;?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="3" align="left">
						<div id="trailpages"><?php echo $this->pagination->create_links()?></div>
					</th>
					<th colspan="2" style="border-left: 0;text-align: right;font-size: 9px;">
						<?php echo $msg?>
					</th>
				</tr>
			</tfoot>
		</table>
	</div>
</div>