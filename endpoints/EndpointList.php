<?php
require_once("../admin/config/config.php");
require_once("../admin/config/db_config.php");
require_once("../admin/models/Database.php");

global $globalDbManager;
$globalDbManager = new Database();
$globalDbManager->dbConnect = $globalDbManager->connect($dbConfig['hostName'], $dbConfig['userName'], $dbConfig['passWord'], $dbConfig['dataBase']);

$sql			= "SELECT * FROM oauth_client_endpoints WHERE 1 ORDER BY Ordering asc";
$endpointList	= $globalDbManager->sqlQueryArray($sql);
$msg			= '';
if(isset($endpointList) && is_array($endpointList) && count($endpointList)>0){
	/* endpoint listing works based on the construction of below array */
	foreach($endpointList as $key=>$value){
		$processList[$value->Module][$value->Process]	= array("order"=>$value->Ordering, "viewId"=>$value->id,"endpoint"=>$value->ServicePath,"method"=>$value->Method);	
	}
}
else
	$msg	=	 "No record found";
//echo '<pre>';print_r($processList);echo '</pre>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title><?php echo SITE_TITLE; ?></title>
	<link rel="STYLESHEET" type="text/css" href="styles.css">
	<link rel="icon" href="../webresources/images/favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="../webresources/images/favicon.ico" type="image/x-icon" />
</head>
<body>
<div class="content" style="margin-top:2%">
	<div class="elem_title"><span style="text-transform:capitalize"><?php echo SITE_TITLE; ?></span> - API Endpoints</div>
	<table style="margin-top:15px" class="endpoint_table" align="center" cellpadding="5" cellspacing="0" width="100%">
		<tr>
			<th align="left" width="20%">Module</th>
			<th align="left" width="30%">Purpose</th>
			<th align="left" width="35%">Endpoint</th>
			<th align="left" width="15%">Action</th>
		</tr>
		<?php
		if($msg!=''){ ?>
		<tr><td align="center" colspan="4"><?php echo $msg; ?></td></tr>
		<?php }
		else{
			foreach($processList as $module=>$processArr){
				?>
				<tr>
					<td valign="top" style="color:#222222;font-weight:bold" rowspan="<?php echo count($processArr); ?>"><?php echo $module; ?></td><?php
					foreach($processArr as $purpose=>$process){
						?>
							<td ><a class="view_link" href="EndpointDetail.php?viewId=<?php echo $process['viewId']; ?>" ><?php echo $purpose; ?></a></td>
							<td ><a class="view_link" href="EndpointDetail.php?viewId=<?php echo $process['viewId']; ?>" ><?php echo $process['endpoint'] ?></a></td>
							<td ><?php
							if(strtolower($purpose) == "login")
								echo "Authorization via oauth";
							else{
								switch($process['method']){
									case "GET"		:	echo "Select"; break;
									case "POST"		:	echo "Insert"; break;
									case "PUT"		:	echo "Update"; break;
									case "DELETE"	:	echo "Delete"; break;
								}
								
							}
							?></td>
						</tr><?php
					}
			}
		}
		?>
	</table>
</div>
</body>
</html>