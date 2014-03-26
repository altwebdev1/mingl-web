<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/LogController.php');
$logObj   =   new LogController();
$display   =   'none';
$class  =  $msg    = $cover_path = '';
global $link_type_array;
$display	=	"none";
$today		=	date('m-d-Y');
$where		=	' ';

if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	unset($_SESSION['sess_logtrack_to_date']);
	unset($_SESSION['sess_logtrack_from_date']);
	unset($_SESSION['sess_logtrack_process']);
	unset($_SESSION['sess_logtrack_searchUserName']);
	unset($_SESSION['sess_logtrack_searchIP']);
}
if(isset($_POST['Search']) && $_POST['Search'] != ''){
	destroyPagingControlsVariables();
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	$_SESSION['sess_logtrack_to_date']      	= $_POST['to_date'];
	$_SESSION['sess_logtrack_from_date']     	= $_POST['from_date']; 
	//$_SESSION['sess_logtrack_process']       	= $_POST['process_type'];
	$_SESSION['sess_logtrack_searchUserName']	= trim($_POST['searchUserName']);
	$_SESSION['sess_logtrack_searchIP']      	= trim($_POST['searchIP']);
	//action_type
}
if(!isset($_SESSION['sess_logtrack_to_date'])) 
	$_SESSION['sess_logtrack_to_date']	=	date('Y-m-d');	//=	$today;//
if(!isset($_SESSION['sess_logtrack_from_date'])) 
	$_SESSION['sess_logtrack_from_date']	=	date('Y-m-d');
setPagingControlValues('l.id',ADMIN_PER_PAGE_LIMIT);
$logtracksResult	=	$logObj->logtrackDetails($where);
$tot_rec 		 = $logObj->getTotalRecordCount();
if($tot_rec==0 && !is_array($logtracksResult)) {
	$_SESSION['curpage'] = 1;
$logtracksResult	=	$logObj->logtrackDetails($where);
}
//echo '<pre>';print_r($actionCounts);echo '</pre>';
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
						 <div class="box-header"><h2><i class="icon_userlist"></i>Log Tracking</h2>
						 <span style="float:right"></span></div>
				            <table cellpadding="0" cellspacing="0" border="0" width="98%" align="center" class="headertable">
								<tr><td height="20"></td></tr>
								<tr>
									<td colspan="2">
										<form name="search_category" action="LogTracking" method="post">
				                           <table align="center" cellpadding="0" cellspacing="0" border="0" class="filter_form" width="100%">									       
												<tr><td height="15"></td></tr>
												<tr>													
													<td width="7%" style="padding-left:20px;"><label>User name</label></td>
													<td width="3%" align="center">:</td>
													<td align="left"  height="40">
														<input  type="text" class="input " title="User name" name="searchUserName" 
										value="<?php if(isset($_SESSION['sess_logtrack_searchUserName']) && $_SESSION['sess_logtrack_searchUserName'] != '') echo $_SESSION['sess_logtrack_searchUserName'];?>">
				
													</td>
													<td width="10%" style="padding-left:20px;"><label>IP Address</label></td>
													<td width="3%" align="center">:</td>
													<td align="left"  height="40">
														<input type="text" class="input"  title="IP Address" name="searchIP" value="<?php if(isset($_SESSION['sess_logtrack_searchIP']) && $_SESSION['sess_logtrack_searchIP'] != '') echo $_SESSION['sess_logtrack_searchIP'];?>">
													</td>
													
													
												</tr>
												<tr>
													<td width="10%" style="padding-left:20px;"><label>Start Time</label></td>
													<td width="3%" align="center">:</td>
													<td align="left"  height="40">
														<input  type="text" class="input medium datepicker" autocomplete="off" title="Select Date" name="from_date" 
										value="<?php if(isset($_SESSION['sess_logtrack_from_date']) && $_SESSION['sess_logtrack_from_date'] != '') echo date('m/d/Y',strtotime($_SESSION['sess_logtrack_from_date']));?>">
													</td>
													<td width="10%" style="padding-left:20px;"><label>End Time</label></td>
													<td width="3%" align="center">:</td>
													<td align="left"  height="40">
														<input type="text" class="input medium datepicker" autocomplete="off"  title="Select Date" name="to_date" value="<?php if(isset($_SESSION['sess_logtrack_to_date']) && $_SESSION['sess_logtrack_to_date'] != '') echo date('m/d/Y',strtotime($_SESSION['sess_logtrack_to_date']));?>">
													</td>
													<!-- <td width="10%" style="padding-left:20px;"><label>Social Network</label></td>
													<td width="3%" align="center">:</td>
													<td height="40" align="left" >														
														<select name="social" id="social" style="width:120px;">
															<option value="">Select</option>
															<?php //if(isset($shareTypeArray) && is_array($shareTypeArray) && count($shareTypeArray) > 0 ) { 
																	//foreach($shareTypeArray as $sharekey=>$sharevalue) { ?>
															<option value="<?php //echo $sharekey;?>" <?php //if(isset($_SESSION['receiptmatch_sess_user_share']) && $_SESSION['receiptmatch_sess_user_share'] == $sharekey && $_SESSION['receiptmatch_sess_user_share'] != '') echo 'selected';?> ><?php //echo $sharevalue; ?></option>
															<?php // } } ?>
														</select>
													</td>
													-->
												</tr>
												
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
												<?php if(isset($logtracksResult) && is_array($logtracksResult) && count($logtracksResult) > 0){ ?>
												<td align="left" width="20%">Total Log(s)&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
												<?php } ?>
												<td align="center">
														<?php if(isset($logtracksResult)	&&	is_array($logtracksResult) && count($logtracksResult) > 0 ) {
														 	pagingControlLatest($tot_rec,'LogTracking'); ?>
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
							<?php if(isset($logtracksResult) && is_array($logtracksResult) && count($logtracksResult) > 0 ) { ?>
							  <form action="LogTracking" class="l_form" name="LogTrackingForm" id="LogTrackingForm"  method="post"> 
							<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table user_actions">
								<tr>
									<th width="1%" class="algn_cntr">S.no</th>
									<th width="12%">User Name</th>
									<th width="15%">URL</th>
									<th width="8%">Data</th>
									<th width="5%">Device</th>
									<th width="2%">Time</th>
									<th width="6%">Duration&nbsp;&nbsp;</th>
								</tr>

								<?php foreach($logtracksResult as $key=>$value){
													if((isset($value->FirstName)	&& $value->FirstName != '') 	&& (isset($value->LastName)	&& $value->LastName != '') )
														$userName	=	ucfirst($value->FirstName).' '.ucfirst($value->LastName);
													else if((isset($value->FirstName)	&& $value->FirstName != '') )	
														$userName	=	 ucfirst($value->FirstName);
													else if((isset($value->LastName)	&& $value->LastName != '') )	
														$userName	=	ucfirst($value->LastName);
													
								 ?>									
								<tr>
									<td class="algn_cntr"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
									<td align="left">
										<?php  //if(isset($value->UserName)	&&	$value->UserName !='') echo ucfirst($value->UserName);//ucfirst($value->firstName).'&nbsp;'.ucfirst($value->lastName) ; 
												//echo $value->logId;
												if(isset($userName)	&&	$userName !='') echo $userName;
												else echo '-';?>								<br><br>
										<p><b class="head_color">IP :</b>
										<?php if(isset($value->ip_address)	&&	$value->ip_address !='') echo $value->ip_address; else echo '-';?>
										</p>
									</td>
									<td align="left">
									<?php if(isset($value->log_stat)	&&	 ($value->log_stat ==1	||	$value->log_stat ==2)){ 
											echo '-';
										} else {?>
									<?php 	if(isset($value->url)	&&	$value->url !='') {
												if (SERVER)		echo "https://".$value->url;
												else 			echo "http://".$value->url;
											}
										  	else echo '-';?>
									<br><br><p><b class="head_color">Method : </b><?php if(isset($value->method)	&&	$value->method !='') echo $value->method; else echo '-';?></p>
									<?php }?>
									</td>
<!--								<td align="left"><?php //if(isset($value->method)	&&	$value->method !='') echo $value->method; else echo '-';?></td>	-->
									<td align="left" class="brk_wrd_cell">
										<?php if(isset($value->log_stat)	&&	 ($value->log_stat ==1	||	$value->log_stat ==2)){ 
											echo '-';
										} else {?>
										<p class="brk_wrd brk_wrd_cell"><b class="head_color">Request : </b><?php if(isset($value->content)	&&	$value->content !='') echo ''.$value->content.'<br><br>'; else echo '-<br><br>';?></p>
										<div class="brk_wrd brk_wrd_cell response_msg" ><b class="head_color">Response : </b><?php if(isset($value->response)	&&	$value->response !='') 
										echo strip_tags($value->response);
										//  echo $value->response;
										  else echo '-';?></div>
										<?php } ?>
<!--										<textarea class="cledito"  name="description" id="description"><?php //if(isset($value->response) && $value->response != '') echo $value->response;?></textarea>
-->
									</td>

<!--									<td align="center"><p class="brk_wrd" ><?php //if(isset($value->response)	&&	$value->response !='') echo $value->response; else echo '-';?></p></td>	-->
									<td align="left"><?php if(isset($value->device_type)	&&	$value->device_type !='') {
																if(array_key_exists($value->device_type, $device_type_array))
																	echo $device_type_array[$value->device_type];
																else echo '-';
															}else echo '-';
															//echo $value->device_type;
															//$device_type_array?></td>
									<td align="center">
											<div class="div_no_wrap">
											<?php if(isset($value->log_stat)	&&	 $value->log_stat ==1	){
														if(isset($value->start_time) && $value->start_time != '0000-00-00 00:00:00'){
															$gmt_current_start_time = convertIntocheckinGmtSite($value->start_time);
															$start_time	=  displayConversationDateTime($gmt_current_start_time,$_SESSION['intermingl_ses_from_timeZone']);
															echo '<br>'.$start_time; 
														}else echo '<br>-';
												} 
												else if(isset($value->log_stat)	&&	 $value->log_stat ==2	){
														if(isset($value->end_time) && $value->end_time != '0000-00-00 00:00:00'){
															$gmt_current_end_time = convertIntocheckinGmtSite($value->end_time);
															$end_time	=  displayConversationDateTime($gmt_current_end_time,$_SESSION['intermingl_ses_from_timeZone']);
															echo '<br>'.$end_time; 
														}else echo '<br>-';
												} 
												else { ?>
											<?php 	if(isset($value->start_time) && $value->start_time != '0000-00-00 00:00:00'){
														$gmt_current_start_time = convertIntocheckinGmtSite($value->start_time);
														$start_time	=  displayConversationDateTime($gmt_current_start_time,$_SESSION['intermingl_ses_from_timeZone']);
														echo $start_time; 
													}else echo '-';?>
<!--											</br><span style="text-align:center">to</span></br>	-->
												<p align="center" style="margin:0 0 0 1px;">to</p>
											<?php 	if(isset($value->end_time) && $value->end_time != '0000-00-00 00:00:00'){
														$gmt_current_end_time = convertIntocheckinGmtSite($value->end_time);
														$end_time	=  displayConversationDateTime($gmt_current_end_time,$_SESSION['intermingl_ses_from_timeZone']);
														echo $end_time; 
													}else echo '-';?>
											<?php } ?>
											</div>
									</td>
<!--
									<td align="left"><?php //if(isset($value->ip_address)	&&	$value->ip_address !='') echo $value->ip_address; else echo '-';?></td>
-->
									<td align="left" ><?php if(isset($value->execution_time)	&&	$value->execution_time > 0) echo round($value->execution_time, 3).' sec'; else echo '-';?></td>
									<!-- <td align="left"><?php if(isset($value->log_stat)	&&	$value->log_stat > 0) echo $log_type_array[$value->log_stat]; else echo '-';?></td> -->
								</tr>
								<?php } ?>
								</form>
										<?php } else { ?>	
											<tr>
												<td colspan="16" align="center" style="color:red;">No result found</td>
											</tr>
											<tr><td height="30"></td></tr>
										<?php    } ?>
								
								</table>
									</td>
								</tr>
							<tr><td height="30"></td></tr>	
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
   </script>
</html>
