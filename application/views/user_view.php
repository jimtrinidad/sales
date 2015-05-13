<script type="text/javascript">
$(document).ready(function(){
	$(".delete").click(function(){
		var e = $(this).attr("id");
		myConfirmBox('Disable','Are you sure you want to disable this user?',
				function(){
					window.location = '<?php echo site_url('user/delete');?>/' + e;
				});	
	});
	$(".enable").click(function(){
		var e = $(this).attr("id");
		myConfirmBox('Enable','Are you sure you want to enable this user?',
				function(){
					window.location = '<?php echo site_url('user/enableuser');?>/' + e;
				});			
	});	

	$('.changePhoto').bind("click",function(){
		var fdata = {
				refid : this.id,
				ajax : 1
				};
		ajaxCallBoxOpen('<?php echo site_url('user/changePhoto')?>',fdata);
	    return false;
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

	$("#userpages #pagination a,#userpages #pagination strong").addClass('button');
	$('.button').button();
	$("#userpages #pagination strong").addClass('ui-state-disabled');

	$('.addUser').bind("click",function(){
		var fdata = {
				ajax : 1
				};
		myDialogBox('<?php echo site_url('user/add')?>',fdata,'user_editor','New User',{width : '600'});
	    return false;
	});	

	$('.editUser').bind("click",function(){
		var e = $(this).attr("id");
		var fdata = {
				ajax : 1
				};
		myDialogBox('<?php echo site_url('user/edit')?>/' + e,fdata,'user_editor','Edit User',{width : '600'});
	    return false;
	});		

	
});

function userWall(id){
	var window_dimensions = "toolbars=no,menubar=no,location=no,scrollbars=no,resizable=yes,status=no";  
		window.open("<?php echo site_url('user/wall')."/"?>"+id,"_blank",  window_dimensions);     
	return true;
}
</script>
<script src="<?php echo base_url()?>assets/js/changePhoto.js" type="text/javascript"></script>
<div class="ui-widget-content">
	<div style="padding:10px;padding-bottom: 3px;font-family: verdana;font-size:15px;font-weight:bold">
		<div>List of Users</div>
		<button class="addUser button button_img"><img src="assets/images/icons/user_add.png">New User</button>
	</div>
	
	<div class="list ui-widget-content" style="border: 0;padding: 5px;">
		<table cellspacing="0" cellpadding="0" border="0" class="tableList">
			<thead>
				<tr>
				<th width="20px">#</th><th>Name</th><th>Date Added</th><th colspan="2">Last Login</th>
				</tr>
			</thead>
			<tbody id="tb">
			<?php $i=1+$counter;?>
			<?php foreach ($results->result_array() as $value):?>
				<tr <?php echo ($value['isActive']=='0')?"style='background:#bdbdbd'":" id='trover' "?>>
					<td style="font-size:11px;">
					<input type="hidden" value="<?php echo $value['id']?>"><?php echo $i;?> )</td>
					<td class="names">
						<?php if($value['isActive']=='1'):?>
							<a class="profile" id="<?php echo $value['id']?>" href="<?php echo site_url('user/profile')."/".$value['id']?>">
							<?php echo $value['lastname'].", ".$value['firstname'] ?></a>
						<?php else:?>
						<b><?php echo $value['lastname'].", ".$value['firstname'] ?></b>
						<?php endif;?></td>
					<td><?php echo date("F d, Y",strtotime($value['dateAdded']))?></td>
					<td><?php if($value['lastLoggin']!='0000-00-00 00:00:00'):echo date("F d, Y - g:i:s a",strtotime($value['lastLoggin']));else:echo "N/A";endif;?></td>
					<td align="right" class="opt" style="width: 220px;">
						<?php if($value['isActive']=='1'):?>
						<a href="javascript:void()" id="<?php echo $value['id']?>" class="button" onClick="userWall(<?php echo $value['id']?>)"><img src="assets/images/icons/wall.png">Wall</a> 						
						<a href="<?php echo site_url('user/changePhoto')?>" id="<?php echo $value['id']?>" class="button changePhoto"><img src="assets/images/icons/change_photo.png">Photo</a>
						<a id="<?php echo $value['id']?>" class="editUser" title="edit user"><img src="assets/images/icons/user_edit.png"></a>
						<a class="delete" id="<?php echo $value['id']?>" title="disable user"><img src="assets/images/icons/disable.png"></a>
						<?php else:?>
						<a class="enable" id="<?php echo $value['id']?>" title="enable user"><img src="assets/images/icons/enable.png"></a>
						<?php endif;?>
					</td>
				</tr>
				<?php $i++; ?>
			<?php endforeach;?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="5">
						<div id="userpages"><?php echo $this->pagination->create_links()?></div>
					</th>
				</tr>
			</tfoot>			
		</table>
	</div>
</div>