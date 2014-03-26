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
$processAspects  = $processGeneral = array();
if(isset($endpointList) && is_array($endpointList) && count($endpointList)>0){
	$i = $j = 0;
	foreach($endpointList as $key=>$value){	
		$processList[$value->Module][$value->Process]	= array("viewId"=>$value->id,"endpoint"=>$value->ServicePath,"method"=>$value->Method,"aspects"=>$value->Aspects);	
		if($value->Aspects != ''){
			$processAspects[$value->Module][$j] = trim($value->Aspects);
			$j++;
		}
		if(strtolower($value->Process) == "login")
			$processGeneral[$value->Module][$i] = 'Authorization via oauth';
		else{
			if($value->Method == 'GET' )
				$processGeneral[$value->Module][$i] = 'List / View';
			else if($value->Method == 'POST' )
				$processGeneral[$value->Module][$i] = 'Add';
			else if($value->Method == 'PUT' )
				$processGeneral[$value->Module][$i] = 'Edit';
			else if($value->Method == 'DELETE' )
				$processGeneral[$value->Module][$i] = 'Remove';
		}
		$i++;
	}
	foreach($processGeneral as $key=>$value){		
		$processGeneral[$key] = array_unique($value);
	}
	foreach($processAspects as $key=>$value){		
		$processAspects[$key] = array_unique($value);
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
	<link rel="icon" href="../admin/webresources/images/favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="../admin/webresources/images/favicon.ico" type="image/x-icon" />
</head>
<body>
<div class="content" style="margin-top:2%">
	<div class="elem_title"><span style="text-transform:capitalize"><?php echo SITE_TITLE; ?></span> - API Endpoints</div>
	<div align="center">
	<table style="margin-top:15px" class="endpoint_table" align="center" cellpadding="5" cellspacing="0" width="70%">
		<tr>
			<th align="left" width="20%">Module</th>
			<th align="left" width="20%">General</th>
			<th align="left" width="15%">Aspects</th>
			<th align="left" width="50%">Action</th>		
		</tr>
		<?php
		if($msg!=''){ ?>
		<tr><td align="center" colspan="4" ><?php echo $msg; ?></td></tr>
		<?php }
		else{
			foreach($processList as $module=>$processArr){
				$generalcount = $aspectscount  = 0;
		?>
				<tr>
					<td valign="top" style="color:#222222;font-weight:bold" rowspan="<?php echo count($processArr); ?>"><?php echo $module; ?></td>
					<td valign="top" style="color:#222222;padding:0px;" rowspan="<?php echo count($processArr); ?>" >
						<table  cellpadding="0" cellspacing="0" width="100%" >
					<?php 
						if(isset($processGeneral[$module]))		{		
						foreach($processGeneral[$module] as $general=>$generalvalue){ 
							$generalcount++;
					?>
							<tr>
								<td <?php if((count($processGeneral[$module]) == $generalcount) && (count($processGeneral[$module]) !=  count($processArr) )) { ?> class='last' <?php } ?>><?php echo $generalvalue;  ?></td>
							</tr>
							
					<?php	} } ?>
					</table>
					</td>
					<td valign="top" style="color:#222222;padding:0px;" rowspan="<?php echo count($processArr); ?>">
						<table  cellpadding="0" cellspacing="0" width="100%" >
					<?php 
						if(isset($processAspects[$module]))		{			
							foreach($processAspects[$module] as $aspects=>$aspectsvalue){
								$aspectscount++;
							 ?>
							<tr>
								<td <?php if((count($processAspects[$module]) == $aspectscount) && (count($processAspects[$module]) !=  count($processArr) )) { ?> class='last' <?php } ?>><?php echo $aspectsvalue;  ?></td>
							</tr>							
						<?php } } ?>
						</table>
					</td>
					<?php foreach($processArr as $purpose=>$process){ ?>	
					<td ><a class="view_link" href="EndpointDetail.php?viewId=<?php echo $process['viewId']; ?>" ><?php echo $purpose?></a></td>
				</tr>
					<?php
					}
			}
		}
		?>
	</table>	
	</div>
</div>
</body>
</html>