function rmyx()
{
    if (myi != 10000)
    {
        myi = myi - 1;
        if (myi <= 0)
        {
            myi = ticks * 60;
        }

        document.getElementById("myx").innerHTML = myi;
        setTimeout("rmyx();",1000);
    }
}

function FDRredo()
{
    if (innerWidth==origWidth && innerHeight==origHeight)
    {
        return;
    }
    window.location.reload();
}

function FDRcountLoads()
{
    TopnewsCount = 0;
    TopLoopCount = 0;

    FDRdo();
    blendTimer = setInterval("FDRdo()",FDRblendInt*1000);
}

function FDRdo() 
{
    if (FDRfinite && TopLoopCount>=FDRmaxLoops) 
    {
        FDRend();
        return;
    }

    FDRfade();

    if (TopnewsCount >= arTopNews.length) 
    {
        TopnewsCount = 0;
        if (FDRfinite)
        {
            TopLoopCount++;
        }
    }
}

function FDRfade()
{
    if (TopLoopCount < FDRmaxLoops) 
    {
        TopnewsStr = "";
        for (var i=0;i<1;i++)
        {
            if (TopnewsCount < arTopNews.length) 
            {
                TopnewsStr += "<a class=\"dis\" onkeypress=\"this.onclick()\" onclick=\"popUp(this.href,'elastic',400,400);return false;\" ";
                TopnewsStr +=  "target=\"newWin\" ";
                TopnewsStr += "href='" + TopPrefix + arTopNews[TopnewsCount+1] + "'>";
                TopnewsStr += arTopNews[TopnewsCount] + "</" + "a>";
                TopnewsCount += 2;
            }
        }

        document.getElementById('IEfad1').innerHTML = TopnewsStr;
    }
}

function FDRend()
{
    clearInterval(blendTimer);
    if (FDRendWithFirst) 
    {
        TopnewsCount = 0;
        TopLoopCount = 0;
        FDRfade();
    }
}

FDRblendInt = 5; // seconds between flips
FDRmaxLoops = 20; // max number of loops (full set of headlines each loop)
FDRendWithFirst = true;

FDRfinite = (FDRmaxLoops > 0);
blendTimer = null;
arTopNews = [];

document.getElementById('IEfad1').style.pixelHeight = document.getElementById('IEfad1').offsetHeight;

TopPrefix = ' ';
setTimeout("rmyx();",1000);

for (var i=0, zz=arTXT.length; i<zz; i++)
{
    arTopNews[arTopNews.length] = arTXT[i];
    arTopNews[arTopNews.length] = arURL[i];
}
addloadevent(FDRcountLoads);
