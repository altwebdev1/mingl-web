<?php
ini_set('default_encoding','utf-8');
function admin_login_check()
{
    if(!isset($_SESSION['intermingl_admin_user_name'])){
		header('location:index.php');
		die();
    }
}
function user_login_check(){
	if(!isset($_SESSION["intermingl_ses_user_id"]) || $_SESSION["intermingl_ses_user_id"] == '')
		{ header('location: home'); die();}
}
function getCurrPage()
{
	$page = substr(strrchr($_SERVER['REQUEST_URI'], '/'), 1);
	$page=explode('?',$page);
	if(is_array($page))
		$page=$page[0];
	return $page;
}
function displayText($text, $length) {
	if (strlen($text) > $length) return strip_tags(substr($text, 0, $length)).' ...'; else return $text;
}

/********************************************************
  * Function Name: escapeSpecialCharacters
  * Purpose: Escapes special characters in a string for use in an SQL statement
  * $data   - array or text
  *******************************************************/
function escapeSpecialCharacters($data)
{
	//$data = trim($data);
	if (get_magic_quotes_gpc())
		return $data; //No need to escape data if magic quotes is turned on
	$data = is_array($data) ? array_map('escapeSpecialCharacters', $data) : mysql_real_escape_string($data);
    return $data;
}
/********************************************************
  * Function Name: unEscapeSpecialCharacters
  * Purpose: UnEscapes special characters in a string for use in an SQL statement
  * $data   - array or text
  *******************************************************/
function unEscapeSpecialCharacters($data)
{
	$data = is_array($data) ? array_map('unEscapeSpecialCharacters', $data) :stripslashes($data);
    return $data;
}
function sendMail($mailContentArray,$type)
{
	error_reporting(E_ALL);
	if(is_array($mailContentArray))
	{
		$heardFrom		= 	'';
		$message		=	'';
		$from 	  		=   $mailContentArray['from'];
		$to   		    =   $mailContentArray['toemail'];
		$subject		= 	$mailContentArray['subject'];
		$sitelinkpath	=	ADMIN_SITE_PATH.'/';
		$filename       = 	ADMIN_ABS_PATH.'/webresources/mail_content/'.$mailContentArray['fileName'];
		$mailData 		= 	file_get_contents($filename);
		$filearray 		= 	explode('/',$mailContentArray['fileName']);
		$typearray 		= 	end($filearray);
		$typeextn 		= 	explode('.',$typearray);
		switch($type)
		{
			case 1:
				//User forgot password
				$mailData 			=	str_replace('{NAME}', $mailContentArray['name'], $mailData);
				$mailData			=	str_replace('{EMAIL}', $mailContentArray['email'], $mailData);
				$mailData 			=	str_replace('{PASSWORD}',  $mailContentArray['password'], $mailData);
				break;
			case 2:
				//User Registeration 
				$mailData 			=	str_replace('{NAME}', $mailContentArray['name'], $mailData);
				$mailData 			=	str_replace('{EMAIL}', $mailContentArray['toemail'], $mailData);
				$mailData 			=	str_replace('{PASSWORD}', $mailContentArray['password'], $mailData);
				break;
			case 3:
				//Admin forgot password
				$mailData 			=	str_replace('{NAME}', $mailContentArray['name'], $mailData);
				$mailData			=	str_replace('{NAME}', $mailContentArray['name'], $mailData);
				$mailData 			=	str_replace('{PASSWORD}',  $mailContentArray['password'], $mailData);
				break;	
			case 4:
				//send report
				$mailData 			=	str_replace('{NAME}', $mailContentArray['toUser'], $mailData);
				$mailData			=	str_replace('{FROM_USER}', $mailContentArray['fromUser'], $mailData);
				break;			
		}
		$mail_image 	= 	ADMIN_SITE_PATH.'/webresources/mail_content/';
		$mailData 		=	str_replace('{SITE_PATH}',$sitelinkpath ,$mailData);
		$mailData 		=	str_replace('{SITE_MAIL_PATH}',$mail_image ,$mailData);
		$mailData		=   str_replace('{YEAR}', date('Y'), $mailData);
		$headers  		= 	"MIME-Version: 1.0\n";
		$headers 		.= 	"Content-Transfer-Encoding: 8bit\n";
		$headers        .= 	"From: Mingle $from\r\n";
		$headers 		.= 	"Content-type: text/html\r\n";
		if ($_SERVER['HTTP_HOST'] == '172.21.4.104'){
			//echo $mailData;
		}
		else {
			mail($to,$subject,$mailData,$headers);
			//$sendmail = mailThroughAmazon($from,$to,$subject,$mailData,'','','','','','','','');
		}
	}
}

function mailThroughAmazon($from,$to,$subject,$html_message='',$text_msg='',$file_text_path='',$file_html_path='',$http_url='',$replyto='',$cc='',$bcc='',$return_path=''){

	require_once('ses.php');

	$ses = new SimpleEmailService(AWSACCESSKEY, AWSSECRETKEY);//broadtags -server
		//$ses->verifyEmailAddress($to);

	$m = new SimpleEmailServiceMessage();
	$m->addTo($to);
	$m->setFrom($from);
	$m->setSubject($subject);
	if($text_msg != '' || $html_message != '') {
		$m->setMessageFromString($text_msg,$html_message);
	}
	else if($file_text_path != '' || $file_html_path != '') {
		$m->setMessageFromFile($file_text_path,$file_html_path);
	}
	else if($http_url != '') {
		$m->setMessageFromURL($http_url);
	}
	else {
		return 'Attempt to send mail with out message';
	}
	if($cc != '')
		$m->addCC($cc);
	if($bcc != '')
		$m->addBCC($bcc);
	if($return_path != '')
		$m->setReturnPath($return_path);
	if($replyto != '')
		$m->addReplyTo($replyto);
	//echo '<pre>';print_r($m);echo '</pre>';
	return $ses->sendEmail($m);
}

function destroyPagingControlsVariables() { //clear paging session variables
    unset($_SESSION['orderby']);
    unset($_SESSION['ordertype']);
    unset($_SESSION['curpage']);
    unset($_SESSION['perpage']);
    unset($_SESSION['paging']);
}
//set paging session variables
function setPagingControlValues($default_field,$per_page) {
	if(isset($_POST['per_page']))
		$_SESSION['perpage'] = $_POST['per_page'];
	elseif(!isset($_SESSION['perpage']))
		$_SESSION['perpage'] = $per_page;

	if(isset($_POST['cur_page']))
		$_SESSION['curpage'] = $_POST['cur_page'];
	elseif(!isset($_SESSION['curpage']))
		$_SESSION['curpage'] = 1;

	if(isset($_POST['order_by']))
		$_SESSION['orderby'] = $_POST['order_by'];
	elseif(!isset($_SESSION['orderby']))
		$_SESSION['orderby'] = $default_field;

	if(isset($_POST['order_type']))
		$_SESSION['ordertype'] = $_POST['order_type'];
	elseif(!isset($_SESSION['ordertype']))
		$_SESSION['ordertype'] = 'desc';
	if(isset($_POST['paging_change']) && $_POST['paging_change']!='')
         $_SESSION['curpage'] = $_POST['paging_change'];
}

// Displays the sort icons for the column headings
// Paramters => 	column : field in the database that is merged in the ORDER BY clause of the query
//					title  : column name to be displayed on the screen.
// Output	 =>		Returns as a Hyperlink with given column and field.
function SortColumn($column, $title)
{
	/*if($_SERVER['REMOTE_ADDR']=='172.21.4.95') {
		echo '===>'.__LINE__.'===>'.$column;
		echo '===>'.__LINE__.'===>'.$title;
		die();
	}*/
	$sort_type = 'ASC';
	$sort_image = 'no_sort.png';
	if (($_SESSION['orderby'] == $column) && ($_SESSION['ordertype'] == 'ASC')){  //asc
		$sort_type = 'DESC';
		$sort_image = 'desc.png';
	}
	elseif (($_SESSION['orderby'] == $column) && ($_SESSION['ordertype'] == 'DESC')){ //desc
		$sort_type = 'ASC';
		$sort_image = 'asc.png';
	}
	$alt_title = 'Sort by '.ucfirst(strtolower($title))." ".strtolower($sort_type);
	$sort_link = "<a href=\"#\" onclick=\"javascript:setPagingControlValues('".$column."','".$sort_type."',".$_SESSION['curpage'].");\" alt=\"".$alt_title."\" title=\"".$alt_title."\" >";
	//return $sort_link.'<strong>'.$title.'</strong></a>&nbsp;'.$sort_link.'</a>';//<img src="'.IMAGE_PATH . $sort_image.'" alt="" border="0">
	return $sort_link.'<strong>'.$title.'</strong></a>&nbsp;'.$sort_link.'<img src="'.ADMIN_IMAGE_PATH . $sort_image.'" alt="" border="0"></a>';
}
function SortColumnAjax($column, $title,$functionName)
{
	$sort_type = 'ASC';
	$sort_image = 'no_sort.gif';
	if (($_SESSION['orderby'] == $column) && ($_SESSION['ordertype'] == 'ASC')){  //asc
		$sort_type = 'DESC';
		$sort_image = 'asc.gif';
	}
	elseif (($_SESSION['orderby'] == $column) && ($_SESSION['ordertype'] == 'DESC')){ //desc
		$sort_type = 'ASC';
		$sort_image = 'desc.gif';
	}
	$alt_title = 'Sort by '.ucfirst(strtolower($title))." ".strtolower($sort_type);
	$sort_link = "<a href=\"javascript:void(0);\" onclick=\"javascript:setPagingControlValuesAjax('".$column."','".$sort_type."',".$_SESSION['curpage']."); $functionName \" alt=\"".$alt_title."\" title=\"".$alt_title."\" >";
	//return $sort_link.'<strong>'.$title.'</strong></a>&nbsp;'.$sort_link.'</a>';//<img src="'.IMAGE_PATH . $sort_image.'" alt="" border="0">
	return $sort_link.'<strong>'.$title.'</strong></a>&nbsp;'.$sort_link.'<img src="'.ADMIN_IMAGE_PATH . $sort_image.'" alt="" border="0"></a>';
}

// Display paging control
//Input : no. of records and URL
function pagingControlLatest($total,$action='')
{
	$per_page 		= $_SESSION['perpage'];
	$page 			= $_SESSION['curpage'];
	$pagination 	= '<table cellspacing="0" cellpadding="0" width="100%" border="0" align="center">
	<tr>
	<td align="center" width="80%" ><table cellspacing="0" cellpadding="0" border="0" align="center" width="100%"><tr><td align="center" valign="top"> ';
	if ($action == '')
		$action = $_SERVER['SCRIPT_NAME'];
	?>
	<form name="paging" id="paging" method="post" action="<?php echo($action);?>"  >
		<input type="Hidden" value="<?php echo($_SESSION['curpage']);?>" name="cur_page" id="cur_page">
		<input type="Hidden" value="<?php echo($_SESSION['orderby']);?>" name="order_by" id="order_by">
		<input type="Hidden" value="<?php echo($_SESSION['ordertype']);?>" name="order_type" id="order_type">
		<?php
		if ($total > $per_page)
		{
        $adjacents = "2";

    	$page = ($page == 0 ? 1 : $page);
    	$start = ($page - 1) * $per_page;

		$firstPage = 1;

		$prev = ($page == 1)?1:$page - 1;

    	$prev = $page - 1;
    	$next = $page + 1;
        $lastpage = ceil($total/$per_page);
    	$lpm1 = $lastpage - 1;

    	if($lastpage > 1)
    	{
    		$pagination .= "<ul class='pagination'>";
                   // $pagination .= "<li class='details'>Page $page of $lastpage</li>";
			if ($page == 1)
			{
				$pagination.= "<li><a class='current cnxt_arr_lt'><span></span></a></li>";
				$pagination.= "<li><a class='current cnxt_arr'><span></span></a></li>";
			}
			else
			{
				$pagination.= "<li><a class='nxt_arr_lt' href='javascript:void(0);' onclick=\"javascript:setPagingControlValues('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$firstPage);\" ><span></span></a></li>";
				$pagination.= "<li><a class='nxt_arr' href='javascript:void(0);' onclick=\"javascript:setPagingControlValues('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$prev);\" ><span></span></a></li>";
			}

    		if ($lastpage < 7 + ($adjacents * 2))
    		{
    			for ($counter = 1; $counter <= $lastpage; $counter++)
    			{
    				if ($counter == $page)
    					$pagination.= "<li><a class='current'>$counter</a></li>";
    				else
    					$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValues('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$counter);\">$counter</a></li>";
    			}
    		}
    		elseif($lastpage > 5 + ($adjacents * 2))
    		{
    			if($page < 1 + ($adjacents * 2))
    			{
    				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
    				{
    					if ($counter == $page)
    						$pagination.= "<li><a class='current'>$counter</a></li>";
    					else
    						$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValues('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$counter);\">$counter</a></li>";
    				}
    				$pagination.= "<li class='dot'>...</li>";
    				$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValues('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$lpm1);\">$lpm1</a></li>";
    				$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValues('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$lastpage);\">$lastpage</a></li>";
    			}
    			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
    			{
    				$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValues('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',1);\">1</a></li>";
    				$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValues('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',2);\">2</a></li>";
    				$pagination.= "<li class='dot'>...</li>";
    				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
    				{
    					if ($counter == $page)
    						$pagination.= "<li><a class='current'>$counter</a></li>";
    					else
    						$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValues('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$counter);\">$counter</a></li>";
    				}
    				$pagination.= "<li class='dot'>..</li>";
    				$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValues('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$lpm1);\" >$lpm1</a></li>";
    				$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValues('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$lastpage);\" >$lastpage</a></li>";
    			}
    			else
    			{
    				$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValues('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',1);\" >1</a></li>";
    				$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValues('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',2);\" >2</a></li>";
    				$pagination.= "<li class='dot'>..</li>";
    				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
    				{
    					if ($counter == $page)
    						$pagination.= "<li><a class='current'>$counter</a></li>";
    					else
    						$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValues('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$counter);\" >$counter</a></li>";
    				}
    			}
    		}

    		if ($page < $counter - 1){
    			$pagination.= "<li><a  class='pre_arr' href='javascript:void(0);' onclick=\"javascript:setPagingControlValues('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$next);\" ><span></span></a></a></li>";
                $pagination.= "<li><a class='pre_arr_lt' shref='javascript:void(0);' onclick=\"javascript:setPagingControlValues('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$lastpage);\" ><span></span></a></a></li>";
    		}else{
    			$pagination.= "<li><a class='current cpre_arr'><span></span></a></a></li>";
                $pagination.= "<li><a class='current cpre_arr_lt'><span></span></a></a></li>";
            }
    		$pagination.= "</ul>";
    	}
	}
		 echo $pagination; ?>
		 	</td></tr>
		 </table>
		</td>
		<td  class="record" align="right">
		<?php $per_page_array =  eval(ADMIN_PER_PAGE_ARRAY);
		if($total > $per_page_array[0]){ ?>
			
			<span class="fright">
				<select name="per_page" id="per_page" onchange="setPerPage(this.value);" style="width:40px;">
				<?php foreach($per_page_array as $value){ ?>
					<option value="<?php echo($value);?>" <?php if($per_page == $value) { echo "selected='selected'"; } ?>><?php echo($value);?></option>
				<?php } ?>
				</select>
			</span>
			<span class="recor_txt">Per page &nbsp;</span>
		<?php }?>
		</td></tr></table>
	</form>
<?php }
// Display paging control
//Input : no. of records and URL
function pagingControlLatestAjax($total,$functionName='')
{
	$per_page 		= $_SESSION['perpage'];
	$page 			= $_SESSION['curpage'];
	$pagination 	= '<br><table cellspacing="0" cellpadding="0" width="100%" border="0" align="center">
	<tr>
	<td align="center" width="90%" ><table cellspacing="0" cellpadding="0" border="0" align="center"><tr><td> ';
	?>
	<form name="pagingAjax" id="pagingAjax" method="post"   >
		<input type="Hidden" value="<?php echo($_SESSION['curpage']);?>" name="cur_page" id="cur_page">
		<input type="Hidden" value="<?php echo($_SESSION['orderby']);?>" name="order_by" id="order_by">
		<input type="Hidden" value="<?php echo($_SESSION['ordertype']);?>" name="order_type" id="order_type">
		<?php
		if ($total > $per_page)
		{
        $adjacents = "2";
    	$page = ($page == 0 ? 1 : $page);
    	$start = ($page - 1) * $per_page;

		$firstPage = 1;

		$prev = ($page == 1)?1:$page - 1;

    	$prev = $page - 1;
    	$next = $page + 1;
        $lastpage = ceil($total/$per_page);
    	$lpm1 = $lastpage - 1;

    	if($lastpage > 1)
    	{
    		$pagination .= "<ul class='pagination'>";
                    $pagination .= "<li class='details'>Page $page of $lastpage</li>";
			if ($page == 1)
			{
				$pagination.= "<li><a class='current cnxt_arr_lt'><span></span></a></li>";
				$pagination.= "<li><a class='current cnxt_arr'><span></span></a></li>";
			}
			else
			{
				$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValuesAjax('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$firstPage); $functionName \" ><<</a></li>";
				$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValuesAjax('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$prev); $functionName \" ><</a></li>";
			}
    		if ($lastpage < 7 + ($adjacents * 2))
    		{
    			for ($counter = 1; $counter <= $lastpage; $counter++)
    			{
    				if ($counter == $page)
    					$pagination.= "<li><a class='current'>$counter</a></li>";
    				else
    					$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValuesAjax('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$counter); $functionName \">$counter</a></li>";
    			}
    		}
    		elseif($lastpage > 5 + ($adjacents * 2))
    		{
    			if($page < 1 + ($adjacents * 2))
    			{
    				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
    				{
    					if ($counter == $page)
    						$pagination.= "<li><a class='current'>$counter</a></li>";
    					else
    						$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValuesAjax('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$counter); $functionName \">$counter</a></li>";
    				}
    				$pagination.= "<li class='dot'>...</li>";
    				$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValuesAjax('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$lpm1); $functionName \">$lpm1</a></li>";
    				$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValuesAjax('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$lastpage); $functionName \">$lastpage</a></li>";
    			}
    			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
    			{
    				$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValuesAjax('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',1); $functionName \">1</a></li>";
    				$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValuesAjax('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',2); $functionName \">2</a></li>";
    				$pagination.= "<li class='dot'>...</li>";
    				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
    				{
    					if ($counter == $page)
    						$pagination.= "<li><a class='current'>$counter</a></li>";
    					else
    						$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValuesAjax('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$counter); $functionName \">$counter</a></li>";
    				}
    				$pagination.= "<li class='dot'>..</li>";
    				$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValuesAjax('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$lpm1); $functionName \" >$lpm1</a></li>";
    				$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValuesAjax('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$lastpage); $functionName \" >$lastpage</a></li>";
    			}
    			else
    			{
    				$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValuesAjax('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',1); $functionName \" >1</a></li>";
    				$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValuesAjax('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',2); $functionName \" >2</a></li>";
    				$pagination.= "<li class='dot'>..</li>";
    				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
    				{
    					if ($counter == $page)
    						$pagination.= "<li><a class='current'>$counter</a></li>";
    					else
    						$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValuesAjax('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$counter); $functionName \" >$counter</a></li>";
    				}
    			}
    		}
    		if ($page < $counter - 1){
    			$pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValuesAjax('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$next); $functionName \" >></a></a></li>";
                $pagination.= "<li><a href='javascript:void(0);' onclick=\"javascript:setPagingControlValuesAjax('".$_SESSION['orderby']."','".$_SESSION['ordertype']."',$lastpage); $functionName \" >>></a></a></li>";
    		}else{
    			$pagination.= "<li><a class='current'>></a></a></li>";
                $pagination.= "<li><a class='current'>>></a></a></li>";
            }
    		$pagination.= "</ul>";
    	}
	}
		 echo $pagination; ?>
		 	</td></tr>
		 </table>
		</td>
		<?php  $per_page_array =  eval(ADMIN_PER_PAGE_ARRAY);
		if($total > $per_page_array[0]){ ?>
		<td  class="record">
		<strong>Per page </strong>
		</td><td class="record" width="20%" style="padding-right:10px;">

			<select name="per_page" id="per_page" onchange="setPerPageAjax(this.value);<?php  echo $functionName; ?>" style="width:40px;">
			<?php foreach($per_page_array as $value){?>
				<option value="<?php echo($value);?>" <? if($per_page == $value) echo " selected='selected'"?>><?php echo($value);?></option>
			<?php }?>
			</select>
		</td>
		<?php }?>
		</tr>
		</table>
	</form>
<?php } 
function uploadVideoToS3($video,$videoName){
	$filename = 'demoposts/'.$videoName;
	if (!class_exists('S3'))require_once('S3.php');
			//instantiate the class		
			$s3 = new S3(AWSACCESSKEY, AWSSECRETKEY);
			$s3->putBucket(BUCKET_NAME, S3::ACL_PUBLIC_READ);
				
			if ($s3->putObjectFile($video, BUCKET_NAME, $filename, S3::ACL_PUBLIC_READ,
					array( // Custom $requestHeaders
					   "Cache-Control" => "max-age=315360000",
			            "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+5 years"))
			        )
			)) {
					//echo "<strong>We successfully uploaded your file.</strong>";
				}else{
					//echo "<strong>Something went wrong while uploading your file... sorry.</strong>";
				}
			
			
	
}
function uploadImageToS3($image_path,$type,$image_name){
	//error_reporting(E_ALL);
	$filename = '';
	if($type == 1){ //user thumb
		$filename = 'users/thumbnail/'.$image_name;
	} else if($type == 2){ //user small thumb
		$filename = 'users/smallThumbnail/'.$image_name;
	} else if($type == 3){ //user Original 
		$filename = 'users/'.$image_name;
	} else if($type == 4){ //cover thumb
		$filename = 'users/cover/'.$image_name;
	}else if($type == 5){ //cover original
		$filename = 'users/cover/original/'.$image_name;
	}else if($type == 6){ //Post thumb
		$filename = 'posts/'.$image_name;
	}else if($type == 7){ //Post thumb
		$filename = 'demoposts/'.$image_name;
	}else if($type == 8){ //message
		$filename = 'message/'.$image_name;
	}
	if (!class_exists('S3'))require_once('S3.php');
			//instantiate the class		
			$s3 = new S3(AWSACCESSKEY, AWSSECRETKEY);
			$s3->putBucket(BUCKET_NAME, S3::ACL_PUBLIC_READ);
				
			if ($s3->putObjectFile($image_path, BUCKET_NAME, $filename, S3::ACL_PUBLIC_READ,
					array( // Custom $requestHeaders
					   "Cache-Control" => "max-age=315360000",
			            "Expires" => gmdate("D, d M Y H:i:s T", strtotime("+5 years"))
			        )
			)) {
					//echo "<strong>We successfully uploaded your file.</strong>";
				}else{
					//echo "<strong>Something went wrong while uploading your file... sorry.</strong>";
				}
			
			
}

function image_exists($type,$image_name){
	return true;
	if($type == 1){ //user thumb
		$filename = 'users/thumbnail/'.$image_name;
	} else if($type == 2){ //user small thumb
		$filename = 'users/smallThumbnail/'.$image_name;
	} else if($type == 3){ //user Original 
		$filename = 'users/'.$image_name;
	} else if($type == 4){ //cover thumb
		$filename = 'users/cover/'.$image_name;
	}else if($type == 5){ //cover original
		$filename = 'users/cover/original/'.$image_name;
	}else if($type == 6){ //Post thumb
		$filename = 'posts/'.$image_name;
	}else if($type == 8){ //message
		$filename = 'message/'.$image_name;
	}else if($type == 9){ //message
		$filename = 'events/cover/'.$image_name;
	}
	$bucket = BUCKET_NAME;
	if (!class_exists('S3'))require_once('S3.php');
	$s3 = new S3(AWSACCESSKEY, AWSSECRETKEY);
	$info = $s3->getObjectInfo($bucket, $filename);
	if ($info){
	 return true;
	}
	else{
		return false;
	}
}
function deleteImages($type,$image_name){
	if($type == 1){ //user thumb
		$filename = 'users/thumbnail/'.$image_name;
	} else if($type == 2){ //user small thumb
		$filename = 'users/smallThumbnail/'.$image_name;
	} else if($type == 3){ //user Original 
		$filename = 'users/'.$image_name;
	} else if($type == 4){ //cover thumb
		$filename = 'users/cover/'.$image_name;
	}else if($type == 5){ //cover original
		$filename = 'users/cover/original/'.$image_name;
	}else if($type == 6){ //Post thumb
		$filename = 'posts/'.$image_name;
	}else if($type == 8){ //message
		$filename = 'message/'.$image_name;
	}
	
	$bucket = BUCKET_NAME;
	if (!class_exists('S3'))require_once('S3.php');
	$s3 = new S3(AWSACCESSKEY, AWSSECRETKEY);
	$info = $s3->deleteObject($bucket, $filename);
	if ($info){
	 return true;
	}
	else{
		return false;
	}
}

function ipAddress(){
	/*if (!empty($_SERVER['HTTP_CLIENT_IP'])){
 		$ip_address=$_SERVER['HTTP_CLIENT_IP'];
	}elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
 		$ip_address=$_SERVER['HTTP_X_FORWARDED_FOR'];
	}else{
 		$ip_address=$_SERVER['REMOTE_ADDR'];
	}*/	
	if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
 		$ip_address=$_SERVER['HTTP_X_FORWARDED_FOR'];
	}else{
 		$ip_address=$_SERVER['REMOTE_ADDR'];
	}
	return $ip_address;
}
function sendingPNForAllUser($user_id,$message){
	if($_SERVER['REMOTE_ADDR']=='172.21.4.95') {
		echo '===>'.__LINE__.'===>'.$user_id.'<br />';
		echo '===>'.__LINE__.'===>'.$message.'<br />';
	}
}
function imagethumb_new($src, $des,$itype,$exn, $maxwidth, $hght) {
		ini_set('memory_limit', '512M');
		
		if(file_exists($src))
		   	$size=getimagesize($src);
		$mime_type	=	$size['mime'];
		$mime_type	= strtolower($mime_type);
		$itype	=	substr($mime_type,strpos($mime_type,'/')+1);
		if($mime_type	==	'image/gif') $exn	= 'gif';
		if( ($mime_type	==	'image/pjpeg') || ($mime_type	==	'image/jpg') || ($mime_type	==	'image/jpeg')) $exn	= 'jpg';
		//if($mime_type	==	'image/jpg') $exn	= 'jpg';
		//if($mime_type	==	'image/jpeg') $exn	= 'jpeg';
		if($mime_type	==	'image/png') $exn	= 'png';
		if($mime_type	==	'image/bmp') $exn	= 'bmp';
		
		$n_width=$size[0];
		$n_height=$size[1];
		$imagehw = GetImageSize($src);
	
		$imagewidth = $imagehw[0];
		$imageheight = $imagehw[1];
		$imgorig = $imagewidth;
		$n_width1 = $maxwidth;
		$n_height1 = $hght;
		if (($n_width - $n_width1) > ($n_height - $n_height1)) 
		{
		  //$imageprop=($n_width1*100)/$imagewidth;
		  //$imagevsize= ($imageheight*$imageprop)/100 ;
		  $imagewidth=$n_width1; 
		  $imageheight=($n_width1/$n_width)*$n_height;
		}else
		{		
			//$imageprop=($n_height1*100)/$imageheight;
		  	//$imagevsize= ($imageheight*$imageprop)/100 ;
		    $imagewidth=($n_height1/$n_height)*$n_width; 
		    $imageheight=$n_height1;	  
	
		}
		
		if($imagewidth > $n_width1){
			$imagewidth = $n_width1;
			$imageheight = round($imageheight / ($imagewidth/$n_width1));
		}
		
		if($n_width <= $n_width1 && $n_height <= $n_height1){
			$imagewidth = $n_width;
			$imageheight = $n_height;
		}
			
		
		//$n_width = $imagewidth;
		//$n_height = $imageheight;
		
		$destimg=imagecreatetruecolor($n_width1,$n_height1) or die("Problem In Creating image");
		switch($exn)
		{
		case "jpg":	
		$srcimg=ImageCreateFromJPEG($src) or die("Problem In opening Source Image");
		$destimg=imagecreatetruecolor($n_width1,$n_height1) or die("Problem In Creating image");
		break;
		case "jpeg":
		$srcimg=ImageCreateFromJPEG($src) or die("Problem In opening Source Image");
		$destimg=imagecreatetruecolor($n_width1,$n_height1) or die("Problem In Creating image");
		break;
		case "pjpeg":
		$srcimg=ImageCreateFromJPEG($src) or die("Problem In opening Source Image");
		$destimg=imagecreatetruecolor($n_width1,$n_height1) or die("Problem In Creating image");
		break;
		case "gif":
		$srcimg=ImageCreateFromGIF($src) or die("Problem In opening Source Image");
		$destimg=ImageCreate($n_width1,$n_height1) or die("Problem In Creating image");
		break;
		case "png":
		$srcimg=ImageCreateFromPNG($src) or die("Problem In opening Source Image");
		$destimg=imagecreatetruecolor($n_width1,$n_height1) or die("Problem In Creating image");
		break;
		case "bmp":
		$srcimg =ImageCreateFromBMP($src) or die("Problem In opening Source Image");
		$destimg=imagecreatetruecolor($n_width1,$n_height1) or die("Problem In Creating image");
		break;
		}
		
		$int = hexdec('ffffff');
		$arr = array("red" => 0xFF & ($int >> 0x10),
	               "green" => 0xFF & ($int >> 0x8),
	               "blue" => 0xFF & $int);
	
		//$black = ImageColorAllocate($image, $arr["red"], $arr["green"], $arr["blue"]); 
		$transparent = imagecolorallocate($destimg,  $arr["red"], $arr["green"], $arr["blue"]);
		
		for($x=0;$x<$n_width1;$x++) {
	           for($y=0;$y<$n_height1;$y++) {
	             imageSetPixel( $destimg, $x, $y, $transparent );
	           }
	         }
		$dest_x = (( $n_width1 / 2 ) - ( $imagewidth / 2 )); // centered
		$dest_y = (( $n_height1 / 2 ) - ( $imageheight / 2 )); // centered	
		ImageCopyresampled($destimg,$srcimg,$dest_x,$dest_y,0,0,$imagewidth,$imageheight,$n_width,$n_height) or die("Problem In resizing");
		//$destimg = $srcimg;
		if(($itype=="jpg")||($itype=="jpeg")||($itype=="pjpeg"))
		{	
			//header('Content-type:image/jpeg');
			
			imagejpeg($destimg, $des, 100); //ImageJPEG($destimg) or die('Problem In saving');
		}
		else
		if($itype=="gif")
		{
			//header('Content-type:image/gif');
			ImageGIF($destimg,$des) or die("Problem In saving"); //ImageGIF($destimg) or die('Problem In saving');
		}
		else
		if($itype=="png")
		{
			//header('Content-type:image/png');
			
			imagepng($destimg, $des, 9, PNG_NO_FILTER) or die("Problem In saving"); //ImagePNG($destimg) or die('Problem In saving');
		}
		else
		if($itype=="bmp")
		{
			//header('Content-type:image/png');
			imagebmp($destimg, $des, 9, BMP_NO_FILTER) or die("Problem In saving"); //ImagePNG($destimg) or die('Problem In saving');
		}
		imagedestroy($destimg);
	}  
	function checkImage($files,$type){
		$flag = 0;
		if (isset($files['tmp_name']) && $files['tmp_name'] != '') {
	   		$ImageArray = array('image/png','image/jpg','image/jpeg','image/gif');
			$dimension = getImageSize($files['tmp_name']);
			if(!in_array($files['type'],$ImageArray)){
				$flag = 1;
			}
			else if(!empty($files['error'])){
				$flag = 2;
			}
			else if($files['size'] > 5242880 ) {
				$flag = 3;
			}
			else if($type ==1 && ($dimension[0] < '100' || $dimension[1] < '100')){
				$flag = 4;
			}
			else if($type ==2 && ($dimension[0] < '300' || $dimension[1] < '100')){
				$flag = 4;
			}
			else if($type ==3 && ($dimension[0] < '640')){
				$flag = 4;
			}
			else{
				$flag = 5;//success
			}
	   }
	   return $flag;
	}
function checkVideo($files){
	if (isset($files['tmp_name']) && $files['tmp_name'] != '') {
		//echo "<br>==================>".$files['size'];
		$VideoArray = array('image/mp4');
		if(!in_array($files['type'],$VideoArray)){
				$flag = 1;
		}
		else if(!empty($files['error'])){
			$flag = 2;
		}
		else if($files['size'] > 5242880 ) {
			$flag = 3;
		}
		else{
				$flag = 5;//success
	    }
	}
}
function subval_sort($a,$subkey) {
			$b  = $c  =	array();
			if(is_array($a) && count($a) > 0) {
				foreach($a as $k=>$v) {
					$b[$k] = $v[$subkey];
				}
			}
			if(is_array($b) && count($b) > 0) {
				asort($b);
				foreach($b as $key=>$val) {
					$c[]   = $a[$key];
				}
			}
			return $c;
}
function sorting($a,$subkey) {

			$b  = $c  =	array();
			if(is_array($a) && count($a) > 0) {
				foreach($a as $k=>$v) {
					$b[$k] = $v->$subkey;
				}
			}
			if(is_array($b) && count($b) > 0) {
				asort($b);
				foreach($b as $key=>$val) {
					$c[]   = $a[$key];
				}
			}
			return $c;
}
function getTimeZone(){
	$key="9dcde915a1a065fbaf14165f00fcc0461b8d0a6b43889614e8acdb8343e2cf15";
	if ($_SERVER['HTTP_HOST'] == '172.21.4.104'){
		$ip = '27.124.58.84';
	}
	else{
		//$ip = $_SERVER['REMOTE_ADDR']; //
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR']; 
	}
	$url = "http://api.ipinfodb.com/v3/ip-city/?key=$key&ip=$ip&format=xml";

	$xml = simplexml_load_file($url);
	foreach($xml->children() as $child)
  	{
 	 //echo $child->getName() . ": " . $child . "<br />";
	 if($child->getName() == 'timeZone')
	 	return $child ;
 	}
}
function convertIntocheckinGmtSite($meet_time, $time_zone=''){
	 if($time_zone=='') {
	 	if(!isset($_SESSION['intermingl_ses_from_timeZone']) || $_SESSION['intermingl_ses_from_timeZone'] == ''){
			 $time_zone = getTimeZone();
			 $_SESSION['intermingl_ses_from_timeZone'] = strval($time_zone);
		} else {
			$time_zone = $_SESSION['intermingl_ses_from_timeZone'];
		}
	 }
	 if(substr($time_zone,0,1) == '-') {
	 	$multiplier = -1;
		$time_zone = substr($time_zone,1);
	 }
	 else
	 	$multiplier = 1;
	$offset_array = explode(':',$time_zone);
	$hour= $offset_array[0];
	$minutes= $offset_array[1];
	$offset = $multiplier*(($hour*60*60)+($minutes*60));
	if($meet_time != '') {
		$date = strtotime(gmdate($meet_time))+$offset;
	}
	else{
		$date = strtotime(gmdate('Y-m-d H:i:s'))+$offset;
	}
	//echo '<pre>';print_r($date);echo '</pre>';
	return date('Y-m-d H:i:s',$date);
}
/********************************************************
* Function : displayDate
********************************************************/
function displayDate($postedDate,$time_zone='')
{
	//$endDate = '2012-07-24 14:00:01';
	if($postedDate != '')
	{
		$db_date	=	date('d',strtotime($postedDate));
		$db_month	=	date('M',strtotime($postedDate));
		$db_year	=	date('Y',strtotime($postedDate));
		$db_hour	=	date('h',strtotime($postedDate));
		$db_min		=	date('i',strtotime($postedDate));
		$db_sec		=	date('s',strtotime($postedDate));
		$meridian	=	date('a',strtotime($postedDate));


		if($time_zone != '') {
			if(substr($time_zone,0,1) == '-') {
				$multiplier = -1;
				$time_zone = substr($time_zone,1);
			}
			else
				$multiplier = 1;
			$offset_array 	= explode(':',$time_zone);
			$hour			= $offset_array[0];
			$minutes		= $offset_array[1];
			$offset 		= $multiplier*(($hour*60*60)+($minutes*60));

			$c_date 		= strtotime(date('Y-m-d H:i:s'))+$offset;
			$cur_date		= date('d',$c_date);
			$cur_month		= date('M',$c_date);
			$cur_year		= date('Y',$c_date);
		}
		else {
			$cur_date	=	date('d');
			$cur_month	=	date('M');
			$cur_year	=	date('Y');
			$c_date 		= strtotime(date('Y-m-d H:i:s'));
		}
		$posted_week = date('W',strtotime($postedDate));
		$current_week = date('W',$c_date);
		if( ($db_year==$cur_year) && ($db_month==$cur_month) )
		{
			if($db_date==$cur_date)
				$time =	$db_hour.':'.$db_min.' '.$meridian;
			else if( ($db_date+1)==$cur_date)
				$time =	'Yesterday '.$db_hour.':'.$db_min.' '.$meridian;
			else if($posted_week == $current_week) {
				$week_day = date('l',strtotime($postedDate));
				$time =	$week_day.' '.$db_hour.':'.$db_min.' '.$meridian;
			}
			else
				//$time =	$db_date. ' '.$db_month.' '.$db_hour.':'.$db_min.' '.$meridian;
				$time =	$db_month. ' '.$db_date.' '.$db_hour.':'.$db_min.' '.$meridian;
		}
		else if($db_year==$cur_year)
			$time =	$db_month. ' '.$db_date.' '.$db_hour.':'.$db_min.' '.$meridian;
		else
			//$time =	$db_date. ' '.$db_month.' '.$db_year.' '.$db_hour.':'.$db_min.' '.$meridian;
			$time =	$db_month. ' '.$db_date.' '.$db_year.' '.$db_hour.':'.$db_min.' '.$meridian;
	}
	else
		$time = 'Null';
	return $time;
}
/********************************************************
* Function : displayDate
********************************************************/
function displayConversationDateTime($postedDate,$time_zone='')
{
	//$endDate = '2012-07-24 14:00:01';
	if($postedDate != '')
	{
		$db_date	=	date('d',strtotime($postedDate));
		$db_month	=	date('m',strtotime($postedDate));
		$db_year	=	date('Y',strtotime($postedDate));
		$db_hour	=	date('h',strtotime($postedDate));
		$db_min		=	date('i',strtotime($postedDate));
		$db_sec		=	date('s',strtotime($postedDate));
		$meridian	=	date('A',strtotime($postedDate));


		if($time_zone != '') {
			if(substr($time_zone,0,1) == '-') {
				$multiplier = -1;
				$time_zone = substr($time_zone,1);
			}
			else
				$multiplier = 1;
			$offset_array 	= explode(':',$time_zone);
			$hour			= $offset_array[0];
			$minutes		= $offset_array[1];
			$offset 		= $multiplier*(($hour*60*60)+($minutes*60));
			$c_date 		= strtotime(date('Y-m-d H:i:s'))+$offset;
			$cur_date		= date('d',$c_date);
			$cur_month		= date('m',$c_date);
			$cur_year		= date('Y',$c_date);
		}
		else {
			$cur_date	=	date('d');
			$cur_month	=	date('m');
			$cur_year	=	date('Y');
		}
		//$time =	$db_month. '/'.$db_date.'/'.$db_year.'&nbsp;&nbsp;'.$db_hour.':'.$db_min;.':'.$db_sec.' '.$meridian;
		$time =	$db_month. '/'.$db_date.'/'.$db_year.'&nbsp;&nbsp;'.$db_hour.':'.$db_min.' '.$meridian;
	}
	else
		$time = 'Null';
	return $time;
}
/********************************************************
* Function : displayDate
********************************************************/
function displayConversationTime($postedDate,$time_zone='')
{
	//$endDate = '2012-07-24 14:00:01';
	if($postedDate != '')
	{
		$db_date	=	date('d',strtotime($postedDate));
		$db_month	=	date('M',strtotime($postedDate));
		$db_year	=	date('Y',strtotime($postedDate));
		$db_hour	=	date('h',strtotime($postedDate));
		$db_min		=	date('i',strtotime($postedDate));
		$db_sec		=	date('s',strtotime($postedDate));
		$meridian	=	date('a',strtotime($postedDate));


		if($time_zone != '') {
			if(substr($time_zone,0,1) == '-') {
				$multiplier = -1;
				$time_zone = substr($time_zone,1);
			}
			else
				$multiplier = 1;
			$offset_array 	= explode(':',$time_zone);
			$hour			= $offset_array[0];
			$minutes		= $offset_array[1];
			$offset 		= $multiplier*(($hour*60*60)+($minutes*60));
			$c_date 		= strtotime(date('Y-m-d H:i:s'))+$offset;
			$cur_date		= date('d',$c_date);
			$cur_month		= date('M',$c_date);
			$cur_year		= date('Y',$c_date);
		}
		else {
			$cur_date	=	date('d');
			$cur_month	=	date('M');
			$cur_year	=	date('Y');
		}
		if( ($db_year==$cur_year) && ($db_month==$cur_month) )
		{
			if($db_date==$cur_date)
				$time =	$db_hour.':'.$db_min.' '.$meridian;
			else if( ($db_date+1)==$cur_date)
				$time =	'Yesterday ';
			else
				//$time =	$db_date. ' '.$db_month.' '.$db_hour.':'.$db_min.' '.$meridian;
				$time =	$db_month. ' '.$db_date;
		}
		else if($db_year==$cur_year)
			$time =	$db_month. ' '.$db_date;
		else
			//$time =	$db_date. ' '.$db_month.' '.$db_year.' '.$db_hour.':'.$db_min.' '.$meridian;
			$time =	$db_month. ' '.$db_date.' '.$db_year;
	}
	else
		$time = 'Null';
	return $time;
}

/********************************************************
* Function : Emoji Comment Text
********************************************************/
function getCommentTextEmoji($plateform,$comments,$from='',$emojiNeeded=''){
		error_reporting(0);
		//$plateform = 'android';
		require_once("emoji.php");
		//echo "<br>==================>".$plateform;
		if($plateform == 'ios' ) 
		{
			 //$datas = base64_encode(emoji_unified_to_softbank($comments)); 
			 $datas = $comments; 
		}
		else if($plateform == 'android' ) 
		{
			  if($from == '1'){
					if($emojiNeeded == 0)
					 	$datas = $comments; 
					else{
						 $convertedComments = emoji_unified_to_html(base64_decode($comments));
						 preg_match_all('/<span class="emoji (.*?)"><\/span>/', $convertedComments, $matches);
							if(count($matches)>1) {
								$data = $convertedComments;
								foreach($matches as $key => $value) {
									if($key==0) {
										foreach($value as $subKey => $subValue) {
											$data = str_replace($subValue,'%*%'.$matches[1][$subKey].'%*%',$data);
										}
									}
								}
							} else {
								$data = preg_replace("/<span[^>]+\>/i", $matches[1][0], $htmlContent);
							}
							if(!strstr($convertedComments,'class="emoji')){
								$data = $convertedComments;
							}
							
							$datas = base64_encode($data);
					}
			 }
			 else{
				$datas = $comments; 
			 }
			
		}
		else{
			 if($from == '1' ) 
			{
				 $data_caption = base64_decode($comments);
				 $datasComment       = emoji_softbank_to_unified($data_caption);
			}
			else if($from == '2' ) 
			{
				 $data_caption = base64_decode($comments);
				 $datasComment = emoji_google_to_unified($data_caption);
			}
			else{
				  $datasComment = base64_decode($comments);
				  return  $datasComment;
			}
			if( preg_match('/(mac)/i', $_SERVER['HTTP_USER_AGENT'])) { 	
				 //$datas = emoji_html_to_unified(emoji_unified_to_html($comments));
				 $datas = emoji_unified_to_html($datasComment);
			}
			else{
				$datas = emoji_unified_to_html($datasComment);
			}
			
		//	$datas = preg_replace('/[^(\x20-\x7F)]*/','', $datas);
		}
	return $datas;
}

/********************************************************
* Function : Emoji Comment Text
********************************************************/
function addCommentTextEmoji($plateform,$comments){
		//error_reporting(E_ALL);
		/*require_once("emoji.php");
		if($plateform == 'ios' ) 
		{
			 $data_caption = base64_decode($comments);
			 $datas       = emoji_softbank_to_unified($data_caption);
		}
		else if($plateform == 'android' ) 
		{
			 $data_caption = base64_decode($comments);
			 $datas = emoji_google_to_unified($data_caption);
		}
		else{
			$datas = $comments;
		}*/
	if($plateform == 'web' ){
		$datas = base64_encode($comments);
	}
	else
		$datas = $comments;
	return $datas;
}
function html_entities ( $string )
{
     return str_replace ( array ( '&amp;' , '&quot;', '&apos;' , '&lt;' , '&gt;' , '&mdash;', '&nbsp;'), array ( '&', '"', "'", '<', '>' ,'--', ' '), $string );
	 // return str_replace ( array ('&', '"', "'", '<', '>' ,'--'), array ('&amp;', '&quot;', '&apos;' , '&lt;' , '&gt;' , '&mdash;'), $string );
}
function logEntry($event,$txt_file){
	$log_dir	=	'C:/wamp/www/intermingl/logs';
	if(!is_dir($log_dir)) {
		mkdir($log_dir,0777);
	}
	$filename = "C:/wamp/www/intermingl/logs".$txt_file;
	$f		  = fopen($filename, 'a' );			// open the log file for writing and put the pointer at the end of the file
	fwrite ($f, $event . chr(13) . chr(10) );
	fclose($f);
}
function getImageMimeType($imagedata)
{
  $imagemimetypes = array( 
    "jpeg" => "FFD8", 
    "png" => "89504E470D0A1A0A", 
    "gif" => "474946",
    "bmp" => "424D"
  );

  foreach ($imagemimetypes as $mime => $hexbytes)
  {
    $bytes = getBytesFromHexString($hexbytes);
    if (substr($imagedata, 0, strlen($bytes)) == $bytes)
      return $mime;
  }

  return NULL;
}
function getBytesFromHexString($hexdata)
{
  for($count = 0; $count < strlen($hexdata); $count+=2)
    $bytes[] = chr(hexdec(substr($hexdata, $count, 2)));

  return implode($bytes);
}
function sendNotificationAWS($message,$EndpointArn,$platform,$badge,$type,$processId,$userId,$unreadCount=0){
	/*global $accesKey;
	global $privateKey;*/
	//error_reporting(E_ALL);
	require_once __DIR__ . '/aws.phar';
	// Create a new Amazon SNS client
	$sns = Aws\Sns\SnsClient::factory(array(
	    'key'    => AWSACCESSKEY,
	    'secret' => AWSSECRETKEY,
	    'region' => 'us-west-2'
	));
	$badge = $badge + 1;
  if($EndpointArn  !=''){
	  try
	  {
	   if($platform == 2){
			$data = array(
			    'TargetArn' => $EndpointArn,
			    'MessageStructure' => 'json',
			    'Message' => json_encode(array(
			        'GCM' => json_encode(array(
			            'data' => array('message' => $message,'badge'=>(integer)$badge ,'sound' => 'default','processId' => $processId,'type' => $type,'userId' => $userId ,'unreadMessage'=>$unreadCount),
			        ))
			    ))
			 );
			$sns->publish($data);
		}
		else{
			if($_SERVER['SERVER_ADDR']=='172.21.4.104')
				$apns = 'APNS_SANDBOX';
		    else
		  		$apns = 'APNS';
			
			$data = array(
				    'TargetArn' => $EndpointArn,
				    'MessageStructure' => 'json',
				    'Message' => json_encode(array(
						$apns => json_encode(array(
				            'aps' => array('alert' => $message,'badge'=> (integer)$badge ,'sound' => 'default', 'processId' => $processId,'type' => $type,'userId' => $userId ,'unreadMessage'=>$unreadCount ),
				        ))
				    ))
				 );
			$sns->publish($data);
			
		}
	  return 1;
	  }
	  catch (Exception $e)
	  {
	  //  echo'<pre>';print_r($data);echo'</pre>';
		//echo"<br>";
		//print($EndpointArn . " - Failed: " . $e->getMessage() . "!\n");
		return 0;
	  }
  }
}
function dateValidation($date){
	$result = 0;
	$date  = explode('/', $date);
	if (count($date) == 3) {
		if($date[0] != '' && $date[1] != '' && $date[2] != ''){
			if($date[2] >= '1983'){
			    if (checkdate($date[0], $date[1], $date[2]))
				    $result = 1;
			   	else 
			       $result = 0;
			}
			else 
		       $result = 0;
		}
		else {
		    $result = 0;
		}
	} 
	else {
	    $result = 0;
	}	
	return $result;
}
function array_sort($array, $on, $order=SORT_ASC)
{
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
            break;
            case SORT_DESC:
                arsort($sortable_array);
            break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}

function logEntryProcess($event,$txt_file){
	if(!is_dir(ABS_PATH.'/logs')) {
		mkdir(ABS_PATH.'/logs',0777);
	}
	$log_dir	=	ABS_PATH.'/logs/'.date('mY');
	if(!is_dir($log_dir)) {
		mkdir($log_dir,0777);
	}
	$filename = ABS_PATH."/logs/".date('mY')."/".$txt_file;
	$f		  = fopen($filename, 'a' );			// open the log file for writing and points to end of file
	fwrite ($f, $event . chr(13) . chr(10) );
	fclose($f);
}

function decodelanguage($string)
{
	require_once("emoji.php");
	$convertedstring	=	emoji_unified_to_html(base64_decode($string));
	preg_match_all('/<span class="emoji (.*?)"><\/span>/', $convertedstring, $matches);
//	echo'<pre>';print_r($convertedstring);echo'</pre>';
//	$convertedstring	=	preg_replace('/[^(\x20-\x7F)]*/','', $convertedstring);
	return $convertedstring;
}

function crypto_rand_secure($min, $max) {
        $range = $max - $min;
        if ($range < 0) return $min; // not so random...
        $log = log($range, 2);
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd >= $range);
        return $min + $rnd;
}

function getPassphrase($length){
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet.= "0123456789";
    for($i=0;$i<$length;$i++){
        $token .= $codeAlphabet[crypto_rand_secure(0,strlen($codeAlphabet))];
    }
    return $token;
}
/********************************************************
  * Function Name: translateLatLngtoAddress
  * Purpose: Gets latitude, longitude from given address
  * Paramters :
  *			$address  - Full Address
  * Output : Returns Boolean value.
  *******************************************************/
function translateAddresstoLatLng($address)
{
	$latLong	=  new stdClass();
	 
	$url="http://maps.google.com/maps/api/geocode/xml?address=".urlencode($address)."&output=xml&sensor=false";
	$page = file_get_contents($url);
	// Parse the returned XML file
	$service_xml = new SimpleXMLElement($page);
	if (isset($service_xml->status)  && $service_xml->status == 'OK' ) {
		$latLong->latitude 	=	$service_xml->result->geometry->location->lat;
		$latLong->longitude =	$service_xml->result->geometry->location->lng;
		return $latLong;
	}
	else  {
		return 1;
	}
}
?>
