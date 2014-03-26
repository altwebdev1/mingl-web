<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/UserController.php');
require_once('controllers/ManagementController.php');
$interestObj   =   new ManagementController();
$userObj   =   new userController();
$original_image_path =  $original_cover_image_path = $actualPassword = '';
$interestStatus		 =	$tagStatus				=	0;
//unset($_SESSION['broadtags_sess_dap_first_name']);
if(isset($_GET['referList']) && $_GET['referList'] == 1 ){
	unset($_SESSION['intermingl_sess_user_platform']);
	unset($_SESSION['intermingl_sess_user_name']);
	unset($_SESSION['intermingl_sess_email']);
	unset($_SESSION['intermingl_sess_user_status']);
	unset($_SESSION['intermingl_sess_location']);
	unset($_SESSION['intermingl_sess_user_registerdate']);
}
if(isset($_GET['viewId']) && $_GET['viewId'] != '' ){
	$condition       = "  AND user.Id = ".$_GET['viewId']." and user.Status in (1,2,4) LIMIT 1 ";
	//$condition       = " user.Id = ".$_GET['viewId']." and user.Status in (1,2) LIMIT 1 ";
	//$field			= " user.UserName,user.Name,user.Email,user.FBId,user.TwitterId,user.Photo,user.CoverPhoto,user.Location,ct.ContactId,htp.fkHashTagId,ht.hashTagName,user.DateCreated,user.ActualPassword,user.EmailNotification ";
	//$field				=	' user.*,count(ui.Status) as interestCount ';
	$field				=	' user.*,c.id as cardId,c.Phone,count(ui.Status) as interestCount ';
	$userDetailsResult  = $userObj->getUserHashDetails($field,$condition);
	
	if(isset($userDetailsResult) && is_array($userDetailsResult) && count($userDetailsResult) > 0){
		$userId					=	$userDetailsResult[0]->id;
		$firstname       		=	$userDetailsResult[0]->FirstName;
		$lastname				=	$userDetailsResult[0]->LastName;
		$email      			= 	$userDetailsResult[0]->Email;
		$fbId       			= 	$userDetailsResult[0]->FacebookId;
		$linkedInId  			= 	$userDetailsResult[0]->LinkedInId;
		$twitterId				=	$userDetailsResult[0]->TwitterId;
		$googleId				=	$userDetailsResult[0]->GooglePlusId;
		$location  				= 	$userDetailsResult[0]->Location;
		$dateCreated    		= 	$userDetailsResult[0]->DateCreated;
		$emailnotify   			= 	$userDetailsResult[0]->EmailNotification;
		$interestStatus			=	$userDetailsResult[0]->interestCount;
		$company				=	$userDetailsResult[0]->Company;
		$title					=	$userDetailsResult[0]->Title;
		$phone					=	$userDetailsResult[0]->Phone;
		$user_id				=	$userDetailsResult[0]->id;
		//$fields         		= 	" count(ut.Status) as userTagCount ";
		$TagResult 				= 	$interestObj->countUserTags($user_id);
		$tagStatus				=	0;
		if(isset($TagResult) && count($TagResult) > 0){
			$tagStatus			=	$TagResult[0]->userTagCount;
		}
		//$_SESSION['broadtags_sess_hash_user_name'] = $firstname;
		if(isset($userDetailsResult[0]->Photo) && $userDetailsResult[0]->Photo != ''){
			$user_image = $userDetailsResult[0]->Photo;
			if(image_exists(3,$user_image))
				$original_image_path = USER_IMAGE_PATH.$user_image;
			else
				$original_image_path = '';			
			if(image_exists(1,$user_image)){
				$image_path = USER_THUMB_IMAGE_PATH.$user_image;
			}
			else
				$image_path = ADMIN_IMAGE_PATH.'no_user.jpeg';
		}
		else
			$image_path = ADMIN_IMAGE_PATH.'no_user.jpeg';
	/*	if(isset($userDetailsResult[0]->CoverPhoto) && $userDetailsResult[0]->CoverPhoto != ''){
			$cover_image = $userDetailsResult[0]->CoverPhoto;
			if(image_exists(5,$cover_image))
				$original_cover_image_path = COVER_IMAGE_PATH.$cover_image;
			else
				$original_cover_image_path = '';
			if(image_exists(4,$cover_image)){
				$cover_path = COVER_THUMB_IMAGE_PATH.$cover_image;
			}else
				$cover_path = ADMIN_IMAGE_PATH.'no_cover_image.jpg';
		}
		else
			$cover_path = ADMIN_IMAGE_PATH.'no_cover_image.jpg';
	*/
	}	
}

?>
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
							 <div class="box-header"><h2><i class="icon_adduser"></i>View User</h2></div>
						  		 <table align="center" cellpadding="0" cellspacing="0" border="0" class="list headertable" width="100%">							        
									<tr><td height="20"></td></tr>
									<tr>
										<td align="center">
											<table cellpadding="0" cellspacing="0" align="center" border="0" width="75%">
<!--
												<tr>
													<td width="25%" align="left" valign="top"><label>Username</label></td>
													<td width="3%" align="center" valign="top">:</td>
													<td align="left" width="25%" valign="top"><?php //if(isset($username) && $username !='') echo $username; else echo '-'; ?></td>
													<td  align="left"  valign="top" width="16%"><label>Password</label></td>
													<td align="center" width="3%" valign="top">:</td>
													<td align="left"  valign="top"><?php //if(isset($actualPassword) && $actualPassword != '' ) echo $actualPassword; else echo '-'; ?></td>							
												</tr>									
-->
												<tr><td height="20"></td></tr>
												<tr>
													<td align="left" valign="top" width="15%"><label>Firstname</label></td>
													<td align="center" valign="top" width="3%">:</td>
													<td align="left" valign="top"><?php if(isset($firstname) && $firstname != '') echo $firstname; else echo '-'; ?></td>										
													<td align="left" valign="top" width="15%"><label>LastName</label></td>
													<td align="center" valign="top" width="3%">:</td>										
													<td align="left" valign="top"><?php if(isset($lastname) && $lastname != '') echo $lastname;  else echo '-'; ?></td>										
												</tr>
												<tr><td height="20"></td></tr>
												<tr>
													<td align="left" valign="top" ><label>Email</label></td>
													<td align="center" valign="top">:</td>										
													<td align="left" valign="top"><?php if(isset($email) && $email != '') echo $email;  else echo '-'; ?></td>										
													<td align="left" valign="top"><label>Title</label></td>
													<td align="center" valign="top">:</td>
													<td align="left" valign="top" ><?php if(isset($title) && $title != '') echo $title;  ?></td>
												</tr>
												<tr><td height="20"></td></tr>
												<tr>
													<td align="left" valign="top"><label>Company</label></td>
													<td align="center" valign="top">:</td>
													<td align="left" valign="top" ><?php if(isset($company) && $company != '') echo $company;  ?></td>
													<td align="left" valign="top" ><label>Phone</label></td>
													<td align="center" valign="top">:</td>
													<td align="left" valign="top"><?php  if(isset($phone) && $phone != '' ) echo $phone;   ?></td>
												</tr>
												<tr><td height="20"></td></tr>
												
												<tr>
													<td align="left" valign="top"><label>Location</label></td>
													<td align="center" valign="top">:</td>
													<td align="left" valign="top"><?php if(isset($location) && $location != '0') echo $location; else echo '-'; ?></td>										
													<td align="left" valign="top"><label>Facebook Id</label></td>
													<td align="center" valign="top">:</td>
													<td align="left" valign="top" ><?php  if(isset($fbId) && $fbId != '' ) echo $fbId; else echo '-';  ?></td>
													
												</tr>	
												<tr><td height="20"></td></tr>
												<tr>
													<td align="left" valign="top" ><label>LinkedIn Id</label></td>
													<td align="center" valign="top">:</td>
													<td align="left" valign="top"><?php  if(isset($linkedInId) && $linkedInId != '' ) echo $linkedInId; else echo '-'; ?></td>
													<td align="left" valign="top"><label>Twitter Id</label></td>
													<td align="center" valign="top">:</td>
													<td align="left" valign="top" ><?php  if(isset($twitterId) && $twitterId != '' ) echo $twitterId; else echo '-';  ?></td>
													
												</tr>	
												
									<!--			<tr><td height="20"></td></tr>	
												<tr>
													<td align="left"  valign="top"><label>Location</label></td>
													<td align="center" valign="top">:</td>
													<td align="left"  valign="top"><?php // if(isset($location) && $location != '' ) echo $location; else echo '-'; ?></td>
													<td  align="left"  valign="top"><label>Registered Date</label></td>
													<td align="center" valign="top">:</td>
													<td align="left"  valign="top"><?php // if(isset($dateCreated) && $dateCreated != '' ) echo date('m/d/Y',strtotime($dateCreated)); else echo '-'; ?></td>
													
												</tr>	
										-->		
												<tr><td height="20"></td></tr>	
												<tr>
													<td align="left" valign="top" ><label>GooglePlus Id</label></td>
													<td align="center" valign="top">:</td>
													<td align="left" valign="top"><?php  if(isset($googleId) && $googleId != '' ) echo $googleId; else echo '-'; ?></td>
													<td align="left" valign="top"><label>Photo</label></td>
													<td align="center" valign="top">:</td>
													<td align="left" valign="top"><a href="<?php if(isset($original_image_path) && $original_image_path != '') { echo $original_image_path; ?>" class="user_photo_pop_up"<?php } else { ?>Javascript:void(0);<?php } ?>" title="Click here" alt="Click here" ><?php if(isset($image_path) && $image_path != '') { ?> <img width="75" height="75" src="<?php echo $image_path;?>"><?php } ?></a></td>
													
												</tr>
												<tr>
													<td  align="left" valign="top"><label>Registered Date</label></td>
													<td align="center" valign="top">:</td>
													<td align="left"  height="90" valign="top"><?php if(isset($dateCreated) && $dateCreated != '' ) echo date('m/d/Y',strtotime($dateCreated)); else echo '-'; ?></a></td>
												</tr>	

													<tr><td height="20"></td></tr>
											</table>
										</td>
									</tr>
									<tr><td ><h2>Notification Settings</h2></td></td></tr>
									<tr><td height="20"></td></tr>
									<tr>
										<td align="center">
											<table cellpadding="0" cellspacing="0" align="center" border="0" width="75%">
												<tr>
													<td  align="left" width="4%" valign="top"><label class="notification">Email Notifications</label></td>
													<td width="3%" align="center" valign="top">:</td>
													<td align="left" width="35%" valign="top"><?php if(isset($emailnotify) && $emailnotify == '1' ) echo 'On'; else echo 'Off'; ?></td>
												</tr>								
												<tr><td height="20"></td></tr>
											</table>
										</td>
									</tr>
									<tr><td height="20"></td></tr>														
<!--									<tr>										
										<td colspan="6" align="center">
											<?php //if(isset($postDetail) && $postDetail!=''){ ?><a href="PostDetail?uid=<?php //echo $_GET['viewId']; ?>" class="submit_button hashtag_pop_up" name="HashtagPostList" id="HashtagPostList" title="View Hashtag Post" alt="View Hashtag Post">Post</a>&nbsp;&nbsp;<?php //} ?>
											<?php //if(isset($hashTagDetail) && $hashTagDetail!=''){ ?><a href="HashTagDetail?uid=<?php //echo $_GET['viewId']; ?>" class="submit_button hashtag_pop_up" name="HashtagList" id="HashtagList" title="View Hashtag List" alt="View Hashtag List">Hashtag</a>&nbsp;&nbsp;<?php //} ?>
											<?php //if(isset($userCreatedHashtag) && $userCreatedHashtag!=''){ ?><a href="HashTagDetail?uid=<?php //echo $_GET['viewId']; ?>&user=1" class="submit_button hashtag_pop_up" name="HashtagList" id="HashtagList" title="View Hashtag List" alt="View Hashtag List">Created Hashtag</a>&nbsp;&nbsp;<?php //} ?>
											<?php //if(isset($contactDetail) && $contactDetail!=''){ ?><a href="ContactDetail?uid=<?php //echo $_GET['viewId']; ?>" class="submit_button hashtag_pop_up" name="ContactList" id="ContactList" title="View Contact List" alt="View Contact List">Contact</a>&nbsp;&nbsp;<?php //} ?>
										</td>
									</tr>
									<tr><td height="20"></td></tr>
-->
									<tr>										
										<td colspan="3" align="center">
											<?php if(isset($interestStatus) && $interestStatus > 0){?>
											<a href="UserInterest?uid=<?php if(isset($userId) && $userId != '') echo $userId; ?>" class="interest_pop_up cboxElement submit_button"  alt="Interest" title="Interest" >Interest</a>&nbsp;&nbsp;&nbsp;
											<?php }?>
											<?php if(isset($tagStatus) && $tagStatus > 0){?>
											<a href="UserTags?uid=<?php if(isset($userId) && $userId != '') echo $userId; ?>" class="interest_pop_up cboxElement submit_button"  alt="Tag" title="Tag" >Tag</a>
											<?php } ?>
										</td>
									</tr>
									<tr><td height="20"></td></tr>
									<tr>										
										<td colspan="6" align="center">		
											<?php /*if( (isset($_GET['act']) && $_GET['act'] != '') && ( isset($_GET['userId']) && $_GET['userId'] != '' ) ) 
													$href_page = "Activity?viewId=".$_GET['userId'];
												  else if(isset($_GET['post']) && $_GET['post'] != '') 
													$href_page = "HashTagPostDetail?viewId=".$_GET['post'];
												  else if( isset($_GET['hashId']) && $_GET['hashId'] != '') 
													$href_page = "HashTagListDetail?viewId=".$_GET['hashId'];
												  else
												  	*/
												$href_page = "UserList";
											?>
												
											<a href="UserManage?editId=<?php if(isset($_GET['viewId']) && $_GET['viewId'] != '') echo $_GET['viewId']; ?>" title="Edit" alt="Edit" class="submit_button">Edit</a>			
											<?php if(isset($_GET['referList'])	&&	$_GET['referList']==1	&&	isset($_SESSION['referPage'])	&&	$_SESSION['referPage']!=''){?>
											<a href="<?php echo $_SESSION['referPage'];?>" class="submit_button referpage" name="Back" id="Back" title="Back" alt="Back" >Back </a>
											<?php 	unset($_SESSION['referPage']);
												  } else {?>
											<a href="<?php if(isset($href_page) && $href_page != '' ) echo $href_page; else echo 'UserList';?>" class="submit_button" name="Back" id="Back" title="Back" alt="Back" >Back </a>
											<?php } ?>
										</td>
									</tr>		
									<tr><td height="10"></td></tr>						   
								</table>
						  	</div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
<?php commonFooter(); ?>
<script type="text/javascript">	
	$(document).ready(function() {		
		$(".interest_pop_up").colorbox(
			{
				iframe:true,
				width:"50%", 
				height:"80%",
				title:true,
		});
		$(".user_photo_pop_up").colorbox({title:true});
	});	
	
</script>
</html>
