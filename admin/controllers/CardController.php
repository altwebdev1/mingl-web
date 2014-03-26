<?php
class CardController extends Controller
{
   function getTotalRecordCount()
	{
		if (!isset($this->CardModelObj))
			$this->loadModel('CardModel', 'CardModelObj');
		if ($this->CardModelObj)
			return $this->CardModelObj->getTotalRecordCount();
	}
	function getCardsList($fields,$condition)
	{
		if (!isset($this->CardModelObj))
			$this->loadModel('CardModel', 'CardModelObj');
		if ($this->CardModelObj)
			return $this->CardModelObj->getCardsList($fields,$condition);
	}
	function updateCardDetails($update_string,$condition)
	{
		if (!isset($this->CardModelObj))
			$this->loadModel('CardModel', 'CardModelObj');
		if ($this->CardModelObj)
			return $this->CardModelObj->updateCardDetails($update_string,$condition);
	}
	function selectCardDetails($field,$condition)
	{
		if (!isset($this->CardModelObj))
			$this->loadModel('CardModel', 'CardModelObj');
		if ($this->CardModelObj)
			return $this->CardModelObj->selectCardDetails($field,$condition);
	}
	function insertCardDetails($register_values)
	{
		if (!isset($this->CardModelObj))
			$this->loadModel('CardModel', 'CardModelObj');
		if ($this->CardModelObj)
			return $this->CardModelObj->insertCardDetails($register_values);
	}
	/*function selectWordDetails()
	{
		if (!isset($this->CardModelObj))
			$this->loadModel('CardModel', 'CardModelObj');
		if ($this->CardModelObj)
			return $this->CardModelObj->selectWordDetails();
	}
	function selectContactDetails($fields,$condition)
	{
		if (!isset($this->CardModelObj))
			$this->loadModel('CardModel', 'CardModelObj');
		if ($this->CardModelObj)
			return $this->CardModelObj->selectContactDetails($fields,$condition);
	}
	function getUserHashDetails($fields,$condition)
	{
		if (!isset($this->CardModelObj))
			$this->loadModel('CardModel', 'CardModelObj');
		if ($this->CardModelObj)
			return $this->CardModelObj->getUserHashDetails($fields,$condition);
	}
	function deleteUserReleatedEntries($userId)
	{
		if (!isset($this->CardModelObj))
			$this->loadModel('CardModel', 'CardModelObj');
		if ($this->CardModelObj)
			return $this->CardModelObj->deleteUserReleatedEntries($userId);
	}
	function getActivityDetails($fields, $condition)
	{
		if (!isset($this->CardModelObj))
			$this->loadModel('CardModel', 'CardModelObj');
		if ($this->CardModelObj)
			return $this->CardModelObj->getActivityDetails($fields, $condition);
	}
	function getActivity($userId)
	{
		if (!isset($this->CardModelObj))
			$this->loadModel('CardModel', 'CardModelObj');
		if ($this->CardModelObj)
			return $this->CardModelObj->getActivity($userId);
	}
	*/
}
?>