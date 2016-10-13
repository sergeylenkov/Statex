document.onkeydown = NavigateThrough;

function NavigateThrough (event)
{
	var code;
	var link = null;	

	if (window.event) event = window.event;
	
	if (event.keyCode) code = event.keyCode;
	else if (event.which) code = event.which;
	
	if ((code == 13) && (event.ctrlKey == true)) 
	{
		document.forms['submit_on_ctrlenter'].submit();
		return(false);
	}		
}
