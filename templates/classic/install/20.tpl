<script type="text/javascript" defer="defer" src="backends/javascript/focus.js"></script>
<script type="text/javascript" defer="defer" src="backends/javascript/sha256.js"></script>
<div style="background-image: url( templates/{$templateset}/images/header-stretch.png ); background-repeat: repeat-x;">
<div align="left"><img src="templates/{$templateset}/images/header.png" alt="Blacknova Traders"></div></div>
<h1>{$title}</h1>
<h2>Configure database settings</h2>

<!-- Safe mode doesn't allow direct file writing.-->
{if $safe_mode}
The machine you are running this script on appears to have PHP's safe_mode enabled.<br>
This means that the automatic portions of this script won't function properly.<br>
Instead of automatically configuring the game, we will generate a file that you can upload via ftp.<br><br>
{/if}

{if $reinstall}
It seems that you have already installed the game. If you want to edit your db_config.php in /config/ dir, enter your admin password:
<form action="install.php" method="post"><input type="password" name="swordfish" value="">&nbsp;
<input type="submit" value="Submit"></form>
Everything looks great! Feel free to run the <a href="./make_galaxy.php">Make Galaxy</a> script now!
{/if}

{if $showit}
<script type="text/javascript" src="backends/javascript/installtips.js"></script>
<form action="install.php"  method="post">
  <table>
    <tr>
      <td>Database type&nbsp;<a href='#' onclick="mytip('0')">?</a></td>
      <td>
        <select tabindex=1 name=_ADODB_SESSION_DRIVER>
        {$output}
        </select>
      </td>
    </tr>
    <tr>
      <td>Database name&nbsp;<a href='#' onclick="mytip('1')">?</a></td>
      <td><input tabindex=2 type=text name=_ADODB_SESSION_DB value="{$v2}"></td>
    </tr>
    <tr>
      <td>Database username&nbsp;<a href='#' onclick="mytip('2')">?</a></td>
      <td><input tabindex=3 type=text name=_ADODB_SESSION_USER value="{$v3}"></td>
    </tr>
    <tr>
      <td>Database password&nbsp;<a href='#' onclick="mytip('2')">?</a></td>
      <td><input tabindex=4 type=password name=_ADODB_SESSION_PWD value="{$v4}"></td>
    </tr>
    <tr>
      <td><strong>Database host</strong>&nbsp;<a href='#' onclick="mytip('3')">?</a></td>
      <td><input tabindex=5 type=text name=_ADODB_SESSION_CONNECT value="{$v5}"></td>
    </tr>
    <tr>
      <td><strong>Database port</strong>&nbsp;<a href='#' onclick="mytip('3')">?</a></td>
      <td><input tabindex=6 type=text name=_dbport value="{$v6}"></td>
    </tr>
    <tr>
      <td>Database table prefix&nbsp;<a href='#' onclick="mytip('5')">?</a></td>
      <td><input tabindex=8 type=text name=_raw_prefix value="{$v8}"></td>
    </tr>
    <tr>
      <td>Admin password&nbsp;<a href='#' onclick="mytip('10')">?</a></td>
      <td><input tabindex=15 type=password name=_adminpass value="{$v14}"></td>
    </tr>
    <tr>
      <td>Confirm admin password&nbsp;<a href='#' onclick="mytip('11')">?</a></td>
      <td><input tabindex=16 type=password name=adminpass2 value="{$v14}"></td>
    </tr>
    <tr>
      <td><strong>Session crypt key</strong>&nbsp;<a href='#' onclick="mytip('14')">?</a></td>
      <td><input tabindex=21 type=text name=_ADODB_CRYPT_KEY value="{$v17}"></td>
    </tr>
    <tr>
      <td><strong>Server type</strong>&nbsp;<a href='#' onclick="mytip('18')">?</a></td>
      <td><input tabindex=22 type=text name=_server_type value="{$v18}"></td>
    </tr>
    <tr>
      <td><input type=hidden name="step" value="2"></td>
    </tr>
    <tr>
      <td><input type=hidden name="swordfish" value="$swordfish"></td>
    </tr>
    <tr>
      <td><input tabindex=22 type="submit" value="Submit" onclick="validate()"></td>
      <td></td>
    </tr>
  </table>
<!--</form>-->
<br><br>
{/if}

<br><br>
<input type="submit" value="{$l_continue}">
<input type="hidden" name="encrypted_password" value="{$encrypted_password}">
<input type="hidden" name="step" value="{$step}">
<input type="hidden" name="total_elapsed" value="{$total_elapsed}">
</form>
<br><br><br><br>

