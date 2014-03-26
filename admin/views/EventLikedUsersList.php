<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
require_once('controllers/EventsController.php');
$eventsObj   =   new EventsController();
$msg  =	$class	=	'';
$display = 'none';
if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	if(isset($_SESSION['intermingl_ses_from_timeZone']))
		unset($_SESSION['intermingl_ses_from_timeZone']);
}
setPagingControlValues('Id',ADMIN_PER_PAGE_LIMIT);
/*if(isset($_GET['eventId']) && $_GET['eventId'] != '' ){
	$fields		=	" u.FirstName,u.LastName,con.DateCreated ";
	$leftJoin	=	" LEFT JOIN users as u ON (con.fkUsersId=u.id) ";
	$condition	=	" AND con.fkEventsId	=	".$_GET['eventId']." AND con.Type=2 ";
	$eventLikedUsersList	=	$eventsObj->eventLikedUsers($fields,$leftJoin,$condition);
	$tot_rec 		= $eventsObj->getTotalRecordCount();
}*/

if(isset($_GET['eventId']) && $_GET['eventId'] != '' ){
	$_SESSION['sess_intermingl_likedEventsId']	=	$_GET['eventId'];
}
if(isset($_SESSION['sess_intermingl_likedEventsId'])	&&	$_SESSION['sess_intermingl_likedEventsId']	!=''){
/*	$fields		=	" u.FirstName,u.LastName,con.DateCreated ";
	$leftJoin	=	" LEFT JOIN users as u ON (con.fkUsersId=u.id) ";
*/
$fields		=	" e.Title,u.FirstName,u.LastName,lu.FirstName as lFirstName,lu.LastName as lLastName,u.id as uId,u.Status as uStatus,lu.Status as luStatus,lu.id as luId,con.DateCreated ";
	$leftJoin	=	" 	LEFT JOIN users as u ON (con.toFkUsersId=u.id) 
						LEFT JOIN users as lu ON (con.fkUsersId=lu.id)
						LEFT JOIN events as e on(con.fkEventsId=e.id) ";
	$condition	=	" 	AND con.fkEventsId	=	".$_SESSION['sess_intermingl_likedEventsId']." AND con.Type=2 ";
	$eventLikedUsersList	=	$eventsObj->eventLikedUsers($fields,$leftJoin,$condition);
	$tot_rec 		= $eventsObj->getTotalRecordCount();
}

commonHead(); ?>
<body>
<div >
	<table cellpadding="10" cellspacing="0" border="0" width="100%" align="center" class="">
		<tr><td><span><h2>
		<?php if(isset($eventLikedUsersList) && is_array($eventLikedUsersList) && count($eventLikedUsersList) > 0)
				echo '"'.$eventLikedUsersList[0]->Title.'" - ';
			?>
		Liked Users</h2></span></td></tr>
		<tr><td height="20" colspan="3"></td></tr>
		<tr>
			<td colspan="2">
				<table cellpadding="0"  cellspacing="0" border="0" align="center" width="100%">
					<tr>
						<?php if(isset($eventLikedUsersList) && is_array($eventLikedUsersList) && count($eventLikedUsersList) > 0){ ?>
						<td align="left" width="20%" colspan="2">No. of Users(s)&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
						<?php } ?>
					
						<td align="center">
							<?php if(isset($eventLikedUsersList)	&&	is_array($eventLikedUsersList) && count($eventLikedUsersList) > 0 ) {
						 		pagingControlLatest($tot_rec,'EventLikedUsersList'); ?>
							<?php }?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td height="20" colspan="3"></td></tr>
		<tr><td align="center" colspan="3" ><div class="<?php echo $class; ?>"><span style="display:<?php  echo $display;  ?>"><?php if(isset($msg) && $msg != '') echo $msg;  ?></span></div></td></tr>
		<?php
		if( isset($eventLikedUsersList)	&&	is_array($eventLikedUsersList) && count($eventLikedUsersList) > 0 )
		{ ?>
			<tr><td valign="top">
				<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table">
					<tr>
						<th align="center" width="3%">#</th>
						<th align="left" width="35%">User</th>
						<th align="left" width="35%">Likes</th>
						<th align="left">Date</th>
					</tr>
			<?php $i=1; foreach($eventLikedUsersList as $key=>$value){
							$userName	=	'';
							if(isset($value->FirstName)	&&	isset($value->LastName))
								$userName	=	ucfirst($value->FirstName).' '.ucfirst($value->LastName);
							else if(isset($value->FirstName))	
								$userName	=	 ucfirst($value->FirstName);
							else if(isset($value->LastName))	
								$userName	=	ucfirst($value->LastName);
							$luserName	=	'';
							if(isset($value->lFirstName)	&&	isset($value->lLastName))
								$luserName	=	ucfirst($value->lFirstName).' '.ucfirst($value->lLastName);
							else if(isset($value->lFirstName))	
								$luserName	=	 ucfirst($value->lFirstName);
							else if(isset($value->lLastName))	
								$luserName	=	ucfirst($value->lLastName);
			?>
					<tr>
						<td><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
						<td><?php 	if(isset($userName)	&&	$userName	!=	''	) {
										if(isset($value->uStatus)	&&	$value->uStatus	!=3) {?>
									<a href="#" onclick="close_this();window.parent.location.href='UserDetail?viewId=<?php echo $value->uId;?>&referList=1';"><?php echo $userName; ?></a>	
								<?php  }else echo $userName;
									}
									else echo '-';?>
						</td>
						<td><?php 	if(isset($luserName)	&&	$luserName	!=	''	) {
										if(isset($value->luStatus)	&&	$value->luStatus	!=	3){?>
									<a href="#" onclick="close_this();window.parent.location.href='UserDetail?viewId=<?php echo $value->luId;?>&referList=1';"><?php echo $luserName; ?></a>	
								<?php 	}
										else echo $luserName; 
									}
									else echo '-'; ?></td>
						<td>
						<?php if(isset($value->DateCreated) && $value->DateCreated != '0000-00-00 00:00:00'){ 
															//echo date('m/d/Y',strtotime($value->dateCreated)); 
											$gmt_current_created_time = convertIntocheckinGmtSite($value->DateCreated);
											$time	=  displayConversationDateTime($gmt_current_created_time,$_SESSION['intermingl_ses_from_timeZone']);
											echo $time;
										}else echo '-';?>
						</td>
					</tr>
	<?php	}	?>
				</table>
			</td></tr>
<?php	}
		else
		{	?>	
			<tr>	<td colspan="3" align="center" style="color:red;">No Users found</td>	</tr>
<?php	} ?>
		<tr><td height="20" colspan="3"></td></tr>
	</table>
</div>
</body>
<?php commonFooter(); ?>
<script type="text/javascript">
$(function(){
   var bodyHeight = $('body').height();
   var maxHeight = '564';
   if(bodyHeight<maxHeight) {
   	setHeight = bodyHeight;
   } else {
   		setHeight = maxHeight;
   }
    parent.$.colorbox.resize({
        innerWidth: $('body').width(),
        innerHeight:setHeight
    });
});
function close_this()
{
self.close();
}
</script>
</html>