<?php 
require_once('includes/CommonIncludes.php');
admin_login_check();
commonHead();
require_once('controllers/ServiceController.php');
$serviceObj   =   new ServiceController();

$type = 'Add Service';
$id_exists 	= 	'';
$rowCount	=	1;
$jsonValueSet	=	'';
if(isset($_GET['editId']) && $_GET['editId'] != '' ){
	$condition       = " and id = ".$_GET['editId'];
	$field			 = " * ";
	$serviceDetails  = $serviceObj->selectServiceDetails($field,$condition);
	if(isset($serviceDetails) && is_array($serviceDetails) && count($serviceDetails) > 0){
		$process		= $serviceDetails[0]->Process;
		$servicePath	= $serviceDetails[0]->ServicePath;
		$method			= $serviceDetails[0]->Method;
		$inputParam		= $serviceDetails[0]->InputParam;
		$outputParam	= $serviceDetails[0]->OutputParam;
		$moduleName		= $serviceDetails[0]->Module;
		$authorization	= $serviceDetails[0]->Authorization;
		$aspects		= $serviceDetails[0]->Aspects;
		$type = 'Edit Service - '.$process;
	}
	$condition				=	"and ocept.fkEndpointId = ".$_GET['editId'];
	$field					=	"ocept.id as Id,FieldName,SampleData,Required,Explanation,JsonValue ";
	$serviceParamsDetails	=	$serviceObj->selectServiceParamsDetails($field,$condition);
	$rowCount				=	count($serviceParamsDetails);
	if(isset($serviceParamsDetails) && is_array($serviceParamsDetails) && count($serviceParamsDetails) > 0) {
		if(isset($serviceParamsDetails[0]->JsonValue)&& $serviceParamsDetails[0]->JsonValue!=""	&&	count($serviceParamsDetails)==1	){
			$jsonValueSet	=	$serviceParamsDetails[0]->JsonValue;
		}
		else{
			foreach($serviceParamsDetails as $params)
			{
				$paramsIdArr[]		=	$params->Id;
				$fieldNameArr[]		=	$params->FieldName;
				$sampleDataArr[]	=	$params->SampleData;
				$requiredArr[]		=	$params->Required;
				$explanationArr[]	=	$params->Explanation;
			}
		}
	}

}
if(isset($_POST['Add']) || isset($_POST['Save'])){
	$_POST          =   unEscapeSpecialCharacters($_POST);
   	$_POST          =   escapeSpecialCharacters($_POST);
	$fieldNameArr	=	$_POST["field_name"];
	$sampleDataArr	=	$_POST["sample_data"];
	$requiredArr	=	$_POST["required"];
	$explanationArr	=	$_POST["explanation"];
	//$jsonParamArray =	$_POST["json_fields"];
	if(isset($_POST['process']) && $_POST['process'] != '')
		$process  =  $_POST['process'];
	if($process != '')
		$ExistCondition = " and Process = '".$process."'";	
	if(isset($_POST['Save']) && $_POST['Save'] == 'Save')
		$id_exists = " and id != '".$_POST['service_id']."'";
	if(isset($_POST['authorization']) && $_POST['authorization'] != '')
		$authorization  =  $_POST['authorization'];
	else{
		$authorization  =  0;
		$_POST['authorization'] = 0 ;
	}
	if(isset($_POST['aspects']) && $_POST['aspects'] != '')
		$aspects  =  $_POST['aspects'];
	else{
		$aspects  =  '';
		$_POST['aspects'] = '' ;
	}
	$field = " * ";	
	$ExistCondition .= $id_exists;
	$alreadyExist   = $serviceObj->selectServiceDetails($field,$ExistCondition);
	$already_exists = 0;
	if(isset($alreadyExist) && is_array($alreadyExist) && count($alreadyExist) > 0){
		if($alreadyExist[0]->Process == $process)
			$already_exists = 1;
	}
	if($already_exists != '1')	
	{
		if(isset($_POST['Add']) && $_POST['Add'] == 'Add'){
			$insert_id   	= 	$serviceObj->insertServiceDetails($_POST);
			$fkEndpointId	=	$insert_id;
			$_SESSION['notification_msg_code']	=	1;
			//$msg = '1&cs=1';
		}
		if(isset($_POST['Save']) && $_POST['Save'] == 'Save'){	
			if(isset($_POST['service_id']) && $_POST['service_id'] != ''){
				$fields    = "	Process       = '".$process."',
								ServicePath   = '".$_POST['service_path']."',
								Method		  = '".$_POST['method']."',
								InputParam	  = '".$_POST['input_param']."',
								OutputParam   = '".$_POST['output_param']."',
								Module		  = '".$_POST['module_name']."',
								Aspects		  = '".$_POST['aspects']."',
								Authorization = '".$authorization."'" ;
				$condition = ' id = '.$_POST['service_id'];
				$serviceObj->updateServiceDetails($fields,$condition);
				$fkEndpointId	=	$_GET['editId'];
				$_SESSION['notification_msg_code']	=	2;
				//$msg = 2;
			}
		}
		//if(isset($_POST["json_value"])	&&	$_POST["json_value"]	!=''){
		if(isset($_POST["jsonStatus"])	&&	$_POST["jsonStatus"] == 'on'	&&	isset($_POST["json_value"])	&&	$_POST["json_value"]	!=''){
			$serviceObj->deleteServiceParamsDetails($fkEndpointId);
			$update_string	=	" JsonValue	=	'".$_POST["json_value"]."', "." fkEndpointId = ".$fkEndpointId." ";
			$serviceObj->insertJsonServiceParamsDetails($update_string);
		}
		else if(isset($_POST["field_name"])	&&	$_POST["field_name"]	!=''){
			$insertParamsValues	=	"";
			for($index = 0;$index < count($fieldNameArr);$index++) {
				if(isset($fieldNameArr[$index]) && !empty($fieldNameArr[$index]))
					$fieldName		=	$fieldNameArr[$index];
				else if(empty($fieldNameArr[$index]))
					continue;
				if(isset($sampleDataArr[$index]))
					$sampleData		=	$sampleDataArr[$index];
				if(isset($requiredArr[$index]))
					$required		=	$requiredArr[$index];
				if(isset($explanationArr[$index]))
					$explanation	=	$explanationArr[$index];
				$insertParamsValues	.=	"('".$fkEndpointId."','".$fieldName."','".$sampleData."','".$required."','".$explanation."'),";
			}
			$insertParamsValues	=	trim($insertParamsValues,",");
			$serviceObj->deleteServiceParamsDetails($fkEndpointId);
			$serviceObj->insertServiceParamsDetails($insertParamsValues);
		}
		
		header("location:ServiceList");
		die();
	}
	else {
		$error         = "Purpose Already Exists";
	}
}
?>
<body onload="return fieldfocus('process');">
	<table cellpadding="0" cellspacing="0" border="0" width="100%" align="center">
		<tr>
			<td align="center">
				<table cellpadding="0" cellspacing="0" border="0" width="95%" align="center">					
					<tr><td colspan="2" class="headermenu"><?php top_header(); ?></td></tr>
				    <tr>
						<td colspan="2">
							<div class="left_menu sidebar-nav" style="float:left;"><?php side_bar()?></div>
						 	 <div id="content_3" class="content">
							 <div class="box-header"><h2><i class="icon_add_service"></i> <?php echo $type; ?></h2>
						 	</div>
						  		 <table align="center" cellpadding="0" cellspacing="0" border="0" class="form_page list" width="100%">
									<tr><td height="20"></td></tr>
									<?php if(isset($error) && $error!='') {?>
									<tr>
										<td align="center">
											<div class="error_msg w50"><span><?php if(isset($error) && $error != '') echo $error;  ?></span></div>
										</td>
									</tr>
									<tr><td height="20"></td></tr>
									<?php } ?>
									<tr>
										<td align="center" width="100%">
											<div align="center" style="margin-left:120px;">
											<form name="add_service_form" id="add_service_form" action="" method="post" >
											<input type="Hidden" name="service_id" id="service_id" value="<?php if(isset($_GET['editId']) && $_GET['editId'] != '' ) echo $_GET['editId'];?>">
											<table align="center" cellpadding="0" cellspacing="0" border="0"  <?php if(isset($_GET['editId']) && $_GET['editId'] != '' ){?> width="95%" <?php } else {?> width="95%"<?php }?>>
												
												<tr>
													<td width="10%" align="left"  valign="top"><label>Purpose&nbsp;<span class="required_field">*</span></label></td>
													<td width="3%" align="center" valign="top">:</td>
													<td align="left" valign="top" height="60">
														<input type="text" tabindex="1" maxlength="250" value="<?php if(isset($process) && $process != '') echo $process;  ?>" id="process" name="process" class="input" style="width:370px;">
														<!-- <span for="process" generated="true" class="error" style="display:none;"></span> -->
													</td>
												</tr>									
												<!-- <tr><td height="20"></td></tr> -->
												<tr>
													<td  align="left"  valign="top" ><label>Endpoint&nbsp;<span class="required_field">*</span></label></td>
													<td  align="center" valign="top">:</td>
													<td align="left" valign="top" height="60">
														<input type="text" tabindex="2" maxlength="100" value="<?php if(isset($servicePath) && $servicePath != '') echo $servicePath;  ?>" id="service_path" name="service_path" class="input" style="width:370px;">
														<!-- <span for="service_path" generated="true" class="error" style="display:none;"></span> -->
													</td>
												</tr>
												<tr>
													
													<td align="left"  valign="top"><label>Module Name&nbsp;<span class="required_field">*</span></label></td>
													<td  align="center" valign="top">:</td>
													<td align="left"  valign="top" >
														<input type="text" tabindex="3" value="<?php if(isset($moduleName) && $moduleName != '') echo $moduleName;  ?>" id="module_name" name="module_name" class="input" style="width:370px;">
													</td>
												</tr>
												<tr><td height="20"></td></tr>
												<tr>
													
													<td align="left"  valign="top"><label>Aspects</label></td>
													<td  align="center" valign="top">:</td>
													<td align="left"  valign="top" >
														<input type="text" tabindex="3" value="<?php if(isset($aspects) && $aspects != '') echo $aspects;  ?>" id="aspects" name="aspects" class="input" style="width:370px;">
													</td>
												</tr>
												<tr><td height="20"></td></tr>
												<tr>
													
													<td align="left"  valign="top"><label>Authorization</label></td>
													<td  align="center" valign="top">:</td>
													<td align="left"  valign="top" >
														<input type="Radio" tabindex="4" name="authorization" id="authorization" value="1" <?php if(isset($authorization) && $authorization == '1' ) echo 'checked'; ?> >&nbsp;<label>Yes</label>&nbsp;&nbsp;<input type="Radio" tabindex="5"  name="authorization" id="authorization" value="0" <?php if(isset($authorization) && $authorization == '0' ) echo 'checked'; ?> >&nbsp;<label>No</label>&nbsp;&nbsp;
													</td>
												</tr>
												<tr><td height="20"></td></tr>
												<tr>
													<td  align="left"  valign="top"><label>Method&nbsp;<span class="required_field">*</span></label></td>
													<td  align="center" valign="top">:</td>
													<td align="left" valign="top" height="50">
														<select id="method" name="method" class="input" style="width:370px;" tabindex="6">
															<option value="">Select Method</option>
															<?php foreach($methodArray as $value) { ?>
																<option value="<?php echo $value; ?>"<?php if(isset($method) && $method ==$value) { ?>selected<?php } ?>><?php echo $value; ?></option>
															<?php } ?>
														</select>
													</td>
												</tr>
												<tr class="inputMethodParamMultiple">
													<td align="left" valign="top"><label>POST Method </label></td>
													<td align="center" valign="top">:</td>
													<td align="left"  valign="top" height="50">
													
														<input type="checkbox" name="jsonStatus" id="methodparam" <?php if(isset($jsonValueSet) && $jsonValueSet != '' ){?>checked<?php }?> >&nbsp;&nbsp;Json
														<div></div>
													</td>
												</tr>									
												<tr class="inputParamDefault">
													<td align="left" valign="top"><label>Input Param</label></td>
													<td align="center" valign="top">:</td>
													<td align="left"  valign="top" height="225">
														<textarea rows="10" cols="45" tabindex="7" id="input_param" name="input_param"><?php if(isset($inputParam) && $inputParam != '' ) echo $inputParam; ?></textarea>
														<!-- <span for="input_param" generated="true" class="error" style="display:none;"></span> -->
														<div>(Separate param with new line)</div>
													</td>
												</tr>
												<tr class="jsonInput">
													<td align="left" valign="top"><label>Input Param</label></td>
													<td align="center" valign="top">:</td>
													<td align="left"  valign="top" >
													<textarea rows="6" cols="45" tabindex="7" id="json_value" name="json_value"><?php if(isset($jsonValueSet) && $jsonValueSet != '' ) echo $jsonValueSet; ?></textarea>

													<br><div></div>
													</td>
													
												</tr>
												
												<tr class="inputParamMultiple">
													<td align="left" valign="top" style="padding-top:7px"><label>Input Param&nbsp;<span class="required_field">*</span></label></td>
													<td align="center" valign="top" style="padding-top:7px">:</td>
													<td align="left" valign="top">
														<table cellpadding="0" cellspacing="7" id="inputParam" border="0" width="100%" align="center">
															<tr align="center">
																<th width="23%" style="color:#494949;" align="left">Field Name</th>
																<th width="23%" style="color:#494949;" align="left">Data</th>
																<th width="10%" style="color:#494949;" align="left">Required</th>
																<th width="36%" style="color:#494949;" align="left">Description</th>
																<th width="4%"></th>
																<th width="4%"></th>
															</tr>
															<?php for($index = 0;$index < $rowCount;$index++) {?>
															<tr align="center" class="clone" clone="<?php echo $index;?>">
																<td valign="top" align="left">
																	<input type="text" name="field_name[]" class="input" id="field_name" tabindex="8" maxlength="100" value="<?php if(isset($fieldNameArr) && is_array($fieldNameArr)) echo htmlspecialchars($fieldNameArr[$index]);?>" style="width:160px">
																	<br>
																	<span id='field_name_empty' class="error_empty"></span>
																</td>
																<td valign="top" align="left">
																	<input type="text"  name="sample_data[]" class="input" id="sample_data" tabindex="9" maxlength="100" value="<?php if(isset($sampleDataArr) && is_array($sampleDataArr)) echo htmlspecialchars($sampleDataArr[$index]);?>" style="width:160px">
																	<br>
																	<span id='sample_data_empty' class="error_empty"></span>
																</td>
																<td valign="top" align="left">
																	<select name="required[]" tabindex="10">
																		<option value="0" <?php if(isset($requiredArr) && is_array($requiredArr) && $requiredArr[$index] == 0) echo "selected";?>>No</option>
																		<option value="1"<?php if(isset($requiredArr) && is_array($requiredArr) && $requiredArr[$index] == 1) echo "selected";?>>Yes</option>
																	</select>
																</td>
																<td valign="top" align="left"><textarea rows="2" cols="32" tabindex="11" name="explanation[]"><?php if(isset($explanationArr) && is_array($explanationArr)) echo htmlspecialchars($explanationArr[$index]);?></textarea></td>
																<td align="left"><a href="javascript:void(0)" onclick="addRow(this)"><img src="webresources/images/add.png" width="15" height="15" alt=""></a></td>
																<td align="left"><a href="javascript:void(0)" onclick="delRow(this)"><img src="webresources/images/remove.png" width="15" height="15" alt=""></a></td>
															</tr>
															<?php }?>
														</table>
													</td>	
												</tr>	
												<tr><td height="20"></td></tr>																		
												<tr>
													<td align="left"  valign="top"><label>Output Param&nbsp;<span class="required_field">*</span></label></td>
													<td align="center" valign="top">:</td>
													<td align="left" width=""  valign="top" height="215">
														<textarea rows="10" cols="45" tabindex="12" id="output_param" name="output_param"><?php if(isset($outputParam) && $outputParam != '' ) echo $outputParam;  ?></textarea>
														<!-- <span for="output_param" generated="true" class="error" style="display:none;"></span> -->
													</td>
												</tr>
												<tr><td height="10"></td></tr>
												<tr>										
													<td colspan="2">&nbsp;</td>
													<td align="left">
														<?php if(isset($_GET['editId']) && $_GET['editId'] != '' ){ ?>
														<input type="submit" value="Save" id="Save" name="Save" class="submit_button" onclick="return validateinputparam();" title="Save" alt="Save" tabindex="13">
														<?php } else { ?>
														<input type="submit" value="Add" id="Add" name="Add" class="submit_button" onclick="return validateinputparam();" title="Add" alt="Add" tabindex="14">
														<?php } ?>
														<a href="ServiceList" class="submit_button" name="Back" id="Back" value="Back" title="Back" title="Back" tabindex="11">Back </a>
													</td>
												</tr> 
												<tr><td height="30"></td></tr>
											</table>
											 </form>	
											</div>
										</td>
									</tr>
								  
								</table>
						  	</div>
						</td>
					</tr>
					<tr><td height="15"></td></tr>
				</table>
			</td>
		</tr>
	</table>
</body>
<?php commonFooter(); ?>
</html>
