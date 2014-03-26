
<?php 
ob_start();
require_once('../includes/AdminCommonIncludes.php');
if (isset($_GET['action']) && ($_GET['action'] == 'LOAD_CONVERSATION')) {
   require_once('../controllers/MessageController.php');
	$messageModelObj   =   new MessageController();
    $j = 0;
    setPagingControlValues('CreatedDate', ADMIN_PER_PAGE_LIMIT);
    $from_user_id = $_GET['FromUserId'];
    $to_user_id = $_SESSION['broadtags_ses_con_to_user_id'] = $_GET['ToUserId'];
	//$messageModelObj->updateMessageReadStatus($to_user_id, $from_user_id);
    $msg_result = $users_conversation_lists = $messageModelObj->messageLists($from_user_id, $to_user_id);
    include('../views/MessageViewAll.php');
}
if (isset($_GET['action']) && ($_GET['action'] == 'GET_LIKE_SHARE_COMMENT')) {
	require_once('../controllers/HashTagController.php');
	$hashTagObj   =   new HashTagController();
	$page_limit   = 10;
	$next_page    = 0;
	$type_name = $limit_clause  = '';
	if((isset($_GET['Page']) && $_GET['Page'] == 'Prev' ) ){
		$_SESSION['broadtags_sess_post_paging']   = $_SESSION['broadtags_sess_post_paging'] - $page_limit;
	}
	else if((isset($_GET['Page']) && $_GET['Page'] == 'Next' ) ){
		$_SESSION['broadtags_sess_post_paging']   = $_SESSION['broadtags_sess_post_paging'] + $page_limit;
		$next_page = $_SESSION['broadtags_sess_post_paging']+ $page_limit;
	}
	else{
		$_SESSION['broadtags_sess_post_paging']    = 0;
		$next_page =0;
	}
	$limit_clause = " limit ".$_SESSION['broadtags_sess_post_paging'].",".$page_limit;	
	if(!isset($_GET['PageName']))
		$_GET['PageName'] = '';
	if(isset($_GET['PostId']) && $_GET['PostId'] != '' ){
		$postId = $_GET['PostId'];	
		if(isset($_GET['Type']) && $_GET['Type'] == 2){
			$type_name = 'Shares';
			$type	   = 2;
			$field     = " cls . * , ut.id AS user_id, ut.UserName, ut.Photo ";
			$condition = " AND cls.fkPostId = ".$postId." AND ut.Status = 1 ";
			$shareList = $hashTagObj->getLikeCommentShareDetail($field,$condition,2,$limit_clause);
			$tot_rec   = $hashTagObj->getTotalRecordCount();
			if(isset($shareList) && is_array($shareList) && count($shareList)>0){
				$user_listing = $shareList;
			}
		}
		if(isset($_GET['Type']) && $_GET['Type'] == 1){
			$type_name = 'Likes';
			$type	   = 1;
			$field     = " cls . * , ut.id AS user_id, ut.UserName, ut.Photo ";
			$condition = " AND cls.fkPostId = ".$postId." AND ut.Status = 1 ";
			$likeList  = $hashTagObj->getLikeCommentShareDetail($field,$condition,1,$limit_clause);
			$tot_rec   = $hashTagObj->getTotalRecordCount();
			if(isset($likeList) && is_array($likeList) && count($likeList)>0){
				$user_listing = $likeList;
			}
		}
		if(isset($_GET['Type']) && $_GET['Type'] == 3){
			$type_name		 = 'Comments';
			$type	   		 = 3;
			$field           = " cls.*, cls.CreatedDate as comment_date, ut.UserName, ut.Photo ";
			$condition       = " and cls.fkPostId = ".$postId." and ut.Status = 1 ";			
			$hashTagComments = $hashTagObj->getLikeCommentShareDetail($field,$condition,3,$limit_clause);
			$tot_rec   		 = $hashTagObj->getTotalRecordCount();
			if(isset($hashTagComments) && is_array($hashTagComments) && count($hashTagComments)>0){
				$user_listing = $hashTagComments;
			}
		}
		if(isset($_GET['Type']) && $_GET['Type'] == 4){
			$type_name		 	= 'Reposted Users';
			$type	   			= 4;
			$field           	= " count(cls.fkUserId) as user_count,ut.UserName, ut.Photo ";
			$condition       	= " and cls.OriginalPostId = ".$postId." and ut.Status = 1 and cls.Status = 1 group by cls.fkUserId ";
			$hashTagPost		= $hashTagObj->getLikeCommentShareDetail($field,$condition,4,$limit_clause);
			$tot_rec   			= $hashTagObj->getTotalRecordCount();
			if(isset($hashTagPost) && is_array($hashTagPost) && count($hashTagPost)>0){
				$user_listing = $hashTagPost;
			}
		}
	}
	//echo "<pre>"; print_r($user_listing); echo "</pre>";
	//echo "<br/>======".$_GET['PageName'];
	?>
	<table align="center" cellpadding="10" cellspacing="0" border="0" width="100%">
	<?php
	if(isset($user_listing) && is_array($user_listing) && count($user_listing) > 0 ){ 
	?>		
			<tr><td height="20"></td></tr>
			<tr><td colspan="2" style="padding-left:3%;"><h2><?php echo $type_name;?></h2></td></tr>
			<tr height="20"><td colspan="2"></td></tr>		
			<?php if($tot_rec > $page_limit){ ?>
			<tr>
				<td align="left" width="50%">
			<?php if(isset($_SESSION['broadtags_sess_post_paging'] ) && $_SESSION['broadtags_sess_post_paging']  != '' && $_SESSION['broadtags_sess_post_paging']  > 0 ) { ?>
			<a href="javascript:void(0);" onclick="return getShareLikeCommentPaging(<?php echo $_GET['PostId']; ?>,<?php echo $type;?>,'Prev','<?php echo $_GET['PageName']; ?>');" style="padding-left:10px;color:#e8276a;"><u><< Previous</u></a>
			<?php } ?>
				</td>
				<td align="right" width="50%">
			<?php if(isset($_SESSION['broadtags_sess_post_paging']) && $next_page < $tot_rec ) { ?>
			<a href="javascript:void(0);" onclick="return getShareLikeCommentPaging(<?php echo $_GET['PostId']; ?>,<?php echo $type;?>,'Next','<?php echo $_GET['PageName']; ?>');" style="padding-right:10px;color:#e8276a;"><u>Next >></u></a>
			<?php } ?>
				</td>
			</tr>
			<tr height="20"><td colspan="2"></td></tr>		
			<?php }	
				 foreach($user_listing as $value){	
						$name	 	 = $value->UserName;
						$hashId 	 = $value->fkHashtagId;
						if($type_name == 'Shares'){
							if($value->ShareType == '1')
								$name .= " - via Facebook";
							if($value->ShareType == '2')
								$name .= " - via Twitter";
							if($value->ShareType == '3')
								$name .= " - via Email";
						}
						if($type_name == 'Reposted Users'){
							if($value->user_count > '1')
								$name .= " ( ".$value->user_count." ) ";
						}
						$userPhoto	 = $value->Photo;
						if(isset($userPhoto) && $userPhoto != '')
						{
							$userImage = $userPhoto;
							if(image_exists(1,$userImage)){
								$profile_image = USER_THUMB_IMAGE_PATH.$userImage;
							}
							else
								$profile_image = ADMIN_IMAGE_PATH.'no_user.jpeg';
						}
						else
							$profile_image = ADMIN_IMAGE_PATH.'no_user.jpeg';
			if($_GET['PageName'] == 'hashId')
				$viewId = $hashId;
			else if($_GET['PageName'] == 'post')
				$viewId = $postId;
			else
				$viewId = $postId;
				
			if($type_name == 'Shares' || $type_name == 'Likes' || $type_name == 'Reposted Users'){
			?>
			
			<tr>				
				<td  width="8%"  valign="top" style="padding-left:7px;"><a href="UserDetail?viewId=<?php if(isset($value->fkUserId) && $value->fkUserId != '') echo $value->fkUserId; ?>&<?php echo $_GET['PageName']; ?>=<?php if(isset($viewId)) echo $viewId; ?>" title="User Name" alt="User Name" style="color:#e8276a;"><img class="profile_img" width="30" height="30" src="<?php echo $profile_image; ?>" alt="Image" /></a></td>
				<td width="65%" align="left"><label style="color:#e8276a;"><a href="UserDetail?viewId=<?php if(isset($value->fkUserId) && $value->fkUserId != '') echo $value->fkUserId; ?>&<?php echo $_GET['PageName']; ?>=<?php if(isset($viewId)) echo $viewId; ?>" title="User Name" alt="User Name" style="color:#e8276a;"><?php echo $name;?></a></label></td>		
			</tr>			
			<tr height="10"><td></td></tr>	
			<?php } else { 
			$gmt_current_created_time = convertIntocheckinGmtSite($value->comment_date);
			$time		 =  displayDate($gmt_current_created_time,$_SESSION['broadtags_ses_from_timeZone']);
			?>				
			<tr>	
			 <td colspan="2" class="popup_border">
			 	<table align="center" cellpadding="0" cellspacing="0" width="100%" border="0">
					<tr><td height="10"></td></tr>
					<tr>				
					<td width="10%" align="center" style="padding-right:10px;padding-left:7px;" valign="top">
						<a href="UserDetail?viewId=<?php if(isset($value->fkUserId) && $value->fkUserId != '') echo $value->fkUserId; ?>&<?php echo $_GET['PageName']; ?>=<?php if(isset($viewId)) echo $viewId; ?>" title="User Name" alt="User Name" style="color:#e8276a;"><img width="30" height="30" src="<?php echo $profile_image; ?>" alt="Image" /></a>
					</td>
					<td width="40%" class="header_block">													
						<label style="color:#e8276a;" class="user_name"><a href="UserDetail?viewId=<?php if(isset($value->fkUserId) && $value->fkUserId != '') echo $value->fkUserId; ?>&<?php echo $_GET['PageName']; ?>=<?php if(isset($viewId)) echo $viewId; ?>" title="User Name" alt="User Name" style="color:#e8276a;"><?php echo $name;?></a></label>
					</td>	
				   <td width="30%" valign="top"> <span class="date date_comment"><?php echo $time;?></span></td>			    
				</tr>
				<tr>
				
				<td colspan="3">
					<div class="popup_text"><?php echo (getCommentTextEmoji('web',$value->Comments,$value->Platform));?></div></td></tr>
				<tr><td height="10"></td></tr>
			</table><!-- MESSAGEENDS -->
			</td>
			</tr>
			
			<!-- <tr><td class="popup_border" colspan="2"></td></tr> -->
			<?php } } 
			 if($tot_rec > $page_limit){ ?>
			<tr>
				<td align="left" width="50%">
			<?php if(isset($_SESSION['broadtags_sess_post_paging'] ) && $_SESSION['broadtags_sess_post_paging']  != '' && $_SESSION['broadtags_sess_post_paging']  > 0 ) { ?>
			<a href="javascript:void(0);" onclick="return getShareLikeCommentPaging(<?php echo $_GET['PostId']; ?>,<?php echo $type;?>,'Prev','<?php echo $_GET['PageName']; ?>');" style="padding-left:10px;color:#e8276a;"><u><< Previous</u></a>
			<?php } ?>
				</td>
				<td align="right" width="50%">
			<?php if(isset($_SESSION['broadtags_sess_post_paging']) && $next_page < $tot_rec ) { ?>
			<a href="javascript:void(0);" onclick="return getShareLikeCommentPaging(<?php echo $_GET['PostId']; ?>,<?php echo $type;?>,'Next','<?php echo $_GET['PageName']; ?>');" style="padding-right:10px;color:#e8276a;"><u>Next >></u></a>
			<?php } ?>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<?php } }
			else{ ?>
			<tr>				
				<td width="25%"></td>	
				<td colspan="2" align="left" style="color:#ff0000;"> No <?php echo $type_name; ?> users found</td>	
							    
			</tr>
  <?php	} ?>
  </table>
 <?php
}

if(isset($_GET['action']) && $_GET['action'] == 'SET_ORDERING_WEBSERVICE'){
	$order_value = 0;
	require_once('../controllers/ServiceController.php');
	$serviceObj   =   new ServiceController();
	$ExistCondition = '';
	$service_exists = '0';
	if(isset($_GET['orderValue']) && $_GET['orderValue'] != '')
		$order_value = $_GET['orderValue'];
	if(isset($_GET['serviceId']) && $_GET['serviceId'] != '')
		$service_id = $_GET['serviceId'];
	if($order_value != '' && $service_id != '' )
		$ExistCondition = " and Ordering = ".$order_value." and id != ".$service_id." and Ordering!='0' ";		
	$field = " Ordering ";	
	$alreadyExist   = $serviceObj->selectServiceDetails($field,$ExistCondition);	
	if(isset($alreadyExist) && is_array($alreadyExist) && count($alreadyExist) > 0){
			$service_exists = 1;
	}	
	if($service_exists != '1'){
		if($order_value != '' && $service_id != '' ){
			$update_string 	    = " Ordering = ".$order_value;
			$condition 		    = " id = ".$service_id;
			$OrderingResult     = $serviceObj->updateServiceDetails($update_string,$condition);
		}
	}
	echo $service_exists;
}
?>