<?php
class UserController extends Controller
{
   function getTotalRecordCount()
	{
		if (!isset($this->UserModelObj))
			$this->loadModel('UserModel', 'UserModelObj');
		if ($this->UserModelObj)
			return $this->UserModelObj->getTotalRecordCount();
	}
	function getUserList($fields,$condition)
	{
		if (!isset($this->UserModelObj))
			$this->loadModel('UserModel', 'UserModelObj');
		if ($this->UserModelObj)
			return $this->UserModelObj->getUserList($fields,$condition);
	}
	function updateUserDetails($update_string,$condition)
	{
		if (!isset($this->UserModelObj))
			$this->loadModel('UserModel', 'UserModelObj');
		if ($this->UserModelObj)
			return $this->UserModelObj->updateUserDetails($update_string,$condition);
	}
	function selectUserDetails($field,$condition)
	{
		if (!isset($this->UserModelObj))
			$this->loadModel('UserModel', 'UserModelObj');
		if ($this->UserModelObj)
			return $this->UserModelObj->selectUserDetails($field,$condition);
	}
	function selectWordDetails()
	{
		if (!isset($this->UserModelObj))
			$this->loadModel('UserModel', 'UserModelObj');
		if ($this->UserModelObj)
			return $this->UserModelObj->selectWordDetails();
	}
	function insertUserDetails($register_values)
	{
		if (!isset($this->UserModelObj))
			$this->loadModel('UserModel', 'UserModelObj');
		if ($this->UserModelObj)
			return $this->UserModelObj->insertUserDetails($register_values);
	}
	function selectContactDetails($fields,$condition)
	{
		if (!isset($this->UserModelObj))
			$this->loadModel('UserModel', 'UserModelObj');
		if ($this->UserModelObj)
			return $this->UserModelObj->selectContactDetails($fields,$condition);
	}
	function getUserHashDetails($fields,$condition)
	{
		if (!isset($this->UserModelObj))
			$this->loadModel('UserModel', 'UserModelObj');
		if ($this->UserModelObj)
			return $this->UserModelObj->getUserHashDetails($fields,$condition);
	}
	function deleteUserReleatedEntries($userId)
	{
		if (!isset($this->UserModelObj))
			$this->loadModel('UserModel', 'UserModelObj');
		if ($this->UserModelObj)
			return $this->UserModelObj->deleteUserReleatedEntries($userId);
	}
	function getActivityDetails($fields, $condition)
	{
		if (!isset($this->UserModelObj))
			$this->loadModel('UserModel', 'UserModelObj');
		if ($this->UserModelObj)
			return $this->UserModelObj->getActivityDetails($fields, $condition);
	}
	function getActivity($userId)
	{
		if (!isset($this->UserModelObj))
			$this->loadModel('UserModel', 'UserModelObj');
		if ($this->UserModelObj)
			return $this->UserModelObj->getActivity($userId);
	}
	function changeUsersStatus($userIds,$updateStatus)
	{
		if (!isset($this->UserModelObj))
			$this->loadModel('UserModel', 'UserModelObj');
		if ($this->UserModelObj)
			return $this->UserModelObj->changeUsersStatus($userIds,$updateStatus);
	}
}
?>