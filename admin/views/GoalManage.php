<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/ManagementController.php');
$goalObj   =   new ManagementController();
$msg =  $goal_exists =	$ExistCondition	=	$goal	= $error_class = '';
$hashTagName_exists = 0;
if( ( isset($_GET['editId']) && $_GET['editId'] != '' ) ||	( isset($_GET['viewId']) && $_GET['viewId'] != ''  ))
{
	if(isset($_GET['viewId']) && $_GET['viewId'] != '')	$condition       = " AND id = ".$_GET['viewId']." and Status != 3 ";
	else	$condition       = " AND id = ".$_GET['editId']." and Status != 3 ";
	$field			 = " * ";
	$goalDetails  = $goalObj->getGoalsList($field,$condition);
	if(isset($goalDetails) && is_array($goalDetails) && count($goalDetails) > 0){
		$goal 			=	$goalDetails[0]->Goal;		
		$status		 	=	$goalDetails[0]->Status;		
	}
}
if(isset($_POST['Add']) || isset($_POST['Save']))
{
	$_POST          =   unEscapeSpecialCharacters($_POST);
   	$_POST          =   escapeSpecialCharacters($_POST);
	if(isset($_POST['add_goal']) && $_POST['add_goal'] != ''){	
		$goal  			=  	stripslashes(trim($_POST['add_goal']));
		$ExistCondition =	" AND Goal = '".trim($_POST['add_goal'])."' ";	
	}
	if(isset($_POST['Save']) && $_POST['Save'] == 'Save')
		$id_exists = " and id != '".trim($_POST['goal_id'])."' and Status != '3' ";
	else //if(isset($_POST['Add']) && $_POST['Add'] == 'Add')
		$id_exists = " "; //" and ht.Status != '3' ";
	$field = " * ";	
	$ExistCondition .= $id_exists;
	$alreadyExist   = $goalObj->selectGoalDetails($field,$ExistCondition);	
	if(isset($alreadyExist) && is_array($alreadyExist) && count($alreadyExist) > 0)
	{
		//if(strtolower($alreadyExist[0]->Goal) == strtolower($goal))
			$goal_exists = 1;
	}
	if($goal_exists != '1')	
	{
		
		if(isset($_POST['Add']) && $_POST['Add'] == 'Add')
		{
			$insert_id   = $goalObj->insertGoalDetails($_POST);
			$_SESSION['notification_msg_code']	=	1;
			//$msg = '1&cs=1';
		}
		if(isset($_POST['Save']) && $_POST['Save'] == 'Save')
		{	
			
			if(isset($_POST['goal_id']) && $_POST['goal_id'] != '')
			{
				$fields    = " Goal   = '".trim($goal)."'"; //, HashtagName = '".base64_encode($hashTagName)."' ";
				if(isset($_POST['goal_status']) && $_POST['goal_status'] != '')
					$fields	.= " , Status = '".$_POST['goal_status']."'";
				$condition = ' id = '.$_POST['goal_id'];
				$goalObj->updateGoalDetails($fields,$condition);
				//$msg = 2;
				$_SESSION['notification_msg_code']	=	2;
			}
		} 
		?>
		<script type="text/javascript">
			//window.parent.location.href = 'GoalList?msg=<?php echo $msg; ?>';
			window.parent.location.href = 'GoalList';
		</script>
<?php }
	else{
		$error             = "Goal already exists";
		$error_class       = "error_msg";
	}
}
?>
<body onload="fieldfocus('goal')">
<div>
	<form name="add_hashtag_form" id="add_goal_form" action="" method="post">
		<input type="Hidden" name="goal_id" id="goal_id" value="<?php if(isset($_GET['editId']) && $_GET['editId'] != '' ) echo $_GET['editId'];?>">
		<table align="center" cellpadding="0" cellspacing="0" border="0" class="list" width="100%">	
			<tr><td colspan="3"><h2><?php if(isset($_GET['editId']) && $_GET['editId'] != '' ) echo "Edit "; else if(isset($_GET['viewId']) && $_GET['viewId'] != '' ) ; else echo 'Add ';?> Goal</h2></td></td></tr>
			<tr><td height="10"></td></tr>
			<tr><td colspan="3" align="center"><div class="<?php  echo $error_class;  ?>"><span><?php if(isset($error) && $error != '') echo $error;  ?></span></div></td></tr>
			<tr><td height="10" colspan="3"></tr>
			<tr height="50">
				<td align="right"  width="30%" valign="top"><label>Goal</label></td>
				<td align="center" width="10%" valign="top"> : </td>
				<td align="left"  valign="top" width="60%" >
				<?php if(isset($_GET['viewId']) && $_GET['viewId'] != '' &&	isset($goal) && $goal != '' ) echo $goal; else { ?>
					<!-- <input type="text" class="input" name="add_goal" id="goal" maxlength="100" title="Enter goal" onkeypress="return isNumberKey_Goal(event)" tabindex="1" value="<?php //if(isset($goal) && $goal != '') echo $goal;  ?>" > -->
					<input type="text" class="input" name="add_goal" id="goal" maxlength="100" title="Enter goal"  tabindex="1" value="<?php if(isset($goal) && $goal != '') echo $goal;  ?>" >
					<span height="30"></span>
				<?php } ?>
				</td>
			</tr>
			

			<?php if(isset($_GET['editId']) ||	isset($_GET['viewId'])){ ?>
			<tr>
				<td align="right"  valign="top" width="30%"><label>Status</label></td>
				<td align="center"  valign="top" width="10%">:</td>
				<td align="left" valign="top" height="20" width="20%" style="padding-bottom:10px;">	
				<?php if(isset($_GET['editId']) && $_GET['editId'] != '' ) { ?>
					<select name="goal_status" id="status" tabindex="2" title="Select Status" style="width:60%;">
						<option value="">Select</option>
						<?php foreach($statusArray as $key => $goal_status) {
								if($key <= 2){
						?>
						
							<option value="<?php echo $key; ?>" <?php  if(isset($status) && $status==$key)  echo 'selected';?> > <?php echo $goal_status; ?></option>
						<?php }
						}?>
					</select>
			<?php }else if(isset($status) && ( $status==1 || $status==2)) 
					echo $statusArray[$status];
				  else echo '-';
			?> 	
<!--					<input type="radio"  name="hashtag_status" value="1" <?php if(isset($status) && $status==1) { ?>checked <?php } ?> title="Active">&nbsp;&nbsp;Active &nbsp;&nbsp;
					<input type="radio"  name="hashtag_status" value="2"  <?php if(isset($status) && $status==2) { ?>checked <?php } ?> title="Inactive">&nbsp;&nbsp;Inactive &nbsp;&nbsp;
-->
				</td>
			</tr>
			<tr><td height="20" colspan="3"></tr>
			<?php }?>

			<tr>
				<td colspan="2">&nbsp;</td>
				<td align="left">
					<?php if(isset($_GET['editId']) && $_GET['editId'] != ''){ ?>
						<input type="submit" class="submit_button" name="Save" id="Save" value="Save" title="Save" alt="Save" >
					<?php } else if(!isset($_GET['viewId'])){ ?>
						<input type="submit" class="submit_button" name="Add" id="Add" value="Add" title="Add" alt="Add">
					<?php } ?>
					<input type="button" class="submit_button" name="Close" id="Close" value="Close" title="Close" alt="Close" onclick="parent.$.colorbox.close();" >
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