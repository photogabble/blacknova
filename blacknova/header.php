<?
// header.php
//
// outputs the game's standard beginning-of-document stuff
// Control variables:
//   $title - inserted into the <title> tag
//   $stylesheet - if you need additional stylesheets, set this to the literal html to insert into the
//                 document. try to use <link> instead of <style> tags
//   $interface - generally pulled from the users cookie, currently determines
//                if the user wants dhtml or not
//   $no_body - set to TRUE if you want to output the <body> tag yourself

header("Cache-Control: no-cache, must-revalidate");

// Comment out the line below if you are running php 4.0.6 or earlier
ob_start("ob_gzhandler");

$html_header = <<<EOD
<!doctype html public "-//w3c//dtd html 3.2//en">
<html>
<head>
  <meta http-equiv="Pragma" content="no-cache">
  <title>{$title}</title>
  <link rel="stylesheet" href="default.css" type="text/css">
  [stylesheet]
  {$stylesheet}
</head>
[body]
EOD;

$html_stylesheet_main = '<link rel="stylesheet" href="main.css" type="text/css">';

$html_body_main = <<<EOD
<body background="images/bgoutspace1.gif" bgcolor="#000000" text="#c0c0c0" link="#00ff00" vlink="#00ff00" alink="#ff0000">
EOD;

$html_body_default = <<<EOD
<body background="" bgcolor="#000000" text="#c0c0c0" link="#00ff00" vlink="#808080" alink="#ff0000">
EOD;

if(isset($interface)) {
  switch($interface) {
    case 'maintext.php':
      $html_header = str_replace('[stylesheet]','',$html_header);
      break;
    case 'main.php':
    default:
      $html_header = str_replace('[stylesheet]',$html_stylesheet_main,$html_header);
  }
} else {
  $interface = 'main.php';
  $html_header = str_replace('[stylesheet]',$html_stylesheet_main,$html_header);
}

if(empty($no_body)) {
  switch($interface) {
    case 'main.php':
      $html_header = str_replace('[body]',$html_body_main,$html_header);
      break;
    default:
      $html_header = str_replace('[body]',$html_body_default,$html_header);
  }
} else {
  $html_header = str_replace('[body]','',$html_header);
}

echo $html_header;

?>
