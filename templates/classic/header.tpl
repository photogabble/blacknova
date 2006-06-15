<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="{$local_lang}">
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="Description" content="A free online game - Open source, web game, with multiplayer space exploration">
  <meta name="Keywords" content="Free, online, game, Open source, web game, multiplayer, space, exploration, blacknova, traders">
  <meta name="Rating" content="General">
  <meta http-equiv="Content-Language" content="{$local_lang}">
  <meta http-equiv="imagetoolbar" content="no">
  <meta http-equiv="imagetoolbar" content="false">
  <link rel="stylesheet" href="{$style_sheet_file}" type="text/css">
<!--  <link rel="stylesheet" href="{$style_sheet_file}">-->
  <link rel="icon" href="templates/{$templateset}/images/favicon.ico" type="image/vnd.microsoft.icon">
  <title>{$title}</title>
 </head>
{if $no_body == 1}
<body class="index" id="bntbody">
{elseif $no_body == 2}
<body bgcolor="#666666" text="#FFFFFF" link="#00FF00" vlink="#00FF00" alink="#FF0000" id="bntbody">
{else}
<body style="background-image: url(templates/{$templateset}/images/bgoutspace1.png);" id="bntbody">
{/if}
<!-- This pops us out of frames. -->
<script type="text/javascript" defer="defer">
    if(self != top) top.location = location;
</script>
<!-- addloadevent cant be defer due to IE.. sigh -->
<script type="text/javascript" defer="defer" src="backends/javascript/addloadevent.js"></script>
