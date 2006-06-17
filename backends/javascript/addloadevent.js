<!--
function addloadevent(func)
{
    var oldonload = window.onload;
    if (typeof window.onload != 'function')
    {
        window.onload = func;
    }
    else
    {
        window.onload = function()
        {
            oldonload();
            func();
        }
    }
}

function popUp(strURL,strType,strHeight,strWidth)
{
    var strOptions="";
    if (strType=="console")
    {
        strOptions="resizable,height="+strHeight+",width="+strWidth;
    }

    if (strType=="fixed")
    {
        strOptions="status,height="+strHeight+",width="+strWidth;
    }

    if (strType=="elastic") 
    {
        strOptions="toolbar,menubar,scrollbars,resizable,location,height="+strHeight+",width="+strWidth;
    }
    window.open(strURL, 'newWin', strOptions);
}

function external_links()
{
    if (!document.getElementsByTagName)
    {
        return;
    }
    for (var i=0; (anchor=document.getElementsByTagName("a")[i]); i++)
    {
        if (anchor.getAttribute("href") && anchor.getAttribute("rel") == "external")
        {
            anchor.target = "_blank";
        }
    }
}

addloadevent(external_links);
-->
