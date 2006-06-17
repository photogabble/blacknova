function createRequestObject()
{
    var ro;
    if (window.XMLHttpRequest)
    {
        try
        {
            ro = new XMLHttpRequest();
        } catch(e) {
            ro = false;
        }
    } 
    else if (window.ActiveXObject)
    {
        try {
            ro = new ActiveXObject("Microsoft.XMLHTTP");
        } catch(e) {
            ro = false;
        }
    } 
    return ro;
}

var http = createRequestObject();
    isBusy = false;

function sndReq(action)
{
    if (isBusy)
    {
        http.onreadystatechange = function () {}
	http.abort();
    }

    http.open('get', 'rpc.php?action='+action);
    isBusy = true;
    http.onreadystatechange = handleResponse;
    http.send(null);
}

function handleResponse()
{
    if(http.readyState !=4) return;
    isBusy = false;
    if(http.readyState == 4){
        var response = http.responseText;
        var update = new Array();

        if(response.indexOf('|' != -1)) {
            update = response.split('|');
            if (document.getElementById(update[0]) && document.getElementById(update[0]).innerHTML)
            {
                document.getElementById(update[0]).innerHTML = update[1];
            }
        }
    }
}

var g_intervalID;
g_intervalID = setInterval("sndReq('scroll')", 1000);

window.onload=function WindowLoad(event)
{
    var objControl;
    if (objControl=document.getElementById('scroll'))
    {
//      objControl.scrollTop = objControl.scrollHeight;
        objControl.scrollTop = 10000;
        document.getElementById('chat1').setAttribute("autocomplete","off");
    }
}
