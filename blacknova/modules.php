<?
if (preg_match("/modules.php/i", $PHP_SELF)) { echo "You can not access this file directly!";die();}

################################################################################
## This script is ©1996 2002 Paul Kirby AKA TheMightyDude                     ##
## And is free to use under condition the copyright notice remains untouched. ##
## Email: webmaster@initcorp.co.uk                                            ##
## WebSite: http://E-Script.initcorp.co.uk                                    ##
################################################################################

################################################################################
## Modules setup variables                                                    ##
################################################################################
## To add modules to bnt you need to add the following defined                ##
## strings following to your modules.                                         ##
################################################################################
## $ModuleTAG= "Module TAG Name";                                             ##
## define($ModuleTAG.'_Name', 'Name of Module', TRUE);                        ##
## define($ModuleTAG.'_Version', 'Module Version', TRUE);                     ##
## define($ModuleTAG.'_Author', 'Name of Author', TRUE);                      ##
## define($ModuleTAG.'_Email', 'Email of Author', TRUE);                      ##
## define($ModuleTAG.'_Website', 'Website for Module Info', TRUE);            ##
## define($ModuleTAG.'_Info','Aditional Info on Module', TRUE);               ##
################################################################################
## $ModuleTAG.'_Name' = (22 char MAX)                                         ##
## $ModuleTAG.'_Version' = (13 char MAX)                                      ##
## $ModuleTAG.'_Author' = (13 char MAX)                                       ##
## $ModuleTAG.'_Info' = (14 char MAX)                                         ##
################################################################################

if(!defined(ModularVersion)) define(ModularVersion, '0.01 ß', TRUE);

#Modules variables
#Example#
#Always seperate fields with /t
#if ($Enable_'Module Name'Module) $modules['Module Name']="Module Filename/tModule Tag";

if ($Enable_GlobalMailerModule) $modules['GMM']="globalmailer.php/tGlobalMailer";
if ($Enable_EmailLoggerModule) $modules['ELM']="emaillogger.php/tEmailLogger";

$modcnt = count($modules);
if ($modcnt >0)
{
  foreach ($modules as $modu => $moduleinfo)
  {
    list ($modfile,$modtag) = split ('/t', $moduleinfo,2); 
    include("modules/$modfile");
  }
}

?>
