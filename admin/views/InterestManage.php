<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/ManagementController.php');
$interestObj   =   new ManagementController();
$msg =  $interest_exists =	$ExistCondition	=	$interest	= $error_class = '';

if( ( isset($_GET['editId']) && $_GET['editId'] != '' ) ||	( isset($_GET['viewId']) && $_GET['viewId'] != ''  ))
{
	if(isset($_GET['viewId']) && $_GET['viewId'] != '')	$condition       = " AND id = ".$_GET['viewId']." and Status != 3 ";
	else	$condition       = " AND id = ".$_GET['editId']." and Status != 3 ";
	$field			 = " * ";
	$interestlDetails  = $interestObj->getInterestsList($field,$condition);
//	echo '<pre>';print_r($interestlDetails);echo '</pre>';
	if(isset($interestlDetails) && is_array($interestlDetails) && count($interestlDetails) > 0){
		$interest 		=	$interestlDetails[0]->Interest;		
		$status		 	=	$interestlDetails[0]->Status;		
	}
}
if(isset($_POST['Add']) || isset($_POST['Save']))
{
	$_POST          =   unEscapeSpecialCharacters($_POST);
   	$_POST          =   escapeSpecialCharacters($_POST);
	if(isset($_POST['add_interest']) && $_POST['add_interest'] != ''){	
		$interest  			=  	stripslashes(trim($_POST['add_interest']));
		$ExistCondition =	" AND Interest = '".trim($_POST['add_interest'])."' ";	
	}
	if(isset($_POST['Save']) && $_POST['Save'] == 'Save')
		$id_exists = " and id != '".$_POST['interest_id']."' and Status != '3' ";
	else //if(isset($_POST['Add']) && $_POST['Add'] == 'Add')
		$id_exists = " "; //" and ht.Status != '3' ";
	$field = " * ";	
	$ExistCondition .= $id_exists;
	$alreadyExist   = $interestObj->selectInterestDetails($field,$ExistCondition);	
	if(isset($alreadyExist) && is_array($alreadyExist) && count($alreadyExist) > 0)
	{
		//if(strtolower($alreadyExist[0]->Interest) == strtolower($interest))
			$interest_exists = 1;
	}
	if($interest_exists != '1')	
	{
		
		if(isset($_POST['Add']) && $_POST['Add'] == 'Add')
		{
			$insert_id   = $interestObj->insertInterestDetails($_POST);
			$_SESSION['notification_msg_code']	=	1;
			//$msg = '1&cs=1';
		}
		if(isset($_POST['Save']) && $_POST['Save'] == 'Save')
		{	
			
			if(isset($_POST['interest_id']) && $_POST['interest_id'] != '')
			{
				$fields    = " Interest   = '".trim($interest)."'"; //, HashtagName = '".base64_encode($hashTagName)."' ";
				if(isset($_POST['interest_status']) && $_POST['interest_status'] != '')
					$fields	.= " , Status = '".$_POST['interest_status']."'";
				$condition = ' id = '.$_POST['interest_id'];
				$interestObj->updateInterestDetails($fields,$condition);
				$_SESSION['notification_msg_code']	=	2;
				//$msg = 2;
			}
		} 
		?>
		<script type="text/javascript">
			//window.parent.location.href = 'InterestList?msg=<?php //echo $msg; ?>';
			window.parent.location.href = 'InterestList';
		</script>
<?php }
	else{
		$error             = "Interest already exists";
		$error_class       = "error_msg";
	}
}
?>
<body onload="fieldfocus('add_interest')">
<div >
	<form name="add_interest_form" id="add_interest_form" action="" method="post">
		<input type="Hidden" name="interest_id" id="interest_id" value="<?php if(isset($_GET['editId']) && $_GET['editId'] != '' ) echo $_GET['editId'];?>">
		<table align="center" cellpadding="0" cellspacing="0" border="0" class="list" width="100%" height="140px">	
			<tr><td colspan="3"><h2><?php if(isset($_GET['editId']) && $_GET['editId'] != '' ) echo "Edit "; else if(isset($_GET['viewId']) && $_GET['viewId'] != '' ) ; else echo 'Add ';?> Interest</h2></td></td></tr>
			<tr><td height="10"></td></tr>
			<tr><td colspan="3" align="center"><div class="<?php  echo $error_class;  ?>"><span><?php if(isset($error) && $error != '') echo $error;  ?></span></div></td></tr>
			<tr><td height="10" colspan="3"></tr>
			<tr height="50">
				<td align="right" width="30%" valign="top"><label>Interest</label></td>
				<td align="center" width="10%" valign="top"> : </td>
				<td align="left" width="60%" valign="top">
				<?php if(isset($_GET['viewId']) && $_GET['viewId'] != '' &&	isset($interest) && $interest != '' ) echo $interest; else { ?>
					<!-- <input type="text" class="input" name="add_goal" id="goal" maxlength="100" title="Enter goal" onkeypress="return isNumberKey_Goal(event)" tabindex="1" value="<?php if(isset($interest) && $interest != '') echo $interest;  ?>" > -->
					<input type="text" class="input" name="add_interest" id="add_interest" maxlength="100"  tabindex="1" value="<?php if(isset($interest) && $interest != '') echo $interest;  ?>" >
				<?php } ?>
				</td>
			</tr>
			
			<?php if(isset($_GET['editId']) ||	isset($_GET['viewId'])){ ?>
			<tr>
				<td align="right"  valign="top" width="30%"><label>Status</label></td>
				<td align="center"  valign="top" width="10%">:</td>
				<td align="left" valign="top" height="20" width="20%" style="padding-bottom:10px;">	
			<?php if(isset($_GET['editId']) && $_GET['editId'] != '' ) { ?>
					<select name="interest_status" id="status" tabindex="2" title="Select Status" style="width:60%;">
						<option value="">Select</option>
						<?php foreach($statusArray as $key => $interest_status) {
								if($key <= 2){
						?>
							<option value="<?php echo $key; ?>" <?php  if(isset($status) && $status==$key)  echo 'selected';?> > <?php echo $interest_status; ?></option>
						<?php }
						}?>
					</select>
				<?php }else if(isset($status) && ( $status==1 || $status==2)) 
					echo $statusArray[$status];
				  else echo '-'; ?> 
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<?php } ?>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td align="left">
					<?php //if(!isset($_GET['viewId']) ) {
					if(isset($_GET['editId']) && $_GET['editId'] != ''){ ?>
						<input type="submit" class="submit_button" name="Save" id="Save" value="Save" title="Save" alt="Save" >
					<?php } else if(!isset($_GET['viewId'])){ ?>
						<input type="submit" class="submit_button" name="Add" id="Add" value="Add" title="Add" alt="Add">
					<?php } ?>
					<input type="button" class="submit_button" name="Close" id="Close" value="Close" title="Close" alt="Close" onclick="parent.$.colorbox.close();" >
					<?php //} ?>
				</td>														
			</tr>
			<td colspan="2" height="30"></td>	
		</table>
	</form>
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