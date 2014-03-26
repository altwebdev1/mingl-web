<?php
class CardModel extends Model
{
   function getCardsList($fields,$condition)
	{
		$limit_clause='';
		$sorting_clause = ' c.id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		if(isset($_SESSION['intermingl_sess_cart_userId']) && $_SESSION['intermingl_sess_cart_userId'] != '')
			$condition .= " and c.fkUserId  = ".$_SESSION['intermingl_sess_cart_userId'];
		if(isset($_SESSION['intermingl_sess_card_userName']) && $_SESSION['intermingl_sess_card_userName'] != '')
			$condition .= " and (u.FirstName LIKE '%".$_SESSION['intermingl_sess_card_userName']."%' || u.LastName LIKE '%".$_SESSION['intermingl_sess_card_userName']."%' ) ";
		if(isset($_SESSION['intermingl_sess_card_type']) && $_SESSION['intermingl_sess_card_type'] != '')
			$condition .= " and c.Card ='".$_SESSION['intermingl_sess_card_type']."' ";
		if(isset($_SESSION['intermingl_sess_cardUser_company']) && $_SESSION['intermingl_sess_cardUser_company'] != '')
			$condition .= " and u.Company LIKE '".$_SESSION['intermingl_sess_cardUser_company']."%' ";
		if(isset($_SESSION['intermingl_sess_cardUser_country']) && $_SESSION['intermingl_sess_cardUser_country'] != '')
			$condition .= " and u.Country LIKE '%".$_SESSION['intermingl_sess_cardUser_country']."%' ";
		/*if(isset($_SESSION['intermingl_sess_Card_status']) && $_SESSION['intermingl_sess_Card_status'] != '')
			$condition .= " and u.Status = '".$_SESSION['intermingl_sess_Card_status']."' ";
		if(isset($_SESSION['intermingl_sess_location']) && $_SESSION['intermingl_sess_location'] != '')
			$condition .= " and u.Location LIKE '%".$_SESSION['intermingl_sess_location']."%' ";
		if(isset($_SESSION['intermingl_sess_Card_registerdate']) && $_SESSION['intermingl_sess_Card_registerdate'] != '')
			$condition .= " and date(u.DateCreated) = '".$_SESSION['intermingl_sess_Card_registerdate']."'";	
*/
		$sql = "select SQL_CALC_FOUND_ROWS ".$fields." from {$this->cardsTable} as c 
				LEFT JOIN users as u ON (c.fkUsersId=u.id)
				WHERE 1".$condition." 
				group by c.id 
				ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		//echo "<br/>======".$sql;
		if(count($result) == 0) return false;
		else return $result;
	}
   function getTotalRecordCount()
	{
		$result = $this->sqlCalcFoundRows();
        return $result;
	}
	function updateCardDetails($update_string,$condition){
		$sql	 =	"update {$this->cardsTable}  set ".$update_string." where ".$condition;
		$this->updateInto($sql);
	}
	function selectCardDetails($field,$condition){
		$sql	 =	"select ".$field." from {$this->cardsTable} where ".$condition;
		//echo "<br/>======".$sql;
		$result = 	$this->sqlQueryArray($sql);
			if($result) return $result;
			else false;
	}
	function insertCardDetails($insertString){
		$sql	 =	"insert into  {$this->cardsTable}  set ".$insertString;
		//if(isset($register_values['Cardname'])	&&	trim($register_values['Cardname']!=""))
			//$sql	.=	"CardName 			= 	'".$register_values['Cardname']."',";
		/*if(isset($register_values['firstname'])	&&	trim($register_values['firstname']!=""))			
			$sql	.=  "FirstName			=	'".$register_values['firstname']."',";
		if(isset($register_values['lastname'])	&&	trim($register_values['lastname']!=""))			
			$sql	.=	"LastName			=	'".$register_values['lastname']."',";
		if(isset($register_values['email'])	&&	trim($register_values['email']!=""))			
			$sql 	.=	"Email 			= 	'".$register_values['email']."',";
		if(isset($register_values['city'])	&&	trim($register_values['city']!=""))			
			$sql	.=  "City			=	'".$register_values['city']."',";
		if(isset($register_values['state'])	&&	trim($register_values['state']!=""))			
			$sql	.=  "State		=	'".$register_values['state']."',";			
						  EmailNotification	= 	1,
		*/
			$sql 	.=	" Card 				= 	1,
						  Status 			= 	1,
						  DateCreated 		= 	'".date('Y-m-d H:i:s')."',
						  DateModified		= 	'".date('Y-m-d H:i:s')."'";
		$this->result = $this->insertInto($sql);
		$insertId = $this->sqlInsertId();
       return $insertId;
	}
	/*function insertCardDetails($register_values){
		$sql	 =	"insert into  {$this->cardsTable}  set ";
		//if(isset($register_values['Cardname'])	&&	trim($register_values['Cardname']!=""))
			//$sql	.=	"CardName 			= 	'".$register_values['Cardname']."',";
		if(isset($register_values['firstname'])	&&	trim($register_values['firstname']!=""))			
			$sql	.=  "FirstName			=	'".$register_values['firstname']."',";
		if(isset($register_values['lastname'])	&&	trim($register_values['lastname']!=""))			
			$sql	.=	"LastName			=	'".$register_values['lastname']."',";
		if(isset($register_values['email'])	&&	trim($register_values['email']!=""))			
			$sql 	.=	"Email 			= 	'".$register_values['email']."',";
		if(isset($register_values['city'])	&&	trim($register_values['city']!=""))			
			$sql	.=  "City			=	'".$register_values['city']."',";
		if(isset($register_values['state'])	&&	trim($register_values['state']!=""))			
			$sql	.=  "State		=	'".$register_values['state']."',";			
		
			$sql 	.=	" Status 			= 	1,
						  EmailNotification	= 	1,
						  DateCreated 		= 	'".date('Y-m-d H:i:s')."',
						  DateModified		= 	'".date('Y-m-d H:i:s')."'";
		$this->result = $this->insertInto($sql);
		$insertId = $this->sqlInsertId();
       return $insertId;
	}
	function selectWordDetails(){
		$sql	 =	"select * from {$this->wordsTable} where 1 order by rand() limit 1 ";
		//echo "<br/>======".$sql;
		$result = 	$this->sqlQueryArray($sql);
			if($result) return $result;
			else false;
	}
	function deleteCardReleatedEntries($CardId){
		$like_postIds = $like_hashIds = $follow_hashIds = $hashIds = $postIds = '';
		$update_string 	= " Status = 3 ";
		$condition 		= " id IN(".$CardId.") ";
		$this->updateCardDetails($update_string,$condition);
	}
	function getActivityDetails($fields, $condition)
	{
		$sql	 =	"SELECT ".$fields." FROM {$this->activityTable} AS At
					WHERE 1 ".$condition. "group by ActivityType" ;
		//echo "<br/>======".$sql;
		$result = 	$this->sqlQueryArray($sql);
			if($result) return $result;
			else false;
	}
*/
}
?>