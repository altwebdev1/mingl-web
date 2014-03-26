<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
require_once('controllers/AdminController.php');
$msg = '';
$adminLoginObj   =   new AdminController();
$fields = '*';
$where  = '1';
$static_details  = $adminLoginObj->getCMS($fields,$where);
if(isset($_POST['cms_submit']) && $_POST['cms_submit'] == 'Submit' ){
		$_POST          =   unEscapeSpecialCharacters($_POST);
   		$_POST          =   escapeSpecialCharacters($_POST);
		$updateString   =   " Content  = '".$_POST['cms_about']."' ,DateModified = '".date('Y-m-d H:i:s')."'";
		$condition      =   " id = 1 ";
		$adminLoginObj->updateCMSDetails($updateString,$condition);
		$updateString   =   " Content  = '".$_POST['cms_privacy']."' ,DateModified = '".date('Y-m-d H:i:s')."'";
		$condition      =   " id = 2 ";
		$adminLoginObj->updateCMSDetails($updateString,$condition);
		$updateString   =   " Content  = '".$_POST['cms_terms']."' ,DateModified = '".date('Y-m-d H:i:s')."'";
		$condition      =   " id = 3 ";
		$adminLoginObj->updateCMSDetails($updateString,$condition);
	/*	$updateString   =   " Content  = '".$_POST['cms_faq']."' ";
		$condition      =   " id = 4 ";
		$adminLoginObj->updateCMSDetails($updateString,$condition);
	*/	
		header('location:StaticPages?msg=1');
}
if(isset($_GET['msg']) && $_GET['msg'] != '')
	$msg = "CMS updated successfully";
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
							 <div class="box-header"><h2><i class="icon-edit" style="padding-right:3px;margin:3px 0 0 0"></i> CMS </h2>
						 	</div>
							  <form name="cms_form" id="cms_form" action="" method="post">
							  	 <table align="center" cellpadding="0" cellspacing="0" border="0" width="100%" class="form_page list headertable">
									<tr><td height="10"></td></tr>
										<tr>
											<td align="center">
												<table cellpadding="0" cellspacing="0" border="0" width="75%" align="center">
													<tr><td  height="40" align="center" colspan="3">
													<?php if($msg !='') { ?><div class="success_msg" align="center"><span><?php echo $msg;?></span></div><?php  } ?>
													</td></tr>
													<tr><td height="20"></td></tr>
													<?php if(isset($static_details) && is_array($static_details) && count($static_details)>0 ) { ?>
													<tr>
														<td align="left" width="12%" valign="top"><label><?php if(isset($static_details[0]->PageName) && $static_details[0]->PageName != '' ) echo $static_details[0]->PageName;?></label></td>
														<td  width="3%" align="center" class="" valign="top">:</td>
														<td height="60" valign="top" align="left"><textarea  class="add_cms" name="cms_about" id="cms_about" rows="15" cols="80"><?php if(isset($static_details[0]->Content) && $static_details[0]->Content != '' ) echo $static_details[0]->Content;?></textarea></td>
													</tr>
													<tr><td height="20"></td></tr>	
													<tr>
														<td align="left"  valign="top"><label><?php if(isset($static_details[1]->PageName) && $static_details[1]->PageName != '' ) echo $static_details[1]->PageName;?></label></td>
														<td align="center" class="" valign="top">:</td>
														<td height="60" valign="top" align="left"><textarea  class="add_cms" name="cms_privacy" id="cms_privacy" rows="15" cols="80"><?php if(isset($static_details[1]->Content) && $static_details[1]->Content != '' ) echo $static_details[1]->Content;?></textarea></td>
													</tr>
													<tr><td height="20"></td></tr>									
													<tr>
													<tr>
														<td align="left"  valign="top"><label><?php if(isset($static_details[2]->PageName) && $static_details[2]->PageName != '' ) echo $static_details[2]->PageName;?></label></td>
														<td align="center" class="" valign="top">:</td>
														<td height="60" valign="top" align="left"><textarea  class="add_cms" name="cms_terms" id="cms_terms" rows="15" cols="80"><?php if(isset($static_details[2]->Content) && $static_details[2]->Content != '' ) echo $static_details[2]->Content;?></textarea></td>
													</tr>
												<!--<tr><td height="20"></td></tr>	
													<tr>
														<td align="left"  valign="top"><label><?php // if(isset($static_details[3]->PageName) && $static_details[3]->PageName != '' ) echo strtoupper($static_details[3]->PageName);?></label></td>
														<td align="center" class="" valign="top">:</td>
														<td height="60" valign="top" align="left"><textarea  class="add_cms" name="cms_faq" id="cms_faq" rows="2" cols="80"><?php //if(isset($static_details[3]->Content) && $static_details[3]->Content != '' ) echo $static_details[3]->Content;?></textarea></td>
													</tr>	-->
													<tr><td height="20"></td></tr>								
													<tr>
														<td colspan="2"></td>
														<td align="left">
															<input type="submit" class="submit_button" name="cms_submit" id="cms_submit" value="Submit" title="Submit" alt="Submit" />
														</td>
													</tr>		
													<?php  } else { ?>
													<tr><td align="center" colspan="3">
													<div class="error_msg" align="center"><span><?php echo "No Static Content Found";?></span></div>
													</td></tr>
													<?php } ?>
													<tr><td height="10"></td></tr>
											</table>
										</td>
									</tr>					  
								</table>
							  </form>	
						  	</div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td height="20"></td></tr>
	</table>
</body>
<?php commonFooter(); ?>
</html>