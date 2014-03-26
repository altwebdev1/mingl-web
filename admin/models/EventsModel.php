<?php
class EventsModel extends Model
{
   	function getEventsList($fields, $leftJoin, $condition)
	{
		$limit_clause = '';
		$sorting_clause = ' e.id desc';
		
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		if(isset($_SESSION['intermingl_sess_event_title']) && $_SESSION['intermingl_sess_event_title'] != '')
			$condition .= " and e.Title LIKE '%".$_SESSION['intermingl_sess_event_title']."%' ";
		if(isset($_SESSION['intermingl_sess_event_loc']) && $_SESSION['intermingl_sess_event_loc'] != '')
			$condition .= " and e.Location LIKE '%".$_SESSION['intermingl_sess_event_loc']."%' ";
		if(isset($_SESSION['intermingl_sess_event_start']) && $_SESSION['intermingl_sess_event_start'] != ''	&&	isset($_SESSION['intermingl_sess_event_end']) && $_SESSION['intermingl_sess_event_end'] != ''){
			$condition .= " AND date(e.StartDate) >=  '".date('Y-m-d',strtotime($_SESSION['intermingl_sess_event_start']))."' AND date(e.EndDate) <= '".date('Y-m-d',strtotime($_SESSION['intermingl_sess_event_end']))."' ";
			//$condition .= " AND ( date(StartDate) >=  '".$_SESSION['intermingl_sess_event_start']."' || date(EndDate) <= '".$_SESSION['intermingl_sess_event_end']."') ";
		}
		else if(isset($_SESSION['intermingl_sess_event_start']) && $_SESSION['intermingl_sess_event_start'] != ''	){
			$condition .= " AND date(e.StartDate) >=  '".date('Y-m-d',strtotime($_SESSION['intermingl_sess_event_start']))."' ";
		}
		else if(isset($_SESSION['intermingl_sess_event_end']) && $_SESSION['intermingl_sess_event_end'] != ''	){
			$condition .= " AND date(e.EndDate) <=  '".date('Y-m-d',strtotime($_SESSION['intermingl_sess_event_end']))."' ";
		}
		if(isset($_SESSION['intermingl_sess_event_status']) && $_SESSION['intermingl_sess_event_status'] != '')
			$condition .= " and e.Status  = '".$_SESSION['intermingl_sess_event_status']."' ";
		
		$sql	=	"SELECT SQL_CALC_FOUND_ROWS ".$fields." FROM {$this->eventsTable} as e ".$leftJoin." WHERE 1 ".$condition." GROUP BY e.id ORDER BY  ".$sorting_clause." ".$limit_clause;
		//echo "<br/>======".$sql;
		$result = 	$this->sqlQueryArray($sql);
		if (count($result) == 0) return false;
		return $result;
	}
	function getTotalRecordCount()
	{
		$result = $this->sqlCalcFoundRows();
        return $result;
	}
	function insertEventDetails($register_values){
		$sql	 =	"insert into  {$this->eventsTable}  set ";
		if(isset($register_values['eventname'])	&&	trim($register_values['eventname']!=""))
			$sql	.=	"Title 			= 	'".$register_values['eventname']."',";
		if(isset($register_values['description'])	&&	trim($register_values['description']!=""))			
			$sql	.=  "Description			=	'".$register_values['description']."',";
		if(isset($register_values['location'])	&&	trim($register_values['location']!=""))			
			$sql	.=  "Location			=	'".$register_values['location']."',";
		if(isset($register_values['twitter_id'])	&&	trim($register_values['twitter_id']!=""))			
			$sql	.=	"TwitterHashtag			=	'".$register_values['twitter_id']."',";
		if(isset($register_values['startdate'])	&&	trim($register_values['startdate']!=""))			
			$sql 	.=	"StartDate 				= 	'".$register_values['startdate']."',";
		if(isset($register_values['enddate'])	&&	trim($register_values['enddate']!=""))			
			$sql 	.=	"EndDate 				= 	'".$register_values['enddate']."',";
			$sql 	.=	"Passphrase 			= 	'".$this->getEventPassphrase(PASSPHRASE_LENGTH)."',";
		/*if(isset($register_values['ipaddress'])	&&	trim($register_values['ipaddress']!=""))			
			$sql 	.=	"IpAddress 		= 	'".$register_values['ipaddress']."',";
		if(isset($register_values['socialtype'])	&&	trim($register_values['socialtype'])!="")
			$sql	.=	"SocialNetworkType	=	'".$register_values['socialtype']."',";
		if(isset($register_values['socialnetwork'])	&&	trim($register_values['socialnetwork'])!="")
			$sql 	.=	"SocialNetwork		=	'".$register_values['socialnetwork']."',";
		if(isset($register_values['interest'])	&&	trim($register_values['interest']!=""))			
			$sql	.=  "Interest			=	'".$register_values['interest']."',";
		if(isset($register_values['summary'])	&&	trim($register_values['summary']!=""))			
			$sql	.=  "Summary			=	'".$register_values['summary']."',";
			if(isset($register_values['company'])	&&	trim($register_values['company']!=""))			
			$sql	.=  "Company			=	'".$register_values['company']."',";
		if(isset($register_values['title'])	&&	trim($register_values['title']!=""))			
			$sql	.=  "Title			=	'".$register_values['title']."',";
		
		if(isset($register_values['phone'])	&&	trim($register_values['phone']!=""))			
			$sql	.=  "Phone			=	'".$register_values['phone']."',";			*/
			$sql 	.=	" Status 			= 	1,
						  DateCreated 		= 	'".date('Y-m-d H:i:s')."',
						  DateModified		= 	'".date('Y-m-d H:i:s')."'";
		$this->result = $this->insertInto($sql);
		$insertId = $this->sqlInsertId();
       return $insertId;
	}
	function updateEventDetails($update_string,$condition){
		$sql	 =	"update {$this->eventsTable}  set ".$update_string." where ".$condition;
		$this->updateInto($sql);
	}
	function deleteEventEntries($userId){
		$like_postIds = $like_hashIds = $follow_hashIds = $hashIds = $postIds = '';
		$update_string 	= " Status = 3 ";
		$condition 		= " id IN(".$userId.") ";
		$this->updateUserDetails($update_string,$condition);
	}
	function getEventPassphrase($length) {
		$passphrase = getPassphrase($length);
		$sql	=	"SELECT count(*) as count_flag FROM {$this->eventsTable} WHERE Passphrase	=	'".$passphrase."' ";
//		echo '<br>---------'.$sql;
		$existingPassphrase = 	$this->sqlQueryArray($sql);
		if ($existingPassphrase[0]->count_flag > 0)
			$this->getEventPassphrase($length);
		else return $passphrase;		
	}
	function selectEventDetails($fields,$condition)
	{
		$sql	 =	"select ".$fields." from {$this->eventsTable} where ".$condition;
		//echo "<br/>======".$sql;
		$result = 	$this->sqlQueryArray($sql);
			if($result) return $result;
			else false;
	}
	function eventJoinedUsers($fields,$leftJoin,$condition)
	{
		$limit_clause = '';
		$sorting_clause = '';
		
		if(!empty($_SESSION['ordertype']));
//			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		
		$sql	 =	"select SQL_CALC_FOUND_ROWS ".$fields." from {$this->joinEventsTable} as je ".$leftJoin." where 1 ".$condition." ".$sorting_clause." ".$limit_clause;
		//echo "<br/>======".$sql;
		$result = 	$this->sqlQueryArray($sql);
			if($result) return $result;
			else false;
	}
	function eventLikedUsers($fields,$leftJoin,$condition)
	{
		$limit_clause = '';
		$sorting_clause = '';
		
		if(!empty($_SESSION['ordertype']));
//			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];

		$sql	 =	"select SQL_CALC_FOUND_ROWS ".$fields." from {$this->connectionsTable} as con ".$leftJoin." where 1 ".$condition."  ".$sorting_clause." ".$limit_clause;
		//echo "<br/>======".$sql;
		$result = 	$this->sqlQueryArray($sql);
			if($result) return $result;
			else false;
	}
}
?>