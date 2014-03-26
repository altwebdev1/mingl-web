<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/EventsController.php');
$eventsObj   =   new EventsController();
$original_image_path =  $original_cover_image_path = $description = '';
if(isset($_GET['viewId']) && $_GET['viewId'] != '' ){
	$condition       = " id = ".$_GET['viewId']." LIMIT 1 ";	
	$field				=	' * ';
	$eventDetailsResult  = $eventsObj->selectEventDetails($field,$condition);
	if(isset($eventDetailsResult) && is_array($eventDetailsResult) && count($eventDetailsResult) > 0){
		$eventtitle       		=	$eventDetailsResult[0]->Title;
		$location  					= $eventDetailsResult[0]->Location;
		$description	=	$eventDetailsResult[0]->Description;
		$startdate      =	date('m/d/Y',strtotime($eventDetailsResult[0]->StartDate));
		$enddate    	=	date('m/d/Y',strtotime($eventDetailsResult[0]->EndDate));
		$twitter  	=	$eventDetailsResult[0]->TwitterHashtag;	
		if(isset($eventDetailsResult[0]->CoverPhoto) && $eventDetailsResult[0]->CoverPhoto != ''){
			$user_image = $eventDetailsResult[0]->CoverPhoto;
			if(image_exists(3,$user_image))
				$original_image_path = EVENT_COVER_IMAGE_PATH.$user_image;
			else
				$original_image_path = '';			
			if(image_exists(1,$user_image)){
				$image_path = EVENT_COVER_IMAGE_PATH.$user_image;
			}
			else
				$image_path = ADMIN_IMAGE_PATH.'no_user.jpeg';
		}
		else
			$image_path = ADMIN_IMAGE_PATH.'no_user.jpeg';
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
							 <div class="box-header"><h2><i style="margin:4px 0px 0px 0px" class="icon_event_list"></i>View Event</h2></div>
						  		 <table align="center" cellpadding="0" cellspacing="0" border="0" class="list headertable" width="100%">							        
									<tr><td height="20"></td></tr>
									<tr>
										<td align="center">
											<table cellpadding="0" cellspacing="0" align="center" border="0" width="75%">
												<tr>
													<td width="25%" align="left" valign="top"><label>Event </label></td>
													<td width="3%" align="center" valign="top">:</td>
													<td align="left" width="25%" valign="top"><?php if(isset($eventtitle) && $eventtitle !='') echo $eventtitle; else echo '-'; ?></td>
													<td  align="left"  valign="top" width="16%"><label>Description</label></td>
													<td align="center" width="3%" valign="top">:</td>
													<td align="left"  valign="top"><?php if(isset($description) && $description != '' ) echo $description; else echo '-'; ?></td>							
													
												</tr>									
												<tr><td height="20"></td></tr>
												<tr>
													<td align="left" valign="top"><label>Location</label></td>
													<td align="center" valign="top">:</td>
													<td align="left" valign="top"><?php if(isset($location) && $location != '') echo $location; else echo '-'; ?></td>										
													<td align="left" valign="top" ><label>Twitter Hashtag</label></td>
													<td align="center" valign="top">:</td>										
													<td align="left" valign="top"><?php if(isset($twitter) && $twitter != '') echo $twitter;  else echo '-'; ?></td>										
												</tr>
												<tr><td height="20"></td></tr>
												<tr>
													<td align="left" valign="top"><label>Start Date</label></td>
													<td align="center" valign="top">:</td>
													<td align="left" valign="top"><?php if(isset($startdate) && $startdate != '') echo $startdate; else echo '-'; ?></td>										
													<td align="left" valign="top" ><label>End Date</label></td>
													<td align="center" valign="top">:</td>										
													<td align="left" valign="top"><?php if(isset($enddate) && $enddate != '') echo $enddate;  else echo '-'; ?></td>										
												</tr>
												<tr><td height="20"></td></tr>	
												<tr>
													<td align="left" valign="top"><label>Image</label></td>
													<td align="center" valign="top">:</td>
													<td align="left" valign="top"><a href="<?php if(isset($original_image_path) && $original_image_path != '') { echo $original_image_path; ?>" class="user_photo_pop_up"<?php } else { ?>Javascript:void(0);<?php } ?>" title="Click here" alt="Click here" ><?php if(isset($image_path) && $image_path != '') { ?> <img width="75" height="75" src="<?php echo $image_path;?>"><?php } ?></a></td>
												</tr>
												<tr><td height="20"></td></tr>
											</table>
										</td>
									</tr>
<!--
									<tr><td ><h2>Notification Settings</h2></td></td></tr>
									<tr><td height="20"></td></tr>
									<tr>
										<td align="center">
											<table cellpadding="0" cellspacing="0" align="center" border="0" width="75%">
												<tr>
													<td  align="left" width="8%" valign="top"><label class="notification">Email Notifications</label></td>
													<td width="3%" align="center" valign="top">:</td>
													<td align="left" width="25%" valign="top"><?php if(isset($enddatenotify) && $enddatenotify == '1' ) echo 'On'; else echo 'Off'; ?></td>
												</tr>								
												<tr><td height="20"></td></tr>
											</table>
										</td>
									</tr>
									<tr><td height="20"></td></tr>														
									<tr>										
										<td colspan="6" align="center">
											<?php if(isset($postDetail) && $postDetail!=''){ ?><a href="PostDetail?uid=<?php echo $_GET['viewId']; ?>" class="submit_button hashtag_pop_up" name="HashtagPostList" id="HashtagPostList" title="View Hashtag Post" alt="View Hashtag Post">Post</a>&nbsp;&nbsp;<?php } ?>
											<?php if(isset($hashTagDetail) && $hashTagDetail!=''){ ?><a href="HashTagDetail?uid=<?php echo $_GET['viewId']; ?>" class="submit_button hashtag_pop_up" name="HashtagList" id="HashtagList" title="View Hashtag List" alt="View Hashtag List">Hashtag</a>&nbsp;&nbsp;<?php } ?>
											<?php if(isset($userCreatedHashtag) && $userCreatedHashtag!=''){ ?><a href="HashTagDetail?uid=<?php echo $_GET['viewId']; ?>&user=1" class="submit_button hashtag_pop_up" name="HashtagList" id="HashtagList" title="View Hashtag List" alt="View Hashtag List">Created Hashtag</a>&nbsp;&nbsp;<?php } ?>
											<?php if(isset($contactDetail) && $contactDetail!=''){ ?><a href="ContactDetail?uid=<?php echo $_GET['viewId']; ?>" class="submit_button hashtag_pop_up" name="ContactList" id="ContactList" title="View Contact List" alt="View Contact List">Contact</a>&nbsp;&nbsp;<?php } ?>
										</td>
									</tr>
-->
									<tr><td height="20"></td></tr>
									<tr>										
										<td colspan="6" align="center">		
											<a href="EventsManage?editId=<?php if(isset($_GET['viewId']) && $_GET['viewId'] != '') echo $_GET['viewId']; ?>" title="Edit" alt="Edit" class="submit_button">Edit</a>			
											<a href="EventsList" class="submit_button" name="Back" id="Back" title="Back" alt="Back" >Back </a>
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
		$(".hashtag_pop_up").colorbox(
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
