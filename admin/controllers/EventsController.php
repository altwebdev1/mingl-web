<?php
class EventsController extends Controller
{
   function getTotalRecordCount()
	{
		if (!isset($this->EventsModelObj))
			$this->loadModel('EventsModel', 'EventsModelObj');
		if ($this->EventsModelObj)
			return $this->EventsModelObj->getTotalRecordCount();
	}
	function getEventsList($fields, $leftJoin, $condition)
	{
		if (!isset($this->EventsModelObj))
			$this->loadModel('EventsModel', 'EventsModelObj');
		if ($this->EventsModelObj)
			return $this->EventsModelObj->getEventsList($fields, $leftJoin, $condition);
	}
	function updateEventDetails($update_string,$condition)
	{
		if (!isset($this->EventsModelObj))
			$this->loadModel('EventsModel', 'EventsModelObj');
		if ($this->EventsModelObj)
			return $this->EventsModelObj->updateEventDetails($update_string,$condition);
	}
	function getEventPassphrase($length)
	{
		if (!isset($this->EventsModelObj))
			$this->loadModel('EventsModel', 'EventsModelObj');
		if ($this->EventsModelObj)
			return $this->EventsModelObj->getEventPassphrase($length);
	}
	function insertEventDetails($register_values)
	{
		if (!isset($this->EventsModelObj))
			$this->loadModel('EventsModel', 'EventsModelObj');
		if ($this->EventsModelObj)
			return $this->EventsModelObj->insertEventDetails($register_values);
	}
	function selectEventDetails($fields,$condition)
	{
		if (!isset($this->EventsModelObj))
			$this->loadModel('EventsModel', 'EventsModelObj');
		if ($this->EventsModelObj)
			return $this->EventsModelObj->selectEventDetails($fields,$condition);
	}
	function eventJoinedUsers($fields,$leftJoin,$condition)
	{
		if (!isset($this->EventsModelObj))
			$this->loadModel('EventsModel', 'EventsModelObj');
		if ($this->EventsModelObj)
			return $this->EventsModelObj->eventJoinedUsers($fields,$leftJoin,$condition);
	}
	function eventLikedUsers($fields,$leftJoin,$condition)
	{
		if (!isset($this->EventsModelObj))
			$this->loadModel('EventsModel', 'EventsModelObj');
		if ($this->EventsModelObj)
			return $this->EventsModelObj->eventLikedUsers($fields,$leftJoin,$condition);
	}
	/**************/
/*	function selectUserDetails($field,$condition)
	{
		if (!isset($this->EventsModelObj))
			$this->loadModel('EventsModel', 'EventsModelObj');
		if ($this->EventsModelObj)
			return $this->EventsModelObj->selectUserDetails($field,$condition);
	}
	function selectWordDetails()
	{
		if (!isset($this->EventsModelObj))
			$this->loadModel('EventsModel', 'EventsModelObj');
		if ($this->EventsModelObj)
			return $this->EventsModelObj->selectWordDetails();
	}
	function selectContactDetails($fields,$condition)
	{
		if (!isset($this->EventsModelObj))
			$this->loadModel('EventsModel', 'EventsModelObj');
		if ($this->EventsModelObj)
			return $this->EventsModelObj->selectContactDetails($fields,$condition);
	}
	function getUserHashDetails($fields,$condition)
	{
		if (!isset($this->EventsModelObj))
			$this->loadModel('EventsModel', 'EventsModelObj');
		if ($this->EventsModelObj)
			return $this->EventsModelObj->getUserHashDetails($fields,$condition);
	}
	function deleteUserReleatedEntries($userId)
	{
		if (!isset($this->EventsModelObj))
			$this->loadModel('EventsModel', 'EventsModelObj');
		if ($this->EventsModelObj)
			return $this->EventsModelObj->deleteUserReleatedEntries($userId);
	}
	function getActivityDetails($fields, $condition)
	{
		if (!isset($this->EventsModelObj))
			$this->loadModel('EventsModel', 'EventsModelObj');
		if ($this->EventsModelObj)
			return $this->EventsModelObj->getActivityDetails($fields, $condition);
	}
	function getActivity($userId)
	{
		if (!isset($this->EventsModelObj))
			$this->loadModel('EventsModel', 'EventsModelObj');
		if ($this->EventsModelObj)
			return $this->EventsModelObj->getActivity($userId);
	}
*/
}
?>