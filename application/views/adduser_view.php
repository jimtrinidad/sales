<script src="<?php echo base_url()?>assets/js/jquery-ui-1.8.12.min.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
$(document).ready(function(){
	var isprog = false;
	$("input:[name='isAdmin']").click(function(){
		if(!isprog){
			$(".programTab").toggle('fast');
			isprog = true;
		}else{
			$(".programTab").toggle('fast');
			isprog = false;
		}
			
	});
	
	$("#userForm").submit(function(){
		if(!$("#saveU").hasClass('clicked')){
			var programs = Array();
			var archives = Array();
			if(!$("input:[name='isAdmin']").is(":checked")){				
				$("input:[name='programs']checked").each(function(i,e){
					programs.push($(e).val());
				}); 
				$("input:[name='archives']checked").each(function(i,e){
					archives.push($(e).val());
				}); 
			}
			/*
			var form_data = {
						username: $("#username").val(),
						password: $("#password").val(),
						passcon: $("#passcon").val(),
						email:$("#email").val(),
						firstname: $("#firstname").val(),
						lastname: $("#lastname").val(),
						programs: programs,
						archives : archives,

						dashboard: $("input:[name='dashboard']:checked").val(),
						dashDetail: $("input:[name='dashDetail']:checked").val(),
						schedule: $("input:[name='schedule']:checked").val(),
						program: $("input:[name='program']:checked").val(),
						programStatus: $("input:[name='programStatus']:checked").val(),
						canCopy: $("input:[name='canCopy']:checked").val(),
						reports: $("input:[name='reports']:checked").val(),
						statistics: $("input:[name='statistics']:checked").val(),
						ranking: $("input:[name='ranking']:checked").val(),
						canSendM: $("input:[name='canSendM']:checked").val(),
						post: $("input:[name='post']:checked").val(),							
						isAdmin: $("input:[name='isAdmin']:checked").val(),
						
						uID: $("#uID").val(),
						p_id : $("#p_id").val(),
						saveU: $("#saveU").val(),
						ajax: '1'
					};
			*/
				var form_data = $(this).serialize();
					form_data = form_data + '&programs=' + programs;
					form_data = form_data + '&archives=' + archives;
					
			$.ajax({
				url: "<?php echo site_url('user/save'); ?>",
				type: 'POST',
				data: form_data,
				success: function(msg) {
						if(msg=='add'){
								alert('A user has been added.');
								location.reload(true);
						}
						else if(msg=='edit'){
								alert('User profile has been updated.');
								location.reload(true);
						}
						else{
							alert(msg);
							$("#saveU").removeClass('clicked');
						}
					}
				});

			}//end of if clicked
		$("#saveU").addClass('clicked');
		return false;
	});	//end save click

	$("#tabsUsers").tabs({
		 tabTemplate: '<span><a href="#{href}"><span>#{label}</span></a></span>' ,
			 panelTemplate: '<li></li>',
			 selected: 2,
			 create: function(event, ui) { 
				setTimeout("firstClicked()",0);
				$("#tabsUsers").removeClass('hidden');
			 }
	});

	$("a").click(function(){$(this).blur();});

	$(".pages:first").addClass('_current').show();

	if($('.pages').length>1){ //show pagination if ther is more than 1 page
		$(".projectPagination").paginate({
			count 		: $('.pages').length,
			start 		: 1,
			display     : 5,
			border					: true,
			border_color			: '#999',
			text_color  			: '#888',
			background_color    	: '#EEE',	
			border_hover_color		: '#999',
			text_hover_color  		: '#C55917',
			background_hover_color	: '#dbdfdc', 
			mouse					: 'press',
			onChange     			: function(page){
										$('._current','#achivesPages').removeClass('_current').hide();
										$('#p'+page).addClass('_current').show();
									  }
		});	
	}

	$('.button').button();
});
function firstClicked(){
	$("#tabsUsers").find('a').first().trigger('click');
}
</script>
<form method="post" id="userForm" action="<?php site_url('user/save')?>">
<div id="tabsUsers" class="hidden" >
	<ul>
		<li><a href="#accountTab">Account</a></li>
		<li class="programTab <?php echo !empty($record['privilege']['isAdmin'])?"hidden":"";?>"><a href="#programTab">Programs</a></li>
		<li class="programTab <?php echo !empty($record['privilege']['isAdmin'])?"hidden":"";?>"><a href="#archiveTab">Archives</a></li>
		<li><a href="#privilegeTab">Privilege</a></li>
	</ul>
	<div id="accountTab">
		<!-- <div class="heading"><?php echo $heading ?></div> -->
		<input type="hidden" id="uID" name="uID" value="<?php if(isset($record['info']['id'])):echo $record['info']['id'];endif; ?>">
		<input type="hidden" id="p_id" name="p_id" value="<?php if(isset($record['info']['p_id'])):echo $record['info']['p_id'];endif; ?>">
		<table width="99%" border="0" cellpadding="0" cellspacing="0">
			<tbody>
				<tr>
					<td width="120px">
					<label for="username">Username: </label>
					</td><td style="display:inline"><input name="username" <?php set_value('username')?> value="<?php if(isset($record['info']['username'])):echo $record['info']['username'];endif; ?>"<?php set_value('username')?> id="username" type="text" style="width:99%">
					</td>
				</tr>
			<?php if(!isset($record['info']['id'])):?>
				<tr>
					<td>
					<label for="password">Password: </label>
					</td><td style="display:inline"><input name="password" id="password" type="password" style="width:99%">
					</td>
				</tr>
				<tr>
					<td>
					<label for="passcon">Retype Password: </label>
					</td><td style="display:inline"><input name="passcon"  id="passcon" type="password" style="width:99%">
					</td>
				</tr>
			<?php endif; ?>	
				<tr>
					<td>
					<label for="firstname">First Name: </label>
					</td><td style="display:inline"><input name="firstname" value="<?php if(isset($record['info']['firstname'])):echo $record['info']['firstname'];endif; ?>"<?php set_value('firstname')?> id="firstname" type="text" style="width:99%">
					</td>
				</tr>
				<tr>
					<td>
					<label for="lastname">Surname: </label>
					</td><td style="display:inline"><input name="lastname" value="<?php if(isset($record['info']['lastname'])):echo $record['info']['lastname'];endif; ?>"<?php set_value('lastname')?> id="lastname" type="text" style="width:99%">
					</td>
				</tr>	
				<tr>
					<td>
					<label for="email">Email: </label>
					</td><td style="display:inline"><input name="email" value="<?php if(isset($record['info']['email'])):echo $record['info']['email'];endif; ?>"<?php set_value('email')?> id="email" type="text" style="width:99%">
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div id="programTab" class="programTab <?php echo !empty($record['privilege']['isAdmin'])?"hidden":"";?>">
		<div class="programdiv">
			<?php if(count($programs->result_array())>0):?>
			<!-- <div class="heading">PROGRAMS</div> -->
			<div style="margin-top: 5px;margin-bottom: 7px;">
				<?php $i=1;?>
				<?php foreach($programs->result_array() as $value):?>
				<div style="float:left;width: 74px;height:55px;padding:5px;" align="center">
					<img title="<?php echo $value['details']?>" src="<?php echo file_exists('assets/photos/logo/'.$value['logo']) ? base_url().'assets/photos/logo/'.$value['logo'] : base_url().'assets/photos/logo/blanklogo.png' ?>" width="50px" height="40px">
					<div>
						<input style="position:relative;top:2px;padding:0;margin:0;" type="checkbox" id="<?php echo $value['id']?>" value="<?php echo $value['id']?>" name="programs" 
						<?php 
						if(isset($userprogram) && is_array($userprogram))
						{
							foreach($userprogram as $up)
							{
								if($up['pid']==$value['id']):echo "checked='checked'";break;endif;
							}	
						}
						?>
						>
							
						<label style="font-weight: bold;font-size:10px;" for="<?php echo $value['id']?>"><?php echo $value['title']." ".$value['batch']?></label>
					</div>
				</div>
				<?php if ($i%7==0):echo "<br>";endif;$i++;?>
				<?php endforeach;?>
				<div class="clearer"></div>
			</div>
			<?php endif;?>
		</div>
	</div>
	<div id="archiveTab" class="programTab <?php echo !empty($record['privilege']['isAdmin'])?"hidden":"";?>">
		<div class="programdiv" id="achivesPages">
			<?php if(count($userPrograms)>0):?>
				<?php 
					$perpage = 15;
					$chunkUserPrograms = array_chunk($userPrograms, $perpage,TRUE);

				?>
				<?php for($x = 1;$x<=count($chunkUserPrograms);$x++):?>
				<div style="margin-top: 5px;margin-bottom: 7px;" id="p<?php echo $x?>" class="pages">
					<?php $i=1;?>
					<?php foreach($chunkUserPrograms[$x-1] as $value):?>
					<div style="float:left;width: 155px;padding:5px;">	
						<img style="float: left;margin-right: 4px" src="<?php echo file_exists('assets/photos/logo/'.$value['logo']) ? base_url().'assets/photos/logo/'.$value['logo'] : base_url().'assets/photos/logo/blanklogo.png' ?>" width="50px" height="43px">				
						<div style="float: left;">
							<input style="position:relative;top:2px;padding:0;margin:0;" type="checkbox" id="a<?php echo $value['userProgramID']?>" value="<?php echo $value['userProgramID']?>" name="archives" 
							<?php 
							if(isset($userarchive) && is_array($userarchive))
							{
								foreach($userarchive as $ua)
								{
									if($ua['userProgramID']==$value['userProgramID']):echo "checked='checked'";break;endif;
								}	
							}
							?>
							>
								
							<label style="font-weight: normal;font-size:11px;font-family: tahoma;" for="a<?php echo $value['userProgramID']?>"><?php echo "<b>".$value['title']." ".$value['batch']."</b><br>Added by<br>".substr($value['firstname'], 0,1).". ".$value['lastname']?></label>
						</div>
					</div>
					<?php if ($i%3==0):echo '<div class="clearer"></div>';endif;$i++;?>					
					<?php endforeach;?>					
				</div>
				<?php endfor;?>
				<div class="clearer"></div>
				<table width="80%">
					<tr>
						<td>
						<div class="projectPagination"> </div>
						</td>
					</tr>
				</table>
			<?php endif;?>
		</div>			
	</div>
	<div id="privilegeTab" class="checkBoxList">
		<!-- <div class="heading">PRIVILEGES</div> -->
		<!-- 
			<table width="98%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td align="right" width="100px">Dashboard</td>
					<td align="left" width="30px"><input type="checkbox" value="1" name="dashboard" <?php echo !empty($record['privilege']['dashboard'])?"checked":"";?>></td>
					<td align="right" width="180px">Dashboard Records</td>
					<td align="left" width="10px"><input type="checkbox" value="1" name="dashDetail" <?php echo !empty($record['privilege']['dashDetail'])?"checked":"";?>></td>			
					<td align="right" width="150px">Programs</td>
					<td align="left"><input type="checkbox" value="1" name="program" <?php echo !empty($record['privilege']['program'])?"checked":"";?>></td>	
				</tr>	
				<tr>
					<td align="right" width="100px">Program Status</td>
					<td align="left"><input type="checkbox" value="1" name="programStatus" <?php echo !empty($record['privilege']['programStatus'])?"checked":"";?>></td>		
					<td align="right">Can Send Email</td>
					<td align="left"><input type="checkbox" value="1" name="canSendM" <?php echo !empty($record['privilege']['canSendM'])?"checked":"";?>></td>
					<td align="right">Can Copy</td>
					<td align="left"><input type="checkbox" value="1" name="canCopy" <?php echo !empty($record['privilege']['canCopy'])?"checked":"";?>></td>
				</tr>
				<tr>
					<td align="right" width="100px">User Ranking</td>
					<td align="left"><input type="checkbox" value="1" name="ranking" <?php echo !empty($record['privilege']['ranking'])?"checked":"";?>></td>	
					<td align="right" width="100px">Reports</td>
					<td align="left"><input type="checkbox" value="1" name="reports" <?php echo !empty($record['privilege']['reports'])?"checked":"";?>></td>	
					<td align="right">Schedules</td>
					<td align="left"><input type="checkbox" value="1" name="schedule" <?php echo !empty($record['privilege']['schedule'])?"checked":"";?>></td>
				</tr>
				<tr>
					<td align="right" width="100px">User Statistics</td>
					<td align="left"><input type="checkbox" value="1" name="statistics" <?php echo !empty($record['privilege']['statistics'])?"checked":"";?>></td>
					<td align="right" width="100px">Post Announcement</td>
					<td align="left"><input type="checkbox" value="1" name="post" <?php echo !empty($record['privilege']['post'])?"checked":"";?>></td>
					<td align="right"><b>Admin</b></td>
					<td align="left"><input type="checkbox" value="1" name="isAdmin" <?php echo !empty($record['privilege']['isAdmin'])?"checked":"";?>></td>		
				</tr>							
			</table>
		 -->
   		<table cellpadding="4" cellspacing="0" width="100%" border="0" class="permissionsList">
   			<tr>
   				<td width="50%">
			   		<ul>
					<?php foreach ($right as $key=>$label):?>
						<li>
							<input type="checkbox" value="1" id="<?php echo $key?>" name="<?php echo $key?>" <?php echo !empty($record['privilege'][$key])?"checked":"";?>>
							<label style="display: inline;" for="<?php echo $key?>"><span><?php echo $label?></span></label>
						</li>
					<?php endforeach;?>
					</ul>
   				</td>
   				<td width="50%" align="left">
			   		<ul>
					<?php foreach ($left as $key=>$label):?>
						<li>
							<input type="checkbox" value="1" id="<?php echo $key?>" name="<?php echo $key?>" <?php echo !empty($record['privilege'][$key])?"checked":"";?>>
							<label style="display: inline;" for="<?php echo $key?>"><span><?php echo $label?></span></label>
						</li>
					<?php endforeach;?>
					</ul>		   				
   				</td>
   			</tr>
   		</table> 			
	</div>
	<div align="right">
		<button class="button" type="submit" style="width:100px;margin-bottom: 3px;padding: 2px 4px;" id="saveU" name="saveU">Save</button>
	</div>
</div>
</form>