<?php
class ManagementController extends Controller
{
   function getTotalRecordCount()
	{
		if (!isset($this->ManagementModelObj))
			$this->loadModel('ManagementModel', 'ManagementModelObj');
		if ($this->ManagementModelObj)
			return $this->ManagementModelObj->getTotalRecordCount();
	}

	function selectTagDetails($fields,$condition)
	{
		if (!isset($this->ManagementModelObj))
			$this->loadModel('ManagementModel', 'ManagementModelObj');
		if ($this->ManagementModelObj)
			return $this->ManagementModelObj->selectTagDetails($fields,$condition);
	}

	function insertTagDetails($hashtag_values)
	{
		if (!isset($this->ManagementModelObj))
			$this->loadModel('ManagementModel', 'ManagementModelObj');
		if ($this->ManagementModelObj)
			return $this->ManagementModelObj->insertTagDetails($hashtag_values);
	}
	function updateTagDetails($fields,$condition)
	{
		if (!isset($this->ManagementModelObj))
			$this->loadModel('ManagementModel', 'ManagementModelObj');
		if ($this->ManagementModelObj)
			return $this->ManagementModelObj->updateTagDetails($fields,$condition);
	}
	function getTagList($fields,$condition)
	{
		if (!isset($this->ManagementModelObj))
			$this->loadModel('ManagementModel', 'ManagementModelObj');
		if ($this->ManagementModelObj)
			return $this->ManagementModelObj->getTagList($fields,$condition);
	}

	
	function getGoalsList($fields,$condition)
	{
		if (!isset($this->ManagementModelObj))
			$this->loadModel('ManagementModel', 'ManagementModelObj');
		if ($this->ManagementModelObj)
			return $this->ManagementModelObj->getGoalsList($fields,$condition);
	}
	function insertGoalDetails($postValues)
	{
		if (!isset($this->ManagementModelObj))
			$this->loadModel('ManagementModel', 'ManagementModelObj');
		if ($this->ManagementModelObj)
			return $this->ManagementModelObj->insertGoalDetails($postValues);
	}
	function updateGoalDetails($update_string,$condition)
	{
		if (!isset($this->ManagementModelObj))
			$this->loadModel('ManagementModel', 'ManagementModelObj');
		if ($this->ManagementModelObj)
			return $this->ManagementModelObj->updateGoalDetails($update_string,$condition);
	}
	function selectGoalDetails($fields,$condition)
	{
		if (!isset($this->ManagementModelObj))
			$this->loadModel('ManagementModel', 'ManagementModelObj');
		if ($this->ManagementModelObj)
			return $this->ManagementModelObj->selectGoalDetails($fields,$condition);
	}
		function getInterestsList($fields,$condition)
	{
		if (!isset($this->ManagementModelObj))
			$this->loadModel('ManagementModel', 'ManagementModelObj');
		if ($this->ManagementModelObj)
			return $this->ManagementModelObj->getInterestsList($fields,$condition);
	}
	function insertInterestDetails($postValues)
	{
		if (!isset($this->ManagementModelObj))
			$this->loadModel('ManagementModel', 'ManagementModelObj');
		if ($this->ManagementModelObj)
			return $this->ManagementModelObj->insertInterestDetails($postValues);
	}
	function updateInterestDetails($update_string,$condition)
	{
		if (!isset($this->ManagementModelObj))
			$this->loadModel('ManagementModel', 'ManagementModelObj');
		if ($this->ManagementModelObj)
			return $this->ManagementModelObj->updateInterestDetails($update_string,$condition);
	}
	function selectInterestDetails($fields,$condition)
	{
		if (!isset($this->ManagementModelObj))
			$this->loadModel('ManagementModel', 'ManagementModelObj');
		if ($this->ManagementModelObj)
			return $this->ManagementModelObj->selectInterestDetails($fields,$condition);
	}
	function selectUserInterests($field,$userid)
	{
		if (!isset($this->ManagementModelObj))
			$this->loadModel('ManagementModel', 'ManagementModelObj');
		if ($this->ManagementModelObj)
			return $this->ManagementModelObj->selectUserInterests($field,$userid);
	}
	function selectUserTags($field,$userid){
		if (!isset($this->ManagementModelObj))
			$this->loadModel('ManagementModel', 'ManagementModelObj');
		if ($this->ManagementModelObj)
			return $this->ManagementModelObj->selectUserTags($field,$userid);
	}
	function selectConnectionDetails($fields,$condition)
	{
		if (!isset($this->ManagementModelObj))
			$this->loadModel('ManagementModel', 'ManagementModelObj');
		if ($this->ManagementModelObj)
			return $this->ManagementModelObj->selectConnectionDetails($fields,$condition);
	}
	function selectCommentDetails($fields,$condition)
	{
		if (!isset($this->ManagementModelObj))
			$this->loadModel('ManagementModel', 'ManagementModelObj');
		if ($this->ManagementModelObj)
			return $this->ManagementModelObj->selectCommentDetails($fields,$condition);
	}
	function changeGoalStatus($updateIds,$updateStatus)
	{
		if (!isset($this->ManagementModelObj))
			$this->loadModel('ManagementModel', 'ManagementModelObj');
		if ($this->ManagementModelObj)
			return $this->ManagementModelObj->changeGoalStatus($updateIds,$updateStatus);
	}
	function changeTagStatus($updateIds,$updateStatus)
	{
		if (!isset($this->ManagementModelObj))
			$this->loadModel('ManagementModel', 'ManagementModelObj');
		if ($this->ManagementModelObj)
			return $this->ManagementModelObj->changeTagStatus($updateIds,$updateStatus);
	}
	function changeInterestStatus($updateIds,$updateStatus)
	{
		if (!isset($this->ManagementModelObj))
			$this->loadModel('ManagementModel', 'ManagementModelObj');
		if ($this->ManagementModelObj)
			return $this->ManagementModelObj->changeInterestStatus($updateIds,$updateStatus);
	}
	function countUserTags($userid)
	{
		if (!isset($this->ManagementModelObj))
			$this->loadModel('ManagementModel', 'ManagementModelObj');
		if ($this->ManagementModelObj)
			return $this->ManagementModelObj->countUserTags($userid);
	}
}
?>