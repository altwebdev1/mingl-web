<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>A Simple Responsive HTML Email</title>
  <style type="text/css">
  body {margin: 0; padding: 0; min-width: 100%!important;}
  img {height: auto;}
  .content {width: 100%; max-width: 600px;}
  .innerpadding {padding:0 55px 30px;}

  @media only screen and (max-width: 550px), screen and (max-device-width: 550px) {
  body[yahoo] .hide {display: none!important;}
  body[yahoo] .buttonwrapper {background-color: transparent!important;}
  }

  /*@media only screen and (min-device-width: 601px) {
    .content {width: 600px !important;}
    .col425 {width: 425px!important;}
    .col380 {width: 223px!important;}
    }*/

  </style>
</head>

<body yahoo bgcolor="#f6f8f1">
<table width="100%" bgcolor="#f6f8f1" border="0" cellpadding="0" cellspacing="0">
<tr>
  <td>
    <!--[if (gte mso 9)|(IE)]>
      <table width="600" align="center" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td>
    <![endif]-->     
    <table bgcolor="#ffffff" class="content" align="center" cellpadding="0" cellspacing="0" border="0">
      <tr>
		<td style="height:88px;background-color:#ebebeb;border-bottom:1px solid #aaaaaa;"  bgcolor="#ebebeb" align="left">
			<div style="padding-left:20px;"><img src="{SITE_MAIL_PATH}header_mail_logo.png" width="62" height="58" alt="Mingl" align="absmiddle"> <strong style="padding-left:5px;font-size:26px;color:#171717;font-family:trebuchet ms">Mingl</strong></div>
		</td>
	</tr>
    <tr><td height="15"></td></tr>		
	<tr><td  align="center" style="font-size:25px;font-family:trebuchet ms;color:#00518e;font-weight:bold;">Welcome to Mingl</td></tr>
	<tr><td align="center" style="color:#6e6e6e;font-size:16px;line-height:30px;font-family:trebuchet ms">Mingl is the best App to create events.</td></tr>
	<tr><td height="15"></td></tr>
	<tr>
		<td>
			<table border="0" cellpadding="0" cellspacing="0" width="90%" align="center">
			<tr><td style="font-weight:bold;font-size:13px;font-family:arial;color:#444444;text-align:left;" colspan="3">Hi {NAME},</span></td></tr>		
			<tr><td height="10"></td></tr>
			<tr><td style="color:#565656;font-size:12px;font-family:trebuchet ms" colspan="3">Thanks for registering with Mingle. Login to create your own events.</td></tr>
			<tr><td height="35"></td></tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table border="0" cellpadding="0" cellspacing="0" align="center" width="220" >
				<tr><td style="color:#ff6500;font-size:13px;font-family:trebuchet ms" colspan="3" align="center">See your login screen details</td></tr>
				<tr><td height="15"></td></tr>
				<tr>
					<td width="70" align="left" nowrap style="white-space:nowrap;text-align:left;color:#29332d;font-size:12px;font-family:trebuchet ms">Email</td>
					<td width="30" align="left" nowrap style="white-space:nowrap;text-align:left;color:#29332d;font-size:12px;font-family:trebuchet ms">:</td>
					<td width="100" align="left" style="color:#29332d;font-size:14px;font-family:trebuchet ms"><a href="mailto:{EMAIL}" style="color:#29332d;font-size:12px;font-family:arial;" title="{EMAIL}">{EMAIL}</a></td>
				</tr>
				<tr><td height="10"></td></tr>
				<tr>
					<td align="left" nowrap style="white-space:nowrap;text-align:left;color:#29332d;font-size:12px;font-family:trebuchet ms">Password</td>
					<td nowrap style="white-space:nowrap;text-align:left;color:#29332d;font-size:12px;font-family:trebuchet ms">:</td>
					<td style="color:#29332d;font-size:12px;font-family:trebuchet ms">{PASSWORD}</td>
				</tr>
				<tr><td colspan="3" height="50"></td></tr>
			</table>
		</td>
	</tr>
      <tr>
        <td class="innerpadding borderbottom">
          <table width="223" align="left" border="0" cellpadding="0" cellspacing="0">  
            <tr>
              <td height="367" style="padding: 0 40px 20px 0;">
				<img class="fix" src="{SITE_MAIL_PATH}screen_short1.png" width="223" height="367" alt="welcome" align="absmiddle">
				<p style="text-align:center;color:#ff6500;font-size:14px;font-family:trebuchet ms;">Welcome Bruce Nant!</p>
				<p style="text-align:center;color:#565656;font-size:12px;font-family:trebuchet ms;">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industries.</p>
              </td>
            </tr>
          </table>
          <!--[if (gte mso 9)|(IE)]>
            <table width="380" align="left" cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td>
          <![endif]-->
          <table class="col380" align="left" border="0" cellpadding="0" cellspacing="0" style="width: 100%; max-width: 223px;">  
            <tr>
              <td>
                <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                  <tr>
                    <td class="bodycopy">
						<img class="fix" src="{SITE_MAIL_PATH}screen_short2.png" width="223" height="367" alt="welcome" align="absmiddle">
                      	<p style="text-align:center;color:#ff6500;font-size:14px;font-family:trebuchet ms;">Join an Event</p>
						<p style="text-align:center;color:#565656;font-size:12px;font-family:trebuchet ms;">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industries.</p>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
          <!--[if (gte mso 9)|(IE)]>
                </td>
              </tr>
          </table>
          <![endif]-->
        </td>
      </tr>
      
      
      <tr><td style="color:#565656;font-size:12px;font-family:trebuchet ms;padding:10px;background-color:#e9e9e9;border-top:1px solid #a9a9a9" align="center">Copyright &copy; {YEAR}</td></tr>
    </table>
    <!--[if (gte mso 9)|(IE)]>
          </td>
        </tr>
    </table>
    <![endif]-->
    </td>
  </tr>
</table>
</body>
</html>