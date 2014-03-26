
<?php
class ManagementModel extends Model
{
	function getTotalRecordCount()
	{
		$result = $this->sqlCalcFoundRows();
        return $result;
	}
   function insertTagDetails($tagValues){
		$sql	 =	"INSERT INTO  {$this->TagTable}  SET 	Tags				= '".trim($tagValues['tagname'])."',
															DateCreated		= '".date('Y-m-d H:i:s')."',
															DateModified 	= '".date('Y-m-d H:i:s')."',
															Status 			= '1'";
	//	echo $sql;
	//	$this->result = $this->set_utfcharset(); // This is used to insert Special Characters into the database.
		$this->result = $this->insertInto($sql);
		$insertId = $this->sqlInsertId();
		return $insertId;
	
	}
	function selectTagDetails($fields,$condition)	{
		$sql	=	"Select ".$fields." from {$this->TagTable} where 1 ".$condition;
		//echo "---->".$sql;
		$result = 	$this->sqlQueryArray($sql);
			if($result) return $result;
			else false;
	}
	function updateTagDetails($update_string,$condition)	{
		$sql	 =	"update {$this->TagTable}  set ".$update_string." where ".$condition;
		//echo "<br/>======".$sql;
		$this->result = $this->set_utfcharset();
		$this->updateInto($sql);
	}
function getTagList($fields,$condition)
	{
		$limit_clause=  $table = '';
		$sorting_clause = ' id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		if(isset($_SESSION['intermingl_sess_tag_name']) && $_SESSION['intermingl_sess_tag_name'] != '')
			$condition .= " and Tags LIKE '%".$_SESSION['intermingl_sess_tag_name']."%' ";
		if(isset($_SESSION['intermingl_sess_tag_status']) && $_SESSION['intermingl_sess_tag_status'] != '')
			$condition .= " and Status = ".$_SESSION['intermingl_sess_tag_status'];
	/*	if(isset($_SESSION['broadtags_sess_hash_tag_userid']) && $_SESSION['broadtags_sess_hash_tag_userid'] != '')
			$condition .= " and ut.UserName LIKE '%".$_SESSION['broadtags_sess_hash_tag_userid']."%'";
		if(isset($_SESSION['broadtags_sess_hashtag_createdby']) && $_SESSION['broadtags_sess_hashtag_createdby'] == '1'){
			$fields     .= ' ,ut.UserName';
			$table		= "	LEFT JOIN  ".$this->userTable." as ut ON ( ut.id = ht.fkUserId )";
			$condition  .= " and (ut.status = 1 or ht.fkUserId = 0) ";
		}*/
		$sql = "SELECT SQL_CALC_FOUND_ROWS ".$fields." 
					FROM {$this->TagTable}	WHERE 1".$condition."  ORDER BY ".$sorting_clause." ".$limit_clause;
	//	echo $sql;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}


	
	function getGoalsList($fields,$condition)
	{
		$limit_clause=  $table = '';
		$sorting_clause = ' id desc';
		
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		if(isset($_SESSION['intermingl_sess_goal']) && $_SESSION['intermingl_sess_goal'] != '')
			$condition .= " and Goal LIKE '%".$_SESSION['intermingl_sess_goal']."%' ";
		if(isset($_SESSION['intermingl_sess_goal_status']) && $_SESSION['intermingl_sess_goal_status'] != '')
			$condition .= " and Status = ".$_SESSION['intermingl_sess_goal_status'];
		$sql = "SELECT SQL_CALC_FOUND_ROWS ".$fields." 
					FROM {$this->goalsTable}
					WHERE 1".$condition."  ORDER BY ".$sorting_clause." ".$limit_clause;
		//echo $sql;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function insertGoalDetails($postValues){
		$sql	 =	"INSERT INTO  {$this->goalsTable}  SET 	Goal				= '".trim($postValues['add_goal'])."',
															DateCreated		= '".date('Y-m-d H:i:s')."',
															DateModified 	= '".date('Y-m-d H:i:s')."',
															Status 			= '1'";
		//echo $sql;
	//	$this->result = $this->set_utfcharset(); // This is used to insert Special Characters into the database.
		$this->result = $this->insertInto($sql);
		$insertId = $this->sqlInsertId();
		return $insertId;
	
	}
	function updateGoalDetails($update_string,$condition)
	{
		$sql	 =	"update {$this->goalsTable}  set ".$update_string." where ".$condition;
		//echo "<br/>======".$sql;
		$this->result = $this->set_utfcharset();
		$this->updateInto($sql);
	}
	function selectGoalDetails($fields,$condition)	{
		$sql	=	"Select ".$fields." from {$this->goalsTable} where 1 ".$condition;
		//echo "---->".$sql;
		$result = 	$this->sqlQueryArray($sql);
			if($result) return $result;
			else false;
	}
	function getInterestsList($fields,$condition)
	{
		$limit_clause=  $table = '';
		$sorting_clause = ' id desc';
		
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		if(isset($_SESSION['intermingl_search_interest']) && $_SESSION['intermingl_search_interest'] != '')
			$condition .= " and Interest LIKE '%".$_SESSION['intermingl_search_interest']."%' ";
		if(isset($_SESSION['intermingl_sess_interest_status']) && $_SESSION['intermingl_sess_interest_status'] != '')
			$condition .= " and Status = ".$_SESSION['intermingl_sess_interest_status'];
		$sql = "SELECT SQL_CALC_FOUND_ROWS ".$fields." 
					FROM {$this->interestsTable}
					WHERE 1".$condition."  ORDER BY ".$sorting_clause." ".$limit_clause;
		//echo $sql;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function insertInterestDetails($postValues){
		$sql	 =	"INSERT INTO  {$this->interestsTable}  SET 	Interest				= '".trim($postValues['add_interest'])."',
															DateCreated		= '".date('Y-m-d H:i:s')."',
															DateModified 	= '".date('Y-m-d H:i:s')."',
															Status 			= '1'";
		//echo $sql;
	//	$this->result = $this->set_utfcharset(); // This is used to insert Special Characters into the database.
		$this->result = $this->insertInto($sql);
		$insertId = $this->sqlInsertId();
		return $insertId;
	
	}
	function updateInterestDetails($update_string,$condition)
	{
		$sql	 =	"update {$this->interestsTable}  set ".$update_string." where ".$condition;
		//echo "<br/>======".$sql;
		$this->result = $this->set_utfcharset();
		$this->updateInto($sql);
	}
	function selectInterestDetails($fields,$condition)	{
		$sql	=	"Select ".$fields." from {$this->interestsTable} where 1 ".$condition;
		//echo "---->".$sql;
		$result = 	$this->sqlQueryArray($sql);
			if($result) return $result;
			else false;
	}
	function selectUserInterests($field,$userid){
		$sql	 =	"SELECT ".$field." FROM {$this->userInterestTable} as ui
					left join {$this->interestsTable} as it on it.id= ui.fkInterestsId
					left join {$this->userTable} as ut on ut.id= ui.fkUsersId
					WHERE ui.fkUsersId = $userid and ui.Status = 1 and it.Status =1  group by ui.fkInterestsId";
		//echo "---->".$sql;
		$result = 	$this->sqlQueryArray($sql);
			if($result) return $result;
			else false;
	}
	function selectUserTags($field,$userid){
		$sql	 =	"SELECT ".$field." FROM {$this->userTagTable} as utt
					left join {$this->TagTable} as tt on tt.id= utt.fkTagsId
					left join {$this->userTable} as ut on ut.id= utt.fkUsersId
					WHERE utt.fkUsersId = $userid and utt.Status = 1 and tt.Status =1  group by utt.fkTagsId";
		$result = 	$this->sqlQueryArray($sql);
			if($result) return $result;
			else false;
	}
	function countUserTags($userid){
		$sql	 =	"SELECT count(ut.Status) as userTagCount  FROM {$this->userTagTable} as ut
					WHERE ut.fkUsersId = $userid and ut.Status = 1 group by ut.fkUsersId";
		$result = 	$this->sqlQueryArray($sql);
//		echo "---->".$sql;
			if($result) return $result;
			else false;
	}
	function selectConnectionDetails($fields,$condition)	{
		$sql	=	"Select ".$fields." from {$this->connectionsTable} where 1  ".$condition;
		//echo "---->".$sql;
		$result = 	$this->sqlQueryArray($sql);
		if($result) return $result;
		else false;
	}
	function selectCommentDetails($fields,$condition)	{
		$sql	=	"Select ".$fields." from {$this->commentsTable} where 1  ".$condition;
		//echo "---->".$sql;
		$result = 	$this->sqlQueryArray($sql);
		if($result) return $result;
		else false;
	}
	function changeGoalStatus($updateIds,$updateStatus){
		$update_string 	= " Status =  ".$updateStatus;
		$condition 		= " id IN(".$updateIds.") ";
		$sql	 =	"update {$this->goalsTable}  set ".$update_string." where ".$condition;
		echo $sql;
		$this->updateInto($sql);
	}
	function changeTagStatus($updateIds,$updateStatus){
		$update_string 	= " Status =  ".$updateStatus;
		$condition 		= " id IN(".$updateIds.") ";
		$sql	 =	"update {$this->TagTable}  set ".$update_string." where ".$condition;
		echo $sql;
		$this->updateInto($sql);
	}
	function changeInterestStatus($updateIds,$updateStatus){
		$update_string 	= " Status =  ".$updateStatus;
		$condition 		= " id IN(".$updateIds.") ";
		$sql	 =	"update {$this->interestsTable}  set ".$update_string." where ".$condition;
		echo $sql;
		$this->updateInto($sql);
	}
}

?>