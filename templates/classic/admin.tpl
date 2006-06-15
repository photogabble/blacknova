<div style="background-image: url( templates/{$templateset}/images/header-stretch.png ); background-repeat: repeat-x;">
  <div align="left">
    <img src="templates/{$templateset}/images/header.png" alt="Blacknova Traders">
  </div>
</div>

<div style="color:white">
<h1>{$title}</h1>

{if $admin_menu == ''}
Welcome to the Administration module for {$l_project_name}<br><br>
Select a function from the list below:<br><br>
<ul id="adminlist">
  <li id="active"><a href="{$adminurl}?menu=logview">Log Viewer</a></li>
  <li><a href="{$adminurl}?menu=an">Admin News</a></li>
  <li><a href="{$adminurl}?menu=emailview">Email Log Viewer</a></li> 
  <li><a href="{$adminurl}?menu=iplog">IP Log viewer</a></li>
  <li><a href="{$adminurl}?menu=perfmon">Performance monitor</a></li>
  <li><a href="{$adminurl}?menu=stats">Statistics</a></li>
  {if $playerinfo_acl >= 255}
<li><a href="{$adminurl}?menu=globmsg">Global Mailer</a></li> 
  <li><a href="{$adminurl}?menu=useredit">User Editor</a></li>
  <li><a href="{$adminurl}?menu=planedit">Planet Editor</a></li>
  <li><a href="{$adminurl}?menu=ipedit">IP Bans Editor</a></li>
  <li><a href="{$adminurl}?menu=setedit">Edit Game Settings</a></li>
  <li><a href="{$adminurl}?menu=sectedit">Sector Editor</a></li>
  <li><a href="{$adminurl}?menu=zoneedit">Zone Editor</a></li>
  <li><a href="{$adminurl}?menu=memberlist">Memberlist editor</a></li>
  <li><a href="{$adminurl}?menu=ai_instruct">{$ai_name} Instructions</a></li>
  <li><a href="{$adminurl}?menu=drop_ai">Drop and Re-Install {$ai_name} Database</a></li>
  <li><a href="{$adminurl}?menu=new_ai">Create A New {$ai_name} Character</a></li>
  <li><a href="{$adminurl}?menu=clear_ai_log">Clear All {$ai_name} Log Files</a></li>
  <li><a href="{$adminurl}?menu=ai_edit">{$ai_name} Character Editor</a></li>
  <li><a href="{$adminurl}?menu=linkedit">Link Editor</a></li>
  <li><a href="{$adminurl}?menu=bigbang">Make Galaxy</a></li>
  <li><a href="{$adminurl}?menu=poof">Universe Reset</a></li>
{/if}
</ul>
{/if}

{if $button_main}
<br>
<form name="bntforma" action="admin.php" method="post" onsubmit="document.bntforma.submit_button.disabled=true;">
  <input name="submit_button" type="submit" value="Return to admin menu">
</form>

<form name="bntformb" action="main.php" method="post" onsubmit="document.bntformb.submit_button.disabled=true;">
  <input name="submit_button" type="submit" value="Return to main menu">
</form>
{/if}

</div><br>
