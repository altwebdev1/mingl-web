<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
require_once('controllers/ManagementController.php');
$interestObj   =   new ManagementController();
$msg  =	$class	=	'';
$display = 'none';
setPagingControlValues('Id',ADMIN_PER_PAGE_LIMIT);
if(isset($_GET['uid']) && $_GET['uid'] != '' ){
	$user_id		=	$_GET['uid'];
	$fields         = "it.Interest ";
	$interestResult = $interestObj->selectUserInterests($fields,$user_id);
	$tot_rec 		= $interestObj->getTotalRecordCount();
}
commonHead(); ?>
<body>
<div >
	<table cellpadding="10" cellspacing="0" border="0" width="100%" align="center" class="">
		<tr><td><span><h2>Interest</h2></span></td></tr>
		<tr><td height="20" colspan="3"></td></tr>
		<tr>
			<td colspan="2">
				<table cellpadding="0"  cellspacing="0" border="0" align="center" width="100%">
					<tr>
						<?php if(isset($interestResult) && is_array($interestResult) && count($interestResult) > 0){ ?>
						<td align="right" width="30%" colspan="2">No. of Interest(s)&nbsp:&nbsp;<span style="padding-right:5px;"><strong><?php echo $tot_rec; ?></strong></span></td>
						<?php } ?>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td align="center" colspan="3" ><div class="<?php echo $class; ?>"><span style="display:<?php  echo $display;  ?>"><?php if(isset($msg) && $msg != '') echo $msg;  ?></span></div></td></tr>
		<?php
		if( is_array($interestResult) && count($interestResult) > 0 )
		{ 
			?><tr><td valign="top" width="20%"><?php
			foreach($interestResult as $key=>$interest)
			{
				?>
				<div class="interst_popup">
					<?php /*echo displayText($tag->HashtagName,'18'); */ echo displayText($interest->Interest,'18'); ?>					
				</div>
	<?php	}	?>
			</td></tr>
<?php	}
		else
		{	?>	
			<tr>
				<td colspan="3" align="center" style="color:red;">No interest found</td>
			</tr>
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
        innerWidth: "40%",
        innerHeight:setHeight
    });
});

</script>
</html>