<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
require_once('controllers/ServiceController.php');
$serviceObj   =   new ServiceController();
$class =  $msg  = $error  = $error_class = '';
$display = $display_add = 'none';
$jobtype_exists = 0;
if(isset($_GET['cs']) && $_GET['cs']=='1') {
	destroyPagingControlsVariables();
	unset($_SESSION['simplyshredded_sess_search_process']);
	unset($_SESSION['simplyshredded_sess_search_module']);
}
if(isset($_GET['add']) && $_GET['add'] != ''){
	$display_add = 'block';
}
if(isset($_GET['delId']) && $_GET['delId'] != '' ){
	$condition       = "id = ".$_GET['delId'];
	$serviceObj->deleteServiceDetails($condition);
	//$serviceObj->deleteServiceParamsDetails($_GET['delId']);
	header("location:ServiceList?msg=3");		
}
if(isset($_POST['Search']) && $_POST['Search'] != ''){
	destroyPagingControlsVariables();
	$_POST          = unEscapeSpecialCharacters($_POST);
    $_POST          = escapeSpecialCharacters($_POST);
	if(isset($_POST['search_process']))
		$_SESSION['simplyshredded_sess_search_process'] 	= $_POST['search_process'];	
	if(isset($_POST['search_module']))
		$_SESSION['simplyshredded_sess_search_module'] 	= $_POST['search_module'];	
}
$_SESSION['ordertype'] = 'asc';
setPagingControlValues('Ordering',ADMIN_PER_PAGE_LIMIT);
$fields    = "*";
$condition = "";
$serviceResult  = $serviceObj->getServiceList($fields,$condition);
$tot_rec 		 = $serviceObj->getTotalRecordCount();
if($tot_rec!=0 && !is_array($serviceResult)) {
	$_SESSION['curpage'] = 1;
	$serviceResult  = $serviceObj->getServiceList($fields,$condition);
}
if(isset($_GET['msg']) && $_GET['msg'] == 1){
	$msg 		= 	"Service added successfully";
	$display	=	"block";
	$class 		= 	"success_msg";
}
else if(isset($_GET['msg']) && $_GET['msg'] == 2){
	$msg 		 = 	"Service updated successfully";
	$display	 =	"block";
	$class 		 = 	"success_msg";
}
else if(isset($_GET['msg']) && $_GET['msg'] == 3){
	$msg 		 = 	"Service deleted successfully";
	$display	 =	"block";
	$class 		 = 	"error_msg";
}

commonHead(); ?>
<body>
	<table cellpadding="0" cellspacing="0" border="0" width="100%" align="center">
		<tr><td height="20"></td></tr>
		<tr>
			<td align="center">
				<table cellpadding="0" cellspacing="0" border="0" width="95%" align="center">					
					<tr><td colspan="2" class="headermenu"><?php top_header(); ?></td></tr>				    
					<tr>
						<td colspan="2">
							<div class="left_menu sidebar-nav" style="float:left;"><?php side_bar()?></div>
						 	 <div id="content_3" class="content">		
							 <div class="box-header"><h2><i class="icon_service_list"></i>Service List</h2>
							 <span style="float:right"><a style="cursor:pointer;" href="ServiceManage" title="Add Services"> + Add Services</a></span>
						 	</div>					 
							   	 <table cellpadding="0" cellspacing="0" border="0" width="98%" align="center" class="headertable">
									 <tr><td height="20"></td></tr>
									 <tr>
					                    <td valign="top" align="center" colspan="2">
											 <form name="search_service" action="ServiceList" method="post">
					                           <table align="center" cellpadding="0" cellspacing="0" border="0" class="filter_form" width="100%">										       
													<tr><td height="15"></td></tr>
													<tr><td align="center"><table cellpadding="0" cellspacing="0" align="center" width="90%" border="0">
													<tr>
														<td align="left"  width="10%" ><label>Module</label></td>
														<td width="2%"  >:</td>
														<td align="left" height="40" width="20%">
															<input type="text" class="input" name="search_module" id="search_module"  value="<?php  if(isset($_SESSION['simplyshredded_sess_search_module']) && $_SESSION['simplyshredded_sess_search_module'] != '') echo unEscapeSpecialCharacters($_SESSION['simplyshredded_sess_search_module']);  ?>" >
														</td>	
														<td align="left"  width="20%" style="padding-left:10%;" ><label>Purpose</label></td>
														<td width="3%" >:</td>
														<td align="left" width="20%">
															<input type="text" class="input" name="search_process" id="search_process"  value="<?php  if(isset($_SESSION['simplyshredded_sess_search_process']) && $_SESSION['simplyshredded_sess_search_process'] != '') echo unEscapeSpecialCharacters($_SESSION['simplyshredded_sess_search_process']);  ?>" >
														</td>		
														<td align="center" colspan="3"><input type="submit" class="submit_button" name="Search" id="Search" value="Search" title="Search" alt="Search"></td>											
													</tr>
													</td></tr></table>
													<tr><td height="15"></td></tr>
													
												 </table>
											  </form>	
					                    </td>
					               	</tr>
									<tr><td height="20"></td></tr>									
									<tr>
										<td colspan="2">
											<table cellpadding="0"  cellspacing="0" border="0" align="center" width="100%">
												<tr>
													<?php if(isset($serviceResult) && is_array($serviceResult) && count($serviceResult) > 0){ ?>
													<td align="left" width="20%">No. of Service(s)&nbsp:&nbsp;<strong><?php echo $tot_rec; ?></strong></td>
													<?php } ?>
													<td align="center">
															<?php if(is_array($serviceResult) && count($serviceResult) > 0 ) {
															 	pagingControlLatest($tot_rec,'ServiceList'); ?>
															<?php }?>
													</td>
												</tr>
											</table>
										</td>
									</tr> 
									<tr><td height="10"></td></tr><!-- <div class="error_msg w50"><span>No User found.</span></div> -->
									<tr><td colspan="2" align="center">
									<?php displayNotification(); ?>
									<!--	<div class="<?php  //echo $class;  ?> w50"><span><?php //if(isset($msg) && $msg != '') echo $msg;  ?></span></div>	-->
										</td>
									</tr>
									<tr><td height="10"></td></tr>
									<tr>
										<td colspan="2">
										    <form action="ServiceList" class="l_form" name="ServiceForm" id="ServiceForm"  method="post"> 
											<table border="0" cellpadding="0" cellspacing="0" width="100%" class="user_table">
												<tr align="left">
													<th width="2%" align="center">#</th>
													<th width="17%"><?php echo SortColumn('Process','Purpose'); ?></th>
													<th width="10%"><?php echo SortColumn('Module','Module'); ?></th>
													<!-- <th width="10%"><?php //echo SortColumn('Aspects','Aspects'); ?></th> -->
													<th><?php echo SortColumn('ServicePath','Endpoint'); ?></th>
													<th width="3%"><?php echo SortColumn('Ordering','Order'); ?></th>													
													 <th  width="3%" colspan="2" align="center">Action</th>			
												</tr>
												<?php if(isset($serviceResult) && is_array($serviceResult) && count($serviceResult) > 0 ) { 
														foreach($serviceResult as $key=>$value){
												?>									
												<tr>												
													<td align="center"><?php echo (($_SESSION['curpage'] - 1) * ($_SESSION['perpage']))+$key+1;?></td>
													<td align="left"><?php if(isset($value->Process) && $value->Process != ''){ echo $value->Process;} else echo '-';?></td>		
													<td align="left"><?php if(isset($value->Module) && $value->Module != ''){ echo $value->Module;} else echo '-';?></td>	
													<!-- <td align="left"><?php //if(isset($value->Aspects) && $value->Aspects != ''){ echo $value->Aspects;} else echo '-';?></td> -->		
													<td align="left">
														<a href="ServiceDetail?id=<?php if(isset($value->id) && $value->id != '') echo $value->id; ?>" title="View Detail" alt="View Detail" ><?php if(isset($value->ServicePath) && $value->ServicePath != ''){ echo SITE_PATH.$value->ServicePath;} else echo '-';?></a>
													</td>													
													<td align="center"><input type="Text" name="ordering" id="ordering" value="<?php if(isset($value->Ordering) && $value->Ordering != '' ) echo $value->Ordering; ?>" class="order" maxlength="5" onkeypress="return isNumberKey(event);" onchange="setOrderingWebService(this.value,<?php if(isset($value->id) && $value->id != '') echo $value->id; ?>);" ></td>
													<td align="center"><a href="ServiceManage?editId=<?php if(isset($value->id) && $value->id != '') echo $value->id; ?>" title="Edit" alt="Edit" class="edit"></a></td> 
													<td align="center"><a onclick="javascript:return confirm('Are you sure to delete?')" href="ServiceList?delId=<?php if(isset($value->id) && $value->id != '') echo $value->id;?>"  title="Delete" class="delete"></a></td>
												</tr>
												<?php } } else { ?>	
												<tr>
													<td colspan="10" align="center" style="color:red;">No Service found</td>
												</tr>
												<?php } ?>																		
											</table>											
											</form>
										</td>
									</tr>
									<tr><td height="20"></td></tr>
									<tr><td height="10"></td></tr>
								</table>							 
						  	</div>
						</td>
					</tr>
					<tr><td height="15"></td></tr>
				</table>
			</td>
		</tr>
		<tr><td height="20"></td></tr>
	</table>
</body>
<?php commonFooter(); ?>
</html>