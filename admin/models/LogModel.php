<?php
class LogModel extends Model
{
   function getTotalRecordCount()
	{
		$result = $this->sqlCalcFoundRows();
        return $result;
	}
	function logtrackDetails($where)
	{
		$limit_clause = '';
		$sorting_clause = ' l.id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		
		if(isset($_SESSION['sess_logtrack_from_date']) && $_SESSION['sess_logtrack_from_date'] != ''	&&	isset($_SESSION['sess_logtrack_to_date']) && $_SESSION['sess_logtrack_to_date'] != ''){
			$where .= " AND ((date(l.start_time) >=  '".date('Y-m-d',strtotime($_SESSION['sess_logtrack_from_date']))."' and date(l.end_time) <= '".date('Y-m-d',strtotime($_SESSION['sess_logtrack_to_date']))."') ) ";
		}
		else if(isset($_SESSION['sess_logtrack_from_date']) && $_SESSION['sess_logtrack_from_date'] != '')
			$where .= " AND date(l.start_time) >=  '".date('Y-m-d',strtotime($_SESSION['sess_logtrack_from_date']))."'";
		else if(isset($_SESSION['sess_logtrack_to_date']) && $_SESSION['sess_logtrack_to_date'] != '')
			$where .= " AND date(l.end_time) <=  '".date('Y-m-d',strtotime($_SESSION['sess_logtrack_to_date']))."'";
		if(isset($_SESSION['sess_logtrack_process']) && $_SESSION['sess_logtrack_process'] != ''){
			$where .= " AND l.status =  '".$_SESSION['sess_logtrack_process']."'  ";
		}
		 if(isset($_SESSION['sess_logtrack_searchUserName']) && $_SESSION['sess_logtrack_searchUserName'] != '')
			$where .= " and (u.FirstName LIKE '%".$_SESSION['sess_logtrack_searchUserName']."%' ||	u.LastName LIKE '%".$_SESSION['sess_logtrack_searchUserName']."%' ) ";
			
		if(isset($_SESSION['sess_logtrack_searchIP']) && $_SESSION['sess_logtrack_searchIP'] != '')
			$where .= " and l.ip_address LIKE '%".$_SESSION['sess_logtrack_searchIP']."%' ";
		
		$sql	=	"SELECT SQL_CALC_FOUND_ROWS l.id as logId,l.*,u.*,ac.device_type
					FROM {$this->logTable} as l 
					left JOIN {$this->oauthSessionAccessTokensTable} as atk on(atk.access_token = l.user ) 
					LEFT JOIN {$this->oauthSessionTable} as ses on ( ses.id = atk.session_id ) 
					LEFT JOIN {$this->userTable} as u on (u.id = ses.owner_id) 
					LEFT JOIN {$this->oauthClientsTable} as ac on(ac.id=ses.client_id) 
					WHERE 1 ".$where." ORDER BY ".$sorting_clause.$limit_clause;
		//echo "============>".$sql;
		$result = 	$this->sqlQueryArray($sql);
		if (count($result) == 0) return false;
		return $result;
	}

}
?>