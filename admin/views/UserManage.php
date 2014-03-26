<?php 
require_once('includes/CommonIncludes.php');
//require_once('includes/php_image_magician.php');
admin_login_check();
commonHead();
require_once('controllers/UserController.php');
$userObj   =   new UserController();
require_once('controllers/AdminController.php');
$adminLoginObj   =   new AdminController();
require_once('controllers/CardController.php');
$cardsObj   =   new CardController();
require_once("includes/phmagick.php");
$field_focus	=	'username';
$class=$ExistCondition=$location='';
$photoUpdateString='';
$email_exists = $facebookid_exist	=	$linkedid_exist	=	$twitter_exist	=	$googleplus_exist	=	0;
$userName_exists  = 0;
$msg	=	'';
if(isset($_GET['editId']) && $_GET['editId'] != '' ){
	$condition       = " id = ".$_GET['editId']." and Status in (1,2,4)";
	$field			 = " * ";
	//$condition     = " id = ".$_GET['editId']." and Status in (1,2)";
	$userDetailsResult  = $userObj->selectUserDetails($field,$condition);
	if(isset($userDetailsResult) && is_array($userDetailsResult) && count($userDetailsResult) > 0){
		$firstname 	=	$userDetailsResult[0]->FirstName;
		$lastname	=	$userDetailsResult[0]->LastName;
		$email      =	$userDetailsResult[0]->Email;
		$fbId       =	$userDetailsResult[0]->FacebookId;
		$linkedInId =	$userDetailsResult[0]->LinkedInId;
		$twitterId	=	$userDetailsResult[0]->TwitterId;
		$googleId	=	$userDetailsResult[0]->GooglePlusId;
		$location   =	$userDetailsResult[0]->Location;	
		$emailnotify=	$userDetailsResult[0]->EmailNotification;
		$company	=	$userDetailsResult[0]->Company;
		$title		=	$userDetailsResult[0]->Title;
		$cardFields	=	" Summary, id as cardId,Phone ";
		$cardCondition	=	' fkUsersId = '.$_GET['editId'].' AND Card = "1" LIMIT 1';
		$cardDetails	=	$cardsObj->selectCardDetails($cardFields,$cardCondition);
		if(isset($cardDetails)	&&	is_array($cardDetails)	&&	count($cardDetails)>0)	{
			$summary	=	$cardDetails[0]->Summary;
			$cardId		=	$cardDetails[0]->cardId;
			$phone		=	$cardDetails[0]->Phone;
		}
//		echo '******************<pre>'; print_r($cardDetails); echo '</pre>';
	//	$interest	=	$userDetailsResult[0]->Interest;
	//	$summary	=	$userDetailsResult[0]->Summary;

		if(isset($userDetailsResult[0]->Photo) && $userDetailsResult[0]->Photo != ''){
			$user_image_name = $userDetailsResult[0]->Photo;
			if(image_exists(3,$user_image_name))
				$original_image_path = USER_IMAGE_PATH.$user_image_name;
			else
				$original_image_path = '';	
			if(image_exists(1,$user_image_name)){
				$user_image_path = USER_THUMB_IMAGE_PATH.$user_image_name;
			}
		}
	}
}
if(isset($_POST['submit'])	&&	$_POST['submit']!="")
{
	$_POST          	=   unEscapeSpecialCharacters($_POST);
	$_POST         		=   escapeSpecialCharacters($_POST);
	$ip_address     	=   ipAddress();
	//if($_SERVER['REMOTE_ADDR']	==	'172.4.21.140')
	//echo "<pre>";print_r($_POST);echo "</pre>";

	if($_POST['email'] != '')
		$ExistCondition .= " ( Email = '".$_POST['email']."' ";
	if($_POST['fbid'] != '')
		$ExistCondition  .= " or FacebookId = '".$_POST['fbid']."' ";	
	if($_POST['linkedid'] != '')
		$ExistCondition  .= " or LinkedInId = '".$_POST['linkedid']."' ";
	if($_POST['twitterid'] != '')
		$ExistCondition  .= " or TwitterId = '".$_POST['twitterid']."' ";
	if($_POST['googleid'] != '')
		$ExistCondition  .= " or GooglePlusId = '".$_POST['googleid']."' ";
	if($_POST['submit'] == 'Save')
		$id_exists = ") and id != '".$_POST['user_id']."' and Status in (1,2,4) ";
	else
		$id_exists = " ) and Status in (1,2,4) ";
		
		$firstname 	=	stripslashes($_POST['firstname']);
		$lastname	=	stripslashes($_POST['lastname']);
		$email      =	stripslashes($_POST['email']);
		$location   =	stripslashes($_POST['location']);
		$title  	=	stripslashes($_POST['title']);
		$company   	=	stripslashes($_POST['company']);
		$phone   	=	stripslashes($_POST['phone']);
	//	$summary	=	$_POST['summary'];	
		$fbId		=	stripslashes($_POST['fbid']);
		$linkedInId	=	stripslashes($_POST['linkedid']);
		$twitterId	=	stripslashes($_POST['twitterid']);
		$googleId	=	stripslashes($_POST['googleid']);
		$summary	=	stripslashes($_POST['summary']);
		
	$field = " * ";	
	$ExistCondition .= $id_exists;
	$alreadyExist   = $userObj->selectUserDetails($field,$ExistCondition);	
	if(isset($alreadyExist) && is_array($alreadyExist) && count($alreadyExist) > 0)	{
		if(($alreadyExist[0]->Email == $_POST['email']) && ($_POST['email'] != ''))
			$email_exists 			=	1;
		if(($alreadyExist[0]->FacebookId == $_POST['fbid']) && ($_POST['fbid'] != ''))	
			$facebookid_exist		=	1;
		if(($alreadyExist[0]->LinkedInId == $_POST['linkedid']) && ($_POST['linkedid'] != ''))
			$linkedid_exist			=	1;
		if(($alreadyExist[0]->TwitterId == $_POST['twitterid']) && ($_POST['twitterid'] != ''))
			$twitter_exist			=	1;
		if(($alreadyExist[0]->GooglePlusId == $_POST['googleid']) && ($_POST['googleid'] != ''))
			$googleplus_exist			=	1;
	}

	if($email_exists != '1' && $facebookid_exist != '1'	&&	$linkedid_exist!='1'	&&	$twitter_exist	!='1'	&&	$googleplus_exist	!=	'1'	)	{
		if($_POST['submit'] == 'Save'){		
			if(isset($_POST['user_id']) && $_POST['user_id'] != ''){
				$fields    = "FirstName             =	'".$_POST['firstname']."',
								LastName 			=	'".$_POST['lastname']."',
								Email 				=	'".$_POST['email']."',
								Location			=	'".$_POST['location']."',
								Title				=	'".$_POST['title']."',
								Company				=	'".$_POST['company']."',
								IpAddress 			=	'".$ip_address."',
								FacebookId			=	'".$_POST['fbid']."',
								LinkedInId			=	'".$_POST['linkedid']."',
								TwitterId			=	'".$_POST['twitterid']."',
								GooglePlusId		=	'".$_POST['googleid']."',
								EmailNotification 	=	'".$_POST['emailnotify']."',
								DateModified		=	'".date('Y-m-d H:i:s')."'";
				$condition		=	' id = '.$_POST['user_id'];
				$update_string	=	" Summary		=	'".$_POST['summary']."',Phone	=	'".$_POST['phone']."' ";
				if(isset($_POST['card_id'])	&&	$_POST['card_id']	!='')
					$cardCondition	=	' id = '.$_POST['card_id'].' ';
				//$cardCondition	=	' fkUsersId = '.$_POST['user_id'].' AND Card = "1" LIMIT 1';
				$userObj->updateUserDetails($fields,$condition);
				$cardsObj->updateCardDetails($update_string,$cardCondition);
				$insert_id = $_POST['user_id'];
				if (isset($_POST['user_photo_upload']) && !empty($_POST['user_photo_upload'])) {
					if(isset($_POST['name_user_photo']) && $_POST['name_user_photo'] != ''){
						$image_path = $_POST['name_user_photo'];
						if(image_exists(3,$image_path))
							unlink(USER_IMAGE_PATH_REL . $image_path);
						if(image_exists(1,$image_path))
							unlink(USER_THUMB_IMAGE_PATH_REL . $image_path);
						if(image_exists(2,$image_path))
							unlink(USER_SMALL_THUMB_IMAGE_PATH_REL . $image_path);
					}
				}
			//$msg = 2;
			$_SESSION['notification_msg_code']	=	2;
			}
		}
		if($_POST['submit']=='Add')	{
			$_POST['ipaddress'] =	$ip_address;
		//	die();
			$insert_id   		=	$userObj->insertUserDetails($_POST);
			$wordResult 	    =	$userObj->selectWordDetails();	
			$word               =	$wordResult[0]->Words;
			$numeric            =	'1234567890';
			$numbers            =	substr(str_shuffle($numeric), 0, 3);
			$actualPassword     =	trim($word.$numbers).$insert_id;
			$password			=	sha1($actualPassword.ENCRYPTSALT);
			$updateString 		=	" Password = '" . $password . "',ActualPassword = '". $actualPassword . "' ";
			$condition 			=	"id = ".$insert_id;

			$cardSummary		=	'';
			if(isset($_POST['summary'])	&&	$_POST['summary']	!='')
				$cardSummary	=	trim($_POST['summary']);
			$insertString		=	" fkUsersId	=	".$insert_id.", Summary	='".$cardSummary."', Phone	=	'".trim($_POST['phone'])."',";
			$cardsObj->insertCardDetails($insertString);
			$userObj->updateUserDetails($updateString,$condition);
			$date_now = date('Y-m-d H:i:s');
			$fields 	= '*';
			$condition 	= ' 1';
			$login_result 					=	$adminLoginObj->getAdminDetails($fields,$condition);
			$mailContentArray['name'] 		=	$_POST['firstname']." ".$_POST['lastname'];
			//$mailContentArray['userName'] 	=	$_POST['username'];
			$mailContentArray['toemail'] 	=	$_POST['email'];
			$mailContentArray['password'] 	=	$actualPassword;
			$mailContentArray['subject'] 	=	'Registration';
			$mailContentArray['userType']	=	'User';
			$mailContentArray['from'] 		=	$login_result[0]->EmailAddress;
			$mailContentArray['fileName']	=	'registration.html';
			sendMail($mailContentArray,'2');
			//$msg = '1&cs=1';
			$_SESSION['notification_msg_code']	=	1;
		}
			if(isset($insert_id)	&&	$insert_id!='')	{
				if (isset($_POST['user_photo_upload']) && !empty($_POST['user_photo_upload'])) {
					$imageName 				= $insert_id . '_' .time() . '.png';
					$temp_image_path 		= TEMP_USER_IMAGE_PATH_REL . $_POST['user_photo_upload'];
					$image_path 			= UPLOAD_USER_PATH_REL . $imageName;
					$imageThumbPath     	= UPLOAD_USER_THUMB_PATH_REL.$imageName;
					$imageSmallThumbPath    = UPLOAD_USER_SMALL_THUMB_PATH_REL.$imageName;
					$oldUserName			= $_POST['name_user_photo'];
					if ( !file_exists(UPLOAD_USER_PATH_REL) ){
						mkdir (UPLOAD_USER_PATH_REL, 0777);
					}
					if ( !file_exists(UPLOAD_USER_THUMB_PATH_REL) ){
						mkdir (UPLOAD_USER_THUMB_PATH_REL, 0777);
					}
					if ( !file_exists(UPLOAD_USER_SMALL_THUMB_PATH_REL) ){
						mkdir (UPLOAD_USER_SMALL_THUMB_PATH_REL, 0777);
					}
					copy($temp_image_path,$image_path);
					
					$phMagick = new phMagick($image_path);
					$phMagick->setDestination($imageSmallThumbPath)->resize(70,70);
					
					$phMagick = new phMagick($image_path);
					$phMagick->setDestination($imageThumbPath)->resize(100,100);
					
					//imagethumb_new($image_path,$imageThumbPath,'','',100,100);
					/*if ($_SERVER['HTTP_HOST'] != '172.21.4.104'){
						if($oldUserName!='') {
							if(image_exists(1,$oldUserName)) {
								deleteImages(1,$oldUserName);
							}
							if(image_exists(2,$oldUserName)) {
								deleteImages(2,$oldUserName);
							}
							if(image_exists(3,$oldUserName)) {
								deleteImages(3,$oldUserName);
							}
						}
						
						uploadImageToS3($imageThumbPath,1,$imageName);					
						uploadImageToS3($imageSmallThumbPath,2,$imageName);
						uploadImageToS3($image_path,3,$imageName);
						unlink($image_path);
						unlink($imageThumbPath);
						unlink($imageSmallThumbPath);
					}*/
					$photoUpdateString	.= " Photo = '" . $imageName . "'";
					unlink(TEMP_USER_IMAGE_PATH_REL . $_POST['user_photo_upload']);
				}
				if($photoUpdateString!='')
				{
					$condition 			= "id = ".$insert_id;
					$userObj->updateUserDetails($photoUpdateString,$condition);
				}
			}
			header("location:UserList");
			die();
		}	//	End of Already Exist condition
	else	{
		if($email_exists == 1){
			$error_msg   = "Email address already exists";
			$field_focus = 'email';
		}
		else if($facebookid_exist	==	1	)	{
			$error_msg   = "Facebook Id already exists";
			$field_focus = 'fbid';
		}
		else if($linkedid_exist	==	1	)	{
			$error_msg   = "LinkedIn Id already exists";
			$field_focus = 'linkedid';
		}
		else if ($userName_exists == 1){
			$error_msg   = "Username already exists";
			$field_focus = 'username';
		}
		else if ($twitter_exist == 1){
			$error_msg   = "Twitter Id already exists";
			$field_focus = 'twitterid';
		}
		else if ($googleplus_exist == 1){
			$error_msg   = "GooglPlus Id already exists";
			$field_focus = 'googleid';
		}
		$display = "block";
		$class   = "error_msg";
	}	
}
?>
<body onload="return fieldfocus('<?php echo $field_focus; ?>');">
	<table cellpadding="0" cellspacing="0" border="0" width="100%" align="center">
		<tr>
			<td align="center">
				<table cellpadding="0" cellspacing="0" border="0" width="95%" align="center">					
					<tr><td colspan="2" class="headermenu"><?php top_header(); ?></td></tr>
				    <tr>
						<td colspan="2">
						 	 <div class="left_menu sidebar-nav" style="float:left;"><?php side_bar()?></div>
							 <div id="content_3" class="content">
							 <div class="box-header"><h2><i class="icon_adduser"></i><?php if(isset($_GET['editId']) && $_GET['editId'] != '' ) echo "Edit "; else echo 'Add ';?>User</h2>
						 	</div>
							  <form name="add_user_form" id="add_user_form" action="" method="post">
							  <input type="Hidden" name="user_id" id="user_id" value="<?php if(isset($_GET['editId']) && $_GET['editId'] != '' ) echo $_GET['editId'];?>">
							  <input type="Hidden" name="card_id" id="card_id" value="<?php if(isset($cardId) && $cardId != '' ) echo $cardId;?>">
						  		 <table align="center" cellpadding="0" cellspacing="0" border="0" class="form_page list headertable" width="100%">
									<tr><td align="center">
									<table cellpadding="0" cellspacing="0" align="center" border="0" width="75%">
									    <tr> <td height="20"> </td></tr>
										<tr><td colspan="6" align="center"><div class="<?php echo $class;  ?> w50"><span><?php if(isset($error_msg) && $error_msg != '') echo $error_msg;  ?></span></div></td></tr>
										<tr><td height="20"></td></tr>
				
										<tr>
											<td  height="50" width="15%" align="left"  valign="top"><label>First Name&nbsp;<span class="required_field">*</span></label></td>
											<td width="3%" align="center"  valign="top">:</td>
											<td align="left"  height="40"  valign="top">
												<input type="text" class="input" name="firstname" id="firstname" maxlength="50" value="<?php if(isset($firstname) && $firstname != '') echo $firstname;  ?>" >
											</td>
											<td height="50" align="left" valign="top"><label>Last Name&nbsp;<span class="required_field">*</span></label></td>
											<td  align="center"  valign="top">:</td>
											<td align="left"  height="40"  valign="top">
												<input type="text" class="input" id="lastname" name="lastname" maxlength="30" value="<?php if(isset($lastname) && $lastname != '' ) echo $lastname;  ?>" >
											</td>
										</tr>						
										<tr>
											<td height="50" align="left"  valign="top"><label>Email&nbsp;<span class="required_field">*</span></label></td>
											<td width="3%" align="center"  valign="top">:</td>
											<td align="left"  height="40"  valign="top">
												<input type="text" class="input" id="email" name="email" maxlength="90" value="<?php if(isset($email) && $email != '') echo $email;  ?>" >
											</td>
											<td height="50" align="left"  valign="top"><label>Title&nbsp;<span class="required_field"></span></label></td>
											<td width="3%" align="center"  valign="top">:</td>
											<td align="left"  height="40"  valign="top">
												<input type="text" class="input" id="title" name="title" maxlength="90" value="<?php if(isset($title) && $title != '') echo $title;  ?>" >
											</td>
										</tr>
										<tr>
											<td height="50" align="left"  valign="top"><label>Company</label></td>
											<td width="3%" align="center"  valign="top">:</td>
											<td align="left"  height="40"  valign="top">
												<input type="text" class="input" id="company" name="company" value="<?php if(isset($company) && $company != '') echo $company;  ?>" >
											</td>
											<td height="50" align="left"  valign="top"><label>Phone</label></td>
											<td width="3%" align="center"  valign="top">:</td>
											<td align="left"  height="40"  valign="top">
												<input type="text" class="input" name="phone" id="phone" onkeypress="return isNumberKey_Phone(event);" maxlength="15" value="<?php  if(isset($phone) && $phone != '' ) echo $phone;   ?>">
												<br><span class="error_empty" id="phone_error"></span>
											</td>
										</tr>
										<tr>
											<td height="50" align="left"  valign="top"><label>Location</label></td>
											<td width="3%" align="center"  valign="top">:</td>
											<td align="left"  height="40"  valign="top">
												<input type="text" class="input" id="location" name="location" value="<?php if(isset($location) && $location != '') echo $location;  ?>" >
											</td>
											<td height="50" align="left"  valign="top"><label>Facebook Id</label></td>
											<td width="3%" align="center"  valign="top">:</td>
											<td align="left"  height="40"  valign="top">
												<input type="text" class="input" name="fbid" id="fbid" maxlength="90" value="<?php  if(isset($fbId) && $fbId != '' ) echo $fbId;   ?>">
											</td>
										</tr>
										<?php // if(isset($_GET['editId']) && $_GET['editId'] != '' ) {?>
										<tr>
											<td height="50" align="left"  valign="top"><label>LinkedIn Id</label></td>
											<td width="3%" align="center"  valign="top">:</td>
											<td align="left"  height="40"  valign="top">
												<input type="text" class="input" name="linkedid" id="linkedid" maxlength="90" value="<?php  if(isset($linkedInId) && $linkedInId != '' ) echo $linkedInId;   ?>">
											</td>
											<td height="50" align="left"  valign="top"><label>Twitter Id</label></td>
											<td width="3%" align="center"  valign="top">:</td>
											<td align="left"  height="40"  valign="top">
												<input type="text" class="input" name="twitterid" id="twitterid" maxlength="90" value="<?php  if(isset($twitterId) && $twitterId != '' ) echo $twitterId;  ?>">
											</td>
										</tr>
										<tr>
											<td height="50" align="left"  valign="top"><label>GooglePlus Id</label></td>
											<td width="3%" align="center"  valign="top">:</td>
											<td align="left"  height="40"  valign="top">
												<input type="text" class="input" name="googleid" id="googleid" maxlength="90" value="<?php  if(isset($googleId) && $googleId != '' ) echo $googleId;  ?>">
											</td>
											<td height="60"  align="left"  valign="top"  valign="top"><label>Photo</label></td>
											<td  align="center" valign="top">:</td>
											<td align="left"  height="60" valign="top">
												<div class="upload fleft">
												<div style="clear: both;float: left"> <input type="file"  name="user_photo" id="user_photo" title="User Photo" onclick="" onchange="return ajaxAdminFileUploadProcess('user_photo');"  /> </div><!-- imageValidation('empty_cat_sel_photo'); -->
												<div style="width:230px;">(Minimum dimension 100x100)</div>
												<span class="error" for="empty_user_photo" generated="true" style="display: none">User Image is required</span>

											</div>
											<?php  if(isset($_POST['user_photo_upload']) && $_POST['user_photo_upload'] != ''){  ?><input type="Hidden" name="user_photo_upload" id="user_photo_upload" value="<?php  echo $_POST['user_photo_upload'];  ?>"><?php  }  ?>
											<input type="Hidden" name="empty_user_photo" id="empty_user_photo" value="<?php  if(isset($user_image_name) && $user_image_name != '') { echo $user_image_name; }  ?>" />
											<input type="Hidden" name="name_user_photo" id="name_user_photo" value="<?php  if(isset($user_image_name) && $user_image_name != '') { echo $user_image_name; }  ?>" />
											</td>
										</tr>
										<?php // }?>
										<tr>
											<td height="60"  align="left" valign="top"><label>Summary</label></td>
											<td  align="center" valign="top">:</td>
											<td align="left"  height="60" valign="top">
												<textarea style="width:74%;height:80px" name="summary" id="summay"><?php if(isset($summary)	&&	$summary!="") { echo $summary; }else echo "";?></textarea>
											</td>
											<td height="60"  align="left" valign="top"><label></label></td>
											<td  align="center" valign="top"></td>
											<td align="left"  height="60" valign="top">
												<div class="fakefile_photo" style="float: left;clear: both;margin-top: 5px">
													<div id="user_photo_img">
														<?php  if(isset($user_image_path) && $user_image_path != ''){  ?>
															<a href="<?php if(isset($original_image_path) && $original_image_path != '') { echo $original_image_path; ?>" class="user_photo_pop_up"<?php } else { ?>Javascript:void(0);<?php } ?>" title="Click here" alt="Click here" ><img src="<?php  echo $user_image_path;  ?>" width="75" height="75" alt="Image"/></a>
														<?php  }  ?>
													</div>
												</div>
											</td>
										</tr>
										<tr><td height="20"></td></tr>
										<tr>
											

										</tr>
					<!--
										<tr>
											<td  height="50" width="15%" align="left"  valign="top"><label>Company</label></td>
											<td width="3%" align="center"  valign="top">:</td>
											<td align="left"  height="40"  valign="top">
												<input type="text" class="input" name="company" id="company" maxlength="100" value="<?php //if(isset($name) && $name != '') echo $name;  ?>" >
											</td>
											<td height="50" align="left" valign="top"><label>Title</label></td>
											<td  align="center"  valign="top">:</td>
											<td align="left"  height="40"  valign="top">
												<input type="text" class="input" id="title" name="title" maxlength="20" value="<?php //if(isset($fbId) && $fbId != '' ) echo $fbId;  ?>" >
											</td>
										</tr>
										<tr>
											<td  height="50" width="15%" align="left"  valign="top"><label>Location</label></td>
											<td width="3%" align="center"  valign="top">:</td>
											<td align="left"  height="40"  valign="top">
												<input type="text" class="input" name="location" id="location" maxlength="100" value="<?php //if(isset($name) && $name != '') echo $name;  ?>" >
											</td>
											<td height="50" align="left" valign="top"><label>Phone</label></td>
											<td  align="center"  valign="top">:</td>
											<td align="left"  height="40"  valign="top">
												<input type="text" class="input" id="Phone" name="Phone" maxlength="20" value="<?php //if(isset($fbId) && $fbId != '' ) echo $fbId;  ?>" >
											</td>
										</tr>			-->				
									</table>
									</td></tr>
									<?php if(isset($_GET['editId']) && $_GET['editId'] != ''){ ?>
									<tr><td colspan="6"><h2>Notification Settings</h2></td></td></tr>
									<tr><td height="20"></td></tr>
									<tr>
									<td align="center">
									<table cellpadding="0" cellspacing="0" align="center" border="0" width="75%">
									<tr>
										<td height="50" width="4%"  align="left" valign="top"><label class="notification">Email Notification</label><br></td>
										<td  align="center"  width="3%" valign="top">:</td>
										<td align="left"  width="35%" height="60"  valign="top" >
											<label><input type="Radio" value="1"  id="emailnotify"  name="emailnotify" <?php if(isset($emailnotify) && $emailnotify == '1') echo 'checked';?> > &nbsp;&nbsp;On</label>&nbsp;&nbsp;&nbsp;&nbsp;<label><input type="Radio" value="0" id="emailnotify" name="emailnotify" <?php if(isset($emailnotify) && $emailnotify == '0') echo 'checked';?> > &nbsp;&nbsp;Off</label>
										</td>
									</tr>
								<!--		<td width="15%" height="50" align="left" valign="top"><label>Hashtag private</label></td>
										<td width="3%"  align="center"  valign="top">:</td>
										<td align="left"  height="60"  valign="top">
											<label><input type="Radio" value="1"  id="hashtag_private" name="hashtag_private" <?php // if(isset($hashtag_private) && $hashtag_private == '1') echo 'checked';?>>&nbsp;&nbsp;On</label>&nbsp;&nbsp;&nbsp;&nbsp;<label><input type="Radio" value="0" id="hashtag_private" name="hashtag_private" <?php // if(isset($hashtag_private) && $hashtag_private == '0') echo 'checked';?>>	&nbsp;&nbsp;Off</label>										
										</td>			
									</tr>
									<tr><td height="20"></td></tr>
									<tr>
										<td height="50"  align="left" valign="top"><label>Contact can chat</label></td>
										<td  align="center"  valign="top">:</td>
										<td align="left"  height="60"  valign="top">
											<label><input type="Radio" value="1" id="contact_can_chat" name="contact_can_chat" <?php // if(isset($contact_can_chat) && $contact_can_chat == '1') echo 'checked';?>>&nbsp;&nbsp;On</label>&nbsp;&nbsp;&nbsp;&nbsp;<label><input type="Radio" value="0" id="contact_can_chat" name="contact_can_chat" <?php // if(isset($contact_can_chat) && $contact_can_chat == '0') echo 'checked';?>>&nbsp;&nbsp;Off</label>
										</td>												
									</tr> -->	
									</table></td></tr>
									<?php  } ?>
									<tr><td height="20"></td></tr>
									<tr>
										<td colspan="6" align="center">
										<?php if(isset($_GET['editId']) && $_GET['editId'] != ''){ ?>
											<input type="submit" class="submit_button" name="submit" id="submit" value="Save" title="Save" alt="Save">
										<?php } else { ?>
										<input type="submit" class="submit_button" name="submit" id="submit"  value="Add" title="Add" alt="Add">
										<?php } ?>
										<a href="UserList"  class="submit_button" name="Back" id="Back"  value="Back" title="Back" alt="Back">Back </a>
									</td>
								</tr>	
									<tr><td height="10"></td></tr>				  
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
<script type="text/javascript">
$(".user_photo_pop_up").colorbox({title:true});
</script>
</html>