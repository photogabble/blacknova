<script type="text/javascript" defer="defer" src="backends/javascript/focus.js"></script>
<script type="text/javascript" defer="defer" src="backends/javascript/sha256.js"></script>
<div style="background-image: url( templates/{$templateset}/images/header-stretch.png ); background-repeat: repeat-x;">
<div align="left"><img src="templates/{$templateset}/images/header.png" alt="Blacknova Traders"></div></div>
<h1>{$title}</h1>
<h2>Testing file integrity</h2>

{if $errorchk > 0}

<p><font color=red>The following files DO NOT match the checksums they shipped with, and may be corrupted!</font><br>
<font color=yellow>You may want to try redownloading them.</font></p>
<div id="wrap" style="width:500px;">
<div id="left" style="float:left; width:200px;">
{section name=bad loop=$badlist step=2 start=1}
{$badlist[bad]}<br>
{/section}
</div>
<div id="right" style="float:right; width:200px;">
{section name=bad loop=$badlist step=2 start=2}
{$badlist[bad]}<br>
{/section}
</div>
</div>
<div style="clear:both;"><br>
<p><font color=red>Continuing to install with files that do not match their checksums could cause bugs, errors, or even data loss.</font></p></div>

{else}
<font color="lightgreen">
<p>{$testedcount} files tested and confirmed to match the checksums they shipped with.</p>
<p>You are clear to proceed with install.</p></font>
{/if}

<br><br>
<form action="install.php" method="post">
<br>
<input type="submit" value="{$l_continue}">
<input type="hidden" name="encrypted_password" value="{$encrypted_password}">
<input type="hidden" name="step" value="{$step}">
<input type="hidden" name="total_elapsed" value="{$total_elapsed}">
</form>
<br><br><br><br>

