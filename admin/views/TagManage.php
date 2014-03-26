<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/ManagementController.php');
$TagObj   =   new ManagementController();
$msg =  $hashTag_exists = $error_class = '';
$tagExist = 0;
$ExistCondition		=	'';

if((isset($_GET['editId']) && $_GET['editId'] != '')	||	(isset($_GET['viewId'])	&&	$_GET['viewId']!=""))
{
	if(isset($_GET['editId']) && $_GET['editId'] != '')
		$id	=	$_GET['editId'];	
	if(isset($_GET['viewId'])	&&	$_GET['viewId']!="")
		$id	=	$_GET['viewId'];
	$condition       = " AND id = ".$id." AND Status != 3 ";
	$field			 = " * ";
	$TagDetails  = $TagObj->selectTagDetails($field,$condition);
	//echo '<pre>';print_r($TagDetails);echo '</pre>';
	if(isset($TagDetails) && is_array($TagDetails) && count($TagDetails) > 0)
	{
		$tagname 			=	$TagDetails[0]->Tags;		
		$status			 	=	$TagDetails[0]->Status;		
	}
}
if(isset($_POST['Add']) || isset($_POST['Save']))
{
	$_POST          =   unEscapeSpecialCharacters($_POST);
   	$_POST          =   escapeSpecialCharacters($_POST);
	if(isset($_POST['tagname']) && $_POST['tagname'] != ''){
		$ExistCondition 	.=	" AND Tags = '".trim($_POST['tagname'])."' ";
		$tagname	=	stripslashes(trim($_POST['tagname']));
	}
	if(isset($_POST['Save']) && $_POST['Save'] == 'Save')
		$id_exists = " and id != '".$_POST['tagid']."' and Status != '3' ";
	else
		$id_exists = " and Status != '3' ";
	$field = " * ";	
	$ExistCondition .= $id_exists;
	$alreadyExist   = $TagObj->selectTagDetails($field,$ExistCondition);	
	if(isset($alreadyExist) && is_array($alreadyExist) && count($alreadyExist) > 0)
	{
		//if(strtolower($alreadyExist[0]->Tags) == strtolower(trim($_POST['tagname'])))
			$tagExist = 1;
	}	
	if($tagExist != '1')	
	{
		if(isset($_POST['Add']) && $_POST['Add'] == 'Add')
		{
			$insert_id   = $TagObj->insertTagDetails($_POST);
			$_SESSION['notification_msg_code']	=	1;
			//$msg = '1&cs=1';
		/*	mkdir (UPLOAD_POST_PATH_REL.$insert_id, 0777);
			mkdir (UPLOAD_POST_PATH_REL.$insert_id.'/Original', 0777);
			mkdir (UPLOAD_POST_PATH_REL.$insert_id.'/Big', 0777);
			mkdir (UPLOAD_POST_PATH_REL.$insert_id.'/Small', 0777);
			mkdir (UPLOAD_POST_PATH_REL.$insert_id.'/Video', 0777);	*/
		}
		if(isset($_POST['Save']) && $_POST['Save'] == 'Save')
		{	
			//echo "<pre>";print_r($_POST);echo "</pre>";
			if(isset($_POST['tagid']) && $_POST['tagid'] != '')
			{
				if(isset($_POST['tagname']) && $_POST['tagname'] != '')
					$fields    = " Tags   = '".trim($_POST['tagname'])."',";
				if(isset($_POST['tag_status']) && $_POST['tag_status'] != '')
					$fields	.= " Status = '".$_POST['tag_status']."'";
				$condition = ' id = '.$_POST['tagid'];
				$TagObj->updateTagDetails($fields,$condition);
				$_SESSION['notification_msg_code']	=	2;
				//$msg = 2;
			}
		} 
		?>
		<script type="text/javascript">
			 window.parent.location.href = 'TagList';
		</script>
<?php 
	}
	else{
		$error             = "Tag already exists";
		$error_class       = "error_msg";
	}
}
?>
<body onload="fieldfocus('tagname')">
<div >
	<form name="add_tag_form" id="add_tag_form" action="" method="post">
		<input type="Hidden" name="tagid" id="tagid" value="<?php if(isset($_GET['editId']) && $_GET['editId'] != '' ) echo $_GET['editId'];?>">
		<?php //if(!isset($_GET['viewId']))	{?>
		<table align="center" cellpadding="0" cellspacing="0" border="0" class="list" width="100%">	
			<tr><td colspan="3"><h2><?php if(isset($_GET['editId']) && $_GET['editId'] != '' ) echo "Edit "; else if(isset($_GET['viewId']) && $_GET['viewId'] != '' ) ; else echo 'Add ';?>Tag</h2></td></td></tr>
			<tr><td height="10"></td></tr>
			<tr><td colspan="3" align="center"><div class="<?php  echo $error_class;  ?>"><span><?php if(isset($error) && $error != '') echo $error;  ?></span></div></td></tr>
			<tr><td height="10" colspan="3"></tr>
			<tr height="50">
				<td align="right" width="30%" valign="top"><label>Tag</label></td>
				<td align="center" width="10%" valign="top"> : </td>
				<td align="left" width="60%" valign="top">
				<?php if(isset($_GET['viewId']) && $_GET['viewId'] != '' &&	isset($tagname) && $tagname != '' ) echo $tagname; else { ?>										
					<input type="text" class="input" name="tagname" id="tagname" maxlength="100" title="Enter Hashtag" tabindex="1" value="<?php if(isset($tagname) && $tagname != '') echo $tagname;  ?>" ><br>
				<?php } ?>
				</td>
			</tr>
			
			<?php if(isset($_GET['editId']) ||	isset($_GET['viewId'])){ ?>
			<tr>
				<td align="right"  valign="top" width="30%"><label>Status</label></td>
				<td align="center"  valign="top" width="10%">:</td>
				<td align="left" valign="top" height="20" width="20%" style="padding-bottom:10px;">	
			<?php if( isset($_GET['editId']) && $_GET['editId'] != '' ) { ?>
					<select name="tag_status" id="status" tabindex="2" title="Select Status" style="width:60%;">
						<option value="">Select</option>				
						<?php foreach($statusArray as $key => $tag_status) {
								if($key <= 2){
						?>
							<option value="<?php echo $key; ?>" <?php  if(isset($status) && $status==$key)  echo 'selected';?> > <?php echo $tag_status; ?></option>
						<?php 	}
						}?>
					</select>
				<?php }else if(	$_GET['viewId'] !='' && isset($status) && ( $status==1 || $status==2)) 
						echo $statusArray[$status];
				?> 
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<?php }?>

			<tr>
				<td colspan="2">&nbsp;</td>
				<td align="left">
					<?php if(isset($_GET['editId']) && $_GET['editId'] != ''){ ?>
						<input type="submit" class="submit_button" name="Save" id="Save" value="Save" title="Save" alt="Save" >
					<?php } else if(!isset($_GET['viewId'])) { ?>
						<input type="submit" class="submit_button" name="Add" id="Add" value="Add" title="Add" alt="Add">
					<?php } ?>
					<input type="button" class="submit_button" name="Close" id="Close" value="Close" title="Close" alt="Close" onclick="parent.$.colorbox.close();" >														
				</td>														
			</tr>	
			<tr><td height="30"></td></tr>													  
		</table>
		<?php // } //else {	?>
<!--		<table align="center" cellpadding="0" cellspacing="0" border="0" class="list" width="100%">	
			<tr>
				<td align="right" width="30%" valign=""><label>Tag</label></td>
				<td align="center" width="10%" valign=""> : </td>
				<td align="left" width="60%"><?php // echo $tagname; ?></td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td align="right"  valign="top" width="30%"><label>Status</label></td>
				<td align="center"  valign="top" width="10%">:</td>
				<td align="left" valign="top" height="20" width="20%" style="padding-bottom:10px><?php // echo $status; ?></tr>
		</table>
-->
		<?php //} ?>
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