<?php
require_once('includes/CommonIncludes.php');
$error = $msg = '';
require_once('controllers/AdminController.php');
$adminLoginObj   =   new AdminController();
$error = $msg = '';
if(isset($_POST['forget_password_submit']) && $_POST['forget_password_submit'] == 'Submit')
{
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
    $_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
    $condition  	= " EmailAddress = '{$_POST['email']}'";
    $login_result 	= $adminLoginObj->checkAdminLogin($condition);
    if($login_result){		
		$mailContentArray['name'] 		= $login_result[0]->UserName;
		$mailContentArray['toemail'] 	= $login_result[0]->EmailAddress;
		$mailContentArray['password'] 	= $login_result[0]->Password;
		$mailContentArray['subject'] 	= 'Forget Password Mail';
		$mailContentArray['userType']	= 'Admin';
		$mailContentArray['from'] 		= $login_result[0]->EmailAddress;
		$mailContentArray['fileName']	= 'adminForgotPasswordMail.html';
		sendMail($mailContentArray,'3');
		$msg = "Login information has been sent to your mail"; 
	}
	else{
		$error = "Invalid Email Address ";
	}
}
commonHead();?>
<body onload="fieldfocus('email');">
	<div id="login_form">
		<table align="center" cellpadding="0" cellspacing="0" border="0" height="100%" width="100%" >
			<tr>
				<td valign="middle" align="center" height="100%">
					<form action="" class="l_form" name="forget_password_form" id="forget_password_form"  method="post">
						<div class="login">
						<table align="center" cellpadding="0" cellspacing="0" border="0" width="450">
							<tr><td colspan="3" height="5"></td></tr>
							<tr>
								<td colspan="3" align="center" class="login_logo">
									<a><strong>Mingl</strong></a>
								</td>
							</tr>
							<tr><td height="10"></td></tr>
							<tr><td align="center" colspan="3"><h2 style="font-size:20px;">Forgot Password</h2></td></tr>
							<tr><td height="10"></td></tr>
							<tr>
							<td colspan="3" align="center" height="20" valign="top">
							<?php if($error !='') { ?><div class="error_msg" align="center"><span><?php echo $error;?></span></div><?php  } ?>
							<?php if($msg !='') { ?><div class="success_msg" align="center"><span><?php echo $msg;?></span></div><?php  } ?>
							</td></tr>
							<tr><td colspan="3" height="20"></td></tr>
							<tr>
								<td width="25%" valign="top" align="left" style="padding-left:50px;"><label>Email</label></td>
								<td width="5%" align="center" valign="top" class="colon">:</td>
								<td  valign="top" align="left" height="50">
									<input type="text" class="input" name="email" id="email" value="" />
								</td>
							</tr>
							<tr>
								<td colspan="2"></td>
								<td align="left">
									<input type="submit" class="submit_button" title="Submit" alt="Submit" name="forget_password_submit" id="forget_password_submit" value="Submit" />&nbsp;&nbsp;&nbsp;<a href="Login" class="submit_button" name="Back" id="Back" value="Back">Back </a>
								</td>
							</tr>
							<tr><td height="10"></td></tr>
							
							<tr><td height="30"></td></tr>
						</table>
					</div>
					</form>
				</td>
			</tr>
		</table>
	</div>
</body>
<?php commonFooter(); ?>
</html>

