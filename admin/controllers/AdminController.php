<?php
class AdminController extends Controller
{
    function checkAdminLogin($where)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->checkAdminLogin($where);
	}
	function getAdminDetails($fields,$where)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->getAdminDetails($fields,$where);
	}
	function updateAdminDetails($fields,$where)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->updateAdminDetails($fields,$where);
	}	
	function getCMS($fields,$where)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->getCMS($fields,$where);
	}	
	function updateCMSDetails($update_string,$where)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->updateCMSDetails($update_string,$where);
	}
	function getDistance($fields,$where)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->getDistance($fields,$where);
	}
	function updateDistanceDetails($update_string,$where)
	{
		if (!isset($this->AdminModelObj))
			$this->loadModel('AdminModel', 'AdminModelObj');
		if ($this->AdminModelObj)
			return $this->AdminModelObj->updateDistanceDetails($update_string,$where);
	}
	
}
?>