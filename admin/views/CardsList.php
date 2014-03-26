<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/CardController.php');
$cardsObj   =   new CardController();
$display   =   'none';
$class  =  $msg    = $cover_path = '';

if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	unset($_SESSION['intermingl_sess_cart_userId']);
	unset($_SESSION['intermingl_sess_card_userName']);
	unset($_SESSION['intermingl_sess_cardUser_company']);
	unset($_SESSION['intermingl_sess_cardUser_country']);
	//unset($_SESSION['intermingl_sess_event_end']);
	//unset($_SESSION['intermingl_sess_event_end']);
	if(isset($_SESSION['intermingl_ses_from_timeZone']))
		unset($_SESSION['intermingl_ses_from_timeZone']);

}
if(isset($_POST['do_action']) && $_POST['do_action'] != ''){
	if(isset($_POST['checkedrecords']) && is_array($_POST['checkedrecords']) && count($_POST['checkedrecords']) > 0	&&	isset($_POST['bulk_action']) && $_POST['bulk_action']!='')
		$delete_ids = implode(',',$_POST['checkedrecords']);

	if(isset($delete_ids) && $delete_ids != '')
		$delete_id = $delete_ids;
}
if(isset($_GET['delId']) && $_GET['delId']!=''){
	$delete_id      = $_GET['delId'];
}
if(isset($delete_id) && $delete_id != ''){	
	$update_string   	 = " Status = 3 ";
	$condition       	 = " id IN(".$delete_id.") ";
	$cardsResult	 = $cardsObj->updateCardDetails($update_string,$condition);
	$_SESSION['notification_msg_code']	=	3;
	header("location:CardsList");	
	die();
}
if(isset($_POST['Search']) && $_POST['Search'] != ''){
	destroyPagingControlsVariables();
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
//	echo "<pre>";print_r($_POST);echo "</pre>";
	if(isset($_POST['card_user_name']))
		$_SESSION['intermingl_sess_card_userName'] 	= $_POST['card_user_name'];
	if(isset($_POST['card_type']))
		$_SESSION['intermingl_sess_card_type']	    = $_POST['card_type'];
	if(isset($_POST['user_card_company']))
		$_SESSION['intermingl_sess_cardUser_company']	= $_POST['user_card_company'];
	if(isset($_POST['user_card_country']))
		$_SESSION['intermingl_sess_cardUser_country']	= $_POST['user_card_country'];
}
setPagingControlValues('id',ADMIN_PER_PAGE_LIMIT);
//setPagingControlValues('id',2);
$fields    = " c.id,c.Card,c.Summary,u.FirstName,u.LastName,u.Company,u.Location,u.Country,u.Email ";
$condition = " and c.Status != '3' ";
if(isset($_GET['viewCardUserId']) && $_GET['viewCardUserId']!='') {
	$fields			=	"  ";
	$_SESSION['intermingl_sess_cart_userId']	= $_GET['viewCardUserId'];
	//$condition		=	" ";//"AND fkUserId = ".$_GET['viewCardUserId']." ";//", Status != 3";
	//$cardsResult	=	$cardsObj->getCardsList($fields,$condition);
}

$cardsListResult  = $cardsObj->getCardsList($fields,$condition);
$tot_rec 		 = $cardsObj->getTotalRecordCount();
if($tot_rec!=0 && !is_array($cardsListResult)) {
	$_SESSION['curpage'] = 1;
	$cardsListResult  = $cardsObj->getCardsList($fields,$condition);
}
//	echo "<pre>";print_r($cardsListResult);echo "</pre>";
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
						 <div class="box-header">
							<h2><i class="icon_userlist"></i>Card List</h2>
						 <!-- <span style="float:right"><a href="CardsManage" title="Add Card"><strong>+ Add Card</strong></a></span>	--> 
						 </div>
				            <table cellpadding="0" cellspacing="0" border="0" width="98%" align="center" class="headertable">
								<tr><td height="20"></td></tr>
								<tr>
				                    <td valign="top" align="center" colspan="2">
										
										 <form name="search_category" action="CardsList" method="post">
				                           <table align="center" cellpadding="0" cellspacing="0" border="0" class="filter_form" width="100%">									       
												<tr><td height="15"></td></tr>
												<tr>													
													<td width="10%" style="padding-left:20px;"><label>Name</label></td>
													<td width="3%" align="center">:</td>
													<td align="left"  height="40">
													
														<input type="text" class="input" id="card_user_name" name="card_user_name"  value="<?php  if(isset($_SESSION['intermingl_sess_card_userName']) && $_SESSION['intermingl_sess_card_userName'] != '') echo unEscapeSpecialCharacters($_SESSION['intermingl_sess_card_userName']);  ?>" >
													</td>
													<td width="7%" style="padding-left:20px;"><label>Card</label></td>
													<td width="3%" align="center">:</td>
													<td align="left"  height="40">
														<select name="card_type" id="card_type" tabindex="2" title="Select Card Type" style="width:30%;">
															<option value="">Select</option>
														<?php 	foreach($cardTypeArray as $key => $user_status) { ?>
															<option value="<?php echo $key; ?>" <?php  if(isset($_SESSION['intermingl_sess_card_type']) && $_SESSION['intermingl_sess_card_type'] != '' && $_SESSION['intermingl_sess_card_type'] == $key) echo 'Selected';  ?>><?php echo $user_status; ?></option>
														<?php 	} ?>
														</select>
<!--	<input type="text" class="input" name="card_name" id="card_name"  value="<?php  //if(isset($_SESSION['intermingl_sess_cardName']) && $_SESSION['intermingl_sess_cardName'] != '') echo unEscapeSpecialCharacters($_SESSION['intermingl_sess_cardName']);  ?>" >	-->
				
													</td>
												</tr>
												<tr><td height="10"></td></tr>
												<tr>
													<td width="10%" style="padding-left:20px;" align="left"><label>Company</label></td>
													<td width="3%" align="center">:</td>
													<td height="40" align="left" >
														<input type="text"  class="input" name="user_card_company" id="user_card_company" title="Company" value="<?php if(isset($_SESSION['intermingl_sess_cardUser_company']) && $_SESSION['intermingl_sess_cardUser_company'] != '') echo $_SESSION['intermingl_sess_cardUser_company']; else echo '';?>" >
													</td>
													<td width="10%" style="padding-left:20px;" align="left"><label>Country</label></td>
													<td width="3%" align="center">:</td>
													<td height="40" align="left" >
														<input  type="text" class="input" name="user_card_country" id="user_card_country" title="Country" value="<?php if(isset($_SESSION['intermingl_sess_cardUser_country']) && $_SESSION['intermingl_sess_cardUser_country'] != '') echo $_SESSION['intermingl_sess_cardUser_country']; else echo '';?>" >
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
												<?php if(isset($cardsListResult) && is_array($cardsListResult) && count($cardsListResult) > 0){ ?>
												<td align="left" width="20%">No. of Cards(s)&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
												<?php } ?>
												<td align="center">
														<?php if(isset($cardsListResult)	&&	is_array($cardsListResult) && count($cardsListResult) > 0 ) {
														 	pagingControlLatest($tot_rec,'CardsList'); ?>
														<?php }?>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr><td height="10"></td></tr><!-- <div class="error_msg w50"><span>No User found.</span></div> -->
								<tr><td colspan= '2' align="center">
								<?php displayNotification(); ?>
<!--								<div class="<?php  //echo $class;  ?> w50"><span><?php //if(isset($msg) && $msg != '') echo $msg;  ?></span></div></td></tr>	-->
								<tr><td height="10"></td></tr>
								<tr>
									<td colspan="2">
									 <?php if(isset($cardsListResult) && is_array($cardsListResult) && count($cardsListResult) > 0 ) { ?>
									  <form action="CardsList" class="l_form" name="CardListForm" id="CardListForm"  method="post"> 
										<!-- <input type="hidden" value="" id="message_hidden" name="message_hidden"/> -->
										<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table">
											<tr align="left">
												<th align="center" width="3%" style="text-align:center;"><input onclick="checkAllRecords('CardListForm');" type="Checkbox" name="checkAll"/></th>
												<th align="center" width="3%" style="text-align:center;">#</th>
												<th width="8%"><?php echo SortColumn('Card','Card'); ?></th>			
												<th width="15%"><?php echo SortColumn('FirstName','User Name'); ?></th>
												<!-- <th width="8%"><?php //echo SortColumn('LastName','Last Name'); ?></th> -->
												<th width="8%"><?php echo SortColumn('Email','Email'); ?></th>
												<th width="8%"><?php echo SortColumn('Company','Company'); ?></th>
												<th width="8%"><?php echo SortColumn('Location','Location'); ?></th>
												<th width="8%"><?php echo SortColumn('Country','Country'); ?></th>
<!--												<th width="8%">Photo<?php //echo SortColumn('TwitterId','Twitter Id'); ?></th>		-->
<!--
												<th colspan="3" align="center" width="15%">Action</th>
-->
											</tr>
											
												<?php	foreach($cardsListResult as $key=>$value){
															if(isset($value->FirstName)	&&	isset($value->LastName)) 	
																$userName	=	ucfirst($value->FirstName).' '.ucfirst($value->LastName);
															else if(isset($value->FirstName))	
																$userName	=	 ucfirst($value->FirstName);
															else if(isset($value->LastName))	
																$userName	=	ucfirst($value->LastName);
												?>									
											<tr>
												<td align="center"><input id="checkedrecords" name="checkedrecords[]" value="<?php  if(isset($value->id) && $value->id != '') echo $value->id  ?>" type="checkbox" /></td>
												<td align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>												
												<td><?php if(isset($value->Card) && $value->Card != ''	&& isset($cardTypeArray[$value->Card])){ echo $cardTypeArray[$value->Card]; }else echo '-';?></td>
												<td><?php if(isset($userName) && $userName != '') echo $userName; else echo '-';
												//if(isset($value->FirstName) && $value->FirstName != '' ){ echo $value->FirstName;}else echo '-';?></td>
												<!-- <td><?php //if(isset($value->LastName) && $value->LastName != '' ){ echo $value->LastName;}else echo '-';?></td>	-->
												<td><?php if(isset($value->Email) && $value->Email != ''){ echo $value->Email;} else echo '-';?></td>
												<td><?php if(isset($value->Company) && $value->Company != '' ){ echo $value->Company;}else echo '-';?></td>
												<td><?php if(isset($value->Location) && $value->Location != ''){ echo $value->Location;} else echo '-';?></td>
												<td><?php if(isset($value->Country) && $value->Country != ''){ echo $value->Country;} else echo '-';?></td>
												
<!--
												<td align="center"  style="background: none no-repeat; background-size:cover;" ><?php //if(isset($image_path) && $image_path != ''){ ?><a <?php //if(isset($image_path) && basename($image_path) != "no_user.jpeg") { ?>href="<?php //echo $original_path; ?>" class="user_image_pop_up" title="View Photo" <?php //} ?> ><img width="36" height="36" src="<?php //echo $image_path;?>" ></a> <?php //} ?></td>
-->
<!--
												<td align="center"><a href="CardsManage?editId=<?php //if(isset($value->id) && $value->id != '') echo $value->id; ?>" title="Edit" alt="Edit" class="edit"></a></td>
												<td align="center"><a href="CardsDetail?viewId=<?php //if(isset($value->id) && $value->id != '') echo $value->id; ?>" title="View" alt="View" class="view"></a></td>
												<td align="center"><a onclick="javascript:return confirm('Are you sure to delete?') " href="CardsList?delId=<?php //if(isset($value->id) && $value->id != '') echo $value->id;?>" title="Delete" alt="Delete" class="delete"></a></td>
-->
											</tr>
											<?php }?>																		
										</table>
										<?php if(isset($cardsListResult) && is_array($cardsListResult) && count($cardsListResult) > 0){ 
											bulk_action(array(3=>'Delete'));
										?>
<!--										<table border="0" cellpadding="0" cellspacing="0" width="80%" class="">
											<tr><td height="10"></td></tr>
											<tr >
												<td align="left">
													<input type="submit" onclick="return deleteAll('Cards');" class="submit_button" name="Delete" id="Delete" value="Delete" title="Delete" alt="Delete">&nbsp;&nbsp;													
												</td>
												<td align="left">
<!--
												<a href="UserManage?editId=<?php //if(isset($_GET['viewId']) && $_GET['viewId'] != '') echo $_GET['viewId']; ?>" title="Edit" alt="Edit" class="submit_button">Edit</a>			

											<a href="UserList" class="submit_button" name="Back" id="Back" title="Back" alt="Back" >Back </a>

												</td>
											</tr>
											<tr><td height="10"></td></tr>
										</table>
-->
										<?php } ?>
										</form>
										<?php } else { ?>	
											<tr>
												<td colspan="16" align="center" style="color:red;">No Cards found</td>
											</tr>
											<tr><td height="30"></td></tr>
									<?php } ?>
									</td>
								</tr>
				            </table>
				        </div>
				     </td></tr>
				</table>
			</td>
		</tr>
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
</script>
</html>
