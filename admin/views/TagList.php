<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
require_once('controllers/ManagementController.php');
require_once('controllers/UserController.php');
$TagObj   =   new ManagementController();
$userObj   =   new UserController();
$class =  $msg  = $error  = $error_class  = $hashtag_ids = $param =  $condition = '' ;
$display = 'none';
if(isset($_GET['cs']) && $_GET['cs']=='1')
{
	destroyPagingControlsVariables();
	unset($_SESSION['intermingl_sess_tag_name']);
	unset($_SESSION['intermingl_sess_tag_status']);
//	$_SESSION['broadtags_sess_hashtag_createdby'] = 1;
}

if(isset($_POST['Search']) && $_POST['Search'] != '')
{
//	print_r($_POST);
	destroyPagingControlsVariables();
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	if(isset($_POST['search_hash_tag']) && $_POST['search_hash_tag'] != '')
		$_SESSION['intermingl_sess_tag_name'] 	= $_POST['search_hash_tag'];	
	else
		$_SESSION['intermingl_sess_tag_name'] 	= '';
	if(isset($_POST['status']))
		$_SESSION['intermingl_sess_tag_status'] 	= $_POST['status'];	
}
//Multiple Select

if(isset($_POST['do_action']) && $_POST['do_action'] != ''){
	if(isset($_POST['checkedrecords']) && is_array($_POST['checkedrecords']) && count($_POST['checkedrecords']) > 0	&&	isset($_POST['bulk_action']) && $_POST['bulk_action']!=''){
		$tagids = implode(',',$_POST['checkedrecords']);
		//$msg	=	4;
		if($_POST['bulk_action']==1){
			$updateIds		=	$tagids; //implode(',',$_POST['checkdelete']);//echo 'its test <pre>'; print_r($_POST); echo '</pre>';
			$updateStatus	=	1;
		}
		else if($_POST['bulk_action']==2){
			$updateIds	=	$tagids; //implode(',',$_POST['checkdelete']);//echo 'its test <pre>'; print_r($_POST); echo '</pre>';
			$updateStatus	=	2;
		}
		else{
			$delete_id = $tagids;
			//$msg	=	3;
		}
	}
}
/*if(isset($_POST['checkdelete']) && is_array($_POST['checkdelete']) && count($_POST['checkdelete']) > 0)
		$tagids = implode(',',$_POST['checkdelete']);
// Multiple Delete
if(isset($_POST['Delete']) && $_POST['Delete'] != '')
{
	if(isset($tagids) && $tagids != '')
		$delete_id = $tagids;
}
*/
if(isset($_GET['delId']) && $_GET['delId']!='')
{
	$delete_id      = $_GET['delId'];
}
if(isset($delete_id) && $delete_id != '')
{	
	$update_string   	 = " Status = 3 ";
	$condition       	 = " Id IN(".$delete_id.") ";
	$HashTagListResult	 = $TagObj->updateTagDetails($update_string,$condition);
	$_SESSION['notification_msg_code']	=	3;
	header("location:TagList");
	die();
}
else if(isset($updateIds) && $updateIds != ''){	
	$TagObj->changeTagStatus($updateIds,$updateStatus);
	$_SESSION['notification_msg_code']	=	4;
	header("location:TagList");
	die();
}

// Status Change
if((isset($_GET['editId']) && $_GET['editId']!='') && (isset($_GET['status']) && $_GET['status']!='') )
{
	$condition = " Id = ".$_GET['editId'];
	$update_string = " Status = ".$_GET['status'];
	$TagListResult  = $TagObj->updateTagDetails($update_string,$condition);
	$_SESSION['notification_msg_code']	=	4;
	header("location:TagList");
	die();
}
/*
//created by
if(isset($_GET['admin']) && $_GET['admin'] != '' ){
	$_SESSION['broadtags_sess_hashtag_createdby'] = 2;	
}
if(isset($_GET['user']) && $_GET['user'] != '' ){
	$_SESSION['broadtags_sess_hashtag_createdby'] = 1;	
}
if(isset($_SESSION['broadtags_sess_hashtag_createdby'])){
	if($_SESSION['broadtags_sess_hashtag_createdby'] == 2)
		$condition  .= ' and ht.CreatedBy = 0 ';
	if($_SESSION['broadtags_sess_hashtag_createdby'] == 1 )
		$condition  .= ' and ht.CreatedBy != 0 and ut.status != 3 ';
}
else{
	$_SESSION['broadtags_sess_hashtag_createdby'] == 1;
	$condition  .= ' and ht.CreatedBy = 0 ';
}
*/
setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
//setPagingControlValues('id',5);

$fields         = " * ";
$condition      = " and Status != '3' ";

$TagResult  	= $TagObj->getTagList($fields,$condition);
$tot_rec 		= $TagObj->getTotalRecordCount();
/*
$field     = ' ut.UserName, ut.id ';
$condition = ' ut.Status = 1 and ht.Status != 3 GROUP BY ut.id Order by ut.UserName asc ';
$userList  =  $TagObj->selectTagUserDetails($field,$condition);
*/
/*if($tot_rec!=0 && !is_array($TagResult)) {
	$_SESSION['curpage'] = 1;
	$hashTagResult       = $TagObj->getTagList($fields,$condition);
}
if(isset($_GET['msg']) && $_GET['msg'] == 1){
	$msg 		= 	"Tag added successfully";
	$display	=	"block";
	$class 		= 	"success_msg";
}
else if(isset($_GET['msg']) && $_GET['msg'] == 2){
	$msg 		= 	"Tag updated successfully";
	$display	=	"block";
	$class 		= 	"success_msg";
}
else if(isset($_GET['msg']) && $_GET['msg'] == 3){
	$msg 		= 	"Tag deleted successfully";
	$display	=	"block";
	$class 		= 	"error_msg";
}
else if(isset($_GET['msg']) && $_GET['msg'] == 4){
	$msg 		= 	"Status changed successfully";
	$display	=	"block";
	$class 		= 	"success_msg";
}
else if(isset($_GET['msg']) && $_GET['msg'] == 5){
	$msg 		= 	"Tag Post added successfully";
	$display	=	"block";
	$class 		= 	"success_msg";
}*/
commonHead(); ?>
<body>
	<table cellpadding="0" cellspacing="0" border="0" width="100%" align="center">
		<tr>
			<td align="center">
				<table cellpadding="0" cellspacing="0" border="0" width="95%" align="center">					
					<tr><td colspan="2"><?php top_header(); ?></td></tr>
				    <tr>
						<td colspan="2">
						 	 <div class="left_menu sidebar-nav" style="float:left;"><?php side_bar()?></div>
						 	 <div id="content_3" class="content">
							 <div class="box-header"><h2><i style="margin-left:5px;margin-top:2px;" class="icon_tag_list"></i>Tag List</h2>
							 <span style="float:right"><a style="cursor:pointer;" title="Add Hashtag" href="TagManage"  class="pop_up"> + Add Tag</a></span>
						 	</div>
							   	 <table cellpadding="0" cellspacing="0" width="98%" align="center" class="headertable">
								 <tr><td height="10"></td></tr>
									 <tr>
					                    <td valign="top" align="center" colspan="2">
											 <form name="search_category" action="TagList<?php echo $param; ?>" method="post">
					                           <table align="center" cellpadding="0" cellspacing="0" border="0" class="filter_form" width="100%">									       
													<tr><td height="15" colspan="4"></td></tr>
													<tr>
											<!--		<td <?php if(isset($_SESSION['intermingl_sess_hashtag_createdby']) && $_SESSION['intermingl_sess_hashtag_createdby'] == '1') { ?> style="padding-left:20px;" width="10%" <?php } else { ?> style="padding-left:50px;" width="15%" <?php }?> align="left"><label>Hashtag</label></td>-->
														<td width="12%" align="left" style="padding-left:100px;"><label>Tag </label></td>
														<td width="2%" >:</td>
														<td align="left" width="20%">
															<input title="Enter Hashtag" type="text" class="input" name="search_hash_tag" id="search_hash_tag" tabindex="1"  value="<?php  if(isset($_SESSION['intermingl_sess_tag_name']) && $_SESSION['intermingl_sess_tag_name'] != '') echo unEscapeSpecialCharacters($_SESSION['intermingl_sess_tag_name']);  ?>" >
														</td>
														<td width="12%" style="padding-left:100px;" align="left"><label>Status</label></td>
														<td width="2%">:</td>
														<td align="left">
															<select name="status" id="status" tabindex="2" title="Select Status" style="width:50%;">
																	<option value="">Select</option>
																	<option value="1" <?php  if(isset($_SESSION['intermingl_sess_tag_status']) && $_SESSION['intermingl_sess_tag_status'] != '' && $_SESSION['intermingl_sess_tag_status'] == '1') echo 'Selected';  ?>>Active</option>
																	<option value="2" <?php  if(isset($_SESSION['intermingl_sess_tag_status']) && $_SESSION['intermingl_sess_tag_status'] != '' && $_SESSION['intermingl_sess_tag_status'] == '2') echo 'Selected';  ?>>Inactive</option>
															</select>
														</td>
														<td  colspan="9" align="center" valign="top"><input type="submit" class="submit_button" name="Search" id="Search" value="Search" title="Search"  alt="Search" tabindex="4"></td>
													</tr>
													<tr><td height="15" colspan="4"></td></tr>													
												 </table>
											  </form>	
					                    </td>
					               	</tr>
									<tr><td height="20" colspan="2"></td></tr>	
									<tr><td height="20"></td></tr>	
						<!--		<tr>
									<td colspan="2">
											<table cellpadding="0"  cellspacing="0" border="0" width="100%">
												<tr>
													<td class="menu_list">														
					 									 <ul class="tabs">		
														 	<li <?php // if(isset($_SESSION['broadtags_sess_hashtag_createdby']) && $_SESSION['broadtags_sess_hashtag_createdby'] == 1) { ?> class = 'sel' <?php // } ?> ><a href="HashTagList?user=1&cs=1" class="tab " name="Search_user_created" id="Search_user_created" title="User Created" alt="User Created">User Created</a></li>
															<li <?php // if(isset($_SESSION['broadtags_sess_hashtag_createdby']) && $_SESSION['broadtags_sess_hashtag_createdby'] == 2) { ?> class = 'sel' <?php // } ?> ><a href="HashTagList?admin=1&cs=1" class="tab " name="Search_admin_created" id="Search_admin_created" title="Admin Created" alt="Admin Created">Admin Created</a></li>
														 </ul>                        								
													</td>
												</tr>
											</table>
										</td>
									</tr>	
						-->			
									<tr>
										<td colspan="2">
												<table cellpadding="0"  cellspacing="0" border="0" align="center" width="100%">															
													<tr>
														<td colspan="2">
															<table cellpadding="0"  cellspacing="0" border="0" align="center" width="100%">
																<tr>
																	<?php if(isset($TagResult) && is_array($TagResult) && count($TagResult) > 0){ ?>
																	<td align="left" width="20%">No. of Tag(s)&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
																	<?php } ?>
																	<td align="center">
																		<?php if(isset($TagResult) && is_array($TagResult) && count($TagResult) > 0 ) {
																		 	pagingControlLatest($tot_rec,'TagList'.$param); ?>
																		<?php }?>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
													
													<tr><td height="10" colspan="2"></td></tr>
													<tr><td align="center" colspan="2">
													<?php displayNotification(); ?>
<!--					<div class="<?php  //echo $class;  ?> "><span style="display:<?php  //echo $display;  ?>"><?php //if(isset($msg) && $msg != '') echo $msg;  ?></span></div>	-->
													</td></tr>
													<tr><td height="10" colspan="2"></td></tr>
													<tr>
														<td colspan="2">
														   <?php if(isset($TagResult) && is_array($TagResult) && count($TagResult) > 0 ) { ?>
														    <form action="TagList" class="l_form" name="TagForm" id="TagForm"  method="post"> 															
															<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table">
																<tr align="left">
																	<th align="center" style="text-align:center"  width="3%"><input onclick="checkAllRecords('TagForm');" type="Checkbox" name="checkAll"/></th>
																	<th align="center"  style="text-align:center" width="5%">#</th>
																	<th width="50%" align="left"><?php echo SortColumn('Tags','Tag'); ?></th>
																	<th width="15%" align="left"><?php echo SortColumn('DateCreated','Created Date'); ?></th>
																	<!--
																	<th width="10%" align="left"><?php // echo SortColumn('Status','Status'); ?></th>
																	<th width="17%" <?php // if($_SERVER['HTTP_HOST'] == '172.21.4.104' || $_SERVER['HTTP_X_FORWARDED_FOR'] == '27.124.58.84' ) { ?>  colspan="5" <?php //} else { ?> colspan="4" <?php // } ?> align="center">Action</th>
																	-->
																</tr>
																<?php	foreach($TagResult as $key=>$value){ ?>									
																<tr id="<?php echo $value->id;?>" onmouseover=showaction(<?php echo $value->id;?>); onmouseout=hideaction(<?php echo $value->id;?>) height="56px">
																	<td align="center"><input id="checkedrecords[]" name="checkedrecords[]" value="<?php  if(isset($value->id) && $value->id != '') echo $value->id  ?>" type="checkbox" hashCount="<?php // if($value->FollowCount > 0 || $value->PostCount > 0 )  echo 1; ?>" /></td>
																	<td align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
																	<td align="left" valign="top">
																	<?php if(isset($value->Tags) && $value->Tags != ''	&&	isset($value->id)	&&	$value->id!=""){ 
																			echo '<a href="TagManage?viewId='.$value->id.'" title="View" alt="View" class="recordView pop_up">'.$value->Tags.'</a>';
																		  } else echo '-'; ?>
																		<div class="userAction" style="display:none" id="userAction_<?php echo $value->id;?>">
																			<?php if(isset($value->Status)	&&	$value->Status == 1) { ?>			
																				<a style="float:left;" class="active_icon userIcon" alt=" Active" title="Active User" onclick="javascript:return confirm('Are you sure want to change the status?')" href="TagList?editId=<?php echo $value->id;?>&status=2"></a>
																			<?php }else if(isset($value->Status)	&&	$value->Status == 2){ ?>
																					<a style="float:left;" class="inactive_icon userIcon"  title="Inactive User" alt="Inactive User" onclick="javascript:return confirm('Are you sure want to change the status?')" href="TagList?editId=<?php echo $value->id;?>&status=1"></a>
																			<?php }?>
																			<div style="padding-top:6px;">
																				<a href="TagManage?editId=<?php if(isset($value->id) && $value->id != '') echo $value->id; ?>" title="Edit" alt="Edit" class="editUser pop_up" style="float:left;" >Edit</a>
																				<a href="TagManage?viewId=<?php if(isset($value->id) && $value->id != '') echo $value->id; ?>" title="View" alt="View" class="viewUser pop_up">View</a>
																				<a onclick="javascript:return confirm('Are you sure to delete?')" href="TagList?delId=<?php if(isset($value->id) && $value->id != '') echo $value->id;?>" title="Delete" alt="Delete" class="deleteUser">Delete</a>
																			</div>
																		</div>
																	</td>
																	<td align="left"><?php if(isset($value->DateCreated) && $value->DateCreated != '0000-00-00 00:00:00'){ echo date('m/d/Y',strtotime($value->DateCreated)); } else echo '-'; ?></td>
																	<!--
																	<td align="center" width="5%">
																		<?php // if($value->Status == 1) { ?><a class="active_icon" title="Click to Inactive" onclick="javascript:return confirm('Are you sure you want to change the status?')" href="TagList?status=2&editId=<?php // if(isset($value->id) && $value->id != '') echo $value->id;?>"></a><?php// } else if($value->Status == 2){ ?><a class="inactive_icon" onclick="javascript:return confirm('Are you sure you want to change the status?')" title="Click to Active" href="HashTagList?status=1&editId=<?php // if(isset($value->id) && $value->id != '') echo $value->id;?>"></a><?php // } else if($value->Status == 4){ ?> <a class="moderate_icon" onclick="javascript:return confirm('Are you sure you want to change the status?')" title="Click to Active" href="HashTagList?status=1&editId=<?php // if(isset($value->id) && $value->id != '') echo $value->id;?>"></a><?php // } ?>
																	</td>
																	<td align="center"><a href="TagManage?editId=<?php // if(isset($value->id) && $value->id != '') echo $value->id; ?>" title="Edit"  class="edit pop_up"></a></td>
																	<td align="center"><a onclick="javascript:return confirm('Are you sure to delete?');"  href="TagList?delId=<?php //if(isset($value->id) && $value->id != '') echo $value->id;?>" title="Delete" class="delete"></a></td>
																	-->
																</tr>
																<?php } ?>																		
															</table>
															<?php if(isset($TagResult) && is_array($TagResult) && count($TagResult) > 0 ){ 
																	bulk_action($statusArray);
																  } ?>
															</form>
													<?php } else { ?>	
																<tr>
																	<td colspan="10" align="center" style="color:red;">No tag found</td>
																</tr>
													<?php } ?>
														</td>
													</tr>
													<tr><td height="10"></td></tr>
												</table>
										</td>
									</tr>								
								</table>							 
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

$(document).ready(function() {		
	$(".pop_up").colorbox(
		{
			iframe:true,
			width:"35%", 
			height:"35%",
			title:true
	});
});
$(function(){
   var bodyHeight = $('body').height();
   var maxHeight = '564';
   if(bodyHeight<maxHeight) {
   	setHeight = bodyHeight;
   } else {
   		setHeight = maxHeight;
   }
    parent.$.colorbox.resize({
        innerWidth:$('body').width(),
        innerHeight:setHeight
    });
});

</script>
</html>