function id(e){return document.getElementById(e);}
function check()
{  //URL validator
    if (id("link").value.substr(0,4)!='http') { id("link").value='http://'+id("link").value; }
    
    if (valid(id("link").value))
    {
		id("form").submit();
	}
	else
	{
		alert("Check URL");
		id("link").focus();
		return false;
	}	
}
	
function redraw()
{
	id("tableform").style.width=id("tabledata").clientWidth+"px";
	var longlink=id("justaddedlink").innerHTML;
	if (longlink.length>60) longlink=longlink.substring(0,60)+"...";
	id("justaddedlink").innerHTML=longlink;
}

function valid(inpurl) {
    return (validate({website: inpurl}, {website: {url: true}}) === undefined);
}

function deletelink(linkid)
{
	if (window.confirm("Are you sure want to delete this link?"))
	{
		id("id_to_delete").value=linkid;
		id("deletelink").submit();
	}
	else
	{
		return false;
	}
}

function copylink()
{
	var link=id("justaddedshortlink");  
    var range = document.createRange();  
    range.selectNode(link);  
    window.getSelection().addRange(range);  
    
    try
    {  
        var successful = document.execCommand('copy');  
        var msg = successful ? 'Copied!' : 'Not copied';  
        id("copied").innerHTML=msg;  
    }
    catch(err)
    {  
        console.log('Short URL copying error');  
    }  
    

    window.getSelection().removeRange(range);  
}
