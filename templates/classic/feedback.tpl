<h1>{$title}</h1>

{if $empty_content}
<form name="bntform" action="feedback.php" method="post" accept-charset="utf-8" onsubmit="document.bntform.submit_button.disabled=true;">
<table>
  <tr>
    <td>{$l_feedback_to}</td>
    <td>
      <input readonly="readonly" type="text" name="dummy" size="40" maxlength="40" value="GameAdmin">
    </td>
  </tr>
  <tr>
    <td>{$l_feedback_from}</td>
    <td>
      <input readonly="readonly" type="text" name="dummy" size="40" maxlength="40" value="{$playerinfo_character_name} - {$accountinfo_email}">
    </td>
  </tr>
  <tr>
    <td>{$l_feedback_topi}</td>
    <td>
      <input readonly="readonly" type="text" name="subject" size="40" maxlength="40" value="{$l_feedback_feedback}">
    </td>
  </tr>
  <tr>
    <td>{$l_feedback_message}</td>
    <td>
      <textarea name="content" rows="5" cols="40"></textarea>
    </td>
  </tr>
  <tr>
    <td></td>
    <td>
      <input type="submit" name="submit_button" value="{$l_submit}"><input type="reset" value="{$l_reset}">
    </td>
  </tr>
</table>
</form>
<br>{$l_feedback_info}<br>
{elseif $mail_result}
<font color="lime">Message Sent</font><br><br>
{else}
<font color="red">Message failed to send!</font><br><br>
{/if}

<a href="main.php">{$l_global_mmenu}</a>
