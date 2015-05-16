<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title><?php echo $title?></title>
<script src="<?php echo base_url()?>assets/js/jquery-1.6.2.min.js" type="text/javascript" charset="utf-8"></script>
<link type="text/css" href="<?php echo base_url()?>assets/css/styles.css" rel="stylesheet" />
<?php echo isset($alert)?$alert:""?>
<script type="text/javascript">
$(document).ready(function(){
	$("#username").focus();
	$("#username").click(function(){
		$(".errormsg").slideUp('fast');
	});
	$(".errormsg").delay(800).slideUp(500);
});
</script>
</head>
<body>
	<div id="login-container">
		<div class="img"><img src="<?php echo base_url()?>assets/images/solidground-logo.png" alt="" width="230px" height="60px"/></div>
		<div id="login-main-box">
			<div id="login-box">
			<h1><img src="<?php echo base_url()?>assets/images/login.png"/>Sales Panel Login </h1>
				<?php if(isset($error_msg)):?>
				<div class="errormsg" align="center">
					<img src="<?php echo base_url()?>assets/images/alert.png"/>
					<span><?php echo $error_msg?></span>
				</div>
				<?php endif;?>
				<form action="<?php echo site_url('user/login')?>" method="post">
				<ul>
					<li>
						<label for="username">Username:</label>
						<div class="box"><input name="username" id="username" type="text" value="" /></div>
					</li>
					<li>
		
						<label for="password">&nbsp;Password:</label>
						<div class="box"><input name="password" id="password" type="password"/> </div>
					</li>
					
					<li class="rem">
						<input type="checkbox" value="1" name="rememberme" id="rememberme"/> <label for="rememberme">Keep me logged in</label>
						<div class="loginSubmit"><input name="login" type="submit" value="" /></div>
					</li>
					
					<li id="submit"></li>
				</ul>
				</form>
		
			
			</div>
		</div>
	</div>
</body>
</html>
