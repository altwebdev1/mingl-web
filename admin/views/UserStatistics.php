<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
require_once('controllers/UserController.php');
$userObj   =   new UserController();
require_once('controllers/ManagementController.php');
$managementObj   =   new ManagementController();
require_once('controllers/EventsController.php');
$eventObj   =   new EventsController();
$condition = '';
$tot_rec = $user_count  = $post_count  = $like_count  = $comment_count  = 0;
$display = 'none';
$from_date = $to_date =  date('Y-m-d');
if(isset($_GET['cs']) && $_GET['cs'] == 1 ){
	$date = date('Y-m-d');
	$_POST['search_date'] = date('m/d/Y');
}
$start_year = 2010;
$end_year   = 2020;$flag = 0;
$condition  = '';$condition_filter = '';$condition_user = '';
$year = '';
$month = '';
if(isset($_POST['Search']) && $_POST['Search'] != ''){
	if(isset($_POST['search_from_date']) && $_POST['search_from_date'] !='')
		$from_date	    = date('Y-m-d',strtotime($_POST['search_from_date']));
	else
		$from_date	    = date('Y-m-d');
	if(isset($_POST['search_to_date']) && $_POST['search_to_date'] !='')
		$to_date	    = date('Y-m-d',strtotime($_POST['search_to_date']));
	else
		$to_date	    = date('Y-m-d');
}




$condition = '';
$fields          = " count(id) as user_count ";
if($from_date != '0000-00-00' && $from_date != '1970-01-01' &&  $from_date != '' &&  $to_date != '0000-00-00' && $to_date != '1970-01-01' &&  $to_date != ''){
	$condition      .= "  and date(DateCreated) >=  '".$from_date."' and date(DateCreated) <=  '".$to_date."'"; 
}
$user_condition 		=	'Status in(1,2) '.$condition;
$tag_condition 			=	' and Status in(1,2) '.$condition;
$interest_condition		=	' and Status =1  '.$condition;
$event_condition		=	' Status =1  '.$condition;
//$comment_condition		=	' and Status =1  '.$condition;
$userResult   			= 	$userObj->selectUserDetails($fields,$user_condition);
$field1         		= 	" count(id) as connection_count ";
$connectionResult  		= 	$managementObj->selectConnectionDetails($field1,$condition);
$field2          		= 	" count(id) as tag_count ";
$TagResult		   		=	$managementObj->selectTagDetails($field2,$tag_condition);
$field3          		= 	" count(id) as interest_count ";
$InterestResult	   		= 	$managementObj->selectInterestDetails($field3,$interest_condition);
$field4          		= 	" count(id) as goal_count ";
$GoalResult		   		= 	$managementObj->selectGoalDetails($field4,$interest_condition);
$field5          		= 	" count(id) as comment_count ";
$CommentResult	   		= 	$managementObj->selectCommentDetails($field5,$interest_condition);
$field6          		= 	" count(id) as event_count ";
$EventResult	   		= 	$eventObj->selectEventDetails($field6,$event_condition);
if(isset($userResult) && is_array($userResult) && count($userResult)>0 ){
		$totalCount =  $userResult[0]->user_count;
}
if(isset($connectionResult) && is_array($connectionResult) && count($connectionResult)>0 ){
		$connectionCount =  $connectionResult[0]->connection_count;
}
if(isset($TagResult) && is_array($TagResult) && count($TagResult)>0 ){
		$TagCount =  $TagResult[0]->tag_count;
}
if(isset($InterestResult) && is_array($InterestResult) && count($InterestResult)>0 ){
		$interestCount =  $InterestResult[0]->interest_count;
}
if(isset($GoalResult) && is_array($GoalResult) && count($GoalResult)>0 ){
		$goalCount =  $GoalResult[0]->goal_count;
}
if(isset($CommentResult) && is_array($CommentResult) && count($CommentResult)>0 ){
		$commentCount =  $CommentResult[0]->comment_count;
}
if(isset($EventResult) && is_array($EventResult) && count($EventResult)>0 ){
		$eventCount =  $EventResult[0]->event_count;
}
commonHead(); ?>
<body>
	<table cellpadding="0" cellspacing="0" border="0" width="100%" align="center" height="100%">
		<tr>
			<td align="center">
				<table cellpadding="0" cellspacing="0" border="0" width="95%" align="center">					
					<tr><td colspan="2" class="headermenu"><?php top_header(); ?></td></tr>				    
					<tr>
						<td colspan="2">
						 	 <div id="content_3" class="content">		
							 	<table cellpadding="0" cellspacing="0" border="0" width="100%" align="center">
									 <tr><td height="10"></td></tr>
									 <tr><td><span><h2>User Statistics List</h2></span></td></tr>									
									<tr><td height="20"></td></tr>									
									<tr><td height="20"></td></tr>
									<tr>
					                    <td valign="top" align="center" colspan="2">											
											 <form name="search_report" action="UserStatistics" method="post">
					                           <table align="center" cellpadding="0" cellspacing="0" border="0" class="filter_form" width="100%">									       
													<tr><td height="15"></td></tr>
													<tr>													
														<td width="30%" style="padding-right:20px;" align="right"><label>From Date</label></td>
														<td width="3%" align="center">:</td>
														<td align="left"  height="40" width="12%">
															<input style="width:90px" type="text"  maxlength="10" class="input" name="search_from_date" id="search_from_date" title="Select Date" value="<?php if(isset($from_date) && $from_date != '') echo date('m/d/Y',strtotime($from_date)); else echo '';?>" >
														</td>
														<td width="10%" style="padding-right:20px;" align="right"><label>To Date</label></td>
														<td width="3%" align="center">:</td>
														<td align="left"  height="40" width="12%">
															<input style="width:90px" type="text"  maxlength="10" class="input" name="search_to_date" id="search_to_date" title="Select Date" value="<?php if(isset($to_date) && $to_date != '') echo date('m/d/Y',strtotime($to_date)); else echo '';?>" >
														</td>
														
														<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
														<td align="left"><input type="submit" class="submit_button" name="Search" id="Search" value="Search"></td>
														<tr><td height="15"></td></tr>														
													</tr>													
												 </table>
											  </form>	
					                    </td>
					               	</tr>
									<tr><td height="20"></td></tr>
									<tr>
										<td align="center">
											<div class="user_statics">
											 <div class="user_statics_table">
										   	<table border="1" cellpadding="0" cellspacing="0" width="40%" class="user_table" >
												<tr>
													<th>Process</th>
													<th>Total</th>
												</tr>
												<tr>
													<td>No.of users registered</td>
													<td style="text-align: center"><?php if(isset($totalCount) && $totalCount != ''){ echo $totalCount ;} else echo '0';?>
													
													</td>		
												</tr>
												<tr>
													<td>No.of connections</td>
													<td style="text-align: center"><?php if(isset($connectionCount) && $connectionCount != ''){ echo $connectionCount ;} else echo '0';?></td>		
												</tr>	
												<tr>
													<td>No.of tags</td>
													<td style="text-align: center"><?php if(isset($TagCount) && $TagCount != ''){ echo $TagCount ;} else echo '0';?></td>		
												</tr>
												<tr>
													<td>No.of interest</td>
													<td style="text-align: center"><?php if(isset($interestCount) && $interestCount != ''){ echo $interestCount ;} else echo '0';?></td>		
												</tr>	
												<tr>
													<td>No.of goals</td>
													<td style="text-align: center"><?php if(isset($goalCount) && $goalCount != ''){ echo $goalCount ;} else echo '0';?></td>		
												</tr>	
												<tr>
													<td>No.of comments</td>
													<td style="text-align: center"><?php if(isset($commentCount) && $commentCount != ''){ echo $commentCount ;} else echo '0';?></td>		
												</tr>	
												<tr>
													<td>No.of events</td>
													<td style="text-align: center"><?php if(isset($eventCount) && $eventCount != ''){ echo $eventCount ;} else echo '0';?></td>		
												</tr>																								
											</table>
											</div>
											</div>
										</td>
									</tr>
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
	
	$(document).ready(function(){
		$("#search_from_date").datepicker({
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
	$("#search_to_date").datepicker({
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
});
		
		
	
</script>
</html>