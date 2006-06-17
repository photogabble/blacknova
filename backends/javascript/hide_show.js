<!--
function setupPanes(containerId, defaultTabId)
{
    // go through the DOM, find each tab-container
    // set up the panes array with named panes
    // find the max height, set tab-panes to that height
    panes[containerId] = new Array();
    var maxHeight = 0; var maxWidth = 0;
    var container = document.getElementById(containerId);
    var paneContainer = container.getElementsByTagName("div")[0];
    var paneList = paneContainer.childNodes;
    for (var i=0; i < paneList.length; i++ )
    {
        var pane = paneList[i];
        if (pane.nodeType != 1) 
        {
            continue;
        }
        if (pane.offsetHeight > maxHeight) 
        {
            maxHeight = pane.offsetHeight;
        }
        if (pane.offsetWidth  > maxWidth ) 
        {
            maxWidth  = pane.offsetWidth;
        }
        panes[containerId][pane.id] = pane;
        pane.style.display = "none";
    }

    paneContainer.style.height = maxHeight + "px";
    paneContainer.style.width  = maxWidth + "px";
    document.getElementById(defaultTabId).onclick();
}

function showPane(paneId, activeTab)
{
    // make tab active class
    // hide other panes (siblings)
    // make pane visible

    for (var con in panes)
    {
        activeTab.blur();
        activeTab.className = "tab-active";
        if (panes[con][paneId] !== null)
        { 
            // tab and pane are members of this container
            var pane = document.getElementById(paneId);
            pane.style.display = "block";
            var container = document.getElementById(con);
            var tabs = container.getElementsByTagName("ul")[0];
            var tabs2 = container.getElementsByTagName("ul")[1];
            var tabs3 = container.getElementsByTagName("ul")[2];
            var tabs4 = container.getElementsByTagName("ul")[3];
            var tabs5 = container.getElementsByTagName("ul")[4];

            var tabList = tabs.getElementsByTagName("a");
            var tabList2 = tabs2.getElementsByTagName("a");
            var tabList3 = tabs2.getElementsByTagName("a");
            var tabList4 = tabs2.getElementsByTagName("a");
            var tabList5 = tabs2.getElementsByTagName("a");

            for (var i=0; i<tabList.length; i++ )
            {
                var tab = tabList[i];
                if (tab != activeTab)
                {
                    tab.className = "tab-disabled";
                }
            }

            for (var i=0; i<tabList2.length; i++ )
            {
                var tab = tabList2[i];
                if (tab != activeTab)
                {
                    tab.className = "tab-disabled";
                }
            }

            for (var i=0; i<tabList3.length; i++ )
            {
                var tab = tabList3[i];
                if (tab != activeTab)
                {
                    tab.className = "tab-disabled";
                }
            }

            for (var i=0; i<tabList4.length; i++ )
            {
                var tab = tabList4[i];
                if (tab != activeTab)
                {
                    tab.className = "tab-disabled";
                }
            }

            for (var i=0; i<tabList5.length; i++ )
            {
                var tab = tabList5[i];
                if (tab != activeTab)
                {
                    tab.className = "tab-disabled";
                }
            }

            for (var i in panes[con])
            {
                var pane = panes[con][i];
                if (pane === undefined)
                {
                    continue;
                }
                if (pane.id == paneId)
                {
                    continue;
                }
                pane.style.display = "none";
            }
        }
    }
    return false;
}
-->
