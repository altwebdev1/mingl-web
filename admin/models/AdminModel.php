<?php
class AdminModel extends Model
{
    function checkAdminLogin($where)
	{
	 $sql	=	"SELECT * FROM {$this->adminTable} WHERE ".$where;
		$result = 	$this->sqlQueryArray($sql);

		if (count($result) == 0) return false;
		return $result;
	}
	function getAdminDetails($fields,$where)
	{
		$sql	=	"SELECT $fields FROM  {$this->adminTable} WHERE ".$where;
		$result = 	$this->sqlQueryArray($sql);

		if (count($result) == 0) return false;
		return $result;
	}
	function updateAdminDetails($fields,$where)
	{
		$sql = "UPDATE {$this->adminTable} SET $fields where ".$where;
		$this->updateInto($sql);
	}	
	function getCMS($fields,$where)
	{
		$sql = "select $fields from {$this->staticpagesTable} where ".$where;
		$result = 	$this->sqlQueryArray($sql);
		if (count($result) == 0) return false;
		return $result;
	}
	function updateCMSDetails($update_string,$where)
	{
		$sql = "UPDATE {$this->staticpagesTable} SET $update_string where ".$where;
		$this->updateInto($sql);
	}
	function getDistance($fields,$where)
	{
		$sql = "select $fields from {$this->distanceTable} where ".$where;
		$result = 	$this->sqlQueryArray($sql);
		if (count($result) == 0) return false;
		return $result;
	}
	function updateDistanceDetails($update_string,$where)
	{
		$sql = "UPDATE {$this->distanceTable} SET $update_string where ".$where;
		$this->updateInto($sql);
	}
}
?>