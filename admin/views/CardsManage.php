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
require_once('controllers/CardController.php');
$cardsObj   =   new CardController();
/*if(isset($_GET['editId']) && $_GET['editId'] != '' ){
	$field		=	" * ";
	$condition	=	" id	=	".$_GET['editId']." ";
	$cardDetailsResult  = $cardsObj->selectCardDetails($field,$condition);
	echo "<pre>";print_r($cardDetailsResult);echo "</pre>";
}*/
if(isset($_GET['editId']) && $_GET['editId'] != '' ){
	$field		=	" * ";
	$condition	=	" id	=	".$_GET['editId']." ";
	$cardDetailsResult  = $cardsObj->selectCardDetails($field,$condition);
//	echo "<pre>";print_r($cardDetailsResult);echo "</pre>";

	$cardDetailsResult  = $cardsObj->selectCardDetails($field,$condition);
	if(isset($cardDetailsResult) && is_array($cardDetailsResult) && count($cardDetailsResult) > 0){
		$firstName 	=	$cardDetailsResult[0]->FirstName;
		$lastName	=	$cardDetailsResult[0]->LastName;
		$email      =	$cardDetailsResult[0]->Email;//date('m/d/Y',strtotime($cardDetailsResult[0]->StartDate));
		$company    =	$cardDetailsResult[0]->Company;//date('m/d/Y',strtotime($cardDetailsResult[0]->EndDate));
		$city  		=	$cardDetailsResult[0]->City;	
		$state   	=	$cardDetailsResult[0]->State;	
		if(isset($cardDetailsResult[0]->Photo) && $cardDetailsResult[0]->Photo != ''){
			$event_cover_image = $cardDetailsResult[0]->Photo;
			if(image_exists(3,$event_cover_image))
				$original_image_path = CARD_IMAGE_PATH.$event_cover_image;
			else
				$original_image_path = '';	
			if(image_exists(1,$event_cover_image)){
				$user_image_path = CARD_IMAGE_PATH.$event_cover_image;
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
			if(isset($_POST['card_id']) && $_POST['card_id'] != ''){
				//$validate_date = dateValidation($_POST['startdate']);
				$lat	=	$long	=	'';
				/*if(isset($_POST['location'])	&&	$_POST['location']	!=''){
					$latLon	=	translateAddresstoLatLng($_POST['location']);
					if(is_object($latLon)){
						$lat	=	$latLon->latitude;
						$long	=	$latLon->longitude;
					}
				}
				*/
//								Latitude		=	'".$lat."',
//								Longitude		=	'".$long."',

				$fields    = "	FirstName 			=	'".$_POST['firstname']."',
								LastName 	=	'".$_POST['lastname']."',
								Email        =	'".$_POST['email']."',
								Company 	=	'".$_POST['company']."',
								City 		=	'".$_POST['city']."',
								State			=	'".$_POST['state']."',
								DateModified	=	'".date('Y-m-d H:i:s')."'";
				$condition	=	' id = '.$_POST['card_id'];
				$cardsObj->updateCardDetails($fields,$condition);			
				$insert_id = $_POST['card_id'];
				if (isset($_POST['user_photo_upload']) && !empty($_POST['user_photo_upload'])) {
					if(isset($_POST['name_user_photo']) && $_POST['name_user_photo'] != ''){
						$image_path = $_POST['name_user_photo'];
						if(image_exists(3,$image_path))
							unlink(CARD_IMAGE_PATH_REL . $image_path);
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
			$msg = 2;
			}
		}
		
		if($_POST['submit']=='Add')	{
			//$_POST['ipaddress'] =	$ip_address;
		//	die();
			$insert_id   		=	$eventsObj->insertCardDetails($_POST);
			$msg = '1&cs=1';
		}
			if(isset($insert_id)	&&	$insert_id!='')	{
			$date_now = date('Y-m-d H:i:s');
				if (isset($_POST['user_photo_upload']) && !empty($_POST['user_photo_upload'])) {
					/*if(isset($_POST['card_id'])	&&	$_POST['card_id']!='')
						$imageName 			= $_POST['card_id'].'_'.$insert_id . '_' . strtotime($date_now) . '.png';
					else */
					$imageName 		= $insert_id . '_' . strtotime($date_now) . '.png';
					$temp_image_path 		= TEMP_USER_IMAGE_PATH_REL . $_POST['user_photo_upload'];
					$image_path 			= CARD_IMAGE_PATH_REL . $imageName;
					$oldeventname			= $_POST['name_user_photo'];
					if ( !file_exists(CARD_IMAGE_PATH_REL) ){
						mkdir (CARD_IMAGE_PATH_REL, 0777);
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
					$photoUpdateString	.= " Photo = '" . $imageName . "'";
					unlink(TEMP_USER_IMAGE_PATH_REL . $_POST['user_photo_upload']);
				}
				if($photoUpdateString!='')
				{
					$condition 			= "id = ".$insert_id;
					$cardsObj->updateCardDetails($photoUpdateString,$condition);
				}
			}
		//	header("location:EventsList?msg=".$msg);	
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
						 	 <div id="content_3" class="content">
							  <form name="add_card_form" id="add_card_form" action="" method="post">
							  <input type="Hidden" name="card_id" id="card_id" value="<?php if(isset($_GET['editId']) && $_GET['editId'] != '' ) echo $_GET['editId'];?>">
						  		 <table align="center" cellpadding="0" cellspacing="0" border="0" class="form_page list headertable" width="100%">
							     	<tr><td colspan="6"><h2><?php if(isset($_GET['editId']) && $_GET['editId'] != '' ) echo "Edit "; else echo 'Add ';?>Card</h2></td></td></tr>
									<tr><td align="center">
									<table cellpadding="0" cellspacing="0" align="center" border="0" width="75%">
										<tr><td colspan="6" align="center"><div class="<?php echo $class;  ?> w50"><span><?php if(isset($error_msg) && $error_msg != '') echo $error_msg;  ?></span></div></td></tr>
										<tr><td height="20"></td></tr>
										<tr>
											<td height="50" width="15%" align="left" valign="top"><label>First Name</label></td>
											<td width="3%" align="center"  valign="top">:</td>
											<td align="left" width="35%"  valign="top" >											
												<input type="text" class="input" name="firstname" id="firstname" maxlength="100" value="<?php if(isset($firstName) && $firstName !='') echo $firstName; ?>" >										
											</td>
											<td height="50" width="15%" align="left" valign="top"><label>Last Name</label></td>
											<td width="3%" align="center"  valign="top">:</td>
											<td align="left" width="35%"  valign="top" >											
												<input type="text" class="input" name="lastname" id="lastname" maxlength="100" value="<?php if(isset($lastName) && $lastName !='') echo $lastName; ?>" >										
											</td>
										</tr>
										<tr><td height="20"></td></tr>			
										<tr>
											<td ><label>Email</label></td>
											<td  align="center">:</td>
											<td align="left"  height="40">
												<input type="text" class="input" id="email" name="email"  value="<?php  if(isset($email) && $email != '') echo unEscapeSpecialCharacters($email);  ?>" >
											</td>
											<td  height="50" width="15%" align="left"  valign="top"><label>Company</label></td>
											<td width="3%" align="center"  valign="top">:</td>
											<td align="left"  height="40"  valign="top">
												<input type="text" class="input" name="company" id="company" maxlength="100" value="<?php if(isset($company) && $company != '') echo $company;  ?>" >
										</tr>						
										<tr>
											<td  height="50" width="15%" align="left"  valign="top"><label>City</label></td>
											<td width="3%" align="center"  valign="top">:</td>
											<td align="left"  height="40"  valign="top">
												<input type="text" class="input" name="city" id="city" maxlength="100" value="<?php if(isset($city) && $city != '') echo $city;  ?>" >
											<td ><label>State</label></td>
											<td  align="center">:</td>
											<td align="left"  height="40">
												<input type="text" class="input" id="state" name="state"  value="<?php  if(isset($state) && $state != '') echo unEscapeSpecialCharacters($state);  ?>" >
											</td>
										</tr>
										<tr>
											
											<td height="60"  align="left"  valign="top"  valign="top"><label>Photo</label></td>
											<td  align="center" valign="top">:</td>
											<td align="left"  height="60" valign="top">
												<div class="upload fleft">
												<div style="clear: both;float: left"> <input type="file"  name="user_photo" id="user_photo" title="Card User Photo" onclick="" onchange="return ajaxAdminFileUploadProcess('user_photo');"  /> </div><!-- imageValidation('empty_cat_sel_photo'); -->
												<div style="width:230px;">(Minimum dimension 100x100)</div>
												<span class="error" for="empty_user_photo" generated="true" style="display: none">User Image is required</span>
												<div class="fakefile_photo" style="float: left;clear: both;margin-top: 5px">
													<div id="user_photo_img">
														<?php  if(isset($user_image_path) && $user_image_path != ''){  ?>
															<a href="<?php if(isset($original_image_path) && $original_image_path != '') { echo $original_image_path; ?>" class="user_photo_pop_up"<?php } else { ?>Javascript:void(0);<?php } ?>" title="Click here" alt="Click here" ><img src="<?php  echo $user_image_path;  ?>" width="75" height="75" alt="Image"/></a>
														<?php  }  ?>
													</div>
												</div>
											</div>
											<?php  if(isset($_POST['user_photo_upload']) && $_POST['user_photo_upload'] != ''){  ?><input type="Hidden" name="user_photo_upload" id="event_photo_upload" value="<?php  echo $_POST['user_photo_upload'];  ?>"><?php  }  ?>
											<input type="Hidden" name="empty_user_photo" id="empty_user_photo" value="<?php  if(isset($event_cover_image) && $event_cover_image != '') { echo $event_cover_image; }  ?>" />
											<input type="Hidden" name="name_user_photo" id="name_user_photo" value="<?php  if(isset($event_cover_image) && $event_cover_image != '') { echo $event_cover_image; }  ?>" />
											</td>
										</tr>		
									</table>
									</td>	
									<tr>
										<td colspan="6" align="center">
										<?php if(isset($_GET['editId']) && $_GET['editId'] != ''){ ?>
											<input type="submit" class="submit_button" name="submit" id="submit" value="Save" title="Save" alt="Save">
										<?php } else { ?>
										<input type="submit" class="submit_button" name="submit" id="submit" value="Add" title="Add" alt="Add">
										<?php } ?>
										<a href="CardsList"  class="submit_button" name="Back" id="Back"  value="Back" title="Back" alt="Back">Back </a>
									</td>
								</tr>	
													  
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
$(".user_photo_pop_up").colorbox({title:true});
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