/*if ("https:" == location.protocol)
    var protocolPath  = 'https://';
else
    var protocolPath  = 'http://';

if (window.location.hostname == '172.21.4.104') {
    var  path = protocolPath + '172.21.4.104/broadtags/admin/';
    var actionPath	= protocolPath + '172.21.4.104/broadtags/admin/';
}
else {
	var  path = protocolPath+''+window.location.hostname+'/admin/';
    var actionPath	= protocolPath+''+window.location.hostname+'/admin/';

}*/
$(document).ready(function(){

 $.validator.addMethod("nameRegexp", function(value, element) {
		return this.optional(element) || !(/:|\?|\\|\*|\"|<|>|\||%/g.test(value));
    });
//--------------Login ----------------Start
$("#admin_login_form").validate({
		rules:{
			user_name		:	{ required:true},
			password		:	{ required:true}
		},
		messages:{
			user_name		:	{ required:'Username is required' },
			password		:	{ required:'Password is required'}
		}

	});
//--------------Login----------------End

//--------------Forget Password---------------start
$("#forget_password_form").validate({
		rules:{
			email       :	{ required:true,email:true }
			// email          :	{ required:true,email:true },
          },
		messages:{
			email       :	{ required:'Email address is required',email:'Please enter a valid email address.'}
			//email      :	{ required:'Email Address is required',email:'Please enter a valid email address.'},
		}
	});
//--------------Forget Password----------------End
//--------------Change Password---------------start
$("#change_password_form").validate({
		rules:{			
			old_password        :	{ required:true},
            new_password     	:   { required:true,minlength:5},
            confirm_password    :	{ required:true,minlength:5, equalTo:'#new_password'}
		},
		messages:{
			old_password		:	{ required:'Old password is required' },
			new_password		:	{ required:'New password is required',minlength:'New Password should have atleast 5 characters'},
			confirm_password    :   { required:'Confirm password is required',minlength:'Confirm Password should have atleast 5 characters',equalTo:'Password mismatch' }
		}
	});
//--------------Change Password----------------End
//--------------General Settings---------------start
$("#general_settings_form").validate({
		rules:{			
			email       :	{ required:true,email:true }
		},
		messages:{
			email       :	{ required:'Email address is required',email:'Please enter a valid email address.'}
		}
	});
//--------------Change Password----------------End
//--------------CMS---------------start
$("#cms_form").validate({
		rules:{			
			cms_about       :	{ required:true},		
			cms_privacy     :	{ required:true},		
			cms_terms       :	{ required:true}			
		},
		messages:{
			cms_about       :	{ required:'About is required'},
			cms_privacy     :	{ required:'Privacy policy is required'},
			cms_terms       :	{ required:'Terms and use is required'}
		}
	});
//--------------CMS----------------End
//--------------Add User---------------start
$("#add_user_form").validate({
		rules:{
			firstname               :	{ required:true},
			lastname           :	{ required:true },
			//gender	           :	{ required:true },
			email        	   :	{ required:true,email:true}
			/*empty_user_photo   :	{ required:true},
			empty_cover_photo  :	{ required:true},*/
            /*fb_id     		   :    { required:{  
											depends: function(element){
												if(($("#fb_id").val() == '') && ($("#twitter_id").val() == '')) return true;
												else false;
   												}
											}},
            twitter_id    	   :	{ required:{  
										depends: function(element){
        										if(($("#fb_id").val() == '') && ($("#twitter_id").val() == '') ) return true;
												else false;
  												}
										}},*/
		},
		messages:{
			firstname       	        :	{ required:'First name is required'},
			lastname          	:	{ required:'Last name is required'},
			email				:	{ required:'Email is required' },
			//gender				:	{ required:'Gender is required' },
			/*empty_user_photo	:	{ required:'User Image is required'},
			empty_cover_photo	:	{ required:'Cover Image is required'},
			fb_id				:	{ required:'Facebook Id is required' },
			twitter_id    		:   { required:'Twitter Id is required' }*/
		}
	});
//--------------Add user----------------End
//--------------Add Hash Tag ---------------start
$("#add_hashtag_form").validate({
		rules:{
			hash_tag_name       :	{ required:true},			
		},
		messages:{
			hash_tag_name       :	{ required:'Hashtag is required'},			
		}
	});
//--------------Add Hash Tag----------------End
$("#add_tag_form").validate({
		rules:{
			tagname       :	{ required:true},			
		},
		messages:{
			tagname       :	{ required:'&nbsp;&nbsp;Tag is required'},			
		}
	});
//--------------Add Hash Tag----------------End

//--------------Add Job Title---------------start
$("#add_title_form").validate({
		rules:{
			job_title_name         :	{ required:true},			
		},
		messages:{
			job_title_name       	:	{ required:'Job title is required'},			
		}
	});
//--------------Add Job Title----------------End
//--------------Add Company---------------start
$("#add_company_form").validate({
		rules:{
			company_name         :	{ required:true},			
			empty_company_photo   :	{ required:true},           
		},
		messages:{
			company_name       	:	{ required:'Company name is required'},
			empty_company_photo	:	{ required:'Company logo is required'},			
		}
	});
//--------------Add Company----------------End
//--------------Add Service---------------start
$("#add_service_form").validate({
		rules:{
			process			:	{ required:true},			
			service_path	:	{ required:true},       
			method			:	{ required:true},
			module_name		:	{ required:true},
			sample_data		:	{ required:true},
			output_param	:	{ required:true}
		},
		messages:{
			process       	:	{ required:'Purpose is required'},
			service_path	:	{ required:'Endpoint is required'},
			method			:	{ required:'Method is required'},
			module_name		:	{ required:'Module Name is required'},
			sample_data		:	{ required:'Field Name is required'},
			output_param	:	{ required:'Output param is required'}
		}
});
//--------------Add Service----------------End
//--------------Add hashtag post---------------start
$("#add_post_form").validate({
		rules:{
			user_id					:	{ required:true},	
			post_type				:	{ required:true},
			
		},
		messages:{
			user_id					:	{ required:'Username is required'},
			post_type				:	{ required:'Post type is required'},
		}
	});
//--------------Add hashtag post----------------End
//--------------Add hashtag post---------------start
$("#post_like_comment_form").validate({
		rules:{
			user_id					:	{ required:true},				
		},
		messages:{
			user_id					:	{ required:'Username is required'},
		}
	});
//--------------Add hashtag post----------------End


});
//-------------- Add goal ----------------End
$("#add_goal_form").validate({
		rules:{
			add_goal       :	{ required:true},			
		},
		messages:{
			add_goal       :	{ required:'Goal is required'},			
		}
	});
//-------------- Add goal ----------------End
//-------------- Interest  ----------------End
$("#add_interest_form").validate({
		rules:{
			add_interest       :	{ required:true},			
		},
		messages:{
			add_interest       :	{ required:'Interest is required'},			
		}
	});
//-------------- Interest  ----------------End
//-------------- add event ----------------End
$("#add_event_form").validate({
		rules:{
			eventname      		:	{ required:true},			
			description   		:	{ required:true},
			location       		:	{ required:true},
			startdate       	:	{ required:true},
			//enddate				: 	{ required:true,greaterThan: "#startdate" },
			enddate       		:	{ required:true},
		},
		messages:{
			eventname       	:	{ required:'Event Name is required'},
			description       	:	{ required:'Description is required'},
			location       		:	{ required:'Location is required'},
			startdate       	:	{ required:'Event start date is required'},
			enddate       		:	{ required:'Event end Date is required'},
			//enddate       		:	{ required:'Event end Date is required',greaterThan:'EndDate Should be greater than StartDate'},
		}
	});

//-------------- add event ----------------End
