<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/UserController.php');
$userObj   =   new UserController();
$display   =   'none';
$class  =  $msg    = $cover_path = '';
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
if(isset($_POST['Delete']) && $_POST['Delete'] != ''){
	if(isset($_POST['checkdelete']) && is_array($_POST['checkdelete']) && count($_POST['checkdelete']) > 0)
		$delete_id = implode(',',$_POST['checkdelete']);
}
if(isset($_GET['delId']) && $_GET['delId']!=''){
	$delete_id      = $_GET['delId'];
}


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
	header("location:UserList?msg=3");	
}	




if(isset($_GET['editId']) && $_GET['editId']!=''){
	$condition = " id = ".$_GET['editId'];
	$update_string = " Status = ".$_GET['status'];
	$userListResult  = $userObj->updateUserDetails($update_string,$condition);
	header("location:UserList?msg=4");
}
//select u.id,u.FirstName,count(c.id)as cardCount from users as u Left JOIN cards as c ON(u.id=c.fkUserId)WHERE 1 and u.Status != '3' and u.Status != '4' group by u.id ORDER BY id desc 
setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$fields    = " u.*, count(c.id)as cardCount ";
$condition = " and u.Status != '3' and u.Status != '4' ";// AND c.Status!='3' ";
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
if(isset($_GET['msg']) && $_GET['msg'] == 1){
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
}
?>
<body>
	<table cellpadding="0" cellspacing="0" border="0" width="100%" align="center">
		<tr>
			<td align="center">
				<table cellpadding="0" cellspacing="0" border="0" width="95%" align="center">					
					<tr><td colspan="2" class="headermenu"><?php top_header(); ?></td></tr>
				    <tr><td colspan="2">
				         <div id="content_3" class="content">
				            <table cellpadding="0" cellspacing="0" border="0" width="100%" align="center" class="headertable">
				                <tr><td height="10%"><span><h2>User List</h2></span></td><td align="right"><span><a href="UserManage" title="Add User"> + Add User</a></span></td></tr>
								<tr><td height="20"></td></tr>
								<tr>
				                    <td valign="top" align="center" colspan="2">
										
										 <form name="search_category" action="UserList" method="post">
				                           <table align="center" cellpadding="0" cellspacing="0" border="0" class="filter_form" width="100%">									       
												<tr><td height="15"></td></tr>
												<tr>													
													<td width="7%" style="padding-left:20px;"><label>Username</label></td>
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
															<?php $i=1; foreach($userStatus as $key => $user_status) { 
																			if($i<=2) {?>
															<option value="<?php echo $key; ?>" <?php  if(isset($_SESSION['intermingl_sess_user_status']) && $_SESSION['intermingl_sess_user_status'] != '' && $_SESSION['intermingl_sess_user_status'] == $key) echo 'Selected';  ?>><?php echo $user_status; ?></option>
																	<?php 	} $i++;
																		}?>
														</select>

<!--														<select name="ses_status" id="ses_status" style="width:40%;">
																<option value="">Select</option>
																<option value="1" <?php  if(isset($_SESSION['intermingl_sess_user_status']) && $_SESSION['intermingl_sess_user_status'] != '' && $_SESSION['intermingl_sess_user_status'] == '1') echo 'Selected';  ?> >Active</option>
																<option value="2" <?php  if(isset($_SESSION['intermingl_sess_user_status']) && $_SESSION['intermingl_sess_user_status'] != '' && $_SESSION['intermingl_sess_user_status'] == '2') echo 'Selected';  ?>>Inactive</option>
															</select>
-->
													</td>
													<td width="10%" style="padding-left:30px;" align="left"><label>Registered Date</label></td>
													<td width="3%" align="center">:</td>
													<td height="40" align="left" >
														<input style="width:90px" type="text"  maxlength="10" class="input" name="ses_date" id="ses_date" title="Select Date" value="<?php if(isset($_SESSION['intermingl_sess_user_registerdate']) && $_SESSION['intermingl_sess_user_registerdate'] != '') echo date('m/d/Y',strtotime($_SESSION['intermingl_sess_user_registerdate'])); else echo '';?>" > (mm/dd/yyyy)
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
								<tr><td colspan= '2' align="center"><div class="<?php  echo $class;  ?> w50"><span><?php if(isset($msg) && $msg != '') echo $msg;  ?></span></div></td></tr>
								<tr><td height="10"></td></tr>
								<tr>
									<td colspan="2">
									  <form action="UserList" class="l_form" name="UserListForm" id="UserListForm"  method="post"> 
										<!-- <input type="hidden" value="" id="message_hidden" name="message_hidden"/> -->
										<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table">
											<tr align="left">
												<th align="center" width="3%"><input onclick="checkAllDelete('UserListForm');" type="Checkbox" name="checkAll"/></th>
												<th align="center" width="3%">#</th>												
												<th width="8%">Photo</th>
												<th width="18%"><?php echo SortColumn('FirstName','User Name'); ?></th>
<!--
												<th width="10%"><?php //echo SortColumn('LastName','LastName'); ?></th>
-->
												<th width="20%"><?php echo SortColumn('Email','Email'); ?></th>
												<th width="10%">Social Networks</th>
<!--
												<th width="10%"><?php //echo SortColumn('FBId','Facebook Id'); ?></th>
												<th width="10%"><?php //echo SortColumn('TwitterId','Twitter Id'); ?></th>
												<th width="8%">Photo</th>												
-->
												<th width="13%"><?php echo SortColumn('Location','Location'); ?></th>
<!--
												<th width="5%">Card(s)</th>
-->
												<th width="5%"><?php echo SortColumn('DateCreated','Registered Date'); ?></th>
												<th colspan="6" width="15%" align="center">Action</th>												
											</tr>
											<?php if(isset($userListResult) && is_array($userListResult) && count($userListResult) > 0 ) { 
													foreach($userListResult as $key=>$value){
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
													if(isset($value->FirstName)	&&	isset($value->FirstName)) 	$userName	=	ucfirst($value->FirstName).' '.ucfirst($value->LastName);
													else if(isset($value->LastName))	$userName	=	ucfirst($value->LastName);
													else if(isset($value->FirstName))	$userName	=	 ucfirst($value->FirstName);
											 ?>									
											<tr>
												<td align="center"><input id="checkdelete" name="checkdelete[]" value="<?php  if(isset($value->id) && $value->id != '') echo $value->id  ?>" type="checkbox" hashCount="<?php if(isset($value->hash_count) && $value->hash_count > 0 ) echo $value->hash_count; ?>"/></td>
												<td align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>												
												<td align="center"  style="<?php if(isset($cover_path) && $cover_path != '' ) { ?>background: url('<?php echo $cover_path;?>') no-repeat;<?php } else { ?>background: none no-repeat; <?php } ?>;background-size:cover;" ><a <?php if(isset($original_path) && $original_path != ADMIN_IMAGE_PATH.'no_user.jpeg' ) { ?> href="<?php echo $original_path; ?>" class="user_image_pop_up"  <?php } ?> title="View Photo"  ><img width="36" height="36" src="<?php echo $image_path;?>" ></a> </td>
												<td>
													<p><?php if(isset($userName) && $userName != '')	echo '<a id="user_"'..'>'trim($userName); else echo '-';?></p>
													<p><?php if(isset($value->cardCount) && $value->cardCount > 1){ ?> 
														<a href="CardsList?viewCardUserId=<?php if(isset($value->id) && $value->id != '') echo $value->id; ?>" title="view Cards "><?php echo '<br>Cards : '.$value->cardCount.'<br>'; ?></a>
														<?php }else echo '<p>Card : 1</p>';?>
													</p>
													<div style="display:block;width:100%;height30px;margin-top:5px;">
													<a href="UserManage?editId=<?php if(isset($value->id) && $value->id != '') echo $value->id; ?>" title="Edit" alt="Edit" class="edit"></a>
													</div>
												</td>
<!--
												<td><?php //if(isset($value->LastName) && $value->LastName != ''){ echo $value->LastName; }else echo '-';?></td>
-->
												<td><?php if(isset($value->Email) && $value->Email != '' ){ echo $value->Email;}else echo '-';?></td>
												<td><?php if(isset($value->FBId) && $value->FBId != ''){ echo '<p><b>Facebook : </b>'.$value->FBId.'</p>'; }else echo '-';?></td>
<!--
												<td><?php //if(isset($value->FBId) && $value->FBId != ''){ echo $value->FBId; }else echo '-';?></td>
												<td><?php //if(isset($value->TwitterId) && $value->TwitterId != ''){ echo $value->TwitterId; }else echo '-';?></td>			

												<td align="center"  style="<?php //if(isset($cover_path) && $cover_path != '' ) { ?>background: url('<?php //echo $cover_path;?>') no-repeat;<?php //} else { ?>background: none no-repeat; <?php //} ?>;background-size:cover;" ><a <?php //if(isset($original_path) && $original_path != ADMIN_IMAGE_PATH.'no_user.jpeg' ) { ?> href="<?php //echo $original_path; ?>" class="user_image_pop_up"  <?php //} ?> title="View Photo"  ><img width="36" height="36" src="<?php //echo $image_path;?>" ></a> </td>													
-->
												<td><?php if(isset($value->Location) && $value->Location != ''){ echo $value->Location; }else echo '-';?></td>	
<!--
												<td><?php //if(isset($value->cardCount) && $value->cardCount > 1){?> 
														<a href="CardsList?viewCardUserId=<?php //if(isset($value->id) && $value->id != '') echo $value->id; ?>" title="view Cards "><?php //echo $value->cardCount; ?></a>
														<?php //}else echo '1';?></td>	
-->
												<td><?php if(isset($value->DateCreated) && $value->DateCreated != '0000-00-00 00:00:00'){ echo date('m/d/Y',strtotime($value->DateCreated)); }else echo '-';?></td>
												<td align="center"><?php if($value->Status == 1) { ?><a class="active_icon" onclick="javascript:return confirm('Are you sure want to change the status?')" href="UserList?status=2&editId=<?php if(isset($value->id) && $value->id != '') echo $value->id;?>" alt="Click to Inactive" title="Click to Inactive"></a><?php } else { ?><a class="inactive_icon" onclick="javascript:return confirm('Are you sure you want to change the status?')" title="Click to Active" alt="Click to Active" href="UserList?status=1&editId=<?php if(isset($value->id) && $value->id != '') echo $value->id;?>"></a><?php } ?></td>																						
					
												<td align="center"><a href="UserManage?editId=<?php if(isset($value->id) && $value->id != '') echo $value->id; ?>" title="Edit" alt="Edit" class="edit"></a></td>
												<td align="center"><a href="UserDetail?viewId=<?php if(isset($value->id) && $value->id != '') echo $value->id; ?>" title="View" alt="View" class="view"></a></td>

												<td align="center"><a href="UserTags?uid=<?php if(isset($value->id) && $value->id != '') echo $value->id; ?>" class="interest_pop_up cboxElement"  alt="Tags"   title="Tags" ><img src="<?php echo ADMIN_IMAGE_PATH; ?>chat_images.png" width="18" height="14" alt=""></a></td>
												<td align="center"><a onclick="<?php if(isset($value->hash_count) && $value->hash_count > 0 ) { ?>alert('Sorry! you can not delete this user'); return false; <?php } else { ?>javascript:return confirm('Are you sure to delete?') <?php } ?>" href="UserList?delId=<?php if(isset($value->id) && $value->id != '') echo $value->id;?>" title="Delete" alt="Delete" class="delete"></a></td>
											
											</tr>
											<?php } } else { ?>	
											<tr>
												<td colspan="16" align="center" style="color:red;">No User found</td>
											</tr>
											<?php } ?>																		
										</table>
										<?php if(isset($userListResult) && is_array($userListResult) && count($userListResult) > 0){ ?>
										<table border="0" cellpadding="0" cellspacing="0" width="100%" class="">
											<tr><td height="10"></td></tr>
											<tr align="">
												<td align="left">
													<input type="submit" onclick="return deleteAll('Users');" class="submit_button" name="Delete" id="Delete" value="Delete" title="Delete" alt="Delete">&nbsp;&nbsp;													
												</td>
											</tr>
										</table>
										<?php } ?>
										</form>
									</td>
								</tr>
				            </table>
				        </div>
				     </td></tr>
				</table>
			</td>
		</tr>
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
</script>
</html>
