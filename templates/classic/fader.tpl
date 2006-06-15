<div style="text-align:center;">
<div ID="IEfad1" style="border-width:1px; border-style:solid; width:50%; text-align:center; margin:0 auto;">
  <noscript><a target="_new" href="news.php" class="headlines">{$l_main_noscript}</a></noscript>
</div></div>

<!-- Cant be defer, due to IE - triggers artopnews being empty. Should be fixed when we move to a new fader. -->
<script type="text/javascript" src="backends/javascript/fader_funcs.js">
</script>

<script type="text/javascript" defer="defer">
<!--
{$url_array}
{$news_array}

{literal}
for (i=0;i<arTXT.length;i++)
{
    arTopNews[arTopNews.length] = arTXT[i];
    arTopNews[arTopNews.length] = arURL[i];
}
{/literal}
//-->
</script>
