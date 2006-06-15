<table width="73%" border="0" cellspacing="2" cellpadding="2">
  <tr>
    <td height="73" width="27%">
      <img src="templates/{$templateset}/images/nnnhead.png" width="312" height="123" alt="News Network">
    </td>
    <td height="73" width="73%" bgcolor="#000000" valign="bottom" align="right">
      <p>{$l_news_info1}<br>{$l_news_info2}<br>{$l_news_info3}<br>{$l_news_info4}<br>{$l_news_info5}<br></p>
      <p>{$l_news_for} {$today}</p>
    </td>
  </tr>
  <tr>
    <td height="22" width="27%" bgcolor="#00001A">&nbsp;</td>
    <td height="22" width="73%" bgcolor="#00001A" align="right">
      <a href="news.php?startdate={$previousday}">{$l_news_prev}</a> - <a href="news.php?startdate={$nextday}">{$l_news_next}</a>
    </td>
  </tr>

{if $news_array != ""}
{section name=index start=0 loop=$news_array}
  <tr>
    <td bgcolor="#000033" align="center">
      {$news_array[index].headline}
    </td>
    <td bgcolor="#000033">
      <p align="justify">{$news_array[index].newstext}</p>
    </td>
  </tr>
{/section}
{else}
  <tr>
    <td bgcolor="#00001A" align="center">{$l_news_flash}</td>
    <td bgcolor="#00001A" align="right">{$l_news_none}</td>
  </tr>
{/if}
</table>
{if ($session_email)}
<a href="index.php">{$l_global_mlogin}</a>
{else}
<a href="main.php">{$l_global_mmenu}</a>
{/if}
