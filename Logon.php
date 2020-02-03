<!DOCTYPE html>
<html>
<head>
<title>Email Messages</title>
	<meta charset="utf-8">
	<style>
	container
	{
		position: 	absolute;
	}
	.prompt
	{
		position: 	absolute;
		left:		100px;
	}
	.data
	{
		position: 	absolute;
		left:		300px;
	}
	#errors
	{
		position: 	absolute;
		left:		100px;
		color:		red;
	}
	</style>
</head>
<body>
<h3>Please log on to mail system</h3>
<br><br>

<form onsubmit='doSubmit()' action='Home.php'>
	<span class='prompt'>User name</span>
	<span class='data'><input type='text' id='ipname'></span>
	<br><br>
	<span class='prompt'>Password</span>
	<span class='data'><input type='password' id='pw'></span>
	<br><br>
	<input type='submit'  value='Log on'>
</form>
<div id='errors'></div>

<script>
// ----------------------------------------------
//	Handle Logon button
//
//	Pass user name and password to AJAX
//	AJAX will return OK or an error message
//
//	If OK, return true to the form action
//	Else 
// ----------------------------------------------
function doSubmit()
{	
	var el = document.getElementById('ipname');
	var name = el.value;
	el = document.getElementById('pw');
	var pw = el.value;

	var hAjax;
	var reply = false;
	hAjax = new XMLHttpRequest();
	
	hAjax.onreadystatechange=function()
	{
		if (hAjax.readyState==4 && hAjax.status==200)
		{
		    var httxt = hAjax.responseText;
		    if (httxt == 'OK')						// Logon success
		    {
			    reply = true;						// Allow the form to post
			    return;
	    	}
			var el = document.getElementById('errors');
			alert ('Error :' + httxt + '"');
		    el.innerHTML = httxt;					// Otherwise display the error
		    reply = false;							// ... and don't proceed
		    return;
	    }
    }
    var str = "AjaxLogOn.php?name=" + name + "&pw=" + pw;
	hAjax.open("GET",str,false);
	hAjax.send();
	return reply;
}
</script>
</body>
</html>
