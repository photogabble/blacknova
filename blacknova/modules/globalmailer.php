<?
if (preg_match("/globalmailer.php/i", $PHP_SELF)) { echo "You can not access this module directly!";die();}

################################################################################
## This script is ©1996 2002 Paul Kirby AKA TheMightyDude                     ##
## And is free to use under condition the copyright notice remains untouched. ##
## Email: admin@initcorp.co.uk                                                ##
## WebSite: http://E-Script.initcorp.co.uk                                    ##
################################################################################
## Global Email Module                                                        ##
################################################################################

################################################################################
## Define the module Information.                                             ##
################################################################################

  $ModuleTAG= "GlobalMailer";    //Used to identify each module.

  if(!defined($ModuleTAG.'_Name')) define($ModuleTAG.'_Name', 'Global Mailer', TRUE);
  if(!defined($ModuleTAG.'_Version')) define($ModuleTAG.'_Version', '0.1.00 ß', TRUE);
  if(!defined($ModuleTAG.'_Author')) define($ModuleTAG.'_Author', 'TheMightyDude', TRUE);
  if(!defined($ModuleTAG.'_Email')) define($ModuleTAG.'_Email', 'mailto:admin@initcorp.co.uk', TRUE);
  if(!defined($ModuleTAG.'_Website')) define($ModuleTAG.'_Website', 'http://e-script.initcorp.co.uk/Modular/globalmailer', TRUE);
  if(!defined($ModuleTAG.'_Info')) define($ModuleTAG.'_Info','Internal Module', TRUE);

?>