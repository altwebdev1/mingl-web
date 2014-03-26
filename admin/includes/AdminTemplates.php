<?php

function commonHead() { 
header('Content-Type:text/html; charset=UTF-8');
ini_set('default_charset', 'UTF-8');
?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html>
        <head>
            <title><?php echo SITE_TITLE; ?></title>			
			<link rel="STYLESHEET" type="text/css" href="<?php echo ADMIN_STYLE_PATH; ?>admin_styles.css">	
			<link rel="STYLESHEET" type="text/css" href="<?php echo ADMIN_STYLE_PATH; ?>emoji_styles.css">								                              
       		<link rel="STYLESHEET" type="text/css" href="<?php echo ADMIN_STYLE_PATH; ?>colorbox.css"> 
			<link rel="STYLESHEET" type="text/css" href="<?php echo ADMIN_STYLE_PATH; ?>jquery-ui.css"> 
			<link rel="STYLESHEET" type="text/css" href="<?php echo ADMIN_STYLE_PATH; ?>jquery.ui.theme.css"> 
			<link rel="icon" href="<?php echo ADMIN_IMAGE_PATH; ?>favicon.ico" type="image/x-icon" />
			<link rel="shortcut icon" href="<?php echo ADMIN_IMAGE_PATH; ?>favicon.ico" type="image/x-icon" />
			<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	   </head>
 <?php } 
   function top_header() { 
   	 $main_link_array = array();

        $menu_management_array = array(
            'Settings' => array(
                'General Settings' => array('GeneralSettings'),
                'Change Password' => array('ChangePassword'),
				'CMS' => array('StaticPages'),
				//'Distance Settings' => array('DistanceSettings'),
            ),
			'User' => array(
				'Add User'	 => array('UserManage'),
                'User List' => array('UserList?cs=1'),
			),
			'Management' => array(
				'Goal List'	   	=>	array('GoalList?cs=1','GoalDetail'),
				'Tag List'		=>	array('TagList?cs=1','TagList'),
				'Interest List'		=>	array('InterestList?cs=1','InterestDetails'),
			),
			'Webservice' => array(
                'Service List'	=> array('ServiceList?cs=1'),
            ),			
			'Report' => array(
                'User Statistics'	=> array('UserStatistics?cs=1'),
				'Share Tracking'   => array('ShareTrackingList?cs=1'),
            ),
			'Event' => array(
                'Event List'	=> array('EventsList?cs=1'),
            ),

        );
        $main_link_array['Settings'] 				= 	array('GeneralSettings', 'ChangePassword','StaticPages','DistanceSettings');
		$main_link_array['User'] 					=	array('UserManage','UserDetail','UserList','Messages','Activity');
		$main_link_array['Management'] 				=	array('GoalList','InterestList','TagList');
		$main_link_array['Webservice'] 				=	array('ServiceList','ServiceManage','ServiceDetail');
		$main_link_array['Report'] 					=	array('UserStatistics','ShareTrackingList');
		$main_link_array['Event'] 					=	array('EventsList','EventsManage','EventsDetail');
		$page = getCurrPage();
		if(isset($_GET['st']) && $_GET['st']!='') {
			$page_st = 'st='.$_GET['st'];
		}
   ?>
		<table border="0" cellpadding="0" cellspacing="0" align="center" width="100%">
			<tr><td height="10"></td></tr>
			<tr>
				<td width="80%">
					<div class="logo_text">
						<a class="logo" title="Mingle" href="login"><strong>Mingl</strong></a>
					</div>	
				</td>
				<td valign="top"><div class="admin_text">Administrator</div>
				<div class="welcome_head"> <span>Welcome , <a title="Admin" href="#">Admin</a>&nbsp;</span><a class="logout" title="Logout" href="Logout">logout</a></div></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
			
 <?php } 
 function commonFooter() { ?>
 	<!-- jquery.validate -->
 	<script src="<?php echo ADMIN_SCRIPT_PATH; ?>jquery-latest.js" type="text/javascript"></script>
	<script src="<?php echo ADMIN_SCRIPT_PATH; ?>Util.js" type="text/javascript"></script>
	<script src="<?php echo ADMIN_SCRIPT_PATH; ?>jquery.validate.js" type="text/javascript"></script>
	<script src="<?php echo ADMIN_SCRIPT_PATH; ?>Validate.js" type="text/javascript"></script>
	<script src="<?php echo ADMIN_SCRIPT_PATH; ?>AjaxDirector.js" type="text/javascript"></script>
	<script src="<?php echo ADMIN_SCRIPT_PATH; ?>AjaxFileUpload.js" type="text/javascript"></script>
	<script src="<?php echo ADMIN_SCRIPT_PATH; ?>jquery.colorbox.js" type="text/javascript"></script>
	<script src="<?php echo ADMIN_SCRIPT_PATH; ?>jquery-ui.js" type="text/javascript"></script>
 <?php } ?>
 
 	<?php  
	function side_bar(){ 
		$page = getCurrPage();
	if(isset($page ) && $page == 'GeneralSettings')
		$general_settings		=	'sel';
	if(isset($page ) && $page == 'ChangePassword')
		$change_pwd				=	'sel';
	if(isset($page ) && $page == 'StaticPages')
		$cms_page				=	'sel';
	if(isset($page ) && $page == 'UserList')
		$user_list			=	'sel';
	if(isset($page ) && $page == 'CardsList')
		$card_list			=	'sel';
	if(isset($page ) && ( $page == 'UserManage')	||	$page == 'UserDetail'){
			$add_user				=	'sel';
	}
	if(isset($page ) && $page == 'GoalList'){
			$goal_list				=	'sel';
	}
	if(isset($page ) && $page == 'TagList'){
			$tag_list			=	'sel';
	}
	if(isset($page ) && $page == 'InterestList'){
			$interest_list				=	'sel';
	}
	if(isset($page)	&&	$page == 'EventsManage')	{
		$event_manage	=	'sel';
	}
	if(isset($page ) && ( $page == 'EventsList')	||	$page == 'EventsDetail'){
			$event_list				=	'sel';
	}
/*	if(isset($page ) && $page == 'addPoster'){
		if(isset($_GET['posters']) && $_GET['posters'] != ''){
			$participant_list		=	'sel';
		}
		else if(isset($_GET['editId']) && $_GET['editId'] != ''){
			$add_poster				=	'sel';
			//$poster_list			=	'sel';
		}
		else
			$add_poster				=	'sel';
	}
	if(isset($page ) && $page == 'viewPoster'){
		if(isset($_GET['posters']) && $_GET['posters'] != '')
			$participant_list		=	'sel';
		else
			$add_poster				=	'sel';
			//$poster_list			=	'sel';
	}
	if(isset($page ) && ($page == 'posterList'))
		$poster_list			=	'sel';
	if(isset($page ) && $page == 'addInterview'){
		 //if(isset($_GET['editId']) && $_GET['editId'] != '')
		 	$add_interview			=	'sel';
			//$interview_list			=	'sel';
		//else
			//$add_interview			=	'sel';
		
	}
	if(isset($page ) && ( $page == 'interviewList' ) || $page == 'userAttendedList')
		$interview_list			=	'sel';
	if(isset($page ) && (  $page == 'viewInterview' ) )// || $page == 'userAttendedList'
		$add_interview			=	'sel';
	if(isset($page ) && ( $page == 'participantList' || $page == 'viewAnswer' || $page == 'viewPoster') )
		$participant_list			=	'sel';
	
	if(isset($page ) && $page == 'viewUser'){
		if(isset($_GET['interviewId']) && $_GET['interviewId'] != '')
			$interview_list			=	'sel';
		else if(isset($_GET['participant']) && $_GET['participant'] != '')
			$participant_list			=	'sel';
		else
			$participant_list			=	'sel';
	}
	

	*/
	if(isset($page ) && ($page == 'ServiceList' || $page=='ServiceDetail' ))	
	    $service_list		=	'sel';
	if(isset($page ) && $page == 'ServiceManage' )	{
		 if(isset($_GET['editId']) && $_GET['editId'] != '')
		 	$service_add		=	'sel';
			//$service_list		=	'sel';
		 else
			$service_add		=	'sel';
	}
	if(isset($page ) && $page == 'Statistics' )	
		$stat_list		=	'sel';
	if(isset($page ) && $page == 'Analytics' )	
		$analytic_list		=	'sel';
	if(isset($page ) && $page == 'LogTracking' )	
		$log_list		=	'sel';
		
		?>
	<div class="span2 main-menu-span">
		<div class="we ll nav-collapse sidebar-nav">
			<ul class="nav nav-tabs nav-stacked main-menu">
				<li class="nav-header hidden-tablet">Menu</li>
				<li>
					<span onclick="left_pannel('admin_settings');" class="nav_main"><i style="margin:0px 12px 0px 4px;" class="icon_Mainset"></i><span id="admin_settings_span" <?php if(($page == 'GeneralSettings') || ($page == 'ChangePassword') || ($page == 'StaticPages') ) { ?> class="hidden-tablet upArrow" <?php } else { ?> class="hidden-tablet downarrowclass"<?php } ?>><a href="javascript:void(0);" >Settings</a></span></span>
					<ul class="nav nav-tabs nav-stacked slideArrow" id="admin_settings" style="<?php if(($page == 'GeneralSettings') || ($page == 'ChangePassword') || ($page == 'StaticPages') ) { ?> display:block <?php } else { ?> display:none <?php } ?>">
						<li><a  class="<?php if(isset($general_settings) && $general_settings != '') echo $general_settings; ?>" href="<?php echo ADMIN_SITE_PATH;?>/GeneralSettings" title="General Settings"><i class="icon_Gset"></i><span class="hidden-tablet"> General Settings</span></a></li>
						<li><a class="<?php if(isset($change_pwd) && $change_pwd != '') echo $change_pwd; ?>" href="<?php echo ADMIN_SITE_PATH;?>/ChangePassword" title="Change Password"><i class="icon_chgpass"></i><span class="hidden-tablet">Change Password</span></a></li> <!-- class="active" -->
						<li><a class="<?php if(isset($cms_page) && $cms_page != '') echo $cms_page; ?>" href="<?php echo ADMIN_SITE_PATH;?>/StaticPages" title="CMS"><i class="icon-edit" style="padding-right:3px;"></i><span class="hidden-tablet">CMS</span></a></li>
					</ul>
				</li>
				<li>
					<span onclick="left_pannel('admin_user_list');" class="nav_main"><i style="margin:0px 1px 0px 6px;"  class="icon_user"></i><span id="admin_user_list_span" <?php if(($page == 'UserList')	||	($page=='UserManage') ||	($page == 'UserDetail')  ||	($page=='CardsList')) { ?> class="hidden-tablet upArrow" <?php } else { ?> class="hidden-tablet downarrowclass"<?php } ?>><a href="javascript:void(0);" >User</a></span></span>
					<ul  class="nav nav-tabs nav-stacked main-menu slideArrow" id="admin_user_list" style="<?php if(($page == 'UserList')	||	($page=='UserManage') ||	($page == 'UserDetail') ||	($page=='CardsList')) { ?>display:block<?php } else { ?>display:none<?php } ?>">
						
						<li><a class="<?php if(isset($user_list) && $user_list != '') echo $user_list; ?>"  href="<?php echo ADMIN_SITE_PATH;?>/UsersList?cs=1" title="Users List"><i class="icon_userlist"></i><span class="hidden-tablet">User List</span></a></li>
						<li><a class="<?php if(isset($add_user) && $add_user != '') echo $add_user; ?>"  href="<?php echo ADMIN_SITE_PATH;?>/UserManage?cs=1" title="Add User"><i class="icon_adduser"></i><span class="hidden-tablet">Add User</span></a></li>
						<li><a class="<?php if(isset($card_list) && $card_list != '') echo $card_list; ?>"  href="<?php echo ADMIN_SITE_PATH;?>/CardsList?cs=1" title="Card List"><i class="icon_userlist"></i><span class="hidden-tablet">Card List</span></a></li>
					</ul>
				</li>
				<li>
					<span onclick="left_pannel('admin_management');" class="nav_main"><i style="margin:0px 9px 0px 8px;" class="icon_management"></i><span id="admin_management_span" <?php if(($page == 'GoalList') || ($page == 'TagList') || ($page == 'InterestList') ) { ?> class="hidden-tablet upArrow" <?php } else { ?> class="hidden-tablet downarrowclass"<?php } ?>><a href="javascript:void(0);" >Management</a></span></span>
					<ul class="nav nav-tabs nav-stacked slideArrow" id="admin_management" style="<?php if(($page == 'GoalList') || ($page == 'TagList') || ($page == 'InterestList') ) { ?> display:block <?php } else { ?> display:none <?php } ?>">
						<li><a  class="<?php if(isset($goal_list) && $goal_list != '') echo $goal_list; ?>" href="<?php echo ADMIN_SITE_PATH;?>/GoalList?cs=1" title="Goal List"><i class="icon_goal_list"></i><span class="hidden-tablet">Goal List</span></a></li>
						<li><a class="<?php if(isset($tag_list) && $tag_list != '') echo $tag_list; ?>" href="<?php echo ADMIN_SITE_PATH;?>/TagList?cs=1" title="Tag List"><i class="icon_tag_list"></i><span class="hidden-tablet">Tag List</span></a></li> <!-- class="active" -->
						<li><a class="<?php if(isset($interest_list) && $interest_list != '') echo $interest_list; ?>" href="<?php echo ADMIN_SITE_PATH;?>/InterestList?cs=1" title="Interest List"><i class="icon-interest_list"></i><span class="hidden-tablet">Interest List</span></a></li>
					</ul>
				</li>
				<li>
					<span onclick="left_pannel('admin_events');" class="nav_main"><i style="margin:0px 0px 0px 8px;" class="icon_event"></i><span id="admin_events_span" <?php if(($page == 'EventsManage') || ($page == 'EventsDetail') || ($page == 'EventsList') ) { ?> class="hidden-tablet upArrow" <?php } else { ?> class="hidden-tablet downarrowclass"<?php } ?>><a href="javascript:void(0);" >Event</a></span></span>
					<ul class="nav nav-tabs nav-stacked slideArrow" id="admin_events" style="<?php if(($page == 'EventsList') || ($page == 'EventsManage') || ($page == 'EventsDetail') ) { ?> display:block <?php } else { ?> display:none <?php } ?>">
						<li><a  class="<?php if(isset($event_list) && $event_list != '') echo $event_list; ?>" href="#<?php //echo ADMIN_SITE_PATH;?>" title="Events List"><i class="icon_event_list"></i><span class="hidden-tablet">Event List</span></a></li>
						<li><a class="<?php if(isset($event_manage) && $event_manage != '') echo $event_manage; ?>" href="#<?php //echo ADMIN_SITE_PATH;?>" title="Events Manage"><i class="icon_add_event"></i><span class="hidden-tablet">Add Event</span></a></li> <!-- class="active" -->
					<!--	<li><a class="<?php // if(isset($interest_list) && $interest_list != '') echo $interest_list; ?>" href="<?php // echo ADMIN_SITE_PATH;?>/InterestList?cs=1" title="Interest List"><i class="icon-edit"></i><span class="hidden-tablet">Interest List</span></a></li> -->
					</ul>
				</li>
				<li>
					<span onclick="left_pannel('admin_service');" class="nav_main"><i style="margin:0px 4px 0px 4px;" class="icon_service"></i><span id="admin_service_span" <?php if(($page == 'ServiceList') || ($page == 'ServiceManage') || ($page == 'ServiceDetail') ) { ?> class="hidden-tablet upArrow" <?php } else { ?> class="hidden-tablet downarrowclass"<?php } ?>><a href="javascript:void(0);" >Service</a></span></span>
					<ul class="nav nav-tabs nav-stacked slideArrow" id="admin_service" style="<?php if(($page == 'ServiceList') || ($page == 'ServiceManage') || ($page == 'ServiceDetail')) { ?> display:block <?php } else { ?> display:none <?php } ?>">
						<li><a  class="<?php if(isset($service_list) && $service_list != '') echo $service_list; ?>" href="#<?php //echo ADMIN_SITE_PATH;?>" title="Service List"><i class="icon_service_list"></i><span class="hidden-tablet">Service List</span></a></li>
						<li><a class="<?php if(isset($service_add) && $service_add != '') echo $service_add; ?>" href="#<?php //echo ADMIN_SITE_PATH;?>" title="Service Manage"><i class="icon_add_service"></i><span class="hidden-tablet">Add Service</span></a></li> <!-- class="active" -->
					
					</ul>
				</li>
				<!--
				<li>
					<span onclick="left_pannel('admin_statistics');" class="nav_main"><i style="margin:0px 0px 0px 8px;" class="icon_statistics"></i><span id="admin_statistics_span" <?php // if(($page == 'Statistics') ) { ?> class="hidden-tablet upArrow" <?php   // } else { ?> class="hidden-tablet downarrowclass"<?php //  } ?>><a href="javascript:void(0);" >Statistics</a></span></span>
					<ul class="nav nav-tabs nav-stacked slideArrow" id="admin_statistics" style="<?php // if(($page == 'Statistics') ) { ?> display:block <?php // } else { ?> display:none <?php // } ?>">
						<li><a  class="<?php //if(isset($stat_list) && $stat_list != '') echo $stat_list; ?>" href="<?php //echo ADMIN_SITE_PATH;?>/Statistics?cs=1" title="Statistics List"><i class="icon_statistics"></i><span class="hidden-tablet">Statistics</span></a></li>
					</ul>
				</li>				
				<li>
					<span onclick="left_pannel('admin_analytics');" class="nav_main"><i style="margin:0px 0px 0px 8px;" class="icon_analytics"></i><span id="admin_analytics_span" <?php if(($page == 'Analytics') ) { ?> class="hidden-tablet upArrow" <?php } else { ?> class="hidden-tablet downarrowclass"<?php } ?>><a href="javascript:void(0);" >Analytics</a></span></span>
					<ul class="nav nav-tabs nav-stacked slideArrow" id="admin_analytics" style="<?php //if(($page == 'Analytics') ) { ?> display:block <?php //  } else { ?> display:none <?php  // } ?>">
						<li><a  class="<?php //if(isset($analytic_list) && $analytic_list != '') echo $analytic_list; ?>" href="<?php //echo ADMIN_SITE_PATH;?>/Analytics?cs=1" title="Analytics List"><i class="icon_analytics"></i><span class="hidden-tablet">Analytics</span></a></li>
					</ul>
				</li>
				-->
				<li>
					<span onclick="left_pannel('admin_logtracking');" class="nav_main"><i style="margin:0px 0px 0px 8px;" class="icon_log_tracking"></i><span id="admin_logtracking_span" <?php if(($page == 'LogTracking') ) { ?> class="hidden-tablet upArrow" <?php } else { ?> class="hidden-tablet downarrowclass"<?php } ?>><a href="javascript:void(0);" >Log Tracking</a></span></span>
					<ul class="nav nav-tabs nav-stacked slideArrow" id="admin_logtracking" style="<?php if(($page == 'LogTracking') ) { ?> display:block <?php } else { ?> display:none <?php } ?>">
						<li><a  class="<?php if(isset($log_list) && $log_list != '') echo $log_list; ?>" href="#<?php //echo ADMIN_SITE_PATH;?>" title="Log Tracking"><i class="icon_log_tracking"></i><span class="hidden-tablet">Log Tracking</span></a></li>
					</ul>
				</li>
			</ul>
		</div>
	</div> 

<!-- 
/LogTracking?cs=1 
/ServiceManage?cs=1
/ServiceList?cs=
/EventsManage?cs=1
/EventsList?cs=1
-->	
	<!-- left menu ends -->
	

	
	
<?php } ?>
<?php 
/*
Purpose : To Perform BulkAction in form
Need to check 
1. form name eg.	<form action="GoalList" class="l_form" name="GoalList" id="GoalList"  method="post">
2. select all check box  <input onclick="checkAllRecords('GoalList');" type="Checkbox" name="checkAll"/>
3. <input id="checkedrecords[]" name="checkedrecords[]" value="<?php  if(isset($value->id) && $value->id != '') echo $value->id  " type="checkbox" />
4. bulk_action($AcctionsArrayList)
*/
function bulk_action($Actions) { ?>
	<table border="0" cellpadding="0" cellspacing="0"  class="">
		<tr><td height="10"></td></tr>
		<tr align="">
			<td align="left" style="padding-top: 7px;">
					<select name="bulk_action" id="bulk_action"  title="Select Action" >
						<option value="">Bulk Actions</option>
						<?php foreach($Actions as $key => $action) { ?>
						<option value="<?php echo $key; ?>"><?php echo $action; ?></option>
								<?php }?>
					</select>
			</td>
			<td align="left" style="padding-left:20px;">
				<input type="submit" onclick="return isActionSelected();" class="submit_button" name="do_action" id="Apply" value="Apply" title="Apply" alt="Apply">&nbsp;&nbsp;													
			</td>
		</tr>
		<tr><td height="10"></td></tr>
	</table>
<?php } ?>
<?php
/********************************************************
  * Function Name: Notification Message
  * Purpose: To display notifications like (Insert/update/Delete/Status change)
  * Paramters :
  *			Need to Notification Session code
  * Output : Returns notification mgs block in table format.
  *******************************************************/
function displayNotification($prefix = ''){
global $notification_msg_class;
global $notification_msg;
	if(isset($_SESSION['notification_msg_code'])	&&	$_SESSION['notification_msg_code']!=''){ 
		$msgCode	=	$_SESSION['notification_msg_code'];
		if( isset($notification_msg_class[$msgCode])	&&	isset($notification_msg[$msgCode]) ){ ?>
			<div class="<?php  echo $notification_msg_class[$msgCode];  ?> w50"><span style="display:block;"><?php echo $prefix.' '.$notification_msg[$msgCode];  ?></span></div>
<?php 	}
		unset($_SESSION['notification_msg_code']);
	}
}
?>