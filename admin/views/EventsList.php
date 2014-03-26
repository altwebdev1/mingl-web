<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/EventsController.php');
$eventsObj   =   new EventsController();
$display   =   'none';
$class  =  $msg    = $cover_path = '';
//if(isset($_SESSION['referPage']))
//	unset($_SESSION['referPage']);
	$_SESSION['referPage']	=	'EventsList';
if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	unset($_SESSION['intermingl_sess_event_title']);
	unset($_SESSION['intermingl_sess_event_loc']);
	unset($_SESSION['intermingl_sess_event_status']);
	unset($_SESSION['intermingl_sess_event_start']);
	unset($_SESSION['intermingl_sess_event_end']);
	unset($_SESSION['sess_intermingl_likedEventsId']);
	unset($_SESSION['sess_intermingl_joinEventId']);
	if(isset($_SESSION['intermingl_ses_from_timeZone']))
		unset($_SESSION['intermingl_ses_from_timeZone']);
}
if(isset($_POST['do_action']) && $_POST['do_action'] != ''){
	if(isset($_POST['checkedrecords']) && is_array($_POST['checkedrecords']) && count($_POST['checkedrecords']) > 0){
		$delete_ids = implode(',',$_POST['checkedrecords']);
//if(isset($_POST['Delete']) && $_POST['Delete'] != ''){
		if(isset($delete_ids) && $delete_ids != '')
			$delete_id = $delete_ids;
	}
}
if(isset($_GET['delId']) && $_GET['delId']!=''){
	$delete_id      = $_GET['delId'];
}
if(isset($delete_id) && $delete_id != ''){	
	$update_string   	 = " Status = 3 ";
	$condition       	 = " id IN(".$delete_id.") ";
	$goalsListResult	 = $eventsObj->updateEventDetails($update_string,$condition);
	$_SESSION['notification_msg_code']	=	3;
	header("location:EventsList");
	die();
}
if(isset($_POST['Search']) && $_POST['Search'] != ''){
	destroyPagingControlsVariables();
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
//	echo "<pre>";print_r($_POST);echo "</pre>";
	if(isset($_POST['event_title']))
		$_SESSION['intermingl_sess_event_title'] 	= $_POST['event_title'];
	if(isset($_POST['event_loc']))
		$_SESSION['intermingl_sess_event_loc']	    = $_POST['event_loc'];
	if(isset($_POST['ses_status']))
		$_SESSION['intermingl_sess_event_status']	= $_POST['event_status'];
	if(isset($_POST['start_date']) && $_POST['start_date'] != ''){
		$validate_date = dateValidation($_POST['start_date']);
		if($validate_date == 1){
			//$date = date('Y-m-d',strtotime($_POST['start_date']));
			//if($date != '' && $date != '1970-01-01' && $date != '0000-00-00' )
				$_SESSION['intermingl_sess_event_start']	= $_POST['start_date'];	//$date;
			//else 
				//$_SESSION['intermingl_sess_event_start']	= '';
		}
		else 
			$_SESSION['intermingl_sess_event_start']	= '';
	}
	else 
		$_SESSION['intermingl_sess_event_start']	= '';
	if(isset($_POST['end_date']) && $_POST['end_date'] != ''){
		$validate_date = dateValidation($_POST['end_date']);
		if($validate_date == 1){
			//$date = date('Y-m-d',strtotime($_POST['end_date']));
			//if($date != '' && $date != '1970-01-01' && $date != '0000-00-00' )
				$_SESSION['intermingl_sess_event_end']	= $_POST['end_date'];	//$date;
			//else 
				//$_SESSION['intermingl_sess_event_end']	= '';
		}
		else 
			$_SESSION['intermingl_sess_event_end']	= '';
	}
	else 
		$_SESSION['intermingl_sess_event_end']	= '';
}
setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
$fields    	= 	" sum(case when con.Type=2 then 1 else 0 end) as likeCount, e.id,e.CoverPhoto,e.Title,e.Description,e.Location,e.TwitterHashtag,e.StartDate,e.EndDate  ";
//$fields    	= 	" sum(case when con.Type=2 then 1 else 0 end) as likeCount, e.*";
$condition 	= 	" and e.Status != '3'";
$leftJoin	=	" LEFT JOIN connections as con ON(e.id=con.fkEventsId) ";
//$leftJoin	=	" LEFT JOIN joinevents as je ON(e.id=je.fkEventsId) LEFT JOIN connections as con ON(e.id=con.fkEventsId) ";
$eventsListResult  = $eventsObj->getEventsList($fields, $leftJoin, $condition);
$tot_rec 		 = $eventsObj->getTotalRecordCount();
$fields    	= 	" distinct e.id, count(je.fkEventsId) as joinedUserCount  ";
$condition 	= 	" and e.Status != '3' ";
$leftJoin	=	" LEFT JOIN joinevents as je ON(e.id=je.fkEventsId) ";
$joinedUsersList  = $eventsObj->getEventsList($fields, $leftJoin, $condition);
//echo '<pre>'; print_r($joinedUsersList); echo '</pre>';
if($tot_rec==0 && !is_array($eventsListResult)) {
	$_SESSION['curpage'] = 1;
	$eventsListResult  = $eventsObj->getEventsList($fields, $leftJoin, $condition);
}
if(isset($_GET['msg']) && $_GET['msg'] == 3){
	$msg 		= 	"Event deleted successfully";
	$display	=	"block";
	$class 		= 	"error_msg";
}
else if(isset($_GET['msg']) && $_GET['msg'] == 2){
	$msg 		= 	"Event updated successfully";
	$display	=	"block";
	$class 		= 	"success_msg";
}
else if(isset($_GET['msg']) && $_GET['msg'] == 1){
	$msg 		= 	"Event added successfully";
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
					 <div class="left_menu sidebar-nav" style="float:left;"><?php side_bar()?></div>
						<div id="content_3" class="content">
						<div class="box-header"><h2><i style="margin-top: 5px;" class="icon_event_list"></i>Events List</h2>
							 <span style="float:right;"><a href="EventsManage" title="Add Event"> + Add Event</a></span>
						 	</div>
				            <table cellpadding="0" cellspacing="0" border="0" width="98%" align="center" class="headertable">
								<tr><td height="20"></td></tr>
								<tr>
				                    <td valign="top" align="center" colspan="2">
										
										 <form name="search_category" action="EventsList" method="post">
				                           <table align="center" cellpadding="0" cellspacing="0" border="0" class="filter_form" width="100%">									       
												<tr><td height="15"></td></tr>
												<tr>													
													<td width="7%" style="padding-left:20px;"><label>Event</label></td>
													<td width="3%" align="center">:</td>
													<td align="left"  height="40">
														<input type="text" class="input" name="event_title" id="event_title"  value="<?php  if(isset($_SESSION['intermingl_sess_event_title']) && $_SESSION['intermingl_sess_event_title'] != '') echo unEscapeSpecialCharacters($_SESSION['intermingl_sess_event_title']);  ?>" >
				
													</td>
													<td width="10%" style="padding-left:20px;"><label>Location</label></td>
													<td width="3%" align="center">:</td>
													<td align="left"  height="40">
														<input type="text" class="input" id="event_loc" name="event_loc"  value="<?php  if(isset($_SESSION['intermingl_sess_event_loc']) && $_SESSION['intermingl_sess_event_loc'] != '') echo unEscapeSpecialCharacters($_SESSION['intermingl_sess_event_loc']);  ?>" >
													</td>
												</tr>
												<tr><td height="10"></td></tr>
												<tr>

													<td width="10%" style="padding-left:20px;" align="left"><label>Event Start Date</label></td>
													<td width="3%" align="center">:</td>
													<td height="40" align="left" >
														<input style="width:90px" type="text" autocomplete="off" maxlength="10" class="input datepicker" name="start_date" id="start_date" title="Select Date" value="<?php if(isset($_SESSION['intermingl_sess_event_start']) && $_SESSION['intermingl_sess_event_start'] != '') echo date('m/d/Y',strtotime($_SESSION['intermingl_sess_event_start'])); else echo '';?>" > (mm/dd/yyyy)
													</td>
													<td width="10%" style="padding-left:20px;" ><label>Event End Date</label></td>
													<td width="3%" align="center">:</td>
													<td height="40" align="left" >
														<input style="width:90px" type="text" autocomplete="off"  maxlength="10" class="input datepicker" name="end_date" id="end_date" title="Select Date" value="<?php if(isset($_SESSION['intermingl_sess_event_end']) && $_SESSION['intermingl_sess_event_end'] != '') echo date('m/d/Y',strtotime($_SESSION['intermingl_sess_event_end'])); else echo '';?>" > (mm/dd/yyyy)
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
												<?php if(isset($eventsListResult) && is_array($eventsListResult) && count($eventsListResult) > 0){ ?>
												<td align="left" width="20%">No. of Events(s)&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
												<?php } ?>
												<td align="center">
														<?php if(isset($eventsListResult)	&&	is_array($eventsListResult) && count($eventsListResult) > 0 ) {
														 	pagingControlLatest($tot_rec,'EventsList'); ?>
														<?php }?>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr><td height="10"></td></tr>
								<tr><td colspan= '2' align="center">
								<?php displayNotification(); ?>
<!--								
								<div class="<?php // echo $class;  ?> w50"><span><?php //if(isset($msg) && $msg != '') echo $msg;  ?></span></div>
-->								
								</td></tr>
								<tr><td height="10"></td></tr>
								<tr>
									<td colspan="2">
									 <?php if(isset($eventsListResult) && is_array($eventsListResult) && count($eventsListResult) > 0 ) { ?>
									  <form action="EventsList" class="l_form" name="EventListForm" id="EventListForm"  method="post"> 
										<!-- <input type="hidden" value="" id="message_hidden" name="message_hidden"/> -->
										<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table">
											<tr align="left">
												<th align="center" width="3%"><input onclick="checkAllRecords('EventListForm');" type="Checkbox" name="checkAll"/></th>
												<th align="center" width="3%" style="text-align:center;">#</th>
												<th width="25%"><?php echo SortColumn('Title','Event'); ?></th>
												<th width="25%"><?php echo SortColumn('Description','Description'); ?></th>
												<th width="10%"><?php echo SortColumn('Location','Location'); ?></th>
												<th width="8%"><?php echo SortColumn('TwitterHashtag','Twitter Hashtag'); ?></th>
												<th width="10%">Count</th>
												<!--<th width="8%"><?php // echo SortColumn('StartDate','Start Date'); ?></th>
												<th width="8%"><?php  // echo SortColumn('EndDate','End Date'); ?></th>-->
												<th width="19%">Date</th>
																						
											</tr>
											
											<?php		$i	=	0;
													foreach($eventsListResult as $key=>$value){
														$original_path = ADMIN_IMAGE_PATH.'no_user.jpeg';
														$photo = $value->CoverPhoto;
														if(isset($photo) && $photo != ''){
															$user_image = $photo;		
															$original_path_rel = EVENT_COVER_IMAGE_PATH_REL.$user_image;
															if(SERVER){
																if(image_exists(9,$user_image)){
																	$original_path = EVENT_COVER_IMAGE_PATH.$user_image;
																}
															}
															else if(file_exists($original_path_rel)){
																	$original_path 	= EVENT_COVER_IMAGE_PATH.$user_image;
															}
														}
											 ?>									
											<tr id="<?php echo $value->id;?>" onmouseover=showaction(<?php echo $value->id;?>); onmouseout=hideaction(<?php echo $value->id;?>) height="56px">
												<td align="center"><input id="checkedrecords" name="checkedrecords[]" value="<?php  if(isset($value->id) && $value->id != '') echo $value->id  ?>" type="checkbox" /></td>
												<td align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>												
												<td valign="top">
													<div style="<?php 	if(isset($original_path) && $original_path != "")	{ ?> 
																			background: url('<?php echo $cover_path;?>') no-repeat;
																		<?php } else { ?>
																			background: none no-repeat; 
																		<?php } ?>;
																			background-size:cover;float:left">
																		<a <?php if(isset($original_path) && $original_path != ADMIN_IMAGE_PATH.'no_user.jpeg' ) 
																			{ ?> href="<?php echo $original_path; ?>" class="user_image_pop_up"  <?php } ?> 
																		title="View Photo"  ><img width="36" height="36" src="<?php echo $original_path;?>" ></a>
													</div>
													<div style="padding-left:50px;">
														<?php if(isset($value->Title) && $value->Title != ''	&&	isset($value->id) && $value->id != ''){
																 echo '<a class="recordView" href="EventsDetail?viewId='.$value->id.'">'.trim($value->Title).'</a>';
	  														  }else echo '-';?>
													</div>
													<div class="userAction" style="display:none;padding-left:50px;" id="userAction_<?php echo $value->id;?>">
														<div style="padding-top:6px;">
															<a href="EventsManage?editId=<?php if(isset($value->id) && $value->id != '') echo $value->id; ?>" title="Edit" alt="Edit" class=" pop_up1" style="float:left;" >Edit</a>
															<a href="EventsDetail?viewId=<?php if(isset($value->id) && $value->id != '') echo $value->id; ?>" title="View" alt="View" class="viewUser pop_up1">View</a>
															<a onclick="javascript:return confirm('Are you sure to delete?')" href="EventsList?delId=<?php if(isset($value->id) && $value->id != '') echo $value->id;?>" title="Delete" alt="Delete" class="deleteUser">Delete</a>
														</div>
													</div>
												</td>
												<td><?php if(isset($value->Description) && $value->Description != '' ){ echo $value->Description;}else echo '-';?></td>
												<td><?php if(isset($value->Location) && $value->Location != ''){ echo $value->Location;} else echo '-';?></td>
												<td><?php if(isset($value->TwitterHashtag) && $value->TwitterHashtag != ''){ echo $value->TwitterHashtag; }else echo '-';?></td>
												<td>
												<?php	
														if(isset($value->likeCount) && $value->likeCount >= 1)	{
														echo '<p style="padding-top:3px;"><b>Like(s) <span style="padding-left:7px;">: </span></b>';
													?> 
														<a href="EventLikedUsersList?cs=1&eventId=<?php if(isset($value->id) && $value->id != '') echo $value->id; ?>" class="pop_up cboxElement" title="Event Joined Users"><?php echo $value->likeCount.'</p>';?>
														</a>
														<?php }?>
												<?php 
														if(isset($joinedUsersList[$i]->joinedUserCount) && $joinedUsersList[$i]->joinedUserCount >= 1)	{
														  echo '<p style="padding-top:5px;"><b>Joined <span style="padding-left:5px;">: </span></b>';	?>
														<a href="EventJoinedUsers?cs=1&eventId=<?php if(isset($value->id) && $value->id != '') echo $value->id; ?>" class="pop_up cboxElement" title="Event Joined Users"><?php echo $joinedUsersList[$i]->joinedUserCount.'</p>'; ?></a>
														<?php }
														if(isset($joinedUsersList[$i]->joinedUserCount) && $joinedUsersList[$i]->joinedUserCount < 1	&&	isset($value->likeCount) && $value->likeCount < 1)
															echo " - ";
														?>
														
												</td>
												<td align="center">
													<?php 	if(isset($value->StartDate) && $value->StartDate != '0000-00-00 00:00:00')
																echo date('m/d/Y',strtotime($value->StartDate))." <div style='padding:3px;'>to</div>";
															else if(isset($value->EndDate) && $value->EndDate != '0000-00-00 00:00:00')
																echo " - <div>to</div>";
															if(isset($value->EndDate) && $value->EndDate != '0000-00-00 00:00:00')	
																echo date('m/d/Y',strtotime($value->EndDate)); 
															else if(isset($value->StartDate) && $value->StartDate != '0000-00-00 00:00:00'	)
																echo " - ";
															if(isset($value->StartDate) && $value->StartDate == '0000-00-00 00:00:00'	&&	isset($value->EndDate) && $value->EndDate == '0000-00-00 00:00:00')
																echo " - ";
													?>
												</td>			
											</tr>
											<?php $i++;
												}?>																		
										</table>
										<?php if(isset($eventsListResult) && is_array($eventsListResult) && count($eventsListResult) > 0){ 
											bulk_action(array(3=>'Delete'));
										?>
										
										<?php } ?>
										</form>
										<?php } else { ?>	
											<tr>
												<td colspan="16" align="center" style="color:red;">No Event found</td>
											</tr>
											<tr><td height="30"></td></tr>
											<?php } ?>
									</td>
								</tr>
								<tr><td height="10"></td></tr>
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
$(".datepicker").datepicker({
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
	closeText		:   "Close"
   });
$(document).ready(function() {		
	$(".pop_up").colorbox(
	{
			iframe:true,
			width:"55%", 
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
