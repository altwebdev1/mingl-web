<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/UserController.php');
$userObj   =   new UserController();
$display   =   'none';
$class  =  $msg    = $cover_path = '';
$updateStatus	=	1;
//if(isset($_SESSION['referPage']))
//	unset($_SESSION['referPage']);
//$_SESSION['referPage']	=	'UserList';
if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	unset($_SESSION['intermingl_sess_user_platform']);
	unset($_SESSION['intermingl_sess_user_name']);
	unset($_SESSION['intermingl_sess_email']);
	unset($_SESSION['intermingl_sess_user_status']);
	unset($_SESSION['intermingl_sess_location']);
	unset($_SESSION['intermingl_sess_user_registerdate']);
	if(isset($_SESSION['intermingl_ses_from_timeZone']))
		unset($_SESSION['intermingl_ses_from_timeZone']);
}
if(isset($_POST['Search']) && $_POST['Search'] != ''){
	destroyPagingControlsVariables();
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	if(isset($_POST['ses_username']))
		$_SESSION['intermingl_sess_user_name'] 	= $_POST['ses_username'];
	if(isset($_POST['ses_email']))
		$_SESSION['intermingl_sess_email']	    = $_POST['ses_email'];
	if(isset($_POST['ses_status']))
		$_SESSION['intermingl_sess_user_status']	= $_POST['ses_status'];
	if(isset($_POST['ses_date']) && $_POST['ses_date'] != ''){
		$validate_date = dateValidation($_POST['ses_date']);
		if($validate_date == 1){
			$date = date('Y-m-d',strtotime($_POST['ses_date']));
			if($date != '' && $date != '1970-01-01' && $date != '0000-00-00' )
				$_SESSION['intermingl_sess_user_registerdate']	= $date;
			else 
				$_SESSION['intermingl_sess_user_registerdate']	= '';
		}
		else 
			$_SESSION['intermingl_sess_user_registerdate']	= '';
	}
	else 
		$_SESSION['intermingl_sess_user_registerdate']	= '';
}
if(isset($_POST['do_action']) && $_POST['do_action'] != ''){
	if(isset($_POST['checkedrecords']) && is_array($_POST['checkedrecords']) && count($_POST['checkedrecords']) > 0	&&	isset($_POST['bulk_action']) && $_POST['bulk_action']!=''){
		$Ids	=	implode(',',$_POST['checkedrecords']);
		if($_POST['bulk_action']==1){
			$userIds	=	$Ids;
			$updateStatus	=	1;
		}
		else if($_POST['bulk_action']==2){
			$userIds	=	$Ids;
			$updateStatus	=	2;
		}
		else
			$delete_id = $Ids;
	}
}
if(isset($_GET['delId']) && $_GET['delId']!='')
	$delete_id      = $_GET['delId'];

if(isset($delete_id) && $delete_id != ''){	
	/*
	$messageResult = $messageObj->selectMessageDetails($delete_id);	
	if(isset($messageResult) && is_array($messageResult) && count($messageResult) > 0 ){
		foreach($messageResult As $key=>$value){			
			if($value->MessageType == 2 || $value->MessageType == 3){
				$unlinkPath = MESSAGE_IMAGE_PATH_REL.$value->Content;
				if ($_SERVER['HTTP_HOST'] == '172.21.4.104'){
					if(file_exists($unlinkPath))
						unlink($unlinkPath);
				}else{
					deleteImages(8,$unlinkPath);
				}
			}
		}
	}*/
	$userObj->deleteUserReleatedEntries($delete_id);
	$_SESSION['notification_msg_code']	=	3;
	header("location:UserList");
	die();
}
else if(isset($userIds) && $userIds != ''){	
	$userObj->changeUsersStatus($userIds,$updateStatus);
	$_SESSION['notification_msg_code']	=	4;
	header("location:UserList");
	die();
}
if(isset($_GET['editId']) && $_GET['editId']!=''	&& isset($_GET['status'])	&&	$_GET['status']!=''){
	$condition = " id = ".$_GET['editId'];
	$update_string = " Status = ".$_GET['status'];
	$userListResult  = $userObj->updateUserDetails($update_string,$condition);
	$_SESSION['notification_msg_code']	=	4;
	header("location:UserList");
	die();
}
//select u.id,u.FirstName,count(c.id)as cardCount from users as u Left JOIN cards as c ON(u.id=c.fkUserId)WHERE 1 and u.Status != '3' and u.Status != '4' group by u.id ORDER BY id desc 
setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$fields    = " u.*, count(c.id)as cardCount ";
$condition = " and u.Status != '3' and u.Status != '2' ";// AND c.Status!='3' ";
$condition = " and u.Status != '3' ";
$userListResult  = $userObj->getUserList($fields,$condition);
$tot_rec 		 = $userObj->getTotalRecordCount();
if($tot_rec!=0 && !is_array($userListResult)) {
	$_SESSION['curpage'] = 1;
	$userListResult  = $userObj->getUserList($fields,$condition);
}
/* Chat list for all user*/
$userIds = '';
/*
if(isset($userListResult) && is_array($userListResult) && count($userListResult) > 0 ){
	foreach($userListResult as $key=>$value){
		$userIds .= $value->id.',';
	}
	if($userIds != '')
	    $chatList = $messageObj->selectMessageDetails(rtrim($userIds,','));
	$fromIds = array();
	$toIds   = array();
	if(isset($chatList) && is_array($chatList) && count($chatList) >0){
		foreach($chatList as $key=>$value){
			$fromIds[] = $value->fromUserId;
			$toIds[]   = $value->toUserId; 
		}
	}
}

if( (isset($fromIds) && count($fromIds) > 0) || (isset($toIds) && count($toIds) > 0) ){
	$chatIds = array_unique(array_merge($fromIds,$toIds));
}
*/
/*if(isset($_GET['msg']) && $_GET['msg'] == 1){
	$msg 		= 	"User added successfully";
	$display	=	"block";
	$class 		= 	"success_msg";
}
else if(isset($_GET['msg']) && $_GET['msg'] == 2){
	$msg 		= 	"User updated successfully";
	$display	=	"block";
	$class 		= 	"success_msg";
}
else if(isset($_GET['msg']) && $_GET['msg'] == 3){
	$msg 		= 	"User deleted successfully";
	$display	=	"block";
	$class 		= 	"error_msg";
}
else if(isset($_GET['msg']) && $_GET['msg'] == 4){
	$msg 		= 	"Status changed successfully";
	$display	=	"block";
	$class 		= 	"success_msg";
}
else if(isset($_GET['msg']) && $_GET['msg'] == 5){
	$msg 		= 	"Message sent successfully";
	$display	=	"block";
	$class 		= 	"success_msg";
}*/
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
						 <div class="box-header"><h2><i class="icon_userlist"></i>User List</h2>
						 <span style="float:right"><a href="UserManage" title="Add User"><strong>+ Add User</strong></a></span></div>
				            <table cellpadding="0" cellspacing="0" border="0" width="98%" align="center" class="headertable">
								<tr><td height="20"></td></tr>
								<tr>
									<td colspan="2">
										<form name="search_category" action="UserList" method="post">
				                           <table align="center" cellpadding="0" cellspacing="0" border="0" class="filter_form" width="100%">									       
												<tr><td height="15"></td></tr>
												<tr>													
													<td width="7%" style="padding-left:20px;"><label>User</label></td>
													<td width="3%" align="center">:</td>
													<td align="left"  height="40">
														<input type="text" class="input" name="ses_username" id="ses_username"  value="<?php  if(isset($_SESSION['intermingl_sess_user_name']) && $_SESSION['intermingl_sess_user_name'] != '') echo unEscapeSpecialCharacters($_SESSION['intermingl_sess_user_name']);  ?>" >
				
													</td>
													<td width="10%" style="padding-left:20px;"><label>Email</label></td>
													<td width="3%" align="center">:</td>
													<td align="left"  height="40">
														<input type="text" class="input" id="ses_email" name="ses_email"  value="<?php  if(isset($_SESSION['intermingl_sess_email']) && $_SESSION['intermingl_sess_email'] != '') echo unEscapeSpecialCharacters($_SESSION['intermingl_sess_email']);  ?>" >
													</td>
												</tr>
												<tr><td height="10"></td></tr>
												<tr>
													<td width="10%" style="padding-left:20px;"><label>Status</label></td>
													<td width="3%" align="center">:</td>
													<td align="left"  height="40">
														<select name="ses_status" id="ses_status" tabindex="2" title="Select Status" style="width:40%;">
															<option value="">Select</option>
														<?php $i=1; 
																foreach($userStatus as $key => $user_status) { 
																	if($i<=2) {?>
															<option value="<?php echo $key; ?>" <?php  if(isset($_SESSION['intermingl_sess_user_status']) && $_SESSION['intermingl_sess_user_status'] != '' && $_SESSION['intermingl_sess_user_status'] == $key) echo 'Selected';  ?>><?php echo $user_status; ?></option>
														<?php 		} $i++; 
																}?>
														</select>
													</td>
													<td width="10%" style="padding-left:20px;" align="left"><label>Registered Date</label></td>
													<td width="3%" align="center">:</td>
													<td height="40" align="left" >
														<input style="width:90px" type="text" autocomplete="off"  maxlength="10" class="input" name="ses_date" id="ses_date" title="Select Date" value="<?php if(isset($_SESSION['intermingl_sess_user_registerdate']) && $_SESSION['intermingl_sess_user_registerdate'] != '') echo date('m/d/Y',strtotime($_SESSION['intermingl_sess_user_registerdate'])); else echo '';?>" > (mm/dd/yyyy)
													</td>																									
												</tr>
												<tr><td height="10"></td></tr>
												<tr>
													<td align="center" colspan="9" ><input type="submit" class="submit_button" name="Search" id="Search" value="Search"></td>
												</tr>
												<tr><td height="10"></td></tr>
											 </table>
										  </form>
									</td>
				               	</tr>
								<tr><td height="20"></td></tr>
								<tr>
									<td colspan="2">
										<table cellpadding="0"  cellspacing="0" border="0" align="center" width="100%">
											<tr>
												<?php if(isset($userListResult) && is_array($userListResult) && count($userListResult) > 0){ ?>
												<td align="left" width="20%">No. of User(s)&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
												<?php } ?>
												<td align="center">
														<?php if(is_array($userListResult) && count($userListResult) > 0 ) {
														 	pagingControlLatest($tot_rec,'UserList'); ?>
														<?php }?>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr><td height="10"></td></tr><!-- <div class="error_msg w50"><span>No User found.</span></div> -->
								<tr><td colspan= '2' align="center">
									<?php displayNotification(); ?>
<!--									
									<div class="<?php  //echo $class;  ?> w50"><span><?php //if(isset($msg) && $msg != '') echo $msg;  ?></span></div>
-->
									</td></tr>
								<tr><td height="10"></td></tr>
								<tr>
									<td colspan="2">
									<?php if(isset($userListResult) && is_array($userListResult) && count($userListResult) > 0 ) { ?>
									  <form action="UserList" class="l_form" name="UserListForm" id="UserListForm"  method="post"> 
										<!-- <input type="hidden" value="" id="message_hidden" name="message_hidden"/> -->
										<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table user_actions">
											<tr align="left">
												<th align="center" style="text-align:center" width="3%"><input onclick="checkAllRecords('UserListForm');" type="Checkbox" name="checkAll"/></th>
												<th align="center" width="3%">#</th>												
<!--												<th width="8%">Photo</th>		-->
												<th width="20%"><?php echo SortColumn('FirstName','User'); ?></th>
<!--
												<th width="10%"><?php //echo SortColumn('LastName','LastName'); ?></th>
-->
												<th width="10%"><?php echo SortColumn('Email','Email'); ?></th>
												<th width="18%">Social Networks</th>
												<th width="9%"><?php echo SortColumn('Location','Location'); ?></th>
												<th width="3%"><?php echo SortColumn('DateCreated','Registered Date'); ?></th>
											</tr>
											<?php foreach($userListResult as $key=>$value){
														$image_path = ADMIN_IMAGE_PATH.'no_user.jpeg';
														$original_path = ADMIN_IMAGE_PATH.'no_user.jpeg';
														$photo = $value->Photo;
														if(isset($photo) && $photo != ''){
															$user_image = $photo;		
															$image_path_rel = USER_THUMB_IMAGE_PATH_REL.$user_image;
															$original_path_rel = USER_IMAGE_PATH_REL.$user_image;
															if(SERVER){
																if(image_exists(1,$user_image)){
																	$image_path = USER_THUMB_IMAGE_PATH.$user_image;
																	$original_path = USER_IMAGE_PATH.$user_image;
																}
															
															}
															else if(file_exists($image_path_rel)){
																	$image_path = USER_THUMB_IMAGE_PATH.$user_image;
																	$original_path = USER_IMAGE_PATH.$user_image;
															}
														}
														$userName	=	'';
													if(isset($value->FirstName)	&&	isset($value->LastName)) 	
														$userName	=	ucfirst($value->FirstName).' '.ucfirst($value->LastName);
													else if(isset($value->FirstName))	
														$userName	=	 ucfirst($value->FirstName);
													else if(isset($value->LastName))	
														$userName	=	ucfirst($value->LastName);
													
											 ?>									
											<tr id="test_id_<?php echo $value->id;?>">
												<td valign="top" align="center"><input id="checkedrecords" name="checkedrecords[]" value="<?php  if(isset($value->id) && $value->id != '') echo $value->id  ?>" type="checkbox" hashCount="<?php if(isset($value->hash_count) && $value->hash_count > 0 ) echo $value->hash_count; ?>"/></td>
												<td valign="top" align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
												<td valign="top" align="center" >
													<div  style="<?php if(isset($cover_path) && $cover_path != '' ) { ?>
																			background: url('<?php echo $cover_path;?>') no-repeat;<?php 
																		} else { 
																			?>background: none no-repeat; 
																<?php 	} ?>;background-size:cover;float:left">
																<a <?php if(isset($original_path) && $original_path != ADMIN_IMAGE_PATH.'no_user.jpeg' ) { ?> href="<?php echo $original_path; ?>" class="user_image_pop_up"  <?php } ?> title="View Photo"  ><img width="36" height="36" src="<?php echo $image_path;?>" ></a>
													</div>	<!-- user profile photo end -->
													
													<div class="user_profile">
														<p align="left" style="padding-left:50px">
															<?php if(isset($userName) && $userName != '')	echo '<a class="recordView" href="UserDetail?viewId='.$value->id.'">'.trim($userName).'</a>'; else echo '-';?>
														</p>
<!--													<p><?php // if(isset($value->cardCount) && $value->cardCount > 1){ ?> 
														<a href="CardsList?viewCardUserId=<?php // if(isset($value->id) && $value->id != '') echo $value->id; ?>" title="view Cards "><?php // echo '<br>Cards : '.$value->cardCount.'<br>'; ?></a>
														<?php // }else echo '<p>Card : 1</p>';?>
													</p>
-->
														<div class="userAction"  align="left" style="padding-left:50px">
														<?php if(isset($value->Status)	&&	$value->Status == 1) { ?>			
																<a class="active_icon userIcon" alt=" Active" title="Active User" onclick="javascript:return confirm('Are you sure want to change the status?')" href="UserList?editId=<?php echo $value->id;?>&status=2"></a>
														<?php } else if(isset($value->Status)	&&	$value->Status == 2){ ?>
																<a class="inactive_icon userIcon"  title="Inactive User" alt="Inactive User" onclick="javascript:return confirm('Are you sure want to change the status?')" href="UserList?editId=<?php echo $value->id;?>&status=1"></a>
														<?php } else if(isset($value->Status)	&&	$value->Status == 4){	?>
																<a class="incomplete_icon userIcon" alt=" Active" title="Profile incomplete"></a>
														<?php }	?>
															<a href="UserManage?editId=<?php if(isset($value->id) && $value->id != '') echo $value->id; ?>" title="Edit" alt="Edit" class="editUser">Edit</a>
															<a href="UserDetail?viewId=<?php if(isset($value->id) && $value->id != '') echo $value->id; ?>" title="View" alt="View" class="viewUser">View</a>
															<a onclick="javascript:return confirm('Are you sure to delete?')" href="UserList?delId=<?php if(isset($value->id) && $value->id != '') echo $value->id;?>" title="Delete" alt="Delete" class="deleteUser">Delete</a>
														</div>	<!-- user action end -->
													</div>	<!-- user profile end -->
												</td>

												<td valign="top"><?php if(isset($value->Email) && $value->Email != '' ){ echo $value->Email;}else echo '-';?></td>
												<td valign="top"><?php 
														if(isset($value->FacebookId) && $value->FacebookId != '')
															echo '<p><b>Facebook <span style="padding-left:12px;">:</span> </b>'.$value->FacebookId.'</p>'; 
														if(isset($value->LinkedInId) && $value->LinkedInId != '')
															echo '<p ><b>LinkedIn <span style="padding-left:19px;">:</span> </b>'.$value->LinkedInId.'</p>';
														if(isset($value->TwitterId) && $value->TwitterId != '')
															 echo '<p><b>Twitter <span style="padding-left:30px;">:</span> </b>'.$value->TwitterId.'</p>'; 
														if(isset($value->GooglePlusId) && $value->GooglePlusId != '')
															echo '<p ><b>GooglePlus <span style="padding-left:1px;">:</span> </b>'.$value->GooglePlusId.'</p>';
														if(isset($value->FacebookId)	&&	$value->FacebookId	==""	&&	isset($value->LinkedInId)	&&	$value->LinkedInId ==""	&&	isset($value->TwitterId)	&&	$value->TwitterId ==""	&&	isset($value->GooglePlusId)	&&	$value->GooglePlusId =="")
															echo " - "; 
													?>
												</td>

												<td valign="top"><?php if(isset($value->Location) && $value->Location != ''){ echo $value->Location; }else echo '-';?></td>	

												<td valign="top"><?php if(isset($value->DateCreated) && $value->DateCreated != '0000-00-00 00:00:00'){ echo date('m/d/Y',strtotime($value->DateCreated)); }else echo '-';?></td>
											</tr>
											<?php } ?> 																		
										</table>
										<?php if(isset($userListResult) && is_array($userListResult) && count($userListResult) > 0){ 
												bulk_action($statusArray);
										?>
<!--										<table border="0" cellpadding="0" cellspacing="0"  class="">
											<tr><td height="10"></td></tr>
											<tr align="">
												<td align="left" style="padding-top: 7px;">
														<select name="bulk_action" id="bulk_action" tabindex="4" title="Select Action" >
															<option value="">Bulk Actions</option>
															<?php //foreach($statusArray as $key => $action) { ?>
															<option value="<?php //echo $key; ?>"><?php //echo $action; ?></option>
																	<?php //}?>
														</select>
												</td>
												<td align="left" style="padding-left:20px;">
													<input type="submit" onclick="return deleteAll('Users');" class="submit_button" name="Delete" id="Delete" value="Apply" title="Apply" alt="Apply">&nbsp;&nbsp;													
												</td>
											</tr>
											<tr><td height="10"></td></tr>
										</table>
-->
										<?php } ?>
										</form>
										<?php } else { ?>	
											<tr>
												<td colspan="16" align="center" style="color:red;">No User found</td>
											</tr>
											<tr><td height="30"></td></tr>
										<?php } ?>
									</td>
								</tr>
				            </table>
				        </div>
				     </td></tr>
				</table>
			</td>
		</tr>
		<tr><td height="20"></td></tr>
	</table>
</body>
<?php commonFooter(); ?>
<script type="text/javascript">
$(".user_image_pop_up").colorbox({title:true});
$("#ses_date").datepicker({
	showButtonPanel	:	true,        
    buttonText		:	'',
    buttonImageOnly	:	true,
    buttonImage		:	path+'webresources/images/calender.png',
    dateFormat		:	'mm/dd/yy',
	changeMonth		:	true,
	changeYear		:	true,
	hideIfNoPrevNext:	true,
	showWeek		:	true,
	yearRange		:	"c-30:c",
	maxDate			:	"0",
	closeText		:   "Close"
   });
   $(document).ready(function() {		
		$(".interest_pop_up").colorbox(
			{
				iframe:true,
				width:"50%", 
				height:"80%",
				title:true,
		});
	});	
	
	
jQuery(function() {
	jQuery("div.userAction a").hide();
	jQuery('table.user_actions tr[id^=test_id_]').hover(function() {
		jQuery(this).find("div.userAction a").css("display","inline-block");
	   
    }, function() {
        jQuery(this).find("div.userAction a").hide();
    });
});
	
	
</script>
</html>
