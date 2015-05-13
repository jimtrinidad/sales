<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" >
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title></title>


<link type="text/css" href="<?php echo base_url()?>assets/css/redmond/jquery-ui.css" rel="stylesheet" />
<link class="ui-theme" rel="Stylesheet" type="text/css" href="http://jqueryui.com/themeroller/css/parseTheme.css.php?ffDefault=Lucida+Grande,+Lucida+Sans,+Arial,+sans-serif&amp;fwDefault=bold&amp;fsDefault=1.1em&amp;cornerRadius=5px&amp;bgColorHeader=5c9ccc&amp;bgTextureHeader=12_gloss_wave.png&amp;bgImgOpacityHeader=55&amp;borderColorHeader=4297d7&amp;fcHeader=ffffff&amp;iconColorHeader=d8e7f3&amp;bgColorContent=fcfdfd&amp;bgTextureContent=06_inset_hard.png&amp;bgImgOpacityContent=100&amp;borderColorContent=a6c9e2&amp;fcContent=222222&amp;iconColorContent=469bdd&amp;bgColorDefault=dfeffc&amp;bgTextureDefault=02_glass.png&amp;bgImgOpacityDefault=85&amp;borderColorDefault=c5dbec&amp;fcDefault=2e6e9e&amp;iconColorDefault=6da8d5&amp;bgColorHover=d0e5f5&amp;bgTextureHover=02_glass.png&amp;bgImgOpacityHover=75&amp;borderColorHover=79b7e7&amp;fcHover=1d5987&amp;iconColorHover=217bc0&amp;bgColorActive=f5f8f9&amp;bgTextureActive=06_inset_hard.png&amp;bgImgOpacityActive=100&amp;borderColorActive=79b7e7&amp;fcActive=e17009&amp;iconColorActive=f9bd01&amp;bgColorHighlight=fbec88&amp;bgTextureHighlight=01_flat.png&amp;bgImgOpacityHighlight=55&amp;borderColorHighlight=fad42e&amp;fcHighlight=363636&amp;iconColorHighlight=2e83ff&amp;bgColorError=fef1ec&amp;bgTextureError=02_glass.png&amp;bgImgOpacityError=95&amp;borderColorError=cd0a0a&amp;fcError=cd0a0a&amp;iconColorError=cd0a0a&amp;bgColorOverlay=aaaaaa&amp;bgTextureOverlay=01_flat.png&amp;bgImgOpacityOverlay=0&amp;opacityOverlay=30&amp;bgColorShadow=aaaaaa&amp;bgTextureShadow=01_flat.png&amp;bgImgOpacityShadow=0&amp;opacityShadow=30&amp;thicknessShadow=8px&amp;offsetTopShadow=-8px&amp;offsetLeftShadow=-8px&amp;cornerRadiusShadow=8px" />
<style type="text/css">
body{font-size: 9px;font-family: 'tahoma'}
.runningStyles{background: #dadada;}
.alertBlock{color: #FF0000 !important;}
.tableList td{border-top: 1px solid #AAAAAA;vertical-align: top;}
</style>
</head>
<body>
	<?php if(isset($right) AND isset($left)):?>
	<div style="width: 50%;float: left">
		<?php foreach($right as $month):?>
		<div class="sidebar-container ui-widget-content" style="margin-bottom: 10px;margin-right:5px;">
			<div class="ui-widget-header widget-title" align="center" style="border:0;padding: 2px;">
			<?php echo $month['month']?>
			</div>
			<div class="sidebar-content widget-content">
				<table class="tableList" cellpadding="2" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th width="25%">Program</th>
							<th>Date</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($month['programs'] as $program):?>
						<tr class="<?php echo (strtotime(NOW) >= strtotime($program['start_date']) AND strtotime(NOW) <= strtotime($program['end_date'])) ? 'runningStyles' : ''?>">
							<td width="25%">																
								<a class="detailsProgramSchedule" id="<?php echo $program['schedule_id']?>"><b><?php echo $program['title'].' '.$program['batch'] ?></b></a>
							</td>
							<td style="border-left: 1px solid #AAAAAA;"><?php echo $program['sessions']?></td>
						</tr>
						<?php endforeach;?>
					</tbody>
				</table>
			</div>
		</div>
		<?php endforeach;?>
	</div>
	<div style="width:50%;float: left">
		<?php foreach($left as $month):?>
		<div class="sidebar-container ui-widget-content" style="margin-bottom: 10px;margin-left:5px;">
			<div class="ui-widget-header widget-title" align="center" style="border:0;padding: 2px;">
			<?php echo $month['month']?>
			</div>
			<div class="sidebar-content widget-content">
				<table class="tableList" cellpadding="2" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th width="25%">Program</th>
							<th>Date</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($month['programs'] as $program):?>
						<tr class="<?php echo (strtotime(NOW) >= strtotime($program['start_date']) AND strtotime(NOW) <= strtotime($program['end_date'])) ? 'runningStyles' : ''?>">
							<td width="25%">																
								<a class="detailsProgramSchedule" id="<?php echo $program['schedule_id']?>"><b><?php echo $program['title'].' '.$program['batch'] ?></b></a>
							</td>
							<td style="border-left: 1px solid #AAAAAA;"><?php echo $program['sessions']?></td>
						</tr>
						<?php endforeach;?>
					</tbody>
				</table>
			</div>
		</div>
		<?php endforeach;?>
	</div>
	<?php else:?>
	<div>
		<?php foreach($months as $month):?>
		<div class="sidebar-container ui-widget-content" style="margin-bottom: 10px;margin-left:5px;font-size: 11px;">
			<div class="ui-widget-header widget-title" align="center" style="border:0;padding: 2px;">
			<?php echo $month['month']?>
			</div>
			<div class="sidebar-content widget-content">
				<table class="tableList" cellpadding="2" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th width="30%">Program</th>
							<th>Date</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($month['programs'] as $program):?>
						<tr class="<?php echo (strtotime(NOW) >= strtotime($program['start_date']) AND strtotime(NOW) <= strtotime($program['end_date'])) ? 'runningStyles' : ''?>">
							<td width="30%">																
								<a class="detailsProgramSchedule" id="<?php echo $program['schedule_id']?>"><b><?php echo $program['title'].' '.$program['batch'] ?></b></a>
							</td>
							<td style="border-left: 1px solid #AAAAAA;"><?php echo $program['sessions']?></td>
						</tr>
						<?php endforeach;?>
					</tbody>
				</table>
			</div>
		</div>
		<?php endforeach;?>
	</div>	
	<?php endif;?>	
</body>
</html>									