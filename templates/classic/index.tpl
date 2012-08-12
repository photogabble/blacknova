<script type="text/javascript" defer="defer" src="backends/javascript/clear_default.js"></script>
<script type="text/javascript" defer="defer" src="backends/javascript/sha256.js"></script>
<noscript></noscript>
<div style="background-image: url( templates/{$templateset}/images/header-stretch.png ); background-repeat: repeat-x;">
    <div class="left"><img src="templates/{$templateset}/images/header.png" alt="{$l_project_name}"></div>
</div>

<table cellspacing="0" cellpadding="0">
  <tr>
    <td class="indexpage2">
      <strong>{$l_in_whatis} {$l_project_name} (<acronym title="{$l_project_name}">BNT</acronym>)?</strong>
      <p>
      {$l_in_bnt_is}
      <p>
      <strong>{$l_in_canrun}</strong>
      <p>
      {$l_yes}! <acronym title="{$l_project_name}">BNT</acronym> {$l_in_isdevd} <a href="http://sourceforge.net/projects/blacknova" tabindex="19">SourceForge</a>.
      <div class="thinline"></div>
      <p>
      <strong>{$l_in_others}</strong>
      <p>
<!--      <a href="http://bnt1.blacknova.net" tabindex="16">{$l_official} <acronym title="{$l_project_name}">BNT</acronym> {$l_in_maingm}</a><br>
      <a href="http://bnt2.blacknova.net" tabindex="17">{$l_official} <acronym title="{$l_project_name}">BNT</acronym> {$l_in_2ndgm}</a><br>
      <a href="http://play.blacknovatraders.com" tabindex="18">{$l_official} <acronym title="{$l_project_name}">BNT</acronym> {$l_in_devgm}</a><br>-->
      <p>
      <a href="http://sourceforge.net"><img src="http://sflogo.sourceforge.net/sflogo.php?group_id=14248&amp;type=1" width="88" height="31" border="0" alt="SourceForge.net Logo"></a>
    </td>
    <td class="indexpage" valign="top">
      <table cellspacing="0" cellpadding="0">
        <tr>
          <td class="indexpage" colspan="3">
            <!-- begin login -->
              <form method="post" action="login2.php" onsubmit="encrypted_password.value=sha256_once(document.forms[0].password.value);password.value='';">
              <table cellspacing="0" cellpadding="0">
                <tr>
                  <td class="indexpage" align="right"><label for="Email">{$l_email}</label></td>
                  <td class="indexpage" align="left"><input type="text" id="Email" name="email" size="20" maxlength="35" value="{$l_in_your_email}" tabindex="1" onfocus="clearDefault(this)" class="colorinputbox"></td>
                  <td class="indexpage infobox" rowspan="6">
                    <strong>{$l_new_forgotpw}</strong>
                    <br>{$l_in_nopass}
                    <p>
                    <strong>{$l_in_gamenum}</strong>
                    <br>{$l_in_multigames} 
                    <p>
                    <strong>{$l_in_newusr}</strong>
                    <br>{$l_in_signup}
                  </td>
                </tr>
                <tr>
                  <td class="indexpage" align="right"><label for="Password">{$l_password} </label></td>
                  <td class="indexpage" align="left"><input type="password" id="Password" name="password" size="20" maxlength="20" value="" tabindex="2"><input type="hidden" name="encrypted_password" value=""></td>
                </tr>
                <tr>
                  <td class="indexpage" align="right"><label for="Game">{$l_in_gm} </label></td>
                  <td class="indexpage" align="left">
                    <p class="center">
                      <select tabindex="3" name="gamenum" id="Game" class="indexdropdown">
                      {$game_drop_down}
                      </select>
                    </p>
                  </td>
                </tr>
                <tr>
                  <td class="indexpage" align="right"><label for="Language">{$l_in_lang} </label></td>
                  <td class="indexpage" align="left">
                    <p class="center">
                      <select tabindex="4" name="newlang" id="Language" class="indexdropdown">
                      {$login_drop_down}
                      </select>
                    </p>
                  </td>
                </tr>
                <tr>
                  <td class="indexpage"></td>
                  <td class="indexpage">
                    <input type="submit" class="indexyellow" alt="{$l_login_title}" name="submit" value="{$l_login_title}" tabindex="5">
                    &nbsp;&nbsp;
                    <a href="new.php" tabindex="6"><button class="indexyellow" tabindex="6">{$l_in_join}</button></a>
                  </td>
                  <td class="indexpage"></td>
                </tr>
              </table>
              </form>
            <!-- end login -->
          </td>
        </tr>
        <tr>
          <td colspan="2" class="indexpage halfwidth" valign="top">
            <ul class="nostyle">
              <li><div class="index"><strong>{$l_in_curgame_info}</strong></div></li>
              <li class="indexpage"><a href="settings.php" tabindex="7">{$l_settings}</a></li>
              <li class="indexpage"><a href="ranking.php" tabindex="8">{$l_rankings}</a></li>
              <li class="indexpage"><a href="news.php" tabindex="9">{$l_news_title}</a></li>
              <li class="indexpgae">&nbsp;</li>
              <li><div class="index"><strong>{$l_help}</strong></div></li>
              <li class="indexpage"><a href="faq/index.htm" tabindex="10">{$l_faq}</a></li>
              <li class="indexpage"><a href="newplayer.php" tabindex="11">{$l_in_guide}</a></li>
            </ul>
          </td>
          <td class="indexpage halfwidth" valign="top">
            <ul class="nostyle">
              <li><div class="index"><strong>{$l_in_stats}</strong></div></li>
              <li class="indexpage">{$l_in_gm_active} <strong>{$num_of_games}</strong></li>
              <li class="indexpage">{$l_in_new2day} <strong>0</strong></li>
              <li class="indexpgae">&nbsp;</li>
              <li class="indexpgae">&nbsp;</li>
              <li><div class="index"><strong>{$l_forums}</strong></div></li>
              <li class="indexpage"><a href="http://forums.blacknova.net" tabindex="13">{$l_in_main_forums}</a></li>
              <li class="indexpage"><a href="http://discuss.blacknovatraders.com" tabindex="14">{$l_in_dev_forums}</a></li>
              <li class="indexpage"><a href="http://forums.example.com" tabindex="15">{$site_name} Forums</a></li>
            </ul>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<p>
