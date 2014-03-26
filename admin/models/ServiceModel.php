<?php
class ServiceModel extends Model
{
   function getTotalRecordCount()
	{
		$result = $this->sqlCalcFoundRows();
        return $result;
	}
	function getServiceList($fields,$condition)
	{
		$limit_clause='';
		$sorting_clause = ' id desc';
		if(!empty($_SESSION['ordertype']))
			$sorting_clause = $_SESSION['orderby'] . ' ' . $_SESSION['ordertype'];
		if(isset($_SESSION['sortBy']) && isset($_SESSION['orderType']))
			$sorting_clause	= $_SESSION['sortBy']. ' ' .$_SESSION['orderType'];
		if(isset($_SESSION['curpage']))
			$limit_clause = ' LIMIT '.(($_SESSION['curpage'] - 1) * ($_SESSION['perpage'])) . ', '. $_SESSION['perpage'];
		if(isset($_SESSION['simplyshredded_sess_search_process']) && $_SESSION['simplyshredded_sess_search_process'] != '')
			$condition .= " and Process LIKE '".$_SESSION['simplyshredded_sess_search_process']."%' ";
		if(isset($_SESSION['simplyshredded_sess_search_module']) && $_SESSION['simplyshredded_sess_search_module'] != '')
			$condition .= " and Module LIKE '%".$_SESSION['simplyshredded_sess_search_module']."%' ";
		$sql = "select SQL_CALC_FOUND_ROWS ".$fields." 
						from {$this->oauthClientEndpointsTable}
				WHERE 1".$condition." group by id ORDER BY ".$sorting_clause." ".$limit_clause;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function deleteServiceDetails($condition)
	{
		$sql = "DELETE FROM {$this->oauthClientEndpointsTable} WHERE ".$condition;
		$this->deleteInto($sql);
	}
	function selectServiceDetails($field,$condition)
	{
		$sql = "select ".$field." 
						from {$this->oauthClientEndpointsTable}
				WHERE 1".$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function selectServiceParamsDetails($field,$condition)
	{
		$sql	=	"SELECT ".$field."
						FROM {$this->oauthClientEndpointsParamsTable} ocept
						RIGHT JOIN {$this->oauthClientEndpointsTable} ocet
						ON (ocet.id = ocept.fkEndpointId)
						WHERE 1 ".$condition;
		$result	=	$this->sqlQueryArray($sql);
		if(count($result) == 0) return false;
		else return $result;
	}
	function insertServiceDetails($values){
		$sql_order = "select max(Ordering) as max_order from {$this->oauthClientEndpointsTable}";
		$result	=	$this->sqlQueryArray($sql_order);
		$result[0]->max_order = $result[0]->max_order+1;
		$sql	 =	"insert into  {$this->oauthClientEndpointsTable}  set	Process 	  = '".$values['process']."',
																			ServicePath	  = '".$values['service_path']."',
																			Method		  = '".$values['method']."',
																			InputParam	  = '".$values['input_param']."',
																			OutputParam	  = '".$values['output_param']."',
																			Module		  = '".$_POST['module_name']."',
																			Ordering	  = ".$result[0]->max_order.",
																			Authorization = '".$_POST['authorization']."'" ;//Aspects		  = '".$_POST['aspects']."',
		$this->result = $this->insertInto($sql);
		$insertId = $this->sqlInsertId();
        return $insertId;
	}
	function insertServiceParamsDetails($values) {
		$sql	=	"INSERT INTO {$this->oauthClientEndpointsParamsTable} 
						(fkEndpointId,FieldName,SampleData,Required,Explanation)
					 	VALUES ".$values;
		//echo "<br>======>".$sql;
		$this->insertInto($sql);
	}
	function insertJsonServiceParamsDetails($values) {
		$sql	 =	"INSERT INTO {$this->oauthClientEndpointsParamsTable}  set	".$values;
		echo "<br>======>".$sql;
		$this->updateInto($sql);
	}
	function deleteServiceParamsDetails($id) {
		$sql	=	"DELETE FROM {$this->oauthClientEndpointsParamsTable}
						WHERE fkEndpointId = ".$id;
		//echo "<br>======>".$sql;
		$this->deleteInto($sql);
	}
	function updateServiceDetails($update_string,$condition){
		$sql	 =	"update {$this->oauthClientEndpointsTable}  set ".$update_string." where ".$condition;
		$this->updateInto($sql);
	}
}
?>