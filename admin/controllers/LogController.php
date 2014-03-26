<?php
class LogController extends Controller
{
   function getTotalRecordCount()
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->getTotalRecordCount();
	}
	function logtrackDetails($where)
	{
		if (!isset($this->LogModelObj))
			$this->loadModel('LogModel', 'LogModelObj');
		if ($this->LogModelObj)
			return $this->LogModelObj->logtrackDetails($where);
	}
}
?>