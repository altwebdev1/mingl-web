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
	//unset($_SESSION['sess_intermingl_firstName']);
//	unset($_SESSION['sess_intermingl_lastName']);
	//unset($_SESSION['sess_intermingl_joinEventId']);
}
/*if(isset($_POST['Search']) && $_POST['Search'] != ''){
	destroyPagingControlsVariables();
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	$_SESSION['sess_intermingl_firstName']        = trim($_POST['firstName']);
	$_SESSION['sess_intermingl_lastName']    =	trim($_POST['lastName']);
	$_SESSION['sess_intermingl_email']        = 	trim($_POST['email']);
}*/
setPagingControlValues('Id',ADMIN_PER_PAGE_LIMIT);
//setPagingControlValues('Id',1);
if(isset($_GET['eventId']) && $_GET['eventId'] != '' ){
	$_SESSION['sess_intermingl_joinEventId']	=	$_GET['eventId'];
}
if(isset($_SESSION['sess_intermingl_joinEventId'])	&&	$_SESSION['sess_intermingl_joinEventId']	!=''){
	$fields		=	" e.Title,u.FirstName,u.LastName,u.Email,je.DateCreated,u.Status,u.id  ";
	$leftJoin	=	" 	LEFT JOIN users as u ON (je.fkUsersId=u.id) 
						LEFT JOIN events as e on(je.fkEventsId=e.id) ";
	$condition	=	" AND je.fkEventsId	=	".$_SESSION['sess_intermingl_joinEventId']." ";
	$joinedUsersList	=	$eventsObj->eventJoinedUsers($fields,$leftJoin,$condition);
//	echo "<pre>";print_r($joinedUsersList);echo "</pre>";
	$tot_rec 		= $eventsObj->getTotalRecordCount();
}
commonHead(); ?>
<body>
<div>
	<table cellpadding="10" cellspacing="0" border="0" width="100%" align="center" class="">
		<tr>
			<td><span><h2>
				<?php if(isset($joinedUsersList) && is_array($joinedUsersList) && count($joinedUsersList) > 0)
				echo '"'.$joinedUsersList[0]->Title.'" - ';?>
				Joined Users</h2></span></td></tr>
		<tr><td height="20" colspan="3"></td></tr>

		<tr>
			<td colspan="2">
				<table cellpadding="0"  cellspacing="0" border="0" align="center" width="100%">
					<tr>
						<?php if(isset($joinedUsersList) && is_array($joinedUsersList) && count($joinedUsersList) > 0){ ?>
						<td align="left" width="20%" colspan="2">No. of Users(s)&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
						<?php } ?>
					
						<td align="center">
							<?php if(isset($joinedUsersList)	&&	is_array($joinedUsersList) && count($joinedUsersList) > 0 ) {
						 		pagingControlLatest($tot_rec,'EventJoinedUsers'); ?>
							<?php }?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td height="20" colspan="3"></td></tr>
		<tr><td align="center" colspan="3" ><div class="<?php echo $class; ?>"><span style="display:<?php  echo $display;  ?>"><?php if(isset($msg) && $msg != '') echo $msg;  ?></span></div></td></tr>

<?php	if( isset($joinedUsersList)	&&	is_array($joinedUsersList) && count($joinedUsersList) > 0 ){ ?>
			<tr><td valign="top">
				<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table">
				<tr>
					<th align="center" width="3%">#</th>
					<th align="left" width="65%">User</th>
					<th align="left">Joined Date</th>
				</tr>
			<?php $i=1; foreach($joinedUsersList as $key=>$value){
							$userName	=	'';
							if(isset($value->FirstName)	&&	isset($value->LastName))
								$userName	=	ucfirst($value->FirstName).' '.ucfirst($value->LastName);
							else if(isset($value->FirstName))	
								$userName	=	 ucfirst($value->FirstName);
							else if(isset($value->LastName))	
								$userName	=	ucfirst($value->LastName);
			?>
			<tr>
				<td><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
				<td><?php 	if(isset($userName)	&&	$userName	!=	''	) {
								if(isset($value->Status)	&&	$value->Status	!=	3) {?>
									<a href="#" onclick="window.parent.location.href='UserDetail?viewId=<?php echo $value->id;?>&referList=1';"><?php echo $userName; ?></a>	
					<?php  		}else echo $userName;
							}else echo '-';?>
				</td>
				<td><?php 	if(isset($value->DateCreated) && $value->DateCreated != '0000-00-00 00:00:00'){ 
								$gmt_current_created_time = convertIntocheckinGmtSite($value->DateCreated);
								$time	=  displayConversationDateTime($gmt_current_created_time,$_SESSION['intermingl_ses_from_timeZone']);
								echo $time;
 							  }else echo '-';?>
				</td>
			</tr>
	<?php	}	?>
				</table>
			</td></tr>
<?php	}else{	?>	
			<tr>	<td colspan="3" align="center" style="color:red;">No Users found</td>	</tr>
<?php	} ?>	
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
        innerWidth:$('body').width(),
        innerHeight:setHeight
    });
});
</script>
</html>