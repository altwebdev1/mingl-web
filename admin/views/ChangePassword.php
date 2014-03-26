<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
require_once('controllers/AdminController.php');
$adminLoginObj   =   new AdminController();
$class =  $msg  = '';
$display = 'none';
if(isset($_POST['change_password_submit']) && $_POST['change_password_submit'] == 'Submit')
{
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
    $md5Pass        =  $_POST['old_password'];
	$condition      =   " id  = '1' AND Password = '{$md5Pass}'";
    $result         =   $adminLoginObj->checkAdminLogin($condition);	
    if($result)
    {
        $updateString   =   " password  = '".$_POST['new_password']."'";
        $condition      =   " id = 1 ";
        $adminLoginObj->updateAdminDetails($updateString,$condition);
		$msg            = "Password updated successfully";
		$class          = "success_msg";
		$display        = "block";
	}
	else{
		$class    = "error_msg";
		$display  = "block";
		$msg      = "Invalid Old Password";
	}

}
commonHead(); ?>
<body>
	<table cellpadding="0" cellspacing="0" border="0" width="100%" align="center">
		<tr>
			<td align="center">
				<table cellpadding="0" cellspacing="0" border="0" width="95%" align="center">					
					<tr><td colspan="2" class="headermenu"><?php top_header(); ?></td></tr>
				    <tr>
						<td colspan="2">
						 	 <div class="left_menu sidebar-nav" style="float:left;"><?php side_bar()?></div>
							 <div id="content_3" class="content">
							 <div class="box-header"><h2><i style="margin:5px 0 0 0" class="icon_chgpass"></i>Change Password</h2>
						 	</div>
							<div align="center" style="margin-top:20px;">
								<div class="<?php  echo $class;  ?>"><span><?php if(isset($msg) && $msg != '') echo $msg;  ?></span></div>
							</div>
							  <form name="change_password_form" id="change_password_form" action="" method="post">
							  	
									<table align="center" cellpadding="0" cellspacing="0" border="0" class="form_page list headertable" width="100%">
									<tr><td align="center">
										<table cellpadding="0" cellspacing="0" align="center" border="0" width="75%">	
									<tr><td height="30"></td></tr>
									<tr>
										<td align="left" valign="top" width="12%" ><label>Old Password</label></td>
										<td align="center" valign="top" width="3%">:</td>
										<td align="left" width="" height="60" valign="top">											
											<input type="Password" class="input" name="old_password" id="old_password"  value="" >
										</td>
									</tr>
									<tr>
										<td align="left" valign="top" ><label>New Password</label></td>
										<td  align="center" valign="top">:</td>
										<td align="left"  height="60" valign="top">
											<input type="Password" class="input" name="new_password" id="new_password"  value="" >	
										</td>
									</tr>
									<tr>
										<td align="left"  valign="top"><label>Confirm Password</label></td>
										<td align="center" valign="top">:</td>
										<td align="left"  height="60" valign="top">
											<input type="Password" class="input" id="confirm_password" name="confirm_password"  value="" >
										</td>
									</tr>
									
									<tr>
										<td colspan="2"></td>
										<td align="left"><input type="submit" class="submit_button" name="change_password_submit" id="change_password_submit" value="Submit" title="Submit" alt="Submit">										
										</td>
									</tr>	
									<tr><td height="10"></td></tr>	
								</table>
								</table></td></tr>
							  </form>	
						  	</div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
<?php commonFooter(); ?>
</html>