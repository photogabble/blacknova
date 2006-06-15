{if $dbprefix != $raw_prefix && $dbprefix !=''}
<!-- Begin news fader -->
<div style="text-align:center;">
<div ID="IEfad1" style="border-width:1px; border-style:solid; width:50%; text-align:center; margin:0 auto;">
  <noscript><a href="news.php" class="dis" target="newWin">{$l_main_noscript}</a></noscript>
</div></div>

<script type="text/javascript" defer="defer">
<!--
var myi = {$seconds_until_update};
var ticks = {$scheduler_ticks};

arURL = new Array({$url_array});
arTXT = new Array({$news_array});
//-->
</script>
<script type="text/javascript" defer="defer" src="backends/javascript/fader_funcs.js"></script>
<!-- End news fader -->
{/if}

{if (!empty($adminnews))}
<!-- Admin news -->
<table border="0" cellpadding="2" cellspacing="0\>
  <tr>
    <td class="footer" nowrap="nowrap" align="center"><strong><font color="red">Important Notice:&nbsp;{$adminnews}&nbsp;</font></strong></td>
  </tr>
</table>
<!-- End Admin news -->
{/if}

<p>
<!-- Legally-required footer -->
<!-- Please note that modification in *ANY* way of the following footer, or the linked copyright page is a violation of   -->
<!-- US Copyright law, and removes all rights you have to utilize the BNT code in its entirety. Please, respect the -->
<!-- developers that have provided the code for you by honoring this legal requirement.                                   -->
<div class="foot">
  <div class="footleft">
    <a href="http://sourceforge.net/projects/blacknova" tabindex="96"> Blacknova Traders </a>
  </div>
{if $gen_time == 999}
  <div class="footcenter"></div>
{/if}
{if $view_source}
  <div class="footcenter">
    <a href="showsource.php?file={$sourcefile}" tabindex="98" onclick="popUp(this.href,'elastic',400,400);return false;" rel="external">View Source</a>
  </div>
{/if}
  <div class="footright">
    <a href="docs/copyright.htm" tabindex="99">Copyright 2000 Ron Harwood and L. Patrick Smallwood</a>
  </div>
</div>
<!-- End Legally-required footer -->

{if $total_elapsed}
<!-- Make galaxy time -->
<div style="text-align:center;">Total elapsed time to make galaxy: {$total_elapsed} seconds</div>
<!-- End Make galaxy time -->
{/if}

<!-- Updates notice -->
{if $seconds_until_update == 10000 && $sched_type != 1}
<div class="center">
  <strong>Updates are not occuring properly</strong>
</div>
{elseif $sched_type == 1}
{else}
<div class="center">
  <strong><span id="myx">{$seconds_until_update}</span></strong> {$l_footer_until_update}
</div>
{/if}
<!-- End Updates notice -->

{if $gen_time != 999}
<div class="center">Page generated in {$gen_time} seconds.</div>
{else}
<div class="center">Timer functions not available.</div>
{/if}

{if (!empty($maindiv))}
</div>
{/if}
</body>
</html>
