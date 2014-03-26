<?php
class UserModel extends Model
{
   function getUserList($fields,$condition)
	{
		$limit_clause='';
		$sorting_clause = ' u.id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		if(isset($_SESSION['intermingl_sess_user_platform']) && $_SESSION['intermingl_sess_user_platform'] != '')
			$condition .= " and u.Platform  = ".$_SESSION['intermingl_sess_user_platform'];
		if(isset($_SESSION['intermingl_sess_user_name']) && $_SESSION['intermingl_sess_user_name'] != '')
			$condition .= " and (u.FirstName LIKE '%".$_SESSION['intermingl_sess_user_name']."%' ||	u.LastName LIKE '%".$_SESSION['intermingl_sess_user_name']."%')";
		if(isset($_SESSION['intermingl_sess_email']) && $_SESSION['intermingl_sess_email'] != '')
			$condition .= " and u.Email LIKE '".$_SESSION['intermingl_sess_email']."%' ";
		if(isset($_SESSION['intermingl_sess_user_status']) && $_SESSION['intermingl_sess_user_status'] != '')
			$condition .= " and u.Status = '".$_SESSION['intermingl_sess_user_status']."' ";
		if(isset($_SESSION['intermingl_sess_location']) && $_SESSION['intermingl_sess_location'] != '')
			$condition .= " and u.Location LIKE '%".$_SESSION['intermingl_sess_location']."%' ";
		if(isset($_SESSION['intermingl_sess_user_registerdate']) && $_SESSION['intermingl_sess_user_registerdate'] != '')
			$condition .= " and date(u.DateCreated) = '".$_SESSION['intermingl_sess_user_registerdate']."'";	
		$sql = "select SQL_CALC_FOUND_ROWS ".$fields." from {$this->userTable} as u
				Left JOIN {$this->cardsTable} as c ON(u.id=c.fkUsersId)
				WHERE 1 ".$condition." group by u.id ORDER BY ".$sorting_clause." ".$limit_clause;
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
	function updateUserDetails($update_string,$condition){
		$sql	 =	"update {$this->userTable}  set ".$update_string." where ".$condition;
	//	echo $sql;
		$this->updateInto($sql);
	}
	function selectUserDetails($field,$condition){
		$sql	 =	"select ".$field." from {$this->userTable} where ".$condition;
	//	echo "<br/>======".$sql;
		$result = 	$this->sqlQueryArray($sql);
			if($result) return $result;
			else false;
	}
	function insertUserDetails($register_values){
		$sql	 =	"insert into  {$this->userTable}  set ";
		if(isset($register_values['username'])	&&	trim($register_values['username']!=""))
			$sql	.=	"UserName 			= 	'".$register_values['username']."',";
		if(isset($register_values['firstname'])	&&	trim($register_values['firstname']!=""))			
			$sql	.=  "FirstName			=	'".$register_values['firstname']."',";
		if(isset($register_values['lastname'])	&&	trim($register_values['lastname']!=""))			
			$sql	.=	"LastName			=	'".$register_values['lastname']."',";
		if(isset($register_values['email'])	&&	trim($register_values['email']!=""))			
			$sql 	.=	"Email 			= 	'".$register_values['email']."',";
		if(isset($register_values['gender'])	&&	trim($register_values['gender']!=""))			
			$sql 	.=	"Gender 		= 	'".$register_values['gender']."',";
			if(isset($register_values['ipaddress'])	&&	trim($register_values['ipaddress']!=""))			
			$sql 	.=	"IpAddress 		= 	'".$register_values['ipaddress']."',";
		if(isset($register_values['fbid'])	&&	trim($register_values['fbid'])!="")
			$sql	.=	"FacebookId	=	'".$register_values['fbid']."',";
		if(isset($register_values['linkedid'])	&&	trim($register_values['linkedid'])!="")
			$sql 	.=	"LinkedInId		=	'".$register_values['linkedid']."',";
		if(isset($register_values['twitterid'])	&&	trim($register_values['twitterid'])!="")
			$sql	.=	"TwitterId	=	'".$register_values['twitterid']."',";
		if(isset($register_values['googleid'])	&&	trim($register_values['googleid'])!="")
			$sql	.=	"GooglePlusId	=	'".$register_values['googleid']."',";			
		if(isset($register_values['interest'])	&&	trim($register_values['interest']!=""))			
			$sql	.=  "Interest			=	'".$register_values['interest']."',";
		//if(isset($register_values['summary'])	&&	trim($register_values['summary']!=""))			
		//	$sql	.=  "Summary			=	'".$register_values['summary']."',";
		if(isset($register_values['company'])	&&	trim($register_values['company']!=""))			
			$sql	.=  "Company			=	'".$register_values['company']."',";
		if(isset($register_values['title'])	&&	trim($register_values['title']!=""))			
			$sql	.=  "Title			=	'".$register_values['title']."',";
		if(isset($register_values['location'])	&&	trim($register_values['location']!=""))			
			$sql	.=  "Location			=	'".$register_values['location']."',";
		//if(isset($register_values['phone'])	&&	trim($register_values['phone']!=""))			
		//	$sql	.=  "Phone			=	'".$register_values['phone']."',";			
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
	function selectContactDetails($fields, $condition)
	{
		$sql	 =	"SELECT ".$fields." FROM {$this->contactTable} AS ct
					JOIN {$this->userTable} AS ut ON ( ut.id = ct.ContactId )
					WHERE ".$condition;
		//echo "<br/>======".$sql;
		$result = 	$this->sqlQueryArray($sql);
			if($result) return $result;
			else false;
	}
	
	function getUserHashDetails($fields, $condition)
	{
	/*	$sql	 =	"SELECT ".$fields." FROM {$this->userTable} AS user
					LEFT JOIN {$this->contactTable} AS ct ON ( ct.fkUserId = user.Id )
					LEFT JOIN {$this->hashTagPostTable} AS htp ON ( htp.fkUserId = user.Id AND htp.Status = 1 )
					LEFT JOIN {$this->hashTagProcessTable} AS ht ON ( ht.fkUserId = user.Id )
					LEFT JOIN {$this->hashTagTable} AS h ON ( h.fkUserId = user.Id )
					WHERE ".$condition;
					LEFT JOIN userinterests as ui ON (user.id=ui.fkUserId)
	
		$sql	 =	"SELECT ".$fields." FROM {$this->userTable} AS user
						LEFT JOIN userinterests as ui ON (user.id=ui.fkUsersId)
						WHERE 1 ".$condition;
	*/
			$sql	 =	"SELECT ".$fields." FROM {$this->userTable} AS user
						LEFT JOIN cards as c ON (user.id=c.fkUsersId)
						LEFT JOIN userinterests as ui ON (user.id=ui.fkUsersId)
						WHERE 1 ".$condition;
		//echo "<br/>======".$sql;
		//LEFT JOIN {$this->hashTagTable} AS ht ON ( ht.fkUserId = user.Id AND ht.Status != 3 )
		$result = 	$this->sqlQueryArray($sql);
			if($result) return $result;
			else false;
	}
	
	function deleteUserReleatedEntries($userId){
		$like_postIds = $like_hashIds = $follow_hashIds = $hashIds = $postIds = '';
		$update_string 	= " Status = 3 ";
		$condition 		= " id IN(".$userId.") ";
		$this->updateUserDetails($update_string,$condition);
		/*
		$sql	 =	" SELECT count(pl.id) as like_count,pl.* from {$this->postLikeTable} as pl where fkUserId in (".$userId.") group by fkUserId,fkHashtagId " ;
		$result = 	$this->sqlQueryArray($sql);
		if(isset($result) && is_array($result) && count($result) > 0 ){
			foreach($result As $key=>$value){
				if($value->like_count > 0 ){
					$sql	 =	"UPDATE {$this->hashCountersTable}  SET  LikeCount = LikeCount-".$value->like_count." WHERE fkHashtagId =".$value->fkHashtagId." and LikeCount > 0 ";
					$this->updateInto($sql);
				}
			}
		}
		$sql	 =	" SELECT count(c.id) as comment_count,c.* from {$this->commentsTable} as c where fkUserId in (".$userId.") group by fkUserId,fkHashtagId ";
		$result = 	$this->sqlQueryArray($sql);		
		if(isset($result) && is_array($result) && count($result) > 0 ){
			foreach($result As $key=>$value){
				if($value->comment_count > 0 ){
					$sql	 =	"UPDATE {$this->hashCountersTable}  SET  CommentCount = CommentCount-".$value->comment_count." WHERE fkHashtagId =".$value->fkHashtagId." and CommentCount > 0 ";
					$this->updateInto($sql);
				}
			}
		}
		$sql	 =	" SELECT * FROM {$this->hashTagProcessTable} where fkUserId in (".$userId.")";
		$result = 	$this->sqlQueryArray($sql);
		if(isset($result) && is_array($result) && count($result) > 0 ){
			foreach($result As $key=>$value){
				$follow_hashIds .= $value->fkHashtagId.',';
			}
		}
		$sql	 =	"UPDATE {$this->hashCountersTable}  SET  FollowCount = FollowCount-1 WHERE fkHashtagId in (".rtrim($follow_hashIds,',').") and FollowCount > 0 ";
		$this->updateInto($sql);
		
		$sql = "delete from {$this->activityTable} WHERE fkUserId in (".$userId.") or (fkActionId = '".$userId."' and ActivityType = 3 )";
		$this->deleteInto($sql);
		
		$sql = "delete from {$this->postLikeTable} WHERE fkUserId in (".$userId.")";
		$this->deleteInto($sql);
		
		$sql = "delete from {$this->commentsTable} WHERE fkUserId in (".$userId.")";
		$this->deleteInto($sql);
		
		$sql = "delete from {$this->sharetrackingTable} WHERE fkUserId in (".$userId.")";
		$this->deleteInto($sql);
		
		$sql = "delete from {$this->hashTagProcessTable} WHERE fkUserId in (".$userId.")";
		$this->deleteInto($sql);
		
		$sql = "delete from {$this->contactTable} WHERE fkUserId in (".$userId.") or ContactId in (".$userId.")";
		$this->deleteInto($sql);
		
		$sql = "delete from {$this->messageTable} WHERE fromUserId in (".$userId.") or toUserId in (".$userId.") ";
		$this->deleteInto($sql); 
		
		$sql = "delete from devicetoken WHERE fkUserId = ".$userId;
		$this->deleteInto($sql);
		
		$sql = "delete from hashtagusercounter WHERE fkUserId in (".$userId.")";
		$this->deleteInto($sql);
		
		$sql = "delete from unreadposts WHERE fkUserId in (".$userId.")";
		$this->deleteInto($sql);
		*/
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
	function getActivity($userId){
	
		$postQuery = "select id from hashtagpost where fkUsersId = $userId";
		$postResult = $this->sqlQueryArray($postQuery);
		$postIds = '0';
		if(is_array($postResult) && count($postResult) > 0){
			$postIds = '';
			foreach($postResult as $key=>$value){
				$postIds .= $value->id.',';
			}
			$postIds = rtrim($postIds,',');
		}
		$contactQuery = "select ContactId from contact where  	fkUsersId = $userId and ContactType = 1";
		$contactResult = $this->sqlQueryArray($contactQuery);
		$contactIds = '0';
		if(is_array($contactResult) && count($contactResult) > 0){
			$contactIds = '';
			foreach($contactResult as $key=>$value){
				$contactIds .= $value->ContactId.',';
			}
			$contactIds = rtrim($contactIds,',');
		}
		
		$blockUserIds 	= $blockCondition = ''; 
		$blockQuery = "select * from contact where (fkUsersId ='".$userId."' or ContactId ='".$userId."') and ContactType = 2 ";
		$blockArray	=  $this->sqlQueryArray($blockQuery);
		//echo'<pre>';print_r($blockArray);echo'</pre>';
		if($blockArray && count($blockArray) > 0){
			foreach($blockArray as $key=>$value){
				if($value->fkUsersId != $userId)
					$blockUserIds .= $value->fkUsersId.',';
				if($value->ContactId != $userId)
					$blockUserIds .= $value->ContactId.',';
			}
		}
		
		if($blockUserIds != '')
		{
			$blockUserIds = rtrim($blockUserIds,',');
			$blockCondition = " and u.id not in (".$blockUserIds.") ";
		}
		$query = "Select a.id as actId,a.ActivityType,u.id as userId,u.Photo,u.UserName,a.ActivityDate,
				  hp.PostType,h.HashtagName,h.OriginalHashtag,h.id as hashId,hp.id as postId,hp.ImagePath
				  from activity as a
				  left join hashtagpost as hp on (hp.id = a.fkActionId and hp.Status=1)
				  left join hashtags as h on ((
				  								(h.id = a.fkActionId and a.ActivityType  = 5) 
												or 
												(h.id = a.fkProcessId and a.ActivityType  = 4) 
												or 
												(h.id = hp.fkHashtagId and (a.ActivityType  = 1 or a.ActivityType  = 2))
												) 
											 and (h.Status=1 or h.Status=2)) 
				  left join user as u on (u.id = a.fkUsersId)
				  where 
				  (
					  (a.fkActionId in ($postIds) and (a.ActivityType = 1 or  a.ActivityType = 2 ))  
					  or 
					  (a.fkUsersId in ($contactIds) and (a.ActivityType = 4  or a.ActivityType =5 )) 
					  or 
					  (a.fkActionId = $userId and  a.ActivityType =3) 
				  ) 
				  and a.fkUsersId !=$userId $blockCondition and u.Status = '1' group by actId order by a.id desc limit 0,50";
		//echo"<br>===================>".$query;
		/*$query = "Select a.id as actId,a.ActivityType,u.id as userId,u.Photo,u.UserName,a.ActivityDate,
				  hp.PostType,h.HashtagName,h.id as hashId,hp.id as postId,hp.ImagePath
				  from activity as a
				  left join hashtagpost as hp on ((hp.id = a.fkActionId) and hp.Status=1)
				  left join hashtags as h on ((h.id = a.fkActionId or h.id = a.fkProcessId) and h.Status=1)
				  
				  left join user as u on (u.id = a.fkUserId)
				  where (
				  	(a.fkActionId in ($postIds) and (a.ActivityType = 1 or  a.ActivityType = 2 ))  
				  or 
				  	(a.fkUserId in ($contactIds) and (a.ActivityType = 4  or a.ActivityType =5 ) ) 
				  or 
				  	(a.fkActionId = $userId and  a.ActivityType =3) 
				  ) 
				  and a.fkUserId !=$userId and u.Status = '1' order by a.id desc limit 0,50";//group by actId */
				  
		//echo"<br>===================>".$query;
		$result = 	$this->sqlQueryArray($query);
		if($result) return $result;
			else false;
	}
	function changeUsersStatus($userIds,$updateStatus){
		$update_string 	= " Status =  ".$updateStatus;
		$condition 		= " id IN(".$userIds.") ";
		$this->updateUserDetails($update_string,$condition);
	}
}
?>