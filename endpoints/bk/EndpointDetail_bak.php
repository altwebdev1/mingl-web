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
$sql			= mysql_query("SELECT * FROM oauth_client_endpoints WHERE id=".$viewId);
$endpointDetail	= mysql_fetch_object($sql);
if( !$endpointDetail )
	header("location:EndpointList.php");
$process		= $endpointDetail->Process;
$authorization	= $endpointDetail->Authorization;
$servicePath	= $endpointDetail->ServicePath;
$method			= $endpointDetail->Method;
$inputParam		= $endpointDetail->InputParam;
$outputParam	= $endpointDetail->OutputParam;
//echo '<pre>';print_r($endpointDetail);echo '</pre>';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title>API Endpoints</title>
	<link rel="STYLESHEET" type="text/css" href="styles.css">	
</head>
<body>
<div class="content" style="margin-top:2%">
	<div class="elem_title"><?php if(isset($process) && $process != '') echo $process; else echo '-'; ?></div>
	<div style="padding:20px 10px 20px 0px; color:#B35900;"><?php if(isset($servicePath) && $servicePath != '') echo SITE_PATH.$servicePath;  else echo '-'; ?></div>
	<table class="endpoint_table" cellpadding="0" cellspacing="0" border="0"  width="30%">
		<tr>
			<td width="10%" align="left"  valign="top"><label>Authorization</label></td>
			<td width="20%" align="left"  valign="top"><label>Method</label></td>
			
		</tr>
		<tr>
			<td width="20%" align="left" valign="top"><?php if(isset($authorization) && $authorization == '0') echo "No";  else echo 'Yes'; ?></td>
			<td align="left" valign="top"><?php if(isset($method) && $method != '' ) echo $method; else echo '-'; ?></td>
		</tr>
	</table>
	<table align="center" cellpadding="0" cellspacing="0" border="0" class="filter_form headertable" width="100%">
		<tr><td height="20"></td></tr>
		<tr>
			<td>
				<table cellpadding="0" cellspacing="0" border="0"  width="100%">
					<tr><td height="20"></td></tr>
					<tr>
						<td width="10%" align="left"  valign="top"><label>Input Param</label></td>
						<td width="3%" align="center" valign="top">:</td>
						<td align="left" valign="top">
							<div style="width:600px;word-wrap:break-word;"><?php if(isset($inputParam) && $inputParam != '' )  {
								echo '<ul><li>'.str_replace(array("\r","\n\n","\n"),array('',"\n","</li>\n<li>"),trim($inputParam,"\n\r")).'</li></ul>';
							} 
							else echo '-'; ?>
							</div>
							</td>
					</tr>									
					<tr><td height="20"></td></tr>
					<tr>
						<td width="10%" align="left"  valign="top"><label>Output Param</label></td>
						<td width="3%" align="center" valign="top">:</td>
						<td width="70%" align="left" valign="top">
							<div class="param" >
								<pre ><?php if(isset($outputParam) && $outputParam != '' ) echo $outputParam; else echo '-'; ?></pre>
							</div>
						</td>
					</tr>									
					<tr><td height="20"></td></tr>
					 <tr>										
						<td colspan="2">&nbsp;</td>
						<td align="left">														
							<a href="EndpointList.php" class="submit_button" name="Back" id="Back" value="Back" title="Back" title="Back">Back </a>
						</td>
					</tr> 
				</table>
			</td>
		</tr>
	</table>
</div>
</body>
</html>