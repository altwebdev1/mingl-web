if ("https:" == location.protocol)
    var protocolPath  = 'https://';
else
    var protocolPath  = 'http://';

if (window.location.hostname == '172.21.4.104') {
    var  path       = protocolPath + '172.21.4.104/haystack/admin/';
    var actionPath	= protocolPath + '172.21.4.104/haystack/admin/';
}
/*else {
	var  path = protocolPath+''+window.location.hostname+'/';
    var actionPath	= protocolPath+''+window.location.hostname+'/';

}*/


function addRow(ref)
{
	var field_name 	= "question_clone_";
	var count 		= 0;
	var node 		= $(ref).closest("tr");
	var empty		=	0;
	var empty1		=	0;
	$(ref).closest("table").find("tr").each(function() {
		var text 	=	$(this).find("textarea").eq(0).val();
		var text1 	=	$(this).find("input").eq(0).val();
		console.log('text'+text);
		console.log('text1'+text1);
		if(text == "")
			empty = 1;
		if(text1 == "")
			empty1 = 1;
	});
	if(empty == 1 && empty1 == 1)	{	
	//	alert("Please provide Question and Profile Title to add new row...");
		alert("Please fill the row to add new row");
		return false;	
	}
	else if(empty == 0	&&	empty1 == 0)
	{
		var length		= node.attr("clone");
		var tabindex	= $("#method").attr("tabindex");
		var clonedRow 	= node.clone(true);
		clonedRow.insertAfter(node);
		if(length >= 0) 	{
			count = (+length)+1;
			clonedRow.attr("clone",count);
			clonedRow.find("input").val("");
			clonedRow.find("select").val(0);
			clonedRow.find("textarea").text("");
			settabindex(tabindex);
		}
	}
	else if(empty1 == 0)	{	
		alert("Please provide Question to add new row");
		return false;	
	}
	else if(empty == 0)	{	
		alert("Please provide Response to add new row");
		return false;	
	}
}
function addRow1()
{	
	if( $('#category').val()==0)	{	alert('Select Category');	return false; }
	else if($('textarea#question').val().length<=0){
	//	alert("Please fill the row to add new row");
		alert("Please provide Question and Profile Title to add new row...");
		return false;
	}
	else  return true;
}

function delRow(ref)
{	
	var count	=	$("#inputParam tr").length;
	if(count > 1)	
	{
		var bool	=	confirm("Are you sure to delete ?");
		if(bool)
			$(ref).closest("tr").remove();
	}
	else
		alert("Atleast one row is required");
}
$(document).ready(function() {
	var tabindex = $("#method").attr("tabindex");
	settabindex(+tabindex+1);
	showHideInputParam();
	if ($('#methodparam').is(":checked")){
		//$(".inputParamDefault").show();
		$(".inputParamMultiple").hide();   		
		$(".jsonInput").show();
	}
	//For Method Change Event
	$("#method").change(function() {
		showHideInputParam();
	});
	
	$("#methodparam").change(function() {
		showHideInputMethodParam();
	});
	
});

function showHideInputParam() {
	var value = $("#method").val();
		$(".jsonInput").hide();
		if(value == "GET" || value == "POST") {
			$(".inputParamDefault").hide();
			$(".inputParamMultiple").show();
			$(".inputMethodParamMultiple").show();
		}
		else {
			$(".inputParamDefault").show();
			$(".inputParamMultiple").hide();
			$(".inputMethodParamMultiple").hide();
		}
}
function showHideInputMethodParam() {

	if ($('#methodparam').is(":checked")){
		$(".inputParamMultiple").hide();   		
		$(".jsonInput").show();
	}
	else {
		$(".jsonInput").hide();
		$(".inputParamMultiple").show();

	}
}
function settabindex(index) {
	$("#inputParam").find("tr").not(":eq(0)").each(function() {
		$(this).find("input").eq(0).attr("tabindex",index++);
		$(this).find("input").eq(1).attr("tabindex",index++);
		$(this).find("select").eq(0).attr("tabindex",index++);
		$(this).find("textarea").eq(0).attr("tabindex",index++);
		$("#output_param").attr("tabindex",index++);
		$("#Save,#Add").attr("tabindex",index++);
		$("#Back").attr("tabindex",index++);
		
	});
}

function ajaxAdminFileUploadProcess(process_pram)
{	
	var loadingIMG  =  '<span class="photo_load load_upimg"><img  src="'+path+'webresources/images/fetch_loader.gif" width="24" height="24" alt=""></span>';	
    $(loadingIMG).insertAfter($("#"+process_pram+"_img"));
    $("#"+process_pram+"_img").hide();	
	var hiddenVal = $("#empty_"+process_pram).val();
    $.ajaxFileUpload
    ({
        url:actionPath+'models/DoAjaxAdminFileUpload.php',
        secureuri:false,
        fileElementId:process_pram,
        dataType: 'json',
        data:{
			
            process:process_pram
        },
		success: function (data)
        {
           	if(typeof(data.error) != 'undefined')
            {
			    if(data.error != '')
                {
                    alert(data.error);
					if($('#'+process_pram+'_upload').val() == '')
						$("#empty_"+process_pram).val(hiddenVal);
					
					 $("#empty_"+process_pram).val(hiddenVal);
					
                }else
                {
					if(hiddenVal=='') {
						$("#empty_"+process_pram).val(1);
					}
					var result	=	data.msg.split("####");
					if(process_pram == 'cover_photo'){
						var img	='<img  src="'+path+'/webresources/uploads/temp/'+result[0] +'.'+ result[1]+'?rnd='+Math.random()+'" width="200" height="75" />\n\
                                        <input type="hidden" name="'+process_pram+'_upload" id="'+process_pram+'_upload" value="'+result[0] +'.'+ result[1]+'" />';
					}
					else{
						var img	='<img  src="'+path+'/webresources/uploads/temp/'+result[0] +'.'+ result[1]+'?rnd='+Math.random()+'" width="75" height="75" />\n\
                                        <input type="hidden" name="'+process_pram+'_upload" id="'+process_pram+'_upload" value="'+result[0] +'.'+ result[1]+'" />';
					}
					
					$("#"+process_pram+"_img").html(img);
	                $("#no_"+process_pram).remove();
				}
                $(".photo_load").remove();
                $("#"+process_pram+"_img").show();
            }
        },
        error: function (data, status, e)
        {
           alert(e);
        }
    });

    return false;
}
function deleteAll(del_name){
	var flag       = 0;
	var company_flag  = 0;
	$("input[name='checkdelete[]']").each(function(){		
		if($(this).attr('checked')){
			flag = 1;	
			if(del_name == 'hashtag' || del_name == 'Users'	||	del_name == 'company' || del_name == 'role'){
				if($(this).attr('countFlag') > 0)				
					company_flag = 1;
			}
		}
	});
	if(flag == 0){
		alert('Select atleast a single record');
		return false;
	}	
	if(company_flag == 1){
		if(del_name == 'company')
			alert('Sorry! you can not delete this company, since it is used by some other user\'s.');
		if(del_name == 'role')
			alert('Sorry! you can not delete this Role, since this Role paly by some other user\'s.');
		if(del_name == 'Users')
			alert('Sorry! you can not delete this selected users');
		return false;
	}
	else if(company_flag == 0 && flag == 1 ){
		if(confirm('Are you sure to delete?'))
			return true;
		else
			return false;
	}
}
function setOrdering(ordering_value,company_id){
		//alert('order--------'+ordering_value+'-------company--------'+company_id)
		$.ajax({
	        type: "GET",
	        url: actionPath+"models/AjaxAction.php",
	        data: 'action=SET_ORDERING&orderValue='+ordering_value+'&companyId='+company_id,
	        success: function (result){
				//alert(result);
	        }			
	    });
}
function setOrderingWebService(ordering_value,service_id){
		$.ajax({
	        type: "GET",
	        url: actionPath+"models/AjaxAction.php",
	        data: 'action=SET_ORDERING_WEBSERVICE&orderValue='+ordering_value+'&serviceId='+service_id,
	        success: function (result){
				//alert("--"+result);
				if(result == 1){
					alert('This Order already assigned for some other Service');
					return false;
				}
	        }			
	    });
}
sendNotification = function(frmname)
{
	flag=0;
	var message = $('#message').val();
	if(message == ''){
		alert('Enter the message');
		return false;
	}
	else if(frmname.user_id.length>1)
	{
		for (var i = 0; i < frmname.user_id.length; i++)
		{
		  if(frmname.user_id[i].selected){
				flag = 1;
				break;
		  }
		}
	}
	else if (frmname.user_id.selected) {
		flag = 1;
	}
	if(flag==0) {
		alert('Please select atleast a user to send notification');
		return false;
	}
	if(flag==1){
		$('#message_hidden').val(message);
		if(confirm('Are you sure to send notification?')) {
			frmname.submit();
			// parent.$.colorbox.close();
		}
			  
	}
}
function loadMessage(to_user_id,from_user_id){
	if($('#filter_user_name').length) {
		$('#filter_user_name option[value='+to_user_id+']').attr('selected',true);
	}
    $('.users').removeClass('sel');
    $('.user_'+to_user_id).addClass('sel');
    $('.loader').show();
	$.ajax({
        type: "GET",
        url: actionPath+"/models/AjaxAction.php?action=LOAD_CONVERSATION",
        data: 'FromUserId='+from_user_id+'&ToUserId='+to_user_id,
        success: function(data) {
           $('.scroll').html(data);
			$('.scroll_content').animate({
                   scrollTop: $('.scroll_content')[0].scrollHeight
               }, 800);
			$('.loader').hide();
			//$('.user_'+to_user_id).removeClass('unread');
        }
    });
}

function getShareLikeCommentList(post_id,type,pageName){
	$.ajax({
        type: "GET",
        url: actionPath+"/models/AjaxAction.php?action=GET_LIKE_SHARE_COMMENT",
        data: 'PostId='+post_id+'&Type='+type+'&PageName='+pageName,
        success: function(data) {			
			 		  
			 //$('#user_list').addClass('user_list_container');
			 if(type == '1'){
			 	$('#user_like').html(data);	
			 	$('#user_like').addClass('user_list_container');
			 	$('#user_like').slideToggle('slow');
				$('#user_comment').hide();
				$('#user_share').hide();
				$('#user_repost').hide();
			 }
			 if(type == '3'){
			 	$('#user_comment').html(data);	
			 	$('#user_comment').addClass('user_list_container');
			 	$('#user_comment').slideToggle('slow');
				$('#user_like').hide();
				$('#user_share').hide();
				$('#user_repost').hide();
			 }
			 if(type == '2'){
			 	$('#user_share').html(data);	
			 	$('#user_share').addClass('user_list_container');
			 	$('#user_share').slideToggle('slow');
				$('#user_like').hide();
				$('#user_comment').hide();
				$('#user_repost').hide();
			 }
			 if(type == '4'){
			 	$('#user_repost').html(data);	
			 	$('#user_repost').addClass('user_list_container');
			 	$('#user_repost').slideToggle('slow');
				$('#user_like').hide();
				$('#user_share').hide();
				$('#user_comment').hide();
			 }
		}
    });

}
function getShareLikeCommentPaging(post_id,type,pageValue,pageName){
	$.ajax({
        type: "GET",
        url: actionPath+"/models/AjaxAction.php?action=GET_LIKE_SHARE_COMMENT",
        data: 'PostId='+post_id+'&Type='+type+'&Page='+pageValue+'&PageName='+pageName,
        success: function(data) {			 	
			 if(type == '1'){
			 	$('#user_like').html(data);	
			 	$('#user_like').addClass('user_list_container');
			 	$('#user_comment').hide();
				$('#user_share').hide();
				$('#user_repost').hide();
			 }
			 if(type == '3'){
			 	$('#user_comment').html(data);	
			 	$('#user_comment').addClass('user_list_container');
			 	$('#user_like').hide();
				$('#user_share').hide();
				$('#user_repost').hide();
			 }
			 if(type == '2'){
			 	$('#user_share').html(data);	
			 	$('#user_share').addClass('user_list_container');
			 	$('#user_like').hide();
				$('#user_comment').hide();
				$('#user_repost').hide();
			 }
			 if(type == '4'){
			 	$('#user_repost').html(data);	
			 	$('#user_repost').addClass('user_list_container');
			 	$('#user_like').hide();
				$('#user_share').hide();
				$('#user_comment').hide();
			 }	
        }
    });

}

function insertPostDetail(post_type){
$("#text_content,#image_content,#video_content,#video_thumb").hide();
	if(post_type == '1')
		$("#text_content").fadeIn(700);
	else if(post_type == '2')
		$("#image_content").fadeIn(700);
	else if(post_type == '3'){
		$("#video_content").fadeIn(700);
		$("#video_thumb").fadeIn(700);
	}

}

function uploadVideoFile(fileName,process_pram)
{	
	$('#process_pram').html(fileName);
	var fileInput = document.getElementById(process_pram)	
	var image_type = (fileInput.files[0].type).split('/');
	
	if(image_type[0] != 'video'){
		alert('Upload only video files')
		$('#'+process_pram).val('');			
		$('#'+process_pram).html('No file selected');	
	}	
}
function uploadImageFile(fileName,process_pram)
{	
	$('#process_pram').html(fileName);
	var fileInput = document.getElementById(process_pram)	
	var image_type = (fileInput.files[0].type).split('/');
	
	if(image_type[0] != 'image'){
		alert('Upload only image files')
		$('#'+process_pram).val('');			
		$('#'+process_pram).html('No file selected');	
	}	
}

