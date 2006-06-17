function validate()
{
    if (document.forms[0]._ADODB_SESSION_CONNECT.value == "")
    {
        alert("Database name cannot be empty!");
        return false;
    }

    if (document.forms[0]._adminpass.value == "")
    {
        alert("Admin password cannot be empty!");
        return false;
    }

    if (document.forms[0]._adminpass.value != document.forms[0].adminpass2.value)
    {
        alert("Admin passwords don't match!");
        return false;
    }

    if (document.forms[0]._ADODB_CRYPT_KEY.value == "")
    {
        alert("Session crypt key cannot be empty!");
        return false;
    }

    if (document.forms[0]._mailer_type.value == "")
    {
        alert("Mailer type cannot be empty!");
        return false;
    }

    if (document.forms[0]._server_type.value == "")
    {
        alert("Server type cannot be empty!");
        return false;
    }

    document.forms[0].submit();
}

function mytip(tooltip_id)
{
    messages=new Array()
    messages[0]="Type of the SQL database. This can be anything supported by ADOdb. <p>NOTE: only mysql work as of right now, due to SQL compat code."
    messages[1]="Name of the SQL database"
    messages[2]="Username and password to connect to the database"
    messages[3]="Hostname and port of the database server. These are defaults, you normally won't have to change them.<p>Note : if you do not know the port, leave the port field empty for default. Ex, MySQL default is 3306"
    messages[5]="Table prefix for the database. If you want to run more than one game on the same database, or if the current table names conflict with tables you already have in your database, you will need to change this"
    messages[6]="Path on the filesystem where the game files will reside"
    messages[7]="This is the trailing part of the URL, that is not part of the domain. If you enter www.example.com/game to access the game, you would make the line like so (without quotes):  \'/game\'. If you do not need to specify a directory, just enter a single slash eg:  \'/\'"
    messages[8]="The ADOdb database module is required to run the game. You can find it at <a href=http://php.weblogs.com/ADODB target=\"_blank\">http://php.weblogs.com/ADODB</a>. Enter the path where it is installed"
    messages[10]="Administrator\'s password and email. Be sure to change these. Don't leave them empty"
    messages[11]="Confirm Administrator\'s password"
    messages[12]="How do you want the system periodically update values in the game?"
    messages[13]="How often (in minutes) your crontab updates the game?"
    messages[14]="Crypt key for sessions saved into the database. You don\'t need to change it"
    messages[15]="If you are running the game on a secure server, enter https here"

    var w=window.open("","_blank","top=30,left=30,width=500,height=200,history=no,menubar=no,status=no,resizable=no")
    var d=w.document
    d.write("<head><title>Helper</title><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"><meta http-equiv=\"Pragma\" content=\"no-cache\"><link rel=\"stylesheet\" href=\"templates/styles/style.css\" type=\"text/css\"></head>");
    d.write("<body background=\"templates/$templateset/images/bgoutspace1.png\" bgcolor=\"#000\" text=\"#CCC\" link=\"#00FF00\" vlink=\"#00FF00\" alink=\"#F00\">");
    d.write("<table border=\"0\" width=\"100%\" height=\"100%\"");
    d.write("<tr><td valign=\"top\">" + messages[tooltip_id] + "</td></tr>")
    d.write("<tr align=\"center\"><td valign=\"bottom\"><br><br><input type=\"button\" value=\"Close window\" onclick=\"window.close()\" ></td></tr></table>")
    return false;
}
