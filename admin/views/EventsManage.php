<?php 

require_once('includes/CommonIncludes.php');
//require_once('includes/php_image_magician.php');
admin_login_check();
commonHead();
require_once('controllers/EventsController.php');
$eventsObj   =   new EventsController();
require_once('controllers/AdminController.php');
$adminLoginObj   =   new AdminController();
require_once("includes/phmagick.php");
$field_focus	=	'eventname';
$class=$ExistCondition=$location='';
$photoUpdateString='';
$email_exists = 0;
$eventname_exists  = 0;
if(isset($_GET['editId']) && $_GET['editId'] != '' ){
	$condition       = " id = ".$_GET['editId'];//." and Status in (1,2)";
	$field			 = " * ";
	$eventDetailsResult  = $eventsObj->selectEventDetails($field,$condition);
	if(isset($eventDetailsResult) && is_array($eventDetailsResult) && count($eventDetailsResult) > 0){
		$eventname 		=	$eventDetailsResult[0]->Title;
		$description	=	$eventDetailsResult[0]->Description;
		$startdate      =	$eventDetailsResult[0]->StartDate;//date('m/d/Y',strtotime($eventDetailsResult[0]->StartDate));
		$enddate    	=	$eventDetailsResult[0]->EndDate;//date('m/d/Y',strtotime($eventDetailsResult[0]->EndDate));
		$twitterid  	=	$eventDetailsResult[0]->TwitterHashtag;	
		$location   	=	$eventDetailsResult[0]->Location;	
		if(isset($eventDetailsResult[0]->CoverPhoto) && $eventDetailsResult[0]->CoverPhoto != ''){
			$event_cover_image = $eventDetailsResult[0]->CoverPhoto;
			if(image_exists(3,$event_cover_image))
				$original_image_path = EVENT_COVER_IMAGE_PATH.$event_cover_image;
			else
				$original_image_path = '';	
			if(image_exists(1,$event_cover_image)){
				$event_image_path = EVENT_COVER_IMAGE_PATH.$event_cover_image;
			}
		}
	
	}
}
if(isset($_POST['submit'])	&&	$_POST['submit']!="")
{
	$_POST          	=   unEscapeSpecialCharacters($_POST);
	$_POST         		=   escapeSpecialCharacters($_POST);
	//if($email_exists != '1' && $eventname_exists != '1')	{
		if($_POST['submit'] == 'Save'){		
			if(isset($_POST['event_id']) && $_POST['event_id'] != ''){
				$validate_date = dateValidation($_POST['startdate']);
				if($validate_date == 1){
					$_POST['startdate'] = date('Y-m-d',strtotime($_POST['startdate']));
				}
				else $_POST['startdate']	=	'';
				$validate_date = dateValidation($_POST['enddate']);
				if($validate_date == 1){
					$_POST['enddate'] = date('Y-m-d',strtotime($_POST['enddate']));
				}
				else $_POST['enddate']	=	'';
				$lat	=	$long	=	'';
				if(isset($_POST['location'])	&&	$_POST['location']	!=''){
					$latLon	=	translateAddresstoLatLng($_POST['location']);
					if(is_object($latLon)){
						$lat	=	$latLon->latitude;
						$long	=	$latLon->longitude;
					}
				}
				//$latLon1	=	translateAddresstoLatLng($_POST['location']);
				$fields    = "	Title 			=	'".trim($_POST['eventname'])."',
								Description 	=	'".trim($_POST['description'])."',
								Location        =	'".trim($_POST['location'])."',
								TwitterHashtag 	=	'".trim($_POST['twitter_id'])."',
								StartDate 		=	'".$_POST['startdate']."',
								EndDate			=	'".$_POST['enddate']."',
								Latitude		=	'".$lat."',
								Longitude		=	'".$long."',
								DateModified	=	'".date('Y-m-d H:i:s')."'";
				$condition	=	' id = '.$_POST['event_id'];
				$eventsObj->updateEventDetails($fields,$condition);			
				$insert_id = $_POST['event_id'];
				if (isset($_POST['event_photo_upload']) && !empty($_POST['event_photo_upload'])) {
					if(isset($_POST['name_event_photo']) && $_POST['name_event_photo'] != ''){
						$image_path = $_POST['name_event_photo'];
						if(image_exists(3,$image_path))
							unlink(EVENT_COVER_IMAGE_PATH_REL . $image_path);
						/*if(image_exists(1,$image_path))
							unlink(USER_THUMB_IMAGE_PATH_REL . $image_path);
						if(image_exists(2,$image_path))
							unlink(USER_SMALL_THUMB_IMAGE_PATH_REL . $image_path);
						*/
					}
				}
				/*if (isset($_POST['cover_photo_upload']) && !empty($_POST['cover_photo_upload'])) {
					if(isset($_POST['name_cover_photo']) && $_POST['name_cover_photo'] != ''){
						$image_path = $_POST['name_cover_photo'];
						if(image_exists(4,$image_path))
							unlink(COVER_IMAGE_PATH_REL . $image_path);
						if(image_exists(5,$image_path))
							unlink(COVER_THUMB_IMAGE_PATH_REL . $image_path);
					}
				}*/
			//$msg = 2;
			$_SESSION['notification_msg_code']	=	2;
			}
		}
		
		if($_POST['submit']=='Add')	{
			//$_POST['ipaddress'] =	$ip_address;
		//	die();
			$validate_date = dateValidation($_POST['startdate']);
			if($validate_date == 1){
				$_POST['startdate'] = date('Y-m-d',strtotime($_POST['startdate']));
				/*if($date != '' && $date != '1970-01-01' && $date != '0000-00-00' )
					$_SESSION['intermingl_sess_user_registerdate']	= $date;
				else 
					$_SESSION['intermingl_sess_user_registerdate']	= '';*/
			}
			else $_POST['startdate']	=	'';
			$validate_date = dateValidation($_POST['enddate']);
			if($validate_date == 1){
				$_POST['enddate'] = date('Y-m-d',strtotime($_POST['enddate']));
			}
			else $_POST['enddate']	=	'';
			$insert_id   		=	$eventsObj->insertEventDetails($_POST);
			$_SESSION['notification_msg_code']	=	1;
			//$msg = '1&cs=1';
		}
			if(isset($insert_id)	&&	$insert_id!='')	{
			$date_now = date('Y-m-d H:i:s');
				if (isset($_POST['event_photo_upload']) && !empty($_POST['event_photo_upload'])) {
					$imageName 				= $insert_id . '_' . strtotime($date_now) . '.png';
					$temp_image_path 		= TEMP_USER_IMAGE_PATH_REL . $_POST['event_photo_upload'];
					$image_path 			= EVENT_COVER_IMAGE_PATH_REL . $imageName;
					$oldeventname			= $_POST['name_event_photo'];
					if ( !file_exists(EVENT_COVER_IMAGE_PATH_REL) ){
						mkdir (EVENT_COVER_IMAGE_PATH_REL, 0777);
					}
					copy($temp_image_path,$image_path);
					//imagethumb_new($image_path,$imageThumbPath,'','',100,100);
					if ($_SERVER['HTTP_HOST'] != '172.21.4.104'){
						if($oldeventname!='') {
							if(image_exists(3,$oldeventname)) {
								deleteImages(3,$oldeventname);
							}
						}
						uploadImageToS3($image_path,3,$imageName);
						unlink($image_path);
					}
					$photoUpdateString	.= " CoverPhoto = '" . $imageName . "'";
					unlink(TEMP_USER_IMAGE_PATH_REL . $_POST['event_photo_upload']);
				}
				if($photoUpdateString!='')
				{
					$condition 			= "id = ".$insert_id;
					$eventsObj->updateEventDetails($photoUpdateString,$condition);
				}
			}
			header("location:EventsList");//?msg=".$msg);	
	/*}	//	End of Already Exist condition
	else	{
		if($email_exists == 1){
			$error_msg   = "Email address already exists";
			$field_focus = 'email';
		}
		else if ($eventname_exists == 1){
			$error_msg   = "eventname already exists";
			$field_focus = 'eventname';
		}
		$display = "block";
		$class   = "error_msg";
	}*/
}
if(isset($_GET['msg']) && $_GET['msg'] == 1){
	$msg 		= 	"Event added successfully";
	$display	=	"block";
	$class 		= 	"success_msg";
}
else if(isset($_GET['msg']) && $_GET['msg'] == 2){
	$msg 		= 	"Event updated successfully";
	$display	=	"block";
	$class 		= 	"success_msg";
}
else if(isset($_GET['msg']) && $_GET['msg'] == 3){
	$msg 		= 	"Event deleted successfully";
	$display	=	"block";
	$class 		= 	"error_msg";
}
else if(isset($_GET['msg']) && $_GET['msg'] == 4){
	$msg 		= 	"Status changed successfully";
	$display	=	"block";
	$class 		= 	"success_msg";
}

?>
<body onload="return fieldfocus('<?php echo $field_focus; ?>');">
	<table cellpadding="0" cellspacing="0" border="0" width="100%" align="center">
		<tr>
			<td align="center">
				<table cellpadding="0" cellspacing="0" border="0" width="95%" align="center">					
					<tr><td colspan="2" class="headermenu"><?php top_header(); ?></td></tr>
				    <tr>
						<td colspan="2">
							<div class="left_menu sidebar-nav" style="float:left;"><?php side_bar()?></div>
						 	 <div id="content_3" class="content">
							 <div class="box-header"><h2><i class="icon_add_event"></i><?php if(isset($_GET['editId']) && $_GET['editId'] != '' ) echo "Edit "; else echo 'Add ';?>Event</h2>
						 	</div>
							  <form name="add_event_form" id="add_event_form" action="" method="post">
							  <input type="Hidden" name="event_id" id="event_id" value="<?php if(isset($_GET['editId']) && $_GET['editId'] != '' ) echo $_GET['editId'];?>">
						  		 <table align="center" cellpadding="0" cellspacing="0" border="0" class="form_page list headertable" width="100%">
									<tr><td align="center">
									<table cellpadding="0" cellspacing="0" align="center" border="0" width="75%">
										<tr><td colspan="6" align="center"><div class="<?php echo $class;  ?> w50"><span><?php if(isset($error_msg) && $error_msg != '') echo $error_msg;  ?></span></div></td></tr>
										<tr><td height="20"></td></tr>
										<tr>
											<td height="50" width="15%" align="left" valign="top"><label>Event&nbsp;<span class="required_field">*</span></label></td>
											<td width="3%" align="center"  valign="top">:</td>
											<td align="left" width="35%"  valign="top" >											
												<input type="text" class="input" name="eventname" id="eventname" maxlength="100" value="<?php if(isset($eventname) && $eventname !='') echo $eventname; ?>" >										
											</td>
											<td height="60"  align="left" valign="top"><label>Description&nbsp;<span class="required_field">*</span></label></td>
											<td  align="center" valign="top">:</td>
											<td align="left"  height="60" valign="top">
												<textarea style="width:74%;height:80px" name="description" id="description"><?php if(isset($description)	&&	$description!="") { echo $description; }else echo "";?></textarea>
											</td>
										</tr>
										<tr><td height="30"></td></tr>			
										<tr>
											<td  height="50" width="15%" align="left"  valign="top"><label>Location&nbsp;<span class="required_field">*</span></label></td>
											<td width="3%" align="center"  valign="top">:</td>
											<td align="left"  height="40"  valign="top">
												<input type="text" class="input" name="location" id="location" maxlength="100" value="<?php if(isset($location) && $location != '') echo $location;  ?>" >
											<td ><label>Twitter</label></td>
											<td  align="center">:</td>
											<td align="left"  height="40">
												<input type="text" class="input" id="twitter_id" name="twitter_id"  value="<?php  if(isset($twitterid) && $twitterid != '') echo unEscapeSpecialCharacters($twitterid);  ?>" >
											</td>
										</tr>						
										<tr>
											
											<td height="50" width="15%" align="left" valign="top"><label>Start Date&nbsp;<span class="required_field">*</span></label></td>
											<td width="3%" align="center"  valign="top">:</td>
											<td align="left" width="35%"  valign="top" >											
												<input type="text" class="input input-medium datepicker1" readonly name="startdate" id="startdate"  value="<?php if(isset($startdate) && $startdate != '0000-00-00 00:00:00'){ echo date('m/d/Y',strtotime($startdate)); }?><?php //if(isset($startdate) && $startdate !='') echo $startdate; ?>" >											
											</td>
											<td height="50" width="15%" align="left" valign="top"><label>End Date&nbsp;<span class="required_field">*</span></label></td>
											<td width="3%" align="center"  valign="top">:</td>
											<td align="left" width="35%"  valign="top" >											
												<input type="text" class="input input-medium datepicker1" readonly name="enddate" id="enddate"  value="<?php if(isset($enddate) && $enddate != '0000-00-00 00:00:00'){ echo date('m/d/Y',strtotime($enddate)); }//if(isset($enddate) && $enddate !='') echo $enddate; ?>" >											
											</td>
										</tr>
										<tr>
											
											<td height="60"  align="left"  valign="top"  valign="top"><label>Photo</label></td>
											<td  align="center" valign="top">:</td>
											<td align="left"  height="60" valign="top">
												<div class="upload fleft">
												<div style="clear: both;float: left"> <input type="file"  name="event_photo" id="event_photo" title="Event Photo" onclick="" onchange="return ajaxAdminFileUploadProcess('event_photo');"  /> </div><!-- imageValidation('empty_cat_sel_photo'); -->
												<div style="width:230px;">(Minimum dimension 100x100)</div>
												<span class="error" for="empty_event_photo" generated="true" style="display: none">Event Image is required</span>
												<div class="fakefile_photo" style="float: left;clear: both;margin-top: 5px">
													<div id="event_photo_img">
														<?php  if(isset($event_image_path) && $event_image_path != ''){  ?>
															<a href="<?php if(isset($original_image_path) && $original_image_path != '') { echo $original_image_path; ?>" class="event_photo_pop_up"<?php } else { ?>Javascript:void(0);<?php } ?>" title="Click here" alt="Click here" ><img src="<?php  echo $event_image_path;  ?>" width="75" height="75" alt="Image"/></a>
														<?php  }  ?>
													</div>
												</div>
											</div>
											<?php  if(isset($_POST['event_photo_upload']) && $_POST['event_photo_upload'] != ''){  ?><input type="Hidden" name="event_photo_upload" id="event_photo_upload" value="<?php  echo $_POST['event_photo_upload'];  ?>"><?php  }  ?>
											<input type="Hidden" name="empty_event_photo" id="empty_event_photo" value="<?php  if(isset($event_cover_image) && $event_cover_image != '') { echo $event_cover_image; }  ?>" />
											<input type="Hidden" name="name_event_photo" id="name_event_photo" value="<?php  if(isset($event_cover_image) && $event_cover_image != '') { echo $event_cover_image; }  ?>" />
											</td>
										</tr>		
									</table>
									</td>	
									<tr>
										<td colspan="6" align="center">
										<?php if(isset($_GET['editId']) && $_GET['editId'] != ''){ ?>
											<input type="submit" class="submit_button" name="submit" id="submit"  value="Save" title="Save" alt="Save">
										<?php } else { ?>
										<input type="submit" class="submit_button" name="submit" id="submit" value="Add" title="Add" alt="Add">
										<?php } ?>
										<a href="EventsList"  class="submit_button" name="Back" id="Back"  value="Back" title="Back" alt="Back">Back </a>
									</td>
								</tr>	
								<tr><td height="10"></td></tr>					  
								</table>
							</form>	
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
$(".event_photo_pop_up").colorbox({title:true});


$("#startdate").datepicker({
	showButtonPanel	:	true,        
    buttonText		:	'',
    buttonImageOnly	:	true,
	onSelect		: function (dateText, inst) {
						$('#enddate').datepicker("option", 'minDate', new Date(dateText));
						},
    onClose			: function () { $(this).focus(); },

    buttonImage		:	path+'webresources/images/calender.png',
    dateFormat		:	'mm/dd/yy',
	changeMonth		:	true,
	changeYear		:	true,
	hideIfNoPrevNext:	true,
	showWeek		:	true,
	yearRange		:	"c-30:c",
	closeText		:   "Close"
 });
 $("#enddate").datepicker({
	showButtonPanel	:	true,        
    buttonText		:	'',
    buttonImageOnly	:	true,
	onSelect		: function () { },
    onClose			: function () { $(this).focus(); },
    buttonImage		:	path+'webresources/images/calender.png',
    dateFormat		:	'mm/dd/yy',
	changeMonth		:	true,
	changeYear		:	true,
	hideIfNoPrevNext:	true,
	showWeek		:	true,
	yearRange		:	"c-30:c",
	closeText		:   "Close"
 });
/*$(".datepicker").datepicker({
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
*/
	/*$('#enddate').datepicker({ showOn: 'button',
      buttonImage: path+'webresources/images/calendar.png',
      buttonImageOnly: true, onSelect: function () { },
      onClose: function () { $(this).focus(); }
    });


  $('#startdate').datepicker({ showOn: 'button',
      buttonImage: path+'webresources/images/calendar.png',
      buttonImageOnly: true, onSelect:
        function (dateText, inst) {
          $('#endDate').datepicker("option", 'minDate', new Date(dateText));
        }
      ,
      onClose: function () { $(this).focus(); }
    });
   */
</script>
</html>