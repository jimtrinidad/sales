<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" >
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title><?php echo $title?></title>
<script src="<?php echo base_url()?>assets/js/jquery-1.6.2.min.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo base_url()?>assets/js/jquery-ui-1.8.12.min.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo base_url()?>assets/js/noSelect.js" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo base_url()?>assets/js/jqueryslidemenu.js" type="text/javascript"></script>

<link type="text/css" href="<?php echo base_url()?>assets/css/styles.css" rel="stylesheet" />
<link type="text/css" href="<?php echo base_url()?>assets/css/jqueryslidemenu.css" rel="stylesheet" />
<link type="text/css" href="<?php echo base_url()?>assets/css/redmond/jquery-ui.css" rel="stylesheet" />


<script type="text/javascript">
function noselect(){
	<?php if(isset($privilege) && $privilege['canCopy']!=1):?>
	$("#main-container,.contentWrap").disableTextSelect();
	<?php endif; ?>	
}

var zIndex = 510; 
function make_draggable(elements)
{
    /* Elements is a jquery object: */
    elements.draggable({
        start:function(e,ui){ ui.helper.css('z-index',++zIndex); },
        stop:function(e,ui){
    		var fdata = {
					zIndex: zIndex,
					xpos :$(this).css('left'),
					ypos :$(this).css('top'),
					id  : $(this).attr('noteid'),
					ajax:1
				};
    		$.ajax({
    			url:'<?php echo site_url('user/saveNotePos')?>',
    			type:'POST',
    			data:fdata,
    			success : function(msg){
    				//alert(msg);
    			}
    		});
        }
    });
}
$(document).ready(function(){

	var tmp;
	$('.stickynote').each(function(){
		 /* Finding the biggest z-index value of the notes */
		  tmp = $(this).css('z-index');
		  if(tmp>zIndex) zIndex = tmp;
	});

	make_draggable($(".dragnote"));	
	
	$('#addnote_panel #close').live('click',function(e){
		$("#modal").addClass('hidden');
		$('#addnote_panel').fadeOut('fast');
		addnote=false;
	});
	
	$("#addnote_panel").draggable({handle: '.header'});
	var addnote = false;
	$('#addnote').click(function(e){
		if(!addnote)
		{
			addnote=true;
			$('#addnote_panel #noteText').val("");
			$("#addnote_panel .notePrev .content").html("");
			$("#modal").removeClass('hidden');
			$("#addnote_panel").fadeIn('fast');
		}
		else
		{
			addnote=false;
			$("#modal").addClass('hidden');
			$("#addnote_panel").fadeOut('fast');
		}		
	});	
	$("#modal").click(function(){
		$("#modal").addClass('hidden');
		$('#addnote_panel').fadeOut('fast');
		addnote=false;
	});	

	$('#addnote_panel #noteText').live('keyup',function(e){
		//alert($(this).val().replace(/<[^>]+>/ig,''));
		/* Setting the text of the preview to the contents of the input field, and stripping all the HTML tags: */
		$("#addnote_panel .notePrev .content").html($(this).val().replace(/<[^>]+>/ig,''));
	});

	$('#addnote_panel #savenote').live("click",function(){
		var fdata = {
					zIndex: ++zIndex,
					userWall:$("#userWall").val(),
					note:$("#addnote_panel #noteText").val(),
					ajax:1
				};
		$.ajax({
			url:'<?php echo site_url('user/addNote')?>',
			type:'POST',
			data:fdata,
			success:function(noteid){
				//location.reload();
				var tmp = $('#addnote_panel .notePrev').clone();
				tmp.find('.stickynote').end().css({'z-index':zIndex,'margin-left':50,'margin-top': 140}).attr('noteid',noteid);
				tmp.find('.top').html("<span id='close'><img class='hidden' title='Remove' src='<?php echo base_url()?>/assets/images/cross.png'/></span>");
				tmp.find('#close').attr('class',noteid);
				tmp.appendTo($('#container'));	
				make_draggable(tmp);
				addnote=false;
				$("#modal").addClass('hidden');
				$("#addnote_panel").fadeOut('fast');			
			}
		});
	});

	$("#stickynote #close").live("click",function(){
		var note = $(this).parent().parent();
		note.remove();
		var fdata = {
					id : $(this).attr('class'),
					ajax : 1
				};
		$.ajax({
			url:'<?php echo site_url('user/removeNote')?>',
			type:'POST',
			data:fdata,
			success : function(msg){
				
			}
		});
	});

	$(".stickynote").live("mouseover",function(){
		$(this).find("img").removeClass("hidden");
	});
	$(".stickynote").live("mouseout",function(){
		$(this).find("img").addClass("hidden");
	});

	$('.button').button();
	
});

</script>
</head>

<body onload="noselect()">
	<div style="background: url('<?php echo base_url()?>/assets/images/modalBg.png') repeat;z-index: 999;width: 100%;height: 100%;position: fixed;" class="hidden" id="modal"></div>
	<div id="main-container" >
		<div id="container">
			<!--
			<div id="header">
			<div id="navigator">
				<div class="clearer"></div>
					<div id="myslidemenu" class="jqueryslidemenu">
						<ul>
	
						 <li><a class="first" href="javascript:void()"><img src="<?php echo base_url()?>/assets/images/icons/home.png"/>Home</a></li>	
						
						<?php if($privilege['isAdmin']==0 && $privilege['canSendM']==1):?>
						<li><a href="javascript:void()"><img src="<?php echo base_url()?>/assets/images/icons/email.png"/>Email</a></li>
						<?php endif;?>	
						
						<?php if($privilege['dashboard']==1):?>
						<li><a href="javascript:void()"><img src="<?php echo base_url()?>/assets/images/icons/grid.png"/>Dashboard</a></li>
						<?php endif;?>	
						<?php if($privilege['program']==1 || $privilege['programStatus']==1):?>
						<li><a href="javascript:void()"><img src="<?php echo base_url()?>/assets/images/icons/book_addresses.png"/>Programs</a></li>
						<?php endif;?>
						<?php if($privilege['ranking']==1):?>
						<li><a href="javascript:void()"><img src="<?php echo base_url()?>/assets/images/icons/trophy.png"/>User Ranking</a></li>
						<?php endif;?>					
						<?php if($privilege['reports']==1):?>
						<li><a href="javascript:void()"><img src="<?php echo base_url()?>/assets/images/icons/reports.png"/>Reports</a></li>
						<?php endif;?>											
						<?php if($privilege['isAdmin']==1):?>
						<li><a href="javascript:void()"><img src="<?php echo base_url()?>/assets/images/icons/users.png"/>User</a></li>
						<?php endif;?>						
						<li><a href="javascript:void()"><img src="<?php echo base_url()?>/assets/images/icons/logout.png"/>Logout</a></li>
						</ul>
					</div>
					<div style="float:right;padding-top:2px;padding-right:10px;margin-top: 4px">
					<?php if(isset($uname)):if($privilege['isAdmin']==1):echo "Admin: <b>".$uname."</b>";else:echo "User: <b>".$uname."</b>";endif;endif; ?>
					</div>
				<div class="clearer"></div>
			</div>
		</div>
		-->
			<div id="main-header">
				<div class="logoDiv">
					 <div class="img"><a href="<?php echo site_url()?>"><img src="<?php echo base_url()?>assets/images/solidground-logo.png" alt="" /></a></div>
				</div>	
				<div id="menuTabs">
					<div id="myslidemenu" class="jqueryslidemenu">
						<ul>							
							 <li class="first_child"><a class="first" href="javascript:void()"><img src="<?php echo base_url()?>/assets/images/icons/home.png"/>Home</a></li>	
							
							<?php if($privilege['isAdmin']==0 && $privilege['canSendM']==1):?>
							<li class="first_child"><a href="javascript:void()"><img src="<?php echo base_url()?>/assets/images/icons/email.png"/>Email</a></li>
							<?php endif;?>	
							
							<?php if($privilege['dashboard']==1):?>
							<li class="first_child"><a href="javascript:void()"><img src="<?php echo base_url()?>/assets/images/icons/grid.png"/>Dashboard</a></li>
							<?php endif;?>	
							<?php if($privilege['program']==1 || $privilege['programStatus']==1):?>
							<li class="first_child"><a href="javascript:void()"><img src="<?php echo base_url()?>/assets/images/icons/book_addresses.png"/>Programs</a></li>
							<?php endif;?>
							<?php if($privilege['ranking']==1):?>
							<li class="first_child"><a href="javascript:void()"><img src="<?php echo base_url()?>/assets/images/icons/trophy.png"/>User Ranking</a></li>
							<?php endif;?>					
							<?php if($privilege['reports']==1):?>
							<li class="first_child"><a href="javascript:void()"><img src="<?php echo base_url()?>/assets/images/icons/reports.png"/>Reports</a></li>
							<?php endif;?>											
							<?php if($privilege['isAdmin']==1):?>
							<li class="first_child"><a href="javascript:void()"><img src="<?php echo base_url()?>/assets/images/icons/users.png"/>User</a></li>
							<?php endif;?>
						</ul>
					</div>
	
					<div class="clearer"></div>
				</div>
				<div class="clearer"></div>			
				<div class="loggedUser">
					<table cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td align="right" valign="top">
								<div style="font-size: 11px;">Welcome <?php if(isset($userWall)):if($privilege['isAdmin']==1):echo "Administrator";else:echo "User";endif;endif; ?></div>
								<div class="name"><?php echo getUserData($userWall)->name?></div>
								<div><a class="button logoutButton" href=javascript:void()">logout</a></div>
							</td>
							<td width="50px;">
								<img class="userProfilePicThumb" alt="" src="<?php echo file_exists('assets/images/userphoto/'.getUserData($userWall)->photo) ? base_url().'assets/images/userphoto/'.getUserData($userWall)->photo : base_url().'assets/images/userphoto/blank.jpg'?>"/>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="clearer"></div>					
			<button id="addnote" class="button" style="position:absolute;top: 67px;left: 100px;margin: 2px;z-index: 105;border: 0;cursor: pointer;">Add note</button>
			<div id="content">
				<?php $this->load->view($content)?>
			</div>
			<div id="main-footer">
				Solid Ground Consulting Ltd.<br/>
				5th Floor, East Tower Philippine Stock Exchange, Tektite Tower Exchange Road, Ortigas Center, Pasig City<br/>
				<span><?php echo date("l,  F j, Y g:i:s a",strtotime(NOW))?></span>
			</div>		
		</div>
	</div>
<div id="addnote_panel" class="hidden">
	<div class="header">
	<span style="float:left;margin:5px;font-weight:bold">Add a new note</span> 
	<span id="close" style="float: right;margin: 5px;font-weight: bold">x</span>
	<div class="clearer"></div>
	</div>
	<div style="padding:10px; border-top: 1px solid #dddddd;min-height: 50px;" >
		<div id="stickynote" class="stickynote notePrev" style="margin-top:0;">
			<div class="top"></div>
			<div class="content"></div>
			<div class="bot">
				<div id="footnote">
					<span id="postdate"><?php echo date("g:i a - D, M-j-y",strtotime(NOW))?></span>
					<br/>
					<span id="postby"><?php echo $uname;?></span>	
				</div>
			</div>
		</div>	
		<div class="noteData">
			<label for="noteText">Text of the note</label>
			<textarea id="noteText"></textarea>	
			<div align="right"><button id="savenote" class="button" style="margin:2px -10px;z-index: 105;cursor: pointer;">Save note</button></div>
		</div>		
	</div>
	<div class="clearer"></div>
</div>
<input type="hidden" id="userWall" value="<?php echo $userWall?>" />
<?php if(isset($notes)):?>
	<?php foreach ($notes as $note):?>		
	<div id="stickynote" class="stickynote dragnote" style="<?php echo $note['xpos'] != 0 && $note['ypos'] != 0 ? "top:".$note['ypos']."px;left:".$note['xpos']."px;z-index:".$note['zIndex'].";":"z-index:".$note['zIndex'].";"?>" noteid="<?php echo $note['id']?>">
		<div class="top"><span id="close" class="<?php echo $note['id']?>"><img class='hidden' title="Remove" src="<?php echo base_url()?>/assets/images/cross.png"/></span></div>
		<div class="content"><?php echo $note['note']?></div>
		<div class="bot">
			<div id="footnote">
				<span id="postdate"><?php echo date("g:i a - D, M-j-y",strtotime($note['postDate']))?></span>
				<br/>
				<span id="postby"><?php echo $note['postBy']?></span>	
			</div>
		</div>
	</div>
	<?php endforeach;?>
<?php endif;?>		
</body>
</html> 