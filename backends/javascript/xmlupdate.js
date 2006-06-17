function xmlupdate()
{
    loadFragmentInToElement('server1.php', 'currentdate');
}

var g_intervalID;
g_intervalID = setInterval(xmlupdate, 2000);

