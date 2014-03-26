<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
require_once('controllers/ManagementController.php');
$interestObj   =   new ManagementController();
$class =  $msg  = $error  = $error_class  = $hashtag_ids = $param =  $condition = $tot_rec	=	'' ;
$display = 'none';
global $statusArray;
if(isset($_GET['cs']) && $_GET['cs']=='1')
{
	destroyPagingControlsVariables();
	unset($_SESSION['intermingl_search_interest']);
	unset($_SESSION['intermingl_sess_interest_status']);
}
if(isset($_POST['Search'])  && $_POST['Search'] != '')
{
//	print_r($_POST);
	destroyPagingControlsVariables();
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	if(isset($_POST['search_interest']) )//&& $_POST['search_goal'] != '')
		$_SESSION['intermingl_search_interest'] 	= $_POST['search_interest'];
	if(isset($_POST['status']))
		$_SESSION['intermingl_sess_interest_status'] 	= $_POST['status'];	
}
//Multiple Select
if(isset($_POST['do_action']) && $_POST['do_action'] != ''){
	if(isset($_POST['checkedrecords']) && is_array($_POST['checkedrecords']) && count($_POST['checkedrecords']) > 0	&&	isset($_POST['bulk_action']) && $_POST['bulk_action']!=''){
		$interest_ids = implode(',',$_POST['checkedrecords']);
		//$msg	=	4;
		if($_POST['bulk_action']==1){
			$updateIds		=	$interest_ids;
			$updateStatus	=	1;
		}
		else if($_POST['bulk_action']==2){
			$updateIds	=	$interest_ids;
			$updateStatus	=	2;
		}
		else{
			$delete_id = $interest_ids;
			//$msg	=	3;
		}
	}
}
/*if(isset($_POST['checkdelete']) && is_array($_POST['checkdelete']) && count($_POST['checkdelete']) > 0)
		$interest_ids = implode(',',$_POST['checkdelete']);
// Multiple Delete
if(isset($_POST['Delete']) && $_POST['Delete'] != '')
{
	if(isset($interest_ids) && $interest_ids != '')
		$delete_id = $interest_ids;
}
*/
if(isset($_GET['delId']) && $_GET['delId']!='')
{
	$delete_id      = $_GET['delId'];
}
if(isset($delete_id) && $delete_id != '')
{	
	$update_string   	 = " Status = 3 ";
	$condition       	 = " id IN(".$delete_id.") ";
	$interestListResult	 = $interestObj->updateInterestDetails($update_string,$condition);
	$_SESSION['notification_msg_code']	=	3;
	header("location:InterestList");	
	die();
}
else if(isset($updateIds) && $updateIds != ''){	
	$interestObj->changeInterestStatus($updateIds,$updateStatus);
	$_SESSION['notification_msg_code']	=	4;
	header("location:InterestList");
	die();
}

// Status Change
if((isset($_GET['editId']) && $_GET['editId']!='') && (isset($_GET['status']) && $_GET['status']!='') )
{
	$condition = " id = ".$_GET['editId'];
	$update_string = " Status = ".$_GET['status'];
	$interestListResult  = $interestObj->updateInterestDetails($update_string, $condition);
	$_SESSION['notification_msg_code']	=	4;
	header("location:InterestList");
	die();
}

setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$fields         = " * ";
$condition      = " AND Status !=3 ";

$interestListResult  = $interestObj->getInterestsList($fields,$condition);
$tot_rec 		= $interestObj->getTotalRecordCount();

if($tot_rec!=0 && !is_array($interestListResult)) {
	$_SESSION['curpage'] = 1;
	$interestListResult       = $interestObj->getInterestsList($fields,$condition);
	$tot_rec 		= $interestObj->getTotalRecordCount();
}
//echo '<pre>';print_r($interestListResult);echo '</pre>';
/*if(isset($_GET['msg']) && $_GET['msg'] == 1){
	$msg 		= 	"Interest added successfully";
	$display	=	"block";
	$class 		= 	"success_msg";
}
else if(isset($_GET['msg']) && $_GET['msg'] == 2){
	$msg 		= 	"Interest updated successfully";
	$display	=	"block";
	$class 		= 	"success_msg";
}
else if(isset($_GET['msg']) && $_GET['msg'] == 3){
	$msg 		= 	"Interest deleted successfully";
	$display	=	"block";
	$class 		= 	"error_msg";
}
else if(isset($_GET['msg']) && $_GET['msg'] == 4){
	$msg 		= 	"Status changed successfully";
	$display	=	"block";
	$class 		= 	"success_msg";
}
*/
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
							 <div class="box-header"><h2><i style="margin-top:3px" class="icon-interest_list"></i>Interest List</h2>
							 <span style="float:right;"><a style="cursor:pointer;" title="Add Interest" href="InterestManage"  class="pop_up"> + Add Interest</a></span>
						 	</div>
							   	 <table cellpadding="0" cellspacing="0" width="98%" align="center" class="headertable">
									 <tr><td height="20" colspan="2"></td></tr>
									 <tr>
					                    <td valign="top" align="center" colspan="2">
											 <form name="search_category" action="InterestList<?php echo $param; ?>" method="post">
					                           <table align="center" cellpadding="0" cellspacing="0" border="0" class="filter_form" width="100%">									       
													<tr><td height="15" colspan="4"></td></tr>
													<tr>
														<td  style="padding-left:100px;" width="12%" align="left"><label>Interest</label></td>
														<td width="2%" >:</td>
														<td align="left" width="20%">
															<input title="Enter Interest" type="text" class="input" name="search_interest" id="search_interest" tabindex="1"  value="<?php  if(isset($_SESSION['intermingl_search_interest']) && $_SESSION['intermingl_search_interest'] != '') echo unEscapeSpecialCharacters($_SESSION['intermingl_search_interest']);  ?>" >
														</td>	
														<td width="12%" align="left" style="padding-left:100px;"><label>Status</label></td>
														<td width="2%">:</td>
														<td align="left" >
															<select name="status" id="status" tabindex="2" title="Select Status" style="width:60%;">
																	<option value="">Select</option>
																	<?php foreach($statusArray as $key => $interest_status) {
																			if($key <=2 ){?>
																	<option value="<?php echo $key; ?>" <?php  if(isset($_SESSION['intermingl_sess_interest_status']) && $_SESSION['intermingl_sess_interest_status'] != '' && $_SESSION['intermingl_sess_interest_status'] == $key) echo 'Selected';  ?>><?php echo $interest_status; ?></option>
																	<?php }
																	}?>
															</select>
														</td> 
															<td colspan="2" width="5%" align="left"></td>
															<td  align="left" valign="top"><input type="submit" class="submit_button" name="Search" id="Search" value="Search" title="Search"  alt="Search" tabindex="4"></td>
													</tr>
													<tr><td height="15" colspan="4"></td></tr>													
												 </table>
											  </form>	
					                    </td>
					               	</tr>
									<tr><td height="20" colspan="2"></td></tr>	
									<tr><td height="20"></td></tr>	
									<tr>
										<td colspan="2">
												<table cellpadding="0"  cellspacing="0" border="0" align="center" width="100%">															
													<tr>
														<td colspan="2">
															<table cellpadding="0"  cellspacing="0" border="0" align="center" width="100%">
																<tr>
																	<?php if(isset($interestListResult) && is_array($interestListResult) && count($interestListResult) > 0){ ?>
																	<td align="left" width="20%">No. of Interest(s)&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
																	<?php } ?>
																	<td align="center">
																		<?php if(isset($interestListResult) && is_array($interestListResult) && count($interestListResult) > 0 ) {
																		 	pagingControlLatest($tot_rec,'InterestList'.$param); ?>
																		<?php }?>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
													
													<tr><td height="10" colspan="2"></td></tr>
													<tr><td align="center" colspan="2">
													<?php displayNotification(); ?>
					<!--	<div class="<?php  //echo $class;  ?> "><span style="display:<?php  //echo $display;  ?>"><?php //if(isset($msg) && $msg != '') echo $msg;  ?></span></div>	-->
													</td></tr>
													<tr><td height="10" colspan="2"></td></tr>
													<tr>
														<td colspan="2">
														<div >
														   <?php if(isset($interestListResult) && is_array($interestListResult) && count($interestListResult) > 0 ) { ?>
														    <form action="InterestList" class="l_form" name="interestform" id="interestform"  method="post"> 															
															<table border="0" cellpadding="0" cellspacing="0" width="100%"  class="user_table">
																<tr align="left">
																	<th align="center"  style="text-align:center" width="5%"><input onclick="checkAllRecords('interestform');" type="Checkbox" name="checkAll"/></th>
																	<th align="center" style="text-align:center"  width="5%">#</th>
																	<th width="40%" align="left"><?php echo SortColumn('Interest','Interest'); ?></th>
																	<th width="20%" align="left"><?php echo SortColumn('DateCreated','Created Date'); ?></th>
																	<!-- <th width="12%" align="left"><?php //echo SortColumn('Status','Status'); ?></th> -->
																	<!-- <th colspan="3" align="center">Action</th> -->
																</tr>
																<?php 	$i=1;	foreach($interestListResult as $key=>$value){ ?>									
																<tr id="<?php echo $value->id;?>" onmouseover=showaction(<?php echo $value->id;?>); onmouseout=hideaction(<?php echo $value->id;?>) height="56px"> 
																	<td align="center"><input id="checkedrecords[]" name="checkedrecords[]" value="<?php  if(isset($value->id) && $value->id != '') echo $value->id  ?>" type="checkbox" /></td>
																	<td align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
																	<td align="left" valign="top">
																		<?php 	if(isset($value->Interest) && $value->Interest != ''	&&	isset($value->id)	&&	$value->id!=''){ 
																				echo '<a href="InterestManage?viewId='.$value->id.'" title="View" alt="View" class="recordView pop_up">'.$value->Interest.'</a>';} else echo '-'; ?>
																		<div class="userAction" style="display:none" id="userAction_<?php echo $value->id;?>">
																			<?php 
																					if(isset($value->Status)	&&	$value->Status == 1) { ?>			
																						<a style="float:left;" class="active_icon userIcon" alt=" Active" title="Active User" onclick="javascript:return confirm('Are you sure want to change the status?')" href="InterestList?editId=<?php echo $value->id;?>&status=2"></a>
																			<?php 	} else if(isset($value->Status)	&&	$value->Status == 2){ ?>
																						<a style="float:left;" class="inactive_icon userIcon"  title="Inactive User" alt="Inactive User" onclick="javascript:return confirm('Are you sure want to change the status?')" href="InterestList?editId=<?php echo $value->id;?>&status=1"></a>
																			<?php 	}	?>
																			<div style="padding-top:6px;">
																				<a href="InterestManage?editId=<?php if(isset($value->id) && $value->id != '') echo $value->id; ?>" title="Edit" alt="Edit" class="editUser pop_up" style="float:left;" >Edit</a>
																				<a href="InterestManage?viewId=<?php if(isset($value->id) && $value->id != '') echo $value->id; ?>" title="View" alt="View" class="viewUser pop_up">View</a>
																				<a onclick="javascript:return confirm('Are you sure to delete?')" href="InterestList?delId=<?php if(isset($value->id) && $value->id != '') echo $value->id;?>" title="Delete" alt="Delete" class="deleteUser">Delete</a>
																			</div>
																		</div>
																
																	</td>
																	<td align="left"><?php if(isset($value->DateCreated) && $value->DateCreated != '0000-00-00 00:00:00'){ echo date('m/d/Y',strtotime($value->DateCreated)); } else echo '-'; ?></td>
																	<!-- <td align="left"><?php //if(isset($value->Status) && $value->Status != ''){ echo $interestStatus[$value->Status];} else echo '-'; ?></td> -->
																	<!--
																	<td align="left"><?php if($value->Status == 1) { ?><a class="active_icon" title="Click to Inactive" onclick="javascript:return confirm('Are you sure you want to change the status?')" href="InterestList?status=2&editId=<?php if(isset($value->id) && $value->id != '') echo $value->id;?>"></a><?php } else if($value->Status == 2){ ?><a class="inactive_icon" onclick="javascript:return confirm('Are you sure you want to change the status?')" title="Click to Active" href="InterestList?status=1&editId=<?php if(isset($value->id) && $value->id != '') echo $value->id;?>"></a><?php }?> </td>
																	<td align="center"><a href="InterestManage?editId=<?php if(isset($value->id) && $value->id != '') echo $value->id; ?>" title="Edit"  class="edit pop_up"></a></td>
																	<td align="center"><a onclick="javascript:return confirm('Are you sure to delete?') "  href="InterestList?delId=<?php if(isset($value->id) && $value->id != '') echo $value->id;?>" title="Delete" class="delete"></a></td>
																	-->
																</tr>
																<?php } ?>
															</table>
															<?php if(isset($interestListResult) && is_array($interestListResult) && count($interestListResult) > 0 ){ 
																	bulk_action($statusArray);
																  }	?>
															</form>
													<?php } else { ?>	
																<tr>
																	<td colspan="10" align="center" style="color:red;">No tag found</td>
																</tr>
													<?php } ?>
														</div>
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
/*
jQuery(function() {
jQuery("div.userAction a").hide();
jQuery('table.user_actions tr[id^=test_id_]').hover(function() {
		console.log('test Purpose');	
		jQuery(this).find("div.userAction a").css("display","inline-block");
    }, function() {
        jQuery(this).find("div.userAction a").hide();
    });
});
*/
</script>
</html>