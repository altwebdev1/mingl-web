<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
require_once('controllers/AdminController.php');
$adminLoginObj   =   new AdminController();
$class =  $msg  = '';
$display = 'none';
$error = $msg = '';
$fields		  =	" * ";
$where		  =	" 1 ";
$user_details = $adminLoginObj->getAdminDetails($fields,$where);
if(isset($user_details) && is_array($user_details) && count($user_details)>0){
	foreach($user_details as $key => $value){
		$user_name 	= 	$value->UserName;
		$email		=	$value->EmailAddress;
		//$distance	=	$value->Distance;
	}
}
/*$fields		  =	" * ";
$where		  =	" 1 LIMIT 1 ";
$distance_details = $adminLoginObj->getDistance($fields,$where);
if(isset($distance_details) && is_array($distance_details) && count($distance_details)>0)
	$distance	=	$distance_details[0]->Distance;
*/
if(isset($_POST['general_settings_submit']) && $_POST['general_settings_submit'] != '' )
{	
	$updateString   =   " UserName  = '".$_POST['user_name']."',EmailAddress = '".$_POST['email']."'";//,Distance = '".$_POST['distance']."' ";
	$condition      =   " id = 1 ";
	$adminLoginObj->updateAdminDetails($updateString,$condition);
/*
	$updateString   =   " Distance = '".$_POST['distance']."' ";
	$condition      =   " id = 1 ";
	$adminLoginObj->updateDistanceDetails($updateString,$condition);
*/
	header('location:GeneralSettings?msg=1');
}
if(isset($_GET['msg']) && $_GET['msg'] != '')
	$msg = "General settings updated successfully";
commonHead(); ?>
<body>
	<table cellpadding="0" cellspacing="0" border="0" width="100%" align="center">
		<tr>
			<td align="center">
				<table cellpadding="0" cellspacing="0" border="0" width="95%" align="center">					
					<tr><td colspan="2" class="headermenu"><?php top_header(); ?></td></tr>				   
				    <tr>
						<td colspan="2" align="center">
						 	 <div class="left_menu sidebar-nav" style="float:left;"><?php side_bar()?></div>
							 <div id="content_3" class="content">
							 <div class="box-header"><h2><i class="icon_Gset"></i>General Settings</h2>
						 	</div>
							  	 <table align="center" cellpadding="0" cellspacing="0" border="0" width="100%" class="form_page list headertable">
								  <form name="general_settings_form" id="general_settings_form" action="" method="post">
								<tr><td align="center"><table cellpadding="0" cellspacing="0" align="center" border="0" width="75%">							 
									<tr><td  height="40" align="center" colspan="3">
									<?php if($msg !='') { ?><div class="success_msg" align="center"><span><?php echo $msg;?></span></div><?php  } ?>
									</td></tr>
									<tr><td height="10"></td></tr>
									<tr>
										<td align="left" width="12%" valign="top"><label>Username</label></td>
										<td align="center" class="" valign="top" width="3%">:</td>
										<td height="60" valign="top" align="left" >
											<input type="text" readonly="readonly" class="input" name="user_name" id="user_name" value="<?php  if(isset($user_name) && $user_name) echo $user_name  ?>" />
										</td>
									</tr>
									<tr>
										<td align="left" valign="top"><label>Email</label></td>
										<td class="" valign="top" align="center">:</td>
										<td align="left"  height="60" valign="top">
											<input type="text" class="input" name="email" id="email" value="<?php  if(isset($email) && $email) echo $email  ?>" />
										</td>
									</tr>
<!--									<tr>
										<td align="left" valign="top"><label>Distance</label></td>
										<td class="" valign="top" align="center">:</td>
										<td align="left"  height="60" valign="top">
											<input type="text" class="input" name="distance" id="distance" value="<php  if(isset($distance) && $distance) echo $distance  ?>" />
										</td>
									</tr>
-->
									<tr>
										<td colspan="2"></td>
										<td align="left">
											<input type="submit" class="submit_button" name="general_settings_submit" id="general_settings_submit" value="Submit" title="Submit" alt="Submit" />
										</td>
									</tr>
									</table></td></tr>
									<tr><td height="10"></td></tr>
								</table>
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