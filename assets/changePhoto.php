<?php
set_time_limit(60);
$err = false;

if (isset($_FILES['image'])) {
	$ftmp = $_FILES['image']['tmp_name'];
	$oname = $_FILES['image']['name'];
	$div_id = $_POST['div_id'];

	$type = @explode('/', $_FILES['image']['type']);
	$type = isset($type[1]) ? $type[1] : '';
	
	$type = ($type != 'pjpeg') ? $type : 'jpeg';
	
	$img_types = array('jpg', 'jpeg', 'gif', 'png');
	
	if (in_array($type, $img_types)) {
		$file_temp_name = substr(md5(time() . $div_id), 0, 14) . 'n' . '.' . $type;
		
		$fname = "images/userphoto/temp/" . $file_temp_name;
		$afname = "images/userphoto/temp/" . $file_temp_name;
		
		if (move_uploaded_file($ftmp, $fname)){
echo "
<html>
	<head>
		<script language='javascript'>
			window.parent.setUploadedImage('{$afname}', '{$file_temp_name}', '{$div_id}');
		</script>
	</head>
</html>
";

			exit();
		}	
	}
	else {
		$err = true;
	}
}
?>
<html>
	<head>
    	<style type="text/css">
			body {
				margin: 0px;
				padding: 0px;
				color:#000;
			}
		</style>
	</head>
	<body>
		<?php
			if ($err) {
		?>
			<script language="javascript">
				window.parent.uploadError('<?=$div_id?>', '<?=$oname?>');
			</script>
		<?php
			}
		?>
		<form name="iform" action="" method="post" enctype="multipart/form-data">
			<div  style="font-family: tahoma;font-size:11px;color: #333333;font-weight: bold;display: block">Upload Photo:&nbsp;</div>
			<input style="width: 220px" id="file" type="file" name="image" onChange="window.parent.upload(this);" /><br>
            <span style="margin-left:83px ;font-size:11px; color:#666666;">only gif, png, jpg files.</span>
			<input type="hidden" value="" name="div_id" />
		</form>
        
	</body>
</html>