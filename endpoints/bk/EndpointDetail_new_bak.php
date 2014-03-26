<?php
require_once("../admin/config/config.php");
require_once("../admin/config/db_config.php");
require_once("../admin/models/Database.php");

global $globalDbManager;
$globalDbManager = new Database();
$globalDbManager->dbConnect = $globalDbManager->connect($dbConfig['hostName'], $dbConfig['userName'], $dbConfig['passWord'], $dbConfig['dataBase']);

if(isset($_GET['viewId']) && $_GET['viewId'] != '' )
	$viewId = $_GET['viewId'];
else
	header("location:EndpointList.php");

$sql			= "SELECT * FROM oauth_client_endpoints WHERE id=".$viewId;
$endpointDetail	= $globalDbManager->sqlQueryArray($sql);
if( !$endpointDetail )
	header("location:EndpointList.php");
$process		= $endpointDetail[0]->Process;
$authorization	= $endpointDetail[0]->Authorization;
$servicePath	= $endpointDetail[0]->ServicePath;
$method			= $endpointDetail[0]->Method;
$inputParam		= $endpointDetail[0]->InputParam;
$outputParam	= $endpointDetail[0]->OutputParam;
//echo '<pre>';print_r($endpointDetail);echo '</pre>';
$sql			= "SELECT * FROM oauth_client_endpoints_params WHERE fkEndpointId =".$viewId;
$endpointParam	= $globalDbManager->sqlQueryArray($sql);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title><?php echo SITE_TITLE; ?></title>
	<link rel="STYLESHEET" type="text/css" href="styles.css">	
	<link rel="icon" href="../admin/webresources/images/favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="../admin/webresources/images/favicon.ico" type="image/x-icon" />
</head>
<body>
<div class="content" style="margin-top:2%">
	<div>
		<span class="elem_title" style="border:0px; font-size:26px"><?php if(isset($process) && $process != '') echo $process; else echo '-'; ?></span>
		<span style="float:right;color:red">* indicates required</span>
	</div>
	<div style="clear:both"></div>
	<div class="head_title title_strip" style="padding:30px 0px 0px 0px;">Resource URL</div>
	<div style="padding:5px 10px 20px 0px; color:#B35900;"><?php if(isset($servicePath) && $servicePath != '') echo SITE_PATH.$servicePath;  else echo '-'; ?></div>
	<table class="endpoint_table" style="margin-left:90px" cellpadding="0" cellspacing="0" border="0"  width="30%">
		<tr>
			<td width="10%" align="left"  valign="top"><label>Authorization</label></td>
			<td width="20%" align="left"  valign="top"><label>Method</label></td>
			
		</tr>
		<tr>
			<td width="20%" align="left" valign="top"><?php if(isset($authorization) && $authorization == '0') echo "No";  else echo 'Yes'; ?></td>
			<td align="left" valign="top"><?php if(isset($method) && $method != '' ) echo strtoupper($method); else echo '-'; ?></td>
		</tr>
	</table>
	<?php 	
	if(isset($method) && ($method == 'POST' || $method == 'GET' ) ) { 
	if(isset($endpointParam) && is_array($endpointParam) && count($endpointParam) > 0 ) { ?>
	<div class="head_title title_strip" style="padding:30px 10px 0px 0px;">Parameters</div>
	<div style="padding:5px 10px 20px 0px; color:#B35900;">All parameters are optional, unless otherwise indicated.</div>
	
	<table class="endpoint_table" style="margin-left:90px" cellpadding="0" cellspacing="0" border="0"  width="50%">
		<?php 
			foreach($endpointParam as $paramkey=>$paramvalue) {  ?>
		<tr>
			<td width="15%" align="left" valign="top" style="word-wrap:break-word;white-space:wrap;"><?php if(isset($paramvalue->FieldName) && $paramvalue->FieldName != '') echo  $paramvalue->FieldName ;  else echo '-'; ?></td>
			<td width="15%" align="left" valign="top" style="word-wrap:break-word;white-space:wrap;"><?php if(isset($paramvalue->SampleData) && $paramvalue->SampleData != '') echo $paramvalue->SampleData; else echo '-'; ?></td>			
			<td width="20%" align="left" valign="top" style="word-wrap:break-word;white-space:wrap;"><?php if(isset($paramvalue->Required) && $paramvalue->Required == '1') echo '<b>Required</b>&nbsp;&nbsp;' ;  if(isset($paramvalue->Explanation) && $paramvalue->Explanation != '') echo  $paramvalue->Explanation ;  else echo '-'; ?></td>
		</tr>
		<?php } ?>
	</table>
	

	<?php } } else if(isset($method) && ($method == 'PUT' || $method == 'GET' ) ) {  
	
	?>
	<?php if(isset($inputParam) && trim($inputParam) != '' )  { ?>
	<div class="head_title title_strip" style="padding:30px 10px 0px 0px;">Parameters</div>
	<div style="width:600px;word-wrap:break-word;color:#555555; margin-top:10px; margin-left:90px">
		<pre>
		<?php
			$inputParam = str_replace("*", "<span style='color:red'>*</span>",$inputParam);
			echo '<ul><li>'.str_replace(array("\r","\n"),array('',"\n","</li>\n<li>"),trim($inputParam,"\n\r")).'</li></ul>';
		?>
		</pre>
	</div>
	<?php
		} } 
		?>
	<div class="head_title title_strip" style="padding:30px 10px 0px 0px;">Sample Response</div>
	<div class="param" style="margin-top:20px; margin-left:90px" >
		<pre ><?php if(isset($outputParam) && $outputParam != '' ) echo $outputParam; else echo '-'; ?></pre>
	</div>
	<div style="padding:20px 10px 10px 90px;">
		<a href="EndpointList.php" class="submit_button" name="Back" id="Back" value="Back" title="Back" title="Back">Back </a>
	</div>
</div>
</body>
</html>